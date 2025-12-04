<?php
/**
 * Content Importer
 * Imports all static pages and posts with images and content
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Content_Importer {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Only import on theme activation, not on every page load
		// This ensures content is only imported once, not on every save
		add_action( 'after_switch_theme', array( $this, 'clean_and_import_all_content' ) );
	}
	
	/**
	 * Clean all theme content and import fresh content
	 * This runs on every theme activation to ensure clean installation
	 */
	public function clean_and_import_all_content() {
		// Increase execution time limit for this operation
		// Using @ operator because set_time_limit() may not work on some servers
		$time_limit_set = @set_time_limit( 600 ); // 10 minutes
		if ( false === $time_limit_set && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'SME Insights: Could not set time limit to 600 seconds' );
		}
		
		// First, clean all existing theme content
		$this->clean_all_theme_content();
		
		// Then import fresh content
		$this->import_all_content();
		
		// Ensure all existing posts have images and categories
		$this->fix_existing_posts();
	}
	
	/**
	 * Fix existing posts - add images and remove Uncategorized
	 */
	private function fix_existing_posts() {
		$posts = get_posts( array(
			'post_type' => 'post',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		) );
		
		// Map post titles to their correct categories
		$post_category_map = array(
			'AI in Marketing Automation' => 'Marketing',
			'Green Marketing Strategies' => 'Marketing',
			'Advanced Computing in Marketing' => 'Marketing',
			'Rally Marketing' => 'Marketing',
			'SME Digital Transformation' => 'Technology',
			'Essential Business Strategies' => 'Strategy',
			'Financial Planning Guide' => 'Finance',
			'Digital Tools' => 'Technology',
			'Social Media Marketing' => 'Marketing',
			'New Government Grants' => 'Finance',
			'Small Business Loans' => 'Finance',
			'Small Business Tax Changes' => 'Finance',
			'Tax Planning Strategies' => 'Finance',
			'Budget Management Tips' => 'Finance',
			'Investment Strategies' => 'Finance',
			'Cash Flow Management' => 'Finance',
			'E-commerce Growth' => 'Marketing',
			'Building a Strong Brand Identity' => 'Marketing',
			'Customer Retention Strategies' => 'Marketing',
		);
		
		foreach ( $posts as $post ) {
			if ( ! isset( $post->ID ) || ! $post->ID ) {
				continue;
			}
			
			$post_id = absint( $post->ID );
			if ( ! $post_id ) {
				continue;
			}
			
			// Determine correct category based on post title
			$correct_category = null;
			foreach ( $post_category_map as $title_key => $category_name ) {
				if ( strpos( $post->post_title, $title_key ) !== false ) {
					$correct_category = $category_name;
					break;
				}
			}
			
			// If we found a matching category, add it (allow multiple categories)
			if ( $correct_category ) {
				$term = get_term_by( 'name', $correct_category, 'main_category' );
				if ( $term && ! is_wp_error( $term ) ) {
					// Add category (allow multiple categories)
					wp_set_object_terms( $post_id, array( $term->term_id ), 'main_category', true );
				}
			} else {
				// If no matching category found, assign default category
				$this->assign_default_category( $post_id );
			}
			
			// Ensure post has tags
			$this->ensure_post_has_tags( $post_id, $post->post_title );
			
			// Fix post "SME Digital Transformation" specifically
			if ( strpos( $post->post_title, 'SME Digital Transformation' ) !== false ) {
				// Always set/update the image (even if it exists, to ensure correct image)
				$this->set_featured_image_from_url( $post_id, 'https://images.unsplash.com/photo-1556761175-b3da737b1109?w=800&h=410&fit=crop', $post->post_title );
				// Ensure it has Technology category (add, allow multiple)
				$tech_term = get_term_by( 'name', 'Technology', 'main_category' );
				if ( $tech_term && ! is_wp_error( $tech_term ) ) {
					// Add category (allow multiple categories)
					wp_set_object_terms( $post_id, array( $tech_term->term_id ), 'main_category', true );
				}
			}
			
			// Remove Uncategorized from all posts
			$uncategorized = get_term_by( 'slug', 'uncategorized', 'main_category' );
			if ( $uncategorized && ! is_wp_error( $uncategorized ) ) {
				wp_remove_object_terms( $post_id, $uncategorized->term_id, 'main_category' );
			}
			
			// Ensure post has image
			if ( ! has_post_thumbnail( $post_id ) ) {
				$this->set_default_featured_image( $post_id, $post->post_title );
			}
			
			// Ensure post has valid category
			$terms = wp_get_post_terms( $post_id, 'main_category' );
			$has_valid = false;
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( $term->slug !== 'uncategorized' && $term->name !== 'Uncategorized' ) {
						$has_valid = true;
						break;
					}
				}
			}
			if ( ! $has_valid ) {
				$this->assign_default_category( $post_id );
			}
		}
	}
	
	/**
	 * Clean all theme-related content from database
	 * This deletes all pages, posts, attachments, and clears cache
	 */
	private function clean_all_theme_content() {
		global $wpdb;
		
		// Increase execution time limit for this operation
		// Using @ operator because set_time_limit() may not work on some servers
		$time_limit_set = @set_time_limit( 300 ); // 5 minutes
		if ( false === $time_limit_set && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'SME Insights: Could not set time limit to 300 seconds' );
		}
		
		// Set flag to indicate we're cleaning
		if ( ! defined( 'SME_CLEANING_CONTENT' ) ) {
			define( 'SME_CLEANING_CONTENT', true );
		}
		
		// 1. Delete all pages in batches (including trash)
		$batch_size = 100;
		$offset = 0;
		do {
			$page_ids = $wpdb->get_col( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s LIMIT %d OFFSET %d",
				'page',
				$batch_size,
				$offset
			) );
			
			if ( ! empty( $page_ids ) ) {
				foreach ( $page_ids as $page_id ) {
					// Force delete (bypass trash) - permanently delete
					wp_delete_post( $page_id, true );
				}
				$offset += $batch_size;
			} else {
				break;
			}
		} while ( count( $page_ids ) === $batch_size );
		
		// 2. Delete all posts in batches (including trash)
		$offset = 0;
		do {
			$post_ids = $wpdb->get_col( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s LIMIT %d OFFSET %d",
				'post',
				$batch_size,
				$offset
			) );
			
			if ( ! empty( $post_ids ) ) {
				foreach ( $post_ids as $post_id ) {
					// Delete featured image first
					$thumbnail_id = get_post_thumbnail_id( $post_id );
					if ( $thumbnail_id ) {
						wp_delete_attachment( $thumbnail_id, true );
					}
					
					// Force delete post
					wp_delete_post( $post_id, true );
				}
				$offset += $batch_size;
			} else {
				break;
			}
		} while ( count( $post_ids ) === $batch_size );
		
		// 3. Delete all attachments in batches (images) uploaded by theme
		$offset = 0;
		do {
			$attachment_ids = $wpdb->get_col( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s LIMIT %d OFFSET %d",
				'attachment',
				$batch_size,
				$offset
			) );
			
			if ( ! empty( $attachment_ids ) ) {
				foreach ( $attachment_ids as $attachment_id ) {
					wp_delete_attachment( $attachment_id, true );
				}
				$offset += $batch_size;
			} else {
				break;
			}
		} while ( count( $attachment_ids ) === $batch_size );
		
		// 4. Clear all cache
		// Use cache helper if available, otherwise fallback to basic clearing
		if ( class_exists( 'SME_Cache_Helper' ) ) {
			SME_Cache_Helper::clear_all_cache();
		} else {
			// Fallback: basic cache clearing
			wp_cache_flush();
			global $wpdb;
			$wpdb->query( $wpdb->prepare( 
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
				'_transient_%',
				'_site_transient_%'
			) );
			clean_post_cache( 0 );
			
			$taxonomies = get_taxonomies( array( 'public' => true ), 'names' );
			if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					if ( is_string( $taxonomy ) ) {
						clean_taxonomy_cache( $taxonomy );
					}
				}
			}
		}
		
		// 5. Flush rewrite rules
		flush_rewrite_rules( false );
	}
	
	/**
	 * Import all content (pages and posts)
	 * Made public so it can be called from Content Manager
	 */
	public function import_all_content() {
		// Increase execution time limit for this operation
		// Increase execution time limit for this operation
		// Using @ operator because set_time_limit() may not work on some servers
		$time_limit_set = @set_time_limit( 600 ); // 10 minutes
		if ( false === $time_limit_set && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'SME Insights: Could not set time limit to 600 seconds' );
		}
		
		// Set flag to indicate we're importing
		// This allows prevent_content_overwrite to skip during import
		if ( ! defined( 'SME_IMPORTING_CONTENT' ) ) {
			define( 'SME_IMPORTING_CONTENT', true );
		}
		
		$this->create_all_pages();
		$this->create_categories_and_tags();
		$this->create_all_posts();
		// Ensure all categories have posts and all posts have categories
		$this->ensure_categories_have_posts();
		$this->ensure_posts_have_categories();
		// Fix existing posts - add images and remove Uncategorized
		$this->fix_existing_posts();
		
		// Create/update Primary Menu with correct order
		if ( class_exists( 'SME_Theme_Setup' ) ) {
			$theme_setup = SME_Theme_Setup::get_instance();
			if ( method_exists( $theme_setup, 'create_default_menu' ) ) {
				$theme_setup->create_default_menu();
			}
		}
	}
	
	/**
	 * Create all static pages
	 */
	private function create_all_pages() {
		$pages = array(
			// Homepage
			array(
				'title'    => 'Home',
				'slug'     => 'home',
				'template' => 'front-page.php',
				'content'  => $this->get_page_content( 'home' ),
			),
			// Main Pages
			array(
				'title'   => 'About Us',
				'slug'    => 'about',
				'template' => 'page-about.php',
				'content' => $this->get_page_content( 'about' ),
			),
			array(
				'title'   => 'Contact Us',
				'slug'    => 'contact',
				'template' => 'page-contact.php',
				'content' => $this->get_page_content( 'contact' ),
			),
			// Legal Pages
			array(
				'title'   => 'Privacy Policy',
				'slug'    => 'privacy-policy',
				'template' => 'page-privacy-policy.php',
				'content' => $this->get_page_content( 'privacy-policy' ),
			),
			array(
				'title'   => 'Terms of Service',
				'slug'    => 'terms-of-service',
				'template' => 'page-terms-of-service.php',
				'content' => $this->get_page_content( 'terms-of-service' ),
			),
			array(
				'title'   => 'Disclaimer',
				'slug'    => 'disclaimer',
				'template' => 'page-disclaimer.php',
				'content' => $this->get_page_content( 'disclaimer' ),
			),
			array(
				'title'   => 'Advertise With Us',
				'slug'    => 'advertise-with-us',
				'template' => 'page-advertise-with-us.php',
				'content' => $this->get_page_content( 'advertise-with-us' ),
			),
			// Contributor & Niche Topics
			array(
				'title'   => 'Become a Contributor',
				'slug'    => 'become-contributor',
				'template' => 'page-become-contributor.php',
				'content' => $this->get_page_content( 'become-contributor' ),
			),
			array(
				'title'   => 'AI in Business',
				'slug'    => 'ai-in-business',
				'template' => 'page-niche-topic.php',
				'content' => $this->get_page_content( 'ai-in-business' ),
			),
			array(
				'title'   => 'E-commerce Trends',
				'slug'    => 'ecommerce-trends',
				'template' => 'page-niche-topic.php',
				'content' => $this->get_page_content( 'ecommerce-trends' ),
			),
			array(
				'title'   => 'Startup Funding',
				'slug'    => 'startup-funding',
				'template' => 'page-niche-topic.php',
				'content' => $this->get_page_content( 'startup-funding' ),
			),
			array(
				'title'   => 'Green Economy',
				'slug'    => 'green-economy',
				'template' => 'page-niche-topic.php',
				'content' => $this->get_page_content( 'green-economy' ),
			),
			array(
				'title'   => 'Remote Work',
				'slug'    => 'remote-work',
				'template' => 'page-niche-topic.php',
				'content' => $this->get_page_content( 'remote-work' ),
			),
		);
		
		foreach ( $pages as $page ) {
			$this->create_page_if_not_exists( $page );
		}
	}
	
	/**
	 * Get Gutenberg content for pages
	 */
	private function get_page_content( $page_type ) {
		$contents = array(
			'home' => '<!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"></div>
<!-- /wp:group -->',
			
			'about' => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"60px","bottom":"60px"},"margin":{"bottom":"80px"}}},"backgroundColor":"background","layout":{"type":"default"}} -->
