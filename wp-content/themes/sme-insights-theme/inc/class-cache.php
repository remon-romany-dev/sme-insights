<?php
/**
 * Built-in Caching System
 * Basic caching without requiring plugins
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Cache {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Browser caching headers
		add_action( 'wp_head', array( $this, 'add_cache_headers' ), 1 );
		add_action( 'template_redirect', array( $this, 'set_cache_headers' ) );
		
		// Cache busting for assets
		add_filter( 'style_loader_src', array( $this, 'add_version_to_assets' ), 10, 2 );
		add_filter( 'script_loader_src', array( $this, 'add_version_to_assets' ), 10, 2 );
		
		// Admin settings
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		
		// Handle clear cache action
		add_action( 'admin_post_sme_clear_cache', array( $this, 'handle_clear_cache' ) );
	}
	
	/**
	 * Add cache headers in head
	 */
	public function add_cache_headers() {
		if ( is_admin() ) {
			return;
		}
		
		$cache_enabled = get_option( 'sme_cache_enabled', true );
		if ( ! $cache_enabled ) {
			return;
		}
		
		// Preconnect to external resources
		echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
		echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
		
		// CDN support
		$cdn_url = get_option( 'sme_cdn_url', '' );
		if ( ! empty( $cdn_url ) ) {
			echo '<link rel="dns-prefetch" href="' . esc_url( $cdn_url ) . '">' . "\n";
		}
	}
	
	/**
	 * Set cache headers
	 */
	public function set_cache_headers() {
		if ( is_admin() ) {
			return;
		}
		
		$cache_enabled = get_option( 'sme_cache_enabled', true );
		if ( ! $cache_enabled ) {
			return;
		}
		
		$cache_duration = get_option( 'sme_cache_duration', 3600 ); // 1 hour default
		
		// Set cache headers
		header( 'Cache-Control: public, max-age=' . $cache_duration );
		header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $cache_duration ) . ' GMT' );
		
		// ETag
		$etag = md5( get_the_ID() . get_the_modified_date( 'U' ) );
		header( 'ETag: "' . $etag . '"' );
	}
	
	/**
	 * Add version to assets for cache busting
	 */
	public function add_version_to_assets( $src, $handle ) {
		if ( strpos( $src, 'ver=' ) === false ) {
			$src = add_query_arg( 'ver', SME_THEME_VERSION, $src );
		}
		return $src;
	}
	
	/**
	 * Add admin menu
	 * Note: This is now handled by SME_Theme_Dashboard
	 * Keeping this for backward compatibility
	 */
	public function add_admin_menu() {
		// Menu is now added by SME_Theme_Dashboard
		// This method is kept for backward compatibility but does nothing
		// The dashboard will call render_admin_page() directly
	}
	
	/**
	 * Register settings
	 */
	public function register_settings() {
		register_setting( 'sme_cache_settings', 'sme_cache_enabled' );
		register_setting( 'sme_cache_settings', 'sme_cache_duration' );
		register_setting( 'sme_cache_settings', 'sme_cdn_url' );
		register_setting( 'sme_cache_settings', 'sme_cdn_enabled' );
		
		// Set defaults
		if ( get_option( 'sme_cache_enabled' ) === false ) {
			update_option( 'sme_cache_enabled', true );
		}
		if ( get_option( 'sme_cache_duration' ) === false ) {
			update_option( 'sme_cache_duration', 3600 );
		}
		if ( get_option( 'sme_cdn_enabled' ) === false ) {
			update_option( 'sme_cdn_enabled', false );
		}
	}
	
	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Show success/error messages
		if ( isset( $_GET['cache_cleared'] ) && $_GET['cache_cleared'] === 'success' ) {
			echo '<div class="notice notice-success is-dismissible"><p>✅ Cache cleared successfully! All caches have been flushed.</p></div>';
		}
		
		// Save settings
		if ( isset( $_POST['sme_save_cache_settings'] ) && check_admin_referer( 'sme_cache_settings' ) ) {
			update_option( 'sme_cache_enabled', isset( $_POST['sme_cache_enabled'] ) );
			update_option( 'sme_cache_duration', intval( $_POST['sme_cache_duration'] ) );
			update_option( 'sme_cdn_enabled', isset( $_POST['sme_cdn_enabled'] ) );
			update_option( 'sme_cdn_url', esc_url_raw( $_POST['sme_cdn_url'] ) );
			
			// Flush rewrite rules if needed
			flush_rewrite_rules();
			
			echo '<div class="notice notice-success is-dismissible"><p>Settings saved!</p></div>';
		}
		
		$cache_enabled = get_option( 'sme_cache_enabled', true );
		$cache_duration = get_option( 'sme_cache_duration', 3600 );
		$cdn_enabled = get_option( 'sme_cdn_enabled', false );
		$cdn_url = get_option( 'sme_cdn_url', '' );
		
		?>
		<div class="wrap" style="padding: 20px 30px; max-width: 1400px; margin: 0 auto;">
			<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #ddd;">
				<div>
					<h1 style="margin: 0 0 5px 0; font-size: 28px; font-weight: 600;">Cache & CDN Settings</h1>
					<p class="description" style="margin: 0; color: #646970;">Configure browser caching and CDN settings for better performance.</p>
				</div>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=sme-insights-dashboard' ) ); ?>" class="button" style="padding: 8px 16px; text-decoration: none; display: inline-block;">
					← Back to Dashboard
				</a>
			</div>
			
			<div style="max-width: 1200px; margin-top: 30px;">
				<form method="post" action="">
					<?php wp_nonce_field( 'sme_cache_settings' ); ?>
					
					<div style="background: #fff; padding: 30px 40px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
						<h2 style="margin-top: 0;">Browser Caching</h2>
						
						<table class="form-table">
							<tr>
								<th scope="row">Enable Browser Caching</th>
								<td>
									<label>
										<input type="checkbox" name="sme_cache_enabled" value="1" <?php checked( $cache_enabled, true ); ?>>
										Enable browser caching headers
									</label>
									<p class="description">This tells browsers to cache your pages for faster loading.</p>
								</td>
							</tr>
							<tr>
								<th scope="row">Cache Duration</th>
								<td>
									<input type="number" name="sme_cache_duration" value="<?php echo esc_attr( $cache_duration ); ?>" min="300" max="86400" step="300" style="padding: 10px 15px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px; width: 150px; box-sizing: border-box;">
									<p class="description" style="margin-top: 8px; color: #646970;">Cache duration in seconds (default: 3600 = 1 hour). Recommended: 3600-7200.</p>
								</td>
							</tr>
						</table>
					</div>
					
					<div style="background: #fff; padding: 30px 40px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
						<h2 style="margin-top: 0;">CDN Settings (Cloudflare, etc.)</h2>
						
						<table class="form-table">
							<tr>
								<th scope="row">Enable CDN</th>
								<td>
									<label>
										<input type="checkbox" name="sme_cdn_enabled" value="1" <?php checked( $cdn_enabled, true ); ?>>
										Enable CDN support
									</label>
									<p class="description">If you're using Cloudflare or another CDN, enable this.</p>
								</td>
							</tr>
							<tr>
								<th scope="row">CDN URL</th>
								<td>
									<input type="url" name="sme_cdn_url" value="<?php echo esc_attr( $cdn_url ); ?>" placeholder="https://cdn.yoursite.com" style="width: 100%; max-width: 500px; padding: 10px 15px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
									<p class="description" style="margin-top: 8px; color: #646970;">Your CDN URL (e.g., https://cdn.yoursite.com or Cloudflare proxy URL). Leave empty if using Cloudflare proxy.</p>
								</td>
							</tr>
						</table>
					</div>
					
					<p class="submit" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
						<?php submit_button( 'Save Settings', 'primary', 'sme_save_cache_settings', false, array( 'style' => 'padding: 10px 20px; font-size: 14px; font-weight: 600; cursor: pointer;' ) ); ?>
					</p>
				</form>
				
				<!-- Clear Cache Section -->
				<div style="background: #fff; padding: 30px 40px; border: 1px solid #ddd; border-radius: 8px; margin-top: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
					<h2 style="margin-top: 0; color: #d63638;">Clear Cache</h2>
					<p style="color: #646970; line-height: 1.6;">
						Clear all WordPress caches including object cache, transients, and rewrite rules. This is useful after making changes to your site.
					</p>
					<p style="color: #00a32a; font-weight: 600;">
						✅ This will clear:
						<ul style="margin: 10px 0 0 20px; line-height: 1.8;">
							<li>WordPress object cache</li>
							<li>All transients</li>
							<li>Rewrite rules cache</li>
							<li>Post cache</li>
							<li>Term cache</li>
							<li>User cache</li>
						</ul>
					</p>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Are you sure you want to clear all caches?');" style="margin-top: 20px;">
						<?php wp_nonce_field( 'sme_clear_cache', 'sme_clear_cache_nonce' ); ?>
						<input type="hidden" name="action" value="sme_clear_cache">
						<button type="submit" class="button button-secondary" style="background: #d63638; color: #fff; border-color: #d63638; padding: 10px 20px; font-size: 14px;">
							🗑️ Clear All Cache
						</button>
					</form>
				</div>
				
				<!-- Info Box -->
				<div style="background: #f0f6fc; padding: 20px; border-left: 4px solid #2271b1; margin-top: 30px; border-radius: 4px;">
					<h3 style="margin-top: 0;">ℹ️ About Caching & CDN</h3>
					<ul style="line-height: 1.8;">
						<li><strong>Browser Caching:</strong> Tells browsers to store your pages locally, reducing server load and improving speed.</li>
						<li><strong>CDN (Content Delivery Network):</strong> Serves your content from servers closer to users, dramatically improving load times.</li>
						<li><strong>Cloudflare:</strong> If you're using Cloudflare, just enable CDN support. The system will automatically use Cloudflare's proxy.</li>
						<li><strong>Advanced Caching:</strong> For object cache and page cache, consider using WP Rocket or W3 Total Cache.</li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Handle clear cache action
	 */
	public function handle_clear_cache() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}
		
		check_admin_referer( 'sme_clear_cache', 'sme_clear_cache_nonce' );
		
		// Use cache helper if available
		if ( class_exists( 'SME_Cache_Helper' ) ) {
			SME_Cache_Helper::clear_all_cache();
		} else {
			// Fallback to manual clearing
			wp_cache_flush();
			
			global $wpdb;
			$wpdb->query( $wpdb->prepare( 
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
				'_transient_%',
				'_site_transient_%'
			) );
			
			flush_rewrite_rules( false );
			
			// Clear theme mods cache specifically
			$stylesheet = get_option( 'stylesheet' );
			$theme_mods_option = 'theme_mods_' . $stylesheet;
			wp_cache_delete( $theme_mods_option, 'options' );
			wp_cache_delete( 'alloptions', 'options' );
		}
		
		// Redirect with success message
		$redirect_url = add_query_arg( array(
			'page' => 'sme-cache-settings',
			'cache_cleared' => 'success',
		), admin_url( 'admin.php' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}
}

