<?php
/**
 * Cache Helper - Comprehensive Cache Management
 * Ensures all caches are properly cleared when needed
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Cache_Helper {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Auto-clear cache on content updates
		add_action( 'save_post', array( $this, 'clear_cache_on_save' ), 10, 2 );
		add_action( 'edited_term', array( $this, 'clear_cache_on_term_edit' ), 10, 3 );
		add_action( 'set_theme_mod', array( $this, 'clear_theme_mod_cache' ), 10, 2 );
	}
	
	/**
	 * Comprehensive cache clearing
	 */
	public static function clear_all_cache() {
		global $wpdb;
		
		// 1. Clear WordPress object cache
		wp_cache_flush();
		
		// 2. Clear all transients
		$wpdb->query( $wpdb->prepare( 
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
			'_transient_%',
			'_site_transient_%'
		) );
		
		// 3. Clear rewrite rules cache
		delete_option( 'rewrite_rules' );
		flush_rewrite_rules( false );
		
		// 4. Clear theme mods cache
		$stylesheet = get_option( 'stylesheet' );
		$theme_mods_option = 'theme_mods_' . $stylesheet;
		wp_cache_delete( $theme_mods_option, 'options' );
		wp_cache_delete( 'alloptions', 'options' );
		
		// 5. Clear post cache
		$post_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_status IN ('publish', 'draft', 'pending', 'private')" );
		if ( ! empty( $post_ids ) ) {
			foreach ( $post_ids as $post_id ) {
				clean_post_cache( $post_id );
			}
		}
		
		// 6. Clear term cache
		$taxonomies = get_taxonomies( array( 'public' => true ), 'names' );
		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				clean_taxonomy_cache( $taxonomy );
			}
		}
		
		// 7. Clear user cache
		$user_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->users}" );
		if ( ! empty( $user_ids ) ) {
			foreach ( $user_ids as $user_id ) {
				clean_user_cache( $user_id );
			}
		}
		
		// 8. Clear comment cache
		$comment_ids = $wpdb->get_col( "SELECT comment_ID FROM {$wpdb->comments}" );
		if ( ! empty( $comment_ids ) ) {
			foreach ( $comment_ids as $comment_id ) {
				clean_comment_cache( $comment_id );
			}
		}
		
		// 9. Clear site cache
		if ( function_exists( 'clean_site_cache' ) ) {
			clean_site_cache( get_current_blog_id() );
		}
		
		// 10. Clear network cache if multisite
		if ( is_multisite() && function_exists( 'clean_network_cache' ) ) {
			clean_network_cache( get_current_network_id() );
		}
		
		// 11. Clear opcache if available
		if ( function_exists( 'opcache_reset' ) ) {
			// Suppress errors only if opcache is disabled or not available
			$opcache_reset = @opcache_reset();
			if ( false === $opcache_reset && function_exists( 'opcache_get_status' ) ) {
				$status = @opcache_get_status();
				if ( ! $status || empty( $status['opcache_enabled'] ) ) {
					// Opcache is disabled, this is expected
				}
			}
		}
		
		// 12. Clear query cache
		wp_cache_delete( 'last_changed', 'posts' );
		wp_cache_delete( 'last_changed', 'terms' );
		
		// 13. Force refresh of alloptions
		wp_cache_delete( 'alloptions', 'options' );
		
		return true;
	}
	
	/**
	 * Clear cache for specific post
	 */
	public function clear_cache_on_save( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		
		// Clear post cache only (more efficient than full flush)
		clean_post_cache( $post_id );
		
		// Clear related caches
		wp_cache_delete( 'last_changed', 'posts' );
		wp_cache_delete( $post_id, 'posts' );
		wp_cache_delete( $post_id, 'post_meta' );
	}
	
	/**
	 * Clear cache for term
	 */
	public function clear_cache_on_term_edit( $term_id, $tt_id, $taxonomy ) {
		clean_term_cache( $term_id, $taxonomy );
		wp_cache_delete( 'last_changed', 'terms' );
		wp_cache_delete( $term_id, $taxonomy );
	}
	
	/**
	 * Clear theme mod cache when updated
	 */
	public function clear_theme_mod_cache( $name, $value ) {
		$stylesheet = get_option( 'stylesheet' );
		$theme_mods_option = 'theme_mods_' . $stylesheet;
		wp_cache_delete( $theme_mods_option, 'options' );
		wp_cache_delete( 'alloptions', 'options' );
	}
	
	/**
	 * Get theme mod without cache
	 */
	public static function get_theme_mod_no_cache( $name, $default = false ) {
		global $wpdb;
		$stylesheet = get_option( 'stylesheet' );
		$theme_mods_option = 'theme_mods_' . $stylesheet;
		
		// Get directly from database
		$theme_mods = $wpdb->get_var( $wpdb->prepare(
			"SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
			$theme_mods_option
		) );
		
		if ( $theme_mods ) {
			$theme_mods = maybe_unserialize( $theme_mods );
			if ( is_array( $theme_mods ) && isset( $theme_mods[ $name ] ) ) {
				return $theme_mods[ $name ];
			}
		}
		
		return $default;
	}
}

