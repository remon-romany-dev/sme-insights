<?php
/**
 * Helper Functions
 * Additional utility functions for theme functionality
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Order menu items in fixed order: Technology, Marketing, Finance, Growth, Strategy, All Articles, About, Contact
if ( ! function_exists( 'sme_order_menu_items' ) ) {
	add_filter( 'wp_get_nav_menu_items', 'sme_order_menu_items', 10, 3 );
	function sme_order_menu_items( $items, $menu, $args ) {
		if ( empty( $items ) || ! is_array( $items ) ) {
			return $items;
		}
		
		// Only apply to primary menu
		if ( is_object( $args ) && isset( $args->theme_location ) && $args->theme_location !== 'primary' ) {
			return $items;
		}
		
		$ordered_items = array();
		$order_map = array(
			'technology' => 1,
			'marketing' => 2,
			'finance' => 3,
			'growth' => 4,
			'strategy' => 5,
		);
		
		$categories = array();
		$all_articles = null;
		$about = null;
		$contact = null;
		$others = array();
		
		foreach ( $items as $item ) {
			if ( $item->type === 'taxonomy' && $item->object === 'main_category' ) {
				$term = get_term( $item->object_id, 'main_category' );
				if ( $term && isset( $term->slug ) ) {
					if ( isset( $order_map[ $term->slug ] ) ) {
						// Known categories - use specific order
						$categories[ $order_map[ $term->slug ] ] = $item;
					} else {
						// New categories added by user - add to categories array with high order number
						$categories[ 100 + count( $categories ) ] = $item;
					}
				}
			} elseif ( strtolower( trim( $item->title ) ) === 'all articles' || 
			           ( $item->type === 'post_type' && $item->object === 'page' && 
			             ( strpos( $item->url, '/business-news-insights' ) !== false || strpos( $item->url, '/blog' ) !== false ) ) ) {
				$all_articles = $item;
			} elseif ( strtolower( trim( $item->title ) ) === 'about' || 
			           strpos( $item->url, '/about' ) !== false ) {
				$about = $item;
			} elseif ( strtolower( trim( $item->title ) ) === 'contact' || 
			           strpos( $item->url, '/contact' ) !== false ) {
				$contact = $item;
			} else {
				$others[] = $item;
			}
		}
		
		// Sort categories by order
		ksort( $categories );
		
		// Build ordered array: categories first, then All Articles, About, Contact, then others
		foreach ( $categories as $category ) {
			$ordered_items[] = $category;
		}
		
		if ( $all_articles ) {
			$ordered_items[] = $all_articles;
		}
		if ( $about ) {
			$ordered_items[] = $about;
		}
		if ( $contact ) {
			$ordered_items[] = $contact;
		}
		
		foreach ( $others as $other ) {
			$ordered_items[] = $other;
		}
		
		return $ordered_items;
	}
}

// Filter menu items for mobile - only show All Articles, About, Contact (categories are already in mobile-category-list)
if ( ! function_exists( 'sme_mobile_menu_items_only' ) ) {
	function sme_mobile_menu_items_only( $items, $menu, $args ) {
		if ( empty( $items ) || ! is_array( $items ) ) {
			return $items;
		}
		
		$filtered_items = array();
		
		foreach ( $items as $item ) {
			// Skip categories (they're in mobile-category-list)
			if ( $item->type === 'taxonomy' && $item->object === 'main_category' ) {
				continue;
			}
			
			// Only show All Articles, About, Contact
			$item_title_lower = strtolower( trim( $item->title ) );
			$item_url = ! empty( $item->url ) ? $item->url : '';
			
			$is_all_articles = ( $item_title_lower === 'all articles' || 
			                    strpos( $item_url, '/business-news-insights' ) !== false || 
			                    strpos( $item_url, '/blog' ) !== false );
			$is_about = ( $item_title_lower === 'about' || strpos( $item_url, '/about' ) !== false );
			$is_contact = ( $item_title_lower === 'contact' || strpos( $item_url, '/contact' ) !== false );
			
			if ( $is_all_articles || $is_about || $is_contact ) {
				$filtered_items[] = $item;
			}
		}
		
		return $filtered_items;
	}
}

if ( ! function_exists( 'sme_get_primary_categories' ) ) {
	function sme_get_primary_categories() {
		$category_slugs = array( 
			'technology',  // أولاً
			'marketing',   // ثانياً
			'finance',     // ثالثاً
			'growth',      // رابعاً
			'strategy'     // أخيراً
		);
		$categories = array();
		$added_slugs = array();
		
		foreach ( $category_slugs as $slug ) {
			$term = get_term_by( 'slug', $slug, 'main_category' );
			if ( $term && ! is_wp_error( $term ) && ! in_array( $slug, $added_slugs, true ) ) {
				$categories[] = $term;
				$added_slugs[] = $slug;
			}
		}
		
		return $categories;
	}
}

if ( ! function_exists( 'sme_default_menu' ) ) {
	function sme_default_menu() {
		$categories = function_exists( 'sme_get_primary_categories' ) ? sme_get_primary_categories() : array();
		$seen_slugs = array();
		$seen_urls = array();
		
		if ( ! empty( $categories ) ) {
			echo '<ul class="main-nav">';
			
			foreach ( $categories as $category ) {
				if ( ! $category || is_wp_error( $category ) ) {
					continue;
				}
				
				$category_slug = isset( $category->slug ) ? sanitize_key( $category->slug ) : '';
				$category_url = get_term_link( $category );
				
				if ( is_wp_error( $category_url ) || empty( $category_slug ) ) {
					continue;
				}
				
				$category_url_normalized = rtrim( trailingslashit( esc_url_raw( $category_url ) ), '/' );
				
				if ( in_array( $category_slug, $seen_slugs, true ) || in_array( $category_url_normalized, $seen_urls, true ) ) {
					continue;
				}
				
				$seen_slugs[] = $category_slug;
				$seen_urls[] = $category_url_normalized;
				
				$active_class = ( is_tax( 'main_category', $category_slug ) ) ? 'active' : '';
				
				$labels = array(
					'technology' => 'Technology',
					'marketing'  => 'Marketing',
					'finance'    => 'Finance',
					'growth'     => 'Growth',
					'strategy'   => 'Strategy',
				);
				
				$menu_name = isset( $labels[ $category_slug ] ) ? $labels[ $category_slug ] : $category->name;
				$menu_name = preg_replace( '/[^\x20-\x7E]/', '', $menu_name );
				$menu_name = trim( $menu_name );
				
				if ( ! empty( $menu_name ) ) {
					echo '<li><a href="' . esc_url( $category_url ) . '" class="' . esc_attr( $active_class ) . '">' . esc_html( $menu_name ) . '</a></li>';
				}
			}
			
			$blog_page = get_page_by_path( 'business-news-insights' );
			if ( ! $blog_page ) {
				$blog_page = get_page_by_path( 'blog' );
			}
			if ( ! $blog_page ) {
				$blog_page = sme_get_page_by_title( 'Business News & Insights' );
			}
			
			if ( $blog_page ) {
				$blog_url = get_permalink( $blog_page->ID );
				$blog_url_normalized = rtrim( trailingslashit( esc_url_raw( $blog_url ) ), '/' );
				
				if ( ! in_array( $blog_url_normalized, $seen_urls, true ) ) {
					$is_blog_active = is_page( $blog_page->ID ) || ( is_home() && ! is_front_page() );
					echo '<li><a href="' . esc_url( $blog_url ) . '" class="' . ( $is_blog_active ? 'active' : '' ) . '">All Articles</a></li>';
					$seen_urls[] = $blog_url_normalized;
				}
			}
			
			$about_url = home_url( '/about' );
			$contact_url = home_url( '/contact' );
			$about_url_normalized = rtrim( trailingslashit( esc_url_raw( $about_url ) ), '/' );
			$contact_url_normalized = rtrim( trailingslashit( esc_url_raw( $contact_url ) ), '/' );
			
			$about_page = get_page_by_path( 'about' );
			if ( ! $about_page ) {
				$about_page = get_page_by_path( 'about-us' );
			}
			$contact_page = get_page_by_path( 'contact' );
			if ( ! $contact_page ) {
				$contact_page = get_page_by_path( 'contact-us' );
			}
			
			$about_permalink = $about_page ? get_permalink( $about_page->ID ) : $about_url;
			$contact_permalink = $contact_page ? get_permalink( $contact_page->ID ) : $contact_url;
			$about_permalink_normalized = rtrim( trailingslashit( esc_url_raw( $about_permalink ) ), '/' );
			$contact_permalink_normalized = rtrim( trailingslashit( esc_url_raw( $contact_permalink ) ), '/' );
			
			if ( ! in_array( $about_url_normalized, $seen_urls, true ) && ! in_array( $about_permalink_normalized, $seen_urls, true ) && ! in_array( 'about', $seen_slugs, true ) ) {
				echo '<li><a href="' . esc_url( $about_permalink ) . '" class="' . ( is_page( 'about' ) || is_page( 'about-us' ) ? 'active' : '' ) . '">About</a></li>';
				$seen_urls[] = $about_permalink_normalized;
			}
			
			if ( ! in_array( $contact_url_normalized, $seen_urls, true ) && ! in_array( $contact_permalink_normalized, $seen_urls, true ) && ! in_array( 'contact', $seen_slugs, true ) ) {
				echo '<li><a href="' . esc_url( $contact_permalink ) . '" class="' . ( is_page( 'contact' ) || is_page( 'contact-us' ) ? 'active' : '' ) . '">Contact</a></li>';
				$seen_urls[] = $contact_permalink_normalized;
			}
			
			echo '</ul>';
		}
	}
}

add_filter( 'wp_nav_menu_objects', 'sme_remove_duplicate_menu_items', 10, 2 );
function sme_remove_duplicate_menu_items( $items, $args ) {
	if ( empty( $items ) || ! is_array( $items ) ) {
		return $items;
	}
	
	if ( isset( $args->theme_location ) && $args->theme_location !== 'primary' ) {
		return $items;
	}
	
	$filtered_items = array();
	$seen_urls = array();
	$seen_slugs = array();
	$seen_object_ids = array();
	$allowed_slugs = array( 'technology', 'marketing', 'finance', 'growth', 'strategy' );
	
	foreach ( $items as $item ) {
		if ( strtolower( trim( $item->title ) ) === 'home' || $item->url === home_url( '/' ) ) {
			continue;
		}
		
		$item_url = ! empty( $item->url ) ? rtrim( trailingslashit( esc_url_raw( $item->url ) ), '/' ) : '';
		
		if ( ! empty( $item_url ) && in_array( $item_url, $seen_urls, true ) ) {
			continue;
		}
		
		if ( ! empty( $item->object_id ) && in_array( $item->object_id, $seen_object_ids, true ) ) {
			continue;
		}
		
		if ( $item->type === 'taxonomy' && $item->object === 'main_category' ) {
			$term_slug = '';
			
			if ( ! empty( $item->object_id ) ) {
				$term = get_term( absint( $item->object_id ), 'main_category' );
				if ( $term && ! is_wp_error( $term ) && isset( $term->slug ) ) {
					$term_slug = sanitize_key( $term->slug );
				}
			}
			
			if ( ! empty( $term_slug ) ) {
				// Only prevent duplicates for categories in allowed list
				// Allow all other categories (user can add new categories)
				if ( in_array( $term_slug, $allowed_slugs, true ) ) {
					if ( in_array( $term_slug, $seen_slugs, true ) ) {
						continue; // Duplicate category
					}
					$seen_slugs[] = $term_slug;
				}
				// For categories not in allowed_slugs, allow them (user-added categories)
			}
		}
		
		if ( ! empty( $item_url ) ) {
			$seen_urls[] = $item_url;
		}
		if ( ! empty( $item->object_id ) ) {
			$seen_object_ids[] = $item->object_id;
		}
		
		$filtered_items[] = $item;
	}
	
	return $filtered_items;
}

if ( ! function_exists( 'sme_remove_home_from_menu' ) ) {
	function sme_remove_home_from_menu( $items, $args ) {
		if ( isset( $args->theme_location ) && $args->theme_location === 'primary' ) {
			$items = preg_replace( '/<li[^>]*>.*?Home.*?<\/li>/i', '', $items );
			
			$seen_urls = array();
			$seen_titles = array();
			$allowed_slugs = array( 'technology', 'marketing', 'finance', 'growth', 'strategy' );
			
			preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $items, $matches );
			
			if ( ! empty( $matches[0] ) ) {
				$filtered_items = array();
				
				foreach ( $matches[0] as $item ) {
					preg_match( '/href=["\']([^"\']+)["\']/', $item, $url_match );
					$url = ! empty( $url_match[1] ) ? $url_match[1] : '';
					
					preg_match( '/<a[^>]*>(.*?)<\/a>/is', $item, $title_match );
					$title = ! empty( $title_match[1] ) ? strip_tags( trim( $title_match[1] ) ) : '';
					$normalized_title = strtolower( preg_replace( '/\s+/', ' ', $title ) );
					
					if ( ! empty( $url ) && in_array( $url, $seen_urls ) ) {
						continue;
					}
					if ( ! empty( $normalized_title ) && in_array( $normalized_title, $seen_titles ) ) {
						continue;
					}
					
					$url_slug = '';
					if ( preg_match( '/\/topic\/([^\/]+)/', $url, $slug_match ) ) {
						$url_slug = $slug_match[1];
					} elseif ( preg_match( '/\/(finance|marketing|technology|growth|strategy)\/?$/', $url, $slug_match ) ) {
						$url_slug = $slug_match[1];
					}
					
					if ( ! empty( $url_slug ) && ! in_array( $url_slug, $allowed_slugs ) ) {
						$is_page_url = ( preg_match( '/\/(about|contact|privacy-policy|terms-of-service|disclaimer|advertise-with-us|become-contributor)\/?$/', $url ) );
						if ( ! $is_page_url ) {
							continue;
						}
					}
					
					if ( ! empty( $url ) ) {
						$seen_urls[] = $url;
					}
					if ( ! empty( $normalized_title ) ) {
						$seen_titles[] = $normalized_title;
					}
					
					$filtered_items[] = $item;
				}
				
				$items = implode( '', $filtered_items );
			}
		}
		return $items;
	}
}

if ( ! class_exists( 'SME_Menu_Walker' ) ) {
	class SME_Menu_Walker extends Walker_Nav_Menu {
		private $added_urls = array();
		private $added_slugs = array();
		private $added_titles = array();
		private $added_object_ids = array();
		private $allowed_slugs = array( 'technology', 'marketing', 'finance', 'growth', 'strategy' );
		private $ordered_items = array();
		
		function start_lvl( &$output, $depth = 0, $args = null ) {
			$indent = str_repeat( "\t", $depth );
			$output .= "\n$indent<ul class=\"sub-menu\">\n";
		}
		
		function end_lvl( &$output, $depth = 0, $args = null ) {
			$indent = str_repeat( "\t", $depth );
			$output .= "$indent</ul>\n";
		}
		
		function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$item_title_lower = strtolower( trim( $item->title ) );
			if ( $item_title_lower === 'home' || $item->url === home_url( '/' ) ) {
				return;
			}
			
			$clean_title = preg_replace( '/[^\x20-\x7E]/', '', $item->title );
			$clean_title = trim( $clean_title );
			
			if ( empty( $clean_title ) ) {
				return;
			}
			
			$normalized_title = strtolower( preg_replace( '/\s+/', ' ', $clean_title ) );
			
			$item_url = ! empty( $item->url ) ? trailingslashit( esc_url_raw( $item->url ) ) : '';
			$item_url_normalized = rtrim( $item_url, '/' );
			
			// Prevent duplicates
			if ( ! empty( $item_url_normalized ) && in_array( $item_url_normalized, $this->added_urls, true ) ) {
				return;
			}
			
			if ( in_array( $normalized_title, $this->added_titles, true ) ) {
				return;
			}
			
			if ( ! empty( $item->object_id ) && in_array( $item->object_id, $this->added_object_ids, true ) ) {
				return;
			}
			
			$is_page = ( $item->object === 'page' );
			$is_custom_link = ( $item->type === 'custom' );
			$is_taxonomy = ( $item->type === 'taxonomy' );
			$url_slug = '';
			$is_all_articles = false;
			
			// Check if it's "All Articles"
			$blog_page = get_page_by_path( 'business-news-insights' );
			if ( ! $blog_page ) {
				$blog_page = get_page_by_path( 'blog' );
			}
			if ( ! $blog_page ) {
				$blog_page = sme_get_page_by_title( 'Business News & Insights' );
			}
			$all_articles_titles = array( 'all articles', 'business news', 'business news & insights', 'blog' );
			if ( $blog_page ) {
				$blog_url = get_permalink( $blog_page->ID );
				$blog_url_normalized = rtrim( trailingslashit( esc_url_raw( $blog_url ) ), '/' );
				if ( $is_page && ! empty( $item->object_id ) && $item->object_id == $blog_page->ID ) {
					$is_all_articles = true;
				} elseif ( $is_custom_link && ! empty( $item_url ) && $item_url_normalized === $blog_url_normalized ) {
					$is_all_articles = true;
				} elseif ( $is_custom_link && ! empty( $item_url ) && ( strpos( $item_url, '/business-news-insights' ) !== false || strpos( $item_url, '/blog' ) !== false ) ) {
					$is_all_articles = true;
				} elseif ( in_array( $normalized_title, $all_articles_titles, true ) ) {
					$is_all_articles = true;
				}
			}
			
			// Check if it's a category
			if ( $is_taxonomy && $item->object === 'main_category' ) {
				$url_slug = '';
				if ( ! empty( $item->object_id ) ) {
					$term = get_term( absint( $item->object_id ), 'main_category' );
					if ( $term && ! is_wp_error( $term ) && isset( $term->slug ) ) {
						$url_slug = sanitize_key( $term->slug );
					}
				}
				
				if ( empty( $url_slug ) && ! empty( $item_url ) ) {
					if ( preg_match( '/\/topic\/([^\/]+)/', $item_url, $matches ) ) {
						$url_slug = sanitize_key( $matches[1] );
					} elseif ( preg_match( '/\/(finance|marketing|technology|growth|strategy)\/?$/', $item_url, $matches ) ) {
						$url_slug = sanitize_key( $matches[1] );
					} elseif ( preg_match( '/main_category=([^&]+)/', $item_url, $matches ) ) {
						$url_slug = sanitize_key( $matches[1] );
					}
				}
				
				// For categories, check if it's in allowed list (to prevent duplicates)
				if ( ! empty( $url_slug ) && in_array( $url_slug, $this->allowed_slugs, true ) ) {
					if ( in_array( $url_slug, $this->added_slugs, true ) ) {
						return; // Duplicate category
					}
					$this->added_slugs[] = $url_slug;
				}
			}
			
			// Track all items to prevent duplicates (allow all items from WordPress Menu)
			if ( ! empty( $item_url_normalized ) ) {
				$this->added_urls[] = $item_url_normalized;
			}
			$this->added_titles[] = $normalized_title;
			if ( ! empty( $item->object_id ) ) {
				$this->added_object_ids[] = $item->object_id;
			}
			
			$display_title = $clean_title;
			if ( $is_all_articles ) {
				$display_title = 'All Articles';
			} elseif ( $is_taxonomy && ! empty( $url_slug ) ) {
				$title_map = array(
					'technology' => 'Technology',
					'marketing' => 'Marketing',
					'finance' => 'Finance',
					'growth' => 'Growth',
					'strategy' => 'Strategy',
				);
				if ( isset( $title_map[ $url_slug ] ) ) {
					$display_title = $title_map[ $url_slug ];
				}
			}
			
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
			
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;
			
			if ( in_array( 'current-menu-item', $classes ) || in_array( 'current_page_item', $classes ) ) {
				$classes[] = 'active';
			}
			
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
			
			$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
			
			$output .= $indent . '<li' . $id . $class_names . '>';
			
			$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
			$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
			$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
			$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';
			
			$item_output = isset( $args->before ) ? $args->before : '';
			$item_output .= '<a' . $attributes . '>';
			$item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . esc_html( $display_title ) . ( isset( $args->link_after ) ? $args->link_after : '' );
			$item_output .= '</a>';
			$item_output .= isset( $args->after ) ? $args->after : '';
			
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
		
		function end_el( &$output, $item, $depth = 0, $args = null ) {
			$output .= "</li>\n";
		}
	}
}

