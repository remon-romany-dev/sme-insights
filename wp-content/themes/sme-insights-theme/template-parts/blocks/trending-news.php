<?php
/**
 * Template Part: Trending News Block
 *
 * @package SME_Insights
 * @since 1.0.0
 */

// Query for trending posts
// Always show trending news - use most recent posts if no comments
// Exclude already displayed posts
$displayed_ids = sme_get_displayed_post_ids();

// Start with date-based query (most recent)
$args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 4,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'ignore_sticky_posts' => true,
	'post__not_in'   => $displayed_ids,
	'no_found_rows'  => true,
	'update_post_meta_cache' => true,
	'update_post_term_cache' => false,
	'meta_query'     => array(
		array(
			'key'     => '_thumbnail_id',
			'compare' => 'EXISTS',
		),
	),
);

// Try to get posts with comments first (if available)
$args_with_comments = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 4,
	'orderby'        => 'comment_count',
	'order'          => 'DESC',
	'ignore_sticky_posts' => true,
	'post__not_in'   => $displayed_ids,
	'no_found_rows'  => true,
	'update_post_meta_cache' => true,
	'update_post_term_cache' => false,
	'meta_query'     => array(
		array(
			'key'     => '_thumbnail_id',
			'compare' => 'EXISTS',
		),
	),
);

$trending_news_query = new WP_Query( $args_with_comments );

// If no posts with comments, fallback to date-based query
if ( ! $trending_news_query->have_posts() ) {
	$args['orderby'] = 'date';
	$args['order'] = 'DESC';
	$trending_news_query = new WP_Query( $args );
}

// If still no posts, remove post__not_in to ensure we show something
if ( ! $trending_news_query->have_posts() ) {
	$args_fallback = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'ignore_sticky_posts' => true,
		'no_found_rows'  => true, // Performance: Skip pagination count
		'update_post_meta_cache' => true,
		'update_post_term_cache' => false, // Performance: Skip term cache if not needed
		'meta_query'     => array(
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'EXISTS',
			),
		),
	);
	$trending_news_query = new WP_Query( $args_fallback );
}

if ( $trending_news_query->have_posts() ) :
	?>
	<div class="trending-news-new">
		<h2 class="section-title">Trending News</h2>
		<ul class="trending-news-list">
			<?php
			while ( $trending_news_query->have_posts() ) : $trending_news_query->the_post();
				// Mark this post as displayed
				sme_mark_post_displayed( get_the_ID() );
				?>
				<li class="trending-news-item">
					<?php if ( has_post_thumbnail() ) : 
						$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
						// Use custom size to match display size and reduce file size
						$image_url = wp_get_attachment_image_url( $thumbnail_id, 'sme-trending-small' );
						if ( ! $image_url ) {
							$image_url = wp_get_attachment_image_url( $thumbnail_id, array( 150, 100 ) );
						}
						$thumbnail_info = wp_get_attachment_image_src( $thumbnail_id, 'sme-trending-small' );
						if ( ! $thumbnail_info ) {
							$thumbnail_info = wp_get_attachment_image_src( $thumbnail_id, array( 150, 100 ) );
						}
						$width = $thumbnail_info ? $thumbnail_info[1] : 150;
						$height = $thumbnail_info ? $thumbnail_info[2] : 100;
						$srcset = wp_get_attachment_image_srcset( $thumbnail_id, 'sme-trending-small' );
						if ( ! $srcset ) {
							$srcset = wp_get_attachment_image_srcset( $thumbnail_id, array( 150, 100 ) );
						}
						$sizes = '(max-width: 768px) 102px, 150px';
					?>
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="trending-news-image" width="<?php echo esc_attr( $width ); ?>" height="<?php echo esc_attr( $height ); ?>" loading="lazy" decoding="async"<?php if ( $srcset ) : ?> srcset="<?php echo esc_attr( $srcset ); ?>" sizes="<?php echo esc_attr( $sizes ); ?>"<?php endif; ?>>
					<?php endif; ?>
					<div class="trending-news-content">
						<h3 class="trending-news-title">
							<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
						</h3>
						<div class="trending-news-meta"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></div>
					</div>
				</li>
			<?php
			endwhile;
			wp_reset_postdata();
			?>
		</ul>
	</div>
	<?php
endif;
?>

