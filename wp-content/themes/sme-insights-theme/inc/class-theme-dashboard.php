<?php
/**
 * Theme Dashboard - Unified Settings Page
 * All theme settings in one place - Easy access from Dashboard
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Theme_Dashboard {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Add main dashboard menu page
		add_action( 'admin_menu', array( $this, 'add_dashboard_menu' ), 1 );
		// Remove duplicate menu items after menu is built
		add_action( 'admin_menu', array( $this, 'remove_duplicate_menu_items' ), 999 );
		// Handle CSV exports early to prevent header issues
		add_action( 'admin_init', array( $this, 'handle_csv_exports' ), 1 );
		// Add admin bar items in admin area
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_items' ), 100 );
	}
	
	/**
	 * Handle CSV exports early to prevent header modification errors
	 */
	public function handle_csv_exports() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Handle Newsletter Subscribers Export
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'sme-newsletter-subscribers' && 
		     isset( $_GET['action'] ) && $_GET['action'] === 'export' && 
		     check_admin_referer( 'export_subscribers' ) ) {
			$subscribers = get_option( 'sme_newsletter_subscribers', array() );
			
			// Clean any output buffers
			while ( ob_get_level() ) {
				ob_end_clean();
			}
			
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=newsletter-subscribers-' . date( 'Y-m-d' ) . '.csv' );
			
			$output = fopen( 'php://output', 'w' );
			fprintf( $output, chr(0xEF).chr(0xBB).chr(0xBF) ); // UTF-8 BOM
			fputcsv( $output, array( 'Email', 'Subscription Date' ) );
			
			foreach ( $subscribers as $email ) {
				fputcsv( $output, array( $email, 'N/A' ) );
			}
			
			fclose( $output );
			exit;
		}
		
		// Handle Contact Form Submissions Export
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'sme-contact-submissions' && 
		     isset( $_GET['action'] ) && $_GET['action'] === 'export' && 
		     check_admin_referer( 'export_contact_submissions' ) ) {
			$submissions = get_option( 'sme_contact_form_submissions', array() );
			
			// Clean any output buffers
			while ( ob_get_level() ) {
				ob_end_clean();
			}
			
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=contact-submissions-' . date( 'Y-m-d' ) . '.csv' );
			
			$output = fopen( 'php://output', 'w' );
			fprintf( $output, chr(0xEF).chr(0xBB).chr(0xBF) ); // UTF-8 BOM
			fputcsv( $output, array( 'Date', 'Name', 'Email', 'Subject', 'Message', 'IP Address' ) );
			
			foreach ( $submissions as $submission ) {
				fputcsv( $output, array(
					isset( $submission['date'] ) ? $submission['date'] : 'N/A',
					isset( $submission['name'] ) ? $submission['name'] : '',
					isset( $submission['email'] ) ? $submission['email'] : '',
					isset( $submission['subject'] ) ? $submission['subject'] : '',
					isset( $submission['message'] ) ? $submission['message'] : '',
					isset( $submission['ip'] ) ? $submission['ip'] : '',
				) );
			}
			
			fclose( $output );
			exit;
		}
		
		// Handle Article Submissions Export
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'sme-article-submissions' && 
		     isset( $_GET['action'] ) && $_GET['action'] === 'export' && 
		     check_admin_referer( 'export_article_submissions' ) ) {
			$submissions = get_option( 'sme_article_submissions', array() );
			
			// Clean any output buffers
			while ( ob_get_level() ) {
				ob_end_clean();
			}
			
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=article-submissions-' . date( 'Y-m-d' ) . '.csv' );
			
			$output = fopen( 'php://output', 'w' );
			fprintf( $output, chr(0xEF).chr(0xBB).chr(0xBF) ); // UTF-8 BOM
			fputcsv( $output, array( 'Date', 'Name', 'Email', 'Article Title', 'LinkedIn/Portfolio', 'Bio', 'Abstract' ) );
			
			foreach ( $submissions as $submission ) {
				fputcsv( $output, array(
					isset( $submission['date'] ) ? $submission['date'] : 'N/A',
					isset( $submission['name'] ) ? $submission['name'] : '',
					isset( $submission['email'] ) ? $submission['email'] : '',
					isset( $submission['title'] ) ? $submission['title'] : '',
					isset( $submission['linkedin'] ) ? $submission['linkedin'] : '',
					isset( $submission['bio'] ) ? $submission['bio'] : '',
					isset( $submission['abstract'] ) ? $submission['abstract'] : '',
				) );
			}
			
			fclose( $output );
			exit;
		}
		
		// Handle Category Restore
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'sme-restore-category' && 
		     isset( $_GET['action'] ) && $_GET['action'] === 'restore' && 
		     isset( $_GET['category'] ) && 
		     check_admin_referer( 'restore_category_' . sanitize_text_field( wp_unslash( $_GET['category'] ) ) ) ) {
			$category_slug = sanitize_text_field( wp_unslash( $_GET['category'] ) );
			$this->restore_category_data( $category_slug );
		}
	}
	
	/**
	 * Remove duplicate menu items (prevent Newsletter Subscribers from appearing twice)
	 */
	public function remove_duplicate_menu_items() {
		global $submenu;
		
		if ( ! isset( $submenu['sme-insights-dashboard'] ) || ! is_array( $submenu['sme-insights-dashboard'] ) ) {
			return;
		}
		
		// Track unique menu slugs to prevent duplicates
		$seen_slugs = array();
		$filtered_submenu = array();
		
		foreach ( $submenu['sme-insights-dashboard'] as $index => $menu_item ) {
			// menu_item format: [0] => menu_title, [1] => capability, [2] => menu_slug, [3] => page_title
			if ( ! isset( $menu_item[2] ) ) {
				continue;
			}
			
			$menu_slug = $menu_item[2];
			
			// Skip if we've already seen this slug
			if ( in_array( $menu_slug, $seen_slugs, true ) ) {
				continue;
			}
			
			$seen_slugs[] = $menu_slug;
			$filtered_submenu[] = $menu_item;
		}
		
		// Replace submenu with filtered version
		$submenu['sme-insights-dashboard'] = $filtered_submenu;
	}
	
	/**
	 * Add dashboard menu page - Main entry point
	 */
	public function add_dashboard_menu() {
		// Main Dashboard Page
		add_menu_page(
			'SME Insights Settings',
			'SME Insights',
			'manage_options',
			'sme-insights-dashboard',
			array( $this, 'render_dashboard' ),
			'dashicons-admin-settings',
			30
		);
		
		// Content Manager (submenu)
		add_submenu_page(
			'sme-insights-dashboard',
			'Content Manager',
			'Content Manager',
			'manage_options',
			'sme-content-manager',
			array( $this, 'render_content_manager' )
		);
		
		// Cache Settings (submenu)
		add_submenu_page(
			'sme-insights-dashboard',
			'Cache Settings',
			'Cache Settings',
			'manage_options',
			'sme-cache-settings',
			array( $this, 'render_cache_settings' )
		);
		
		// Image Optimizer (submenu)
		add_submenu_page(
			'sme-insights-dashboard',
			'Image Optimizer',
			'Image Optimizer',
			'manage_options',
			'sme-image-optimizer',
			array( $this, 'render_image_optimizer' )
		);
		
		// Sitemap Settings (submenu)
		add_submenu_page(
			'sme-insights-dashboard',
			'Sitemap Settings',
			'Sitemap Settings',
			'manage_options',
			'sme-sitemap-settings',
			array( $this, 'render_sitemap_settings' )
		);
		
		// Coming Soon Settings (submenu)
		add_submenu_page(
			'sme-insights-dashboard',
			'Coming Soon',
			'Coming Soon',
			'manage_options',
			'sme-coming-soon',
			array( $this, 'render_coming_soon' )
		);
		
		// Newsletter Subscribers (submenu)
		add_submenu_page(
			'sme-insights-dashboard',
			'Newsletter Subscribers',
			'Newsletter Subscribers',
			'manage_options',
			'sme-newsletter-subscribers',
			array( $this, 'render_newsletter_subscribers' )
		);
		
		// Contact Form Submissions (submenu)
		add_submenu_page(
			'sme-insights-dashboard',
			'Contact Form Submissions',
			'Contact Submissions',
			'manage_options',
			'sme-contact-submissions',
			array( $this, 'render_contact_submissions' )
		);
		
		// Article Submissions (submenu)
		add_submenu_page(
			'sme-insights-dashboard',
			'Article Submissions',
			'Article Submissions',
			'manage_options',
			'sme-article-submissions',
			array( $this, 'render_article_submissions' )
		);
		
		// Category Icons (submenu)
		add_submenu_page(
			'sme-insights-dashboard',
			'Category Icons',
			'Category Icons',
			'manage_options',
			'sme-category-icons',
			array( $this, 'render_category_icons' )
		);
		
		// Restore Category (submenu)
		add_submenu_page(
			'sme-insights-dashboard',
			'Restore Category',
			'Restore Category',
			'manage_options',
			'sme-restore-category',
			array( $this, 'render_restore_category' )
		);
		
		// Theme Settings (submenu) - All theme customization in one place
		add_submenu_page(
			'sme-insights-dashboard',
			'Theme Settings',
			'Theme Settings',
			'manage_options',
			'sme-theme-settings',
			array( $this, 'render_theme_settings' )
		);
	}
	
	/**
	 * Render main dashboard page
	 */
	public function render_dashboard() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Show update notice
		$last_update = get_option( 'sme_theme_last_update', '' );
		$current_version = '1.0.3';
		$saved_version = get_option( 'sme_theme_version', '1.0.0' );
		
		if ( version_compare( $saved_version, $current_version, '<' ) ) {
			update_option( 'sme_theme_version', $current_version );
			$last_update = current_time( 'mysql' );
			update_option( 'sme_theme_last_update', $last_update );
		}
		
		if ( ! empty( $last_update ) ) {
			echo '<div class="notice notice-info is-dismissible" style="margin: 20px 20px 0 0;">';
			echo '<h3 style="margin-top: 0;">🎉 Theme Updated Successfully!</h3>';
			echo '<p><strong>Latest Updates (v' . esc_html( $current_version ) . '):</strong></p>';
			echo '<ul style="margin-left: 20px;">';
			echo '<li>✅ Fixed Breaking News gradient background (blue gradient)</li>';
			echo '<li>✅ Fixed Sidebar sticky position on single post pages</li>';
			echo '<li>✅ Reordered post elements (title above categories)</li>';
			echo '<li>✅ Fixed Main News visibility on mobile devices</li>';
			echo '<li>✅ Improved responsive design for all homepage sections</li>';
			echo '<li>✅ Enhanced sidebar sticky functionality with better browser support</li>';
			echo '</ul>';
			echo '<p><strong>Update Date:</strong> ' . esc_html( date_i18n( 'F j, Y g:i A', strtotime( $last_update ) ) ) . '</p>';
			echo '</div>';
		}
		
		$posts_count = wp_count_posts( 'post' )->publish;
		$pages_count = wp_count_posts( 'page' )->publish;
		$media_count = wp_count_posts( 'attachment' )->inherit;
		$cache_enabled = get_option( 'sme_cache_enabled', true );
		$image_optimizer_enabled = get_option( 'sme_image_optimizer_enabled', true );
		$cdn_enabled = get_option( 'sme_cdn_enabled', false );
		?>
		<div class="wrap">
			<h1>SME Insights Dashboard</h1>
			<p class="description">Manage all theme settings from one place. All features are accessible here.</p>
			
			<div class="sme-dashboard" style="max-width: 1400px; margin-top: 30px;">
				
				<!-- Quick Stats -->
				<div class="sme-stats" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
					<div class="stat-box" style="background: linear-gradient(135deg, #2271b1 0%, #135e96 100%); color: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
						<h3 style="margin: 0 0 10px; color: #fff; font-size: 14px; font-weight: 600; text-transform: uppercase;">Posts</h3>
						<p style="font-size: 2.5rem; font-weight: 700; margin: 0; color: #fff;"><?php echo esc_html( $posts_count ); ?></p>
					</div>
					<div class="stat-box" style="background: linear-gradient(135deg, #00a32a 0%, #008a20 100%); color: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
						<h3 style="margin: 0 0 10px; color: #fff; font-size: 14px; font-weight: 600; text-transform: uppercase;">Pages</h3>
						<p style="font-size: 2.5rem; font-weight: 700; margin: 0; color: #fff;"><?php echo esc_html( $pages_count ); ?></p>
					</div>
					<div class="stat-box" style="background: linear-gradient(135deg, #d63638 0%, #b32d2e 100%); color: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
						<h3 style="margin: 0 0 10px; color: #fff; font-size: 14px; font-weight: 600; text-transform: uppercase;">Media</h3>
						<p style="font-size: 2.5rem; font-weight: 700; margin: 0; color: #fff;"><?php echo esc_html( $media_count ); ?></p>
					</div>
					<div class="stat-box" style="background: linear-gradient(135deg, #8c8f94 0%, #646970 100%); color: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
						<h3 style="margin: 0 0 10px; color: #fff; font-size: 14px; font-weight: 600; text-transform: uppercase;">Status</h3>
						<p style="font-size: 1.2rem; font-weight: 600; margin: 0; color: #fff;">
							<?php echo $cache_enabled ? '✅ Cache ON' : '❌ Cache OFF'; ?><br>
							<?php echo $image_optimizer_enabled ? '✅ Images ON' : '❌ Images OFF'; ?>
						</p>
					</div>
				</div>
				
				<!-- Quick Actions -->
				<div class="sme-quick-actions" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
					
					<!-- Content Manager -->
					<div class="action-card" style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
						<div style="font-size: 3rem; margin-bottom: 15px;">📝</div>
						<h2 style="margin: 0 0 10px; color: #2271b1;">Content Manager</h2>
						<p style="color: #646970; line-height: 1.6; margin-bottom: 20px;">Manage posts, pages, and content. Delete or re-import content easily.</p>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-content-manager' ) ); ?>" class="button button-primary" style="padding: 10px 25px; font-size: 14px; font-weight: 600;">
							Open Content Manager
						</a>
					</div>
					
					<!-- Cache Settings -->
					<div class="action-card" style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
						<div style="font-size: 3rem; margin-bottom: 15px;">⚡</div>
						<h2 style="margin: 0 0 10px; color: #2271b1;">Cache Settings</h2>
						<p style="color: #646970; line-height: 1.6; margin-bottom: 20px;">Configure browser caching and CDN settings for better performance.</p>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-cache-settings' ) ); ?>" class="button button-primary" style="padding: 10px 25px; font-size: 14px; font-weight: 600;">
							Open Cache Settings
						</a>
					</div>
					
					<!-- Image Optimizer -->
					<div class="action-card" style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
						<div style="font-size: 3rem; margin-bottom: 15px;">🖼️</div>
						<h2 style="margin: 0 0 10px; color: #2271b1;">Image Optimizer</h2>
						<p style="color: #646970; line-height: 1.6; margin-bottom: 20px;">Automatically optimize images on upload. Resize, compress, and add alt text.</p>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-image-optimizer' ) ); ?>" class="button button-primary" style="padding: 10px 25px; font-size: 14px; font-weight: 600;">
							Open Image Optimizer
						</a>
					</div>
					
					<!-- Newsletter Subscribers -->
					<div class="action-card" style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
						<div style="font-size: 3rem; margin-bottom: 15px;">📧</div>
						<h2 style="margin: 0 0 10px; color: #2271b1;">Newsletter Subscribers</h2>
						<p style="color: #646970; line-height: 1.6; margin-bottom: 20px;">View and manage newsletter subscribers. Export emails or delete subscribers.</p>
						<?php
						$subscribers_count = sme_get_newsletter_subscribers_count();
						?>
						<p style="font-size: 16px; font-weight: 600; color: #2271b1; margin-bottom: 15px;">Total: <?php echo esc_html( $subscribers_count ); ?> subscribers</p>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-newsletter-subscribers' ) ); ?>" class="button button-primary" style="padding: 10px 25px; font-size: 14px; font-weight: 600;">
							Manage Subscribers
						</a>
					</div>
					
					<!-- Contact Form Submissions -->
					<div class="action-card" style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
						<div style="font-size: 3rem; margin-bottom: 15px;">📬</div>
						<h2 style="margin: 0 0 10px; color: #2271b1;">Contact Submissions</h2>
						<p style="color: #646970; line-height: 1.6; margin-bottom: 20px;">View and manage contact form submissions from your website.</p>
						<?php
						$contact_submissions = get_option( 'sme_contact_form_submissions', array() );
						$contact_count = count( $contact_submissions );
						?>
						<p style="font-size: 16px; font-weight: 600; color: #2271b1; margin-bottom: 15px;">Total: <?php echo esc_html( $contact_count ); ?> submissions</p>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-contact-submissions' ) ); ?>" class="button button-primary" style="padding: 10px 25px; font-size: 14px; font-weight: 600;">
							View Submissions
						</a>
					</div>
					
					<!-- Article Submissions -->
					<div class="action-card" style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
						<div style="font-size: 3rem; margin-bottom: 15px;">📝</div>
						<h2 style="margin: 0 0 10px; color: #2271b1;">Article Submissions</h2>
						<p style="color: #646970; line-height: 1.6; margin-bottom: 20px;">View and manage article submissions from contributors.</p>
						<?php
						$article_submissions = get_option( 'sme_article_submissions', array() );
						$article_count = count( $article_submissions );
						?>
						<p style="font-size: 16px; font-weight: 600; color: #2271b1; margin-bottom: 15px;">Total: <?php echo esc_html( $article_count ); ?> submissions</p>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-article-submissions' ) ); ?>" class="button button-primary" style="padding: 10px 25px; font-size: 14px; font-weight: 600;">
							View Submissions
						</a>
					</div>
					
				</div>
				
				<!-- Settings Overview -->
				<div class="sme-settings-overview" style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
					<h2 style="margin-top: 0; color: #1d2327;">Current Settings Overview</h2>
					<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
						<div>
							<h3 style="color: #2271b1; margin-bottom: 15px;">Cache Settings</h3>
							<ul style="list-style: none; padding: 0; margin: 0;">
								<li style="padding: 8px 0; border-bottom: 1px solid #f0f0f1;">
									<strong>Browser Cache:</strong> <?php echo $cache_enabled ? '<span style="color: #00a32a;">✅ Enabled</span>' : '<span style="color: #d63638;">❌ Disabled</span>'; ?>
								</li>
								<li style="padding: 8px 0; border-bottom: 1px solid #f0f0f1;">
									<strong>Cache Duration:</strong> <?php echo esc_html( get_option( 'sme_cache_duration', 3600 ) / 3600 ); ?> hours
								</li>
								<li style="padding: 8px 0; border-bottom: 1px solid #f0f0f1;">
									<strong>CDN:</strong> <?php echo $cdn_enabled ? '<span style="color: #00a32a;">✅ Enabled</span>' : '<span style="color: #d63638;">❌ Disabled</span>'; ?>
								</li>
								<?php if ( $cdn_enabled ) : ?>
								<li style="padding: 8px 0;">
									<strong>CDN URL:</strong> <?php echo esc_html( get_option( 'sme_cdn_url', '' ) ); ?>
								</li>
								<?php endif; ?>
							</ul>
						</div>
						<div>
							<h3 style="color: #2271b1; margin-bottom: 15px;">Image Optimizer</h3>
							<ul style="list-style: none; padding: 0; margin: 0;">
								<li style="padding: 8px 0; border-bottom: 1px solid #f0f0f1;">
									<strong>Optimization:</strong> <?php echo $image_optimizer_enabled ? '<span style="color: #00a32a;">✅ Enabled</span>' : '<span style="color: #d63638;">❌ Disabled</span>'; ?>
								</li>
								<?php if ( $image_optimizer_enabled ) : ?>
								<li style="padding: 8px 0; border-bottom: 1px solid #f0f0f1;">
									<strong>Max Dimensions:</strong> <?php echo esc_html( get_option( 'sme_image_max_width', 1920 ) ); ?>x<?php echo esc_html( get_option( 'sme_image_max_height', 1080 ) ); ?>
								</li>
								<li style="padding: 8px 0; border-bottom: 1px solid #f0f0f1;">
									<strong>Quality:</strong> <?php echo esc_html( get_option( 'sme_image_quality', 85 ) ); ?>%
								</li>
								<li style="padding: 8px 0;">
									<strong>Auto Alt Text:</strong> <?php echo get_option( 'sme_image_auto_alt', true ) ? '<span style="color: #00a32a;">✅ Enabled</span>' : '<span style="color: #d63638;">❌ Disabled</span>'; ?>
								</li>
								<?php endif; ?>
							</ul>
						</div>
					</div>
				</div>
				
				<!-- Theme Settings - All in One Place -->
				<?php
				// Handle form submissions
				if ( isset( $_POST['sme_dashboard_settings_submit'] ) && check_admin_referer( 'sme_dashboard_settings', 'sme_dashboard_nonce' ) ) {
					// Social Media Settings
					if ( isset( $_POST['social_facebook'] ) ) {
						set_theme_mod( 'social_facebook', esc_url_raw( $_POST['social_facebook'] ) );
					}
					if ( isset( $_POST['social_twitter'] ) ) {
						set_theme_mod( 'social_twitter', esc_url_raw( $_POST['social_twitter'] ) );
					}
					if ( isset( $_POST['social_linkedin'] ) ) {
						set_theme_mod( 'social_linkedin', esc_url_raw( $_POST['social_linkedin'] ) );
					}
					if ( isset( $_POST['social_youtube'] ) ) {
						set_theme_mod( 'social_youtube', esc_url_raw( $_POST['social_youtube'] ) );
					}
					if ( isset( $_POST['social_instagram'] ) ) {
						set_theme_mod( 'social_instagram', esc_url_raw( $_POST['social_instagram'] ) );
					}
					
					// Header Settings
					if ( isset( $_POST['header_logo_text'] ) ) {
						set_theme_mod( 'header_logo_text', sanitize_text_field( $_POST['header_logo_text'] ) );
					}
					if ( isset( $_POST['header_top_bar_text'] ) ) {
						set_theme_mod( 'header_top_bar_text', sanitize_text_field( $_POST['header_top_bar_text'] ) );
					}
					
					// Footer Settings
					if ( isset( $_POST['footer_company_name'] ) ) {
						set_theme_mod( 'footer_company_name', sanitize_text_field( $_POST['footer_company_name'] ) );
					}
					
					// Contact Form Email
					if ( isset( $_POST['sme_contact_form_email'] ) ) {
						set_theme_mod( 'sme_contact_form_email', sanitize_email( $_POST['sme_contact_form_email'] ) );
					}
					
					echo '<div class="notice notice-success is-dismissible" style="margin: 20px 20px 0 0;"><p>✅ Settings saved successfully!</p></div>';
				}
				
				// Get current values
				$social_facebook = get_theme_mod( 'social_facebook', 'https://facebook.com/smeinsights' );
				$social_twitter = get_theme_mod( 'social_twitter', 'https://twitter.com/smeinsights' );
				$social_linkedin = get_theme_mod( 'social_linkedin', 'https://linkedin.com/company/smeinsights' );
				$social_youtube = get_theme_mod( 'social_youtube', 'https://youtube.com/@smeinsights' );
				$social_instagram = get_theme_mod( 'social_instagram', 'https://instagram.com/smeinsights' );
				$header_logo_text = get_theme_mod( 'header_logo_text', 'SME INSIGHTS' );
				$header_top_bar_text = get_theme_mod( 'header_top_bar_text', 'Become a Contributor' );
				$footer_company_name = get_theme_mod( 'footer_company_name', 'SME INSIGHTS' );
				$contact_form_email = get_theme_mod( 'sme_contact_form_email', get_option( 'admin_email' ) );
				?>
				
				<!-- All Theme Settings Form -->
				<form method="post" action="" style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
					<?php wp_nonce_field( 'sme_dashboard_settings', 'sme_dashboard_nonce' ); ?>
					
					<h2 style="margin-top: 0; color: #1d2327; padding-bottom: 15px; border-bottom: 2px solid #f0f0f1;">⚙️ Theme Settings</h2>
					
					<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; margin-top: 25px;">
						
						<!-- Social Media Settings -->
						<div>
							<h3 style="color: #2271b1; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #f0f0f1;">📱 Social Media Links</h3>
							<table class="form-table" style="margin-top: 0;">
								<tbody>
									<tr>
										<th scope="row"><label for="social_facebook">Facebook URL</label></th>
										<td><input type="url" id="social_facebook" name="social_facebook" value="<?php echo esc_attr( $social_facebook ); ?>" class="regular-text"></td>
									</tr>
									<tr>
										<th scope="row"><label for="social_twitter">Twitter URL</label></th>
										<td><input type="url" id="social_twitter" name="social_twitter" value="<?php echo esc_attr( $social_twitter ); ?>" class="regular-text"></td>
									</tr>
									<tr>
										<th scope="row"><label for="social_linkedin">LinkedIn URL</label></th>
										<td><input type="url" id="social_linkedin" name="social_linkedin" value="<?php echo esc_attr( $social_linkedin ); ?>" class="regular-text"></td>
									</tr>
									<tr>
										<th scope="row"><label for="social_youtube">YouTube URL</label></th>
										<td><input type="url" id="social_youtube" name="social_youtube" value="<?php echo esc_attr( $social_youtube ); ?>" class="regular-text"></td>
									</tr>
									<tr>
										<th scope="row"><label for="social_instagram">Instagram URL</label></th>
										<td><input type="url" id="social_instagram" name="social_instagram" value="<?php echo esc_attr( $social_instagram ); ?>" class="regular-text"></td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<!-- Header & Footer Settings -->
						<div>
							<h3 style="color: #2271b1; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #f0f0f1;">🎨 Header & Footer</h3>
							<table class="form-table" style="margin-top: 0;">
								<tbody>
									<tr>
										<th scope="row"><label for="header_logo_text">Logo Text</label></th>
										<td><input type="text" id="header_logo_text" name="header_logo_text" value="<?php echo esc_attr( $header_logo_text ); ?>" class="regular-text"></td>
									</tr>
									<tr>
										<th scope="row"><label for="header_top_bar_text">Top Bar Link Text</label></th>
										<td><input type="text" id="header_top_bar_text" name="header_top_bar_text" value="<?php echo esc_attr( $header_top_bar_text ); ?>" class="regular-text"></td>
									</tr>
									<tr>
										<th scope="row"><label for="footer_company_name">Footer Company Name</label></th>
										<td><input type="text" id="footer_company_name" name="footer_company_name" value="<?php echo esc_attr( $footer_company_name ); ?>" class="regular-text"></td>
									</tr>
								</tbody>
							</table>
							
							<h3 style="color: #2271b1; margin-top: 30px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #f0f0f1;">📧 Contact Form</h3>
							<table class="form-table" style="margin-top: 0;">
								<tbody>
									<tr>
										<th scope="row"><label for="sme_contact_form_email">Contact Form Email</label></th>
										<td>
											<input type="email" id="sme_contact_form_email" name="sme_contact_form_email" value="<?php echo esc_attr( $contact_form_email ); ?>" class="regular-text">
											<p class="description">Email address where contact form submissions will be sent</p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						
					</div>
					
					<p class="submit" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #f0f0f1;">
						<input type="submit" name="sme_dashboard_settings_submit" class="button button-primary button-large" value="💾 Save All Settings">
						<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-secondary" style="margin-left: 10px;">🎨 Open Full Customizer</a>
					</p>
				</form>
				
				<!-- Quick Links -->
				<div class="sme-quick-links" style="background: #f0f6fc; padding: 25px; border-left: 4px solid #2271b1; border-radius: 4px;">
					<h3 style="margin-top: 0; color: #1d2327;">🔗 Quick Links</h3>
					<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 15px;">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-content-manager' ) ); ?>" style="color: #2271b1; text-decoration: none; padding: 10px; background: #fff; border-radius: 6px; display: block; text-align: center; font-weight: 600;">
							📝 Content Manager
						</a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-cache-settings' ) ); ?>" style="color: #2271b1; text-decoration: none; padding: 10px; background: #fff; border-radius: 6px; display: block; text-align: center; font-weight: 600;">
							⚡ Cache Settings
						</a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-image-optimizer' ) ); ?>" style="color: #2271b1; text-decoration: none; padding: 10px; background: #fff; border-radius: 6px; display: block; text-align: center; font-weight: 600;">
							🖼️ Image Optimizer
						</a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-newsletter-subscribers' ) ); ?>" style="color: #2271b1; text-decoration: none; padding: 10px; background: #fff; border-radius: 6px; display: block; text-align: center; font-weight: 600;">
							📧 Newsletter Subscribers
						</a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-contact-submissions' ) ); ?>" style="color: #2271b1; text-decoration: none; padding: 10px; background: #fff; border-radius: 6px; display: block; text-align: center; font-weight: 600;">
							📬 Contact Submissions
						</a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-article-submissions' ) ); ?>" style="color: #2271b1; text-decoration: none; padding: 10px; background: #fff; border-radius: 6px; display: block; text-align: center; font-weight: 600;">
							📝 Article Submissions
						</a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-category-icons' ) ); ?>" style="color: #2271b1; text-decoration: none; padding: 10px; background: #fff; border-radius: 6px; display: block; text-align: center; font-weight: 600;">
							📋 Category Icons
						</a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-sitemap-settings' ) ); ?>" style="color: #2271b1; text-decoration: none; padding: 10px; background: #fff; border-radius: 6px; display: block; text-align: center; font-weight: 600;">
							🗺️ Sitemap Settings
						</a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-coming-soon' ) ); ?>" style="color: #2271b1; text-decoration: none; padding: 10px; background: #fff; border-radius: 6px; display: block; text-align: center; font-weight: 600;">
							🚀 Coming Soon
						</a>
						<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" style="color: #2271b1; text-decoration: none; padding: 10px; background: #fff; border-radius: 6px; display: block; text-align: center; font-weight: 600;">
							🎨 Full Customizer
						</a>
					</div>
				</div>
				
			</div>
			
			<!-- Developer Credit -->
			<div style="background: #f0f6fc; padding: 20px; border-top: 2px solid #2271b1; margin-top: 30px; border-radius: 4px; text-align: center;">
				<p style="margin: 0; font-size: 14px; color: #50575e;">
					<strong>Theme developed by:</strong> 
					<a href="https://prortec.com/remon-romany/" target="_blank" rel="noopener noreferrer" style="color: #2271b1; text-decoration: none; font-weight: 600;">Remon Romany</a> 
					| Senior Strategic Web Developer
				</p>
				<p style="margin: 10px 0 0; font-size: 12px; color: #646970;">
					Portfolio: <a href="https://prortec.com/remon-romany/" target="_blank" rel="noopener noreferrer" style="color: #2271b1;">https://prortec.com/remon-romany/</a>
				</p>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render Content Manager (delegate to SME_Content_Manager)
	 */
	public function render_content_manager() {
		if ( class_exists( 'SME_Content_Manager' ) ) {
			$content_manager = SME_Content_Manager::get_instance();
			$content_manager->render_admin_page();
		}
	}
	
	/**
	 * Render Cache Settings (delegate to SME_Cache)
	 */
	public function render_cache_settings() {
		if ( class_exists( 'SME_Cache' ) ) {
			$cache = SME_Cache::get_instance();
			$cache->render_admin_page();
		}
	}
	
	/**
	 * Render Image Optimizer (delegate to SME_Image_Optimizer)
	 */
	public function render_image_optimizer() {
		if ( class_exists( 'SME_Image_Optimizer' ) ) {
			$optimizer = SME_Image_Optimizer::get_instance();
			$optimizer->render_admin_page();
		}
	}
	
	/**
	 * Render Sitemap Settings
	 */
	public function render_sitemap_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Handle form submission
		if ( isset( $_POST['sme_sitemap_submit'] ) && check_admin_referer( 'sme_sitemap_settings', 'sme_sitemap_nonce' ) ) {
			update_option( 'sme_sitemap_enabled', isset( $_POST['sme_sitemap_enabled'] ) );
			update_option( 'sme_sitemap_ping_google', isset( $_POST['sme_sitemap_ping_google'] ) );
			update_option( 'sme_sitemap_ping_bing', isset( $_POST['sme_sitemap_ping_bing'] ) );
			
			// Flush rewrite rules
			flush_rewrite_rules();
			
			echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully!</p></div>';
		}
		
		$sitemap_enabled = get_option( 'sme_sitemap_enabled', true );
		$ping_google = get_option( 'sme_sitemap_ping_google', true );
		$ping_bing = get_option( 'sme_sitemap_ping_bing', true );
		?>
		<div class="wrap">
			<h1>Sitemap Settings</h1>
			<p class="description">Configure XML sitemap settings. Sitemap is automatically generated and available at <a href="<?php echo esc_url( home_url( '/sitemap.xml' ) ); ?>" target="_blank"><?php echo esc_url( home_url( '/sitemap.xml' ) ); ?></a></p>
			
			<form method="post" action="">
				<?php wp_nonce_field( 'sme_sitemap_settings', 'sme_sitemap_nonce' ); ?>
				
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="sme_sitemap_enabled">Enable Sitemap</label>
							</th>
							<td>
								<input type="checkbox" id="sme_sitemap_enabled" name="sme_sitemap_enabled" value="1" <?php checked( $sitemap_enabled, true ); ?>>
								<label for="sme_sitemap_enabled">Enable automatic XML sitemap generation</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="sme_sitemap_ping_google">Ping Google</label>
							</th>
							<td>
								<input type="checkbox" id="sme_sitemap_ping_google" name="sme_sitemap_ping_google" value="1" <?php checked( $ping_google, true ); ?>>
								<label for="sme_sitemap_ping_google">Automatically notify Google when new content is published</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="sme_sitemap_ping_bing">Ping Bing</label>
							</th>
							<td>
								<input type="checkbox" id="sme_sitemap_ping_bing" name="sme_sitemap_ping_bing" value="1" <?php checked( $ping_bing, true ); ?>>
								<label for="sme_sitemap_ping_bing">Automatically notify Bing when new content is published</label>
							</td>
						</tr>
					</tbody>
				</table>
				
				<p class="submit">
					<input type="submit" name="sme_sitemap_submit" class="button button-primary" value="Save Settings">
				</p>
			</form>
			
			<div style="background: #f0f6fc; padding: 20px; border-left: 4px solid #2271b1; margin-top: 30px; border-radius: 4px;">
				<h3>📋 Sitemap URLs</h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/sitemap.xml' ) ); ?>" target="_blank">Main Sitemap</a></li>
					<li><a href="<?php echo esc_url( home_url( '/sitemap-posts.xml' ) ); ?>" target="_blank">Posts Sitemap</a></li>
					<li><a href="<?php echo esc_url( home_url( '/sitemap-pages.xml' ) ); ?>" target="_blank">Pages Sitemap</a></li>
					<li><a href="<?php echo esc_url( home_url( '/sitemap-categories.xml' ) ); ?>" target="_blank">Categories Sitemap</a></li>
				</ul>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render Coming Soon settings page
	 */
	public function render_coming_soon() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Handle form submission
		if ( isset( $_POST['sme_coming_soon_submit'] ) && check_admin_referer( 'sme_coming_soon_settings', 'sme_coming_soon_nonce' ) ) {
			// Explicitly handle checkbox - if not set, it's false
			$enable_coming_soon = isset( $_POST['sme_enable_coming_soon'] ) && $_POST['sme_enable_coming_soon'] === '1' ? true : false;
			
			// Use wp_unslash to remove backslashes before sanitizing
			$title = isset( $_POST['sme_coming_soon_title'] ) ? sanitize_text_field( wp_unslash( $_POST['sme_coming_soon_title'] ) ) : '';
			$description = isset( $_POST['sme_coming_soon_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['sme_coming_soon_description'] ) ) : '';
			$email_placeholder = isset( $_POST['sme_coming_soon_email_placeholder'] ) ? sanitize_text_field( wp_unslash( $_POST['sme_coming_soon_email_placeholder'] ) ) : '';
			$notification_email = isset( $_POST['sme_coming_soon_notification_email'] ) ? sanitize_email( wp_unslash( $_POST['sme_coming_soon_notification_email'] ) ) : '';
			
			// Countdown settings
			$countdown_enabled = isset( $_POST['sme_coming_soon_countdown_enable'] ) && $_POST['sme_coming_soon_countdown_enable'] === '1' ? true : false;
			$countdown_date = isset( $_POST['sme_coming_soon_countdown_date'] ) ? sanitize_text_field( $_POST['sme_coming_soon_countdown_date'] ) : '';
			$countdown_time = isset( $_POST['sme_coming_soon_countdown_time'] ) ? sanitize_text_field( $_POST['sme_coming_soon_countdown_time'] ) : '12:00';
			
			// Progress bar settings
			$progress_enabled = isset( $_POST['sme_coming_soon_progress_enable'] ) && $_POST['sme_coming_soon_progress_enable'] === '1' ? true : false;
			$progress_percentage = isset( $_POST['sme_coming_soon_progress_percentage'] ) ? intval( $_POST['sme_coming_soon_progress_percentage'] ) : 75;
			$progress_percentage = max( 0, min( 100, $progress_percentage ) ); // Clamp between 0-100
			
			// Contact settings
			$contact_email = isset( $_POST['sme_coming_soon_contact_email'] ) ? sanitize_email( wp_unslash( $_POST['sme_coming_soon_contact_email'] ) ) : '';
			$show_contact_email = isset( $_POST['sme_coming_soon_show_contact_email'] ) && $_POST['sme_coming_soon_show_contact_email'] === '1' ? true : false;
			$show_social_media = isset( $_POST['sme_coming_soon_show_social_media'] ) && $_POST['sme_coming_soon_show_social_media'] === '1' ? true : false;
			
			// Save settings - explicitly save as boolean
			// If disabled, remove the option
			if ( $enable_coming_soon ) {
				set_theme_mod( 'sme_enable_coming_soon', true );
			} else {
				// Remove the option when disabled
				remove_theme_mod( 'sme_enable_coming_soon' );
				// Also delete from options directly to ensure it's gone
				$theme_mods = get_option( 'theme_mods_' . get_stylesheet(), array() );
				unset( $theme_mods['sme_enable_coming_soon'] );
				update_option( 'theme_mods_' . get_stylesheet(), $theme_mods );
			}
			
			set_theme_mod( 'sme_coming_soon_title', $title );
			set_theme_mod( 'sme_coming_soon_description', $description );
			set_theme_mod( 'sme_coming_soon_email_placeholder', $email_placeholder );
			set_theme_mod( 'sme_coming_soon_notification_email', $notification_email );
			set_theme_mod( 'sme_coming_soon_countdown_enable', $countdown_enabled );
			set_theme_mod( 'sme_coming_soon_countdown_date', $countdown_date );
			set_theme_mod( 'sme_coming_soon_countdown_time', $countdown_time );
			set_theme_mod( 'sme_coming_soon_progress_enable', $progress_enabled );
			set_theme_mod( 'sme_coming_soon_progress_percentage', $progress_percentage );
			set_theme_mod( 'sme_coming_soon_contact_email', $contact_email );
			set_theme_mod( 'sme_coming_soon_show_contact_email', $show_contact_email );
			set_theme_mod( 'sme_coming_soon_show_social_media', $show_social_media );
			
			// Clear any caches
			if ( function_exists( 'wp_cache_flush' ) ) {
				wp_cache_flush();
			}
			
			// Clear object cache
			wp_cache_delete( 'theme_mods_' . get_stylesheet(), 'theme_mods' );
			wp_cache_delete( 'theme_mods_' . get_stylesheet(), 'options' );
			
			$status_text = $enable_coming_soon ? 'enabled' : 'disabled';
			echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully! Coming Soon mode is now <strong>' . esc_html( $status_text ) . '</strong>.</p></div>';
		}
		
		// Handle delete all subscribers
		if ( isset( $_POST['sme_delete_all_subscribers'] ) && check_admin_referer( 'sme_delete_subscribers', 'sme_delete_subscribers_nonce' ) ) {
			delete_option( 'sme_coming_soon_subscribers' );
			echo '<div class="notice notice-success is-dismissible"><p>All subscribers have been deleted.</p></div>';
		}
		
		// Get the actual value - if not set, it's false
		$enable_coming_soon_raw = get_theme_mod( 'sme_enable_coming_soon', false );
		$enable_coming_soon = ( $enable_coming_soon_raw === true || $enable_coming_soon_raw === '1' || $enable_coming_soon_raw === 1 );
		$title = get_theme_mod( 'sme_coming_soon_title', "We're Launching Soon!" );
		$description = get_theme_mod( 'sme_coming_soon_description', "We're working hard to bring you the latest insights, strategies, and tools to help your small business thrive. Stay tuned for something amazing!" );
		$email_placeholder = get_theme_mod( 'sme_coming_soon_email_placeholder', 'Enter your email to get notified' );
		$notification_email = get_theme_mod( 'sme_coming_soon_notification_email', get_option( 'admin_email' ) );
		?>
		<div class="wrap">
			<h1>Coming Soon Settings</h1>
			<p class="description">Enable coming soon mode to show visitors a beautiful page while your site is under construction. Admins can still access the site normally.</p>
			
			<form method="post" action="">
				<?php wp_nonce_field( 'sme_coming_soon_settings', 'sme_coming_soon_nonce' ); ?>
				
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="sme_enable_coming_soon">Enable Coming Soon Mode</label>
							</th>
							<td>
								<input type="checkbox" id="sme_enable_coming_soon" name="sme_enable_coming_soon" value="1" <?php checked( $enable_coming_soon, true ); ?>>
								<label for="sme_enable_coming_soon">Show coming soon page to visitors (admins can still access the site)</label>
								<?php 
								$actual_value = get_theme_mod( 'sme_enable_coming_soon', false );
								$is_actually_enabled = ( $actual_value === true || $actual_value === '1' || $actual_value === 1 );
								?>
								<?php if ( $is_actually_enabled ) : ?>
									<p class="description" style="color: #d63638; margin-top: 10px;">
										<strong>⚠️ Coming Soon is currently ACTIVE</strong> - Visitors will see the coming soon page.
									</p>
								<?php else : ?>
									<p class="description" style="color: #00a32a; margin-top: 10px;">
										<strong>✓ Coming Soon is currently DISABLED</strong> - Site is accessible to everyone.
									</p>
								<?php endif; ?>
								<p class="description" style="margin-top: 5px; font-size: 11px; color: #666;">
									Debug: Stored value = <?php echo esc_html( var_export( $actual_value, true ) ); ?> | Checkbox checked = <?php echo $enable_coming_soon ? 'true' : 'false'; ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="sme_coming_soon_title">Coming Soon Title</label>
							</th>
							<td>
								<input type="text" id="sme_coming_soon_title" name="sme_coming_soon_title" value="<?php echo esc_attr( $title ); ?>" class="regular-text">
								<p class="description">Main heading displayed on the coming soon page</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="sme_coming_soon_description">Description</label>
							</th>
							<td>
								<textarea id="sme_coming_soon_description" name="sme_coming_soon_description" rows="4" class="large-text"><?php echo esc_textarea( $description ); ?></textarea>
								<p class="description">Description text displayed below the title</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="sme_coming_soon_email_placeholder">Email Input Placeholder</label>
							</th>
							<td>
								<input type="text" id="sme_coming_soon_email_placeholder" name="sme_coming_soon_email_placeholder" value="<?php echo esc_attr( $email_placeholder ); ?>" class="regular-text">
								<p class="description">Placeholder text for the email input field</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="sme_coming_soon_notification_email">Notification Email Address</label>
							</th>
							<td>
								<input type="email" id="sme_coming_soon_notification_email" name="sme_coming_soon_notification_email" value="<?php echo esc_attr( $notification_email ); ?>" class="regular-text">
								<p class="description"><strong>Important:</strong> This is the email address where you'll receive notifications when someone subscribes. Default: <?php echo esc_html( get_option( 'admin_email' ) ); ?></p>
								<?php if ( empty( $notification_email ) ) : ?>
									<p class="description" style="color: #d63638;">
										<strong>⚠️ Warning:</strong> No notification email set. You won't receive notifications when users subscribe.
									</p>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
				
				<h2 style="margin-top: 40px; padding-bottom: 10px; border-bottom: 2px solid #ddd;">⏰ Countdown Timer Settings</h2>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="sme_coming_soon_countdown_enable">Enable Countdown Timer</label>
							</th>
							<td>
								<input type="checkbox" id="sme_coming_soon_countdown_enable" name="sme_coming_soon_countdown_enable" value="1" <?php checked( get_theme_mod( 'sme_coming_soon_countdown_enable', false ), true ); ?>>
								<label for="sme_coming_soon_countdown_enable">Show countdown timer on coming soon page</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="sme_coming_soon_countdown_date">Launch Date</label>
							</th>
							<td>
								<input type="date" id="sme_coming_soon_countdown_date" name="sme_coming_soon_countdown_date" value="<?php echo esc_attr( get_theme_mod( 'sme_coming_soon_countdown_date', '' ) ); ?>" class="regular-text">
								<p class="description">Select the date when your site will launch</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="sme_coming_soon_countdown_time">Launch Time</label>
							</th>
							<td>
								<input type="time" id="sme_coming_soon_countdown_time" name="sme_coming_soon_countdown_time" value="<?php echo esc_attr( get_theme_mod( 'sme_coming_soon_countdown_time', '12:00' ) ); ?>" class="regular-text">
								<p class="description">Select the time when your site will launch (24-hour format)</p>
							</td>
						</tr>
					</tbody>
				</table>
				
				<h2 style="margin-top: 40px; padding-bottom: 10px; border-bottom: 2px solid #ddd;">📊 Progress Bar Settings</h2>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="sme_coming_soon_progress_enable">Enable Progress Bar</label>
							</th>
							<td>
								<input type="checkbox" id="sme_coming_soon_progress_enable" name="sme_coming_soon_progress_enable" value="1" <?php checked( get_theme_mod( 'sme_coming_soon_progress_enable', false ), true ); ?>>
								<label for="sme_coming_soon_progress_enable">Show progress bar on coming soon page</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="sme_coming_soon_progress_percentage">Progress Percentage</label>
							</th>
							<td>
								<input type="number" id="sme_coming_soon_progress_percentage" name="sme_coming_soon_progress_percentage" value="<?php echo esc_attr( get_theme_mod( 'sme_coming_soon_progress_percentage', 75 ) ); ?>" min="0" max="100" class="small-text">
								<span>%</span>
								<p class="description">Enter a number between 0 and 100 to show your progress</p>
							</td>
						</tr>
					</tbody>
				</table>
				
				<h2 style="margin-top: 40px; padding-bottom: 10px; border-bottom: 2px solid #ddd;">📧 Contact Information</h2>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="sme_coming_soon_show_contact_email">Show Contact Email</label>
							</th>
							<td>
								<input type="checkbox" id="sme_coming_soon_show_contact_email" name="sme_coming_soon_show_contact_email" value="1" <?php checked( get_theme_mod( 'sme_coming_soon_show_contact_email', false ), true ); ?>>
								<label for="sme_coming_soon_show_contact_email">Display contact email on coming soon page</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="sme_coming_soon_contact_email">Contact Email Address</label>
							</th>
							<td>
								<input type="email" id="sme_coming_soon_contact_email" name="sme_coming_soon_contact_email" value="<?php echo esc_attr( get_theme_mod( 'sme_coming_soon_contact_email', '' ) ); ?>" class="regular-text">
								<p class="description">Email address for visitors to contact you (e.g., info@example.com)</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="sme_coming_soon_show_social_media">Show Social Media Links</label>
							</th>
							<td>
								<input type="checkbox" id="sme_coming_soon_show_social_media" name="sme_coming_soon_show_social_media" value="1" <?php checked( get_theme_mod( 'sme_coming_soon_show_social_media', true ), true ); ?>>
								<label for="sme_coming_soon_show_social_media">Display social media links on coming soon page (uses links from Theme Customizer > Social Media)</label>
							</td>
						</tr>
					</tbody>
				</table>
				
				<p class="submit">
					<input type="submit" name="sme_coming_soon_submit" class="button button-primary" value="Save Settings">
				</p>
			</form>
			
			<div style="background: #f0f6fc; padding: 20px; border-left: 4px solid #2271b1; margin-top: 30px; border-radius: 4px;">
				<h3>📋 Preview</h3>
				<p>You can preview the coming soon page by visiting your site while logged out (or in an incognito window).</p>
				<p><strong>Note:</strong> As an admin, you can still access the site normally even when Coming Soon is enabled.</p>
			</div>
			
			<div style="background: #fff3cd; padding: 20px; border-left: 4px solid #ffb900; margin-top: 20px; border-radius: 4px;">
				<h3>💡 Tips & Best Practices</h3>
				<ul style="margin-left: 20px;">
					<li><strong>Coming Soon Mode:</strong> Only affects visitors - admins can always access the site</li>
					<li><strong>Email Notifications:</strong> You'll receive an email at the notification address whenever someone subscribes</li>
					<li><strong>Subscriber Confirmation:</strong> Subscribers automatically receive a confirmation email</li>
					<li><strong>Export Subscribers:</strong> You can export all subscribers as a CSV file for use in email marketing tools</li>
					<li><strong>Rate Limiting:</strong> The system limits subscription attempts to prevent spam (5 per hour per IP)</li>
					<li><strong>Email Validation:</strong> All emails are validated before being saved</li>
					<li><strong>Duplicate Prevention:</strong> The system prevents duplicate email subscriptions</li>
				</ul>
			</div>
			
			<div style="background: #e7f3ff; padding: 20px; border-left: 4px solid #2271b1; margin-top: 20px; border-radius: 4px;">
				<h3>📋 Recommendations</h3>
				<ul style="margin-left: 20px;">
					<li><strong>Email Service:</strong> Consider using an email service provider (like SendGrid, Mailgun, or SMTP plugin) for better deliverability</li>
					<li><strong>Email Marketing:</strong> Export subscribers regularly and import them into your email marketing platform (Mailchimp, ConvertKit, etc.)</li>
					<li><strong>Testing:</strong> Test the subscription form with a real email address to ensure notifications are working</li>
					<li><strong>Privacy:</strong> Make sure your Privacy Policy mentions how subscriber emails will be used</li>
					<li><strong>GDPR Compliance:</strong> Consider adding a checkbox for consent if you're targeting EU users</li>
				</ul>
			</div>
			
			<?php
			// Display subscribers list
			$subscribers = sme_get_coming_soon_subscribers();
			$subscribers_count = count( $subscribers );
			?>
			<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-top: 30px; border-radius: 4px;">
				<h2>📧 Subscribers List (<?php echo esc_html( $subscribers_count ); ?>)</h2>
				<?php if ( $subscribers_count > 0 ) : ?>
					<p class="description">These are the email addresses that have subscribed to your Coming Soon page.</p>
					<div style="margin-top: 15px;">
						<?php if ( isset( $_GET['export'] ) && $_GET['export'] === 'subscribers' ) : ?>
							<?php
							// Export subscribers as CSV
							header( 'Content-Type: text/csv' );
							header( 'Content-Disposition: attachment; filename="coming-soon-subscribers-' . date( 'Y-m-d' ) . '.csv"' );
							echo "Email,Subscription Date\n";
							foreach ( $subscribers as $email ) {
								echo esc_html( $email ) . ",\n";
							}
							exit;
							?>
						<?php else : ?>
							<div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; background: #f9f9f9; border-radius: 4px;">
								<table class="wp-list-table widefat fixed striped" style="margin: 0;">
									<thead>
										<tr>
											<th style="width: 50px;">#</th>
											<th>Email Address</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $subscribers as $index => $email ) : ?>
											<tr>
												<td><?php echo esc_html( $index + 1 ); ?></td>
												<td><?php echo esc_html( $email ); ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
							<div style="margin-top: 15px;">
								<a href="<?php echo esc_url( add_query_arg( 'export', 'subscribers', admin_url( 'admin.php?page=sme-coming-soon-settings' ) ) ); ?>" class="button">
									📥 Export as CSV
								</a>
								<?php if ( $subscribers_count > 0 ) : ?>
									<form method="post" action="" style="display: inline-block; margin-left: 10px;" onsubmit="return confirm('Are you sure you want to delete all subscribers? This action cannot be undone.');">
										<?php wp_nonce_field( 'sme_delete_subscribers', 'sme_delete_subscribers_nonce' ); ?>
										<input type="hidden" name="sme_delete_all_subscribers" value="1">
										<input type="submit" class="button button-secondary" value="🗑️ Delete All Subscribers">
									</form>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<p class="description">No subscribers yet. Subscribers will appear here once someone signs up on your Coming Soon page.</p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render Theme Settings page - All theme customization in one place
	 */
	public function render_theme_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Handle form submission
		if ( isset( $_POST['sme_theme_settings_submit'] ) && check_admin_referer( 'sme_theme_settings', 'sme_theme_settings_nonce' ) ) {
			// Social Media Settings - Always save (even if empty to allow clearing)
			set_theme_mod( 'social_facebook', isset( $_POST['social_facebook'] ) ? esc_url_raw( $_POST['social_facebook'] ) : '' );
			set_theme_mod( 'social_twitter', isset( $_POST['social_twitter'] ) ? esc_url_raw( $_POST['social_twitter'] ) : '' );
			set_theme_mod( 'social_linkedin', isset( $_POST['social_linkedin'] ) ? esc_url_raw( $_POST['social_linkedin'] ) : '' );
			set_theme_mod( 'social_youtube', isset( $_POST['social_youtube'] ) ? esc_url_raw( $_POST['social_youtube'] ) : '' );
			set_theme_mod( 'social_instagram', isset( $_POST['social_instagram'] ) ? esc_url_raw( $_POST['social_instagram'] ) : '' );
			
			// Header Settings
			if ( isset( $_POST['header_logo_text'] ) ) {
				set_theme_mod( 'header_logo_text', sanitize_text_field( $_POST['header_logo_text'] ) );
			}
			if ( isset( $_POST['header_top_bar_text'] ) ) {
				set_theme_mod( 'header_top_bar_text', sanitize_text_field( $_POST['header_top_bar_text'] ) );
			}
			if ( isset( $_POST['header_search_placeholder'] ) ) {
				set_theme_mod( 'header_search_placeholder', sanitize_text_field( $_POST['header_search_placeholder'] ) );
			}
			if ( isset( $_POST['header_subscribe_text'] ) ) {
				set_theme_mod( 'header_subscribe_text', sanitize_text_field( $_POST['header_subscribe_text'] ) );
			}
			
			// Footer Settings
			if ( isset( $_POST['footer_company_name'] ) ) {
				set_theme_mod( 'footer_company_name', sanitize_text_field( $_POST['footer_company_name'] ) );
			}
			if ( isset( $_POST['footer_copyright_text'] ) ) {
				set_theme_mod( 'footer_copyright_text', wp_kses_post( $_POST['footer_copyright_text'] ) );
			}
			
			// Contact Form Email
			if ( isset( $_POST['sme_contact_form_email'] ) ) {
				$contact_email = sanitize_email( $_POST['sme_contact_form_email'] );
				if ( empty( $contact_email ) ) {
					remove_theme_mod( 'sme_contact_form_email' ); // Remove if empty to use default
				} else {
					set_theme_mod( 'sme_contact_form_email', $contact_email );
				}
			}
			
			// Newsletter Notification Email
			if ( isset( $_POST['sme_newsletter_notification_email'] ) ) {
				$newsletter_email = sanitize_email( $_POST['sme_newsletter_notification_email'] );
				if ( empty( $newsletter_email ) ) {
					remove_theme_mod( 'sme_newsletter_notification_email' ); // Remove if empty to use default
				} else {
					set_theme_mod( 'sme_newsletter_notification_email', $newsletter_email );
				}
			}
			
			// Clear all caches to ensure changes appear immediately
			if ( function_exists( 'wp_cache_flush' ) ) {
				wp_cache_flush();
			}
			
			// Clear theme mods cache
			wp_cache_delete( 'theme_mods_' . get_stylesheet(), 'theme_mods' );
			wp_cache_delete( 'theme_mods_' . get_stylesheet(), 'options' );
			
			// Clear object cache
			wp_cache_delete( 'theme_mods_' . get_stylesheet() );
			
			echo '<div class="notice notice-success is-dismissible"><p>✅ Settings saved successfully! Changes will appear immediately on your site.</p></div>';
		}
		
				// Get current values - Force refresh from database
				wp_cache_delete( 'theme_mods_' . get_stylesheet(), 'theme_mods' );
				$social_facebook = get_theme_mod( 'social_facebook', 'https://facebook.com/smeinsights' );
				$social_twitter = get_theme_mod( 'social_twitter', 'https://twitter.com/smeinsights' );
				$social_linkedin = get_theme_mod( 'social_linkedin', 'https://linkedin.com/company/smeinsights' );
				$social_youtube = get_theme_mod( 'social_youtube', 'https://youtube.com/@smeinsights' );
				$social_instagram = get_theme_mod( 'social_instagram', 'https://instagram.com/smeinsights' );
				$header_logo_text = get_theme_mod( 'header_logo_text', 'SME INSIGHTS' );
				$header_top_bar_text = get_theme_mod( 'header_top_bar_text', 'Become a Contributor' );
				$header_search_placeholder = get_theme_mod( 'header_search_placeholder', 'Search articles...' );
				$header_subscribe_text = get_theme_mod( 'header_subscribe_text', 'SUBSCRIBE' );
				$footer_company_name = get_theme_mod( 'footer_company_name', 'SME INSIGHTS' );
				$footer_copyright_text = get_theme_mod( 'footer_copyright_text', 'Copyright © {year} {site_name}. | Privacy Policy | Terms of Service | Your trusted source for Small Business News & Insights.' );
				$contact_form_email = get_theme_mod( 'sme_contact_form_email', '' );
				if ( empty( $contact_form_email ) ) {
					$contact_form_email = get_option( 'admin_email' );
				}
		?>
		<div class="wrap">
			<h1>⚙️ Theme Settings</h1>
			<p class="description">Customize your theme settings. All changes are saved immediately and appear on your site.</p>
			
			<form method="post" action="" style="max-width: 1400px; margin-top: 30px;">
				<?php wp_nonce_field( 'sme_theme_settings', 'sme_theme_settings_nonce' ); ?>
				
				<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; margin-top: 25px;">
					
					<!-- Social Media Settings -->
					<div style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
						<h2 style="margin-top: 0; color: #1d2327; padding-bottom: 15px; border-bottom: 2px solid #f0f0f1;">📱 Social Media Links</h2>
						<p class="description" style="margin-bottom: 20px;">Add your social media links. These will appear in the header, footer, and contact page.</p>
						<table class="form-table">
							<tbody>
								<tr>
									<th scope="row"><label for="social_facebook">Facebook URL</label></th>
									<td><input type="url" id="social_facebook" name="social_facebook" value="<?php echo esc_attr( $social_facebook ); ?>" class="regular-text"></td>
								</tr>
								<tr>
									<th scope="row"><label for="social_twitter">Twitter URL</label></th>
									<td><input type="url" id="social_twitter" name="social_twitter" value="<?php echo esc_attr( $social_twitter ); ?>" class="regular-text"></td>
								</tr>
								<tr>
									<th scope="row"><label for="social_linkedin">LinkedIn URL</label></th>
									<td><input type="url" id="social_linkedin" name="social_linkedin" value="<?php echo esc_attr( $social_linkedin ); ?>" class="regular-text"></td>
								</tr>
								<tr>
									<th scope="row"><label for="social_youtube">YouTube URL</label></th>
									<td><input type="url" id="social_youtube" name="social_youtube" value="<?php echo esc_attr( $social_youtube ); ?>" class="regular-text"></td>
								</tr>
								<tr>
									<th scope="row"><label for="social_instagram">Instagram URL</label></th>
									<td><input type="url" id="social_instagram" name="social_instagram" value="<?php echo esc_attr( $social_instagram ); ?>" class="regular-text"></td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- Header Settings -->
					<div style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
						<h2 style="margin-top: 0; color: #1d2327; padding-bottom: 15px; border-bottom: 2px solid #f0f0f1;">🎨 Header Settings</h2>
						<p class="description" style="margin-bottom: 20px;">Customize your header text and labels.</p>
						<table class="form-table">
							<tbody>
								<tr>
									<th scope="row"><label for="header_logo_text">Logo Text</label></th>
									<td>
										<input type="text" id="header_logo_text" name="header_logo_text" value="<?php echo esc_attr( $header_logo_text ); ?>" class="regular-text">
										<p class="description">Main logo text displayed in the header</p>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="header_top_bar_text">Top Bar Link Text</label></th>
									<td>
										<input type="text" id="header_top_bar_text" name="header_top_bar_text" value="<?php echo esc_attr( $header_top_bar_text ); ?>" class="regular-text">
										<p class="description">Text for the "Become a Contributor" link in top bar</p>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="header_search_placeholder">Search Placeholder</label></th>
									<td>
										<input type="text" id="header_search_placeholder" name="header_search_placeholder" value="<?php echo esc_attr( $header_search_placeholder ); ?>" class="regular-text">
										<p class="description">Placeholder text in the search input field</p>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="header_subscribe_text">Subscribe Button Text</label></th>
									<td>
										<input type="text" id="header_subscribe_text" name="header_subscribe_text" value="<?php echo esc_attr( $header_subscribe_text ); ?>" class="regular-text">
										<p class="description">Text for the subscribe button in header</p>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- Footer Settings -->
					<div style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
						<h2 style="margin-top: 0; color: #1d2327; padding-bottom: 15px; border-bottom: 2px solid #f0f0f1;">🦶 Footer Settings</h2>
						<p class="description" style="margin-bottom: 20px;">Customize your footer content.</p>
						<table class="form-table">
							<tbody>
								<tr>
									<th scope="row"><label for="footer_company_name">Company Name</label></th>
									<td>
										<input type="text" id="footer_company_name" name="footer_company_name" value="<?php echo esc_attr( $footer_company_name ); ?>" class="regular-text">
										<p class="description">Company name displayed in footer (Column 1 heading)</p>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="footer_copyright_text">Copyright Text</label></th>
									<td>
										<textarea id="footer_copyright_text" name="footer_copyright_text" rows="3" class="large-text"><?php echo esc_textarea( $footer_copyright_text ); ?></textarea>
										<p class="description">Use {year} for current year and {site_name} for site name</p>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- Contact Form Settings -->
					<div style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
						<h2 style="margin-top: 0; color: #1d2327; padding-bottom: 15px; border-bottom: 2px solid #f0f0f1;">📧 Contact Form Settings</h2>
						<p class="description" style="margin-bottom: 20px;">Configure where contact form submissions are sent.</p>
						<table class="form-table">
							<tbody>
								<tr>
									<th scope="row"><label for="sme_contact_form_email">Contact Form Email</label></th>
									<td>
										<input type="email" id="sme_contact_form_email" name="sme_contact_form_email" value="<?php echo esc_attr( $contact_form_email ); ?>" class="regular-text">
										<p class="description">Email address where contact form submissions will be sent. Leave empty to use the default admin email.</p>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="sme_newsletter_notification_email">Newsletter Notification Email</label></th>
									<td>
										<?php
										$newsletter_email = get_theme_mod( 'sme_newsletter_notification_email', '' );
										if ( empty( $newsletter_email ) ) {
											$newsletter_email = get_option( 'admin_email' );
										}
										?>
										<input type="email" id="sme_newsletter_notification_email" name="sme_newsletter_notification_email" value="<?php echo esc_attr( $newsletter_email ); ?>" class="regular-text">
										<p class="description">Email address where newsletter subscription notifications will be sent. Leave empty to use the default admin email.</p>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
				</div>
				
				<p class="submit" style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #f0f0f1;">
					<input type="submit" name="sme_theme_settings_submit" class="button button-primary button-large" value="💾 Save All Settings">
					<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-secondary" style="margin-left: 10px;">🎨 Open Full Customizer</a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-insights-dashboard' ) ); ?>" class="button button-secondary" style="margin-left: 10px;">🏠 Back to Dashboard</a>
				</p>
			</form>
			
			<!-- Info Box -->
			<div style="background: #f0f6fc; padding: 20px; border-left: 4px solid #2271b1; margin-top: 30px; border-radius: 4px;">
				<h3 style="margin-top: 0;">💡 Tips</h3>
				<ul style="margin-left: 20px;">
					<li><strong>Social Media Links:</strong> These links appear in the header, footer, contact page, and about page</li>
					<li><strong>Header Settings:</strong> Changes to logo text and button labels appear immediately</li>
					<li><strong>Footer Settings:</strong> Use {year} and {site_name} placeholders in copyright text</li>
					<li><strong>Contact Form Email:</strong> If left empty, uses the default WordPress admin email</li>
					<li><strong>More Options:</strong> For colors, fonts, and advanced settings, use the "Open Full Customizer" button</li>
				</ul>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render Newsletter Subscribers page
	 */
	public function render_newsletter_subscribers() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Handle delete action
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['email'] ) && check_admin_referer( 'delete_subscriber' ) ) {
			$email_to_delete = sanitize_email( $_GET['email'] );
			$subscribers = get_option( 'sme_newsletter_subscribers', array() );
			$subscribers = array_filter( $subscribers, function( $email ) use ( $email_to_delete ) {
				return $email !== $email_to_delete;
			} );
			update_option( 'sme_newsletter_subscribers', array_values( $subscribers ) );
			echo '<div class="notice notice-success is-dismissible"><p>Subscriber deleted successfully.</p></div>';
		}
		
		// Handle bulk delete
		if ( isset( $_POST['bulk_delete'] ) && check_admin_referer( 'bulk_delete_subscribers' ) ) {
			if ( isset( $_POST['subscriber_emails'] ) && is_array( $_POST['subscriber_emails'] ) ) {
				$emails_to_delete = array_map( 'sanitize_email', $_POST['subscriber_emails'] );
				$subscribers = get_option( 'sme_newsletter_subscribers', array() );
				$subscribers = array_filter( $subscribers, function( $email ) use ( $emails_to_delete ) {
					return ! in_array( $email, $emails_to_delete, true );
				} );
				update_option( 'sme_newsletter_subscribers', array_values( $subscribers ) );
				echo '<div class="notice notice-success is-dismissible"><p>' . count( $emails_to_delete ) . ' subscriber(s) deleted successfully.</p></div>';
			}
		}
		
		$subscribers = get_option( 'sme_newsletter_subscribers', array() );
		$total_subscribers = count( $subscribers );
		
		?>
		<div class="wrap">
			<h1>📧 Newsletter Subscribers</h1>
			<p class="description">Manage your newsletter subscribers. View, export, or delete subscriber emails.</p>
			
			<div style="max-width: 1200px; margin-top: 30px;">
				<!-- Statistics -->
				<div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 30px;">
					<h2 style="margin-top: 0;">Statistics</h2>
					<p style="font-size: 18px;"><strong>Total Subscribers:</strong> <?php echo esc_html( $total_subscribers ); ?></p>
					<?php if ( $total_subscribers > 0 ) : ?>
						<p style="margin-top: 10px;">
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=sme-newsletter-subscribers&action=export' ), 'export_subscribers' ) ); ?>" class="button button-secondary">
								📥 Export CSV
							</a>
						</p>
					<?php endif; ?>
				</div>
				
				<!-- Subscribers List -->
				<div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
					<h2 style="margin-top: 0;">Subscribers List</h2>
					
					<?php if ( $total_subscribers > 0 ) : ?>
						<form method="post" action="" onsubmit="return confirm('Are you sure you want to delete the selected subscribers?');">
							<?php wp_nonce_field( 'bulk_delete_subscribers' ); ?>
							
							<div style="margin-bottom: 20px;">
								<button type="submit" name="bulk_delete" class="button button-secondary">
									🗑️ Delete Selected
								</button>
							</div>
							
							<table class="wp-list-table widefat fixed striped">
								<thead>
									<tr>
										<th style="width: 40px;">
											<input type="checkbox" id="select-all-subscribers">
										</th>
										<th>Email Address</th>
										<th style="width: 150px;">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $subscribers as $email ) : ?>
										<tr>
											<td>
												<input type="checkbox" name="subscriber_emails[]" value="<?php echo esc_attr( $email ); ?>" class="subscriber-checkbox">
											</td>
											<td>
												<strong><?php echo esc_html( $email ); ?></strong>
											</td>
											<td>
												<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=sme-newsletter-subscribers&action=delete&email=' . urlencode( $email ) ), 'delete_subscriber' ) ); ?>" 
												   class="button button-small" 
												   onclick="return confirm('Are you sure you want to delete this subscriber?');">
													🗑️ Delete
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</form>
						
						<script>
						document.addEventListener('DOMContentLoaded', function() {
							const selectAll = document.getElementById('select-all-subscribers');
							const checkboxes = document.querySelectorAll('.subscriber-checkbox');
							
							if (selectAll) {
								selectAll.addEventListener('change', function() {
									checkboxes.forEach(function(checkbox) {
										checkbox.checked = selectAll.checked;
									});
								});
							}
						});
						</script>
					<?php else : ?>
						<p style="padding: 20px; text-align: center; color: #666;">No subscribers yet. Subscribers will appear here once they subscribe to your newsletter.</p>
					<?php endif; ?>
				</div>
				
				<!-- Info Box -->
				<div style="background: #f0f6fc; padding: 20px; border-left: 4px solid #2271b1; margin-top: 30px; border-radius: 4px;">
					<h3 style="margin-top: 0;">ℹ️ Information</h3>
					<ul style="line-height: 1.8;">
						<li><strong>Subscriber Emails:</strong> All subscriber emails are saved in the database and can be exported at any time.</li>
						<li><strong>Export:</strong> Click "Export CSV" to download all subscriber emails in CSV format.</li>
						<li><strong>Delete:</strong> You can delete individual subscribers or use bulk delete to remove multiple subscribers.</li>
						<li><strong>Email Sending:</strong> If emails are not being sent, check your SMTP settings. In XAMPP (local environment), you may need to configure SMTP or use a plugin like "WP Mail SMTP".</li>
						<li><strong>Note:</strong> Even if email sending fails, subscriptions are still saved and can be viewed here.</li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render Contact Form Submissions page
	 */
	public function render_contact_submissions() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Handle delete action
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['index'] ) && check_admin_referer( 'delete_submission' ) ) {
			$index_to_delete = absint( $_GET['index'] );
			$submissions = get_option( 'sme_contact_form_submissions', array() );
			if ( isset( $submissions[ $index_to_delete ] ) ) {
				unset( $submissions[ $index_to_delete ] );
				$submissions = array_values( $submissions );
				update_option( 'sme_contact_form_submissions', $submissions );
				echo '<div class="notice notice-success is-dismissible"><p>Submission deleted successfully.</p></div>';
			}
		}
		
		// Handle bulk delete
		if ( isset( $_POST['bulk_delete'] ) && check_admin_referer( 'bulk_delete_contact_submissions' ) ) {
			if ( isset( $_POST['submission_indices'] ) && is_array( $_POST['submission_indices'] ) ) {
				$indices_to_delete = array_map( 'absint', $_POST['submission_indices'] );
				$submissions = get_option( 'sme_contact_form_submissions', array() );
				foreach ( $indices_to_delete as $index ) {
					if ( isset( $submissions[ $index ] ) ) {
						unset( $submissions[ $index ] );
					}
				}
				$submissions = array_values( $submissions );
				update_option( 'sme_contact_form_submissions', $submissions );
				echo '<div class="notice notice-success is-dismissible"><p>' . count( $indices_to_delete ) . ' submission(s) deleted successfully.</p></div>';
			}
		}
		
		$submissions = get_option( 'sme_contact_form_submissions', array() );
		$total_submissions = count( $submissions );
		// Reverse to show newest first
		$submissions = array_reverse( $submissions, true );
		
		?>
		<div class="wrap">
			<h1>📬 Contact Form Submissions</h1>
			<p class="description">View and manage all contact form submissions from your website.</p>
			
			<div style="max-width: 1400px; margin-top: 30px;">
				<!-- Statistics -->
				<div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 30px;">
					<h2 style="margin-top: 0;">Statistics</h2>
					<p style="font-size: 18px;"><strong>Total Submissions:</strong> <?php echo esc_html( $total_submissions ); ?></p>
					<?php if ( $total_submissions > 0 ) : ?>
						<p style="margin-top: 10px;">
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=sme-contact-submissions&action=export' ), 'export_contact_submissions' ) ); ?>" class="button button-secondary">
								📥 Export CSV
							</a>
						</p>
					<?php endif; ?>
				</div>
				
				<!-- Submissions List -->
				<div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
					<h2 style="margin-top: 0;">Submissions List</h2>
					
					<?php if ( $total_submissions > 0 ) : ?>
						<form method="post" action="" onsubmit="return confirm('Are you sure you want to delete the selected submissions?');">
							<?php wp_nonce_field( 'bulk_delete_contact_submissions' ); ?>
							
							<div style="margin-bottom: 20px;">
								<button type="submit" name="bulk_delete" class="button button-secondary">
									🗑️ Delete Selected
								</button>
							</div>
							
							<table class="wp-list-table widefat fixed striped">
								<thead>
									<tr>
										<th style="width: 40px;">
											<input type="checkbox" id="select-all-submissions">
										</th>
										<th style="width: 150px;">Date</th>
										<th style="width: 150px;">Name</th>
										<th style="width: 200px;">Email</th>
										<th style="width: 150px;">Subject</th>
										<th>Message Preview</th>
										<th style="width: 150px;">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $submissions as $original_index => $submission ) : 
										// Get the correct index (since we reversed the array)
										$submission_index = $total_submissions - 1 - $original_index;
										$message_preview = isset( $submission['message'] ) ? wp_trim_words( $submission['message'], 20 ) : '';
									?>
										<tr>
											<td>
												<input type="checkbox" name="submission_indices[]" value="<?php echo esc_attr( $submission_index ); ?>" class="submission-checkbox">
											</td>
											<td>
												<strong><?php echo esc_html( isset( $submission['date'] ) ? date_i18n( 'M j, Y g:i A', strtotime( $submission['date'] ) ) : 'N/A' ); ?></strong>
											</td>
											<td>
												<strong><?php echo esc_html( isset( $submission['name'] ) ? $submission['name'] : '' ); ?></strong>
											</td>
											<td>
												<a href="mailto:<?php echo esc_attr( isset( $submission['email'] ) ? $submission['email'] : '' ); ?>">
													<?php echo esc_html( isset( $submission['email'] ) ? $submission['email'] : '' ); ?>
												</a>
											</td>
											<td>
												<?php echo esc_html( isset( $submission['subject'] ) ? $submission['subject'] : '' ); ?>
											</td>
											<td>
												<?php echo esc_html( $message_preview ); ?>
											</td>
											<td>
												<a href="#" onclick="showSubmissionDetails(<?php echo esc_js( $submission_index ); ?>); return false;" class="button button-small">View</a>
												<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=sme-contact-submissions&action=delete&index=' . $submission_index ), 'delete_submission' ) ); ?>" 
												   class="button button-small" 
												   onclick="return confirm('Are you sure you want to delete this submission?');">
													🗑️ Delete
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</form>
						
						<!-- Submission Details Modal -->
						<div id="submission-details-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 100000; overflow-y: auto;">
							<div style="background: #fff; margin: 50px auto; max-width: 800px; padding: 30px; border-radius: 8px; position: relative;">
								<button onclick="closeSubmissionDetails();" style="position: absolute; top: 10px; right: 10px; background: #ddd; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px;">✕ Close</button>
								<div id="submission-details-content"></div>
							</div>
						</div>
						
						<script>
						const submissionsData = <?php echo json_encode( array_values( array_reverse( get_option( 'sme_contact_form_submissions', array() ), true ) ) ); ?>;
						
						function showSubmissionDetails(index) {
							const submission = submissionsData[index];
							if (!submission) return;
							
							let html = '<h2>Submission Details</h2>';
							html += '<p><strong>Date:</strong> ' + (submission.date || 'N/A') + '</p>';
							html += '<p><strong>Name:</strong> ' + (submission.name || '') + '</p>';
							html += '<p><strong>Email:</strong> <a href="mailto:' + (submission.email || '') + '">' + (submission.email || '') + '</a></p>';
							html += '<p><strong>Subject:</strong> ' + (submission.subject || '') + '</p>';
							html += '<p><strong>Message:</strong></p>';
							html += '<div style="background: #f5f5f5; padding: 15px; border-radius: 4px; white-space: pre-wrap;">' + (submission.message || '') + '</div>';
							if (submission.ip) {
								html += '<p><strong>IP Address:</strong> ' + submission.ip + '</p>';
							}
							
							document.getElementById('submission-details-content').innerHTML = html;
							document.getElementById('submission-details-modal').style.display = 'block';
						}
						
						function closeSubmissionDetails() {
							document.getElementById('submission-details-modal').style.display = 'none';
						}
						
						document.addEventListener('DOMContentLoaded', function() {
							const selectAll = document.getElementById('select-all-submissions');
							const checkboxes = document.querySelectorAll('.submission-checkbox');
							
							if (selectAll) {
								selectAll.addEventListener('change', function() {
									checkboxes.forEach(function(checkbox) {
										checkbox.checked = selectAll.checked;
									});
								});
							}
							
							// Close modal on outside click
							document.getElementById('submission-details-modal').addEventListener('click', function(e) {
								if (e.target === this) {
									closeSubmissionDetails();
								}
							});
						});
						</script>
					<?php else : ?>
						<p style="padding: 20px; text-align: center; color: #666;">No submissions yet. Submissions will appear here once users submit the contact form.</p>
					<?php endif; ?>
				</div>
				
				<!-- Info Box -->
				<div style="background: #f0f6fc; padding: 20px; border-left: 4px solid #2271b1; margin-top: 30px; border-radius: 4px;">
					<h3 style="margin-top: 0;">ℹ️ Information</h3>
					<ul style="line-height: 1.8;">
						<li><strong>Submissions:</strong> All contact form submissions are saved in the database (last 100 submissions).</li>
						<li><strong>Export:</strong> Click "Export CSV" to download all submissions in CSV format.</li>
						<li><strong>View:</strong> Click "View" to see full submission details in a popup.</li>
						<li><strong>Delete:</strong> You can delete individual submissions or use bulk delete to remove multiple submissions.</li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render Article Submissions page
	 */
	public function render_article_submissions() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Handle delete action
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['index'] ) && check_admin_referer( 'delete_article_submission' ) ) {
			$index_to_delete = absint( $_GET['index'] );
			$submissions = get_option( 'sme_article_submissions', array() );
			if ( isset( $submissions[ $index_to_delete ] ) ) {
				unset( $submissions[ $index_to_delete ] );
				$submissions = array_values( $submissions );
				update_option( 'sme_article_submissions', $submissions );
				echo '<div class="notice notice-success is-dismissible"><p>Submission deleted successfully.</p></div>';
			}
		}
		
		// Handle bulk delete
		if ( isset( $_POST['bulk_delete'] ) && check_admin_referer( 'bulk_delete_article_submissions' ) ) {
			if ( isset( $_POST['submission_indices'] ) && is_array( $_POST['submission_indices'] ) ) {
				$indices_to_delete = array_map( 'absint', $_POST['submission_indices'] );
				$submissions = get_option( 'sme_article_submissions', array() );
				foreach ( $indices_to_delete as $index ) {
					if ( isset( $submissions[ $index ] ) ) {
						unset( $submissions[ $index ] );
					}
				}
				$submissions = array_values( $submissions );
				update_option( 'sme_article_submissions', $submissions );
				echo '<div class="notice notice-success is-dismissible"><p>' . count( $indices_to_delete ) . ' submission(s) deleted successfully.</p></div>';
			}
		}
		
		$submissions = get_option( 'sme_article_submissions', array() );
		$total_submissions = count( $submissions );
		// Reverse to show newest first
		$submissions = array_reverse( $submissions, true );
		
		?>
		<div class="wrap">
			<h1>📝 Article Submissions</h1>
			<p class="description">View and manage all article submissions from contributors.</p>
			
			<div style="max-width: 1400px; margin-top: 30px;">
				<!-- Statistics -->
				<div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 30px;">
					<h2 style="margin-top: 0;">Statistics</h2>
					<p style="font-size: 18px;"><strong>Total Submissions:</strong> <?php echo esc_html( $total_submissions ); ?></p>
					<?php if ( $total_submissions > 0 ) : ?>
						<p style="margin-top: 10px;">
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=sme-article-submissions&action=export' ), 'export_article_submissions' ) ); ?>" class="button button-secondary">
								📥 Export CSV
							</a>
						</p>
					<?php endif; ?>
				</div>
				
				<!-- Submissions List -->
				<div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
					<h2 style="margin-top: 0;">Submissions List</h2>
					
					<?php if ( $total_submissions > 0 ) : ?>
						<form method="post" action="" onsubmit="return confirm('Are you sure you want to delete the selected submissions?');">
							<?php wp_nonce_field( 'bulk_delete_article_submissions' ); ?>
							
							<div style="margin-bottom: 20px;">
								<button type="submit" name="bulk_delete" class="button button-secondary">
									🗑️ Delete Selected
								</button>
							</div>
							
							<table class="wp-list-table widefat fixed striped">
								<thead>
									<tr>
										<th style="width: 40px;">
											<input type="checkbox" id="select-all-article-submissions">
										</th>
										<th style="width: 150px;">Date</th>
										<th style="width: 150px;">Name</th>
										<th style="width: 200px;">Email</th>
										<th style="width: 250px;">Article Title</th>
										<th>Abstract Preview</th>
										<th style="width: 150px;">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $submissions as $original_index => $submission ) : 
										// Get the correct index (since we reversed the array)
										$submission_index = $total_submissions - 1 - $original_index;
										$abstract_preview = isset( $submission['abstract'] ) ? wp_trim_words( $submission['abstract'], 15 ) : '';
									?>
										<tr>
											<td>
												<input type="checkbox" name="submission_indices[]" value="<?php echo esc_attr( $submission_index ); ?>" class="article-submission-checkbox">
											</td>
											<td>
												<strong><?php echo esc_html( isset( $submission['date'] ) ? date_i18n( 'M j, Y g:i A', strtotime( $submission['date'] ) ) : 'N/A' ); ?></strong>
											</td>
											<td>
												<strong><?php echo esc_html( isset( $submission['name'] ) ? $submission['name'] : '' ); ?></strong>
											</td>
											<td>
												<a href="mailto:<?php echo esc_attr( isset( $submission['email'] ) ? $submission['email'] : '' ); ?>">
													<?php echo esc_html( isset( $submission['email'] ) ? $submission['email'] : '' ); ?>
												</a>
											</td>
											<td>
												<strong><?php echo esc_html( isset( $submission['title'] ) ? $submission['title'] : '' ); ?></strong>
											</td>
											<td>
												<?php echo esc_html( $abstract_preview ); ?>
											</td>
											<td>
												<a href="#" onclick="showArticleSubmissionDetails(<?php echo esc_js( $submission_index ); ?>); return false;" class="button button-small">View</a>
												<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=sme-article-submissions&action=delete&index=' . $submission_index ), 'delete_article_submission' ) ); ?>" 
												   class="button button-small" 
												   onclick="return confirm('Are you sure you want to delete this submission?');">
													🗑️ Delete
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</form>
						
						<!-- Submission Details Modal -->
						<div id="article-submission-details-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 100000; overflow-y: auto;">
							<div style="background: #fff; margin: 50px auto; max-width: 900px; padding: 30px; border-radius: 8px; position: relative;">
								<button onclick="closeArticleSubmissionDetails();" style="position: absolute; top: 10px; right: 10px; background: #ddd; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px;">✕ Close</button>
								<div id="article-submission-details-content"></div>
							</div>
						</div>
						
						<script>
						const articleSubmissionsData = <?php echo json_encode( array_values( array_reverse( get_option( 'sme_article_submissions', array() ), true ) ) ); ?>;
						
						function showArticleSubmissionDetails(index) {
							const submission = articleSubmissionsData[index];
							if (!submission) return;
							
							let html = '<h2>Article Submission Details</h2>';
							html += '<p><strong>Date:</strong> ' + (submission.date || 'N/A') + '</p>';
							html += '<p><strong>Name:</strong> ' + (submission.name || '') + '</p>';
							html += '<p><strong>Email:</strong> <a href="mailto:' + (submission.email || '') + '">' + (submission.email || '') + '</a></p>';
							html += '<p><strong>Article Title:</strong> ' + (submission.title || '') + '</p>';
							if (submission.linkedin) {
								html += '<p><strong>LinkedIn/Portfolio:</strong> <a href="' + submission.linkedin + '" target="_blank">' + submission.linkedin + '</a></p>';
							}
							html += '<p><strong>Author Bio:</strong></p>';
							html += '<div style="background: #f5f5f5; padding: 15px; border-radius: 4px; white-space: pre-wrap; margin-bottom: 15px;">' + (submission.bio || '') + '</div>';
							html += '<p><strong>Article Abstract/Content:</strong></p>';
							html += '<div style="background: #f5f5f5; padding: 15px; border-radius: 4px; white-space: pre-wrap;">' + (submission.abstract || '') + '</div>';
							if (submission.ip) {
								html += '<p style="margin-top: 15px;"><strong>IP Address:</strong> ' + submission.ip + '</p>';
							}
							
							document.getElementById('article-submission-details-content').innerHTML = html;
							document.getElementById('article-submission-details-modal').style.display = 'block';
						}
						
						function closeArticleSubmissionDetails() {
							document.getElementById('article-submission-details-modal').style.display = 'none';
						}
						
						document.addEventListener('DOMContentLoaded', function() {
							const selectAll = document.getElementById('select-all-article-submissions');
							const checkboxes = document.querySelectorAll('.article-submission-checkbox');
							
							if (selectAll) {
								selectAll.addEventListener('change', function() {
									checkboxes.forEach(function(checkbox) {
										checkbox.checked = selectAll.checked;
									});
								});
							}
							
							// Close modal on outside click
							document.getElementById('article-submission-details-modal').addEventListener('click', function(e) {
								if (e.target === this) {
									closeArticleSubmissionDetails();
								}
							});
						});
						</script>
					<?php else : ?>
						<p style="padding: 20px; text-align: center; color: #666;">No submissions yet. Submissions will appear here once contributors submit articles.</p>
					<?php endif; ?>
				</div>
				
				<!-- Info Box -->
				<div style="background: #f0f6fc; padding: 20px; border-left: 4px solid #2271b1; margin-top: 30px; border-radius: 4px;">
					<h3 style="margin-top: 0;">ℹ️ Information</h3>
					<ul style="line-height: 1.8;">
						<li><strong>Submissions:</strong> All article submissions are saved in the database (last 100 submissions).</li>
						<li><strong>Export:</strong> Click "Export CSV" to download all submissions in CSV format.</li>
						<li><strong>View:</strong> Click "View" to see full submission details including article abstract and author bio.</li>
						<li><strong>Delete:</strong> You can delete individual submissions or use bulk delete to remove multiple submissions.</li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render Category Icons page
	 */
	public function render_category_icons() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		if ( isset( $_POST['save_category_icons'] ) && check_admin_referer( 'sme_save_category_icons' ) ) {
			$categories = isset( $_POST['categories'] ) ? $_POST['categories'] : array();
			
			foreach ( $categories as $term_id => $data ) {
				$term_id = absint( $term_id );
				if ( $term_id > 0 ) {
					if ( isset( $data['icon'] ) ) {
						$icon = sanitize_text_field( $data['icon'] );
						update_term_meta( $term_id, 'category_icon', $icon );
					}
					if ( isset( $data['color'] ) ) {
						$color = sanitize_text_field( $data['color'] );
						update_term_meta( $term_id, 'category_color', $color );
					}
				}
			}
			
			echo '<div class="notice notice-success is-dismissible"><p>Category icons and colors saved successfully!</p></div>';
		}
		
		$categories = get_terms( array(
			'taxonomy' => 'main_category',
			'hide_empty' => false,
		) );
		
		$popular_icons = array(
			'💰' => 'Money',
			'📊' => 'Chart',
			'💼' => 'Briefcase',
			'🚀' => 'Rocket',
			'💡' => 'Lightbulb',
			'📈' => 'Growth',
			'🎯' => 'Target',
			'⚡' => 'Lightning',
			'🔧' => 'Tools',
			'📱' => 'Mobile',
			'🌐' => 'Globe',
			'💻' => 'Computer',
			'📢' => 'Megaphone',
			'🎨' => 'Art',
			'🏆' => 'Trophy',
			'🔒' => 'Lock',
			'📝' => 'Document',
			'♟️' => 'Chess',
			'$' => 'Dollar',
			'⚙️' => 'Settings',
		);
		
		?>
		<div class="wrap">
			<h1>📋 Category Icons & Colors</h1>
			<p style="font-size: 14px; color: #666; margin-bottom: 30px;">Manage icons and colors for your Main Categories. Icons will be displayed on category pages.</p>
			
			<form method="post" action="">
				<?php wp_nonce_field( 'sme_save_category_icons' ); ?>
				
				<div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin-bottom: 20px;">
					<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
						<table class="wp-list-table widefat fixed striped">
							<thead>
								<tr>
									<th style="width: 200px;">Category Name</th>
									<th style="width: 150px;">Current Icon</th>
									<th>Icon (Emoji)</th>
									<th>Color</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $categories as $category ) : 
									$current_icon = get_term_meta( $category->term_id, 'category_icon', true );
									$current_color = get_term_meta( $category->term_id, 'category_color', true );
									if ( empty( $current_icon ) ) {
										$current_icon = '📄';
									}
									if ( empty( $current_color ) ) {
										$current_color = '#2563eb';
									}
								?>
									<tr>
										<td>
											<strong><?php echo esc_html( $category->name ); ?></strong>
											<br>
											<small style="color: #666;"><?php echo esc_html( $category->slug ); ?></small>
										</td>
										<td>
											<div style="font-size: 2rem; text-align: center; padding: 10px; background: #f5f5f5; border-radius: 8px;">
												<?php echo esc_html( $current_icon ); ?>
											</div>
										</td>
										<td>
											<input type="text" 
											       name="categories[<?php echo esc_attr( $category->term_id ); ?>][icon]" 
											       value="<?php echo esc_attr( $current_icon ); ?>" 
											       placeholder="📄"
											       style="width: 100%; padding: 8px; font-size: 1.2rem; text-align: center;"
											       maxlength="2" />
											<p style="margin: 5px 0 0; font-size: 12px; color: #666;">
												<strong>Popular Icons:</strong>
												<?php 
												$icon_count = 0;
												foreach ( $popular_icons as $icon => $name ) : 
													if ( $icon_count >= 10 ) break;
													$icon_count++;
												?>
													<span style="cursor: pointer; margin: 0 3px; font-size: 1.2rem;" 
													      onclick="document.querySelector('input[name=\'categories[<?php echo esc_js( $category->term_id ); ?>][icon]\']').value='<?php echo esc_js( $icon ); ?>'; this.parentElement.previousElementSibling.value='<?php echo esc_js( $icon ); ?>';">
														<?php echo esc_html( $icon ); ?>
													</span>
												<?php endforeach; ?>
											</p>
										</td>
										<td>
											<input type="color" 
											       name="categories[<?php echo esc_attr( $category->term_id ); ?>][color]" 
											       value="<?php echo esc_attr( $current_color ); ?>" 
											       style="width: 80px; height: 40px; cursor: pointer;" />
											<span style="margin-left: 10px; font-family: monospace; font-size: 12px;">
												<?php echo esc_html( $current_color ); ?>
											</span>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p style="padding: 20px; text-align: center; color: #666;">No categories found. Please create Main Categories first.</p>
					<?php endif; ?>
				</div>
				
				<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
					<p class="submit">
						<input type="submit" name="save_category_icons" class="button button-primary" value="Save All Changes" />
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-insights-dashboard' ) ); ?>" class="button button-secondary">Back to Dashboard</a>
					</p>
				<?php endif; ?>
			</form>
			
			<div style="background: #f0f6fc; padding: 20px; border-left: 4px solid #2271b1; margin-top: 30px; border-radius: 4px;">
				<h3 style="margin-top: 0;">ℹ️ How to Use</h3>
				<ul style="line-height: 1.8;">
					<li><strong>Icon:</strong> Enter an emoji icon (like 📄, 💰, 📊) or click on popular icons below the field.</li>
					<li><strong>Color:</strong> Click the color picker to choose a color for the category badge and icon.</li>
					<li><strong>Preview:</strong> The current icon is displayed in the "Current Icon" column.</li>
					<li><strong>Save:</strong> Click "Save All Changes" to update all categories at once.</li>
				</ul>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Restore category data from defaults
	 */
	private function restore_category_data( $category_slug ) {
		$default_data = array(
			'ai-in-business' => array(
				'name'        => 'AI in Business',
				'description' => 'Explore how artificial intelligence is transforming small businesses. From automation to customer service, discover the latest AI tools and strategies that can help your business grow.',
				'icon'        => '🤖',
				'color'       => '#2563eb',
				'page_content' => '<!-- wp:heading -->
<h2>AI in Business</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Explore how artificial intelligence is transforming small businesses. From automation to customer service, discover the latest AI tools and strategies that can help your business grow.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Latest Articles</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Stay updated with the latest insights on AI applications in small business operations, marketing, and customer engagement.</p>
<!-- /wp:paragraph -->',
				'page_title' => 'AI in Business',
				'page_template' => 'page-niche-topic.php',
			),
		);
		
		if ( ! isset( $default_data[ $category_slug ] ) ) {
			wp_die( 'Category not found in default data.' );
		}
		
		$term = get_term_by( 'slug', $category_slug, 'main_category' );
		
		$data = $default_data[ $category_slug ];
		
		// Create category if it doesn't exist
		if ( ! $term || is_wp_error( $term ) ) {
			$term_result = wp_insert_term(
				$data['name'],
				'main_category',
				array(
					'description' => $data['description'],
					'slug'        => $category_slug,
				)
			);
			
			if ( is_wp_error( $term_result ) ) {
				wp_die( 'Error creating category: ' . $term_result->get_error_message() );
			}
			
			$term_id = isset( $term_result['term_id'] ) ? $term_result['term_id'] : 0;
			
			if ( $term_id > 0 ) {
				update_term_meta( $term_id, 'category_icon', $data['icon'] );
				update_term_meta( $term_id, 'category_color', $data['color'] );
			}
		} else {
			// Update existing category
			$result = wp_update_term( $term->term_id, 'main_category', array(
				'description' => $data['description']
			) );
			
			if ( is_wp_error( $result ) ) {
				wp_die( 'Error updating category: ' . $result->get_error_message() );
			}
			
			update_term_meta( $term->term_id, 'category_icon', $data['icon'] );
			update_term_meta( $term->term_id, 'category_color', $data['color'] );
		}
		
		// Restore page if exists
		if ( isset( $data['page_title'] ) ) {
			$page = get_page_by_path( $category_slug );
			
			if ( $page ) {
				// Update existing page - add placeholder content to show template sections
				$placeholder_content = '<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
	<!-- wp:paragraph {"style":{"color":{"background":"#e3f2fd"}},"backgroundColor":"blue","textColor":"white"} -->
	<p class="has-white-color has-blue-background-color has-text-color has-background" style="padding:20px;border-radius:8px;margin:20px 0;"><strong>📋 Template Information:</strong> This page uses the "Niche Topic Page" template. The following sections are automatically generated:</p>
	<!-- /wp:paragraph -->
	
	<!-- wp:list -->
	<ul>
		<!-- wp:list-item -->
		<li><strong>Hero Section:</strong> Blue gradient banner with title, tagline, and description</li>
		<!-- /wp:list-item -->
		
		<!-- wp:list-item -->
		<li><strong>Start Here: Essential Guides:</strong> 3 featured articles grid</li>
		<!-- /wp:list-item -->
		
		<!-- wp:list-item -->
		<li><strong>Latest News &amp; Analysis:</strong> Latest articles list</li>
		<!-- /wp:list-item -->
		
		<!-- wp:list-item -->
		<li><strong>Tools &amp; Resources:</strong> 4 resource cards</li>
		<!-- /wp:list-item -->
		
		<!-- wp:list-item -->
		<li><strong>Our Experts:</strong> 3 expert profiles</li>
		<!-- /wp:list-item -->
		
		<!-- wp:list-item -->
		<li><strong>Newsletter Signup:</strong> Subscription form</li>
		<!-- /wp:list-item -->
	</ul>
	<!-- /wp:list -->
	
	<!-- wp:paragraph {"style":{"color":{"background":"#fff3cd"}},"backgroundColor":"yellow","textColor":"black"} -->
	<p class="has-black-color has-yellow-background-color has-text-color has-background" style="padding:15px;border-radius:8px;margin:20px 0;"><strong>💡 Note:</strong> This content is only visible in the editor. On the frontend, the template automatically generates the full design. Visit the page to see the complete layout.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->';
				
				$page_data = array(
					'ID'           => $page->ID,
					'post_title'   => $data['page_title'],
					'post_content' => $placeholder_content,
					'post_status'  => 'publish',
				);
				
				wp_update_post( $page_data );
				
				// Set template AFTER updating post to ensure it's saved
				if ( isset( $data['page_template'] ) ) {
					update_post_meta( $page->ID, '_wp_page_template', $data['page_template'] );
					// Force refresh cache
					clean_post_cache( $page->ID );
				}
			} else {
				// Create new page with placeholder content
				$placeholder_content = '<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
	<!-- wp:paragraph {"style":{"color":{"background":"#e3f2fd"}},"backgroundColor":"blue","textColor":"white"} -->
	<p class="has-white-color has-blue-background-color has-text-color has-background" style="padding:20px;border-radius:8px;margin:20px 0;"><strong>📋 Template Information:</strong> This page uses the "Niche Topic Page" template. The following sections are automatically generated:</p>
	<!-- /wp:paragraph -->
	
	<!-- wp:list -->
	<ul>
		<!-- wp:list-item -->
		<li><strong>Hero Section:</strong> Blue gradient banner with title, tagline, and description</li>
		<!-- /wp:list-item -->
		
		<!-- wp:list-item -->
		<li><strong>Start Here: Essential Guides:</strong> 3 featured articles grid</li>
		<!-- /wp:list-item -->
		
		<!-- wp:list-item -->
		<li><strong>Latest News &amp; Analysis:</strong> Latest articles list</li>
		<!-- /wp:list-item -->
		
		<!-- wp:list-item -->
		<li><strong>Tools &amp; Resources:</strong> 4 resource cards</li>
		<!-- /wp:list-item -->
		
		<!-- wp:list-item -->
		<li><strong>Our Experts:</strong> 3 expert profiles</li>
		<!-- /wp:list-item -->
		
		<!-- wp:list-item -->
		<li><strong>Newsletter Signup:</strong> Subscription form</li>
		<!-- /wp:list-item -->
	</ul>
	<!-- /wp:list -->
	
	<!-- wp:paragraph {"style":{"color":{"background":"#fff3cd"}},"backgroundColor":"yellow","textColor":"black"} -->
	<p class="has-black-color has-yellow-background-color has-text-color has-background" style="padding:15px;border-radius:8px;margin:20px 0;"><strong>💡 Note:</strong> This content is only visible in the editor. On the frontend, the template automatically generates the full design. Visit the page to see the complete layout.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->';
				
				$page_data = array(
					'post_title'    => $data['page_title'],
					'post_name'     => $category_slug,
					'post_content'  => $placeholder_content,
					'post_status'   => 'publish',
					'post_type'     => 'page',
				);
				
				$page_id = wp_insert_post( $page_data );
				
				if ( $page_id && ! is_wp_error( $page_id ) && isset( $data['page_template'] ) ) {
					update_post_meta( $page_id, '_wp_page_template', $data['page_template'] );
					// Force refresh cache
					clean_post_cache( $page_id );
				}
			}
		}
		
		wp_redirect( admin_url( 'admin.php?page=sme-restore-category&restored=1&category=' . urlencode( $category_slug ) ) );
		exit;
	}
	
	/**
	 * Render Restore Category page
	 */
	public function render_restore_category() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Permission denied' );
		}
		
		$restored = isset( $_GET['restored'] ) && $_GET['restored'] === '1';
		$category_slug = isset( $_GET['category'] ) ? sanitize_text_field( wp_unslash( $_GET['category'] ) ) : '';
		
		$default_categories = array(
			'ai-in-business' => array(
				'name'        => 'AI in Business',
				'description' => 'Explore how artificial intelligence is transforming small businesses. From automation to customer service, discover the latest AI tools and strategies that can help your business grow.',
				'icon'        => '🤖',
				'color'       => '#2563eb',
				'page_content' => '', // Empty - design comes from page-niche-topic.php template
				'page_title' => 'AI in Business',
				'page_template' => 'page-niche-topic.php',
			),
		);
		
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Restore Category Data', 'sme-insights' ); ?></h1>
			
			<?php if ( $restored ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><strong><?php echo esc_html__( 'Success!', 'sme-insights' ); ?></strong> <?php echo esc_html__( 'Category data has been restored successfully.', 'sme-insights' ); ?></p>
				</div>
			<?php endif; ?>
			
			<div class="card" style="max-width: 800px;">
				<h2><?php echo esc_html__( 'Available Categories', 'sme-insights' ); ?></h2>
				<p><?php echo esc_html__( 'Select a category to restore its default data (description, icon, and color).', 'sme-insights' ); ?></p>
				
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php echo esc_html__( 'Category', 'sme-insights' ); ?></th>
							<th><?php echo esc_html__( 'Current Status', 'sme-insights' ); ?></th>
							<th><?php echo esc_html__( 'Actions', 'sme-insights' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $default_categories as $slug => $data ) : 
							$term = get_term_by( 'slug', $slug, 'main_category' );
							$exists = $term && ! is_wp_error( $term );
							$current_description = $exists ? $term->description : '';
							$current_icon = $exists ? get_term_meta( $term->term_id, 'category_icon', true ) : '';
							$current_color = $exists ? get_term_meta( $term->term_id, 'category_color', true ) : '';
							
							// Check page status
							$page = get_page_by_path( $slug );
							$page_exists = $page && ! is_wp_error( $page );
							$page_needs_restore = false;
							if ( $page_exists && isset( $data['page_content'] ) ) {
								$current_page_content = $page->post_content;
								$page_needs_restore = empty( trim( $current_page_content ) ) || $current_page_content !== $data['page_content'];
							}
							
							$needs_restore = $exists && ( empty( $current_description ) || empty( $current_icon ) || empty( $current_color ) ) || ( isset( $data['page_content'] ) && ( ! $page_exists || $page_needs_restore ) );
						?>
							<tr>
								<td>
									<strong><?php echo esc_html( $data['name'] ); ?></strong><br>
									<small style="color: #666;"><?php echo esc_html( $slug ); ?></small>
								</td>
								<td>
									<?php if ( $exists ) : ?>
										<?php if ( $needs_restore ) : ?>
											<span style="color: #d63638;">⚠️ <?php echo esc_html__( 'Missing Data', 'sme-insights' ); ?></span>
											<?php if ( isset( $data['page_content'] ) ) : ?>
												<br><small style="color: #666;">
													<?php if ( ! $page_exists ) : ?>
														📄 <?php echo esc_html__( 'Page not found', 'sme-insights' ); ?>
													<?php elseif ( $page_needs_restore ) : ?>
														📄 <?php echo esc_html__( 'Page content missing', 'sme-insights' ); ?>
													<?php endif; ?>
												</small>
											<?php endif; ?>
										<?php else : ?>
											<span style="color: #00a32a;">✓ <?php echo esc_html__( 'Complete', 'sme-insights' ); ?></span>
											<?php if ( $page_exists ) : ?>
												<br><small style="color: #00a32a;">📄 <?php echo esc_html__( 'Page exists', 'sme-insights' ); ?></small>
											<?php endif; ?>
										<?php endif; ?>
									<?php else : ?>
										<span style="color: #d63638;">✗ <?php echo esc_html__( 'Not Found', 'sme-insights' ); ?></span>
									<?php endif; ?>
								</td>
								<td>
									<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=sme-restore-category&action=restore&category=' . urlencode( $slug ) ), 'restore_category_' . $slug ) ); ?>" 
									   class="button button-primary"
									   onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to restore this category? This will create the category if it doesn\'t exist, and restore/update the page.', 'sme-insights' ) ); ?>');">
										<?php echo $exists ? esc_html__( 'Restore', 'sme-insights' ) : esc_html__( 'Create & Restore', 'sme-insights' ); ?>
									</a>
									<?php if ( $exists ) : ?>
										<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=main_category&tag_ID=' . $term->term_id . '&post_type=post' ) ); ?>" 
										   class="button">
											<?php echo esc_html__( 'Edit Category', 'sme-insights' ); ?>
										</a>
									<?php endif; ?>
									<?php 
									$page = get_page_by_path( $slug );
									if ( $page ) : 
									?>
										<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $page->ID . '&action=edit' ) ); ?>" 
										   class="button">
											<?php echo esc_html__( 'Edit Page', 'sme-insights' ); ?>
										</a>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				
				<div style="margin-top: 20px; padding: 15px; background: #f0f0f1; border-left: 4px solid #2271b1;">
					<h3 style="margin-top: 0;"><?php echo esc_html__( 'Default Data for AI in Business', 'sme-insights' ); ?></h3>
					<ul>
						<li><strong><?php echo esc_html__( 'Name:', 'sme-insights' ); ?></strong> <?php echo esc_html( $default_categories['ai-in-business']['name'] ); ?></li>
						<li><strong><?php echo esc_html__( 'Description:', 'sme-insights' ); ?></strong> <?php echo esc_html( $default_categories['ai-in-business']['description'] ); ?></li>
						<li><strong><?php echo esc_html__( 'Icon:', 'sme-insights' ); ?></strong> <?php echo esc_html( $default_categories['ai-in-business']['icon'] ); ?></li>
						<li><strong><?php echo esc_html__( 'Color:', 'sme-insights' ); ?></strong> <span style="display: inline-block; width: 20px; height: 20px; background: <?php echo esc_attr( $default_categories['ai-in-business']['color'] ); ?>; border: 1px solid #ccc; vertical-align: middle;"></span> <?php echo esc_html( $default_categories['ai-in-business']['color'] ); ?></li>
						<li><strong><?php echo esc_html__( 'Page Title:', 'sme-insights' ); ?></strong> <?php echo esc_html( $default_categories['ai-in-business']['page_title'] ); ?></li>
						<li><strong><?php echo esc_html__( 'Page Template:', 'sme-insights' ); ?></strong> <?php echo esc_html( $default_categories['ai-in-business']['page_template'] ); ?></li>
						<li><strong><?php echo esc_html__( 'Page Content:', 'sme-insights' ); ?></strong> <?php echo esc_html__( 'Empty (design comes from template automatically)', 'sme-insights' ); ?></li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Add admin bar items in admin area
	 */
	public function add_admin_bar_items( $wp_admin_bar ) {
		// Only show in admin area, not in frontend
		if ( ! is_admin() ) {
			return;
		}
		
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Get stats
		$posts_count = wp_count_posts( 'post' )->publish;
		$pages_count = wp_count_posts( 'page' )->publish;
		$media_count = wp_count_posts( 'attachment' )->inherit;
		
		// Newsletter subscribers count
		$newsletter_emails = get_option( 'sme_newsletter_subscribers', array() );
		$subscribers_count = is_array( $newsletter_emails ) ? count( $newsletter_emails ) : 0;
		
		// Contact submissions count
		$contact_submissions = get_option( 'sme_contact_submissions', array() );
		$contact_count = is_array( $contact_submissions ) ? count( $contact_submissions ) : 0;
		
		// Article submissions count
		$article_submissions = get_option( 'sme_article_submissions', array() );
		$article_count = is_array( $article_submissions ) ? count( $article_submissions ) : 0;
		
		// Main Dashboard Link
		$wp_admin_bar->add_menu( array(
			'id'    => 'sme-dashboard',
			'title' => '<span class="ab-icon dashicons-admin-settings" style="margin-top: 3px;"></span> <span class="ab-label">SME Insights</span>',
			'href'  => esc_url( admin_url( 'admin.php?page=sme-insights-dashboard' ) ),
			'meta'  => array(
				'title' => esc_html__( 'SME Insights Dashboard', 'sme-insights' ),
			),
		) );
		
		// Quick Stats (separate, outside SME Insights for quick access)
		$wp_admin_bar->add_menu( array(
			'id'    => 'sme-quick-stats',
			'title' => '<span class="ab-icon dashicons-chart-bar" style="margin-top: 3px;"></span> <span class="ab-label">' . esc_html__( 'Quick Stats', 'sme-insights' ) . '</span>',
			'href'  => '#',
			'meta'  => array(
				'title' => esc_html__( 'Quick Statistics', 'sme-insights' ),
			),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-stats',
			'id'     => 'sme-stats-posts',
			'title'  => sprintf( esc_html__( 'Posts: %d', 'sme-insights' ), $posts_count ),
			'href'   => esc_url( admin_url( 'edit.php' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-stats',
			'id'     => 'sme-stats-pages',
			'title'  => sprintf( esc_html__( 'Pages: %d', 'sme-insights' ), $pages_count ),
			'href'   => esc_url( admin_url( 'edit.php?post_type=page' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-stats',
			'id'     => 'sme-stats-media',
			'title'  => sprintf( esc_html__( 'Media: %d', 'sme-insights' ), $media_count ),
			'href'   => esc_url( admin_url( 'upload.php' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-stats',
			'id'     => 'sme-stats-subscribers',
			'title'  => sprintf( esc_html__( 'Newsletter Subscribers: %d', 'sme-insights' ), $subscribers_count ),
			'href'   => esc_url( admin_url( 'admin.php?page=sme-newsletter-subscribers' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-stats',
			'id'     => 'sme-stats-contact',
			'title'  => sprintf( esc_html__( 'Contact Messages: %d', 'sme-insights' ), $contact_count ),
			'href'   => esc_url( admin_url( 'admin.php?page=sme-contact-submissions' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-stats',
			'id'     => 'sme-stats-articles',
			'title'  => sprintf( esc_html__( 'Article Submissions: %d', 'sme-insights' ), $article_count ),
			'href'   => esc_url( admin_url( 'admin.php?page=sme-article-submissions' ) ),
		) );
		
		// Quick Links (submenu)
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-dashboard',
			'id'     => 'sme-quick-links',
			'title'  => esc_html__( '🔗 Quick Links', 'sme-insights' ),
			'href'   => '#',
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-links',
			'id'     => 'sme-link-content',
			'title'  => esc_html__( '📝 Content Manager', 'sme-insights' ),
			'href'   => esc_url( admin_url( 'admin.php?page=sme-content-manager' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-links',
			'id'     => 'sme-link-image',
			'title'  => esc_html__( '🖼️ Image Optimizer', 'sme-insights' ),
			'href'   => esc_url( admin_url( 'admin.php?page=sme-image-optimizer' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-links',
			'id'     => 'sme-link-newsletter',
			'title'  => esc_html__( '📧 Newsletter Subscribers', 'sme-insights' ),
			'href'   => esc_url( admin_url( 'admin.php?page=sme-newsletter-subscribers' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-links',
			'id'     => 'sme-link-contact',
			'title'  => esc_html__( '📨 Contact Submissions', 'sme-insights' ),
			'href'   => esc_url( admin_url( 'admin.php?page=sme-contact-submissions' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-links',
			'id'     => 'sme-link-articles',
			'title'  => esc_html__( '📝 Article Submissions', 'sme-insights' ),
			'href'   => esc_url( admin_url( 'admin.php?page=sme-article-submissions' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-links',
			'id'     => 'sme-link-cache',
			'title'  => esc_html__( '⚡ Cache Settings', 'sme-insights' ),
			'href'   => esc_url( admin_url( 'admin.php?page=sme-cache-settings' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-links',
			'id'     => 'sme-link-sitemap',
			'title'  => esc_html__( '🗺️ Sitemap Settings', 'sme-insights' ),
			'href'   => esc_url( admin_url( 'admin.php?page=sme-sitemap-settings' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-links',
			'id'     => 'sme-link-theme',
			'title'  => esc_html__( '🎨 Theme Settings', 'sme-insights' ),
			'href'   => esc_url( admin_url( 'admin.php?page=sme-theme-settings' ) ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-quick-links',
			'id'     => 'sme-link-customizer',
			'title'  => esc_html__( '🎨 Full Customizer', 'sme-insights' ),
			'href'   => esc_url( admin_url( 'customize.php' ) ),
		) );
		
		// Main Categories (submenu inside SME Insights)
		$wp_admin_bar->add_menu( array(
			'parent' => 'sme-dashboard',
			'id'     => 'sme-categories',
			'title'  => esc_html__( '📂 Main Categories', 'sme-insights' ),
			'href'   => esc_url( admin_url( 'edit-tags.php?taxonomy=main_category' ) ),
		) );
		
		// Show notification badges for new items (submenu inside SME Insights)
		$total_new_items = $contact_count + $article_count;
		if ( $total_new_items > 0 ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'sme-dashboard',
				'id'     => 'sme-notifications',
				'title'  => sprintf( esc_html__( '🔔 New Items (%d)', 'sme-insights' ), $total_new_items ),
				'href'   => '#',
				'meta'   => array(
					'class' => 'sme-notifications-badge',
				),
			) );
			
			if ( $contact_count > 0 ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'sme-notifications',
					'id'     => 'sme-notif-contact',
					'title'  => sprintf( esc_html__( '📨 %d New Contact Message(s)', 'sme-insights' ), $contact_count ),
					'href'   => esc_url( admin_url( 'admin.php?page=sme-contact-submissions' ) ),
				) );
			}
			
			if ( $article_count > 0 ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'sme-notifications',
					'id'     => 'sme-notif-article',
					'title'  => sprintf( esc_html__( '📝 %d New Article Submission(s)', 'sme-insights' ), $article_count ),
					'href'   => esc_url( admin_url( 'admin.php?page=sme-article-submissions' ) ),
				) );
			}
		}
		
		// View Site (separate, outside SME Insights)
		$wp_admin_bar->add_menu( array(
			'id'    => 'sme-view-site',
			'title' => '<span class="ab-icon dashicons-admin-appearance" style="margin-top: 3px;"></span> <span class="ab-label">' . esc_html__( 'View Site', 'sme-insights' ) . '</span>',
			'href'  => esc_url( home_url( '/' ) ),
			'meta'  => array(
				'title' => esc_html__( 'View Site', 'sme-insights' ),
				'target' => '_blank',
			),
		) );
	}
}
