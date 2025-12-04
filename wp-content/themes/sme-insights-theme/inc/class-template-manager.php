<?php
/**
 * Template Manager
 * Export/Import templates and manage saved templates
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Template_Manager {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Use plugins_loaded for theme-independent initialization
		add_action( 'plugins_loaded', array( $this, 'init_hooks' ), 5 );
		add_action( 'after_setup_theme', array( $this, 'init_hooks' ), 999 );
	}
	
	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		
		// AJAX handlers with high priority
		add_action( 'wp_ajax_sme_export_template', array( $this, 'export_template' ), 10 );
		add_action( 'wp_ajax_sme_import_template', array( $this, 'import_template' ), 10 );
		add_action( 'wp_ajax_sme_duplicate_template', array( $this, 'duplicate_template' ), 10 );
		add_action( 'wp_ajax_sme_reset_header_styles', array( $this, 'reset_header_styles' ), 10 );
		add_action( 'wp_ajax_sme_reset_footer_styles', array( $this, 'reset_footer_styles' ), 10 );
	}
	
	/**
	 * Enqueue admin assets
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( 'appearance_page_sme-template-manager' !== $hook ) {
			return;
		}
		
		// Check if already enqueued to prevent duplicates
		if ( wp_style_is( 'sme-template-manager', 'enqueued' ) ) {
			return;
		}
		
		wp_enqueue_style(
			'sme-template-manager',
			SME_THEME_ASSETS . '/css/template-manager.css',
			array(),
			SME_THEME_VERSION
		);
		
		if ( ! wp_script_is( 'sme-template-manager', 'enqueued' ) ) {
			wp_enqueue_script(
				'sme-template-manager',
				SME_THEME_ASSETS . '/js/template-manager.js',
				array( 'jquery' ),
				SME_THEME_VERSION,
				true
			);
		}
		
		wp_localize_script( 'sme-template-manager', 'smeTemplateManager', array(
			'nonce' => wp_create_nonce( 'sme_template_manager_nonce' ),
		) );
	}
	
	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'themes.php',
			__( 'Template Manager', 'sme-insights' ),
			__( 'Template Manager', 'sme-insights' ),
			'edit_theme_options',
			'sme-template-manager',
			array( $this, 'render_admin_page' )
		);
	}
	
	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		$templates = get_option( 'sme_saved_templates', array() );
		$header_styles = get_option( 'sme_header_styles', array() );
		$footer_styles = get_option( 'sme_footer_styles', array() );
		
		?>
		<div class="wrap sme-template-manager-wrap">
			<h1><?php esc_html_e( 'Template Manager', 'sme-insights' ); ?></h1>
			<p class="description"><?php esc_html_e( 'Manage, export, and import your saved templates.', 'sme-insights' ); ?></p>
			
			<div class="sme-templates-grid">
				<!-- Header Template -->
				<div class="sme-template-card">
					<h2><?php esc_html_e( 'Header Template', 'sme-insights' ); ?></h2>
					<?php if ( ! empty( $header_styles ) ) : ?>
						<p class="sme-template-status">
							<span class="sme-status-active"><?php esc_html_e( 'Active', 'sme-insights' ); ?></span>
						</p>
						<div class="sme-template-actions">
							<button class="button button-secondary sme-export-btn" data-type="header"><?php esc_html_e( 'Export', 'sme-insights' ); ?></button>
							<button class="button button-secondary sme-reset-btn" data-type="header"><?php esc_html_e( 'Reset', 'sme-insights' ); ?></button>
						</div>
					<?php else : ?>
						<p class="sme-template-status">
							<span class="sme-status-inactive"><?php esc_html_e( 'No custom styles', 'sme-insights' ); ?></span>
						</p>
					<?php endif; ?>
				</div>
				
				<!-- Footer Template -->
				<div class="sme-template-card">
					<h2><?php esc_html_e( 'Footer Template', 'sme-insights' ); ?></h2>
					<?php if ( ! empty( $footer_styles ) ) : ?>
						<p class="sme-template-status">
							<span class="sme-status-active"><?php esc_html_e( 'Active', 'sme-insights' ); ?></span>
						</p>
						<div class="sme-template-actions">
							<button class="button button-secondary sme-export-btn" data-type="footer"><?php esc_html_e( 'Export', 'sme-insights' ); ?></button>
							<button class="button button-secondary sme-reset-btn" data-type="footer"><?php esc_html_e( 'Reset', 'sme-insights' ); ?></button>
						</div>
					<?php else : ?>
						<p class="sme-template-status">
							<span class="sme-status-inactive"><?php esc_html_e( 'No custom styles', 'sme-insights' ); ?></span>
						</p>
					<?php endif; ?>
				</div>
			</div>
			
			<!-- Saved Templates -->
			<div class="sme-saved-templates-section">
				<h2><?php esc_html_e( 'Saved Templates', 'sme-insights' ); ?></h2>
				
				<?php if ( ! empty( $templates ) ) : ?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Name', 'sme-insights' ); ?></th>
								<th><?php esc_html_e( 'Type', 'sme-insights' ); ?></th>
								<th><?php esc_html_e( 'Date', 'sme-insights' ); ?></th>
								<th><?php esc_html_e( 'Actions', 'sme-insights' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $templates as $id => $template ) : ?>
								<tr>
									<td><strong><?php echo esc_html( $template['name'] ); ?></strong></td>
									<td><?php echo esc_html( ucfirst( $template['type'] ) ); ?></td>
									<td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $template['date'] ) ) ); ?></td>
									<td>
										<button class="button button-small sme-export-btn" data-template-id="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Export', 'sme-insights' ); ?></button>
										<button class="button button-small sme-duplicate-btn" data-template-id="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Duplicate', 'sme-insights' ); ?></button>
										<button class="button button-small button-link-delete sme-delete-btn" data-template-id="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Delete', 'sme-insights' ); ?></button>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<p><?php esc_html_e( 'No templates saved yet.', 'sme-insights' ); ?></p>
				<?php endif; ?>
			</div>
			
			<!-- Import Section -->
			<div class="sme-import-section">
				<h2><?php esc_html_e( 'Import Template', 'sme-insights' ); ?></h2>
				<form id="smeImportForm" enctype="multipart/form-data">
					<p>
						<input type="file" id="smeImportFile" name="template_file" accept=".json">
						<button type="submit" class="button button-primary"><?php esc_html_e( 'Import Template', 'sme-insights' ); ?></button>
					</p>
					<p class="description"><?php esc_html_e( 'Select a JSON file exported from Template Manager.', 'sme-insights' ); ?></p>
				</form>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Export template via AJAX
	 */
	public function export_template() {
		check_ajax_referer( 'sme_template_manager_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		$type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
		$template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';
		
		$export_data = array();
		
		if ( 'header' === $type ) {
			$export_data = array(
				'type' => 'header',
				'styles' => get_option( 'sme_header_styles', array() ),
				'export_date' => current_time( 'mysql' ),
			);
		} elseif ( 'footer' === $type ) {
			$export_data = array(
				'type' => 'footer',
				'styles' => get_option( 'sme_footer_styles', array() ),
				'export_date' => current_time( 'mysql' ),
			);
		} elseif ( ! empty( $template_id ) ) {
			$templates = get_option( 'sme_saved_templates', array() );
			if ( isset( $templates[ $template_id ] ) ) {
				$export_data = $templates[ $template_id ];
				$export_data['export_date'] = current_time( 'mysql' );
			}
		}
		
		if ( empty( $export_data ) ) {
			wp_send_json_error( array( 'message' => __( 'No data to export', 'sme-insights' ) ) );
		}
		
		wp_send_json_success( array( 
			'data' => $export_data,
			'filename' => 'sme-template-' . $type . $template_id . '-' . date( 'Y-m-d' ) . '.json'
		) );
	}
	
	/**
	 * Import template via AJAX
	 */
	public function import_template() {
		check_ajax_referer( 'sme_template_manager_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		if ( ! isset( $_FILES['template_file'] ) || $_FILES['template_file']['error'] !== UPLOAD_ERR_OK ) {
			wp_send_json_error( array( 'message' => __( 'File upload error', 'sme-insights' ) ) );
		}
		
		$file_content = file_get_contents( $_FILES['template_file']['tmp_name'] );
		$import_data = json_decode( $file_content, true );
		
		if ( ! $import_data || ! isset( $import_data['type'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid template file', 'sme-insights' ) ) );
		}
		
		if ( 'header' === $import_data['type'] && isset( $import_data['styles'] ) ) {
			update_option( 'sme_header_styles', $import_data['styles'] );
			wp_send_json_success( array( 'message' => __( 'Header template imported successfully', 'sme-insights' ) ) );
		} elseif ( 'footer' === $import_data['type'] && isset( $import_data['styles'] ) ) {
			update_option( 'sme_footer_styles', $import_data['styles'] );
			wp_send_json_success( array( 'message' => __( 'Footer template imported successfully', 'sme-insights' ) ) );
		} elseif ( isset( $import_data['name'] ) && isset( $import_data['data'] ) ) {
			$templates = get_option( 'sme_saved_templates', array() );
			$id = 'template_' . time() . '_' . wp_rand( 1000, 9999 );
			$templates[ $id ] = $import_data;
			update_option( 'sme_saved_templates', $templates );
			wp_send_json_success( array( 'message' => __( 'Template imported successfully', 'sme-insights' ) ) );
		}
		
		wp_send_json_error( array( 'message' => __( 'Invalid template format', 'sme-insights' ) ) );
	}
	
	/**
	 * Duplicate template via AJAX
	 */
	public function duplicate_template() {
		check_ajax_referer( 'sme_template_manager_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		if ( ! isset( $_POST['template_id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Template ID is required', 'sme-insights' ) ) );
		}
		
		$template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( wp_unslash( $_POST['template_id'] ) ) : '';
		$templates = get_option( 'sme_saved_templates', array() );
		
		if ( ! isset( $templates[ $template_id ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Template not found', 'sme-insights' ) ) );
		}
		
		$template = $templates[ $template_id ];
		$new_id = 'template_' . time() . '_' . wp_rand( 1000, 9999 );
		
		$templates[ $new_id ] = array(
			'name' => $template['name'] . ' (Copy)',
			'type' => $template['type'],
			'data' => $template['data'],
			'date' => current_time( 'mysql' ),
		);
		
		update_option( 'sme_saved_templates', $templates );
		
		wp_send_json_success( array( 'message' => __( 'Template duplicated successfully', 'sme-insights' ) ) );
	}
	
	/**
	 * Reset header styles
	 */
	public function reset_header_styles() {
		check_ajax_referer( 'sme_template_manager_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		delete_option( 'sme_header_styles' );
		wp_send_json_success( array( 'message' => __( 'Header styles reset successfully', 'sme-insights' ) ) );
	}
	
	/**
	 * Reset footer styles
	 */
	public function reset_footer_styles() {
		check_ajax_referer( 'sme_template_manager_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		delete_option( 'sme_footer_styles' );
		wp_send_json_success( array( 'message' => __( 'Footer styles reset successfully', 'sme-insights' ) ) );
	}
}

