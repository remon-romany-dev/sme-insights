<?php
/**
 * Visual Template Editor
 * Allows live editing of header, footer, and templates from frontend
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Visual_Editor {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Enqueue scripts with high priority to ensure they load on all pages
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		
		// Add Edit Design button to admin bar
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 100 );
		
		// Render UI and styles with high priority
		add_action( 'wp_footer', array( $this, 'render_editor_ui' ), 999 );
		add_action( 'wp_head', array( $this, 'output_saved_styles' ), 999 );
		
		// AJAX handlers
		add_action( 'wp_ajax_sme_save_template', array( $this, 'save_template' ) );
		add_action( 'wp_ajax_sme_load_template', array( $this, 'load_template' ) );
		add_action( 'wp_ajax_sme_delete_template', array( $this, 'delete_template' ) );
		add_action( 'wp_ajax_sme_update_element', array( $this, 'update_element' ) );
		
		// Body class for editor
		add_filter( 'body_class', array( $this, 'add_editor_class' ) );
	}
	
	/**
	 * Enqueue frontend scripts
	 */
	public function enqueue_scripts() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		
		// Check if already enqueued to prevent duplicates
		// Note: wp_script_is() can only be used inside wp_enqueue_scripts hook
		if ( did_action( 'wp_enqueue_scripts' ) && wp_script_is( 'sme-visual-editor', 'enqueued' ) ) {
			return;
		}
		
		// Enqueue universal editor first (works on any design)
		// Universal editor will enqueue itself via wp_enqueue_scripts hook
		// No need to call it directly here
		
		// Enqueue design flexibility script
		// Design flexibility will enqueue itself via wp_enqueue_scripts hook
		// No need to call it directly here
		
		// Enqueue visual editor styles
		wp_enqueue_style(
			'sme-visual-editor',
			SME_THEME_ASSETS . '/css/visual-editor.css',
			array(),
			SME_THEME_VERSION
		);
		
		// Enqueue visual editor script
		wp_enqueue_script(
			'sme-visual-editor',
			SME_THEME_ASSETS . '/js/visual-editor.js',
			array( 'jquery' ),
			SME_THEME_VERSION,
			true
		);
		
		wp_localize_script( 'sme-visual-editor', 'smeVisualEditor', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'sme_visual_editor_nonce' ),
			'templates' => $this->get_saved_templates(),
			'strings' => array(
				'edit' => __( 'Edit', 'sme-insights' ),
				'save' => __( 'Save', 'sme-insights' ),
				'cancel' => __( 'Cancel', 'sme-insights' ),
				'delete' => __( 'Delete', 'sme-insights' ),
				'header' => __( 'Header', 'sme-insights' ),
				'footer' => __( 'Footer', 'sme-insights' ),
				'page' => __( 'Page', 'sme-insights' ),
				'template' => __( 'Template', 'sme-insights' ),
			),
		) );
	}
	
	/**
	 * Enqueue admin scripts
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}
		
		wp_enqueue_style(
			'sme-visual-editor-admin',
			SME_THEME_ASSETS . '/css/visual-editor-admin.css',
			array(),
			SME_THEME_VERSION
		);
	}
	
	/**
	 * Add editor class to body
	 */
	public function add_editor_class( $classes ) {
		if ( current_user_can( 'edit_theme_options' ) && isset( $_GET['sme_edit'] ) && '1' === sanitize_text_field( $_GET['sme_edit'] ) ) {
			$classes[] = 'sme-visual-editor-active';
		}
		return $classes;
	}
	
	/**
	 * Output saved styles in head
	 */
	public function output_saved_styles() {
		// Check if already output to prevent duplicates
		static $outputted = false;
		if ( $outputted ) {
			return;
		}
		$outputted = true;
		
		$header_styles = get_option( 'sme_header_styles', array() );
		$footer_styles = get_option( 'sme_footer_styles', array() );
		
		if ( empty( $header_styles ) && empty( $footer_styles ) ) {
			return;
		}
		
		echo '<style id="sme-saved-styles">';
		
		if ( ! empty( $header_styles ) && is_array( $header_styles ) ) {
			// Use flexible selectors for header
			$header_selectors = apply_filters( 'sme_header_selectors', array(
				'header',
				'.header',
				'.main-header',
				'[role="banner"]',
				'header.site-header',
				'.site-header',
				'[data-sme-element-type="header"]'
			) );
			echo implode( ', ', array_map( 'esc_attr', $header_selectors ) ) . ' {';
			foreach ( $header_styles as $property => $value ) {
				// Don't override critical header colors (background, color) to maintain consistency
				$critical_properties = array( 'background', 'background-color', 'color', 'border-color' );
				if ( in_array( $property, $critical_properties ) ) {
					continue; // Skip critical color properties to maintain theme consistency
				}
				if ( ! empty( $value ) && $value !== 'auto' && $value !== 'none' && is_string( $property ) ) {
					echo esc_attr( $property ) . ': ' . esc_attr( $value ) . ';';
				}
			}
			echo '}';
		}
		
		if ( ! empty( $footer_styles ) && is_array( $footer_styles ) ) {
			// Use flexible selectors for footer
			$footer_selectors = apply_filters( 'sme_footer_selectors', array(
				'footer',
				'.footer',
				'.main-footer',
				'[role="contentinfo"]',
				'footer.site-footer',
				'.site-footer',
				'[data-sme-element-type="footer"]'
			) );
			echo implode( ', ', array_map( 'esc_attr', $footer_selectors ) ) . ' {';
			foreach ( $footer_styles as $property => $value ) {
				if ( ! empty( $value ) && $value !== 'auto' && $value !== 'none' && is_string( $property ) ) {
					echo esc_attr( $property ) . ': ' . esc_attr( $value ) . ';';
				}
			}
			echo '}';
		}
		
		echo '</style>';
	}
	
	/**
	 * Add Edit Design button to admin bar
	 */
	public function add_admin_bar_menu( $wp_admin_bar ) {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		
		// Only show in frontend, not in admin area
		if ( is_admin() ) {
			return;
		}

		// Temporarily hidden as per handover notes (refactoring save function)
		return;
		
		$is_editing = isset( $_GET['sme_edit'] ) && '1' === sanitize_text_field( $_GET['sme_edit'] );
		
		if ( ! $is_editing ) {
			// Edit Design
			$wp_admin_bar->add_menu( array(
				'id'    => 'sme-edit-design',
				'title' => '<span class="ab-icon dashicons-admin-customizer" style="margin-top: 3px;"></span> <span class="ab-label">' . esc_html__( 'Edit Design', 'sme-insights' ) . '</span>',
				'href'  => esc_url( add_query_arg( 'sme_edit', '1' ) ),
				'meta'  => array(
					'title' => esc_html__( 'Edit Design', 'sme-insights' ),
				),
			) );
			
			// Edit Header
			$wp_admin_bar->add_menu( array(
				'id'    => 'sme-edit-header',
				'title' => '<span class="ab-icon dashicons-arrow-up-alt" style="margin-top: 3px;"></span> <span class="ab-label">' . esc_html__( 'Edit Header', 'sme-insights' ) . '</span>',
				'href'  => esc_url( add_query_arg( array( 'sme_edit' => '1', 'sme_element' => 'header' ) ) ),
				'meta'  => array(
					'title' => esc_html__( 'Edit Header', 'sme-insights' ),
				),
			) );
			
			// Edit Footer
			$wp_admin_bar->add_menu( array(
				'id'    => 'sme-edit-footer',
				'title' => '<span class="ab-icon dashicons-arrow-down-alt" style="margin-top: 3px;"></span> <span class="ab-label">' . esc_html__( 'Edit Footer', 'sme-insights' ) . '</span>',
				'href'  => esc_url( add_query_arg( array( 'sme_edit' => '1', 'sme_element' => 'footer' ) ) ),
				'meta'  => array(
					'title' => esc_html__( 'Edit Footer', 'sme-insights' ),
				),
			) );
		}
	}
	
	/**
	 * Render editor UI in footer
	 */
	public function render_editor_ui() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		
		$is_editing = isset( $_GET['sme_edit'] ) && '1' === sanitize_text_field( $_GET['sme_edit'] );
		
		if ( ! $is_editing ) {
			// Don't show button in footer anymore - it's in admin bar now
			return;
		}
		
		// Show full editor interface
		?>
		<div class="sme-visual-editor-panel" id="smeVisualEditorPanel">
			<div class="sme-editor-header">
				<h3><?php esc_html_e( 'Visual Editor', 'sme-insights' ); ?></h3>
				<div class="sme-editor-actions">
					<button class="sme-btn sme-btn-secondary" id="smeSaveTemplate"><?php esc_html_e( 'Save as Template', 'sme-insights' ); ?></button>
					<button class="sme-btn sme-btn-primary" id="smeSaveChanges"><?php esc_html_e( 'Save Changes', 'sme-insights' ); ?></button>
					<a href="<?php echo esc_url( remove_query_arg( array( 'sme_edit', 'sme_element' ) ) ); ?>" class="sme-btn sme-btn-danger"><?php esc_html_e( 'Exit', 'sme-insights' ); ?></a>
				</div>
			</div>
			
			<div class="sme-editor-tabs">
				<button class="sme-tab active" data-tab="elements"><?php esc_html_e( 'Elements', 'sme-insights' ); ?></button>
				<button class="sme-tab" data-tab="styles"><?php esc_html_e( 'Styles', 'sme-insights' ); ?></button>
				<button class="sme-tab" data-tab="templates"><?php esc_html_e( 'Templates', 'sme-insights' ); ?></button>
			</div>
			
			<div class="sme-editor-content">
				<!-- Elements Tab -->
				<div class="sme-tab-content active" data-content="elements">
					<div class="sme-editor-section">
						<h4><?php esc_html_e( 'Select Element to Edit', 'sme-insights' ); ?></h4>
						<p class="sme-hint"><?php esc_html_e( 'Click on any element on the page to edit it', 'sme-insights' ); ?></p>
					</div>
				</div>
				
				<!-- Styles Tab -->
				<div class="sme-tab-content" data-content="styles">
					<div class="sme-editor-section" id="smeStyleEditor">
						<h4><?php esc_html_e( 'Style Editor', 'sme-insights' ); ?></h4>
						<div class="sme-style-controls">
							<div class="sme-control-group">
								<label><?php esc_html_e( 'Font Size', 'sme-insights' ); ?></label>
								<input type="range" min="10" max="100" value="16" class="sme-range" data-style="font-size" data-unit="px">
								<span class="sme-value">16px</span>
							</div>
							
							<div class="sme-control-group">
								<label><?php esc_html_e( 'Font Weight', 'sme-insights' ); ?></label>
								<select class="sme-select" data-style="font-weight">
									<option value="300">Light</option>
									<option value="400" selected>Normal</option>
									<option value="600">Semi Bold</option>
									<option value="700">Bold</option>
									<option value="900">Black</option>
								</select>
							</div>
							
							<div class="sme-control-group">
								<label><?php esc_html_e( 'Text Color', 'sme-insights' ); ?></label>
								<input type="color" class="sme-color" data-style="color" value="#000000">
							</div>
							
							<div class="sme-control-group">
								<label><?php esc_html_e( 'Background Color', 'sme-insights' ); ?></label>
								<input type="color" class="sme-color" data-style="background-color" value="#ffffff">
							</div>
							
							<div class="sme-control-group">
								<label><?php esc_html_e( 'Padding', 'sme-insights' ); ?></label>
								<div class="sme-spacing-controls">
									<input type="number" class="sme-number" data-style="padding-top" placeholder="Top" min="0" max="200">
									<input type="number" class="sme-number" data-style="padding-right" placeholder="Right" min="0" max="200">
									<input type="number" class="sme-number" data-style="padding-bottom" placeholder="Bottom" min="0" max="200">
									<input type="number" class="sme-number" data-style="padding-left" placeholder="Left" min="0" max="200">
								</div>
							</div>
							
							<div class="sme-control-group">
								<label><?php esc_html_e( 'Margin', 'sme-insights' ); ?></label>
								<div class="sme-spacing-controls">
									<input type="number" class="sme-number" data-style="margin-top" placeholder="Top" min="0" max="200">
									<input type="number" class="sme-number" data-style="margin-right" placeholder="Right" min="0" max="200">
									<input type="number" class="sme-number" data-style="margin-bottom" placeholder="Bottom" min="0" max="200">
									<input type="number" class="sme-number" data-style="margin-left" placeholder="Left" min="0" max="200">
								</div>
							</div>
							
							<div class="sme-control-group">
								<label><?php esc_html_e( 'Width', 'sme-insights' ); ?></label>
								<input type="range" min="0" max="100" value="100" class="sme-range" data-style="width" data-unit="%">
								<span class="sme-value">100%</span>
							</div>
							
							<div class="sme-control-group">
								<label><?php esc_html_e( 'Height', 'sme-insights' ); ?></label>
								<input type="range" min="0" max="500" value="auto" class="sme-range" data-style="height" data-unit="px">
								<span class="sme-value">auto</span>
							</div>
							
							<div class="sme-control-group">
								<label><?php esc_html_e( 'Border Radius', 'sme-insights' ); ?></label>
								<input type="range" min="0" max="50" value="0" class="sme-range" data-style="border-radius" data-unit="px">
								<span class="sme-value">0px</span>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Templates Tab -->
				<div class="sme-tab-content" data-content="templates">
					<div class="sme-editor-section">
						<h4><?php esc_html_e( 'Saved Templates', 'sme-insights' ); ?></h4>
						<div class="sme-templates-list" id="smeTemplatesList">
							<?php $this->render_templates_list(); ?>
						</div>
						<button class="sme-btn sme-btn-secondary" id="smeLoadTemplate"><?php esc_html_e( 'Load Template', 'sme-insights' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Element Highlighter -->
		<div class="sme-element-highlighter"></div>
		<?php
	}
	
	/**
	 * Get saved templates
	 */
	private function get_saved_templates() {
		$templates = get_option( 'sme_saved_templates', array() );
		return $templates;
	}
	
	/**
	 * Render templates list
	 */
	private function render_templates_list() {
		$templates = $this->get_saved_templates();
		
		if ( empty( $templates ) ) {
			echo '<p class="sme-no-templates">' . esc_html__( 'No templates saved yet.', 'sme-insights' ) . '</p>';
			return;
		}
		
		echo '<ul class="sme-templates">';
		foreach ( $templates as $id => $template ) {
			echo '<li data-template-id="' . esc_attr( $id ) . '">';
			echo '<span class="sme-template-name">' . esc_html( $template['name'] ) . '</span>';
			echo '<span class="sme-template-type">' . esc_html( $template['type'] ) . '</span>';
			echo '<button class="sme-btn-sm sme-btn-danger" data-delete-template="' . esc_attr( $id ) . '">' . esc_html__( 'Delete', 'sme-insights' ) . '</button>';
			echo '</li>';
		}
		echo '</ul>';
	}
	
	/**
	 * Save template via AJAX
	 */
	public function save_template() {
		check_ajax_referer( 'sme_visual_editor_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		if ( ! isset( $_POST['name'] ) || ! isset( $_POST['type'] ) || ! isset( $_POST['data'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing required fields', 'sme-insights' ) ) );
		}
		
		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$data_raw = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : '';
		$data = json_decode( $data_raw, true );
		
		if ( empty( $name ) || empty( $type ) ) {
			wp_send_json_error( array( 'message' => __( 'Name and type are required', 'sme-insights' ) ) );
		}
		
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			wp_send_json_error( array( 'message' => __( 'Invalid JSON data', 'sme-insights' ) ) );
		}
		
		if ( ! is_array( $data ) ) {
			wp_send_json_error( array( 'message' => __( 'Data must be an array', 'sme-insights' ) ) );
		}
		
		// Sanitize template data
		$data = array_map( 'sanitize_text_field', $data );
		
		$templates = $this->get_saved_templates();
		$id = 'template_' . time() . '_' . wp_rand( 1000, 9999 );
		
		$templates[ $id ] = array(
			'name' => sanitize_text_field( $name ),
			'type' => sanitize_text_field( $type ),
			'data' => $data,
			'date' => current_time( 'mysql' ),
		);
		
		update_option( 'sme_saved_templates', $templates );
		
		wp_send_json_success( array(
			'message' => __( 'Template saved successfully', 'sme-insights' ),
			'id' => $id,
		) );
	}
	
	/**
	 * Load template via AJAX
	 */
	public function load_template() {
		check_ajax_referer( 'sme_visual_editor_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		if ( ! isset( $_POST['template_id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Template ID is required', 'sme-insights' ) ) );
		}
		
		$id = isset( $_POST['template_id'] ) ? sanitize_text_field( wp_unslash( $_POST['template_id'] ) ) : '';
		$templates = $this->get_saved_templates();
		
		if ( ! isset( $templates[ $id ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Template not found', 'sme-insights' ) ) );
		}
		
		wp_send_json_success( array(
			'data' => $templates[ $id ]['data'],
		) );
	}
	
	/**
	 * Delete template via AJAX
	 */
	public function delete_template() {
		check_ajax_referer( 'sme_visual_editor_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		if ( ! isset( $_POST['template_id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Template ID is required', 'sme-insights' ) ) );
		}
		
		$id = isset( $_POST['template_id'] ) ? sanitize_text_field( wp_unslash( $_POST['template_id'] ) ) : '';
		$templates = $this->get_saved_templates();
		
		if ( isset( $templates[ $id ] ) ) {
			unset( $templates[ $id ] );
			update_option( 'sme_saved_templates', $templates );
			wp_send_json_success( array( 'message' => __( 'Template deleted', 'sme-insights' ) ) );
		}
		
		wp_send_json_error( array( 'message' => __( 'Template not found', 'sme-insights' ) ) );
	}
	
	/**
	 * Update element via AJAX
	 */
	public function update_element() {
		check_ajax_referer( 'sme_visual_editor_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		if ( ! isset( $_POST['element_id'] ) || ! isset( $_POST['styles'] ) || ! isset( $_POST['type'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing required fields', 'sme-insights' ) ) );
		}
		
		$element_id = isset( $_POST['element_id'] ) ? sanitize_text_field( wp_unslash( $_POST['element_id'] ) ) : '';
		$styles_raw = isset( $_POST['styles'] ) ? wp_unslash( $_POST['styles'] ) : '';
		$styles = json_decode( $styles_raw, true );
		
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			wp_send_json_error( array( 'message' => __( 'Invalid JSON data', 'sme-insights' ) ) );
		}
		
		if ( ! is_array( $styles ) ) {
			wp_send_json_error( array( 'message' => __( 'Styles must be an array', 'sme-insights' ) ) );
		}
		
		$type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		
		// Sanitize styles array
		$styles = array_map( 'sanitize_text_field', $styles );
		
		// Save styles based on type
		if ( 'header' === $type ) {
			update_option( 'sme_header_styles', $styles );
		} elseif ( 'footer' === $type ) {
			update_option( 'sme_footer_styles', $styles );
		} elseif ( 'page' === $type ) {
			$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
			if ( $post_id > 0 ) {
				update_post_meta( $post_id, 'sme_custom_styles', $styles );
			}
		} elseif ( 'template' === $type ) {
			$template_name = isset( $_POST['template_name'] ) ? sanitize_text_field( wp_unslash( $_POST['template_name'] ) ) : '';
			if ( ! empty( $template_name ) ) {
				update_option( 'sme_template_styles_' . sanitize_key( $template_name ), $styles );
			}
		}
		
		wp_send_json_success( array( 'message' => __( 'Changes saved', 'sme-insights' ) ) );
	}
}

