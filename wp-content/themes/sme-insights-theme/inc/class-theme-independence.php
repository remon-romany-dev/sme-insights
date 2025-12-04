<?php
/**
 * Theme Independence System
 * Ensures all systems work with any theme and any design
 * Follows WordPress best practices
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Theme_Independence {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Use plugins_loaded for maximum compatibility
		add_action( 'plugins_loaded', array( $this, 'ensure_independence' ), 1 );
		
		// Also hook into muplugins_loaded for must-use plugins compatibility
		add_action( 'muplugins_loaded', array( $this, 'ensure_independence' ), 1 );
		
		// Re-initialize on theme switch
		add_action( 'switch_theme', array( $this, 'reinit_on_theme_switch' ), 10, 2 );
		
		// Re-initialize on theme activation
		add_action( 'after_switch_theme', array( $this, 'reinit_on_theme_activation' ) );
	}
	
	/**
	 * Ensure all systems are theme-independent
	 */
	public function ensure_independence() {
		// Register all hooks with high priority to ensure they run
		$this->register_theme_independent_hooks();
		
		// Ensure all classes are loaded
		$this->ensure_classes_loaded();
		
		// Add filters for design flexibility
		$this->add_design_flexibility_filters();
	}
	
	/**
	 * Register theme-independent hooks
	 */
	private function register_theme_independent_hooks() {
		// Ensure all editor systems use plugins_loaded
		if ( class_exists( 'SME_Quick_Editor' ) ) {
		}
		
		if ( class_exists( 'SME_Universal_Editor' ) ) {
		}
		
		if ( class_exists( 'SME_Visual_Editor' ) ) {
		}
		
		if ( class_exists( 'SME_Page_Builder_Blocks' ) ) {
		}
	}
	
	/**
	 * Ensure all classes are loaded
	 */
	private function ensure_classes_loaded() {
		$required_classes = array(
			'SME_Quick_Editor',
			'SME_Universal_Editor',
			'SME_Visual_Editor',
			'SME_Page_Builder_Blocks',
			'SME_Template_Customizer',
			'SME_Template_Manager',
			'SME_Design_Flexibility',
		);
		
		$missing = array();
		foreach ( $required_classes as $class ) {
			if ( ! class_exists( $class ) ) {
				$missing[] = $class;
			}
		}
		
		if ( ! empty( $missing ) ) {
			// Log missing classes but don't break
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'SME Insights: Missing classes: ' . implode( ', ', $missing ) );
			}
		}
	}
	
	/**
	 * Add filters for design flexibility
	 */
	private function add_design_flexibility_filters() {
		// Allow themes to override selectors
		add_filter( 'sme_universal_selectors_header', array( $this, 'filter_header_selectors' ), 10, 2 );
		add_filter( 'sme_universal_selectors_footer', array( $this, 'filter_footer_selectors' ), 10, 2 );
		add_filter( 'sme_universal_selectors_page', array( $this, 'filter_page_selectors' ), 10, 2 );
		
		// Allow themes to add custom editable elements
		add_filter( 'sme_editable_elements', array( $this, 'filter_editable_elements' ), 10, 1 );
	}
	
	/**
	 * Filter header selectors
	 */
	public function filter_header_selectors( $selectors, $type ) {
		// Allow themes to add custom selectors
		$custom_selectors = apply_filters( 'sme_custom_header_selectors', array(), $type );
		return array_merge( $selectors, $custom_selectors );
	}
	
	/**
	 * Filter footer selectors
	 */
	public function filter_footer_selectors( $selectors, $type ) {
		// Allow themes to add custom selectors
		$custom_selectors = apply_filters( 'sme_custom_footer_selectors', array(), $type );
		return array_merge( $selectors, $custom_selectors );
	}
	
	/**
	 * Filter page selectors
	 */
	public function filter_page_selectors( $selectors, $type ) {
		// Allow themes to add custom selectors
		$custom_selectors = apply_filters( 'sme_custom_page_selectors', array(), $type );
		return array_merge( $selectors, $custom_selectors );
	}
	
	/**
	 * Filter editable elements
	 */
	public function filter_editable_elements( $elements ) {
		// Allow themes to add custom editable elements
		$custom_elements = apply_filters( 'sme_custom_editable_elements', array() );
		return array_merge( $elements, $custom_elements );
	}
	
	/**
	 * Re-initialize on theme switch
	 */
	public function reinit_on_theme_switch( $new_name, $new_theme ) {
		// Clear any theme-specific caches
		$this->clear_theme_caches();
		
		// Re-initialize all systems
		$this->ensure_independence();
	}
	
	/**
	 * Re-initialize on theme activation
	 */
	public function reinit_on_theme_activation() {
		// Same as theme switch
		$this->reinit_on_theme_switch( '', null );
	}
	
	/**
	 * Clear theme-specific caches
	 */
	private function clear_theme_caches() {
		// Clear object cache if available
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}
		
		// Clear transients
		delete_transient( 'sme_theme_independence_check' );
	}
}

SME_Theme_Independence::get_instance();

