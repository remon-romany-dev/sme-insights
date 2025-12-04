<?php
/**
 * Custom Taxonomies
 * Registers and manages custom taxonomies for content organization
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Taxonomies {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'init', array( $this, 'register_taxonomies' ), 0 );
		add_action( 'after_switch_theme', array( $this, 'create_default_terms' ) );
		add_action( 'after_switch_theme', array( $this, 'flush_rewrite_rules' ) );
		add_action( 'admin_init', array( $this, 'maybe_flush_rewrite_rules' ) );
		
		$default_terms_created = get_option( 'sme_default_terms_created', false );
		if ( ! $default_terms_created ) {
			add_action( 'init', array( $this, 'create_default_terms' ), 20 );
		}
		
		add_action( 'main_category_add_form_fields', array( $this, 'add_category_icon_field' ) );
		add_action( 'main_category_edit_form_fields', array( $this, 'edit_category_icon_field' ) );
		add_action( 'created_main_category', array( $this, 'save_category_icon' ) );
		add_action( 'edited_main_category', array( $this, 'save_category_icon' ) );
	}
	
	/**
	 * Check if rewrite rules need to be flushed
	 */
	public function maybe_flush_rewrite_rules() {
		// Check if rewrite rules have been flushed
		$flushed = get_option( 'sme_rewrite_rules_flushed', false );
		
		if ( ! $flushed ) {
			flush_rewrite_rules( false );
			update_option( 'sme_rewrite_rules_flushed', true );
		}
	}
	
	/**
	 * Register custom taxonomies
	 */
	public function register_taxonomies() {
		
		// Main Category Taxonomy (Finance, Marketing, Technology, etc.)
		$labels = array(
			'name'              => _x( 'Main Categories', 'taxonomy general name', 'sme-insights' ),
			'singular_name'     => _x( 'Main Category', 'taxonomy singular name', 'sme-insights' ),
			'search_items'      => __( 'Search Main Categories', 'sme-insights' ),
			'all_items'         => __( 'All Main Categories', 'sme-insights' ),
			'parent_item'       => __( 'Parent Category', 'sme-insights' ),
			'parent_item_colon' => __( 'Parent Category:', 'sme-insights' ),
			'edit_item'         => __( 'Edit Category', 'sme-insights' ),
			'update_item'       => __( 'Update Category', 'sme-insights' ),
			'add_new_item'      => __( 'Add New Main Category', 'sme-insights' ),
			'new_item_name'     => __( 'New Category Name', 'sme-insights' ),
			'menu_name'         => __( 'Main Categories', 'sme-insights' ),
		);
		
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'topic' ),
			'show_in_rest'      => true,
		);
		
		register_taxonomy( 'main_category', array( 'post' ), $args );
		
		// Tags (for flexible tagging)
		register_taxonomy( 'article_tag', array( 'post' ), array(
			'hierarchical'      => false,
			'labels'            => array(
				'name'          => _x( 'Article Tags', 'taxonomy general name', 'sme-insights' ),
				'singular_name' => _x( 'Article Tag', 'taxonomy singular name', 'sme-insights' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'tag' ),
			'show_in_rest'      => true,
		) );
	}
	
	/**
	 * Create default main categories
	 */
	public function create_default_terms() {
		$default_categories = array(
			'finance' => array(
				'name'        => 'Finance',
				'slug'        => 'finance',
				'description' => 'Expert insights on small business finance, funding, budgeting, and financial management.',
				'color'       => '#065f46', // Dark Green
				'icon'        => '$',
			),
			'marketing' => array(
				'name'        => 'Marketing',
				'slug'        => 'marketing',
				'description' => 'Proven strategies to attract customers, build brands, and increase sales.',
				'color'       => '#ea580c', // Orange
				'icon'        => '📢',
			),
			'technology' => array(
				'name'        => 'Technology',
				'slug'        => 'technology',
				'description' => 'Latest tools, software, and technology trends for small businesses.',
				'color'       => '#2563eb', // Bright Blue
				'icon'        => '⚙️',
			),
			'growth' => array(
				'name'        => 'Growth',
				'slug'        => 'growth',
				'description' => 'Actionable strategies to scale your small business.',
				'color'       => '#0ea5e9', // Sky Blue
				'icon'        => '📈',
			),
			'strategy' => array(
				'name'        => 'Strategy',
				'slug'        => 'strategy',
				'description' => 'Deep analysis of market trends and expert legal advice.',
				'color'       => '#991b1b', // Dark Red
				'icon'        => '♟️',
			),
		);
		
		foreach ( $default_categories as $key => $category ) {
			$existing_term = get_term_by( 'slug', $category['slug'], 'main_category' );
			
			if ( ! $existing_term || is_wp_error( $existing_term ) ) {
				$term = wp_insert_term(
					$category['name'],
					'main_category',
					array(
						'description' => $category['description'],
						'slug'        => $category['slug'],
					)
				);
				
				if ( ! is_wp_error( $term ) && isset( $term['term_id'] ) ) {
					update_term_meta( $term['term_id'], 'category_color', $category['color'] );
					update_term_meta( $term['term_id'], 'category_icon', $category['icon'] );
				}
			} else {
				$has_color = get_term_meta( $existing_term->term_id, 'category_color', true );
				$has_icon = get_term_meta( $existing_term->term_id, 'category_icon', true );
				
				if ( empty( $has_color ) ) {
					update_term_meta( $existing_term->term_id, 'category_color', $category['color'] );
				}
				
				if ( empty( $has_icon ) ) {
					update_term_meta( $existing_term->term_id, 'category_icon', $category['icon'] );
				}
			}
		}
		
		$default_terms_created = get_option( 'sme_default_terms_created', false );
		if ( ! $default_terms_created ) {
			$all_categories = get_terms( array(
				'taxonomy'   => 'main_category',
				'hide_empty' => false,
			) );
			
			if ( ! empty( $all_categories ) && ! is_wp_error( $all_categories ) ) {
				foreach ( $all_categories as $cat ) {
					if ( preg_match( '/[\x{0600}-\x{06FF}]/u', $cat->name ) ) {
						$clean_name = preg_replace( '/[^\x20-\x7E]/', '', $cat->name );
						$clean_name = trim( $clean_name );
						
						if ( ! empty( $clean_name ) && $clean_name !== $cat->name ) {
							wp_update_term( $cat->term_id, 'main_category', array(
								'name' => $clean_name,
							) );
						}
					}
				}
			}
			
			update_option( 'sme_default_terms_created', true );
		}
	}
	
	/**
	 * Flush rewrite rules when theme is activated
	 * This ensures taxonomy URLs work correctly
	 */
	public function flush_rewrite_rules() {
		// Register taxonomies first
		$this->register_taxonomies();
		// Then flush rewrite rules
		flush_rewrite_rules( false );
		// Mark as flushed
		update_option( 'sme_rewrite_rules_flushed', true );
	}
	
	/**
	 * Add icon field to category add form
	 */
	public function add_category_icon_field() {
		$popular_icons = array(
			'💰' => 'Money',
			'📊' => 'Chart',
			'💼' => 'Briefcase',
			'🚀' => 'Rocket',
			'💡' => 'Lightbulb',
			'📈' => 'Growth',
			'🎯' => 'Target',
			'⚡' => 'Lightning',
			'🔧' => 'Tools',
			'📱' => 'Mobile',
			'🌐' => 'Globe',
			'💻' => 'Computer',
			'📢' => 'Megaphone',
			'🎨' => 'Art',
			'🏆' => 'Trophy',
			'🔒' => 'Lock',
			'📝' => 'Document',
			'♟️' => 'Chess',
			'$' => 'Dollar',
			'⚙️' => 'Settings',
			'📄' => 'Page',
		);
		?>
		<div class="form-field term-icon-wrap">
			<label for="category_icon"><?php _e( 'Category Icon', 'sme-insights' ); ?></label>
			<input type="text" id="category_icon" name="category_icon" value="" placeholder="📄" style="width: 100px; padding: 8px; font-size: 1.5rem; text-align: center;" maxlength="2" />
			<p style="margin-top: 10px;">
				<strong style="display: block; margin-bottom: 8px;">Popular Icons:</strong>
				<?php foreach ( $popular_icons as $icon => $name ) : ?>
					<span style="cursor: pointer; margin: 0 5px; font-size: 1.5rem; padding: 5px; display: inline-block; border: 1px solid #ddd; border-radius: 4px; background: #fff;" 
					      onclick="document.getElementById('category_icon').value='<?php echo esc_js( $icon ); ?>';" 
					      title="<?php echo esc_attr( $name ); ?>">
						<?php echo esc_html( $icon ); ?>
					</span>
				<?php endforeach; ?>
			</p>
			<p class="description"><?php _e( 'Choose an emoji icon for this category. Click on any icon above to select it.', 'sme-insights' ); ?></p>
		</div>
		
		<div class="form-field term-color-wrap">
			<label for="category_color"><?php _e( 'Category Color', 'sme-insights' ); ?></label>
			<input type="color" id="category_color" name="category_color" value="#2563eb" style="width: 100px; height: 40px; cursor: pointer;" />
			<p class="description"><?php _e( 'Choose a color for this category badge and icon.', 'sme-insights' ); ?></p>
		</div>
		<?php
	}
	
	/**
	 * Add icon field to category edit form
	 */
	public function edit_category_icon_field( $term ) {
		$current_icon = get_term_meta( $term->term_id, 'category_icon', true );
		$current_color = get_term_meta( $term->term_id, 'category_color', true );
		
		if ( empty( $current_icon ) ) {
			$current_icon = '📄';
		}
		if ( empty( $current_color ) ) {
			$current_color = '#2563eb';
		}
		
		$popular_icons = array(
			'💰' => 'Money',
			'📊' => 'Chart',
			'💼' => 'Briefcase',
			'🚀' => 'Rocket',
			'💡' => 'Lightbulb',
			'📈' => 'Growth',
			'🎯' => 'Target',
			'⚡' => 'Lightning',
			'🔧' => 'Tools',
			'📱' => 'Mobile',
			'🌐' => 'Globe',
			'💻' => 'Computer',
			'📢' => 'Megaphone',
			'🎨' => 'Art',
			'🏆' => 'Trophy',
			'🔒' => 'Lock',
			'📝' => 'Document',
			'♟️' => 'Chess',
			'$' => 'Dollar',
			'⚙️' => 'Settings',
			'📄' => 'Page',
		);
		?>
		<tr class="form-field term-icon-wrap">
			<th scope="row">
				<label for="category_icon"><?php _e( 'Category Icon', 'sme-insights' ); ?></label>
			</th>
			<td>
				<div style="margin-bottom: 15px;">
					<div style="font-size: 3rem; text-align: center; padding: 15px; background: #f5f5f5; border-radius: 8px; margin-bottom: 15px; border: 2px solid #ddd;">
						<span id="icon_preview"><?php echo esc_html( $current_icon ); ?></span>
					</div>
					<input type="text" id="category_icon" name="category_icon" value="<?php echo esc_attr( $current_icon ); ?>" placeholder="📄" style="width: 150px; padding: 10px; font-size: 1.5rem; text-align: center; border: 2px solid #2271b1; border-radius: 4px;" maxlength="2" oninput="document.getElementById('icon_preview').textContent=this.value||'📄';" />
				</div>
				<p style="margin-top: 10px;">
					<strong style="display: block; margin-bottom: 10px;">Popular Icons (Click to select):</strong>
					<?php foreach ( $popular_icons as $icon => $name ) : ?>
						<span style="cursor: pointer; margin: 0 5px 10px 0; font-size: 1.8rem; padding: 8px; display: inline-block; border: 2px solid <?php echo ( $icon === $current_icon ) ? '#2271b1' : '#ddd'; ?>; border-radius: 6px; background: <?php echo ( $icon === $current_icon ) ? '#f0f6fc' : '#fff'; ?>; transition: all 0.2s;" 
						      onclick="var input=document.getElementById('category_icon');input.value='<?php echo esc_js( $icon ); ?>';document.getElementById('icon_preview').textContent='<?php echo esc_js( $icon ); ?>';var spans=document.querySelectorAll('.term-icon-wrap span[onclick]');spans.forEach(function(s){s.style.borderColor='#ddd';s.style.background='#fff';});this.style.borderColor='#2271b1';this.style.background='#f0f6fc';" 
						      title="<?php echo esc_attr( $name ); ?>"
						      onmouseover="if(this.style.borderColor!=='#2271b1')this.style.borderColor='#2271b1';"
						      onmouseout="if(this.textContent.trim()!=='<?php echo esc_js( $icon ); ?>'){this.style.borderColor='#ddd';this.style.background='#fff';}">
							<?php echo esc_html( $icon ); ?>
						</span>
					<?php endforeach; ?>
				</p>
				<p class="description"><?php _e( 'Choose an emoji icon for this category. Click on any icon above to select it.', 'sme-insights' ); ?></p>
			</td>
		</tr>
		
		<tr class="form-field term-color-wrap">
			<th scope="row">
				<label for="category_color"><?php _e( 'Category Color', 'sme-insights' ); ?></label>
			</th>
			<td>
				<input type="color" id="category_color" name="category_color" value="<?php echo esc_attr( $current_color ); ?>" style="width: 100px; height: 40px; cursor: pointer;" />
				<span style="margin-left: 15px; font-family: monospace; font-size: 14px; color: #666;">
					<?php echo esc_html( $current_color ); ?>
				</span>
				<p class="description"><?php _e( 'Choose a color for this category badge and icon.', 'sme-insights' ); ?></p>
			</td>
		</tr>
		<?php
	}
	
	/**
	 * Save category icon and color
	 */
	public function save_category_icon( $term_id ) {
		// Don't process if term is being deleted
		if ( isset( $_POST['action'] ) && $_POST['action'] === 'delete-tag' ) {
			return;
		}
		
		// Don't process if this is a bulk delete action
		if ( isset( $_POST['delete_tags'] ) || isset( $_POST['action'] ) && $_POST['action'] === 'bulk-delete' ) {
			return;
		}
		
		// Verify term still exists
		$term = get_term( $term_id, 'main_category' );
		if ( ! $term || is_wp_error( $term ) ) {
			return;
		}
		
		if ( isset( $_POST['category_icon'] ) ) {
			$icon = sanitize_text_field( $_POST['category_icon'] );
			if ( ! empty( $icon ) ) {
				update_term_meta( $term_id, 'category_icon', $icon );
			} else {
				delete_term_meta( $term_id, 'category_icon' );
			}
		}
		
		if ( isset( $_POST['category_color'] ) ) {
			$color = sanitize_text_field( $_POST['category_color'] );
			if ( ! empty( $color ) ) {
				update_term_meta( $term_id, 'category_color', $color );
			} else {
				delete_term_meta( $term_id, 'category_color' );
			}
		}
	}
}

