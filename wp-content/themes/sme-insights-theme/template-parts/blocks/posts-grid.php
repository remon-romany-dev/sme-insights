<?php
/**
 * Posts Grid Block Template
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get section data from query var
$section_data = get_query_var( 'section_data', array() );

$section_title = isset( $section_data['section_title'] ) ? $section_data['section_title'] : 'Latest Posts';
$columns = isset( $section_data['grid_columns'] ) ? $section_data['grid_columns'] : '3';
$posts_count = isset( $section_data['grid_posts_count'] ) ? intval( $section_data['grid_posts_count'] ) : 9;
$category = isset( $section_data['grid_category'] ) ? $section_data['grid_category'] : '';
$show_pagination = isset( $section_data['grid_pagination'] ) && $section_data['grid_pagination'] == '1';

// Query posts
$args = array(
	'post_type'      => 'post',
	'posts_per_page' => $posts_count,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
	'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
);

if ( $category ) {
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'main_category',
			'field'    => 'term_id',
			'terms'    => intval( $category ),
		),
	);
}

$query = new WP_Query( $args );
?>

<section class="posts-grid-section" data-columns="<?php echo esc_attr( $columns ); ?>">
	<?php if ( $section_title ) : ?>
		<h2 class="section-title"><?php echo esc_html( $section_title ); ?></h2>
	<?php endif; ?>
	
	<div class="posts-grid posts-grid-<?php echo esc_attr( $columns ); ?>-cols">
		<?php if ( $query->have_posts() ) : ?>
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<article class="post-card">
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php echo esc_url( get_permalink() ); ?>" class="post-image">
							<?php the_post_thumbnail( 'sme-thumbnail', array( 
								'alt' => get_the_title(),
								'loading' => 'lazy'
							) ); ?>
						</a>
					<?php endif; ?>
					<div class="post-content">
						<div class="post-categories">
							<?php
							$categories = get_the_terms( get_the_ID(), 'main_category' );
							if ( $categories && ! is_wp_error( $categories ) ) {
								foreach ( array_slice( $categories, 0, 1 ) as $cat ) {
									$color = SME_Helpers::get_category_color( $cat->term_id );
									echo '<span class="post-category" style="color: ' . esc_attr( $color ) . ';">' . esc_html( $cat->name ) . '</span>';
								}
							}
							?>
						</div>
						<h3 class="post-title">
							<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
						</h3>
						<div class="post-meta">
							<span><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></span>
							<span>•</span>
							<span><?php echo get_comments_number(); ?> comments</span>
						</div>
						<?php 
						$custom_excerpt = get_post_meta( get_the_ID(), '_sme_custom_excerpt', true );
						if ( has_excerpt() || $custom_excerpt ) : 
						?>
							<p class="post-excerpt">
								<?php 
								$excerpt = $custom_excerpt ?: get_the_excerpt();
								echo esc_html( wp_trim_words( $excerpt, 20 ) ); 
								?>
							</p>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<p><?php _e( 'No posts found.', 'sme-insights' ); ?></p>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>
	
	<?php if ( $show_pagination && $query->max_num_pages > 1 ) : ?>
		<div class="posts-pagination">
			<?php
			echo paginate_links( array(
				'total'     => $query->max_num_pages,
				'current'   => max( 1, get_query_var( 'paged' ) ),
				'prev_text' => __( 'Previous', 'sme-insights' ),
				'next_text' => __( 'Next', 'sme-insights' ),
			) );
			?>
		</div>
	<?php endif; ?>
</section>

