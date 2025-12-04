<?php
/**
 * Search Results Template
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

get_header();
?>

<div class="main-content-layout">
	<div class="container">
		<div class="main-content-area">
			<div class="container-inner">
				<?php global $wp_query; ?>
				
				<header class="page-header search-results-header">
					<div class="search-results-icon">
						<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="11" cy="11" r="8"></circle>
							<path d="m21 21-4.35-4.35"></path>
						</svg>
					</div>
					<h1 class="page-title search-results-title">
						<?php
						printf(
							/* translators: %s: search query. */
							esc_html__( 'Search Results for: %s', 'sme-insights' ),
							'<span class="search-query-highlight">' . esc_html( get_search_query() ) . '</span>'
						);
						?>
					</h1>
					<?php if ( $wp_query->found_posts > 0 ) : ?>
						<div class="search-results-count">
							<span class="results-number"><?php echo number_format_i18n( $wp_query->found_posts ); ?></span>
							<span class="results-text">
								<?php
								printf(
									/* translators: %d: number of results. */
									esc_html( _n( 'result found', 'results found', $wp_query->found_posts, 'sme-insights' ) ),
									number_format_i18n( $wp_query->found_posts )
								);
								?>
							</span>
						</div>
					<?php else : ?>
						<p class="search-no-results-message"><?php esc_html_e( 'No results found. Try different keywords.', 'sme-insights' ); ?></p>
					<?php endif; ?>
				</header>

				<?php if ( have_posts() ) : ?>
					<div class="blog-posts-grid">
						<?php while ( have_posts() ) : the_post(); ?>
							<article class="blog-post-card">
								<?php if ( has_post_thumbnail() ) : ?>
									<a href="<?php echo esc_url( get_permalink() ); ?>">
										<?php the_post_thumbnail( 'sme-medium', array( 
											'alt' => get_the_title(),
											'class' => 'blog-post-image',
											'loading' => 'lazy'
										) ); ?>
									</a>
								<?php else : ?>
									<a href="<?php echo esc_url( get_permalink() ); ?>" class="blog-post-image-placeholder" style="display: flex; align-items: center; justify-content: center; background-color: var(--bg-secondary); height: 200px; width: 100%; border-radius: 8px; color: var(--accent-secondary);">
										<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
											<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
											<polyline points="14 2 14 8 20 8"></polyline>
											<line x1="16" y1="13" x2="8" y2="13"></line>
											<line x1="16" y1="17" x2="8" y2="17"></line>
											<polyline points="10 9 9 9 8 9"></polyline>
										</svg>
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
										<?php echo esc_html( get_bloginfo( 'name' ) ); ?> • <?php echo esc_html( get_the_date( 'F j, Y' ) ); ?>
									</div>
									<?php 
									$custom_excerpt = get_post_meta( get_the_ID(), '_sme_custom_excerpt', true );
									$excerpt = $custom_excerpt ?: get_the_excerpt();
									?>
									<p class="blog-post-excerpt">
										<?php echo esc_html( wp_trim_words( $excerpt, 25 ) ); ?>
									</p>
								</div>
							</article>
						<?php endwhile; ?>
					</div>

					<!-- Pagination -->
					<div class="blog-pagination">
						<?php
						the_posts_pagination( array(
							'mid_size'  => 2,
							'prev_text' => __( 'Previous', 'sme-insights' ),
							'next_text' => __( 'Next', 'sme-insights' ),
						) );
						?>
					</div>
				<?php else : ?>
					<div class="search-no-results">
						<div class="search-no-results-icon">
							<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<circle cx="11" cy="11" r="8"></circle>
								<path d="m21 21-4.35-4.35"></path>
							</svg>
						</div>
						<h2 class="search-no-results-title"><?php esc_html_e( 'Nothing Found', 'sme-insights' ); ?></h2>
						<p class="search-no-results-text">
							<?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'sme-insights' ); ?>
						</p>
						<div class="search-no-results-form">
							<?php get_search_form(); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
