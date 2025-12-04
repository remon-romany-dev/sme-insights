<?php
/**
 * Template Part: Main News Mobile Block
 * Displays only the first post for mobile devices
 *
 * @package SME_Insights
 * @since 1.0.0
 */

// Query for the first post (most recent)
// Exclude already displayed posts
$displayed_ids = sme_get_displayed_post_ids();
$args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 1,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'ignore_sticky_posts' => true,
	'post__not_in'   => $displayed_ids, // Exclude already displayed posts
	'no_found_rows'  => true,
	'update_post_meta_cache' => true,
	'update_post_term_cache' => true, // Need terms for categories
	'meta_query'     => array(
		array(
			'key'     => '_thumbnail_id',
			'compare' => 'EXISTS',
		),
	),
);

$main_news_query = new WP_Query( $args );

// Only show if we have a post
if ( $main_news_query->have_posts() ) :
	while ( $main_news_query->have_posts() ) : $main_news_query->the_post();
		// Mark this post as displayed
		sme_mark_post_displayed( get_the_ID() );
		$categories = get_the_terms( get_the_ID(), 'main_category' );
		$category_color = $categories && ! is_wp_error( $categories ) && isset( $categories[0] ) ? SME_Helpers::get_category_color( $categories[0]->term_id ) : '#2563eb';
		?>
		<section class="main-news-mobile">
			<h2 class="main-news-mobile-title">Main News</h2>
			<article class="main-news-mobile-card">
				<?php if ( has_post_thumbnail() ) : 
					$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
					// Use custom mobile size to reduce file size on mobile devices
					$image_url = wp_get_attachment_image_url( $thumbnail_id, 'sme-mobile-main' );
					if ( ! $image_url ) {
						$image_url = wp_get_attachment_image_url( $thumbnail_id, 'medium' );
					}
					$thumbnail_info = wp_get_attachment_image_src( $thumbnail_id, 'sme-mobile-main' );
					if ( ! $thumbnail_info ) {
						$thumbnail_info = wp_get_attachment_image_src( $thumbnail_id, 'medium' );
					}
					$width = $thumbnail_info ? $thumbnail_info[1] : 400;
					$height = $thumbnail_info ? $thumbnail_info[2] : 225;
					$srcset = wp_get_attachment_image_srcset( $thumbnail_id, 'sme-mobile-main' );
					if ( ! $srcset ) {
						$srcset = wp_get_attachment_image_srcset( $thumbnail_id, 'medium' );
					}
					$sizes = '(max-width: 768px) 100vw, 400px';
				?>
					<a href="<?php echo esc_url( get_permalink() ); ?>" class="main-news-mobile-image-link">
						<?php 
						echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title() ) . '" class="main-news-mobile-image" width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '" loading="eager" decoding="async" fetchpriority="high"';
						if ( $srcset ) {
							echo ' srcset="' . esc_attr( $srcset ) . '" sizes="' . esc_attr( $sizes ) . '"';
						}
						echo '>';
						?>
					</a>
				<?php endif; ?>
				
				<div class="main-news-mobile-content">
					<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
						<div class="main-news-mobile-categories">
							<?php foreach ( array_slice( $categories, 0, 1 ) as $cat ) : 
								$color = SME_Helpers::get_category_color( $cat->term_id );
							?>
								<span class="main-news-mobile-category" style="background: <?php echo esc_attr( $color ); ?>; color: #fff;">
									<?php echo esc_html( $cat->name ); ?>
								</span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					
					<h3 class="main-news-mobile-heading">
						<a href="<?php echo esc_url( get_permalink() ); ?>">
							<?php the_title(); ?>
						</a>
					</h3>
					
					<div class="main-news-mobile-meta">
						<span><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></span>
						<span>•</span>
						<span><?php echo number_format( get_comments_number() ); ?> comments</span>
					</div>
					
					<?php 
					$custom_excerpt = get_post_meta( get_the_ID(), '_sme_custom_excerpt', true );
					$excerpt = $custom_excerpt ? $custom_excerpt : ( has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 25, '...' ) );
					if ( $excerpt ) :
					?>
						<p class="main-news-mobile-excerpt">
							<?php echo esc_html( $excerpt ); ?>
						</p>
					<?php endif; ?>
					
					<a href="<?php echo esc_url( get_permalink() ); ?>" class="main-news-mobile-read-more">
						Read More →
					</a>
				</div>
			</article>
		</section>
		<?php
	endwhile;
	wp_reset_postdata();
endif;
?>

