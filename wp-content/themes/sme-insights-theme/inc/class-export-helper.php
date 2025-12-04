<?php
/**
 * Export Helper
 * Ensures all posts and images are included in WordPress export
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Export_Helper {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_filter( 'export_args', array( $this, 'ensure_all_posts_exported' ), 10, 1 );
		add_filter( 'export_post_ids', array( $this, 'include_all_post_ids' ), 10, 1 );
		add_action( 'export_wp', array( $this, 'increase_export_limits' ), 1 );
		add_filter( 'wp_export_query_args', array( $this, 'remove_export_limits' ), 10, 1 );
		add_filter( 'export_query', array( $this, 'ensure_all_attachments_included' ), 10, 1 );
		add_action( 'export_wp', array( $this, 'include_all_featured_images' ), 5 );
	}

	public function ensure_all_posts_exported( $args ) {
		if ( ! isset( $args['content'] ) || $args['content'] !== 'all' ) {
			return $args;
		}

		if ( ! isset( $args['post_ids'] ) || empty( $args['post_ids'] ) ) {
			global $wpdb;
			
			$all_post_ids = $wpdb->get_col( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} 
				WHERE post_type = %s 
				AND post_status = %s 
				ORDER BY post_date DESC",
				'post',
				'publish'
			) );
			
			if ( ! empty( $all_post_ids ) ) {
				$args['post_ids'] = array_map( 'absint', $all_post_ids );
			}
		}

		return $args;
	}

	public function include_all_post_ids( $post_ids ) {
		if ( empty( $post_ids ) ) {
			global $wpdb;
			
			$all_post_ids = $wpdb->get_col( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} 
				WHERE post_type = %s 
				AND post_status = %s 
				ORDER BY post_date DESC",
				'post',
				'publish'
			) );
			
			if ( ! empty( $all_post_ids ) ) {
				return array_map( 'absint', $all_post_ids );
			}
		}
		
		return $post_ids;
	}

	public function increase_export_limits() {
		@set_time_limit( 600 );
		@ini_set( 'memory_limit', '512M' );
	}

	public function remove_export_limits( $args ) {
		if ( isset( $args['posts_per_page'] ) ) {
			unset( $args['posts_per_page'] );
		}
		
		if ( isset( $args['number'] ) ) {
			unset( $args['number'] );
		}
		
		$args['posts_per_page'] = -1;
		$args['no_found_rows'] = false;
		
		return $args;
	}

	public function ensure_all_attachments_included( $query ) {
		global $wpdb;
		
		if ( ! isset( $query['post_type'] ) || ! in_array( 'attachment', (array) $query['post_type'] ) ) {
			if ( ! is_array( $query['post_type'] ) ) {
				$query['post_type'] = array( $query['post_type'] );
			}
			$query['post_type'][] = 'attachment';
		}
		
		return $query;
	}

	public function include_all_featured_images() {
		global $wpdb;
		
		$post_ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT meta_value FROM {$wpdb->postmeta} 
			WHERE meta_key = %s 
			AND meta_value != '' 
			AND meta_value != '0'",
			'_thumbnail_id'
		) );
		
		if ( ! empty( $post_ids ) ) {
			foreach ( $post_ids as $attachment_id ) {
				$attachment_id = absint( $attachment_id );
				if ( $attachment_id && get_post( $attachment_id ) ) {
					$attachment = get_post( $attachment_id );
					if ( $attachment && $attachment->post_type === 'attachment' ) {
						wp_update_post( array(
							'ID' => $attachment_id,
							'post_status' => 'inherit',
						) );
					}
				}
			}
		}
	}
}

