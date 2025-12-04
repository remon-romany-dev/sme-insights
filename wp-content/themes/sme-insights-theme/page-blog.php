<?php
/**
 * Template Name: Blog Page with Gallery
 * 
 * This template displays all blog posts with a gallery at the top.
 * 
 * HOW TO USE:
 * 1. Go to Pages > Add New
 * 2. Create a new page (e.g., "Blog")
 * 3. In Page Attributes, select "Blog Page with Gallery" template
 * 4. Publish the page
 * 5. Add the page to your menu
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

get_header();

// Get current page title
global $post;
$page_title = '';
if ( $post && isset( $post->post_title ) && ! empty( $post->post_title ) ) {
	$page_title = $post->post_title;
} else {
	$page_title = 'Business News & Insights';
}

// Get all posts
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$posts_query = new WP_Query( array(
	'post_type'      => 'post',
	'posts_per_page' => 9,
	'post_status'    => 'publish',
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
) );
?>

<div class="main-content-layout">
	<!-- Blog Hero -->
	<div class="blog-hero">
		<div class="container-inner">
			<h1><?php echo esc_html( $page_title ); ?></h1>
			<?php 
			// Get page content
			$page_content = '';
			if ( $post && isset( $post->post_content ) && ! empty( trim( $post->post_content ) ) ) {
				$page_content = apply_filters( 'the_content', $post->post_content );
			}
			
			if ( ! empty( $page_content ) ) : 
			?>
				<p><?php echo wp_strip_all_tags( $page_content ); ?></p>
			<?php else : ?>
				<p>Discover the latest insights, trends, and expert analysis from our team of business professionals.</p>
			<?php endif; ?>
		</div>
	</div>

	<!-- Main Content -->
	<div class="container">
		<div class="main-content-area">
			<div class="container-inner" style="max-width: 1400px; margin: 0 auto; padding: 0 20px; width: 100%;">
				<!-- Category Filter -->
				<?php
				// Get all main categories
				$categories = get_terms( array(
					'taxonomy'   => 'main_category',
					'hide_empty' => true,
				) );
				
				if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
				?>
					<section class="blog-category-filter" style="margin: 0 auto 40px auto; padding: 20px; background: var(--bg-secondary); border-radius: 12px; max-width: 1400px; width: 100%;">
						<div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center; align-items: center;">
							<button class="category-filter-btn active" data-category="all" style="padding: 12px 24px; background: var(--accent-primary); color: #fff; border: none; border-radius: 25px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.3s;">
								All Categories
							</button>
							<?php foreach ( $categories as $category ) : 
								$color = SME_Helpers::get_category_color( $category->term_id );
							?>
								<button class="category-filter-btn" data-category="<?php echo esc_attr( $category->term_id ); ?>" style="padding: 12px 24px; background: transparent; color: <?php echo esc_attr( $color ); ?>; border: 2px solid <?php echo esc_attr( $color ); ?>; border-radius: 25px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.3s; white-space: nowrap;">
									<?php echo esc_html( $category->name ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endif; ?>

				<!-- Blog Posts Grid -->
				<div class="blog-posts-grid" id="blog-posts-container">
					<?php
					if ( $posts_query->have_posts() ) :
						while ( $posts_query->have_posts() ) : $posts_query->the_post();
							// Get post categories for filtering
							$post_categories = get_the_terms( get_the_ID(), 'main_category' );
							$category_ids = array();
							if ( $post_categories && ! is_wp_error( $post_categories ) ) {
								foreach ( $post_categories as $cat ) {
									$category_ids[] = $cat->term_id;
								}
							}
					?>
						<article class="blog-post-card" data-categories="<?php echo esc_attr( implode( ',', $category_ids ) ); ?>">
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
									if ( $post_categories && ! is_wp_error( $post_categories ) ) {
										foreach ( array_slice( $post_categories, 0, 2 ) as $cat ) {
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
					else :
					?>
						<p style="text-align: center; padding: 40px; color: var(--text-secondary);">
							<?php _e( 'No posts found.', 'sme-insights' ); ?>
						</p>
					<?php endif; ?>
				</div>
				
				<!-- No Posts Message (hidden by default) -->
				<div id="no-posts-message" style="display: none; text-align: center; padding: 40px; color: var(--text-secondary);">
					<p><?php _e( 'No posts found in this category.', 'sme-insights' ); ?></p>
				</div>

				<!-- Pagination -->
				<?php if ( $posts_query->max_num_pages > 1 ) : ?>
					<div class="blog-pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 50px; padding: 20px 0;">
						<?php
						$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
						$max   = intval( $posts_query->max_num_pages );
						$blog_page_url = get_permalink( get_option( 'page_for_posts' ) );
						if ( ! $blog_page_url ) {
							// Fallback to current page URL
							global $wp;
							$blog_page_url = home_url( $wp->request );
							// Remove paged from URL if exists
							$blog_page_url = preg_replace( '/\/page\/\d+\/?$/', '', $blog_page_url );
							$blog_page_url = trailingslashit( $blog_page_url );
						}
						
						// Previous button
						if ( $paged > 1 ) {
							if ( $paged == 2 ) {
								$prev_url = $blog_page_url;
							} else {
								$prev_url = $blog_page_url . 'page/' . ( $paged - 1 ) . '/';
							}
							echo '<a href="' . esc_url( $prev_url ) . '" class="pagination-btn">' . esc_html__( 'Previous', 'sme-insights' ) . '</a>';
						}
						
						// Page numbers
						for ( $i = 1; $i <= $max; $i++ ) {
							if ( $i == 1 || $i == $max || ( $i >= $paged - 2 && $i <= $paged + 2 ) ) {
								$class = ( $i == $paged ) ? 'pagination-btn active' : 'pagination-btn';
								if ( $i == 1 ) {
									$page_url = $blog_page_url;
								} else {
									$page_url = $blog_page_url . 'page/' . $i . '/';
								}
								echo '<a href="' . esc_url( $page_url ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $i ) . '</a>';
							} elseif ( $i == $paged - 3 || $i == $paged + 3 ) {
								echo '<span class="pagination-btn" style="pointer-events: none;">...</span>';
							}
						}
						
						// Next button
						if ( $paged < $max ) {
							$next_url = $blog_page_url . 'page/' . ( $paged + 1 ) . '/';
							echo '<a href="' . esc_url( $next_url ) . '" class="pagination-btn">' . esc_html__( 'Next', 'sme-insights' ) . '</a>';
						}
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();

