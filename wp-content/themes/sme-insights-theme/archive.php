<?php
/**
 * Archive Template - Category Pages
 * Matches finance-page.html design exactly
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

get_header();

$term = get_queried_object();
$category_color = $term && isset( $term->term_id ) ? SME_Helpers::get_category_color( $term->term_id ) : '#2563eb';
$category_icon = $term && isset( $term->term_id ) ? SME_Helpers::get_category_icon( $term->term_id ) : '📄';
?>

<div class="main-content-layout">
	<div class="container">
		<div class="main-content-area">
			<div class="container-inner">
				<?php if ( is_tax( 'main_category' ) && $term ) : ?>
					<!-- Category Gallery Section -->
					<?php
					// Get latest posts with featured images for gallery
					$gallery_posts = get_posts( array(
						'post_type'      => 'post',
						'posts_per_page' => 8,
						'post_status'    => 'publish',
						'tax_query'      => array(
							array(
								'taxonomy' => 'main_category',
								'field'    => 'term_id',
								'terms'    => $term->term_id,
							),
						),
						'meta_query'     => array(
							array(
								'key'     => '_thumbnail_id',
								'compare' => 'EXISTS',
							),
						),
						'orderby'        => 'date',
						'order'          => 'DESC',
					) );
					
					if ( ! empty( $gallery_posts ) ) :
					?>
						<section class="category-gallery" style="margin: 0 0 50px 0;">
							<div class="category-gallery-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px;">
								<?php foreach ( array_slice( $gallery_posts, 0, 8 ) as $gallery_post ) : 
									$thumbnail_id = get_post_thumbnail_id( $gallery_post->ID );
									if ( $thumbnail_id ) :
								?>
									<a href="<?php echo esc_url( get_permalink( $gallery_post->ID ) ); ?>" class="category-gallery-item" style="position: relative; display: block; overflow: hidden; border-radius: 12px; aspect-ratio: 1; transition: transform 0.3s, box-shadow 0.3s;">
										<?php echo wp_get_attachment_image( $thumbnail_id, 'medium', false, array( 
											'style' => 'width: 100%; height: 100%; object-fit: cover; display: block;',
											'alt' => get_the_title( $gallery_post->ID )
										) ); ?>
										<div class="gallery-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 50%); opacity: 0; transition: opacity 0.3s; display: flex; align-items: flex-end; padding: 15px;">
											<h3 style="color: #fff; font-size: 0.9rem; font-weight: 600; margin: 0; line-height: 1.3;">
												<?php echo esc_html( wp_trim_words( get_the_title( $gallery_post->ID ), 8 ) ); ?>
											</h3>
										</div>
									</a>
								<?php 
									endif;
								endforeach; 
								?>
							</div>
						</section>
					<?php endif; ?>
					
					<!-- Category Hero Section -->
					<section class="category-hero" style="background: #fff; padding: 40px 40px 60px 40px; margin: 0 0 40px 0; text-align: center; border-bottom: 1px solid var(--border-color);">
						<div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; margin-bottom: 20px;">
							<?php if ( $category_icon === '$' ) : ?>
								<svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: <?php echo esc_attr( $category_color ); ?>;">
									<path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							<?php else : ?>
								<div style="font-size: 3rem; color: <?php echo esc_attr( $category_color ); ?>;">
									<?php echo $category_icon; ?>
								</div>
							<?php endif; ?>
						</div>
						<h1 style="font-size: 3rem; font-weight: 700; margin: 0 0 15px; color: <?php echo esc_attr( $category_color ); ?>;">
							<?php echo esc_html( $term->name ); ?>
						</h1>
						<div style="width: 80px; height: 3px; background: <?php echo esc_attr( $category_color ); ?>; margin: 0 auto 20px; border-radius: 2px;"></div>
						<?php if ( $term->description ) : ?>
							<p style="font-size: 1.2rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto; line-height: 1.6;">
								<?php echo esc_html( $term->description ); ?>
							</p>
						<?php endif; ?>
					</section>

					<!-- Featured Article Section -->
					<?php
					$featured_query = new WP_Query( array(
						'post_type'      => 'post',
						'posts_per_page' => 1,
						'post_status'    => 'publish',
						'tax_query'      => array(
							array(
								'taxonomy' => 'main_category',
								'field'    => 'term_id',
								'terms'    => $term->term_id,
							),
						),
						'meta_query'     => array(
							array(
								'key'     => '_sme_is_featured',
								'value'   => '1',
								'compare' => '=',
							),
						),
						'orderby'        => 'date',
						'order'          => 'DESC',
					) );
					
					// If no featured post, get the latest post
					if ( ! $featured_query->have_posts() ) {
						wp_reset_postdata();
						$featured_query = new WP_Query( array(
							'post_type'      => 'post',
							'posts_per_page' => 1,
							'post_status'    => 'publish',
							'tax_query'      => array(
								array(
									'taxonomy' => 'main_category',
									'field'    => 'term_id',
									'terms'    => $term->term_id,
								),
							),
							'orderby'        => 'date',
							'order'          => 'DESC',
						) );
					}
					
					if ( $featured_query->have_posts() ) :
						$featured_query->the_post();
						$featured_id = get_the_ID();
					?>
						<section class="featured-article-section" style="margin: 40px 0;">
							<article class="featured-article" style="display: flex; gap: 30px; background: var(--bg-card); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
								<?php if ( has_post_thumbnail() ) : ?>
									<div style="flex: 1; min-width: 0;">
										<a href="<?php echo esc_url( get_permalink() ); ?>">
											<?php the_post_thumbnail( 'large', array( 
												'alt' => get_the_title(),
												'style' => 'width: 100%; height: 100%; object-fit: cover; display: block; min-height: 300px;'
											) ); ?>
										</a>
									</div>
								<?php endif; ?>
								<div style="flex: 1; padding: 40px; display: flex; flex-direction: column; justify-content: center;">
									<div style="display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap;">
										<span style="background: <?php echo esc_attr( $category_color ); ?>; color: #fff; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
											<?php echo esc_html( $term->name ); ?>
										</span>
										<?php if ( get_post_meta( get_the_ID(), '_sme_is_featured', true ) === '1' ) : ?>
											<span style="background: var(--bg-secondary); color: var(--text-primary); padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Featured</span>
										<?php endif; ?>
									</div>
									<h2 style="font-size: 2rem; font-weight: 700; margin: 0 0 15px; color: var(--text-primary); line-height: 1.3;">
										<a href="<?php echo esc_url( get_permalink() ); ?>" style="color: inherit; text-decoration: none; transition: color 0.3s;">
											<?php echo esc_html( get_the_title() ); ?>
										</a>
									</h2>
									<?php 
									$custom_excerpt = get_post_meta( get_the_ID(), '_sme_custom_excerpt', true );
									$excerpt = $custom_excerpt ?: get_the_excerpt();
									?>
									<p style="font-size: 1.1rem; color: var(--text-secondary); margin: 0 0 20px; line-height: 1.6;">
										<?php echo esc_html( wp_trim_words( $excerpt, 25 ) ); ?>
									</p>
									<div style="font-size: 0.9rem; color: var(--text-light);">
										<?php echo esc_html( get_bloginfo( 'name' ) ); ?> • <?php echo esc_html( get_the_date( 'F j, Y' ) ); ?> • <?php echo number_format( get_post_meta( get_the_ID(), 'post_views_count', true ) ?: 0 ); ?> views
									</div>
								</div>
							</article>
						</section>
					<?php 
						wp_reset_postdata();
					endif;
					?>

					<!-- Sub-Topics Bar -->
					<?php
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
									   style="padding: 12px 24px; background: transparent; color: <?php echo esc_attr( $category_color ); ?>; border: 2px solid <?php echo esc_attr( $category_color ); ?>; text-decoration: none; border-radius: 25px; font-weight: 600; font-size: 0.95rem; transition: all 0.3s; white-space: nowrap;">
										<?php echo esc_html( $sub_topic->name ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						</section>
					<?php endif; ?>
					
					<!-- Blog Posts Grid -->
					<div class="blog-posts-grid">
						<?php
						// Exclude featured post from main grid
						$exclude_ids = isset( $featured_id ) ? array( $featured_id ) : array();
						
						// Get posts for this category
						$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
						$posts_query = new WP_Query( array(
							'post_type'      => 'post',
							'posts_per_page' => 6,
							'post_status'    => 'publish',
							'paged'          => $paged,
							'post__not_in'   => $exclude_ids,
							'tax_query'      => array(
								array(
									'taxonomy' => 'main_category',
									'field'    => 'term_id',
									'terms'    => $term->term_id,
								),
							),
							'orderby'        => 'date',
							'order'          => 'DESC',
						) );
						
						if ( $posts_query->have_posts() ) :
							while ( $posts_query->have_posts() ) : $posts_query->the_post();
						?>
							<article class="blog-post-card">
								<?php if ( has_post_thumbnail() ) : ?>
									<a href="<?php echo esc_url( get_permalink() ); ?>">
										<?php the_post_thumbnail( 'sme-medium', array( 
											'alt' => get_the_title(),
											'class' => 'blog-post-image',
											'loading' => 'lazy'
										) ); ?>
									</a>
								<?php endif; ?>
								<div class="blog-post-content">
									<div class="blog-post-categories">
										<?php
										$categories = get_the_terms( get_the_ID(), 'main_category' );
										if ( $categories && ! is_wp_error( $categories ) ) {
											foreach ( array_slice( $categories, 0, 2 ) as $cat ) {
												$color = SME_Helpers::get_category_color( $cat->term_id );
												echo '<span class="blog-post-category" style="background: ' . esc_attr( $color ) . '; color: #fff;">' . esc_html( $cat->name ) . '</span>';
											}
										}
										?>
									</div>
									<h2 class="blog-post-title">
										<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
									</h2>
									<div class="blog-post-meta">
										<?php echo esc_html( get_bloginfo( 'name' ) ); ?> • <?php echo esc_html( get_the_date( 'F j, Y' ) ); ?> • <?php echo number_format( get_post_meta( get_the_ID(), 'post_views_count', true ) ?: 0 ); ?> views
									</div>
									<?php 
									$custom_excerpt = get_post_meta( get_the_ID(), '_sme_custom_excerpt', true );
									$excerpt = $custom_excerpt ?: get_the_excerpt();
									?>
									<p class="blog-post-excerpt">
										<?php echo esc_html( wp_trim_words( $excerpt, 20 ) ); ?>
									</p>
								</div>
							</article>
						<?php 
							endwhile;
							wp_reset_postdata();
						endif;
						?>
					</div>

					<!-- Pagination -->
					<?php if ( $posts_query->max_num_pages > 1 ) : ?>
						<div class="blog-pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 50px; padding: 20px 0;">
							<?php
							$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
							$max   = intval( $posts_query->max_num_pages );
							$term_link = get_term_link( $term );
							
							// Check if permalink structure uses /page/X/ format
							$permalink_structure = get_option( 'permalink_structure' );
							$use_pretty_permalinks = ! empty( $permalink_structure );
							
							// Helper function to get page URL
							$get_page_url = function( $page_num ) use ( $term_link, $use_pretty_permalinks ) {
								if ( $page_num == 1 ) {
									return $term_link;
								}
								if ( $use_pretty_permalinks ) {
									// Remove trailing slash if exists, add /page/X/
									$term_link_clean = rtrim( $term_link, '/' );
									return $term_link_clean . '/page/' . $page_num . '/';
								} else {
									return add_query_arg( 'paged', $page_num, $term_link );
								}
							};
							
							// Previous button
							if ( $paged > 1 ) {
								$prev_url = $get_page_url( $paged - 1 );
								echo '<a href="' . esc_url( $prev_url ) . '" class="pagination-btn">' . esc_html__( 'Previous', 'sme-insights' ) . '</a>';
							}
							
							// Page numbers
							for ( $i = 1; $i <= $max; $i++ ) {
								if ( $i == 1 || $i == $max || ( $i >= $paged - 2 && $i <= $paged + 2 ) ) {
									$class = ( $i == $paged ) ? 'pagination-btn active' : 'pagination-btn';
									$page_url = $get_page_url( $i );
									echo '<a href="' . esc_url( $page_url ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $i ) . '</a>';
								} elseif ( $i == $paged - 3 || $i == $paged + 3 ) {
									echo '<span class="pagination-btn" style="pointer-events: none;">...</span>';
								}
							}
							
							// Next button
							if ( $paged < $max ) {
								$next_url = $get_page_url( $paged + 1 );
								echo '<a href="' . esc_url( $next_url ) . '" class="pagination-btn">' . esc_html__( 'Next', 'sme-insights' ) . '</a>';
							}
							?>
						</div>
					<?php endif; ?>

					<!-- Popular Articles Section -->
					<?php
					$popular_query = new WP_Query( array(
						'post_type'      => 'post',
						'posts_per_page' => 3,
						'post_status'    => 'publish',
						'tax_query'      => array(
							array(
								'taxonomy' => 'main_category',
								'field'    => 'term_id',
								'terms'    => $term->term_id,
							),
						),
						'orderby'        => 'comment_count',
						'order'          => 'DESC',
					) );
					
					// Fallback to date if no comments
					if ( ! $popular_query->have_posts() || ( $popular_query->have_posts() && get_comments_number( $popular_query->posts[0]->ID ) == 0 ) ) {
						wp_reset_postdata();
						$popular_query = new WP_Query( array(
							'post_type'      => 'post',
							'posts_per_page' => 3,
							'post_status'    => 'publish',
							'tax_query'      => array(
								array(
									'taxonomy' => 'main_category',
									'field'    => 'term_id',
									'terms'    => $term->term_id,
								),
							),
							'orderby'        => 'date',
							'order'          => 'DESC',
						) );
					}
					
					if ( $popular_query->have_posts() ) :
					?>
						<section class="popular-articles-section" style="margin: 60px 0 40px;">
							<h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 30px; color: var(--text-primary);">Popular in <?php echo esc_html( $term->name ); ?></h2>
							<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
								<?php while ( $popular_query->have_posts() ) : $popular_query->the_post(); ?>
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
											<span style="background: <?php echo esc_attr( $category_color ); ?>; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; display: inline-block; margin-bottom: 12px;">
												<?php echo esc_html( $term->name ); ?>
											</span>
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
						wp_reset_postdata();
					endif;
					?>
				<?php elseif ( is_tag() && $term ) : ?>
					<!-- Tag Archive Page -->
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
						
						<?php if ( have_posts() ) : ?>
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
				<?php else : ?>
					<!-- Default Archive (if not main_category or tag) -->
					<section class="posts-grid-section">
						<div class="posts-grid posts-grid-3-cols">
							<?php if ( have_posts() ) : ?>
								<?php while ( have_posts() ) : the_post(); ?>
									<article class="post-card">
										<?php if ( has_post_thumbnail() ) : ?>
											<a href="<?php echo esc_url( get_permalink() ); ?>" class="post-image">
												<?php the_post_thumbnail( 'sme-thumbnail', array( 'alt' => get_the_title(), 'loading' => 'lazy' ) ); ?>
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
						</div>
						
						<?php
						// Pagination
						the_posts_pagination( array(
							'mid_size'  => 2,
							'prev_text' => __( 'Previous', 'sme-insights' ),
							'next_text' => __( 'Next', 'sme-insights' ),
						) );
						?>
					</section>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
