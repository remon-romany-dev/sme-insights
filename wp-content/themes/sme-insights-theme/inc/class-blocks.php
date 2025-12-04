<?php
/**
 * Custom Gutenberg Blocks
 * Handles block registration and rendering for all theme blocks
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Blocks {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		add_filter( 'block_categories_all', array( $this, 'register_block_category' ), 10, 2 );
	}
	
	/**
	 * Register custom blocks
	 */
	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		
		// Register all custom blocks
		$this->register_posts_grid_block();
		$this->register_category_hero_block();
		$this->register_featured_article_block();
		$this->register_sub_topics_bar_block();
		$this->register_popular_articles_block();
	}
	
	/**
	 * Register Posts Grid Block
	 */
	private function register_posts_grid_block() {
		register_block_type( 'sme-insights/posts-grid', array(
			'render_callback' => array( $this, 'render_posts_grid_block' ),
			'attributes' => array(
				'postsPerPage' => array(
					'type' => 'number',
					'default' => 6,
				),
				'category' => array(
					'type' => 'string',
					'default' => '',
				),
				'columns' => array(
					'type' => 'number',
					'default' => 3,
				),
			),
		) );
	}
	
	/**
	 * Register Category Hero Block
	 */
	private function register_category_hero_block() {
		register_block_type( 'sme-insights/category-hero', array(
			'render_callback' => array( $this, 'render_category_hero_block' ),
			'attributes' => array(
				'title' => array(
					'type' => 'string',
					'default' => '',
				),
				'description' => array(
					'type' => 'string',
					'default' => '',
				),
				'icon' => array(
					'type' => 'string',
					'default' => '',
				),
				'color' => array(
					'type' => 'string',
					'default' => '#2563eb',
				),
			),
		) );
	}
	
	/**
	 * Register Featured Article Block
	 */
	private function register_featured_article_block() {
		register_block_type( 'sme-insights/featured-article', array(
			'render_callback' => array( $this, 'render_featured_article_block' ),
			'attributes' => array(
				'postId' => array(
					'type' => 'number',
					'default' => 0,
				),
				'category' => array(
					'type' => 'string',
					'default' => '',
				),
			),
		) );
	}
	
	/**
	 * Register Sub Topics Bar Block
	 */
	private function register_sub_topics_bar_block() {
		register_block_type( 'sme-insights/sub-topics-bar', array(
			'render_callback' => array( $this, 'render_sub_topics_bar_block' ),
			'attributes' => array(
				'categoryColor' => array(
					'type' => 'string',
					'default' => '#2563eb',
				),
			),
		) );
	}
	
	/**
	 * Register Popular Articles Block
	 */
	private function register_popular_articles_block() {
		register_block_type( 'sme-insights/popular-articles', array(
			'render_callback' => array( $this, 'render_popular_articles_block' ),
			'attributes' => array(
				'postsPerPage' => array(
					'type' => 'number',
					'default' => 3,
				),
				'category' => array(
					'type' => 'string',
					'default' => '',
				),
				'title' => array(
					'type' => 'string',
					'default' => 'Popular Articles',
				),
			),
		) );
	}
	
	/**
	 * Register custom block category
	 */
	public function register_block_category( $categories, $editor_context ) {
		return array_merge(
			array(
				array(
					'slug'  => 'sme-insights',
					'title' => __( 'SME Insights Blocks', 'sme-insights' ),
					'icon'  => 'admin-post',
				),
			),
			$categories
		);
	}
	
	/**
	 * Enqueue block editor assets
	 */
	public function enqueue_block_editor_assets() {
		// Check if we're in widgets editor or customize widgets (WordPress 5.8+)
		$is_widgets_editor = ( function_exists( 'wp_use_widgets_block_editor' ) && wp_use_widgets_block_editor() );
		$is_customize_widgets = ( isset( $_GET['customize-widgets'] ) && ! empty( sanitize_text_field( $_GET['customize-widgets'] ) ) );
		
		// Use wp-block-editor instead of deprecated wp-editor (WordPress 5.8+)
		$dependencies = array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components' );
		
		// Only add wp-block-editor if not in widgets editor
		if ( ! $is_widgets_editor && ! $is_customize_widgets ) {
			$dependencies[] = 'wp-block-editor';
		}
		
		wp_enqueue_script(
			'sme-insights-blocks',
			SME_THEME_ASSETS . '/js/blocks.js',
			$dependencies,
			SME_THEME_VERSION,
			true
		);
		
		wp_enqueue_style(
			'sme-insights-blocks-editor',
			SME_THEME_ASSETS . '/css/blocks-editor.css',
			array( 'wp-edit-blocks' ),
			SME_THEME_VERSION
		);
	}
	
	/**
	 * Render Posts Grid Block
	 */
	public function render_posts_grid_block( $attributes ) {
		$posts_per_page = isset( $attributes['postsPerPage'] ) ? $attributes['postsPerPage'] : 6;
		$category = isset( $attributes['category'] ) ? $attributes['category'] : '';
		$columns = isset( $attributes['columns'] ) ? $attributes['columns'] : 3;
		
		ob_start();
		get_template_part( 'template-parts/blocks/posts-grid' );
		return ob_get_clean();
	}
	
	/**
	 * Render Category Hero Block
	 */
	public function render_category_hero_block( $attributes ) {
		$title = isset( $attributes['title'] ) ? $attributes['title'] : '';
		$description = isset( $attributes['description'] ) ? $attributes['description'] : '';
		$icon = isset( $attributes['icon'] ) ? $attributes['icon'] : '';
		$color = isset( $attributes['color'] ) ? $attributes['color'] : '#2563eb';
		
		ob_start();
		?>
		<section class="category-hero" style="background: #fff; padding: 40px 40px 60px 40px; margin: 10px 0 40px 0; text-align: center; border-bottom: 1px solid var(--border-color);">
			<?php if ( $icon ) : ?>
				<div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; margin-bottom: 20px;">
					<?php if ( $icon === '$' ) : ?>
						<svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: <?php echo esc_attr( $color ); ?>;">
							<path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					<?php else : ?>
						<div style="font-size: 3rem; color: <?php echo esc_attr( $color ); ?>;"><?php echo esc_html( $icon ); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( $title ) : ?>
				<h1 style="font-size: 3rem; font-weight: 700; margin: 0 0 15px; color: <?php echo esc_attr( $color ); ?>; line-height: 1.2;">
					<?php echo esc_html( $title ); ?>
				</h1>
				<div style="width: 80px; height: 3px; background: <?php echo esc_attr( $color ); ?>; margin: 0 auto 20px; border-radius: 2px;"></div>
			<?php endif; ?>
			<?php if ( $description ) : ?>
				<p style="font-size: 1.2rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto; line-height: 1.6;">
					<?php echo esc_html( $description ); ?>
				</p>
			<?php endif; ?>
		</section>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * Render Featured Article Block
	 */
	public function render_featured_article_block( $attributes ) {
		$post_id = isset( $attributes['postId'] ) ? $attributes['postId'] : 0;
		$category = isset( $attributes['category'] ) ? $attributes['category'] : '';
		
		ob_start();
		// Use existing template part
		get_template_part( 'template-parts/blocks/featured-article' );
		return ob_get_clean();
	}
	
	/**
	 * Render Sub Topics Bar Block
	 */
	public function render_sub_topics_bar_block( $attributes ) {
		$category_color = isset( $attributes['categoryColor'] ) ? $attributes['categoryColor'] : '#2563eb';
		
		ob_start();
		$sub_topics = get_terms( array(
			'taxonomy'   => 'sub_topic',
			'hide_empty' => true,
			'number'     => 10,
		) );
		
		if ( ! empty( $sub_topics ) && ! is_wp_error( $sub_topics ) ) :
		?>
			<section class="sub-topics-bar" style="margin: 40px 0;">
				<div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center; padding: 20px; background: var(--bg-secondary); border-radius: 12px;">
					<?php foreach ( $sub_topics as $sub_topic ) : ?>
						<a href="<?php echo esc_url( get_term_link( $sub_topic ) ); ?>" 
						   style="padding: 12px 24px; background: transparent; color: <?php echo esc_attr( $category_color ); ?>; border: 2px solid <?php echo esc_attr( $category_color ); ?>; text-decoration: none; border-radius: 25px; font-weight: 600; font-size: 0.95rem; transition: all 0.3s; white-space: nowrap; font-family: inherit;">
							<?php echo esc_html( $sub_topic->name ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php
		endif;
		return ob_get_clean();
	}
	
	/**
	 * Render Popular Articles Block
	 */
	public function render_popular_articles_block( $attributes ) {
		$posts_per_page = isset( $attributes['postsPerPage'] ) ? $attributes['postsPerPage'] : 3;
		$category = isset( $attributes['category'] ) ? $attributes['category'] : '';
		$title = isset( $attributes['title'] ) ? $attributes['title'] : 'Popular Articles';
		
		ob_start();
		// Use existing template logic
		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => $posts_per_page,
			'post_status'    => 'publish',
			'orderby'        => 'comment_count',
			'order'          => 'DESC',
		);
		
		if ( $category ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'main_category',
					'field'    => 'slug',
					'terms'    => $category,
				),
			);
		}
		
		$query = new WP_Query( $args );
		
		if ( $query->have_posts() ) :
		?>
			<section class="popular-articles-section" style="margin: 60px 0 40px;">
				<h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 30px; color: var(--text-primary);"><?php echo esc_html( $title ); ?></h2>
				<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<article style="background: var(--bg-card); border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s;">
							<?php if ( has_post_thumbnail() ) : ?>
								<a href="<?php echo esc_url( get_permalink() ); ?>">
									<?php the_post_thumbnail( 'sme-medium', array( 
										'alt' => get_the_title(),
										'loading' => 'lazy',
										'style' => 'width: 100%; height: 200px; object-fit: cover; display: block;'
									) ); ?>
								</a>
							<?php endif; ?>
							<div style="padding: 20px;">
								<?php
								$categories = get_the_terms( get_the_ID(), 'main_category' );
								if ( $categories && ! is_wp_error( $categories ) && isset( $categories[0] ) ) :
									$cat_color = SME_Helpers::get_category_color( $categories[0]->term_id );
								?>
									<span style="background: <?php echo esc_attr( $cat_color ); ?>; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; display: inline-block; margin-bottom: 12px;">
										<?php echo esc_html( $categories[0]->name ); ?>
									</span>
								<?php endif; ?>
								<h3 style="font-size: 1.2rem; font-weight: 700; margin: 0 0 10px; line-height: 1.4;">
									<a href="<?php echo esc_url( get_permalink() ); ?>" style="color: var(--text-primary); text-decoration: none; transition: color 0.3s;">
										<?php echo esc_html( get_the_title() ); ?>
									</a>
								</h3>
								<div style="font-size: 0.85rem; color: var(--text-light);">
									<?php echo esc_html( get_bloginfo( 'name' ) ); ?> • <?php echo esc_html( get_the_date( 'F j, Y' ) ); ?> • <?php echo number_format( get_post_meta( get_the_ID(), 'post_views_count', true ) ?: 0 ); ?> views
								</div>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
			</section>
		<?php
		endif;
		wp_reset_postdata();
		return ob_get_clean();
	}
}
