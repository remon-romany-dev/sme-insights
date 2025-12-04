<?php
/**
 * XML Sitemap Generator
 * Generates XML sitemap without requiring plugins
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Sitemap {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_action( 'template_redirect', array( $this, 'generate_sitemap' ) );
		add_action( 'after_switch_theme', array( $this, 'flush_rewrite_rules' ) );
		add_action( 'publish_post', array( $this, 'ping_search_engines' ) );
		add_action( 'publish_page', array( $this, 'ping_search_engines' ) );
	}
	
	public function flush_rewrite_rules() {
		$this->add_rewrite_rules();
		flush_rewrite_rules();
	}
	
	public function add_rewrite_rules() {
		add_rewrite_rule( '^sitemap\.xml$', 'index.php?sitemap=1', 'top' );
		add_rewrite_rule( '^sitemap-posts\.xml$', 'index.php?sitemap=posts', 'top' );
		add_rewrite_rule( '^sitemap-pages\.xml$', 'index.php?sitemap=pages', 'top' );
		add_rewrite_rule( '^sitemap-categories\.xml$', 'index.php?sitemap=categories', 'top' );
	}
	
	public function generate_sitemap() {
		$sitemap_type = get_query_var( 'sitemap' );
		
		if ( ! $sitemap_type ) {
			return;
		}
		
		header( 'Content-Type: application/xml; charset=utf-8' );
		
		if ( $sitemap_type === '1' ) {
			$this->generate_index_sitemap();
		} elseif ( $sitemap_type === 'posts' ) {
			$this->generate_posts_sitemap();
		} elseif ( $sitemap_type === 'pages' ) {
			$this->generate_pages_sitemap();
		} elseif ( $sitemap_type === 'categories' ) {
			$this->generate_categories_sitemap();
		}
		
		exit;
	}
	
	private function generate_index_sitemap() {
		$site_url = home_url();
		
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		
		// Posts sitemap
		$posts_count = wp_count_posts( 'post' )->publish;
		if ( $posts_count > 0 ) {
			echo "\t<sitemap>\n";
			echo "\t\t<loc>" . esc_url( $site_url . '/sitemap-posts.xml' ) . "</loc>\n";
			echo "\t\t<lastmod>" . esc_html( date( 'c' ) ) . "</lastmod>\n";
			echo "\t</sitemap>\n";
		}
		
		// Pages sitemap
		$pages_count = wp_count_posts( 'page' )->publish;
		if ( $pages_count > 0 ) {
			echo "\t<sitemap>\n";
			echo "\t\t<loc>" . esc_url( $site_url . '/sitemap-pages.xml' ) . "</loc>\n";
			echo "\t\t<lastmod>" . esc_html( date( 'c' ) ) . "</lastmod>\n";
			echo "\t</sitemap>\n";
		}
		
		// Categories sitemap
		$categories = get_terms( array(
			'taxonomy'   => 'main_category',
			'hide_empty' => true,
		) );
		if ( ! empty( $categories ) ) {
			echo "\t<sitemap>\n";
			echo "\t\t<loc>" . esc_url( $site_url . '/sitemap-categories.xml' ) . "</loc>\n";
			echo "\t\t<lastmod>" . esc_html( date( 'c' ) ) . "</lastmod>\n";
			echo "\t</sitemap>\n";
		}
		
		echo '</sitemapindex>';
	}
	
	private function generate_posts_sitemap() {
		global $wpdb;
		
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
		
		$offset = 0;
		$posts_per_page = 1000;
		
		while ( true ) {
			$posts = $wpdb->get_results( $wpdb->prepare(
				"SELECT ID, post_modified FROM {$wpdb->posts} 
				WHERE post_type = 'post' 
				AND post_status = 'publish' 
				ORDER BY post_modified DESC 
				LIMIT %d OFFSET %d",
				$posts_per_page,
				$offset
			) );
			
			if ( empty( $posts ) ) {
				break;
			}
			
			foreach ( $posts as $post_data ) {
				$post_id = absint( $post_data->ID );
				$url = get_permalink( $post_id );
				$modified = date( 'c', strtotime( $post_data->post_modified ) );
				$priority = 0.8;
				
				if ( get_post_meta( $post_id, '_sme_is_featured', true ) ) {
					$priority = 0.9;
				}
				
				echo "\t<url>\n";
				echo "\t\t<loc>" . esc_url( $url ) . "</loc>\n";
				echo "\t\t<lastmod>" . esc_html( $modified ) . "</lastmod>\n";
				echo "\t\t<changefreq>weekly</changefreq>\n";
				echo "\t\t<priority>" . esc_html( $priority ) . "</priority>\n";
				
				if ( has_post_thumbnail( $post_id ) ) {
					$image_url = get_the_post_thumbnail_url( $post_id, 'large' );
					$image_title = get_the_title( $post_id );
					$image_alt = get_post_meta( get_post_thumbnail_id( $post_id ), '_wp_attachment_image_alt', true );
					
					echo "\t\t<image:image>\n";
					echo "\t\t\t<image:loc>" . esc_url( $image_url ) . "</image:loc>\n";
					echo "\t\t\t<image:title>" . esc_html( $image_title ) . "</image:title>\n";
					if ( ! empty( $image_alt ) ) {
						echo "\t\t\t<image:caption>" . esc_html( $image_alt ) . "</image:caption>\n";
					}
					echo "\t\t</image:image>\n";
				}
				
				echo "\t</url>\n";
			}
			
			if ( count( $posts ) < $posts_per_page ) {
				break;
			}
			
			$offset += $posts_per_page;
		}
		
		echo '</urlset>';
	}
	
	private function generate_pages_sitemap() {
		global $wpdb;
		
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		
		$offset = 0;
		$posts_per_page = 1000;
		
		while ( true ) {
			$pages = $wpdb->get_results( $wpdb->prepare(
				"SELECT ID, post_modified FROM {$wpdb->posts} 
				WHERE post_type = 'page' 
				AND post_status = 'publish' 
				ORDER BY post_modified DESC 
				LIMIT %d OFFSET %d",
				$posts_per_page,
				$offset
			) );
			
			if ( empty( $pages ) ) {
				break;
			}
			
			foreach ( $pages as $page_data ) {
				$page_id = absint( $page_data->ID );
				$url = get_permalink( $page_id );
				$modified = date( 'c', strtotime( $page_data->post_modified ) );
				
				$priority = ( is_front_page( $page_id ) ) ? 1.0 : 0.7;
				
				echo "\t<url>\n";
				echo "\t\t<loc>" . esc_url( $url ) . "</loc>\n";
				echo "\t\t<lastmod>" . esc_html( $modified ) . "</lastmod>\n";
				echo "\t\t<changefreq>monthly</changefreq>\n";
				echo "\t\t<priority>" . esc_html( $priority ) . "</priority>\n";
				echo "\t</url>\n";
			}
			
			if ( count( $pages ) < $posts_per_page ) {
				break;
			}
			
			$offset += $posts_per_page;
		}
		
		echo '</urlset>';
	}
	
	private function generate_categories_sitemap() {
		$categories = get_terms( array(
			'taxonomy'   => 'main_category',
			'hide_empty' => true,
		) );
		
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		
		foreach ( $categories as $category ) {
			$url = get_term_link( $category );
			
			echo "\t<url>\n";
			echo "\t\t<loc>" . esc_url( $url ) . "</loc>\n";
			echo "\t\t<lastmod>" . esc_html( date( 'c' ) ) . "</lastmod>\n";
			echo "\t\t<changefreq>weekly</changefreq>\n";
			echo "\t\t<priority>0.8</priority>\n";
			echo "\t</url>\n";
		}
		
		echo '</urlset>';
	}
	
	public function ping_search_engines() {
		$sitemap_url = home_url( '/sitemap.xml' );
		
		wp_remote_get( 'https://www.google.com/ping?sitemap=' . urlencode( $sitemap_url ), array(
			'timeout' => 5,
			'blocking' => false,
		) );
		
		wp_remote_get( 'https://www.bing.com/ping?sitemap=' . urlencode( $sitemap_url ), array(
			'timeout' => 5,
			'blocking' => false,
		) );
	}
}