<div class="wp-block-group has-background-background-color has-background" style="margin-bottom:80px;padding-top:60px;padding-bottom:60px;border-radius:12px;"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"2.5rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"25px"}}}} -->
<h2 class="wp-block-heading has-text-align-center" style="margin-bottom:25px;font-size:2.5rem;font-weight:700;color:var(--text-primary);">The Challenge for Small Businesses</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.2rem","lineHeight":"1.9"},"spacing":{"margin":{"bottom":"0px"}}}} -->
<p class="has-text-align-center" style="margin-bottom:0px;font-size:1.2rem;line-height:1.9;color:var(--text-secondary);max-width:800px;margin-left:auto;margin-right:auto;">In today\'s rapidly changing business landscape, small business owners face an enormous challenge in accessing reliable information and practical strategies. Between conflicting advice and theoretical content, it\'s difficult to know what actually works in the real world. Many entrepreneurs find themselves drowning in generic advice that doesn\'t address their specific needs or circumstances.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"layout":{"type":"default"},"style":{"spacing":{"margin":{"bottom":"80px"}}}} -->
<div class="wp-block-group" style="margin-bottom:80px;"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"2.5rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"25px"}}}} -->
<h2 class="wp-block-heading has-text-align-center" style="margin-bottom:25px;font-size:2.5rem;font-weight:700;color:var(--text-primary);">Our Mission: To Cut Through the Noise</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.2rem","lineHeight":"1.9"},"spacing":{"margin":{"bottom":"30px"}}}} -->
<p class="has-text-align-center" style="margin-bottom:30px;font-size:1.2rem;line-height:1.9;color:var(--text-secondary);max-width:800px;margin-left:auto;margin-right:auto;">That\'s why we founded SME Insights. Our mission is simple: to provide small and medium business leaders with practical insights, deep analysis, and guidance from real experts in the field. We believe that the right information at the right time is the most powerful tool for growth.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem","lineHeight":"1.8"}}} -->
<p class="has-text-align-center" style="font-size:1.1rem;line-height:1.8;color:var(--text-secondary);max-width:800px;margin-left:auto;margin-right:auto;">Every article we publish is either written by or reviewed by industry professionals who have walked the path you\'re on. We don\'t just report what\'s happening—we explain what it means for your business and how you can act on it.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"2.5rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"50px","top":"80px"}}}} -->
<h2 class="wp-block-heading has-text-align-center" style="margin-top:80px;margin-bottom:50px;font-size:2.5rem;font-weight:700;color:var(--text-primary);">The Experts Behind the Insights</h2>
<!-- /wp:heading -->

<!-- wp:columns {"style":{"spacing":{"margin":{"bottom":"80px"}}}} -->
<div class="wp-block-columns" style="margin-bottom:80px;"><!-- wp:column {"style":{"spacing":{"padding":{"top":"35px","right":"35px","bottom":"35px","left":"35px"}}},"backgroundColor":"base","layout":{"type":"default"}} -->
<div class="wp-block-column has-base-background-color has-background" style="padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:35px;background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08);text-align:center;"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","style":{"border":{"radius":"50%"}}} -->
<figure class="wp-block-image size-large"><img alt="Sarah Mitchell" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&amp;h=200&amp;fit=crop" style="width:150px;height:150px;border-radius:50%;object-fit:cover;margin:0 auto 20px;display:block;border:4px solid var(--bg-secondary);"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.4rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"8px"}}}} -->
<h3 class="wp-block-heading has-text-align-center" style="margin-bottom:8px;font-size:1.4rem;font-weight:700;color:var(--accent-secondary);">Sarah Mitchell</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"600"},"spacing":{"margin":{"bottom":"15px"}}}} -->
<p class="has-text-align-center" style="margin-bottom:15px;font-weight:600;color:var(--text-secondary);">Founder &amp; Editor-in-Chief</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.95rem","lineHeight":"1.7"}}} -->
<p class="has-text-align-center" style="font-size:0.95rem;line-height:1.7;color:var(--text-secondary);margin-bottom:20px;">With over 15 years of experience in business journalism and entrepreneurship, Sarah has helped thousands of small business owners navigate complex challenges. She holds an MBA from Harvard Business School.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"35px","right":"35px","bottom":"35px","left":"35px"}}},"backgroundColor":"base","layout":{"type":"default"}} -->
<div class="wp-block-column has-base-background-color has-background" style="padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:35px;background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08);text-align:center;"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","style":{"border":{"radius":"50%"}}} -->
<figure class="wp-block-image size-large"><img alt="David Chen" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&amp;h=200&amp;fit=crop" style="width:150px;height:150px;border-radius:50%;object-fit:cover;margin:0 auto 20px;display:block;border:4px solid var(--bg-secondary);"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.4rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"8px"}}}} -->
<h3 class="wp-block-heading has-text-align-center" style="margin-bottom:8px;font-size:1.4rem;font-weight:700;color:var(--accent-secondary);">David Chen</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"600"},"spacing":{"margin":{"bottom":"15px"}}}} -->
<p class="has-text-align-center" style="margin-bottom:15px;font-weight:600;color:var(--text-secondary);">Lead Financial Analyst</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.95rem","lineHeight":"1.7"}}} -->
<p class="has-text-align-center" style="font-size:0.95rem;line-height:1.7;color:var(--text-secondary);margin-bottom:20px;">A certified financial planner with 12 years of experience helping SMEs optimize their finances. David has worked with over 500 small businesses on funding, tax strategy, and financial planning.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"35px","right":"35px","bottom":"35px","left":"35px"}}},"backgroundColor":"base","layout":{"type":"default"}} -->
<div class="wp-block-column has-base-background-color has-background" style="padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:35px;background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08);text-align:center;"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","style":{"border":{"radius":"50%"}}} -->
<figure class="wp-block-image size-large"><img alt="Emily Rodriguez" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&amp;h=200&amp;fit=crop" style="width:150px;height:150px;border-radius:50%;object-fit:cover;margin:0 auto 20px;display:block;border:4px solid var(--bg-secondary);"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.4rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"8px"}}}} -->
<h3 class="wp-block-heading has-text-align-center" style="margin-bottom:8px;font-size:1.4rem;font-weight:700;color:var(--accent-secondary);">Emily Rodriguez</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"600"},"spacing":{"margin":{"bottom":"15px"}}}} -->
<p class="has-text-align-center" style="margin-bottom:15px;font-weight:600;color:var(--text-secondary);">Marketing Strategy Expert</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.95rem","lineHeight":"1.7"}}} -->
<p class="has-text-align-center" style="font-size:0.95rem;line-height:1.7;color:var(--text-secondary);margin-bottom:20px;">Serial entrepreneur and digital marketing specialist. Emily has built and scaled three successful businesses, generating over $50M in combined revenue. She shares real-world strategies that actually work.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"60px","bottom":"60px"},"margin":{"bottom":"80px"}}},"backgroundColor":"background","layout":{"type":"default"}} -->
<div class="wp-block-group has-background-background-color has-background" style="margin-bottom:80px;padding-top:60px;padding-bottom:60px;border-radius:12px;"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"2.5rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"20px"}}}} -->
<h2 class="wp-block-heading has-text-align-center" style="margin-bottom:20px;font-size:2.5rem;font-weight:700;color:var(--text-primary);">What We Stand For</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"},"spacing":{"margin":{"bottom":"50px"}}}} -->
<p class="has-text-align-center" style="margin-bottom:50px;font-size:1.1rem;color:var(--text-secondary);max-width:700px;margin-left:auto;margin-right:auto;">Our core values guide everything we do and shape the content we deliver to you.</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"style":{"spacing":{"margin":{"bottom":"0px"}}}} -->
<div class="wp-block-columns" style="margin-bottom:0px;"><!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}}},"backgroundColor":"base","layout":{"type":"default"}} -->
<div class="wp-block-column has-base-background-color has-background" style="padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px;background:linear-gradient(135deg, #f7fafc 0%, #ffffff 100%);border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid var(--border-color);position:relative;overflow:hidden;text-align:center;"><!-- wp:html -->
<div style="position:absolute;top:0;right:0;width:100px;height:100px;background:linear-gradient(135deg, rgba(234, 88, 12, 0.05) 0%, transparent 100%);border-radius:0 0 0 100px;"></div>
<div style="width:70px;height:70px;margin:0 auto 25px;background:#ea580c;border-radius:50%;display:flex;align-items:center;justify-content:center;position:relative;z-index:1;">
<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color:#fff;"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
</div>
<!-- /wp:html -->

<!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"15px"}}}} -->
<h3 class="wp-block-heading has-text-align-center" style="margin-bottom:15px;font-size:1.5rem;font-weight:700;color:var(--text-primary);">Practicality Over Theory</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1rem","lineHeight":"1.7"}}} -->
<p class="has-text-align-center" style="font-size:1rem;line-height:1.7;color:var(--text-secondary);">We focus on strategies you can implement today, not abstract concepts. Every piece of content is designed to be actionable and immediately useful.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}}},"backgroundColor":"base","layout":{"type":"default"}} -->
<div class="wp-block-column has-base-background-color has-background" style="padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px;background:linear-gradient(135deg, #f7fafc 0%, #ffffff 100%);border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid var(--border-color);position:relative;overflow:hidden;text-align:center;"><!-- wp:html -->
<div style="position:absolute;top:0;right:0;width:100px;height:100px;background:linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, transparent 100%);border-radius:0 0 0 100px;"></div>
<div style="width:70px;height:70px;margin:0 auto 25px;background:var(--accent-secondary);border-radius:50%;display:flex;align-items:center;justify-content:center;position:relative;z-index:1;">
<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color:#fff;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
</div>
<!-- /wp:html -->

<!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"15px"}}}} -->
<h3 class="wp-block-heading has-text-align-center" style="margin-bottom:15px;font-size:1.5rem;font-weight:700;color:var(--text-primary);">Expert-Driven Content</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1rem","lineHeight":"1.7"}}} -->
<p class="has-text-align-center" style="font-size:1rem;line-height:1.7;color:var(--text-secondary);">Every article is written by or reviewed by a real expert in their field. We don\'t publish generic content—we deliver insights from people who\'ve been there.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}}},"backgroundColor":"base","layout":{"type":"default"}} -->
<div class="wp-block-column has-base-background-color has-background" style="padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px;background:linear-gradient(135deg, #f7fafc 0%, #ffffff 100%);border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid var(--border-color);position:relative;overflow:hidden;text-align:center;"><!-- wp:html -->
<div style="position:absolute;top:0;right:0;width:100px;height:100px;background:linear-gradient(135deg, rgba(14, 165, 233, 0.05) 0%, transparent 100%);border-radius:0 0 0 100px;"></div>
<div style="width:70px;height:70px;margin:0 auto 25px;background:#0ea5e9;border-radius:50%;display:flex;align-items:center;justify-content:center;position:relative;z-index:1;">
<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color:#fff;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
</div>
<!-- /wp:html -->

