<?php
/**
 * System Check
 * Verifies all Page Builder components are working correctly
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_System_Check {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}
	
	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'themes.php',
			__( 'System Check', 'sme-insights' ),
			__( 'System Check', 'sme-insights' ),
			'edit_theme_options',
			'sme-system-check',
			array( $this, 'render_admin_page' )
		);
	}
	
	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		$checks = $this->run_checks();
		
		?>
		<div class="wrap sme-system-check-wrap">
			<h1><?php esc_html_e( 'Page Builder System Check', 'sme-insights' ); ?></h1>
			<p class="description"><?php esc_html_e( 'Verify all Page Builder components are working correctly.', 'sme-insights' ); ?></p>
			
			<div class="sme-checks-results">
				<?php foreach ( $checks as $check ) : ?>
					<div class="sme-check-item <?php echo esc_attr( $check['status'] ); ?>">
						<div class="sme-check-header">
							<span class="sme-check-icon">
								<?php if ( 'pass' === $check['status'] ) : ?>
									<span class="dashicons dashicons-yes-alt"></span>
								<?php elseif ( 'warning' === $check['status'] ) : ?>
									<span class="dashicons dashicons-warning"></span>
								<?php else : ?>
									<span class="dashicons dashicons-dismiss"></span>
								<?php endif; ?>
							</span>
							<h3><?php echo esc_html( $check['title'] ); ?></h3>
						</div>
						<div class="sme-check-details">
							<p><?php echo esc_html( $check['message'] ); ?></p>
							<?php if ( ! empty( $check['details'] ) ) : ?>
								<ul>
									<?php foreach ( $check['details'] as $detail ) : ?>
										<li><?php echo esc_html( $detail ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		
		<style>
		.sme-system-check-wrap { max-width: 1200px; }
		.sme-checks-results { margin-top: 20px; }
		.sme-check-item { background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin-bottom: 15px; }
		.sme-check-item.pass { border-left: 4px solid #00a32a; }
		.sme-check-item.warning { border-left: 4px solid #dba617; }
		.sme-check-item.fail { border-left: 4px solid #d63638; }
		.sme-check-header { display: flex; align-items: center; margin-bottom: 10px; }
		.sme-check-icon { margin-right: 10px; font-size: 24px; }
		.sme-check-item.pass .sme-check-icon { color: #00a32a; }
		.sme-check-item.warning .sme-check-icon { color: #dba617; }
		.sme-check-item.fail .sme-check-icon { color: #d63638; }
		.sme-check-header h3 { margin: 0; }
		.sme-check-details ul { margin: 10px 0 0 20px; }
		</style>
		<?php
	}
	
	/**
	 * Run all checks
	 */
	private function run_checks() {
		$checks = array();
		
		// Check 1: Visual Editor
		$checks[] = $this->check_visual_editor();
		
		// Check 2: Page Builder Blocks
		$checks[] = $this->check_page_builder_blocks();
		
		// Check 3: Template Customizer
		$checks[] = $this->check_template_customizer();
		
		// Check 4: Template Manager
		$checks[] = $this->check_template_manager();
		
		// Check 5: Files existence
		$checks[] = $this->check_files();
		
		// Check 6: JavaScript files
		$checks[] = $this->check_javascript();
		
		// Check 7: CSS files
		$checks[] = $this->check_css();
		
		// Check 8: AJAX handlers
		$checks[] = $this->check_ajax_handlers();
		
		return $checks;
	}
	
	/**
	 * Check Visual Editor
	 */
	private function check_visual_editor() {
		$status = 'pass';
		$message = __( 'Visual Editor is properly configured.', 'sme-insights' );
		$details = array();
		
		if ( ! class_exists( 'SME_Visual_Editor' ) ) {
			$status = 'fail';
			$message = __( 'Visual Editor class not found.', 'sme-insights' );
		} else {
			$details[] = __( 'Class exists', 'sme-insights' );
			
			// Check if instance exists
			$instance = SME_Visual_Editor::get_instance();
			if ( ! $instance ) {
				$status = 'fail';
				$message = __( 'Visual Editor instance not created.', 'sme-insights' );
			} else {
				$details[] = __( 'Instance created', 'sme-insights' );
			}
			
			// Check if file exists
			$file = SME_THEME_DIR . '/inc/class-visual-editor.php';
			if ( ! file_exists( $file ) ) {
				$status = 'fail';
				$message = __( 'Visual Editor file not found.', 'sme-insights' );
			} else {
				$details[] = __( 'File exists', 'sme-insights' );
			}
		}
		
		return array(
			'title' => __( 'Visual Editor', 'sme-insights' ),
			'status' => $status,
			'message' => $message,
			'details' => $details,
		);
	}
	
	/**
	 * Check Page Builder Blocks
	 */
	private function check_page_builder_blocks() {
		$status = 'pass';
		$message = __( 'Page Builder Blocks are properly configured.', 'sme-insights' );
		$details = array();
		
		if ( ! class_exists( 'SME_Page_Builder_Blocks' ) ) {
			$status = 'fail';
			$message = __( 'Page Builder Blocks class not found.', 'sme-insights' );
		} else {
			$details[] = __( 'Class exists', 'sme-insights' );
			
			$instance = SME_Page_Builder_Blocks::get_instance();
			if ( ! $instance ) {
				$status = 'fail';
				$message = __( 'Page Builder Blocks instance not created.', 'sme-insights' );
			} else {
				$details[] = __( 'Instance created', 'sme-insights' );
			}
			
			$file = SME_THEME_DIR . '/inc/class-page-builder-blocks.php';
			if ( ! file_exists( $file ) ) {
				$status = 'fail';
				$message = __( 'Page Builder Blocks file not found.', 'sme-insights' );
			} else {
				$details[] = __( 'File exists', 'sme-insights' );
			}
			
			// Check if blocks are registered
			if ( function_exists( 'register_block_type' ) ) {
				$details[] = __( 'Block registration available', 'sme-insights' );
			} else {
				$status = 'warning';
				$message = __( 'Block registration function not available (Gutenberg may not be active).', 'sme-insights' );
			}
		}
		
		return array(
			'title' => __( 'Page Builder Blocks', 'sme-insights' ),
			'status' => $status,
			'message' => $message,
			'details' => $details,
		);
	}
	
	/**
	 * Check Template Customizer
	 */
	private function check_template_customizer() {
		$status = 'pass';
		$message = __( 'Template Customizer is properly configured.', 'sme-insights' );
		$details = array();
		
		if ( ! class_exists( 'SME_Template_Customizer' ) ) {
			$status = 'fail';
			$message = __( 'Template Customizer class not found.', 'sme-insights' );
		} else {
			$details[] = __( 'Class exists', 'sme-insights' );
			
			$instance = SME_Template_Customizer::get_instance();
			if ( ! $instance ) {
				$status = 'fail';
				$message = __( 'Template Customizer instance not created.', 'sme-insights' );
			} else {
				$details[] = __( 'Instance created', 'sme-insights' );
			}
			
			$file = SME_THEME_DIR . '/inc/class-template-customizer.php';
			if ( ! file_exists( $file ) ) {
				$status = 'fail';
				$message = __( 'Template Customizer file not found.', 'sme-insights' );
			} else {
				$details[] = __( 'File exists', 'sme-insights' );
			}
		}
		
		return array(
			'title' => __( 'Template Customizer', 'sme-insights' ),
			'status' => $status,
			'message' => $message,
			'details' => $details,
		);
	}
	
	/**
	 * Check Template Manager
	 */
	private function check_template_manager() {
		$status = 'pass';
		$message = __( 'Template Manager is properly configured.', 'sme-insights' );
		$details = array();
		
		if ( ! class_exists( 'SME_Template_Manager' ) ) {
			$status = 'fail';
			$message = __( 'Template Manager class not found.', 'sme-insights' );
		} else {
			$details[] = __( 'Class exists', 'sme-insights' );
			
			$instance = SME_Template_Manager::get_instance();
			if ( ! $instance ) {
				$status = 'fail';
				$message = __( 'Template Manager instance not created.', 'sme-insights' );
			} else {
				$details[] = __( 'Instance created', 'sme-insights' );
			}
			
			$file = SME_THEME_DIR . '/inc/class-template-manager.php';
			if ( ! file_exists( $file ) ) {
				$status = 'fail';
				$message = __( 'Template Manager file not found.', 'sme-insights' );
			} else {
				$details[] = __( 'File exists', 'sme-insights' );
			}
		}
		
		return array(
			'title' => __( 'Template Manager', 'sme-insights' ),
			'status' => $status,
			'message' => $message,
			'details' => $details,
		);
	}
	
	/**
	 * Check files existence
	 */
	private function check_files() {
		$status = 'pass';
		$message = __( 'All required files exist.', 'sme-insights' );
		$details = array();
		$missing = array();
		
		$required_files = array(
			'/inc/class-visual-editor.php',
			'/inc/class-page-builder-blocks.php',
			'/inc/class-template-customizer.php',
			'/inc/class-template-manager.php',
			'/assets/js/visual-editor.js',
			'/assets/js/page-builder-blocks.js',
			'/assets/js/template-customizer-admin.js',
			'/assets/js/template-manager.js',
			'/assets/css/visual-editor.css',
			'/assets/css/visual-editor-admin.css',
			'/assets/css/page-builder-blocks.css',
			'/assets/css/page-builder-blocks-editor.css',
			'/assets/css/template-customizer-admin.css',
			'/assets/css/template-manager.css',
		);
		
		foreach ( $required_files as $file ) {
			$full_path = SME_THEME_DIR . $file;
			if ( file_exists( $full_path ) ) {
				$details[] = basename( $file ) . ' - ' . __( 'Exists', 'sme-insights' );
			} else {
				$missing[] = basename( $file );
				$status = 'fail';
			}
		}
		
		if ( ! empty( $missing ) ) {
			$message = sprintf( __( 'Missing files: %s', 'sme-insights' ), implode( ', ', $missing ) );
		}
		
		return array(
			'title' => __( 'Required Files', 'sme-insights' ),
			'status' => $status,
			'message' => $message,
			'details' => $details,
		);
	}
	
	/**
	 * Check JavaScript files
	 */
	private function check_javascript() {
		$status = 'pass';
		$message = __( 'All JavaScript files are accessible.', 'sme-insights' );
		$details = array();
		
		$js_files = array(
			'visual-editor.js',
			'page-builder-blocks.js',
			'template-customizer-admin.js',
			'template-manager.js',
		);
		
		foreach ( $js_files as $file ) {
			$path = SME_THEME_DIR . '/assets/js/' . $file;
			if ( file_exists( $path ) && is_readable( $path ) ) {
				$size = filesize( $path );
				$details[] = $file . ' - ' . size_format( $size );
			} else {
				$status = 'fail';
				$message = sprintf( __( 'JavaScript file not accessible: %s', 'sme-insights' ), $file );
			}
		}
		
		return array(
			'title' => __( 'JavaScript Files', 'sme-insights' ),
			'status' => $status,
			'message' => $message,
			'details' => $details,
		);
	}
	
	/**
	 * Check CSS files
	 */
	private function check_css() {
		$status = 'pass';
		$message = __( 'All CSS files are accessible.', 'sme-insights' );
		$details = array();
		
		$css_files = array(
			'visual-editor.css',
			'visual-editor-admin.css',
			'page-builder-blocks.css',
			'page-builder-blocks-editor.css',
			'template-customizer-admin.css',
			'template-manager.css',
		);
		
		foreach ( $css_files as $file ) {
			$path = SME_THEME_DIR . '/assets/css/' . $file;
			if ( file_exists( $path ) && is_readable( $path ) ) {
				$size = filesize( $path );
				$details[] = $file . ' - ' . size_format( $size );
			} else {
				$status = 'fail';
				$message = sprintf( __( 'CSS file not accessible: %s', 'sme-insights' ), $file );
			}
		}
		
		return array(
			'title' => __( 'CSS Files', 'sme-insights' ),
			'status' => $status,
			'message' => $message,
			'details' => $details,
		);
	}
	
	/**
	 * Check AJAX handlers
	 */
	private function check_ajax_handlers() {
		$status = 'pass';
		$message = __( 'All AJAX handlers are registered.', 'sme-insights' );
		$details = array();
		
		$ajax_actions = array(
			'sme_save_template',
			'sme_load_template',
			'sme_delete_template',
			'sme_update_element',
			'sme_save_template_styles',
			'sme_load_template_styles',
			'sme_reset_template_styles',
			'sme_export_template',
			'sme_import_template',
			'sme_duplicate_template',
			'sme_reset_header_styles',
			'sme_reset_footer_styles',
		);
		
		global $wp_filter;
		$registered = 0;
		
		foreach ( $ajax_actions as $action ) {
			$hook = 'wp_ajax_' . $action;
			if ( isset( $wp_filter[ $hook ] ) ) {
				$registered++;
				$details[] = $action . ' - ' . __( 'Registered', 'sme-insights' );
			} else {
				$status = 'warning';
				$details[] = $action . ' - ' . __( 'Not registered', 'sme-insights' );
			}
		}
		
		if ( $registered < count( $ajax_actions ) ) {
			$message = sprintf( __( '%d of %d AJAX handlers registered.', 'sme-insights' ), $registered, count( $ajax_actions ) );
		}
		
		return array(
			'title' => __( 'AJAX Handlers', 'sme-insights' ),
			'status' => $status,
			'message' => $message,
			'details' => $details,
		);
	}
}

SME_System_Check::get_instance();

