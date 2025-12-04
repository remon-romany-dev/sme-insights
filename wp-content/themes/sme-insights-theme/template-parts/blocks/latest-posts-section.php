<?php
/**
 * Template Part: Latest Posts Section Block
 *
 * @package SME_Insights
 * @since 1.0.0
 */
?>

<section class="latest-posts-section">
	<div class="latest-posts-wrapper">
		<!-- Left Column - Latest Posts (70%) -->
		<div class="latest-posts-column">
			<h2 class="section-title">Latest From SME Insights</h2>
			<div class="latest-posts-list">
				<?php
				// Exclude already displayed posts
				$displayed_ids = sme_get_displayed_post_ids();
				$latest_args = array(
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => 2,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'ignore_sticky_posts' => true,
					'post__not_in'   => $displayed_ids, // Exclude already displayed posts
					'meta_query'     => array(
						array(
							'key'     => '_thumbnail_id',
							'compare' => 'EXISTS',
						),
					),
				);
				$latest_query = new WP_Query( $latest_args );

				if ( $latest_query->have_posts() ) :
					while ( $latest_query->have_posts() ) : $latest_query->the_post();
						// Mark this post as displayed
						sme_mark_post_displayed( get_the_ID() );
						$categories = get_the_terms( get_the_ID(), 'main_category' );
						?>
						<div class="latest-post-item">
							<?php if ( has_post_thumbnail() ) : 
								$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
								$image_meta = wp_get_attachment_metadata( $thumbnail_id );
								$image_url = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
								$width = isset( $image_meta['width'] ) ? $image_meta['width'] : 300;
								$height = isset( $image_meta['height'] ) ? $image_meta['height'] : 200;
							?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="latest-post-image" width="<?php echo esc_attr( $width ); ?>" height="<?php echo esc_attr( $height ); ?>" loading="lazy" decoding="async">
							<?php endif; ?>
							<div class="latest-post-content">
								<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
									<span class="latest-post-category"><?php echo esc_html( $categories[0]->name ); ?></span>
								<?php endif; ?>
								<h3 class="latest-post-title">
									<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
								</h3>
								<p class="latest-post-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
								<div class="latest-post-meta"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?> • <?php echo esc_html( get_comments_number() ); ?> views</div>
							</div>
						</div>
					<?php
					endwhile;
					wp_reset_postdata();
				endif;
				?>
			</div>
			<!-- View All Insights Button -->
			<div class="view-all-cta" style="margin-top: 40px; margin-bottom: 5px; text-align: center; padding: 30px; background: var(--bg-secondary); border-radius: 8px;">
				<p style="font-size: 16px; color: var(--text-secondary); margin-bottom: 15px;">Explore more insights and articles</p>
				<?php
				// Get Blog page URL - try multiple methods
				$blog_page_url = '';
				
				// Method 1: Try to find page with 'blog' slug
				$blog_page = get_page_by_path( 'blog' );
				if ( $blog_page ) {
					$blog_page_url = get_permalink( $blog_page->ID );
				} else {
					$blog_page = sme_get_page_by_title( 'Business News & Insights' );
					if ( $blog_page ) {
						$blog_page_url = get_permalink( $blog_page->ID );
					} else {
						// Method 3: Search for page using page-blog.php template
						$pages = get_pages( array(
							'meta_key'   => '_wp_page_template',
							'meta_value' => 'page-blog.php',
							'number'     => 1,
						) );
						if ( ! empty( $pages ) ) {
							$blog_page_url = get_permalink( $pages[0]->ID );
						} else {
							// Method 4: Fallback to posts archive
							$blog_page_url = get_post_type_archive_link( 'post' );
						}
					}
				}
				?>
				<a href="<?php echo esc_url( $blog_page_url ); ?>" class="view-all-button" style="display: inline-block; background: var(--accent-secondary); color: #fff; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: 600; font-size: 14px; transition: all 0.3s ease;">View All Insights</a>
			</div>
		</div>

		<!-- Right Column - Most Popular (30%) -->
		<div class="most-popular-column">
			<h2 class="section-title">Most Popular</h2>
			<ul class="most-popular-list">
				<?php
				$popular_args = array(
					'post_type'      => 'post',
					'posts_per_page' => 5,
					'orderby'        => 'comment_count',
					'order'          => 'DESC',
					'ignore_sticky_posts' => true,
				);
				$popular_query = new WP_Query( $popular_args );

				if ( $popular_query->have_posts() ) :
					$count = 1;
					while ( $popular_query->have_posts() ) : $popular_query->the_post();
						?>
						<li class="most-popular-item">
							<span class="most-popular-number"><?php echo esc_html( $count ); ?></span>
							<?php if ( has_post_thumbnail() ) : ?>
								<img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="most-popular-image" loading="lazy">
							<?php endif; ?>
							<div class="most-popular-content">
								<h3 class="most-popular-title">
									<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
								</h3>
								<div class="most-popular-meta"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?> • <?php echo esc_html( get_comments_number() ); ?> views</div>
							</div>
						</li>
					<?php
						$count++;
					endwhile;
					wp_reset_postdata();
				endif;
				?>
			</ul>
		</div>
	</div>
</section>

