<?php
/**
 * Template Customizer
 * Allows customization of category, single, and archive templates
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Template_Customizer {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_ajax_sme_save_template_styles', array( $this, 'save_template_styles' ) );
		add_action( 'wp_ajax_sme_load_template_styles', array( $this, 'load_template_styles' ) );
		add_action( 'wp_ajax_sme_reset_template_styles', array( $this, 'reset_template_styles' ) );
		
		// Output styles with high priority to ensure they apply
		add_action( 'wp_head', array( $this, 'output_template_styles' ), 999 );
		
		// Ensure styles output even after theme changes
		add_action( 'after_setup_theme', array( $this, 'ensure_styles_output' ), 999 );
	}
	
	/**
	 * Ensure styles are output
	 */
	public function ensure_styles_output() {
		// Styles will be output via wp_head hook
		// This method ensures the hook is always registered
		if ( ! has_action( 'wp_head', array( $this, 'output_template_styles' ) ) ) {
			add_action( 'wp_head', array( $this, 'output_template_styles' ), 999 );
		}
	}
	
	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'themes.php',
			__( 'Template Customizer', 'sme-insights' ),
			__( 'Template Customizer', 'sme-insights' ),
			'edit_theme_options',
			'sme-template-customizer',
			array( $this, 'render_admin_page' )
		);
	}
	
	/**
	 * Enqueue admin assets
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( 'appearance_page_sme-template-customizer' !== $hook ) {
			return;
		}
		
		// Check if already enqueued to prevent duplicates
		if ( wp_style_is( 'sme-template-customizer-admin', 'enqueued' ) ) {
			return;
		}
		
		wp_enqueue_style(
			'sme-template-customizer-admin',
			SME_THEME_ASSETS . '/css/template-customizer-admin.css',
			array(),
			SME_THEME_VERSION
		);
		
		if ( ! wp_script_is( 'sme-template-customizer-admin', 'enqueued' ) ) {
			wp_enqueue_script(
				'sme-template-customizer-admin',
				SME_THEME_ASSETS . '/js/template-customizer-admin.js',
				array( 'jquery', 'wp-color-picker' ),
				SME_THEME_VERSION,
				true
			);
		}
		
		wp_enqueue_style( 'wp-color-picker' );
		
		wp_localize_script( 'sme-template-customizer-admin', 'smeTemplateCustomizer', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'sme_template_customizer_nonce' ),
			'strings' => array(
				'save' => __( 'Save Changes', 'sme-insights' ),
				'reset' => __( 'Reset to Default', 'sme-insights' ),
				'saved' => __( 'Changes saved successfully!', 'sme-insights' ),
				'error' => __( 'Error saving changes', 'sme-insights' ),
			),
		) );
	}
	
	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'category';
		$tabs = array(
			'category' => __( 'Category Pages', 'sme-insights' ),
			'single' => __( 'Single Post', 'sme-insights' ),
			'archive' => __( 'Archive Pages', 'sme-insights' ),
		);
		
		?>
		<div class="wrap sme-template-customizer-wrap">
			<h1><?php esc_html_e( 'Template Customizer', 'sme-insights' ); ?></h1>
			<p class="description"><?php esc_html_e( 'Customize the appearance of your category pages, single posts, and archive pages.', 'sme-insights' ); ?></p>
			
			<nav class="nav-tab-wrapper">
				<?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
					<a href="?page=sme-template-customizer&tab=<?php echo esc_attr( $tab_key ); ?>" class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
						<?php echo esc_html( $tab_label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
			
			<div class="sme-customizer-content">
				<form id="smeTemplateCustomizerForm" data-template-type="<?php echo esc_attr( $active_tab ); ?>">
					<?php $this->render_template_settings( $active_tab ); ?>
					
					<p class="submit">
						<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'sme-insights' ); ?></button>
						<button type="button" class="button button-secondary" id="smeResetStyles"><?php esc_html_e( 'Reset to Default', 'sme-insights' ); ?></button>
					</p>
				</form>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render template settings
	 */
	private function render_template_settings( $template_type ) {
		$styles = $this->get_template_styles( $template_type );
		
		?>
		<div class="sme-settings-section">
			<h2><?php esc_html_e( 'Typography', 'sme-insights' ); ?></h2>
			
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="title_font_size"><?php esc_html_e( 'Title Font Size', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="number" id="title_font_size" name="title_font_size" value="<?php echo esc_attr( $styles['title_font_size'] ?? 32 ); ?>" min="10" max="100" class="small-text">
						<span class="description">px</span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="title_font_weight"><?php esc_html_e( 'Title Font Weight', 'sme-insights' ); ?></label>
					</th>
					<td>
						<select id="title_font_weight" name="title_font_weight">
							<option value="300" <?php selected( $styles['title_font_weight'] ?? '700', '300' ); ?>><?php esc_html_e( 'Light', 'sme-insights' ); ?></option>
							<option value="400" <?php selected( $styles['title_font_weight'] ?? '700', '400' ); ?>><?php esc_html_e( 'Normal', 'sme-insights' ); ?></option>
							<option value="600" <?php selected( $styles['title_font_weight'] ?? '700', '600' ); ?>><?php esc_html_e( 'Semi Bold', 'sme-insights' ); ?></option>
							<option value="700" <?php selected( $styles['title_font_weight'] ?? '700', '700' ); ?>><?php esc_html_e( 'Bold', 'sme-insights' ); ?></option>
							<option value="900" <?php selected( $styles['title_font_weight'] ?? '700', '900' ); ?>><?php esc_html_e( 'Black', 'sme-insights' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="title_color"><?php esc_html_e( 'Title Color', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="text" id="title_color" name="title_color" value="<?php echo esc_attr( $styles['title_color'] ?? '#111827' ); ?>" class="sme-color-picker">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="content_font_size"><?php esc_html_e( 'Content Font Size', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="number" id="content_font_size" name="content_font_size" value="<?php echo esc_attr( $styles['content_font_size'] ?? 16 ); ?>" min="10" max="50" class="small-text">
						<span class="description">px</span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="content_line_height"><?php esc_html_e( 'Content Line Height', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="number" id="content_line_height" name="content_line_height" value="<?php echo esc_attr( $styles['content_line_height'] ?? 1.6 ); ?>" min="1" max="3" step="0.1" class="small-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="content_color"><?php esc_html_e( 'Content Color', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="text" id="content_color" name="content_color" value="<?php echo esc_attr( $styles['content_color'] ?? '#374151' ); ?>" class="sme-color-picker">
					</td>
				</tr>
			</table>
		</div>
		
		<div class="sme-settings-section">
			<h2><?php esc_html_e( 'Colors', 'sme-insights' ); ?></h2>
			
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="background_color"><?php esc_html_e( 'Background Color', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="text" id="background_color" name="background_color" value="<?php echo esc_attr( $styles['background_color'] ?? '#ffffff' ); ?>" class="sme-color-picker">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="link_color"><?php esc_html_e( 'Link Color', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="text" id="link_color" name="link_color" value="<?php echo esc_attr( $styles['link_color'] ?? '#2563eb' ); ?>" class="sme-color-picker">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="link_hover_color"><?php esc_html_e( 'Link Hover Color', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="text" id="link_hover_color" name="link_hover_color" value="<?php echo esc_attr( $styles['link_hover_color'] ?? '#1d4ed8' ); ?>" class="sme-color-picker">
					</td>
				</tr>
				<?php if ( 'category' === $template_type ) : ?>
				<tr>
					<th scope="row">
						<label for="category_color"><?php esc_html_e( 'Category Badge Color', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="text" id="category_color" name="category_color" value="<?php echo esc_attr( $styles['category_color'] ?? '#2563eb' ); ?>" class="sme-color-picker">
					</td>
				</tr>
				<?php endif; ?>
			</table>
		</div>
		
		<div class="sme-settings-section">
			<h2><?php esc_html_e( 'Spacing', 'sme-insights' ); ?></h2>
			
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="container_padding"><?php esc_html_e( 'Container Padding', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="number" id="container_padding" name="container_padding" value="<?php echo esc_attr( $styles['container_padding'] ?? 20 ); ?>" min="0" max="100" class="small-text">
						<span class="description">px</span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="post_spacing"><?php esc_html_e( 'Post Spacing', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="number" id="post_spacing" name="post_spacing" value="<?php echo esc_attr( $styles['post_spacing'] ?? 30 ); ?>" min="0" max="100" class="small-text">
						<span class="description">px</span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="title_margin_bottom"><?php esc_html_e( 'Title Margin Bottom', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="number" id="title_margin_bottom" name="title_margin_bottom" value="<?php echo esc_attr( $styles['title_margin_bottom'] ?? 20 ); ?>" min="0" max="100" class="small-text">
						<span class="description">px</span>
					</td>
				</tr>
			</table>
		</div>
		
		<div class="sme-settings-section">
			<h2><?php esc_html_e( 'Layout', 'sme-insights' ); ?></h2>
			
			<table class="form-table">
				<?php if ( 'category' === $template_type || 'archive' === $template_type ) : ?>
				<tr>
					<th scope="row">
						<label for="columns"><?php esc_html_e( 'Columns', 'sme-insights' ); ?></label>
					</th>
					<td>
						<select id="columns" name="columns">
							<option value="1" <?php selected( $styles['columns'] ?? 3, 1 ); ?>>1 Column</option>
							<option value="2" <?php selected( $styles['columns'] ?? 3, 2 ); ?>>2 Columns</option>
							<option value="3" <?php selected( $styles['columns'] ?? 3, 3 ); ?>>3 Columns</option>
							<option value="4" <?php selected( $styles['columns'] ?? 3, 4 ); ?>>4 Columns</option>
						</select>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<th scope="row">
						<label for="max_width"><?php esc_html_e( 'Max Width', 'sme-insights' ); ?></label>
					</th>
					<td>
						<input type="number" id="max_width" name="max_width" value="<?php echo esc_attr( $styles['max_width'] ?? 1200 ); ?>" min="600" max="1920" class="small-text">
						<span class="description">px</span>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}
	
	/**
	 * Get template styles
	 */
	private function get_template_styles( $template_type ) {
		$defaults = $this->get_default_styles( $template_type );
		$saved = get_option( 'sme_template_styles_' . $template_type, array() );
		return wp_parse_args( $saved, $defaults );
	}
	
	/**
	 * Get default styles
	 */
	private function get_default_styles( $template_type ) {
		$defaults = array(
			'title_font_size' => 32,
			'title_font_weight' => '700',
			'title_color' => '#111827',
			'content_font_size' => 16,
			'content_line_height' => 1.6,
			'content_color' => '#374151',
			'background_color' => '#ffffff',
			'link_color' => '#2563eb',
			'link_hover_color' => '#1d4ed8',
			'container_padding' => 20,
			'post_spacing' => 30,
			'title_margin_bottom' => 20,
			'max_width' => 1200,
		);
		
		if ( 'category' === $template_type || 'archive' === $template_type ) {
			$defaults['columns'] = 3;
			$defaults['category_color'] = '#2563eb';
		}
		
		return $defaults;
	}
	
	/**
	 * Save template styles via AJAX
	 */
	public function save_template_styles() {
		check_ajax_referer( 'sme_template_customizer_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		if ( ! isset( $_POST['template_type'] ) || ! isset( $_POST['styles'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing required fields', 'sme-insights' ) ) );
		}
		
		$template_type = sanitize_text_field( $_POST['template_type'] );
		
		if ( ! in_array( $template_type, array( 'category', 'single', 'archive' ), true ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid template type', 'sme-insights' ) ) );
		}
		
		$styles = array();
		if ( is_array( $_POST['styles'] ) ) {
			foreach ( $_POST['styles'] as $key => $value ) {
				$styles[ sanitize_key( $key ) ] = sanitize_text_field( $value );
			}
		}
		
		update_option( 'sme_template_styles_' . $template_type, $styles );
		
		wp_send_json_success( array( 'message' => __( 'Styles saved successfully', 'sme-insights' ) ) );
	}
	
	/**
	 * Load template styles via AJAX
	 */
	public function load_template_styles() {
		check_ajax_referer( 'sme_template_customizer_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		if ( ! isset( $_POST['template_type'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Template type is required', 'sme-insights' ) ) );
		}
		
		$template_type = sanitize_text_field( $_POST['template_type'] );
		
		if ( ! in_array( $template_type, array( 'category', 'single', 'archive' ), true ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid template type', 'sme-insights' ) ) );
		}
		
		$styles = $this->get_template_styles( $template_type );
		
		wp_send_json_success( array( 'styles' => $styles ) );
	}
	
	/**
	 * Reset template styles via AJAX
	 */
	public function reset_template_styles() {
		check_ajax_referer( 'sme_template_customizer_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		if ( ! isset( $_POST['template_type'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Template type is required', 'sme-insights' ) ) );
		}
		
		$template_type = sanitize_text_field( $_POST['template_type'] );
		
		if ( ! in_array( $template_type, array( 'category', 'single', 'archive' ), true ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid template type', 'sme-insights' ) ) );
		}
		
		$defaults = $this->get_default_styles( $template_type );
		
		update_option( 'sme_template_styles_' . $template_type, $defaults );
		
		wp_send_json_success( array( 'message' => __( 'Styles reset to default', 'sme-insights' ), 'styles' => $defaults ) );
	}
	
	/**
	 * Output template styles in head
	 */
	public function output_template_styles() {
		// Check if already output to prevent duplicates
		static $outputted = false;
		if ( $outputted ) {
			return;
		}
		$outputted = true;
		
		// Category pages
		if ( is_category() || is_tax( 'main_category' ) ) {
			$styles = $this->get_template_styles( 'category' );
			if ( ! empty( $styles ) && is_array( $styles ) ) {
				$this->output_css( '.taxonomy-main_category', $styles );
			}
		}
		
		// Single post
		if ( is_single() ) {
			$styles = $this->get_template_styles( 'single' );
			if ( ! empty( $styles ) && is_array( $styles ) ) {
				$this->output_css( '.single-post', $styles );
			}
		}
		
		// Archive pages
		if ( is_archive() && ! is_category() && ! is_tax( 'main_category' ) ) {
			$styles = $this->get_template_styles( 'archive' );
			if ( ! empty( $styles ) && is_array( $styles ) ) {
				$this->output_css( '.archive', $styles );
			}
		}
	}
	
	/**
	 * Get current template type
	 */
	private function get_current_template_type() {
		if ( is_category() || is_tax( 'main_category' ) ) {
			return 'category';
		} elseif ( is_single() ) {
			return 'single';
		} elseif ( is_archive() ) {
			return 'archive';
		}
		return '';
	}
	
	/**
	 * Output CSS with universal selectors
	 */
	private function output_css( $selector, $styles ) {
		if ( empty( $styles ) || ! is_array( $styles ) ) {
			return;
		}
		
		// Get universal selectors
		$template_type = $this->get_current_template_type();
		$base_selectors = array( $selector );
		
		// Add body class selectors
		$body_classes = array();
		if ( 'category' === $template_type ) {
			$body_classes[] = 'body.sme-category-editable';
			$body_classes[] = 'body.category';
			$body_classes[] = 'body.tax-main_category';
		} elseif ( 'single' === $template_type ) {
			$body_classes[] = 'body.sme-single-editable';
			$body_classes[] = 'body.single';
		} elseif ( 'archive' === $template_type ) {
			$body_classes[] = 'body.sme-archive-editable';
			$body_classes[] = 'body.archive';
		}
		
		// Get fallback selectors
		$selectors = apply_filters( 'sme_template_customizer_selectors', $base_selectors, $template_type );
		
		// Add universal selectors
		$universal_selectors = array();
		foreach ( $body_classes as $body_class ) {
			$universal_selectors[] = $body_class . ' ' . $selector;
			$universal_selectors[] = $body_class . ' .content-area';
			$universal_selectors[] = $body_class . ' .site-content';
			$universal_selectors[] = $body_class . ' #content';
			$universal_selectors[] = $body_class . ' main';
			$universal_selectors[] = $body_class . ' [role="main"]';
		}
		
		// Merge all selectors
		$all_selectors = array_merge( $selectors, $universal_selectors );
		$selector_string = implode( ', ', array_unique( $all_selectors ) );
		
		$style_id = sanitize_html_class( str_replace( array( '.', ' ', ',' ), array( '-', '-', '-' ), $selector ) );
		echo '<style id="sme-template-styles-' . esc_attr( $style_id ) . '">';
		echo esc_attr( $selector_string ) . ' {';
		
		if ( isset( $styles['background_color'] ) ) {
			echo 'background-color: ' . esc_attr( $styles['background_color'] ) . ';';
		}
		
		if ( isset( $styles['max_width'] ) ) {
			echo 'max-width: ' . intval( $styles['max_width'] ) . 'px;';
		}
		
		if ( isset( $styles['container_padding'] ) ) {
			echo 'padding: ' . intval( $styles['container_padding'] ) . 'px;';
		}
		
		echo '}';
		
		// Title styles
		echo esc_attr( $selector ) . ' .entry-title, ' . esc_attr( $selector ) . ' h1, ' . esc_attr( $selector ) . ' h2 {';
		if ( isset( $styles['title_font_size'] ) ) {
			echo 'font-size: ' . intval( $styles['title_font_size'] ) . 'px;';
		}
		if ( isset( $styles['title_font_weight'] ) ) {
			echo 'font-weight: ' . esc_attr( $styles['title_font_weight'] ) . ';';
		}
		if ( isset( $styles['title_color'] ) ) {
			echo 'color: ' . esc_attr( $styles['title_color'] ) . ';';
		}
		if ( isset( $styles['title_margin_bottom'] ) ) {
			echo 'margin-bottom: ' . intval( $styles['title_margin_bottom'] ) . 'px;';
		}
		echo '}';
		
		// Content styles
		echo esc_attr( $selector ) . ' .entry-content, ' . esc_attr( $selector ) . ' .post-content {';
		if ( isset( $styles['content_font_size'] ) ) {
			echo 'font-size: ' . intval( $styles['content_font_size'] ) . 'px;';
		}
		if ( isset( $styles['content_line_height'] ) ) {
			echo 'line-height: ' . floatval( $styles['content_line_height'] ) . ';';
		}
		if ( isset( $styles['content_color'] ) ) {
			echo 'color: ' . esc_attr( $styles['content_color'] ) . ';';
		}
		echo '}';
		
		// Link styles
		echo esc_attr( $selector ) . ' a {';
		if ( isset( $styles['link_color'] ) ) {
			echo 'color: ' . esc_attr( $styles['link_color'] ) . ';';
		}
		echo '}';
		
		echo esc_attr( $selector ) . ' a:hover {';
		if ( isset( $styles['link_hover_color'] ) ) {
			echo 'color: ' . esc_attr( $styles['link_hover_color'] ) . ';';
		}
		echo '}';
		
		// Post spacing
		if ( isset( $styles['post_spacing'] ) ) {
			echo esc_attr( $selector ) . ' .post, ' . esc_attr( $selector ) . ' article {';
			echo 'margin-bottom: ' . intval( $styles['post_spacing'] ) . 'px;';
			echo '}';
		}
		
		// Columns
		if ( isset( $styles['columns'] ) ) {
			echo esc_attr( $selector ) . ' .posts-grid {';
			echo 'grid-template-columns: repeat(' . intval( $styles['columns'] ) . ', 1fr);';
			echo '}';
		}
		
		// Category badge color
		if ( isset( $styles['category_color'] ) ) {
			echo esc_attr( $selector ) . ' .category-badge, ' . esc_attr( $selector ) . ' .post-category {';
			echo 'background-color: ' . esc_attr( $styles['category_color'] ) . ';';
			echo '}';
		}
		
		echo '</style>';
	}
}

