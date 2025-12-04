<?php
/**
 * Template Part: Hero Slider Block
 *
 * @package SME_Insights
 * @since 1.0.0
 */

// Query for featured posts with thumbnails
// Exclude already displayed posts
$displayed_ids = sme_get_displayed_post_ids();
$args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 3,
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

$hero_slider_query = new WP_Query( $args );

// Always show Main News section, even if no posts
?>
<div class="main-news-new">
<?php if ( $hero_slider_query->have_posts() ) : ?>
		<h2 class="section-title">Main News</h2>
		<div class="slider-container">
			<div class="slider-wrapper">
				<div class="slider-track" id="mainSlider">
					<?php
					while ( $hero_slider_query->have_posts() ) : $hero_slider_query->the_post();
						// Mark this post as displayed
						sme_mark_post_displayed( get_the_ID() );
						$categories = get_the_terms( get_the_ID(), 'main_category' );
						?>
						<div class="slider-item">
							<?php if ( has_post_thumbnail() ) : 
								$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
								$image_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
								$srcset = wp_get_attachment_image_srcset( $thumbnail_id, 'large' );
								$sizes = '(max-width: 768px) 100vw, (max-width: 1200px) 800px, 1200px';
								$image_meta = wp_get_attachment_metadata( $thumbnail_id );
								$width = isset( $image_meta['width'] ) ? $image_meta['width'] : 1200;
								$height = isset( $image_meta['height'] ) ? $image_meta['height'] : 410;
							?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="slider-image" loading="eager" fetchpriority="high" width="<?php echo esc_attr( $width ); ?>" height="<?php echo esc_attr( $height ); ?>"<?php if ( $srcset ) : ?> srcset="<?php echo esc_attr( $srcset ); ?>" sizes="<?php echo esc_attr( $sizes ); ?>"<?php endif; ?>>
							<?php endif; ?>
							<div class="slider-overlay">
								<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
									<div class="slider-categories">
										<?php foreach ( array_slice( $categories, 0, 2 ) as $category ) : ?>
											<span class="slider-category"><?php echo esc_html( $category->name ); ?></span>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
								<h3 class="slider-title">
									<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
								</h3>
								<div class="slider-meta"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> • <?php echo esc_html( get_the_date( 'F j, Y' ) ); ?> • <?php echo esc_html( get_comments_number() ); ?></div>
							</div>
						</div>
					<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
			<div class="slider-controls">
				<button class="slider-btn" onclick="prevSlide()" aria-label="Previous slide">‹</button>
				<button class="slider-btn" onclick="nextSlide()" aria-label="Next slide">›</button>
			</div>
			<div class="slider-dots" id="mainSliderDots"></div>
		</div>
<?php else : ?>
	<!-- Fallback: Show message if no posts -->
	<div class="slider-container">
		<div class="slider-wrapper">
			<div class="slider-track" id="mainSlider">
				<div class="slider-item">
					<div style="background: var(--bg-secondary); padding: 100px 20px; text-align: center; border-radius: 8px;">
						<p style="color: var(--text-secondary); font-size: 1.1rem;">No posts available yet. Please add some posts with featured images.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
</div>