<!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"15px"}}}} -->
<h3 class="wp-block-heading has-text-align-center" style="margin-bottom:15px;font-size:1.5rem;font-weight:700;color:var(--text-primary);">Community-Focused</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1rem","lineHeight":"1.7"}}} -->
<p class="has-text-align-center" style="font-size:1rem;line-height:1.7;color:var(--text-secondary);">We\'re a platform for experts to share their knowledge, not just a publication. Our community of contributors and readers drives everything we do.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"40px","right":"30px","bottom":"40px","left":"30px"}}},"backgroundColor":"base","layout":{"type":"default"}} -->
<div class="wp-block-column has-base-background-color has-background" style="padding-top:40px;padding-right:30px;padding-bottom:40px;padding-left:30px;background:linear-gradient(135deg, #f7fafc 0%, #ffffff 100%);border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid var(--border-color);position:relative;overflow:hidden;text-align:center;"><!-- wp:html -->
<div style="position:absolute;top:0;right:0;width:100px;height:100px;background:linear-gradient(135deg, rgba(6, 95, 70, 0.05) 0%, transparent 100%);border-radius:0 0 0 100px;"></div>
<div style="width:70px;height:70px;margin:0 auto 25px;background:#065f46;border-radius:50%;display:flex;align-items:center;justify-content:center;position:relative;z-index:1;">
<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color:#fff;"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
</div>
<!-- /wp:html -->

<!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.5rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"15px"}}}} -->
<h3 class="wp-block-heading has-text-align-center" style="margin-bottom:15px;font-size:1.5rem;font-weight:700;color:var(--text-primary);">Transparency &amp; Trust</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1rem","lineHeight":"1.7"}}} -->
<p class="has-text-align-center" style="font-size:1rem;line-height:1.7;color:var(--text-secondary);">We\'re transparent about our sources, our experts, and our methods. Trust is earned, and we work every day to maintain yours.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"60px","bottom":"60px"}}},"gradient":"breaking-gradient","layout":{"type":"default"}} -->
<div class="wp-block-group has-breaking-gradient-gradient-background has-background" style="padding-top:60px;padding-bottom:60px"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"2.5rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"25px"}}}} -->
<h2 class="wp-block-heading has-text-align-center" style="margin-bottom:25px;font-size:2.5rem;font-weight:700">Join Our Community</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.2rem"},"spacing":{"margin":{"bottom":"40px"}}}} -->
<p class="has-text-align-center" style="margin-bottom:40px;font-size:1.2rem">Become part of a community of business leaders who are committed to growth, learning, and success. Whether you want to stay informed, share your expertise, or connect with like-minded entrepreneurs, we\'re here for you.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"base","textColor":"contrast","style":{"spacing":{"padding":{"top":"15px","bottom":"15px","left":"35px","right":"35px"}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-contrast-color has-base-background-color has-text-color has-background wp-element-button" style="padding-top:15px;padding-right:35px;padding-bottom:15px;padding-left:35px">Subscribe to Newsletter</a></div>
<!-- /wp:button -->

