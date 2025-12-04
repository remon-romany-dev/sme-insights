<?php
/**
 * Taxonomy Template for main_category
 * 
 * This template displays category archive pages.
 * Category content can be customized via WordPress Customizer.
 * 
 * HOW TO EDIT:
 * 1. Appearance > Customize > Category Templates
 * 2. Edit category descriptions in Posts > Categories
 * 3. Customize category colors and icons via theme settings
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

<div class="main-content-layout" style="padding: 10px 0;">
	<div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">
		<div class="main-content-area" style="width: 100%;">
			<div class="container-inner" style="max-width: 1400px; margin: 0 auto; padding: 0;">
				<?php if ( $term ) : ?>
					<!-- Category Hero Section -->
					<section class="category-hero" style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); padding: 60px 40px; margin: 0 0 50px 0; text-align: center; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 8px 25px rgba(0,0,0,0.05);">
						<div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; margin-bottom: 25px; background: linear-gradient(135deg, <?php echo esc_attr( $category_color ); ?> 0%, var(--accent-secondary) 100%); border-radius: 50%; box-shadow: 0 6px 20px rgba(37, 99, 235, 0.2);">
							<?php if ( $category_icon === '$' ) : ?>
								<svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #fff;">
									<path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							<?php elseif ( $category_icon === '🏠' ) : ?>
								<svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #fff;">
									<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									<polyline points="9 22 9 12 15 12 15 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							<?php elseif ( $category_icon === '💻' ) : ?>
								<svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #fff;">
									<rect x="2" y="7" width="20" height="14" rx="2" ry="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							<?php elseif ( $category_icon === '📈' ) : ?>
								<svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #fff;">
									<polyline points="23 6 13.5 15.5 8.5 10.5 1 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									<polyline points="17 6 23 6 23 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							<?php elseif ( $category_icon === '📋' ) : ?>
								<svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #fff;">
									<path d="M12 2L2 7l10 5 10-5-10-5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							<?php else : ?>
								<div style="font-size: 2.5rem; color: #fff;">
									<?php echo esc_html( $category_icon ); ?>
								</div>
							<?php endif; ?>
						</div>
						<h1 style="font-size: 3rem; font-weight: 700; margin: 0 0 20px; color: var(--text-primary); line-height: 1.2;">
							<?php echo esc_html( $term->name ); ?>
						</h1>
						<div style="width: 80px; height: 3px; background: linear-gradient(135deg, <?php echo esc_attr( $category_color ); ?> 0%, var(--accent-secondary) 100%); margin: 0 auto 25px; border-radius: 2px;"></div>
						<?php if ( $term->description ) : ?>
							<p style="font-size: 1.15rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto 20px; line-height: 1.7;">
								<?php echo esc_html( $term->description ); ?>
							</p>
						<?php endif; ?>
						<div style="display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 1.1rem; color: var(--text-secondary); margin-top: 20px;">
							<span style="font-weight: 600; color: <?php echo esc_attr( $category_color ); ?>;">
								<?php echo number_format( $term->count ); ?>
							</span>
							<span><?php echo _n( 'Article', 'Articles', $term->count, 'sme-insights' ); ?></span>
						</div>
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
						// Double-check: Verify this post actually has the current category
						$featured_categories = get_the_terms( get_the_ID(), 'main_category' );
						$featured_has_category = false;
						
						if ( $featured_categories && ! is_wp_error( $featured_categories ) ) {
							foreach ( $featured_categories as $feat_cat ) {
								if ( $feat_cat->term_id == $term->term_id ) {
									$featured_has_category = true;
									break;
								}
							}
						}
						
						// Only display if post has the current category
						if ( $featured_has_category ) :
							$featured_id = get_the_ID();
					?>
						<section class="featured-article-section" style="margin: 40px 0 50px 0;">
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
						endif; // End featured_has_category check
						wp_reset_postdata();
					endif; // End featured_query->have_posts()
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
					<div class="blog-posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; margin-top: 0;">
						<?php
						// Exclude featured post from main grid
						$exclude_ids = isset( $featured_id ) ? array( $featured_id ) : array();
						
						// Get posts for this category
						$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
						
						$posts_per_page = 6;
						$posts_query = new WP_Query( array(
							'post_type'      => 'post',
							'posts_per_page' => $posts_per_page,
							'post_status'    => 'publish',
							'paged'          => $paged,
							'post__not_in'   => $exclude_ids,
							'no_found_rows'  => false, // Need count for pagination
							'update_post_meta_cache' => true,
							'update_post_term_cache' => true, // Need terms for display
							'tax_query'      => array(
								array(
									'taxonomy' => 'main_category',
									'field'    => 'term_id',
									'terms'    => $term->term_id,
									'operator' => 'IN',
								),
							),
							'orderby'        => 'date',
							'order'          => 'DESC',
						) );
						
						// Calculate max pages from query results
						$total_found = intval( $posts_query->found_posts );
						if ( $total_found > 0 ) {
							$max_pages = ceil( $total_found / $posts_per_page );
						} else {
							$max_pages = 0;
						}
						
						// Redirect if page number exceeds available pages
						if ( $paged > $max_pages && $max_pages > 0 ) {
							// Redirect to last available page
							if ( $max_pages > 1 ) {
								$term_link = get_term_link( $term );
								$permalink_structure = get_option( 'permalink_structure' );
								if ( ! empty( $permalink_structure ) ) {
									$term_link_clean = rtrim( $term_link, '/' );
									$redirect_url = $term_link_clean . '/page/' . $max_pages . '/';
								} else {
									$redirect_url = add_query_arg( 'paged', $max_pages, $term_link );
								}
								wp_redirect( $redirect_url );
							} else {
								wp_redirect( get_term_link( $term ) );
							}
							exit;
						}
						
						if ( $posts_query->have_posts() ) :
							while ( $posts_query->have_posts() ) : $posts_query->the_post();
								// Double-check: Verify this post actually has the current category
								$post_categories = get_the_terms( get_the_ID(), 'main_category' );
								$has_current_category = false;
								
								if ( $post_categories && ! is_wp_error( $post_categories ) ) {
									foreach ( $post_categories as $post_cat ) {
										if ( $post_cat->term_id == $term->term_id ) {
											$has_current_category = true;
											break;
										}
									}
								}
								
								// Only display if post has the current category
								if ( ! $has_current_category ) {
									continue;
								}
						?>
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
						<?php 
							endwhile;
							wp_reset_postdata();
						endif;
						?>
					</div>

					<!-- Pagination -->
					<?php 
					// Use calculated max_pages (calculated from separate count query for accuracy)
					$max_pages_display = isset( $max_pages ) ? $max_pages : 0;
					// Ensure max_pages is valid
					if ( $max_pages_display <= 0 && isset( $posts_query ) ) {
						$total_found = intval( $posts_query->found_posts );
						$max_pages_display = $total_found > 0 ? ceil( $total_found / 6 ) : 0;
					}
					// Only show pagination if there's more than 1 page
					if ( $max_pages_display > 1 ) : ?>
						<div class="blog-pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 50px; padding: 20px 0;">
							<?php
							$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
							$max   = $max_pages_display;
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
							
							// Page numbers - ensure we don't show more pages than available
							// Double-check max pages from the actual query
							$actual_max = isset( $posts_query ) ? intval( $posts_query->max_num_pages ) : $max;
							$final_max = min( $max, $actual_max ); // Use the smaller value to be safe
							
							for ( $i = 1; $i <= $final_max; $i++ ) {
								// Only show page if it's within the valid range
								if ( $i == 1 || $i == $final_max || ( $i >= $paged - 2 && $i <= $paged + 2 && $i <= $final_max ) ) {
									$class = ( $i == $paged ) ? 'pagination-btn active' : 'pagination-btn';
									$page_url = $get_page_url( $i );
									echo '<a href="' . esc_url( $page_url ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $i ) . '</a>';
								} elseif ( ( $i == $paged - 3 && $i > 1 ) || ( $i == $paged + 3 && $i < $final_max ) ) {
									echo '<span class="pagination-btn" style="pointer-events: none;">...</span>';
								}
							}
							
							// Next button - use final_max to ensure we don't show next for non-existent pages
							if ( $paged < $final_max ) {
								$next_url = $get_page_url( $paged + 1 );
								echo '<a href="' . esc_url( $next_url ) . '" class="pagination-btn">' . esc_html__( 'Next', 'sme-insights' ) . '</a>';
							}
							?>
						</div>
					<?php endif; ?>

					<!-- Popular Articles Section -->
					<?php
					// Get current page for pagination
					$popular_paged = isset( $_GET['popular_page'] ) ? max( 1, intval( $_GET['popular_page'] ) ) : 1;
					
					// Exclude featured post and posts already shown in main grid
					$exclude_for_popular = isset( $featured_id ) ? array( $featured_id ) : array();
					if ( isset( $posts_query ) && $posts_query->have_posts() ) {
						$main_grid_ids = wp_list_pluck( $posts_query->posts, 'ID' );
						$exclude_for_popular = array_merge( $exclude_for_popular, $main_grid_ids );
					}
					
					$popular_query = new WP_Query( array(
						'post_type'      => 'post',
						'posts_per_page' => 3,
						'paged'           => $popular_paged,
						'post_status'    => 'publish',
						'post__not_in'   => $exclude_for_popular,
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
							'paged'           => $popular_paged,
							'post_status'    => 'publish',
							'post__not_in'   => $exclude_for_popular,
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
						<section class="popular-articles-section" style="margin: 60px 0 40px; position: relative;">
							<h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 30px; color: var(--text-primary);">Popular in <?php echo esc_html( $term->name ); ?></h2>
							<div class="popular-slider-wrapper" style="position: relative;">
								<div class="popular-slider-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; overflow: hidden;">
									<?php while ( $popular_query->have_posts() ) : $popular_query->the_post(); ?>
										<article class="popular-slide" style="background: var(--bg-card); border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s;">
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
								
								<!-- Slider Navigation Arrows -->
								<?php if ( $popular_query->max_num_pages > 1 ) : ?>
									<div class="popular-slider-nav" style="display: flex; justify-content: center; gap: 15px; margin-top: 30px; align-items: center;">
										<?php
										$popular_max = intval( $popular_query->max_num_pages );
										
										// Previous arrow button
										if ( $popular_paged > 1 ) {
											$prev_url = add_query_arg( 'popular_page', $popular_paged - 1, get_term_link( $term ) );
											echo '<a href="' . esc_url( $prev_url ) . '" class="slider-arrow slider-arrow-prev" style="width: 50px; height: 50px; border-radius: 50%; background: var(--accent-primary); color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.15);" aria-label="Previous">';
											echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>';
											echo '</a>';
										} else {
											echo '<span class="slider-arrow slider-arrow-prev disabled" style="width: 50px; height: 50px; border-radius: 50%; background: #e0e0e0; color: #999; display: flex; align-items: center; justify-content: center; cursor: not-allowed; opacity: 0.5;">';
											echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>';
											echo '</span>';
										}
										
										// Next arrow button
										if ( $popular_paged < $popular_max ) {
											$next_url = add_query_arg( 'popular_page', $popular_paged + 1, get_term_link( $term ) );
											echo '<a href="' . esc_url( $next_url ) . '" class="slider-arrow slider-arrow-next" style="width: 50px; height: 50px; border-radius: 50%; background: var(--accent-primary); color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.15);" aria-label="Next">';
											echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>';
											echo '</a>';
										} else {
											echo '<span class="slider-arrow slider-arrow-next disabled" style="width: 50px; height: 50px; border-radius: 50%; background: #e0e0e0; color: #999; display: flex; align-items: center; justify-content: center; cursor: not-allowed; opacity: 0.5;">';
											echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>';
											echo '</span>';
										}
										?>
									</div>
								<?php endif; ?>
							</div>
						</section>
					<?php 
						wp_reset_postdata();
					endif;
					?>
				<?php else : ?>
					<p><?php _e( 'Category not found.', 'sme-insights' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();

