<?php
/**
 * Theme Setup Class
 * Handles theme initialization, features, and core functionality
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Theme_Setup {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_setup' ) );
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
		add_filter( 'excerpt_length', array( $this, 'custom_excerpt_length' ) );
		add_filter( 'excerpt_more', array( $this, 'custom_excerpt_more' ) );
		add_action( 'after_switch_theme', array( $this, 'flush_rewrite_rules' ) );
		add_action( 'after_switch_theme', array( $this, 'create_default_menu' ) );
		add_action( 'init', array( $this, 'maybe_create_default_menu' ), 20 );
	}
	
	/**
	 * Create default menu on first load if it doesn't exist or needs update
	 */
	public function maybe_create_default_menu() {
		// Only run once per session
		if ( get_transient( 'sme_menu_created_' . get_current_blog_id() ) ) {
			return;
		}
		
		$menu_name = 'Primary Menu';
		$menu_exists = wp_get_nav_menu_object( $menu_name );
		
		// If menu doesn't exist or has wrong items, recreate it
		if ( ! $menu_exists ) {
			$this->create_default_menu();
			set_transient( 'sme_menu_created_' . get_current_blog_id(), true, HOUR_IN_SECONDS );
		} else {
			// Check if menu has correct items
			$menu_items = wp_get_nav_menu_items( $menu_exists->term_id );
			$has_all_articles = false;
			$item_count = 0;
			
			if ( $menu_items ) {
				foreach ( $menu_items as $item ) {
					$item_count++;
					if ( strtolower( trim( $item->title ) ) === 'all articles' ) {
						$has_all_articles = true;
					}
				}
			}
			
			// If menu is missing "All Articles" or has wrong count, recreate it
			if ( ! $has_all_articles || $item_count < 8 ) {
				$this->create_default_menu();
				set_transient( 'sme_menu_created_' . get_current_blog_id(), true, HOUR_IN_SECONDS );
			}
		}
	}
	
	/**
	 * Theme setup
	 * WordPress Best Practice: Load text domain for translations
	 */
	public function theme_setup() {
		// Load theme text domain
		load_theme_textdomain( 'sme-insights', get_template_directory() . '/languages' );
		
		// Add theme support
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		) );
		add_theme_support( 'custom-logo' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		
		// Register navigation menus
		register_nav_menus( array(
			'primary' => __( 'Primary Menu', 'sme-insights' ),
			'footer'   => __( 'Footer Menu', 'sme-insights' ),
		) );
		
		// Image sizes
		add_image_size( 'sme-featured', 800, 410, true );
		add_image_size( 'sme-thumbnail', 300, 200, true );
		add_image_size( 'sme-medium', 600, 400, true );
		// Small sizes for performance optimization
		add_image_size( 'sme-breaking-news', 80, 60, true ); // For breaking news ticker
		add_image_size( 'sme-trending-small', 150, 100, true ); // For trending news
		add_image_size( 'sme-mobile-main', 400, 225, true ); // For mobile main news (16:9 aspect ratio)
		
		// Gutenberg support - Full editor styling
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		add_editor_style( array( 'assets/css/blocks-editor.css', 'assets/css/main.css' ) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'custom-spacing' );
		add_theme_support( 'custom-units', array( 'px', 'em', 'rem', 'vh', 'vw' ) );
		
		// Add editor color palette to match theme
		add_theme_support( 'editor-color-palette', array(
			array(
				'name'  => __( 'Primary Blue', 'sme-insights' ),
				'slug'  => 'primary-blue',
				'color' => '#1a365d',
			),
			array(
				'name'  => __( 'Secondary Blue', 'sme-insights' ),
				'slug'  => 'secondary-blue',
				'color' => '#2563eb',
			),
			array(
				'name'  => __( 'Text Primary', 'sme-insights' ),
				'slug'  => 'text-primary',
				'color' => '#1a202c',
			),
			array(
				'name'  => __( 'Text Secondary', 'sme-insights' ),
				'slug'  => 'text-secondary',
				'color' => '#4a5568',
			),
		) );
		
		// Add editor font sizes to match theme
		add_theme_support( 'editor-font-sizes', array(
			array(
				'name' => __( 'Small', 'sme-insights' ),
				'size' => 14,
				'slug' => 'small',
			),
			array(
				'name' => __( 'Normal', 'sme-insights' ),
				'size' => 16,
				'slug' => 'normal',
			),
			array(
				'name' => __( 'Large', 'sme-insights' ),
				'size' => 20,
				'slug' => 'large',
			),
			array(
				'name' => __( 'Huge', 'sme-insights' ),
				'size' => 32,
				'slug' => 'huge',
			),
		) );
	}
	
	/**
	 * Register sidebars
	 */
	public function register_sidebars() {
		register_sidebar( array(
			'name'          => __( 'Main Sidebar', 'sme-insights' ),
			'id'            => 'sidebar-main',
			'description'   => __( 'Main sidebar widget area', 'sme-insights' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
	
	/**
	 * Custom excerpt length
	 */
	public function custom_excerpt_length( $length ) {
		return 30;
	}
	
	/**
	 * Custom excerpt more
	 */
	public function custom_excerpt_more( $more ) {
		return '...';
	}
	
	/**
	 * Flush rewrite rules when theme is activated
	 * This ensures taxonomy URLs work correctly
	 */
	public function flush_rewrite_rules() {
		flush_rewrite_rules();
	}
	
	/**
	 * Create default navigation menu on theme activation
	 * This creates a WordPress menu that can be edited in the admin
	 */
	public function create_default_menu() {
		// Check if menu already exists
		$menu_name = 'Primary Menu';
		$menu_exists = wp_get_nav_menu_object( $menu_name );
		$menu_id = false;
		
		if ( $menu_exists ) {
			// Menu exists, get its ID
			$menu_id = $menu_exists->term_id;
			
			// Only delete items if menu is empty (to avoid deleting user-added items)
			$menu_items = wp_get_nav_menu_items( $menu_id );
			if ( ! empty( $menu_items ) ) {
				// Menu has items - don't delete them, just return
				// The menu will be managed by the user through Customizer
				return true;
			}
		} else {
			// Create the menu
			$menu_id = wp_create_nav_menu( $menu_name );
			
			if ( is_wp_error( $menu_id ) ) {
				return false;
			}
		}
		
		// Build menu in correct order: Technology | Marketing | Finance | Growth | Strategy | All Articles | About | Contact
		$menu_order = array();
		
		// 1. Add categories in correct order
		$category_slugs = array( 'technology', 'marketing', 'finance', 'growth', 'strategy' );
		$title_map = array(
			'technology' => 'Technology',
			'marketing' => 'Marketing',
			'finance' => 'Finance',
			'growth' => 'Growth',
			'strategy' => 'Strategy',
		);
		
		foreach ( $category_slugs as $slug ) {
			$term = get_term_by( 'slug', $slug, 'main_category' );
			if ( $term && ! is_wp_error( $term ) ) {
				$category_url = get_term_link( $term );
				if ( ! is_wp_error( $category_url ) ) {
					$menu_title = isset( $title_map[ $slug ] ) ? $title_map[ $slug ] : $term->name;
					
					$menu_order[] = array(
						'title'  => $menu_title,
						'url'    => $category_url,
						'type'   => 'taxonomy',
						'object' => 'main_category',
						'object_id' => $term->term_id,
					);
				}
			}
		}
		
		// 2. Add All Articles link
		$blog_page = get_page_by_path( 'business-news-insights' );
		if ( ! $blog_page ) {
			$blog_page = get_page_by_path( 'blog' );
		}
		if ( ! $blog_page ) {
			$blog_page = sme_get_page_by_title( 'Business News & Insights' );
		}
		if ( $blog_page ) {
			$blog_url = get_permalink( $blog_page->ID );
			$menu_order[] = array(
				'title'  => 'All Articles',
				'url'    => $blog_url,
				'type'   => 'post_type',
				'object' => 'page',
				'object_id' => $blog_page->ID,
			);
		}
		
		// 3. Add About page
		$about_page = get_page_by_path( 'about' );
		if ( ! $about_page ) {
			$about_page = get_page_by_path( 'about-us' );
		}
		if ( ! $about_page ) {
			$about_page = sme_get_page_by_title( 'About' );
		}
		if ( ! $about_page ) {
			$about_page = sme_get_page_by_title( 'About Us' );
		}
		if ( $about_page ) {
			$about_url = get_permalink( $about_page->ID );
			$menu_order[] = array(
				'title'  => 'About',
				'url'    => $about_url,
				'type'   => 'post_type',
				'object' => 'page',
				'object_id' => $about_page->ID,
			);
		}
		
		// 4. Add Contact page
		$contact_page = get_page_by_path( 'contact' );
		if ( ! $contact_page ) {
			$contact_page = get_page_by_path( 'contact-us' );
		}
		if ( ! $contact_page ) {
			$contact_page = sme_get_page_by_title( 'Contact' );
		}
		if ( ! $contact_page ) {
			$contact_page = sme_get_page_by_title( 'Contact Us' );
		}
		if ( $contact_page ) {
			$contact_url = get_permalink( $contact_page->ID );
			$menu_order[] = array(
				'title'  => 'Contact',
				'url'    => $contact_url,
				'type'   => 'post_type',
				'object' => 'page',
				'object_id' => $contact_page->ID,
			);
		}
		
		// Add all menu items in correct order
		foreach ( $menu_order as $item_data ) {
			wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-title'  => $item_data['title'],
				'menu-item-url'    => $item_data['url'],
				'menu-item-status' => 'publish',
				'menu-item-type'   => $item_data['type'],
				'menu-item-object' => $item_data['object'],
				'menu-item-object-id' => $item_data['object_id'],
			) );
		}
		
		// Assign menu to primary location (always update this)
		$locations = get_theme_mod( 'nav_menu_locations' );
		if ( ! is_array( $locations ) ) {
			$locations = array();
		}
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
		
		return true;
	}
}

