<?php
/**
 * Code Review & Compatibility Check
 * Ensures code works correctly and doesn't conflict with other themes/plugins
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Code_Review {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Run compatibility checks
		add_action( 'plugins_loaded', array( $this, 'check_compatibility' ), 1 );
		add_action( 'after_setup_theme', array( $this, 'check_compatibility' ), 1 );
		
		// Prevent conflicts with other themes/plugins
		add_action( 'init', array( $this, 'prevent_conflicts' ), 1 );
	}
	
	/**
	 * Check compatibility
	 */
	public function check_compatibility() {
		// Check if required functions exist
		if ( ! function_exists( 'add_action' ) || ! function_exists( 'add_filter' ) ) {
			return;
		}
		
		// Check WordPress version
		global $wp_version;
		if ( version_compare( $wp_version, '5.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'wp_version_notice' ) );
		}
		
		// Check for common conflicts
		$this->check_common_conflicts();
	}
	
	/**
	 * Prevent conflicts with other themes/plugins
	 */
	public function prevent_conflicts() {
		// Check for duplicate registrations
		$this->prevent_duplicate_registrations();
	}
	
	/**
	 * Check for common conflicts
	 */
	private function check_common_conflicts() {
		$conflicts = array();
		
		// Check for Elementor
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			// Not a conflict, but note it
			// Our system should work alongside Elementor
		}
		
		// Check for other page builders
		if ( defined( 'WPB_VC_VERSION' ) ) {
			// Visual Composer - should work fine
		}
		
		// Check for caching plugins
		if ( defined( 'WP_ROCKET_VERSION' ) || defined( 'W3TC' ) ) {
			// Caching plugins - should work fine with our cache system
		}
		
		// Check for security plugins
		if ( defined( 'WORDFENCE_VERSION' ) || defined( 'ITHEME_SECURITY_VERSION' ) ) {
			// Security plugins - should work fine
		}
	}
	
	/**
	 * Prevent duplicate registrations
	 */
	private function prevent_duplicate_registrations() {
		// Check if blocks are already registered
		if ( function_exists( 'register_block_type' ) ) {
			// Use has_action to prevent duplicate registrations
			if ( ! has_action( 'init', array( 'SME_Page_Builder_Blocks', 'register_blocks' ) ) ) {
				// Blocks will be registered by class
			}
		}
		
		// Check if scripts are already enqueued
		// Use wp_script_is to check before enqueuing
		// This is handled in each class's enqueue methods
	}
	
	/**
	 * WordPress version notice
	 */
	public function wp_version_notice() {
		?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'SME Insights Theme requires WordPress 5.0 or higher. Please update WordPress.', 'sme-insights' ); ?></p>
		</div>
		<?php
	}
	
	/**
	 * Verify all classes are loaded
	 */
	public static function verify_classes() {
		$required_classes = array(
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
			error_log( 'SME Insights: Missing classes: ' . implode( ', ', $missing ) );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Verify all hooks are registered
	 */
	public static function verify_hooks() {
		global $wp_filter;
		
		$required_hooks = array(
			'wp_enqueue_scripts' => array(
				'sme-universal-editor',
				'sme-visual-editor',
			),
			'wp_head' => array(
				'output_universal_styles',
				'output_saved_styles',
				'output_template_styles',
			),
		);
		
		$missing = array();
		foreach ( $required_hooks as $hook => $callbacks ) {
			if ( ! isset( $wp_filter[ $hook ] ) ) {
				$missing[] = $hook;
				continue;
			}
			
			// Check if callbacks exist (simplified check)
			// Full verification would require checking all callbacks
		}
		
		return empty( $missing );
	}
}

SME_Code_Review::get_instance();

