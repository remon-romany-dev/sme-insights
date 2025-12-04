<?php
/**
 * SEO Optimizer
 * Handles SEO optimization including meta tags, schema markup, and sitemap generation
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_SEO_Optimizer {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Use plugins_loaded for theme independence
		add_action( 'plugins_loaded', array( $this, 'init_hooks' ), 5 );
		add_action( 'after_setup_theme', array( $this, 'init_hooks' ), 999 );
	}
	
	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		add_action( 'wp_head', array( $this, 'output_meta_tags' ), 1 );
		add_action( 'wp_head', array( $this, 'output_og_tags' ), 2 );
		add_action( 'wp_head', array( $this, 'output_twitter_tags' ), 3 );
		add_action( 'wp_head', array( $this, 'output_schema_markup' ), 4 );
		add_action( 'wp_head', array( $this, 'output_canonical' ), 5 );
		add_action( 'wp_head', array( $this, 'output_robots_meta' ), 6 );
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'optimize_image_attributes' ), 10, 3 );
		add_filter( 'wp_get_attachment_image', array( $this, 'add_lazy_loading' ), 10, 5 );
		add_action( 'wp_footer', array( $this, 'output_breadcrumbs_schema' ), 1 );
		add_action( 'init', array( $this, 'add_sitemap_rewrite' ) );
		add_action( 'template_redirect', array( $this, 'render_sitemap' ) );
	}
	
	/**
	 * Output meta tags
	 */
	public function output_meta_tags() {
		$description = $this->get_meta_description();
		if ( $description ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
		}
		
		$keywords = $this->get_meta_keywords();
		if ( $keywords ) {
			echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '">' . "\n";
		}
		
		if ( is_single() || is_page() ) {
			$author = get_the_author_meta( 'display_name' );
			if ( $author ) {
				echo '<meta name="author" content="' . esc_attr( $author ) . '">' . "\n";
			}
		}
		
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">' . "\n";
		
		$language = get_locale();
		if ( $language ) {
			echo '<meta http-equiv="content-language" content="' . esc_attr( $language ) . '">' . "\n";
		}
		
		$theme_color = get_theme_mod( 'theme_color', '#2563eb' );
		echo '<meta name="theme-color" content="' . esc_attr( $theme_color ) . '">' . "\n";
		
		$geo_region = get_theme_mod( 'geo_region', '' );
		if ( $geo_region ) {
			echo '<meta name="geo.region" content="' . esc_attr( $geo_region ) . '">' . "\n";
		}
		
		if ( is_single() ) {
			echo '<meta name="content-type" content="article">' . "\n";
		} elseif ( is_page() ) {
			echo '<meta name="content-type" content="page">' . "\n";
		}
		
		echo '<meta name="revisit-after" content="7 days">' . "\n";
		
		if ( is_singular() ) {
			$copyright_year = date( 'Y' );
			echo '<meta name="copyright" content="© ' . esc_attr( $copyright_year ) . ' ' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
		}
	}
	
	/**
	 * Output Open Graph tags
	 */
	public function output_og_tags() {
		$og_title = $this->get_og_title();
		$og_description = $this->get_og_description();
		$og_image = $this->get_og_image();
		$og_url = $this->get_og_url();
		$og_type = $this->get_og_type();
		$og_site_name = get_bloginfo( 'name' );
		
		echo '<!-- Open Graph / Facebook -->' . "\n";
		echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $og_url ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $og_description ) . '">' . "\n";
		if ( $og_image ) {
			echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">' . "\n";
			echo '<meta property="og:image:width" content="1200">' . "\n";
			echo '<meta property="og:image:height" content="630">' . "\n";
			echo '<meta property="og:image:alt" content="' . esc_attr( $og_title ) . '">' . "\n";
		}
		echo '<meta property="og:site_name" content="' . esc_attr( $og_site_name ) . '">' . "\n";
		echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
	}
	
	/**
	 * Output Twitter Card tags
	 */
	public function output_twitter_tags() {
		$twitter_card = 'summary_large_image';
		$twitter_title = $this->get_og_title();
		$twitter_description = $this->get_og_description();
		$twitter_image = $this->get_og_image();
		$twitter_site = get_theme_mod( 'twitter_handle', '' );
		
		echo '<!-- Twitter Card -->' . "\n";
		echo '<meta name="twitter:card" content="' . esc_attr( $twitter_card ) . '">' . "\n";
		if ( $twitter_site ) {
			echo '<meta name="twitter:site" content="' . esc_attr( $twitter_site ) . '">' . "\n";
		}
		echo '<meta name="twitter:title" content="' . esc_attr( $twitter_title ) . '">' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( $twitter_description ) . '">' . "\n";
		if ( $twitter_image ) {
			echo '<meta name="twitter:image" content="' . esc_url( $twitter_image ) . '">' . "\n";
		}
	}
	
	/**
	 * Output Schema.org structured data
	 */
	public function output_schema_markup() {
		if ( is_front_page() ) {
			$this->output_organization_schema();
		} elseif ( is_single() ) {
			$this->output_article_schema();
		} elseif ( is_page() ) {
			$this->output_webpage_schema();
		} elseif ( is_category() || is_tax() ) {
			$this->output_collection_page_schema();
		}
		
		// Always output Website schema
		$this->output_website_schema();
	}
	
	/**
	 * Output Organization schema
	 */
	private function output_organization_schema() {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Organization',
			'name' => get_bloginfo( 'name' ),
			'url' => home_url(),
			'logo' => $this->get_site_logo(),
			'sameAs' => $this->get_social_profiles(),
		);
		
		$description = get_bloginfo( 'description' );
		if ( $description ) {
			$schema['description'] = $description;
		}
		
		echo '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		echo "\n" . '</script>' . "\n";
	}
	
	/**
	 * Output Article schema
	 */
	private function output_article_schema() {
		$post = get_queried_object();
		if ( ! $post ) {
			return;
		}
		
		$image = $this->get_post_image( $post->ID );
		$author = get_userdata( $post->post_author );
		
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Article',
			'headline' => get_the_title(),
			'description' => $this->get_meta_description(),
			'image' => $image ? $image : $this->get_site_logo(),
			'datePublished' => get_the_date( 'c' ),
			'dateModified' => get_the_modified_date( 'c' ),
			'author' => array(
				'@type' => 'Person',
				'name' => $author ? $author->display_name : get_bloginfo( 'name' ),
			),
			'publisher' => array(
				'@type' => 'Organization',
				'name' => get_bloginfo( 'name' ),
				'logo' => array(
					'@type' => 'ImageObject',
					'url' => $this->get_site_logo(),
				),
			),
			'mainEntityOfPage' => array(
				'@type' => 'WebPage',
				'@id' => get_permalink(),
			),
		);
		
		// Add article body
		$content = get_the_content();
		if ( $content ) {
			$schema['articleBody'] = wp_strip_all_tags( $content );
		}
		
		// Add categories
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			$schema['articleSection'] = array();
			foreach ( $categories as $category ) {
				$schema['articleSection'][] = $category->name;
			}
		}
		
		// Add tags
		$tags = get_the_tags();
		if ( ! empty( $tags ) ) {
			$schema['keywords'] = array();
			foreach ( $tags as $tag ) {
				$schema['keywords'][] = $tag->name;
			}
		}
		
		// Add word count
		$word_count = str_word_count( wp_strip_all_tags( $content ) );
		if ( $word_count > 0 ) {
			$schema['wordCount'] = $word_count;
		}
		
		// Add reading time estimate
		$reading_time = ceil( $word_count / 200 ); // Average reading speed
		if ( $reading_time > 0 ) {
			$schema['timeRequired'] = 'PT' . $reading_time . 'M';
		}
		
		echo '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		echo "\n" . '</script>' . "\n";
	}
	
	/**
	 * Output WebPage schema
	 */
	private function output_webpage_schema() {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'WebPage',
			'name' => get_the_title(),
			'description' => $this->get_meta_description(),
			'url' => get_permalink(),
		);
		
		$image = $this->get_post_image( get_the_ID() );
		if ( $image ) {
			$schema['image'] = $image;
		}
		
		echo '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		echo "\n" . '</script>' . "\n";
	}
	
	/**
	 * Output CollectionPage schema
	 */
	private function output_collection_page_schema() {
		$term = get_queried_object();
		if ( ! $term ) {
			return;
		}
		
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'CollectionPage',
			'name' => $term->name,
			'description' => $term->description ? $term->description : $this->get_meta_description(),
			'url' => get_term_link( $term ),
		);
		
		echo '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		echo "\n" . '</script>' . "\n";
	}
	
	/**
	 * Output Website schema
	 */
	private function output_website_schema() {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'WebSite',
			'name' => get_bloginfo( 'name' ),
			'url' => home_url(),
			'potentialAction' => array(
				'@type' => 'SearchAction',
				'target' => array(
					'@type' => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		);
		
		$description = get_bloginfo( 'description' );
		if ( $description ) {
			$schema['description'] = $description;
		}
		
		echo '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		echo "\n" . '</script>' . "\n";
	}
	
	/**
	 * Output canonical URL
	 */
	public function output_canonical() {
		$canonical = $this->get_canonical_url();
		if ( $canonical ) {
			echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
		}
	}
	
	/**
	 * Output robots meta
	 */
	public function output_robots_meta() {
		if ( is_search() || is_404() ) {
			echo '<meta name="robots" content="noindex, nofollow">' . "\n";
			return;
		}
		
		// Check if post/page has noindex
		if ( is_singular() ) {
			$noindex = get_post_meta( get_the_ID(), '_sme_noindex', true );
			if ( $noindex ) {
				echo '<meta name="robots" content="noindex, nofollow">' . "\n";
				return;
			}
		}
		
		// Default: index, follow
		echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1, indexifembedded">' . "\n";
	}
	
	/**
	 * Optimize image attributes
	 */
	public function optimize_image_attributes( $attr, $attachment, $size ) {
		// Ensure alt text
		if ( empty( $attr['alt'] ) ) {
			$attr['alt'] = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
			if ( empty( $attr['alt'] ) ) {
				$attr['alt'] = get_the_title( $attachment->ID );
			}
		}
		
		// Add width and height for performance
		$image_meta = wp_get_attachment_metadata( $attachment->ID );
		if ( $image_meta && isset( $image_meta['width'] ) && isset( $image_meta['height'] ) ) {
			if ( ! isset( $attr['width'] ) ) {
				$attr['width'] = $image_meta['width'];
			}
			if ( ! isset( $attr['height'] ) ) {
				$attr['height'] = $image_meta['height'];
			}
		}
		
		// Add loading="lazy" for performance
		if ( ! isset( $attr['loading'] ) ) {
			$attr['loading'] = 'lazy';
		}
		
		return $attr;
	}
	
	/**
	 * Add lazy loading to images
	 */
	public function add_lazy_loading( $html, $attachment_id, $size, $icon, $attr ) {
		// WordPress 5.5+ has native lazy loading, but we ensure it's there
		if ( strpos( $html, 'loading=' ) === false ) {
			$html = str_replace( '<img ', '<img loading="lazy" ', $html );
		}
		
		return $html;
	}
	
	/**
	 * Output breadcrumbs schema
	 */
	public function output_breadcrumbs_schema() {
		if ( is_front_page() ) {
			return;
		}
		
		$breadcrumbs = $this->get_breadcrumbs();
		if ( empty( $breadcrumbs ) ) {
			return;
		}
		
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'BreadcrumbList',
			'itemListElement' => array(),
		);
		
		$position = 1;
		foreach ( $breadcrumbs as $breadcrumb ) {
			$schema['itemListElement'][] = array(
				'@type' => 'ListItem',
				'position' => $position,
				'name' => $breadcrumb['name'],
				'item' => $breadcrumb['url'],
			);
			$position++;
		}
		
		echo '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		echo "\n" . '</script>' . "\n";
	}
	
	/**
	 * Get breadcrumbs
	 */
	private function get_breadcrumbs() {
		$breadcrumbs = array();
		
		// Home
		$breadcrumbs[] = array(
			'name' => get_bloginfo( 'name' ),
			'url' => home_url(),
		);
		
		if ( is_single() ) {
			// Category
			$categories = get_the_category();
			if ( ! empty( $categories ) ) {
				$category = $categories[0];
				$breadcrumbs[] = array(
					'name' => $category->name,
					'url' => get_category_link( $category->term_id ),
				);
			}
			
			// Post
			$breadcrumbs[] = array(
				'name' => get_the_title(),
				'url' => get_permalink(),
			);
		} elseif ( is_page() ) {
			// Parent pages
			$parent_id = wp_get_post_parent_id( get_the_ID() );
			$parents = array();
			while ( $parent_id ) {
				$parent = get_post( $parent_id );
				if ( $parent ) {
					$parents[] = array(
						'name' => $parent->post_title,
						'url' => get_permalink( $parent->ID ),
					);
					$parent_id = wp_get_post_parent_id( $parent->ID );
				} else {
					break;
				}
			}
			$breadcrumbs = array_merge( $breadcrumbs, array_reverse( $parents ) );
			
			// Current page
			$breadcrumbs[] = array(
				'name' => get_the_title(),
				'url' => get_permalink(),
			);
		} elseif ( is_category() || is_tax() ) {
			$term = get_queried_object();
			if ( $term ) {
				$breadcrumbs[] = array(
					'name' => $term->name,
					'url' => get_term_link( $term ),
				);
			}
		}
		
		return $breadcrumbs;
	}
	
	/**
	 * Add sitemap rewrite rule
	 */
	public function add_sitemap_rewrite() {
		add_rewrite_rule( '^sitemap\.xml$', 'index.php?sme_sitemap=1', 'top' );
	}
	
	/**
	 * Render sitemap
	 */
	public function render_sitemap() {
		if ( ! isset( $_GET['sme_sitemap'] ) || $_GET['sme_sitemap'] !== '1' ) {
			return;
		}
		
		header( 'Content-Type: text/xml; charset=utf-8' );
		
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		
		// Homepage
		echo '<url>' . "\n";
		echo '<loc>' . esc_url( home_url() ) . '</loc>' . "\n";
		echo '<lastmod>' . esc_html( date( 'c' ) ) . '</lastmod>' . "\n";
		echo '<changefreq>daily</changefreq>' . "\n";
		echo '<priority>1.0</priority>' . "\n";
		echo '</url>' . "\n";
		
		// Posts
		$posts = get_posts( array(
			'post_type' => 'post',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		) );
		
		foreach ( $posts as $post ) {
			echo '<url>' . "\n";
			echo '<loc>' . esc_url( get_permalink( $post->ID ) ) . '</loc>' . "\n";
			echo '<lastmod>' . esc_html( get_the_modified_date( 'c', $post->ID ) ) . '</lastmod>' . "\n";
			echo '<changefreq>weekly</changefreq>' . "\n";
			echo '<priority>0.8</priority>' . "\n";
			echo '</url>' . "\n";
		}
		
		// Pages
		$pages = get_pages( array(
			'post_status' => 'publish',
		) );
		
		foreach ( $pages as $page ) {
			echo '<url>' . "\n";
			echo '<loc>' . esc_url( get_permalink( $page->ID ) ) . '</loc>' . "\n";
			echo '<lastmod>' . esc_html( get_the_modified_date( 'c', $page->ID ) ) . '</lastmod>' . "\n";
			echo '<changefreq>monthly</changefreq>' . "\n";
			echo '<priority>0.6</priority>' . "\n";
			echo '</url>' . "\n";
		}
		
		// Categories
		$categories = get_categories( array(
			'hide_empty' => true,
		) );
		
		foreach ( $categories as $category ) {
			echo '<url>' . "\n";
			echo '<loc>' . esc_url( get_category_link( $category->term_id ) ) . '</loc>' . "\n";
			echo '<lastmod>' . esc_html( date( 'c' ) ) . '</lastmod>' . "\n";
			echo '<changefreq>weekly</changefreq>' . "\n";
			echo '<priority>0.7</priority>' . "\n";
			echo '</url>' . "\n";
		}
		
		echo '</urlset>';
		exit;
	}
	
	/**
	 * Get meta description
	 */
	private function get_meta_description() {
		if ( is_singular() ) {
			$meta = get_post_meta( get_the_ID(), '_sme_meta_description', true );
			if ( $meta ) {
				return $meta;
			}
			
			$excerpt = get_the_excerpt();
			if ( $excerpt ) {
				return wp_trim_words( $excerpt, 25 );
			}
		} elseif ( is_category() || is_tax() ) {
			$term = get_queried_object();
			if ( $term && $term->description ) {
				return wp_trim_words( $term->description, 25 );
			}
		}
		
		return get_bloginfo( 'description' );
	}
	
	/**
	 * Get meta keywords
	 */
	private function get_meta_keywords() {
		if ( is_singular() ) {
			$tags = get_the_tags();
			if ( $tags ) {
				$keywords = array();
				foreach ( $tags as $tag ) {
					$keywords[] = $tag->name;
				}
				return implode( ', ', $keywords );
			}
		}
		
		return '';
	}
	
	/**
	 * Get OG title
	 */
	private function get_og_title() {
		if ( is_singular() ) {
			return get_the_title();
		} elseif ( is_category() || is_tax() ) {
			$term = get_queried_object();
			return $term ? $term->name : get_bloginfo( 'name' );
		}
		
		return get_bloginfo( 'name' );
	}
	
	/**
	 * Get OG description
	 */
	private function get_og_description() {
		return $this->get_meta_description();
	}
	
	/**
	 * Get OG image
	 */
	private function get_og_image() {
		if ( is_singular() ) {
			$image = $this->get_post_image( get_the_ID() );
			if ( $image ) {
				return $image;
			}
		}
		
		return $this->get_site_logo();
	}
	
	/**
	 * Get OG URL
	 */
	private function get_og_url() {
		if ( is_singular() ) {
			return get_permalink();
		} elseif ( is_category() || is_tax() ) {
			$term = get_queried_object();
			return $term ? get_term_link( $term ) : home_url();
		}
		
		return home_url();
	}
	
	/**
	 * Get OG type
	 */
	private function get_og_type() {
		if ( is_singular() ) {
			return 'article';
		}
		
		return 'website';
	}
	
	/**
	 * Get canonical URL
	 */
	private function get_canonical_url() {
		if ( is_singular() ) {
			return get_permalink();
		} elseif ( is_category() || is_tax() ) {
			$term = get_queried_object();
			return $term ? get_term_link( $term ) : home_url();
		} elseif ( is_home() || is_front_page() ) {
			return home_url();
		}
		
		return '';
	}
	
	/**
	 * Get post image
	 */
	private function get_post_image( $post_id ) {
		// Featured image
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, 'full' );
			if ( $image ) {
				return $image[0];
			}
		}
		
		// First image in content
		$post = get_post( $post_id );
		if ( $post ) {
			preg_match_all( '/<img[^>]+src=["\']([^"\']+)["\']/', $post->post_content, $matches );
			if ( ! empty( $matches[1] ) ) {
				return $matches[1][0];
			}
		}
		
		return '';
	}
	
	/**
	 * Get site logo
	 */
	private function get_site_logo() {
		$logo_id = get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			$logo = wp_get_attachment_image_src( $logo_id, 'full' );
			if ( $logo ) {
				return $logo[0];
			}
		}
		
		return '';
	}
	
	/**
	 * Get social profiles
	 */
	private function get_social_profiles() {
		$profiles = array();
		
		$facebook = get_theme_mod( 'social_facebook', '' );
		if ( $facebook ) {
			$profiles[] = $facebook;
		}
		
		$twitter = get_theme_mod( 'social_twitter', '' );
		if ( $twitter ) {
			$profiles[] = $twitter;
		}
		
		$linkedin = get_theme_mod( 'social_linkedin', '' );
		if ( $linkedin ) {
			$profiles[] = $linkedin;
		}
		
		return $profiles;
	}
}

SME_SEO_Optimizer::get_instance();

