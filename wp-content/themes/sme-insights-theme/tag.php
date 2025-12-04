<?php
/**
 * Tag Archive Template
 * 
 * This template displays tag archive pages.
 *
 * @package SME_Insights
 * @since 1.1.0
 * @author Remon Romany
 */

get_header();

$term = get_queried_object();
?>

<div class="main-content-layout">
	<div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">
		<div class="main-content-area" style="width: 100%;">
			<div class="container-inner" style="max-width: 1400px; margin: 0 auto; padding: 0;">
				<?php if ( $term ) : ?>
					<!-- Tag Hero Section -->
					<section class="tag-hero" style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); padding: 60px 40px; margin: 0 0 50px 0; text-align: center; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 8px 25px rgba(0,0,0,0.05);">
						<div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; margin-bottom: 25px; background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%); border-radius: 50%; box-shadow: 0 6px 20px rgba(37, 99, 235, 0.2);">
							<span style="font-size: 2.5rem;">🏷️</span>
						</div>
						<h1 style="font-size: 3rem; font-weight: 700; margin: 0 0 20px; color: var(--text-primary); line-height: 1.2;">
							<?php echo esc_html( $term->name ); ?>
						</h1>
						<div style="width: 80px; height: 3px; background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%); margin: 0 auto 25px; border-radius: 2px;"></div>
						<?php if ( $term->description ) : ?>
							<p style="font-size: 1.15rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto 20px; line-height: 1.7;">
								<?php echo esc_html( $term->description ); ?>
							</p>
						<?php endif; ?>
						<div style="display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 1.1rem; color: var(--text-secondary); margin-top: 20px;">
							<span style="font-weight: 600; color: var(--accent-primary);">
								<?php echo number_format( $term->count ); ?>
							</span>
							<span><?php echo _n( 'Article', 'Articles', $term->count, 'sme-insights' ); ?></span>
						</div>
					</section>

					<!-- Tag Posts Grid -->
					<section class="tag-posts-section">
						<div class="blog-posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; margin-top: 0;">
							<?php if ( have_posts() ) : ?>
								<?php while ( have_posts() ) : the_post(); ?>
									<article class="blog-post-card" style="background: var(--bg-card); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s;">
										<?php if ( has_post_thumbnail() ) : ?>
											<a href="<?php echo esc_url( get_permalink() ); ?>" style="display: block; overflow: hidden;">
												<?php the_post_thumbnail( 'sme-medium', array( 
													'alt' => get_the_title(),
													'class' => 'blog-post-image',
													'style' => 'width: 100%; height: 220px; object-fit: cover; transition: transform 0.3s;',
													'loading' => 'lazy'
												) ); ?>
											</a>
										<?php endif; ?>
										<div class="blog-post-content" style="padding: 25px;">
											<div class="blog-post-categories" style="display: flex; gap: 8px; margin-bottom: 15px; flex-wrap: wrap;">
												<?php
												$categories = get_the_terms( get_the_ID(), 'main_category' );
												if ( $categories && ! is_wp_error( $categories ) ) {
													foreach ( array_slice( $categories, 0, 2 ) as $cat ) {
														$color = SME_Helpers::get_category_color( $cat->term_id );
														echo '<span class="blog-post-category" style="background: ' . esc_attr( $color ) . '; color: #fff; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">' . esc_html( $cat->name ) . '</span>';
													}
												}
												?>
											</div>
											<h2 class="blog-post-title" style="font-size: 1.4rem; font-weight: 700; margin: 0 0 12px; line-height: 1.3;">
												<a href="<?php echo esc_url( get_permalink() ); ?>" style="color: var(--text-primary); text-decoration: none; transition: color 0.3s;">
													<?php echo esc_html( get_the_title() ); ?>
												</a>
											</h2>
											<div class="blog-post-meta" style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 15px;">
												<?php echo esc_html( get_the_date( 'F j, Y' ) ); ?> • <?php echo number_format( get_post_meta( get_the_ID(), 'post_views_count', true ) ?: 0 ); ?> views
											</div>
											<?php 
											$custom_excerpt = get_post_meta( get_the_ID(), '_sme_custom_excerpt', true );
											$excerpt = $custom_excerpt ?: get_the_excerpt();
											?>
											<p class="blog-post-excerpt" style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.6; margin: 0;">
												<?php echo esc_html( wp_trim_words( $excerpt, 20 ) ); ?>
											</p>
										</div>
									</article>
								<?php endwhile; ?>
							<?php else : ?>
								<div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
									<p style="font-size: 1.2rem; color: var(--text-secondary);"><?php _e( 'No posts found for this tag.', 'sme-insights' ); ?></p>
								</div>
							<?php endif; ?>
						</div>
						
						<?php 
						// Reset post data before pagination
						wp_reset_postdata();
						if ( have_posts() ) : ?>
							<div class="blog-pagination" style="margin-top: 50px; text-align: center;">
								<?php
								the_posts_pagination( array(
									'mid_size'  => 2,
									'prev_text' => __( '← Previous', 'sme-insights' ),
									'next_text' => __( 'Next →', 'sme-insights' ),
								) );
								?>
							</div>
						<?php endif; ?>
					</section>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
?>

