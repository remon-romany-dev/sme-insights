<?php
/**
 * Auto Page Setup
 * Automatically creates required pages on theme activation
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Page_Setup {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Backward compatibility
	}
	
	/**
	 * Create required pages on theme activation
	 */
	public function create_required_pages() {
		$pages = array(
			// Become a Contributor
			array(
				'title'   => 'Become a Contributor',
				'slug'    => 'become-contributor',
				'template' => 'page-become-contributor.php',
				'content' => '',
			),
			// Niche Topics
			array(
				'title'   => 'AI in Business',
				'slug'    => 'ai-in-business',
				'template' => 'page-niche-topic.php',
				'content' => '',
			),
			array(
				'title'   => 'E-commerce Trends',
				'slug'    => 'ecommerce-trends',
				'template' => 'page-niche-topic.php',
				'content' => '',
			),
			array(
				'title'   => 'Startup Funding',
				'slug'    => 'startup-funding',
				'template' => 'page-niche-topic.php',
				'content' => '',
			),
			array(
				'title'   => 'Green Economy',
				'slug'    => 'green-economy',
				'template' => 'page-niche-topic.php',
				'content' => '',
			),
			array(
				'title'   => 'Remote Work',
				'slug'    => 'remote-work',
				'template' => 'page-niche-topic.php',
				'content' => '',
			),
		);
		
		foreach ( $pages as $page ) {
			$this->create_page_if_not_exists( $page );
		}
	}
	
	/**
	 * Create page if it doesn't exist
	 */
	private function create_page_if_not_exists( $page_data ) {
		// Check if page already exists
		$existing_page = get_page_by_path( $page_data['slug'] );
		
		if ( $existing_page ) {
			// Page exists, update template if needed
			update_post_meta( $existing_page->ID, '_wp_page_template', $page_data['template'] );
			return;
		}
		
		// Create new page
		$page_id = wp_insert_post( array(
			'post_title'    => $page_data['title'],
			'post_name'    => $page_data['slug'],
			'post_content' => $page_data['content'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => 1,
		) );
		
		if ( $page_id && ! is_wp_error( $page_id ) ) {
			// Set page template
			update_post_meta( $page_id, '_wp_page_template', $page_data['template'] );
		}
	}
}

