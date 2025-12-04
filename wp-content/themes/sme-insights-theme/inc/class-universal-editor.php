<?php
/**
 * Universal Editor System
 * Works on any design, any theme, any structure - like Elementor
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Universal_Editor {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Use plugins_loaded to work even if theme changes
		add_action( 'plugins_loaded', array( $this, 'init' ), 5 );
		
		// Also hook into after_setup_theme as fallback
		add_action( 'after_setup_theme', array( $this, 'init' ), 999 );
		
		// Ensure it works even after theme switch
		add_action( 'switch_theme', array( $this, 'reinit' ), 10, 2 );
	}
	
	/**
	 * Initialize
	 */
	public function init() {
		// Only if user can edit
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		
		// Enqueue universal scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_universal_scripts' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 999 );
		
		// Output universal styles
		add_action( 'wp_head', array( $this, 'output_universal_styles' ), 999 );
		add_action( 'wp_footer', array( $this, 'output_universal_scripts' ), 999 );
		
		// Add body classes
		add_filter( 'body_class', array( $this, 'add_universal_classes' ) );
	}
	
	/**
	 * Reinitialize after theme switch
	 */
	public function reinit( $new_name, $new_theme ) {
		$this->init();
	}
	
	/**
	 * Enqueue universal scripts
	 */
	public function enqueue_universal_scripts() {
		// Check if already enqueued to prevent duplicates
		// Note: wp_script_is() can only be used inside wp_enqueue_scripts hook
		if ( did_action( 'wp_enqueue_scripts' ) && wp_script_is( 'sme-universal-editor', 'enqueued' ) ) {
			return;
		}
		
		// Universal editor core
		wp_enqueue_script(
			'sme-universal-editor',
			SME_THEME_ASSETS . '/js/universal-editor.js',
			array( 'jquery' ),
			SME_THEME_VERSION,
			true
		);
		
		// Universal styles
		// Note: wp_style_is() can only be used inside wp_enqueue_scripts hook
		$style_enqueued = false;
		if ( did_action( 'wp_enqueue_scripts' ) ) {
			$style_enqueued = wp_style_is( 'sme-universal-editor', 'enqueued' );
		}
		if ( ! $style_enqueued ) {
			wp_enqueue_style(
				'sme-universal-editor',
				SME_THEME_ASSETS . '/css/universal-editor.css',
				array(),
				SME_THEME_VERSION
			);
		}
		
		// Localize script
		wp_localize_script( 'sme-universal-editor', 'smeUniversalEditor', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'sme_universal_editor_nonce' ),
			'canEdit' => current_user_can( 'edit_theme_options' ),
			'isEditMode' => isset( $_GET['sme_edit'] ) && '1' === sanitize_text_field( $_GET['sme_edit'] ) ? 1 : 0,
			'strings' => array(
				'edit' => __( 'Edit', 'sme-insights' ),
				'save' => __( 'Save', 'sme-insights' ),
				'cancel' => __( 'Cancel', 'sme-insights' ),
			),
		) );
	}
	
	/**
	 * Enqueue admin scripts
	 */
	public function enqueue_admin_scripts( $hook ) {
		// Only enqueue on relevant pages
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php', 'appearance_page_sme-template-customizer', 'appearance_page_sme-template-manager' ), true ) ) {
			return;
		}
		
		// Check if already enqueued
		if ( wp_style_is( 'sme-universal-editor-admin', 'enqueued' ) ) {
			return;
		}
		
		wp_enqueue_style(
			'sme-universal-editor-admin',
			SME_THEME_ASSETS . '/css/universal-editor-admin.css',
			array(),
			SME_THEME_VERSION
		);
	}
	
	/**
	 * Output universal styles
	 */
	public function output_universal_styles() {
		$header_styles = get_option( 'sme_header_styles', array() );
		$footer_styles = get_option( 'sme_footer_styles', array() );
		$page_styles = get_option( 'sme_page_styles', array() );
		
		if ( empty( $header_styles ) && empty( $footer_styles ) && empty( $page_styles ) ) {
			return;
		}
		
		echo '<style id="sme-universal-styles">';
		
		// Header styles - use universal selectors
		if ( ! empty( $header_styles ) && is_array( $header_styles ) ) {
			$selectors = $this->get_universal_selectors( 'header' );
			echo implode( ', ', array_map( 'esc_attr', $selectors ) ) . ' {';
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
		
		// Footer styles - use universal selectors
		if ( ! empty( $footer_styles ) && is_array( $footer_styles ) ) {
			$selectors = $this->get_universal_selectors( 'footer' );
			echo implode( ', ', array_map( 'esc_attr', $selectors ) ) . ' {';
			foreach ( $footer_styles as $property => $value ) {
				if ( ! empty( $value ) && $value !== 'auto' && $value !== 'none' && is_string( $property ) ) {
					echo esc_attr( $property ) . ': ' . esc_attr( $value ) . ';';
				}
			}
			echo '}';
		}
		
		// Page styles - use universal selectors
		if ( ! empty( $page_styles ) && is_array( $page_styles ) ) {
			$selectors = $this->get_universal_selectors( 'page' );
			echo implode( ', ', array_map( 'esc_attr', $selectors ) ) . ' {';
			foreach ( $page_styles as $property => $value ) {
				if ( ! empty( $value ) && $value !== 'auto' && $value !== 'none' && is_string( $property ) ) {
					echo esc_attr( $property ) . ': ' . esc_attr( $value ) . ';';
				}
			}
			echo '}';
		}
		
		echo '</style>';
	}
	
	/**
	 * Get universal selectors for element type
	 */
	private function get_universal_selectors( $type ) {
		$selectors = array();
		
		switch ( $type ) {
			case 'header':
				$selectors = array(
					'header',
					'.header',
					'.main-header',
					'.site-header',
					'header.site-header',
					'[role="banner"]',
					'[data-sme-element="header"]',
					'[data-sme-element-type="header"]',
					'[data-sme-editable="header"]',
					'body.sme-has-header > header:first-of-type',
					'body.sme-has-header > .header:first-of-type',
					'body.sme-has-header > .main-header:first-of-type',
				);
				break;
				
			case 'footer':
				$selectors = array(
					'footer',
					'.footer',
					'.main-footer',
					'.site-footer',
					'footer.site-footer',
					'[role="contentinfo"]',
					'[data-sme-element="footer"]',
					'[data-sme-element-type="footer"]',
					'[data-sme-editable="footer"]',
					'body.sme-has-footer > footer:last-of-type',
					'body.sme-has-footer > .footer:last-of-type',
					'body.sme-has-footer > .main-footer:last-of-type',
				);
				break;
				
			case 'page':
				$selectors = array(
					'body.sme-page-editable',
					'body.page',
					'body.single',
					'body.category',
					'body.archive',
					'.content-area',
					'.site-content',
					'#content',
					'main',
					'[role="main"]',
					'[data-sme-element="page"]',
					'[data-sme-editable="page"]',
				);
				break;
		}
		
		// Allow filtering
		return apply_filters( 'sme_universal_selectors_' . $type, $selectors, $type );
	}
	
	/**
	 * Output universal scripts
	 */
	public function output_universal_scripts() {
		// Check if already output to prevent duplicates
		static $outputted = false;
		if ( $outputted ) {
			return;
		}
		$outputted = true;
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		?>
		<script>
		// Universal Editor Initialization
		(function() {
			'use strict';
			
			// Prevent duplicate initialization
			if (window.SMEUniversalEditorInitialized) {
				return;
			}
			window.SMEUniversalEditorInitialized = true;
			
			// Ensure it runs even if DOM is already loaded
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', initUniversalEditor);
			} else {
				initUniversalEditor();
			}
			
			// Run after delay to catch dynamic content
			let initAttempts = 0;
			const maxAttempts = 3;
			const delays = [100, 500, 1000];
			
			delays.forEach(delay => {
				setTimeout(() => {
					if (initAttempts < maxAttempts && typeof window.SMEUniversalEditorCore !== 'undefined' && !window.SMEUniversalEditorCore.initialized) {
						initUniversalEditor();
						initAttempts++;
					}
				}, delay);
			});
			
			function initUniversalEditor() {
				if (typeof window.SMEUniversalEditorCore !== 'undefined' && !window.SMEUniversalEditorCore.initialized) {
					window.SMEUniversalEditorCore.init();
				}
			}
		})();
		</script>
		<?php
	}
	
	/**
	 * Add universal body classes
	 */
	public function add_universal_classes( $classes ) {
		// Add classes to help identify elements
		$classes[] = 'sme-universal-editor-enabled';
		
		// Detect header
		if ( $this->has_element( 'header' ) ) {
			$classes[] = 'sme-has-header';
		}
		
		// Detect footer
		if ( $this->has_element( 'footer' ) ) {
			$classes[] = 'sme-has-footer';
		}
		
		// Page type classes
		if ( is_page() ) {
			$classes[] = 'sme-page-editable';
		}
		if ( is_single() ) {
			$classes[] = 'sme-single-editable';
		}
		if ( is_category() || is_tax() ) {
			$classes[] = 'sme-category-editable';
		}
		if ( is_archive() ) {
			$classes[] = 'sme-archive-editable';
		}
		
		return $classes;
	}
	
	/**
	 * Check if element exists (for body classes)
	 */
	private function has_element( $type ) {
		// PHP check - assume elements exist
		return true;
	}
}

SME_Universal_Editor::get_instance();

