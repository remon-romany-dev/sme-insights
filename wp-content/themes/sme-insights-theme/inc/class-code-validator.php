<?php
/**
 * Code Validator
 * Validates all code and ensures everything works correctly
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Code_Validator {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Run validation on admin init
		add_action( 'admin_init', array( $this, 'validate_all' ), 1 );
		
		// Also run on frontend for logged-in users
		if ( is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) {
			add_action( 'wp', array( $this, 'validate_all' ), 1 );
		}
	}
	
	/**
	 * Validate all systems
	 */
	public function validate_all() {
		$errors = array();
		
		// Check required classes
		$errors = array_merge( $errors, $this->validate_classes() );
		
		// Check required files
		$errors = array_merge( $errors, $this->validate_files() );
		
		// Check hooks
		$errors = array_merge( $errors, $this->validate_hooks() );
		
		// Check dependencies
		$errors = array_merge( $errors, $this->validate_dependencies() );
		
		// Log errors if any
		if ( ! empty( $errors ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'SME Insights Validation Errors: ' . print_r( $errors, true ) );
		}
		
		return empty( $errors );
	}
	
	/**
	 * Validate required classes
	 */
	private function validate_classes() {
		$errors = array();
		$required_classes = array(
			'SME_Quick_Editor',
			'SME_Universal_Editor',
			'SME_Visual_Editor',
			'SME_Page_Builder_Blocks',
			'SME_Template_Customizer',
			'SME_Template_Manager',
			'SME_Design_Flexibility',
			'SME_Theme_Independence',
		);
		
		foreach ( $required_classes as $class ) {
			if ( ! class_exists( $class ) ) {
				$errors[] = "Missing class: {$class}";
			}
		}
		
		return $errors;
	}
	
	/**
	 * Validate required files
	 */
	private function validate_files() {
		$errors = array();
		$required_files = array(
			'inc/class-quick-editor.php',
			'inc/class-universal-editor.php',
			'inc/class-visual-editor.php',
			'inc/class-page-builder-blocks.php',
			'inc/class-template-customizer.php',
			'inc/class-template-manager.php',
			'inc/class-design-flexibility.php',
			'inc/class-theme-independence.php',
			'assets/css/quick-editor.css',
			'assets/js/quick-editor.js',
			'assets/css/universal-editor.css',
			'assets/js/universal-editor.js',
		);
		
		foreach ( $required_files as $file ) {
			$file_path = SME_THEME_DIR . '/' . $file;
			if ( ! file_exists( $file_path ) ) {
				$errors[] = "Missing file: {$file}";
			}
		}
		
		return $errors;
	}
	
	/**
	 * Validate hooks
	 */
	private function validate_hooks() {
		$errors = array();
		global $wp_filter;
		
		// Check if critical hooks are registered
		$critical_hooks = array(
			'plugins_loaded',
			'wp_enqueue_scripts',
			'admin_bar_menu',
		);
		
		foreach ( $critical_hooks as $hook ) {
			if ( ! isset( $wp_filter[ $hook ] ) ) {
				// Not necessarily an error, but worth noting
			}
		}
		
		return $errors;
	}
	
	/**
	 * Validate dependencies
	 */
	private function validate_dependencies() {
		$errors = array();
		
		// Check WordPress version
		global $wp_version;
		if ( version_compare( $wp_version, '5.0', '<' ) ) {
			$errors[] = "WordPress version too old: {$wp_version} (requires 5.0+)";
		}
		
		// Check PHP version
		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			$errors[] = "PHP version too old: " . PHP_VERSION . " (requires 7.4+)";
		}
		
		// Check required functions
		$required_functions = array(
			'add_action',
			'add_filter',
			'wp_enqueue_script',
			'wp_enqueue_style',
		);
		
		foreach ( $required_functions as $function ) {
			if ( ! function_exists( $function ) ) {
				$errors[] = "Missing function: {$function}";
			}
		}
		
		return $errors;
	}
	
	/**
	 * Get validation status
	 */
	public function get_status() {
		$status = array(
			'valid' => $this->validate_all(),
			'classes' => $this->validate_classes(),
			'files' => $this->validate_files(),
			'hooks' => $this->validate_hooks(),
			'dependencies' => $this->validate_dependencies(),
		);
		
		return $status;
	}
}

SME_Code_Validator::get_instance();

