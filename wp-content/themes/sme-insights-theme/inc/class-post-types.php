<?php
/**
 * Custom Post Types
 * Registers and manages custom post types
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Post_Types {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'init', array( $this, 'register_post_types' ) );
	}
	
	/**
	 * Register custom post types
	 */
	public function register_post_types() {
		
		// Main Articles Post Type (default posts will be used, but we'll enhance it)
		// We'll use standard 'post' type but with custom taxonomies
		
		// Main Categories are registered in class-taxonomies.php
	}
}

