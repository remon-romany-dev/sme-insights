<?php
/**
 * Assets Management
 * Handles CSS and JavaScript enqueuing with optimization
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Assets {
	
	private static $instance = null;
	private $assets_base = null;
	private $theme_colors = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_dynamic_css' ), 20 );
		add_action( 'wp_head', array( $this, 'add_critical_css' ), 1 );
		
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_styles' ) );
		add_filter( 'script_loader_tag', array( $this, 'add_defer_attribute' ), 99, 2 );
		add_filter( 'style_loader_tag', array( $this, 'optimize_css_loading' ), 99, 2 );
		add_action( 'wp_head', array( $this, 'add_preload_resources' ), 2 );
		add_action( 'init', array( $this, 'add_cache_headers' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'defer_wordpress_scripts' ), 999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'optimize_wordpress_css' ), 999 );
		add_filter( 'script_loader_tag', array( $this, 'add_async_defer_to_third_party' ), 10, 2 );
	}
	
	/**
	 * Get assets base URL with CDN support
	 * Cached to avoid multiple database queries
	 */
	private function get_assets_base() {
		if ( null !== $this->assets_base ) {
			return $this->assets_base;
		}
		
		$cdn_enabled = get_option( 'sme_cdn_enabled', false );
		$this->assets_base = $cdn_enabled ? get_option( 'sme_cdn_url', '' ) : SME_THEME_ASSETS;
		
		return $this->assets_base ? $this->assets_base : SME_THEME_ASSETS;
	}
	
	/**
	 * Get theme colors from Customizer
	 * Cached to avoid multiple database queries
	 */
	private function get_theme_colors() {
		if ( null !== $this->theme_colors ) {
			return $this->theme_colors;
		}
		
		$this->theme_colors = array(
			'accent_primary'   => get_theme_mod( 'sme_accent_primary', '#1a365d' ),
			'accent_secondary' => get_theme_mod( 'sme_accent_secondary', '#2563eb' ),
			'accent_hover'     => get_theme_mod( 'sme_accent_hover', '#1e40af' ),
			'text_primary'     => get_theme_mod( 'sme_text_primary', '#1a202c' ),
			'text_secondary'   => get_theme_mod( 'sme_text_secondary', '#4a5568' ),
			'bg_primary'       => get_theme_mod( 'sme_bg_primary', '#ffffff' ),
			'bg_secondary'     => get_theme_mod( 'sme_bg_secondary', '#f7fafc' ),
			'border_color'     => get_theme_mod( 'sme_border_color', '#e2e8f0' ),
		);
		
		return $this->theme_colors;
	}
	
	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		$assets_base = $this->get_assets_base();
		
		wp_enqueue_script(
			'sme-main',
			$assets_base . '/js/main.js',
			array(),
			SME_THEME_VERSION,
			true
		);
		
		wp_localize_script( 'sme-main', 'smeAjax', array(
			'ajaxurl' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'nonce'   => wp_create_nonce( 'sme_nonce' ),
		) );
		
		wp_localize_script( 'sme-main', 'smeTheme', array(
			'ajaxurl'       => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'newsletterNonce' => wp_create_nonce( 'sme_newsletter_subscribe' ),
		) );
		
		wp_script_add_data( 'sme-main', 'defer', true );
	}
	
	/**
	 * Add defer/async attribute to scripts
	 */
	public function add_defer_attribute( $tag, $handle ) {
		$defer_scripts = array( 'sme-main' );
		$async_scripts = array();
		$exclude_scripts = array( 'jquery-core', 'jquery-migrate' );
		
		if ( strpos( $tag, 'googletagmanager.com' ) !== false || 
		     strpos( $tag, 'gtag/js' ) !== false || 
		     strpos( $tag, 'google-analytics.com' ) !== false ||
		     strpos( $tag, 'accounts.google.com/gsi/client' ) !== false ) {
			if ( strpos( $tag, ' async' ) === false && strpos( $tag, ' defer' ) === false ) {
				return str_replace( ' src', ' async src', $tag );
			}
			return $tag;
		}
		
		if ( in_array( $handle, $exclude_scripts, true ) ) {
			return $tag;
		}
		
		if ( in_array( $handle, $defer_scripts, true ) ) {
			if ( strpos( $tag, ' defer' ) === false && strpos( $tag, ' async' ) === false ) {
				return str_replace( ' src', ' defer src', $tag );
			}
		} elseif ( in_array( $handle, $async_scripts, true ) ) {
			if ( strpos( $tag, ' async' ) === false && strpos( $tag, ' defer' ) === false ) {
				return str_replace( ' src', ' async src', $tag );
			}
		}
		
		return $tag;
	}
	
	public function defer_wordpress_scripts() {
		if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
			return;
		}
		
		add_filter( 'script_loader_tag', function( $tag, $handle ) {
			if ( 'jquery-migrate' === $handle ) {
				if ( strpos( $tag, ' defer' ) === false && strpos( $tag, ' async' ) === false ) {
					return str_replace( ' src', ' defer src', $tag );
				}
			}
			return $tag;
		}, 10, 2 );
	}
	
	public function optimize_wordpress_css() {
	}
	
	public function add_async_defer_to_third_party( $tag, $handle ) {
		$async_scripts = array( 'google-analytics', 'gtag', 'googletagmanager', 'adsbygoogle' );
		
		foreach ( $async_scripts as $async_script ) {
			if ( strpos( $handle, $async_script ) !== false || strpos( $tag, $async_script ) !== false ) {
				if ( strpos( $tag, ' async' ) === false && strpos( $tag, ' defer' ) === false ) {
					$tag = str_replace( ' src', ' async defer src', $tag );
				}
				break;
			}
		}
		
		return $tag;
	}
	
	/**
	 * Optimize CSS loading - make non-critical CSS async
	 */
	public function optimize_css_loading( $tag, $handle ) {
		$wordpress_core_css = array( 'wp-block-library', 'wp-block-library-theme', 'global-styles' );
		$non_critical_css = array( 'sme-blocks', 'sme-header-responsive', 'sme-page-builder-blocks' );
		
		$should_async = false;
		
		foreach ( $wordpress_core_css as $core_css ) {
			if ( strpos( $handle, $core_css ) !== false ) {
				$should_async = true;
				break;
		}
		}
		
		if ( $handle === 'sme-main' ) {
			return $tag;
		}
		
		if ( ! $should_async && in_array( $handle, $non_critical_css, true ) ) {
			$should_async = true;
		}
		
		if ( $should_async ) {
			$tag = $this->make_css_async( $tag );
		}
		
		return $tag;
	}
	
	/**
	 * Convert CSS link to async loading using media="print" trick
	 */
	private function make_css_async( $tag ) {
		$tag = str_replace( array( "media='all'", 'media="all"' ), array( "media='print' data-async-css='true'", 'media="print" data-async-css="true"' ), $tag );
		
		$noscript_tag = str_replace( 
			array( "media='print' data-async-css='true'", 'media="print" data-async-css="true"' ), 
			array( "media='all'", 'media="all"' ), 
			$tag 
		);
		
		return $tag . '<noscript>' . $noscript_tag . '</noscript>';
	}
	
	/**
	 * Enqueue styles
	 * WordPress Best Practice: Proper style enqueuing with dependencies and versioning
	 */
	public function enqueue_styles() {
		$assets_base = $this->get_assets_base();
		$template_dir = get_template_directory();
		
		$styles = array(
			'sme-main' => '/css/main.css',
			'sme-blocks' => '/css/blocks.css',
			'sme-header-responsive' => '/css/header-responsive.css',
		);
		
		foreach ( $styles as $handle => $path ) {
		wp_enqueue_style(
				$handle,
				$assets_base . $path,
			array(),
				SME_THEME_VERSION,
				'all'
		);
		}
		
		if ( file_exists( $template_dir . '/assets/css/page-builder-blocks.css' ) ) {
			wp_enqueue_style(
				'sme-page-builder-blocks',
				$assets_base . '/css/page-builder-blocks.css',
				array(),
				SME_THEME_VERSION,
				'all'
			);
		}
	}
	
	/**
	 * Add critical CSS inline with dynamic colors from options
	 */
	public function add_critical_css() {
		$colors = $this->get_theme_colors();
		
		?>
		<style id="sme-critical-css">
		/* Prevent Flash of White Screen (FOUC) */
		html { background-color: <?php echo esc_attr( $colors['bg_primary'] ); ?> !important; width: 100%; min-height: 100vh; overflow-x: hidden; }
		body { background-color: <?php echo esc_attr( $colors['bg_primary'] ); ?> !important; margin: 0 !important; padding: 0 !important; opacity: 1 !important; width: 100%; min-height: 100vh; overflow-x: hidden; }
		
		:root {
			--bg-primary: <?php echo esc_attr( $colors['bg_primary'] ); ?>;
			--bg-secondary: <?php echo esc_attr( $colors['bg_secondary'] ); ?>;
			--bg-card: #ffffff;
			--bg-dark: <?php echo esc_attr( $colors['accent_primary'] ); ?>;
			--bg-dark-secondary: #2d3748;
			--text-primary: <?php echo esc_attr( $colors['text_primary'] ); ?>;
			--text-secondary: <?php echo esc_attr( $colors['text_secondary'] ); ?>;
			--text-light: #718096;
			--accent-primary: <?php echo esc_attr( $colors['accent_primary'] ); ?>;
			--accent-secondary: <?php echo esc_attr( $colors['accent_secondary'] ); ?>;
			--accent-success: <?php echo esc_attr( $colors['accent_secondary'] ); ?>;
			--accent-hover: <?php echo esc_attr( $colors['accent_hover'] ); ?>;
			--border-color: <?php echo esc_attr( $colors['border_color'] ); ?>;
			--border-dark: #cbd5e0;
			--breaking-gradient: linear-gradient(135deg, <?php echo esc_attr( $colors['accent_primary'] ); ?> 0%, <?php echo esc_attr( $colors['accent_secondary'] ); ?> 100%);
		}
		* { margin: 0; padding: 0; box-sizing: border-box; }
		body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: var(--text-primary); background: var(--bg-primary) !important; }
		img { max-width: 100%; height: auto; display: block; }
		.container-inner { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
		.main-header { background: var(--bg-primary); border-bottom: 1px solid var(--border-color); min-height: 70px; }
		button:focus-visible, a:focus-visible { outline: 2px solid var(--accent-secondary); outline-offset: 2px; }
		.breaking-news-bar { height: 50px; min-height: 50px; contain: layout style paint; will-change: transform; }
		.breaking-news-container { height: 50px; display: flex; align-items: center; }
		.breaking-news-ticker { height: 40px; min-height: 40px; overflow: hidden; contain: layout; }
		.ticker-content { height: 40px; display: inline-flex; align-items: center; will-change: transform; }
		.ticker-item { display: inline-flex; align-items: center; height: 40px; min-height: 40px; flex-shrink: 0; }
		.ticker-item img { width: 40px; height: 30px; object-fit: cover; flex-shrink: 0; display: block; }
		.popular-tags-section { min-height: 40px; }
		.top-bar { min-height: 35px; }
		.header-content { display: flex; align-items: center; justify-content: space-between; min-height: 70px; }
		.desktop-nav { min-height: 50px; }
		.header-actions { display: flex; align-items: center; gap: 10px; min-height: 40px; }
		.site-logo { display: inline-block; min-height: 40px; line-height: 40px; }
		.latest-post-item { min-height: 200px; }
		.latest-post-image { width: 100%; height: auto; aspect-ratio: 16/9; object-fit: cover; }
		.main-nav { display: flex; list-style: none; margin: 0; padding: 0; }
		.main-nav li { margin: 0; padding: 0; }
		.main-nav a { display: block; padding: 10px 15px; }
		.search-overlay, .mobile-nav-wrapper { will-change: transform; }
		
		/* Performance optimizations */
		* { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
		img { content-visibility: auto; }
		.breaking-news-bar, .main-header { content-visibility: auto; }
		</style>
		<script>
		(function() {
			var links = document.querySelectorAll('link[data-async-css="true"]');
			links.forEach(function(link) {
				link.addEventListener('load', function() {
					this.media = 'all';
					this.removeAttribute('data-async-css');
				});
			});
		})();
		</script>
		<!-- Resource Hints for Performance -->
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link rel="dns-prefetch" href="https://www.googletagmanager.com">
		<link rel="dns-prefetch" href="https://www.google-analytics.com">
		<link rel="dns-prefetch" href="https://accounts.google.com">
		
		<!-- Preconnect to same origin for faster asset loading -->
		<link rel="preconnect" href="<?php echo esc_url( home_url() ); ?>">
		<?php
	}
	
	/**
	 * Add preload for critical resources
	 * WordPress Best Practice: Only preload resources that are used immediately
	 */
	public function add_preload_resources() {
		$assets_base = $this->get_assets_base();
		
		$main_css_url = $assets_base . '/css/main.css';
		?>
		<link rel="preload" as="style" href="<?php echo esc_url( $main_css_url ); ?>" id="sme-preload-css">
		<noscript><link rel="stylesheet" href="<?php echo esc_url( $main_css_url ); ?>"></noscript>
		<script>
		(function() {
			var link = document.getElementById('sme-preload-css');
			if (link) {
				link.addEventListener('load', function() {
					this.onload = null;
					this.rel = 'stylesheet';
				});
			}
		})();
		</script>
		<?php
	}
	
	/**
	 * Enqueue dynamic CSS from options
	 */
	public function enqueue_dynamic_css() {
		$css = $this->generate_dynamic_css();
		if ( ! empty( $css ) ) {
			wp_add_inline_style( 'sme-main', $css );
		}
	}
	
	/**
	 * Generate dynamic CSS from theme options
	 */
	private function generate_dynamic_css() {
		$colors = $this->get_theme_colors();
		
		if ( $colors['accent_primary'] === '#1a365d' && 
		     $colors['accent_secondary'] === '#2563eb' && 
		     $colors['accent_hover'] === '#1e40af' ) {
			return '';
		}
		
		$css = ":root {";
		$css .= "--accent-primary: " . esc_attr( $colors['accent_primary'] ) . ";";
		$css .= "--accent-secondary: " . esc_attr( $colors['accent_secondary'] ) . ";";
		$css .= "--accent-hover: " . esc_attr( $colors['accent_hover'] ) . ";";
		$css .= "--text-primary: " . esc_attr( $colors['text_primary'] ) . ";";
		$css .= "--text-secondary: " . esc_attr( $colors['text_secondary'] ) . ";";
		$css .= "--bg-primary: " . esc_attr( $colors['bg_primary'] ) . ";";
		$css .= "--bg-secondary: " . esc_attr( $colors['bg_secondary'] ) . ";";
		$css .= "--border-color: " . esc_attr( $colors['border_color'] ) . ";";
		$css .= "--breaking-gradient: linear-gradient(135deg, " . esc_attr( $colors['accent_primary'] ) . " 0%, " . esc_attr( $colors['accent_secondary'] ) . " 100%);";
			$css .= "}";
		
		return $css;
	}
	
	/**
	 * Add cache headers for static assets
	 * This function is kept for reference but headers should be set at server level
	 */
	public function add_cache_headers() {
		if ( ! is_admin() && ! wp_is_json_request() && ! headers_sent() ) {
			$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			
			if ( ! empty( $request_uri ) && preg_match( '/\.(css|js|jpg|jpeg|png|gif|svg|woff|woff2|ttf|eot|ico|webp|avif)$/i', $request_uri ) ) {
				$expires = 31536000; // 1 year
				header( 'Cache-Control: public, max-age=' . absint( $expires ) . ', immutable' );
				header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + absint( $expires ) ) . ' GMT' );
				
				// Add compression headers for text-based assets
				if ( preg_match( '/\.(css|js|html|svg)$/i', $request_uri ) ) {
					header( 'Vary: Accept-Encoding' );
				}
			}
		}
	}
	
	/**
	 * Enqueue editor styles for Gutenberg
	 * This makes the editor look like the actual page - Elementor Live Style
	 */
	public function enqueue_editor_styles() {
		wp_enqueue_style(
			'sme-editor-main',
			SME_THEME_ASSETS . '/css/main.css',
			array(),
			SME_THEME_VERSION,
			'all'
		);
		
		wp_enqueue_style(
			'sme-editor-blocks',
			SME_THEME_ASSETS . '/css/blocks-editor.css',
			array( 'sme-editor-main', 'wp-edit-blocks' ),
			SME_THEME_VERSION,
			'all'
		);
		
		$editor_css = $this->get_editor_css();
		wp_add_inline_style( 'sme-editor-blocks', $editor_css );
		
		wp_add_inline_style( 'sme-editor-blocks', '
			.editor-styles-wrapper {
				width: 100% !important;
				max-width: 100% !important;
				padding: 0 !important;
				margin: 0 !important;
			}
			.editor-styles-wrapper .block-editor-block-list__layout {
				padding: 0 !important;
				margin: 0 !important;
			}
		' );
	}
	
	/**
	 * Get additional CSS for editor
	 */
	private function get_editor_css() {
		$colors = $this->get_theme_colors();
		
		$css = sprintf(
			'.editor-styles-wrapper { background: %s !important; color: %s !important; }
			.editor-styles-wrapper .wp-block { color: %s !important; }
			.editor-styles-wrapper p { color: %s !important; }
			.editor-styles-wrapper a { color: %s !important; }',
			esc_attr( $colors['bg_primary'] ),
			esc_attr( $colors['text_primary'] ),
			esc_attr( $colors['text_primary'] ),
			esc_attr( $colors['text_secondary'] ),
			esc_attr( $colors['accent_secondary'] )
		);
		
		return $css;
	}
}

