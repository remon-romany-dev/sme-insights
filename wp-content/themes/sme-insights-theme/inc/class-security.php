<?php
/**
 * Security Best Practices
 * Implements WordPress security best practices
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Security {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		$this->init_security();
	}
	
	/**
	 * Initialize security measures
	 */
	private function init_security() {
		add_action( 'init', array( $this, 'disable_file_editing' ) );
		add_action( 'init', array( $this, 'remove_version_info' ) );
		add_filter( 'login_errors', array( $this, 'hide_login_errors' ) );
		add_filter( 'style_loader_src', array( $this, 'remove_version_query_string' ), 10, 2 );
		add_filter( 'script_loader_src', array( $this, 'remove_version_query_string' ), 10, 2 );
		add_action( 'wp_head', array( $this, 'add_security_headers' ), 1 );
		add_filter( 'xmlrpc_enabled', '__return_false' );
		add_filter( 'rest_authentication_errors', array( $this, 'restrict_rest_api' ), 99 );
	}
	
	/**
	 * Disable file editing in WordPress admin
	 */
	public function disable_file_editing() {
		if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
			define( 'DISALLOW_FILE_EDIT', true );
		}
	}
	
	/**
	 * Remove WordPress version from head
	 */
	public function remove_version_info() {
		remove_action( 'wp_head', 'wp_generator' );
		add_filter( 'the_generator', '__return_empty_string' );
	}
	
	/**
	 * Hide login errors for security
	 */
	public function hide_login_errors() {
		return __( 'Invalid username or password.', 'sme-insights' );
	}
	
	/**
	 * Remove version query string from scripts and styles
	 */
	public function remove_version_query_string( $src, $handle ) {
		if ( strpos( $src, 'ver=' ) !== false ) {
			$src = remove_query_arg( 'ver', $src );
		}
		return $src;
	}
	
	/**
	 * Add security headers
	 */
	public function add_security_headers() {
		if ( ! headers_sent() ) {
			header( 'X-Content-Type-Options: nosniff' );
			header( 'X-Frame-Options: SAMEORIGIN' );
			header( 'X-XSS-Protection: 1; mode=block' );
			header( 'Referrer-Policy: strict-origin-when-cross-origin' );
			header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
			
			// Content Security Policy (non-restrictive for compatibility)
			// Include worker-src to allow Web Workers and blob: URLs
			$csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://www.googletagmanager.com blob:; worker-src 'self' blob:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com data:; img-src 'self' data: https: http:; connect-src 'self' https:;";
			header( "Content-Security-Policy: {$csp}" );
		}
	}
	
	/**
	 * Restrict REST API access
	 */
	public function restrict_rest_api( $result ) {
		if ( ! empty( $result ) ) {
			return $result;
		}
		
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_cannot_access',
				__( 'Only authenticated users can access the REST API.', 'sme-insights' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		
		return $result;
	}
	
	/**
	 * Sanitize input data
	 */
	public static function sanitize_input( $data, $type = 'text' ) {
		switch ( $type ) {
			case 'email':
				return sanitize_email( $data );
			case 'url':
				return esc_url_raw( $data );
			case 'int':
				return absint( $data );
			case 'float':
				return floatval( $data );
			case 'textarea':
				return sanitize_textarea_field( $data );
			case 'key':
				return sanitize_key( $data );
			case 'text':
			default:
				return sanitize_text_field( $data );
		}
	}
	
	/**
	 * Verify nonce
	 */
	public static function verify_nonce( $nonce, $action ) {
		return wp_verify_nonce( $nonce, $action );
	}
	
	/**
	 * Check user capability
	 */
	public static function check_capability( $capability = 'manage_options' ) {
		return current_user_can( $capability );
	}
}

