<?php
/**
 * Template Part: Featured Insights Block
 *
 * @package SME_Insights
 * @since 1.0.0
 */

// Query for featured insights posts
// Exclude already displayed posts
$displayed_ids = sme_get_displayed_post_ids();

// First, try to get 4 featured posts
$args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 4,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'ignore_sticky_posts' => true,
	'post__not_in'   => $displayed_ids, // Exclude already displayed posts
	'no_found_rows'  => true,
	'update_post_meta_cache' => true,
	'update_post_term_cache' => true, // Need terms for categories
	'meta_query'     => array(
		'relation' => 'AND',
		array(
			'key'     => '_sme_is_featured',
			'value'   => '1',
			'compare' => '=',
		),
		array(
			'key'     => '_thumbnail_id',
			'compare' => 'EXISTS',
		),
	),
);

$featured_insights_query = new WP_Query( $args );

// If we don't have 4 posts, try to get more (without featured requirement if needed)
if ( $featured_insights_query->post_count < 4 ) {
	$needed = 4 - $featured_insights_query->post_count;
	$current_ids = array();
	
	// Get IDs of current featured posts
	if ( $featured_insights_query->have_posts() ) {
		while ( $featured_insights_query->have_posts() ) {
			$featured_insights_query->the_post();
			$current_ids[] = get_the_ID();
		}
		wp_reset_postdata();
	}
	
	// Get additional posts (can be non-featured if needed, but must have thumbnail)
	$additional_args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => $needed,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'ignore_sticky_posts' => true,
		'post__not_in'   => array_merge( $displayed_ids, $current_ids ),
		'no_found_rows'  => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
		'meta_query'     => array(
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'EXISTS',
			),
		),
	);
	
	$additional_query = new WP_Query( $additional_args );
	
	// If we got additional posts, merge them
	if ( $additional_query->have_posts() && $additional_query->post_count > 0 ) {
		// Get all post IDs from both queries
		$all_post_ids = array_merge( $current_ids, wp_list_pluck( $additional_query->posts, 'ID' ) );
		// Limit to 4
		$all_post_ids = array_slice( $all_post_ids, 0, 4 );
		
		// Create new query with merged post IDs
		$merged_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 4,
			'post__in'       => $all_post_ids,
			'orderby'        => 'post__in', // Maintain order
			'ignore_sticky_posts' => true,
			'no_found_rows'  => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		);
		
		$featured_insights_query = new WP_Query( $merged_args );
	} elseif ( count( $current_ids ) < 4 ) {
		// If still don't have 4, try without excluding displayed posts (last resort)
		$fallback_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 4,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'ignore_sticky_posts' => true,
			'post__not_in'   => $current_ids, // Only exclude current featured posts
			'no_found_rows'  => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
			'meta_query'     => array(
				array(
					'key'     => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			),
		);
		
		$fallback_query = new WP_Query( $fallback_args );
		
		if ( $fallback_query->have_posts() && $fallback_query->post_count > 0 ) {
			$all_post_ids = array_merge( $current_ids, wp_list_pluck( $fallback_query->posts, 'ID' ) );
			$all_post_ids = array_slice( $all_post_ids, 0, 4 );
			
			$merged_args = array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 4,
				'post__in'       => $all_post_ids,
				'orderby'        => 'post__in',
				'ignore_sticky_posts' => true,
				'no_found_rows'  => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => true,
			);
			
			$featured_insights_query = new WP_Query( $merged_args );
		}
	}
}

if ( $featured_insights_query->have_posts() ) :
	?>
	<section class="featured-insights-section">
		<h2 class="section-title">Featured Insights</h2>
		<div class="featured-insights-grid">
			<?php
			while ( $featured_insights_query->have_posts() ) : $featured_insights_query->the_post();
				// Mark this post as displayed
				sme_mark_post_displayed( get_the_ID() );
				$categories = get_the_terms( get_the_ID(), 'main_category' );
				?>
				<div class="featured-insight-item">
					<?php if ( has_post_thumbnail() ) : ?>
						<img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="featured-insight-image" loading="lazy">
					<?php endif; ?>
					<div class="featured-insight-overlay">
						<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
							<span class="featured-insight-category"><?php echo esc_html( $categories[0]->name ); ?></span>
						<?php endif; ?>
						<h3 class="featured-insight-title">
							<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
						</h3>
						<div class="featured-insight-meta"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></div>
					</div>
				</div>
			<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</section>
	<?php
endif;
?>

