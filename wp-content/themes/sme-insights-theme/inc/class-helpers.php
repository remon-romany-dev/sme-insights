<?php
/**
 * Helper Functions
 * Utility functions for theme functionality
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Helpers {
	
	/**
	 * Get posts by main category
	 *
	 * @param string $category_slug Category slug (sanitized)
	 * @param int    $posts_per_page Number of posts (default: 10)
	 * @param array  $additional_args Additional WP_Query arguments
	 * @return WP_Query|false Query object or false on failure
	 */
	public static function get_posts_by_category( $category_slug, $posts_per_page = 10, $additional_args = array() ) {
		if ( empty( $category_slug ) || ! is_string( $category_slug ) ) {
			return false;
		}
		
		$category_slug = sanitize_key( $category_slug );
		$posts_per_page = absint( $posts_per_page );
		
		if ( $posts_per_page < 1 || $posts_per_page > 100 ) {
			$posts_per_page = 10;
		}
		
		$args = wp_parse_args( $additional_args, array(
			'post_type'      => 'post',
			'posts_per_page' => $posts_per_page,
			'post_status'    => 'publish',
			'no_found_rows'  => false,
			'tax_query'      => array(
				array(
					'taxonomy' => 'main_category',
					'field'    => 'slug',
					'terms'    => $category_slug,
				),
			),
		) );
		
		return new WP_Query( $args );
	}
	
	/**
	 * Get category color
	 *
	 * @param int|string $term_id_or_slug Term ID or slug
	 * @param string     $default_color Default color if not found
	 * @return string Sanitized color hex code
	 */
	public static function get_category_color( $term_id_or_slug, $default_color = '#2563eb' ) {
		$term_id = self::get_term_id( $term_id_or_slug );
		
		if ( ! $term_id ) {
			return sanitize_hex_color( $default_color );
		}
		
		$color = get_term_meta( absint( $term_id ), 'category_color', true );
		
		if ( empty( $color ) || ! self::is_valid_hex_color( $color ) ) {
			return sanitize_hex_color( $default_color );
		}
		
		return sanitize_hex_color( $color );
	}
	
	/**
	 * Get category icon
	 *
	 * @param int|string $term_id_or_slug Term ID or slug
	 * @param string     $default_icon Default icon if not found
	 * @return string Sanitized icon
	 */
	public static function get_category_icon( $term_id_or_slug, $default_icon = '📄' ) {
		$term_id = self::get_term_id( $term_id_or_slug );
		
		if ( ! $term_id ) {
			return sanitize_text_field( $default_icon );
		}
		
		$icon = get_term_meta( absint( $term_id ), 'category_icon', true );
		
		return ! empty( $icon ) ? sanitize_text_field( $icon ) : sanitize_text_field( $default_icon );
	}
	
	/**
	 * Get term ID from ID or slug
	 *
	 * @param int|string $term_id_or_slug Term ID or slug
	 * @return int|false Term ID or false on failure
	 */
	private static function get_term_id( $term_id_or_slug ) {
		if ( is_numeric( $term_id_or_slug ) ) {
			return absint( $term_id_or_slug );
		}
		
		if ( ! is_string( $term_id_or_slug ) || empty( $term_id_or_slug ) ) {
			return false;
		}
		
		$term = get_term_by( 'slug', sanitize_key( $term_id_or_slug ), 'main_category' );
		
		return $term && ! is_wp_error( $term ) ? absint( $term->term_id ) : false;
	}
	
	/**
	 * Validate hex color
	 *
	 * @param string $color Color hex code
	 * @return bool
	 */
	private static function is_valid_hex_color( $color ) {
		return preg_match( '/^#[a-f0-9]{6}$/i', $color ) === 1;
	}
	
	/**
	 * Format post date
	 *
	 * @param int|WP_Post|null $post_id Post ID or post object
	 * @param string           $date_format Date format
	 * @return string Formatted date
	 */
	public static function format_post_date( $post_id = null, $date_format = 'F j, Y' ) {
		if ( ! $date_format || ! is_string( $date_format ) ) {
			$date_format = 'F j, Y';
		}
		
		return get_the_date( $date_format, $post_id );
	}
	
	/**
	 * Get post excerpt
	 *
	 * @param int    $length Excerpt length (default: 30)
	 * @param int    $post_id Post ID
	 * @param string $more_text More text indicator
	 * @return string Sanitized excerpt
	 */
	public static function get_post_excerpt( $length = 30, $post_id = null, $more_text = '...' ) {
		$length = absint( $length );
		
		if ( $length < 1 || $length > 200 ) {
			$length = 30;
		}
		
		$excerpt = get_the_excerpt( $post_id );
		
		if ( empty( $excerpt ) ) {
			$post = get_post( $post_id );
			if ( $post ) {
				$excerpt = $post->post_content;
			}
		}
		
		return wp_trim_words( $excerpt, $length, $more_text );
	}
	
	/**
	 * Get featured image URL
	 *
	 * @param int    $post_id Post ID
	 * @param string $size Image size
	 * @return string|false Image URL or false on failure
	 */
	public static function get_featured_image_url( $post_id = null, $size = 'sme-featured' ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		
		$post_id = absint( $post_id );
		
		if ( ! $post_id ) {
			return false;
		}
		
		$image_id = get_post_thumbnail_id( $post_id );
		
		if ( ! $image_id ) {
			return false;
		}
		
		$image = wp_get_attachment_image_src( absint( $image_id ), sanitize_key( $size ) );
		
		return $image && isset( $image[0] ) ? esc_url_raw( $image[0] ) : false;
	}
}

