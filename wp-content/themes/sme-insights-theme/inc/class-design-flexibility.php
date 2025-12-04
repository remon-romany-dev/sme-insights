<?php
/**
 * Design Flexibility Helper
 * Ensures all Page Builder components work regardless of design changes
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Design_Flexibility {
	
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
		// Add data attributes to header/footer for visual editor
		add_filter( 'wp_head', array( $this, 'add_header_editor_attributes' ), 1 );
		add_filter( 'wp_footer', array( $this, 'add_footer_editor_attributes' ), 1 );
		
		// Add data attributes to body for page editor
		add_filter( 'body_class', array( $this, 'add_body_editor_class' ) );
		
		// Ensure blocks are registered even if theme changes
		add_action( 'after_setup_theme', array( $this, 'ensure_blocks_registered' ), 20 );
		
		// Add fallback selectors for template customizer
		add_filter( 'sme_template_customizer_selectors', array( $this, 'add_fallback_selectors' ), 10, 2 );
		
		// Ensure visual editor scripts load on all pages
		add_action( 'wp_enqueue_scripts', array( $this, 'ensure_visual_editor_scripts' ), 999 );
	}
	
	/**
	 * Add editor attributes to header
	 */
	public function add_header_editor_attributes() {
		// This will be output via JavaScript to ensure it works even if header structure changes
		?>
		<script>
		(function() {
			document.addEventListener('DOMContentLoaded', function() {
				// Find header element (flexible selector)
				const headerSelectors = ['header', '.header', '.main-header', '[role="banner"]', 'header.site-header', '.site-header'];
				let headerElement = null;
				
				for (let i = 0; i < headerSelectors.length; i++) {
					headerElement = document.querySelector(headerSelectors[i]);
					if (headerElement) {
						headerElement.setAttribute('data-sme-editable', 'header');
						headerElement.setAttribute('data-sme-element-type', 'header');
						break;
					}
				}
			});
		})();
		</script>
		<?php
	}
	
	/**
	 * Add editor attributes to footer
	 */
	public function add_footer_editor_attributes() {
		?>
		<script>
		(function() {
			document.addEventListener('DOMContentLoaded', function() {
				// Find footer element (flexible selector)
				const footerSelectors = ['footer', '.footer', '.main-footer', '[role="contentinfo"]', 'footer.site-footer', '.site-footer'];
				let footerElement = null;
				
				for (let i = 0; i < footerSelectors.length; i++) {
					footerElement = document.querySelector(footerSelectors[i]);
					if (footerElement) {
						footerElement.setAttribute('data-sme-editable', 'footer');
						footerElement.setAttribute('data-sme-element-type', 'footer');
						break;
					}
				}
			});
		})();
		</script>
		<?php
	}
	
	/**
	 * Add body class for page editor
	 */
	public function add_body_editor_class( $classes ) {
		if ( is_page() || is_single() || is_category() || is_tax() || is_archive() ) {
			$classes[] = 'sme-page-editable';
		}
		return $classes;
	}
	
	/**
	 * Ensure blocks are registered
	 */
	public function ensure_blocks_registered() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		
		// Force re-register blocks if needed
		if ( class_exists( 'SME_Page_Builder_Blocks' ) ) {
			$instance = SME_Page_Builder_Blocks::get_instance();
			if ( $instance && method_exists( $instance, 'register_blocks' ) ) {
				add_action( 'init', array( $instance, 'register_blocks' ), 20 );
			}
		}
	}
	
	/**
	 * Add fallback selectors for template customizer
	 */
	public function add_fallback_selectors( $selectors, $template_type ) {
		$fallbacks = array(
			'category' => array(
				'.taxonomy-main_category',
				'.category',
				'.archive',
				'body.category',
				'body.tax-main_category',
				'.posts-container',
				'.content-area',
			),
			'single' => array(
				'.single-post',
				'.single',
				'body.single',
				'.entry-content',
				'.post-content',
				'.content-area',
			),
			'archive' => array(
				'.archive',
				'body.archive',
				'.posts-container',
				'.content-area',
			),
		);
		
		if ( isset( $fallbacks[ $template_type ] ) ) {
			$selectors = array_merge( $selectors, $fallbacks[ $template_type ] );
		}
		
		return array_unique( $selectors );
	}
	
	/**
	 * Ensure visual editor scripts load on all pages
	 */
	public function ensure_visual_editor_scripts() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		
		// Force enqueue visual editor scripts
		if ( class_exists( 'SME_Visual_Editor' ) ) {
			$instance = SME_Visual_Editor::get_instance();
			if ( $instance && method_exists( $instance, 'enqueue_scripts' ) ) {
				$instance->enqueue_scripts();
			}
		}
	}
}

SME_Design_Flexibility::get_instance();