<!-- wp:button {"textColor":"base","style":{"border":{"width":"2px","style":"solid"},"spacing":{"padding":{"top":"15px","bottom":"15px","left":"35px","right":"35px"}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-base-color has-text-color wp-element-button" style="border-width:2px;border-style:solid;padding-top:15px;padding-right:35px;padding-bottom:15px;padding-left:35px">Become a Contributor</a></div>
<!-- /wp:button -->

<!-- wp:button {"textColor":"base","style":{"border":{"width":"2px","style":"solid"},"spacing":{"padding":{"top":"15px","bottom":"15px","left":"35px","right":"35px"}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-base-color has-text-color wp-element-button" style="border-width:2px;border-style:solid;padding-top:15px;padding-right:35px;padding-bottom:15px;padding-left:35px">Contact Us</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->',
			
			'contact' => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"60px","bottom":"40px"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="padding-top:60px;padding-bottom:40px"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"2rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"15px"}}}} -->
<h2 class="wp-block-heading has-text-align-center" style="margin-bottom:15px;font-size:2rem;font-weight:700">Before You Reach Out</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"},"spacing":{"margin":{"bottom":"50px"}}}} -->
<p class="has-text-align-center" style="margin-bottom:50px;font-size:1.1rem">Find quick answers to common questions or get directed to the right place for your inquiry.</p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.4rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"15px"}}}} -->
<h3 class="wp-block-heading has-text-align-center" style="margin-bottom:15px;font-size:1.4rem;font-weight:700">Question about an article?</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.95rem","lineHeight":"1.7"},"spacing":{"margin":{"bottom":"25px"}}}} -->
<p class="has-text-align-center" style="margin-bottom:25px;font-size:0.95rem;line-height:1.7">The best place to discuss our content is in the comments section below each article. This allows everyone to benefit from the conversation.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"spacing":{"padding":{"top":"12px","bottom":"12px","left":"24px","right":"24px"}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" style="padding-top:12px;padding-right:24px;padding-bottom:12px;padding-left:24px">Visit Blog Page</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.4rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"15px"}}}} -->
<h3 class="wp-block-heading has-text-align-center" style="margin-bottom:15px;font-size:1.4rem;font-weight:700">Want to write for us?</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.95rem","lineHeight":"1.7"},"spacing":{"margin":{"bottom":"25px"}}}} -->
<p class="has-text-align-center" style="margin-bottom:25px;font-size:0.95rem;line-height:1.7">We\'re always welcoming expert contributors. Please review our submission guidelines and learn how to become a contributor.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"spacing":{"padding":{"top":"12px","bottom":"12px","left":"24px","right":"24px"}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" style="padding-top:12px;padding-right:24px;padding-bottom:12px;padding-left:24px">Become a Contributor</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontSize":"1.4rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"15px"}}}} -->
<h3 class="wp-block-heading has-text-align-center" style="margin-bottom:15px;font-size:1.4rem;font-weight:700">Interested in advertising?</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.95rem","lineHeight":"1.7"},"spacing":{"margin":{"bottom":"25px"}}}} -->
<p class="has-text-align-center" style="margin-bottom:25px;font-size:0.95rem;line-height:1.7">For advertising and partnership inquiries, please use the form below and select "Advertising & Partnerships" from the subject menu.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"spacing":{"padding":{"top":"12px","bottom":"12px","left":"24px","right":"24px"}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" style="padding-top:12px;padding-right:24px;padding-bottom:12px;padding-left:24px">Fill Out the Form</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:columns {"style":{"spacing":{"margin":{"top":"-60px","bottom":"80px"}}}} -->
<div class="wp-block-columns" style="margin-top:-60px;margin-bottom:80px"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"2rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"30px"}}}} -->
<h2 class="wp-block-heading" style="margin-bottom:30px;font-size:2rem;font-weight:700">Send Us a Message</h2>
<!-- /wp:heading -->

<!-- wp:html -->
<form id="contactForm" action="#" method="post">
<div class="form-group" style="margin-bottom: 25px;">
<label for="name" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Full Name *</label>
<input type="text" id="name" name="name" required placeholder="John Smith" style="width: 100%; padding: 15px; border: 2px solid var(--border-color); border-radius: 6px; font-size: 1rem; font-family: inherit; transition: border-color 0.3s;">
</div>

<div class="form-group" style="margin-bottom: 25px;">
<label for="email" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Email Address *</label>
<input type="email" id="email" name="email" required placeholder="john@example.com" style="width: 100%; padding: 15px; border: 2px solid var(--border-color); border-radius: 6px; font-size: 1rem; font-family: inherit; transition: border-color 0.3s;">
</div>

<div class="form-group" style="margin-bottom: 25px;">
<label for="subject" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Subject *</label>
<select id="subject" name="subject" required style="width: 100%; padding: 15px; border: 2px solid var(--border-color); border-radius: 6px; font-size: 1rem; font-family: inherit; transition: border-color 0.3s;">
<option value="">Select a subject</option>
<option value="general">General Inquiry</option>
<option value="advertising">Advertising & Partnerships</option>
<option value="technical">Report a Technical Issue</option>
<option value="feedback">Feedback & Suggestions</option>
</select>
</div>

<div class="form-group" style="margin-bottom: 25px;">
<label for="message" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Message *</label>
<textarea id="message" name="message" required placeholder="Tell us how we can help you..." style="width: 100%; padding: 15px; border: 2px solid var(--border-color); border-radius: 6px; font-size: 1rem; font-family: inherit; transition: border-color 0.3s; resize: vertical; min-height: 150px;"></textarea>
</div>

<button type="submit" class="submit-btn" style="background: var(--accent-secondary); color: #fff; padding: 15px 40px; border: none; border-radius: 6px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s; width: 100%;">Send Message</button>
</form>
<!-- /wp:html --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"2rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"30px"}}}} -->
<h2 class="wp-block-heading" style="margin-bottom:30px;font-size:2rem;font-weight:700">Contact Information</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.2rem","fontWeight":"700"},"spacing":{"margin":{"bottom":"10px"}}}} -->
<h3 class="wp-block-heading" style="margin-bottom:10px;font-size:1.2rem;font-weight:700">Email Us</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>For general inquiries:<br><a href="mailto:info@smeinsights.com">info@smeinsights.com</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>For editorial submissions:<br><a href="mailto:editor@smeinsights.com">editor@smeinsights.com</a></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.2rem","fontWeight":"700"},"spacing":{"margin":{"top":"30px","bottom":"10px"}}}} -->
<h3 class="wp-block-heading" style="margin-top:30px;margin-bottom:10px;font-size:1.2rem;font-weight:700">Call Us</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Business Hours: Monday - Friday<br>9:00 AM - 5:00 PM EST</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Phone: <a href="tel:+11234567890">+1 (123) 456-7890</a></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.2rem","fontWeight":"700"},"spacing":{"margin":{"top":"30px","bottom":"10px"}}}} -->
<h3 class="wp-block-heading" style="margin-top:30px;margin-bottom:10px;font-size:1.2rem;font-weight:700">Visit Us</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>SME Insights Headquarters<br>123 Business Road<br>Dubai, UAE</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->',
			
			'privacy-policy' => '<!-- wp:heading -->
<h2>Privacy Policy</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>At SME Insights, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your personal information when you visit our website.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Information We Collect</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We collect information that you provide directly to us, such as when you subscribe to our newsletter, submit a contact form, or interact with our website.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>How We Use Your Information</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We use the information we collect to provide, maintain, and improve our services, send you newsletters and updates, and respond to your inquiries.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Data Security</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
<!-- /wp:paragraph -->',
			
			'terms-of-service' => '<!-- wp:heading -->
<h2>Terms of Service</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>By accessing and using SME Insights, you agree to be bound by these Terms of Service. Please read them carefully before using our website.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Use of Website</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>You may use our website for lawful purposes only. You agree not to use the website in any way that violates any applicable laws or regulations.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Intellectual Property</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>All content on this website, including text, graphics, logos, and images, is the property of SME Insights and is protected by copyright and other intellectual property laws.</p>
<!-- /wp:paragraph -->',
			
			'disclaimer' => '<!-- wp:heading -->
<h2>Disclaimer</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The information provided on SME Insights is for general informational purposes only. While we strive to provide accurate and up-to-date information, we make no representations or warranties of any kind, express or implied, about the completeness, accuracy, reliability, or suitability of the information.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Not Professional Advice</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The content on this website is not intended to be a substitute for professional advice. Always seek the advice of qualified professionals regarding any business, financial, legal, or other matters.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Limitation of Liability</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>SME Insights shall not be liable for any loss or damage arising from the use of information on this website.</p>
<!-- /wp:paragraph -->',
			
			'advertise-with-us' => '<!-- wp:heading -->
<h2>Advertise With Us</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Reach thousands of small business owners and decision-makers by advertising with SME Insights. We offer various advertising opportunities to help you connect with our engaged audience.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Advertising Options</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li>Banner advertisements</li>
<li>Sponsored content</li>
<li>Newsletter sponsorships</li>
<li>Featured listings</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>Contact Our Advertising Team</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>For advertising inquiries, please contact us at ads@smeinsights.com or use our contact form.</p>
<!-- /wp:paragraph -->',
			
			'become-contributor' => '<!-- wp:heading -->
<h2>Become a Contributor</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Share your expertise and insights with our community of small business owners. We welcome contributions from industry experts, successful entrepreneurs, and business professionals.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Why Contribute?</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li>Reach a targeted audience of small business owners</li>
<li>Build your professional reputation</li>
<li>Share your knowledge and expertise</li>
<li>Get published on a trusted platform</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>How to Submit</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>To submit an article or pitch an idea, please contact us at contributors@smeinsights.com with your proposal or completed article.</p>
<!-- /wp:paragraph -->',
			
			'ai-in-business' => '<!-- wp:heading -->
<h2>AI in Business</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Explore how artificial intelligence is transforming small businesses. From automation to customer service, discover the latest AI tools and strategies that can help your business grow.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Latest Articles</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Stay updated with the latest insights on AI applications in small business operations, marketing, and customer engagement.</p>
<!-- /wp:paragraph -->',
			
			'ecommerce-trends' => '<!-- wp:heading -->
<h2>E-commerce Trends</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Stay ahead of the curve with the latest e-commerce trends and strategies. Learn about new technologies, marketing tactics, and customer experience innovations that are shaping online retail.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Latest Articles</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Discover the trends that are driving e-commerce success and how you can implement them in your business.</p>
<!-- /wp:paragraph -->',
			
			'startup-funding' => '<!-- wp:heading -->
<h2>Startup Funding</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Navigate the world of startup funding with expert guidance. Learn about different funding options, from bootstrapping to venture capital, and find the right path for your startup.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Latest Articles</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Get insights on securing funding, understanding investor expectations, and building a compelling pitch for your startup.</p>
<!-- /wp:paragraph -->',
			
			'green-economy' => '<!-- wp:heading -->
<h2>Green Economy</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Discover how small businesses can contribute to and benefit from the green economy. Learn about sustainable practices, eco-friendly technologies, and green business opportunities.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Latest Articles</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Explore sustainable business practices, renewable energy solutions, and how going green can drive business growth.</p>
<!-- /wp:paragraph -->',
			
			'remote-work' => '<!-- wp:heading -->
<h2>Remote Work</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Master the art of remote work and distributed teams. Learn best practices for managing remote employees, building team culture, and maintaining productivity in a virtual environment.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Latest Articles</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Get tips on remote team management, collaboration tools, and strategies for building a successful remote work culture.</p>
<!-- /wp:paragraph -->',
		);
		
		return isset( $contents[ $page_type ] ) ? $contents[ $page_type ] : '';
	}
	
	/**
	 * Create categories and tags
	 */
	private function create_categories_and_tags() {
		// Update old category names to new ones in database
		$this->update_old_category_names();
		
		// Main Categories
		$main_categories = array(
			'Finance' => array(
				'description' => 'Your comprehensive guide to managing your company\'s finances, from daily budgeting to securing major investments.',
				'color' => '#065f46',
				'icon' => '💰',
			),
			'Marketing' => array(
				'description' => 'Proven strategies to attract new customers, build a strong brand, and significantly increase your sales.',
				'color' => '#ea580c',
				'icon' => '📢',
			),
			'Technology' => array(
				'description' => 'The latest tools, software, and tech trends you need to automate your operations and boost productivity.',
				'color' => '#2563eb',
				'icon' => '⚙️',
			),
			'Growth' => array(
				'description' => 'Action plans and strategies to scale your business, build a strong team, and successfully enter new markets.',
				'color' => '#0891b2',
				'icon' => '📈',
			),
			'Strategy' => array(
				'description' => 'Deep analyses of market trends and expert advice to legally protect your business and make long-term strategic decisions.',
				'color' => '#7c2d12',
				'icon' => '⚖️',
			),
		);
		
		foreach ( $main_categories as $name => $data ) {
			$term = term_exists( $name, 'main_category' );
			if ( ! $term ) {
				// Only create if it doesn't exist
				$term = wp_insert_term( $name, 'main_category', array(
					'description' => $data['description'],
				) );
			} else {
				// Term exists - get term ID
				$term_id = is_array( $term ) ? $term['term_id'] : $term;
				$term = array( 'term_id' => $term_id );
				
				// Only update description if it's empty (preserve user edits)
				$existing_description = term_description( $term_id, 'main_category' );
				if ( empty( trim( $existing_description ) ) ) {
					wp_update_term( $term_id, 'main_category', array(
						'description' => $data['description'],
					) );
				}
			}
			
			if ( ! is_wp_error( $term ) && isset( $term['term_id'] ) ) {
				// Only update meta if not already set (preserve user edits)
				$existing_color = get_term_meta( $term['term_id'], '_sme_category_color', true );
				$existing_icon = get_term_meta( $term['term_id'], '_sme_category_icon', true );
				
				if ( empty( $existing_color ) ) {
					update_term_meta( $term['term_id'], '_sme_category_color', $data['color'] );
				}
				if ( empty( $existing_icon ) ) {
					update_term_meta( $term['term_id'], '_sme_category_icon', $data['icon'] );
				}
			}
		}
		
		// Article Tags (Niche Topics)
		$niche_tags = array(
			'ai-in-business',
			'ecommerce-trends',
			'startup-funding',
			'green-economy',
			'remote-work',
			'ai',
			'apple',
			'computing',
			'green',
			'nasa',
			'rally',
		);
		
		foreach ( $niche_tags as $tag ) {
			$term = term_exists( $tag, 'article_tag' );
			if ( ! $term ) {
				wp_insert_term( ucwords( str_replace( '-', ' ', $tag ) ), 'article_tag', array(
					'slug' => $tag,
				) );
			}
		}
	}
	
	/**
	 * Update old category names to new simplified names in database
	 */
	private function update_old_category_names() {
		$category_updates = array(
			'Finance & Funding' => 'Finance',
			'Marketing & Sales' => 'Marketing',
			'Technology & Tools' => 'Technology',
			'Growth & Scaling' => 'Growth',
			'Growth & Scal' => 'Growth',
			'Growth & Scale' => 'Growth',
			'Strategy & Legal' => 'Strategy',
		);
		
		foreach ( $category_updates as $old_name => $new_name ) {
			$old_term = get_term_by( 'name', $old_name, 'main_category' );
			if ( $old_term && ! is_wp_error( $old_term ) ) {
				// Check if new name already exists
				$new_term = get_term_by( 'name', $new_name, 'main_category' );
				
				if ( $new_term && ! is_wp_error( $new_term ) && $new_term->term_id !== $old_term->term_id ) {
					// New term exists and is different - merge old into new
					// Update all posts to use new term (limit to 1000 posts per batch for performance)
					$offset = 0;
					$batch_size = 1000;
					$max_iterations = 1000; // Safety limit (1,000,000 posts max)
					$iteration_count = 0;
					
					do {
						$iteration_count++;
						
						// Safety check to prevent infinite loops
						if ( $iteration_count > $max_iterations ) {
							break;
						}
						
						$posts = get_posts( array(
							'post_type' => 'post',
							'posts_per_page' => $batch_size,
							'offset' => $offset,
							'tax_query' => array(
								array(
									'taxonomy' => 'main_category',
									'field' => 'term_id',
									'terms' => $old_term->term_id,
								),
							),
							'fields' => 'ids',
						) );
						
						if ( ! empty( $posts ) ) {
							foreach ( $posts as $post_id ) {
								wp_set_object_terms( $post_id, $new_term->term_id, 'main_category', true );
								wp_remove_object_terms( $post_id, $old_term->term_id, 'main_category' );
							}
							$offset += $batch_size;
						} else {
							break;
						}
					} while ( count( $posts ) === $batch_size );
					
					// Delete old term
					wp_delete_term( $old_term->term_id, 'main_category' );
				} else {
					// Update old term name to new name
					wp_update_term( $old_term->term_id, 'main_category', array(
						'name' => $new_name,
						'slug' => sanitize_title( $new_name ),
					) );
				}
			}
		}
	}
	
	/**
	 * Create all posts with content
	 */
	private function create_all_posts() {
		$posts = $this->get_posts_data();
		
		foreach ( $posts as $post_data ) {
			$this->create_post_with_image( $post_data );
		}
	}
	
	/**
	 * Get posts data from HTML files
	 */
	private function get_posts_data() {
		return array(
			// Featured Posts from Homepage - Hero Slider
			array(
				'title' => 'Essential Business Strategies Every Small Business Owner Should Know in 2024',
				'content' => $this->get_full_article_content( 'Essential Business Strategies' ),
				'excerpt' => 'Discover the key business strategies that successful small business owners are using in 2024 to grow and scale their operations.',
				'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=410&fit=crop',
				'date' => '2024-03-20',
				'category' => 'Strategy',
				'tags' => array( 'business-strategy', 'growth' ),
				'featured' => true,
			),
			array(
				'title' => 'Financial Planning Guide: How to Manage Your Small Business Budget Effectively',
				'content' => $this->get_full_article_content( 'Financial Planning Guide' ),
				'excerpt' => 'Learn how to create and manage a budget that works for your small business. Get expert tips on cash flow management and financial planning.',
				'image' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=410&fit=crop',
				'date' => '2024-03-19',
				'category' => 'Finance',
				'tags' => array( 'finance', 'budgeting' ),
				'featured' => true,
			),
			array(
				'title' => 'Digital Tools That Can Transform Your Small Business Operations',
				'content' => $this->get_full_article_content( 'Digital Tools' ),
				'excerpt' => 'Explore the latest digital tools and technologies that can help streamline your business operations and increase productivity.',
				'image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=410&fit=crop',
				'date' => '2024-03-18',
				'category' => 'Technology',
				'tags' => array( 'technology', 'tools' ),
				'featured' => true,
			),
			// Trending News Posts
			array(
				'title' => 'Social Media Marketing Tips for Small Businesses in 2024',
				'content' => $this->get_full_article_content( 'Social Media Marketing' ),
				'excerpt' => 'Learn effective social media marketing strategies that can help your small business reach more customers and grow your online presence.',
				'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=150&h=100&fit=crop',
				'date' => '2024-03-17',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'social-media' ),
			),
			// Finance Category Posts
			array(
				'title' => 'New Government Grants Available for Small Businesses: How to Apply',
				'content' => $this->get_government_grants_content(),
				'excerpt' => 'Discover the latest government grants and funding opportunities available for small businesses. Learn how to apply and maximize your chances of approval.',
				'image' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=600&h=400&fit=crop',
				'date' => '2024-01-15',
				'category' => 'Finance',
				'tags' => array( 'finance', 'funding', 'grants', 'startup-funding' ),
				'featured' => true,
				'breaking' => true,
			),
			array(
				'title' => 'Small Business Loans: Complete Guide to Funding Options',
				'content' => $this->get_full_article_content( 'Business Loans' ),
				'excerpt' => 'Explore various loan options available for small businesses, from traditional bank loans to alternative financing solutions.',
				'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&h=220&fit=crop',
				'date' => '2024-01-09',
				'category' => 'Finance',
				'tags' => array( 'finance', 'loans', 'startup-funding' ),
			),
			array(
				'title' => 'Small Business Tax Changes: What You Need to Know for 2024',
				'content' => $this->get_full_article_content( 'Tax Changes' ),
				'excerpt' => 'Stay informed about the latest tax regulations and changes that affect small businesses. Get expert advice on how to prepare and optimize your tax strategy.',
				'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&h=220&fit=crop',
				'date' => '2024-01-13',
				'category' => 'Finance',
				'tags' => array( 'finance', 'tax' ),
			),
			array(
				'title' => 'Tax Planning Strategies for Small Businesses',
				'content' => $this->get_full_article_content( 'Tax Planning' ),
				'excerpt' => 'Learn effective tax planning strategies that can help your small business save money and stay compliant with tax regulations.',
				'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=150&h=100&fit=crop',
				'date' => '2024-03-18',
				'category' => 'Finance',
				'tags' => array( 'finance', 'tax' ),
			),
			array(
				'title' => 'Budget Management Tips for Growing Companies',
				'content' => $this->get_full_article_content( 'Budget Management' ),
				'excerpt' => 'Discover practical budget management tips that can help growing companies maintain financial stability while scaling operations.',
				'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=150&h=100&fit=crop',
				'date' => '2024-03-17',
				'category' => 'Finance',
				'tags' => array( 'finance', 'budgeting' ),
			),
			array(
				'title' => 'Investment Strategies for Small Business Growth',
				'content' => $this->get_full_article_content( 'Investment Strategies' ),
				'excerpt' => 'Learn how to make smart investment decisions that fuel your small business growth while managing financial risks effectively.',
				'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600&h=400&fit=crop',
				'date' => '2024-03-19',
				'category' => 'Finance',
				'tags' => array( 'finance', 'investment', 'startup-funding' ),
			),
			array(
				'title' => 'Cash Flow Management: Essential Tips for Small Business Owners',
				'content' => $this->get_full_article_content( 'Cash Flow Management' ),
				'excerpt' => 'Master the art of cash flow management with proven strategies to keep your business financially healthy and prepared for growth opportunities.',
				'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&h=400&fit=crop',
				'date' => '2024-03-20',
				'category' => 'Finance',
				'tags' => array( 'finance', 'cash-flow', 'financial-planning' ),
			),
			// Marketing Category Posts
			array(
				'title' => 'Social Media Marketing Strategies for Small Businesses in 2024',
				'content' => $this->get_full_article_content( 'Social Media Marketing Strategies' ),
				'excerpt' => 'Explore how small and medium enterprises are leveraging digital tools to stay competitive and grow their businesses in today\'s market.',
				'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop',
				'date' => '2024-01-14',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'social-media' ),
				'featured' => true,
			),
			array(
				'title' => 'E-commerce Growth: SMEs See 40% Increase in Online Sales',
				'content' => $this->get_full_article_content( 'E-commerce Growth' ),
				'excerpt' => 'Small businesses are experiencing significant growth in online sales. Learn about the strategies and tools that are driving this success.',
				'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&h=400&fit=crop',
				'date' => '2024-01-12',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'ecommerce', 'ecommerce-trends' ),
			),
			array(
				'title' => 'Building a Strong Brand Identity',
				'content' => $this->get_full_article_content( 'Brand Identity' ),
				'excerpt' => 'Learn how to build a strong brand identity that resonates with your target audience and sets your business apart from competitors.',
				'image' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=150&h=100&fit=crop',
				'date' => '2024-03-15',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'branding' ),
			),
			array(
				'title' => 'Customer Retention Strategies That Work',
				'content' => $this->get_full_article_content( 'Customer Retention' ),
				'excerpt' => 'Discover proven customer retention strategies that can help your small business build long-term relationships and increase customer lifetime value.',
				'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop',
				'date' => '2024-03-14',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'customer-retention' ),
			),
			array(
				'title' => 'Content Marketing Strategies for Small Businesses',
				'content' => $this->get_full_article_content( 'Content Marketing Strategies' ),
				'excerpt' => 'Learn how to create compelling content that attracts customers, builds brand awareness, and drives sales for your small business.',
				'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop',
				'date' => '2024-03-19',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'content-marketing', 'ecommerce-trends' ),
			),
			array(
				'title' => 'Email Marketing Best Practices for Small Business Success',
				'content' => $this->get_full_article_content( 'Email Marketing Best Practices' ),
				'excerpt' => 'Master email marketing with proven strategies that help small businesses connect with customers, increase engagement, and boost sales.',
				'image' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=600&h=400&fit=crop',
				'date' => '2024-03-20',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'email-marketing', 'customer-engagement' ),
			),
			// Technology Category Posts
			array(
				'title' => 'SME Digital Transformation: How Small Businesses Are Adapting to New Technologies',
				'content' => $this->get_full_article_content( 'Digital Transformation' ),
				'excerpt' => 'Explore how small and medium enterprises are leveraging digital tools to stay competitive and grow their businesses in today\'s market.',
				'image' => 'https://images.unsplash.com/photo-1556761175-b3da737b1109?w=600&h=400&fit=crop',
				'date' => '2024-01-14',
				'category' => 'Technology',
				'tags' => array( 'technology', 'digital-transformation', 'ai-in-business' ),
				'breaking' => true,
			),
			array(
				'title' => 'Digital Tools That Can Transform Your Small Business Operations',
				'content' => $this->get_full_article_content( 'Digital Tools Transformation' ),
				'excerpt' => 'Discover essential digital tools and software that can revolutionize how your small business operates and competes in today\'s market.',
				'image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=600&h=400&fit=crop',
				'date' => '2024-01-13',
				'category' => 'Technology',
				'tags' => array( 'technology', 'tools' ),
				'featured' => true,
			),
			array(
				'title' => 'Cybersecurity Essentials for Small Businesses: Protect Your Digital Assets',
				'content' => $this->get_full_article_content( 'Cybersecurity Essentials' ),
				'excerpt' => 'Learn essential cybersecurity practices to protect your small business from cyber threats and safeguard your digital assets.',
				'image' => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=600&h=400&fit=crop',
				'date' => '2024-02-10',
				'category' => 'Technology',
				'tags' => array( 'technology', 'cybersecurity' ),
			),
			array(
				'title' => 'Cloud Computing for SMEs: Benefits and Implementation Guide',
				'content' => $this->get_full_article_content( 'Cloud Computing for SMEs' ),
				'excerpt' => 'Discover how cloud computing can improve efficiency, reduce costs, and scale your small business operations effectively.',
				'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&h=400&fit=crop',
				'date' => '2024-02-15',
				'category' => 'Technology',
				'tags' => array( 'technology', 'cloud-computing' ),
			),
			array(
				'title' => 'AI Tools for Small Business: Automate and Scale Your Operations',
				'content' => $this->get_full_article_content( 'AI Tools for Small Business' ),
				'excerpt' => 'Explore how artificial intelligence tools can help small businesses automate tasks, improve customer service, and drive growth.',
				'image' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600&h=400&fit=crop',
				'date' => '2024-03-19',
				'category' => 'Technology',
				'tags' => array( 'technology', 'ai-in-business', 'automation' ),
			),
			array(
				'title' => 'Mobile Apps for Small Business Management: Top Tools to Consider',
				'content' => $this->get_full_article_content( 'Mobile Apps for Business Management' ),
				'excerpt' => 'Discover the best mobile applications that can help you manage your small business on the go, from accounting to customer relations.',
				'image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=600&h=400&fit=crop',
				'date' => '2024-03-20',
				'category' => 'Technology',
				'tags' => array( 'technology', 'mobile-apps', 'remote-work' ),
			),
			// Growth Category Posts
			array(
				'title' => 'How to Build a Sustainable Business Model for Long-Term Success',
				'content' => $this->get_full_article_content( 'Sustainable Business Model' ),
				'excerpt' => 'Learn how to build a sustainable business model that ensures long-term success and growth for your company.',
				'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=600&h=400&fit=crop',
				'date' => '2024-03-18',
				'category' => 'Growth',
				'tags' => array( 'growth', 'sustainability', 'green-economy' ),
			),
			array(
				'title' => 'Scaling Your Business: Strategies for Sustainable Growth',
				'content' => $this->get_full_article_content( 'Scaling Your Business' ),
				'excerpt' => 'Discover proven strategies for scaling your business without compromising quality or customer satisfaction.',
				'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=400&fit=crop',
				'date' => '2024-02-20',
				'category' => 'Growth',
				'tags' => array( 'growth', 'scaling' ),
			),
			array(
				'title' => 'Team Building and Leadership: Growing Your Business Through People',
				'content' => $this->get_full_article_content( 'Team Building and Leadership' ),
				'excerpt' => 'Learn how to build and lead effective teams that drive business growth and create a positive workplace culture.',
				'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&h=400&fit=crop',
				'date' => '2024-02-25',
				'category' => 'Growth',
				'tags' => array( 'growth', 'leadership', 'team-building' ),
			),
			array(
				'title' => 'Productivity Hacks for Growing Businesses: Maximize Your Efficiency',
				'content' => $this->get_full_article_content( 'Productivity Hacks' ),
				'excerpt' => 'Discover practical productivity strategies and tools that can help your growing business operate more efficiently.',
				'image' => 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=600&h=400&fit=crop',
				'date' => '2024-03-01',
				'category' => 'Growth',
				'tags' => array( 'growth', 'productivity' ),
			),
			array(
				'title' => 'Expanding Your Business: When and How to Open New Locations',
				'content' => $this->get_full_article_content( 'Expanding Your Business' ),
				'excerpt' => 'Learn the strategic considerations and practical steps for expanding your business to new locations and markets.',
				'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=400&fit=crop',
				'date' => '2024-03-19',
				'category' => 'Growth',
				'tags' => array( 'growth', 'expansion', 'scaling' ),
			),
			array(
				'title' => 'Building Strategic Partnerships for Business Growth',
				'content' => $this->get_full_article_content( 'Strategic Partnerships' ),
				'excerpt' => 'Discover how strategic partnerships can accelerate your business growth and open new opportunities for collaboration and expansion.',
				'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&h=400&fit=crop',
				'date' => '2024-03-20',
				'category' => 'Growth',
				'tags' => array( 'growth', 'partnerships', 'networking' ),
			),
			// Strategy Category Posts
			array(
				'title' => 'Market Analysis: Understanding Your Competitive Landscape',
				'content' => $this->get_full_article_content( 'Market Analysis' ),
				'excerpt' => 'Learn how to conduct comprehensive market analysis to understand your competitive landscape and identify growth opportunities.',
				'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=400&fit=crop',
				'date' => '2024-03-16',
				'category' => 'Strategy',
				'tags' => array( 'strategy', 'market-analysis' ),
			),
			array(
				'title' => 'Business Planning: Creating a Roadmap for Success',
				'content' => $this->get_full_article_content( 'Business Planning' ),
				'excerpt' => 'Learn how to create a comprehensive business plan that guides your company toward long-term success and growth.',
				'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=600&h=400&fit=crop',
				'date' => '2024-02-28',
				'category' => 'Strategy',
				'tags' => array( 'strategy', 'business-planning' ),
			),
			array(
				'title' => 'Intellectual Property Protection for Small Businesses',
				'content' => $this->get_full_article_content( 'Intellectual Property Protection' ),
				'excerpt' => 'Understand how to protect your business ideas, trademarks, and creative works through proper intellectual property strategies.',
				'image' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&h=400&fit=crop',
				'date' => '2024-03-05',
				'category' => 'Strategy',
				'tags' => array( 'strategy', 'legal', 'intellectual-property' ),
			),
			array(
				'title' => 'Risk Management Strategies for Small Businesses',
				'content' => $this->get_full_article_content( 'Risk Management Strategies' ),
				'excerpt' => 'Learn how to identify, assess, and mitigate risks that could impact your small business operations and growth.',
				'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=600&h=400&fit=crop',
				'date' => '2024-03-10',
				'category' => 'Strategy',
				'tags' => array( 'strategy', 'risk-management' ),
			),
			array(
				'title' => 'Legal Compliance Guide for Small Businesses: Stay Protected',
				'content' => $this->get_full_article_content( 'Legal Compliance Guide' ),
				'excerpt' => 'Navigate the complex world of business regulations and ensure your small business stays compliant with all legal requirements.',
				'image' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&h=400&fit=crop',
				'date' => '2024-03-19',
				'category' => 'Strategy',
				'tags' => array( 'strategy', 'legal', 'compliance' ),
			),
			array(
				'title' => 'Competitive Analysis: How to Stay Ahead of Your Competitors',
				'content' => $this->get_full_article_content( 'Competitive Analysis' ),
				'excerpt' => 'Learn how to conduct effective competitive analysis to understand your market position and develop winning strategies.',
				'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=400&fit=crop',
				'date' => '2024-03-20',
				'category' => 'Strategy',
				'tags' => array( 'strategy', 'competitive-analysis', 'market-research' ),
			),
			// Growth Posts with AI, Computing, Green tags
			array(
				'title' => 'AI-Powered Growth Strategies: How Artificial Intelligence Can Scale Your Business',
				'content' => $this->get_full_article_content( 'AI-Powered Growth Strategies' ),
				'excerpt' => 'Discover how artificial intelligence can transform your business growth strategy, from automated customer service to predictive analytics.',
				'image' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600&h=400&fit=crop',
				'date' => '2024-03-21',
				'category' => 'Growth',
				'tags' => array( 'growth', 'ai', 'ai-in-business', 'scaling' ),
			),
			array(
				'title' => 'Cloud Computing Solutions for Business Expansion: Scaling Your Infrastructure',
				'content' => $this->get_full_article_content( 'Cloud Computing Solutions for Business Expansion' ),
				'excerpt' => 'Learn how cloud computing can help your business scale efficiently, reduce costs, and support rapid growth without infrastructure limitations.',
				'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&h=400&fit=crop',
				'date' => '2024-03-22',
				'category' => 'Growth',
				'tags' => array( 'growth', 'computing', 'cloud-computing', 'scaling' ),
			),
			array(
				'title' => 'Green Business Growth: Sustainable Scaling Strategies for Modern Companies',
				'content' => $this->get_full_article_content( 'Green Business Growth' ),
				'excerpt' => 'Explore how sustainable business practices can drive growth while reducing environmental impact. Learn green scaling strategies that benefit both your business and the planet.',
				'image' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=600&h=400&fit=crop',
				'date' => '2024-03-23',
				'category' => 'Growth',
				'tags' => array( 'growth', 'green', 'green-economy', 'sustainability' ),
			),
			array(
				'title' => 'Scaling with AI: How Machine Learning Can Accelerate Business Growth',
				'content' => $this->get_full_article_content( 'Scaling with AI' ),
				'excerpt' => 'Understand how machine learning and AI technologies can help your business scale faster, make data-driven decisions, and automate growth processes.',
				'image' => 'https://images.unsplash.com/photo-1555255707-c07966088b7b?w=600&h=400&fit=crop',
				'date' => '2024-03-24',
				'category' => 'Growth',
				'tags' => array( 'growth', 'ai', 'ai-in-business', 'machine-learning' ),
			),
			// Marketing Posts with Apple, NASA, Rally tags
			array(
				'title' => 'Apple Marketing Strategies: Lessons from the Tech Giant for Small Businesses',
				'content' => $this->get_full_article_content( 'Apple Marketing Strategies' ),
				'excerpt' => 'Learn valuable marketing lessons from Apple\'s success. Discover how to create compelling brand stories, build customer loyalty, and market your products effectively.',
				'image' => 'https://images.unsplash.com/photo-1573804633927-bfcbcd909acd?w=600&h=400&fit=crop',
				'date' => '2024-03-25',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'apple', 'branding', 'customer-engagement' ),
			),
			array(
				'title' => 'NASA-Inspired Marketing: How Space Innovation Can Transform Your Sales Strategy',
				'content' => $this->get_full_article_content( 'NASA-Inspired Marketing' ),
				'excerpt' => 'Explore how NASA\'s approach to innovation, storytelling, and public engagement can inspire your marketing and sales strategies. Learn to communicate complex ideas simply.',
				'image' => 'https://images.unsplash.com/photo-1446776653964-20c1d3a81b06?w=600&h=400&fit=crop',
				'date' => '2024-03-26',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'nasa', 'innovation', 'storytelling' ),
			),
			array(
				'title' => 'Rally Marketing: Creating Momentum and Excitement in Your Sales Campaigns',
				'content' => $this->get_full_article_content( 'Rally Marketing' ),
				'excerpt' => 'Discover how to create rally-style marketing campaigns that generate excitement, build momentum, and drive sales. Learn from successful rally marketing strategies.',
				'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&h=400&fit=crop',
				'date' => '2024-03-27',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'rally', 'campaigns', 'sales' ),
			),
			array(
				'title' => 'Advanced Computing in Marketing: Leveraging Data Analytics for Sales Growth',
				'content' => $this->get_full_article_content( 'Advanced Computing in Marketing' ),
				'excerpt' => 'Learn how advanced computing and data analytics can transform your marketing efforts, improve customer targeting, and increase sales conversion rates.',
				'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&h=400&fit=crop',
				'date' => '2024-03-28',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'computing', 'data-analytics', 'sales' ),
			),
			array(
				'title' => 'Green Marketing Strategies: How Sustainability Can Drive Sales and Customer Loyalty',
				'content' => $this->get_full_article_content( 'Green Marketing Strategies' ),
				'excerpt' => 'Discover how green marketing can help your business attract eco-conscious customers, build brand loyalty, and increase sales while promoting sustainability.',
				'image' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=600&h=400&fit=crop',
				'date' => '2024-03-29',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'green', 'green-economy', 'sustainability' ),
			),
			array(
				'title' => 'AI in Marketing Automation: How Artificial Intelligence Can Boost Your Sales',
				'content' => $this->get_full_article_content( 'AI in Marketing Automation' ),
				'excerpt' => 'Explore how AI-powered marketing automation can help you personalize customer experiences, optimize campaigns, and significantly increase sales conversion rates.',
				'image' => 'https://images.unsplash.com/photo-1555255707-c07966088b7b?w=600&h=400&fit=crop',
				'date' => '2024-03-30',
				'category' => 'Marketing',
				'tags' => array( 'marketing', 'ai', 'ai-in-business', 'automation' ),
			),
		);
	}
	
	/**
	 * Get full article content
	 */
	private function get_full_article_content( $title ) {
		return "<!-- wp:paragraph -->
<p><strong>This comprehensive guide provides detailed insights and actionable advice about {$title} for small business owners.</strong></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Introduction</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>In today's competitive business landscape, understanding {$title} is crucial for success. This guide will walk you through everything you need to know, from basic concepts to advanced strategies that can transform your business operations.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Why {$title} Matters</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Successful small businesses understand that {$title} is not just a nice-to-have, but a fundamental component of sustainable growth. Companies that prioritize this area see significant improvements in efficiency, customer satisfaction, and overall profitability.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Key Strategies and Best Practices</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Here are the most effective strategies that successful businesses are using:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
<li><strong>Strategy 1:</strong> Implement best practices and industry standards</li>
<li><strong>Strategy 2:</strong> Leverage technology and automation tools</li>
<li><strong>Strategy 3:</strong> Focus on customer experience and satisfaction</li>
<li><strong>Strategy 4:</strong> Continuously measure and optimize performance</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>Common Challenges and Solutions</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Many small businesses face similar challenges when implementing {$title}. The most common issues include limited resources, lack of expertise, and resistance to change. However, with the right approach and tools, these challenges can be overcome.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Real-World Examples</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Let's look at some real-world examples of businesses that have successfully implemented {$title} strategies. These case studies demonstrate the tangible benefits and ROI that can be achieved.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Getting Started</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Ready to get started? Begin by assessing your current situation, identifying areas for improvement, and creating a step-by-step action plan. Remember, small incremental changes often lead to significant long-term results.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Conclusion</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>By following these strategies and best practices, you can significantly improve your business outcomes. Remember, success comes from consistent effort, continuous learning, and adapting to changing market conditions. Start implementing these ideas today and watch your business grow.</p>
<!-- /wp:paragraph -->";
	}
	
	/**
	 * Get government grants article content (full content from single-page.html)
	 */
	private function get_government_grants_content() {
		return "<!-- wp:paragraph -->
<p><strong>The government has announced a new wave of grants specifically designed to support small and medium enterprises (SMEs) across various sectors. This comprehensive guide will walk you through everything you need to know about these opportunities and how to successfully apply.</strong></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 id=\"grant-programs\">Understanding the New Grant Programs</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The latest funding initiative represents one of the most substantial support packages for SMEs in recent years. With a total allocation of over <strong>$500 million</strong>, these grants are aimed at helping small businesses recover, grow, and innovate in an increasingly competitive marketplace.</p>
<!-- /wp:paragraph -->

<!-- wp:quote -->
<blockquote class=\"wp-block-quote\">
<p>\"This grant program represents a game-changing opportunity for small businesses. The key to success is understanding which category aligns best with your business goals and preparing a compelling application that clearly demonstrates impact.\"</p>
</blockquote>
<!-- /wp:quote -->

<!-- wp:paragraph -->
<p>The program is divided into several categories, each targeting specific business needs and sectors. Whether you're looking to expand your operations, invest in new technology, or enhance your workforce skills, there's likely a grant program that fits your requirements.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h3>Key Grant Categories</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li><strong>Innovation and Technology Grants:</strong> Up to $100,000 for businesses investing in digital transformation and technological upgrades</li>
<li><strong>Export Development Grants:</strong> Support for SMEs looking to expand into international markets</li>
<li><strong>Sustainability and Green Business Grants:</strong> Funding for environmentally conscious business initiatives</li>
<li><strong>Workforce Development Grants:</strong> Support for training and upskilling your team</li>
<li><strong>Capital Investment Grants:</strong> Assistance with equipment purchases and facility improvements</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 id=\"eligibility\">Eligibility Requirements</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>To qualify for these grants, your business must meet certain criteria. While specific requirements vary by program, common eligibility factors include:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ol>
<li>Operating as a registered business for at least 12 months</li>
<li>Employing between 1-200 full-time equivalent staff</li>
<li>Having an annual turnover between $50,000 and $50 million</li>
<li>Demonstrating financial viability and a solid business plan</li>
<li>Being current with all tax obligations</li>
</ol>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 id=\"how-to-apply\">How to Apply Successfully</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The application process can seem daunting, but with proper preparation, you can significantly increase your chances of success. Here's a step-by-step approach:</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h3>1. Research and Choose the Right Grant</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Don't apply for every grant available. Instead, focus on programs that align closely with your business goals and needs. Review the eligibility criteria carefully and ensure your business genuinely fits the target profile.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h3>2. Prepare Your Documentation</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Most grant applications require substantial documentation, including:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
<li>Business registration certificates</li>
<li>Financial statements (typically last 2-3 years)</li>
<li>Tax returns and compliance certificates</li>
<li>Detailed business plan</li>
<li>Project proposal and budget</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h3>3. Craft a Compelling Application</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Your application should clearly demonstrate how the grant will benefit your business and contribute to broader economic goals. Be <strong>specific about outcomes</strong>, provide <strong>realistic timelines</strong>, and show <strong>how you'll measure success</strong>.</p>
<!-- /wp:paragraph -->

<!-- wp:quote -->
<blockquote class=\"wp-block-quote\">
<p>\"The most successful grant applications tell a story. They don't just list what you'll do with the money—they explain why it matters, who it will help, and how it will create lasting value.\"</p>
</blockquote>
<!-- /wp:quote -->

<!-- wp:heading -->
<h2 id=\"deadlines\">Important Deadlines</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Applications for the current round close on March 31, 2024. However, it's crucial to note that some grant categories operate on a first-come, first-served basis, so early application is recommended. Processing times typically range from 6-12 weeks after the application deadline.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 id=\"next-steps\">Next Steps</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If you're ready to apply, visit the official government grants portal or contact your local business development center for personalized assistance. Many regions also offer free workshops and one-on-one consultations to help businesses navigate the application process.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Remember, securing a grant is competitive, but with thorough preparation and a strong application, your small business has a genuine opportunity to access valuable funding that could transform your operations and accelerate your growth.</p>
<!-- /wp:paragraph -->";
	}
	
	/**
	 * Create post with featured image
	 */
	private function create_post_with_image( $post_data ) {
		// Check if post already exists using $wpdb (replaces deprecated get_page_by_title)
		global $wpdb;
		
		$existing = $wpdb->get_var( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'post' AND post_status != 'trash' LIMIT 1",
			$post_data['title']
		) );
		
		if ( $existing ) {
			return;
		}
		
		// Create post
		$post_id = wp_insert_post( array(
			'post_title'    => $post_data['title'],
			'post_content'  => $post_data['content'],
			'post_excerpt'  => $post_data['excerpt'],
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_date'     => $post_data['date'] . ' 10:00:00',
		) );
		
		if ( is_wp_error( $post_id ) ) {
			return;
		}
		
		// Set featured image - ensure every post has an image
		if ( ! empty( $post_data['image'] ) ) {
			$this->set_featured_image_from_url( $post_id, $post_data['image'], $post_data['title'] );
		} else {
			// If no image specified, use default image
			$this->set_default_featured_image( $post_id, $post_data['title'] );
		}
		
		// Set category - allow multiple categories per post
		// But ensure every post has at least one category (NO Uncategorized)
		if ( ! empty( $post_data['category'] ) ) {
			$term = get_term_by( 'name', $post_data['category'], 'main_category' );
			if ( $term && ! is_wp_error( $term ) ) {
				// Add category (allow multiple categories)
				wp_set_object_terms( $post_id, array( $term->term_id ), 'main_category', true );
			} else {
				// If category not found, assign to first available category (not Uncategorized)
				$this->assign_default_category( $post_id );
			}
		} else {
			// If no category specified, assign to first available category (not Uncategorized)
			$this->assign_default_category( $post_id );
		}
		
		// Remove Uncategorized category if it exists
		$uncategorized = get_term_by( 'slug', 'uncategorized', 'main_category' );
		if ( $uncategorized && ! is_wp_error( $uncategorized ) ) {
			wp_remove_object_terms( $post_id, $uncategorized->term_id, 'main_category' );
		}
		
		// Set tags
		if ( ! empty( $post_data['tags'] ) ) {
			wp_set_post_terms( $post_id, $post_data['tags'], 'article_tag' );
		}
		
		// Set meta fields
		if ( ! empty( $post_data['featured'] ) ) {
			update_post_meta( $post_id, '_sme_is_featured', '1' );
		}
		
		if ( ! empty( $post_data['breaking'] ) ) {
			update_post_meta( $post_id, 'breaking_news', '1' );
		}
		
		if ( ! empty( $post_data['excerpt'] ) ) {
			update_post_meta( $post_id, '_sme_custom_excerpt', $post_data['excerpt'] );
		}
	}
	
	/**
	 * Assign default category to post if no category is set
	 */
	private function assign_default_category( $post_id ) {
		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return;
		}
		
		$categories = get_terms( array(
			'taxonomy' => 'main_category',
			'hide_empty' => false,
		) );
		
		if ( is_wp_error( $categories ) || empty( $categories ) ) {
			return;
		}
		
		// Exclude Uncategorized category
		$valid_categories = array();
		foreach ( $categories as $category ) {
			if ( $category->slug !== 'uncategorized' && $category->name !== 'Uncategorized' ) {
				$valid_categories[] = $category;
			}
		}
		
		if ( ! empty( $valid_categories ) ) {
			// Get the category with the least posts
			$category_counts = array();
			foreach ( $valid_categories as $category ) {
				$count = $category->count;
				$category_counts[ $category->term_id ] = $count;
			}
			asort( $category_counts );
			$least_used_category_id = key( $category_counts );
			// Add category (allow multiple categories)
			wp_set_object_terms( $post_id, array( $least_used_category_id ), 'main_category', true );
		} else {
			// If no valid categories, use first available (except Uncategorized)
			$first_category = reset( $categories );
			if ( $first_category && $first_category->slug !== 'uncategorized' ) {
				// Add category (allow multiple categories)
				wp_set_object_terms( $post_id, array( $first_category->term_id ), 'main_category', true );
			}
		}
	}
	
	/**
	 * Set default featured image if post doesn't have one
	 */
	private function set_default_featured_image( $post_id, $post_title ) {
		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return;
		}
		
		// Use a default business/technology image
		$default_image_url = 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=410&fit=crop';
		$this->set_featured_image_from_url( $post_id, $default_image_url, $post_title );
	}
	
	/**
	 * Ensure all posts have at least one category
	 */
	private function ensure_posts_have_categories() {
		// Process in batches
		$offset = 0;
		$batch_size = 500;
		$max_iterations = 1000; // Safety limit (500,000 posts max)
		$iteration_count = 0;
		
		do {
			$iteration_count++;
			
			// Safety check to prevent infinite loops
			if ( $iteration_count > $max_iterations ) {
				break;
			}
			
			$all_posts = get_posts( array(
				'post_type' => 'post',
				'posts_per_page' => $batch_size,
				'offset' => $offset,
				'post_status' => 'publish',
				'fields' => 'ids', // Only get IDs for better performance
			) );
			
			if ( ! empty( $all_posts ) ) {
				foreach ( $all_posts as $post_id ) {
					$post_id = absint( $post_id );
					if ( ! $post_id ) {
						continue;
					}
					
					$terms = wp_get_post_terms( $post_id, 'main_category' );
					
					// Check if post has no categories or only has Uncategorized
					$has_valid_category = false;
					if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
						foreach ( $terms as $term ) {
							if ( $term->slug !== 'uncategorized' && $term->name !== 'Uncategorized' ) {
								$has_valid_category = true;
								break;
							}
						}
					}
					
					if ( ! $has_valid_category ) {
						// Remove Uncategorized if it exists
						$uncategorized = get_term_by( 'slug', 'uncategorized', 'main_category' );
						if ( $uncategorized && ! is_wp_error( $uncategorized ) ) {
							wp_remove_object_terms( $post_id, $uncategorized->term_id, 'main_category' );
						}
						// Assign valid category
						$this->assign_default_category( $post_id );
					}
					
					// Get post once and use for both operations
					$post = get_post( $post_id );
					if ( $post ) {
						// Ensure post has featured image
						if ( ! has_post_thumbnail( $post_id ) ) {
							$this->set_default_featured_image( $post_id, $post->post_title );
						}
						
						// Ensure post has tags
						$this->ensure_post_has_tags( $post_id, $post->post_title );
					}
				}
				$offset += $batch_size;
			} else {
				break;
			}
		} while ( count( $all_posts ) === $batch_size );
	}
	
	/**
	 * Ensure post has at least one tag based on content
	 *
	 * @param int    $post_id   Post ID
	 * @param string $post_title Post title
	 */
	private function ensure_post_has_tags( $post_id, $post_title = '' ) {
		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return;
		}
		
		// Get existing tags
		$existing_tags = wp_get_post_terms( $post_id, 'article_tag', array( 'fields' => 'ids' ) );
		if ( is_wp_error( $existing_tags ) ) {
			$existing_tags = array();
		}
		
		// If post already has tags, return
		if ( ! empty( $existing_tags ) ) {
			return;
		}
		
		// Get post content for tag detection
		if ( empty( $post_title ) ) {
			$post = get_post( $post_id );
			if ( ! $post || ! isset( $post->post_title ) ) {
				return;
			}
			if ( $post ) {
				$post_title = $post->post_title;
			}
		}
		
		$post_content = '';
		$post = get_post( $post_id );
		if ( $post ) {
			$post_content = $post->post_content . ' ' . $post->post_title;
		}
		
		// Determine appropriate tags based on content
		$suggested_tags = $this->suggest_tags_for_post( $post_title, $post_content );
		
		if ( ! empty( $suggested_tags ) ) {
			$tag_ids = array();
			foreach ( $suggested_tags as $tag_name ) {
				// Check if tag exists
				$tag = get_term_by( 'name', $tag_name, 'article_tag' );
				if ( ! $tag || is_wp_error( $tag ) ) {
					// Create tag if it doesn't exist
					$tag_result = wp_insert_term( $tag_name, 'article_tag' );
					if ( ! is_wp_error( $tag_result ) ) {
						$tag_id = $tag_result['term_id'];
					} else {
						continue;
					}
				} else {
					$tag_id = $tag->term_id;
				}
				$tag_ids[] = $tag_id;
			}
			
			// Assign tags to post
			if ( ! empty( $tag_ids ) ) {
				wp_set_object_terms( $post_id, $tag_ids, 'article_tag', true );
			}
		}
	}
	
	/**
	 * Suggest tags for a post based on title and content
	 *
	 * @param string $title   Post title
	 * @param string $content Post content
	 * @return array Array of suggested tag names
	 */
	private function suggest_tags_for_post( $title, $content = '' ) {
		$suggested_tags = array();
		$text = strtolower( $title . ' ' . $content );
		
		// Keyword mapping for tags
		$keyword_tag_map = array(
			// Marketing keywords
			'marketing' => 'Marketing',
			'advertising' => 'Marketing',
			'brand' => 'Branding',
			'social media' => 'Social Media',
			'seo' => 'SEO',
			'content marketing' => 'Content Marketing',
			'email marketing' => 'Email Marketing',
			'digital marketing' => 'Digital Marketing',
			'online marketing' => 'Online Marketing',
			
			// Finance keywords
			'finance' => 'Finance',
			'financial' => 'Finance',
			'budget' => 'Budgeting',
			'tax' => 'Tax',
			'investment' => 'Investment',
			'loan' => 'Loans',
			'grant' => 'Grants',
			'funding' => 'Funding',
			'cash flow' => 'Cash Flow',
			'money' => 'Finance',
			
			// Technology keywords
			'technology' => 'Technology',
			'tech' => 'Technology',
			'digital' => 'Digital',
			'ai' => 'Artificial Intelligence',
			'artificial intelligence' => 'Artificial Intelligence',
			'automation' => 'Automation',
			'software' => 'Software',
			'app' => 'Apps',
			'website' => 'Web Development',
			'e-commerce' => 'E-commerce',
			'ecommerce' => 'E-commerce',
			'online' => 'Online Business',
			
			// Strategy keywords
			'strategy' => 'Strategy',
			'strategies' => 'Strategy',
			'planning' => 'Planning',
			'business plan' => 'Business Planning',
			'growth' => 'Growth',
			'scaling' => 'Scaling',
			'expansion' => 'Expansion',
			
			// Operations keywords
			'operations' => 'Operations',
			'management' => 'Management',
			'productivity' => 'Productivity',
			'efficiency' => 'Efficiency',
			'process' => 'Process Improvement',
			
			// Sales keywords
			'sales' => 'Sales',
			'customer' => 'Customer Service',
			'client' => 'Customer Service',
			'retention' => 'Customer Retention',
			
			// General business keywords
			'business' => 'Business',
			'entrepreneur' => 'Entrepreneurship',
			'startup' => 'Startups',
			'small business' => 'Small Business',
			'sme' => 'SME',
		);
		
		// Check for keywords and suggest tags
		foreach ( $keyword_tag_map as $keyword => $tag ) {
			if ( strpos( $text, $keyword ) !== false ) {
				if ( ! in_array( $tag, $suggested_tags ) ) {
					$suggested_tags[] = $tag;
				}
			}
		}
		
		// If no tags found, add generic tags based on category
		if ( empty( $suggested_tags ) ) {
			// Try to get post ID from global or use a workaround
			global $post;
			$post_id = 0;
			if ( isset( $post ) && isset( $post->ID ) ) {
				$post_id = $post->ID;
			}
			
			if ( $post_id ) {
				$categories = wp_get_post_terms( $post_id, 'main_category', array( 'fields' => 'names' ) );
				if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
					// Use category name as tag
					$suggested_tags[] = $categories[0];
				}
			}
			
			// Default generic tags if still empty
			if ( empty( $suggested_tags ) ) {
				$suggested_tags = array( 'Business', 'Tips' );
			}
		}
		
		// Limit to 3 tags max
		return array_slice( $suggested_tags, 0, 3 );
	}
	
	/**
	 * Ensure all categories have at least some posts
	 */
	private function ensure_categories_have_posts() {
		$categories = get_terms( array(
			'taxonomy' => 'main_category',
			'hide_empty' => false,
		) );
		
		if ( empty( $categories ) || is_wp_error( $categories ) ) {
			return;
		}
		
		// Find categories with no posts
		$empty_categories = array();
		foreach ( $categories as $category ) {
			if ( $category->count == 0 ) {
				$empty_categories[] = $category;
			}
		}
		
		// If there are empty categories, redistribute posts
		if ( ! empty( $empty_categories ) ) {
			// Get total post count first (more efficient than loading all posts)
			$total_posts = wp_count_posts( 'post' );
			$total_posts_count = isset( $total_posts->publish ) ? (int) $total_posts->publish : 0;
			
			if ( $total_posts_count === 0 ) {
				return;
			}
			
			// Calculate how many posts each category should have
			$total_categories = count( $categories );
			$posts_per_category = max( 1, floor( $total_posts_count / $total_categories ) );
			
			// Get posts in batches
			$offset = 0;
			$batch_size = 500;
			$post_index = 0;
			
			foreach ( $empty_categories as $empty_category ) {
				$posts_to_assign = min( $posts_per_category, $total_posts_count - $post_index );
				$assigned = 0;
				$category_offset = 0;
				$max_iterations = ceil( $total_posts_count / $batch_size ) * 2; // Safety limit
				$iteration_count = 0;
				
				// Process in batches for this category
				while ( $assigned < $posts_to_assign && $category_offset < $total_posts_count && $iteration_count < $max_iterations ) {
					$iteration_count++;
					
					$batch_posts = get_posts( array(
						'post_type' => 'post',
						'posts_per_page' => min( $batch_size, $posts_to_assign - $assigned ),
						'offset' => $category_offset,
						'post_status' => 'publish',
						'fields' => 'ids', // Only get IDs for better performance
					) );
					
					if ( empty( $batch_posts ) ) {
						break;
					}
					
					foreach ( $batch_posts as $post_id ) {
						if ( $assigned >= $posts_to_assign ) {
							break 2; // Break out of both loops
						}
						
						$current_terms = wp_get_post_terms( $post_id, 'main_category' );
						if ( is_wp_error( $current_terms ) ) {
							$current_terms = array();
						}
						
						// Skip if post already has this category
						$has_category = false;
						if ( ! empty( $current_terms ) && ! is_wp_error( $current_terms ) ) {
							foreach ( $current_terms as $term ) {
								if ( isset( $term->term_id ) && $term->term_id === $empty_category->term_id ) {
									$has_category = true;
									break;
								}
							}
						}
						
						if ( ! $has_category ) {
							// Add empty category to post (keep existing categories too)
							if ( ! empty( $current_terms ) && ! is_wp_error( $current_terms ) ) {
								$term_ids = array();
								foreach ( $current_terms as $term ) {
									if ( isset( $term->term_id ) ) {
										$term_ids[] = $term->term_id;
									}
								}
								$term_ids[] = $empty_category->term_id;
								wp_set_post_terms( $post_id, $term_ids, 'main_category' );
							} else {
								wp_set_post_terms( $post_id, array( $empty_category->term_id ), 'main_category' );
							}
							
							$assigned++;
							$post_index++;
						}
					}
					
					$category_offset += count( $batch_posts );
					
					// If we've assigned enough posts for this category, move to next category
					if ( $assigned >= $posts_to_assign ) {
						break;
					}
					
					// Safety check: if no posts were assigned in this batch, increment offset to avoid infinite loop
					if ( $assigned === 0 && ! empty( $batch_posts ) ) {
						$category_offset += $batch_size;
					}
				}
			}
		}
	}
	
	/**
	 * Set featured image from URL
	 */
	private function set_featured_image_from_url( $post_id, $image_url, $alt_text ) {
		// Skip if image URL is empty
		if ( empty( $image_url ) ) {
			return;
		}
		
		// Check if post already has a featured image with the same URL
		if ( has_post_thumbnail( $post_id ) ) {
			$thumbnail_id = get_post_thumbnail_id( $post_id );
			$current_image_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );
			
			// Normalize URLs for comparison (remove query strings, trailing slashes)
			$current_url_normalized = rtrim( preg_replace( '/\?.*/', '', $current_image_url ), '/' );
			$new_url_normalized = rtrim( preg_replace( '/\?.*/', '', $image_url ), '/' );
			
			// If URLs match, don't re-download
			if ( $current_url_normalized === $new_url_normalized ) {
				return true;
			}
			
			// Delete old thumbnail if URL is different
			delete_post_thumbnail( $post_id );
			wp_delete_attachment( $thumbnail_id, true );
		}
		
		// Check if image already exists in media library
		$existing_attachment_id = sme_get_attachment_by_url( $image_url );
		if ( $existing_attachment_id ) {
			// Use existing attachment instead of downloading again
			set_post_thumbnail( $post_id, $existing_attachment_id );
			update_post_meta( $existing_attachment_id, '_wp_attachment_image_alt', $alt_text );
			return true;
		}
		
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		
		// Set timeout for large images
		$timeout_seconds = 30;
		
		// Download image
		$tmp = download_url( $image_url, $timeout_seconds );
		
		if ( is_wp_error( $tmp ) ) {
			return false;
		}
		
		// Get file extension
		$file_array = array(
			'name'     => sanitize_file_name( basename( parse_url( $image_url, PHP_URL_PATH ) ) ),
			'tmp_name' => $tmp,
		);
		
		// If no extension, add .jpg
		if ( ! pathinfo( $file_array['name'], PATHINFO_EXTENSION ) ) {
			$file_array['name'] .= '.jpg';
		}
		
		// Handle sideload
		$id = media_handle_sideload( $file_array, $post_id );
		
		if ( is_wp_error( $id ) ) {
			// Clean up temporary file
			if ( isset( $file_array['tmp_name'] ) && file_exists( $file_array['tmp_name'] ) ) {
				unlink( $file_array['tmp_name'] );
			}
			return false;
		}
		
		// Set as featured image
		set_post_thumbnail( $post_id, $id );
		
		// Set alt text
		update_post_meta( $id, '_wp_attachment_image_alt', $alt_text );
	}
	
	/**
	 * Create page if not exists
	 */
	private function create_page_if_not_exists( $page_data ) {
		global $wpdb;
		
		// Check if page exists (including in trash)
		$existing_page = $wpdb->get_row( $wpdb->prepare(
			"SELECT ID, post_status FROM {$wpdb->posts} WHERE post_name = %s AND post_type = 'page' LIMIT 1",
			$page_data['slug']
		) );
		
		if ( $existing_page ) {
			// If page is in trash, restore it
			if ( $existing_page->post_status === 'trash' ) {
				wp_untrash_post( $existing_page->ID );
				wp_update_post( array(
					'ID'          => $existing_page->ID,
					'post_status' => 'publish',
				) );
			}
			
			// Update template and content
			update_post_meta( $existing_page->ID, '_wp_page_template', $page_data['template'] );
			
			// Get content if not provided
			$content = isset( $page_data['content'] ) ? $page_data['content'] : '';
			if ( empty( $content ) && isset( $page_data['slug'] ) ) {
				$content = $this->get_page_content( $page_data['slug'] );
			}
			
			// Update page content ONLY if empty
			// This ensures user edits are NEVER overwritten
			// The importer only runs on theme activation, so this is safe
			$current_content = get_post_field( 'post_content', $existing_page->ID );
			if ( empty( trim( $current_content ) ) && ! empty( $content ) ) {
				wp_update_post( array(
					'ID'           => $existing_page->ID,
					'post_content' => $content,
				) );
			}
			
			// If this is the Home page, set it as front page
			if ( isset( $page_data['slug'] ) && $page_data['slug'] === 'home' ) {
				// Set as static front page
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $existing_page->ID );
			}
			
			return;
		}
		
		// Get content if not provided
		$content = isset( $page_data['content'] ) ? $page_data['content'] : '';
		if ( empty( $content ) && isset( $page_data['slug'] ) ) {
			$content = $this->get_page_content( $page_data['slug'] );
		}
		
		$page_id = wp_insert_post( array(
			'post_title'    => $page_data['title'],
			'post_name'     => $page_data['slug'],
			'post_content'  => $content,
			'post_status'   => 'publish',
			'post_type'     => 'page',
			'post_author'   => 1,
		) );
		
		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_post_meta( $page_id, '_wp_page_template', $page_data['template'] );
			
			// If this is the Home page, set it as front page
			if ( isset( $page_data['slug'] ) && $page_data['slug'] === 'home' ) {
				// Set as static front page
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $page_id );
			}
		}
	}
}

