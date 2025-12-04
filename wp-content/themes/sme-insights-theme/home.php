<?php
/**
 * Blog Page Template - All Posts with Gallery
 * Displays all blog posts with a gallery at the top
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
				<!-- Blog Gallery Section -->
				<?php
				// Get latest posts with featured images for gallery
				$gallery_posts = get_posts( array(
					'post_type'      => 'post',
					'posts_per_page' => 8,
					'post_status'    => 'publish',
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
					<section class="blog-gallery" style="margin: 0 0 50px 0;">
						<div class="blog-gallery-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px;">
							<?php foreach ( array_slice( $gallery_posts, 0, 8 ) as $gallery_post ) : 
								$thumbnail_id = get_post_thumbnail_id( $gallery_post->ID );
								if ( $thumbnail_id ) :
							?>
								<a href="<?php echo esc_url( get_permalink( $gallery_post->ID ) ); ?>" class="blog-gallery-item" style="position: relative; display: block; overflow: hidden; border-radius: 12px; aspect-ratio: 1; transition: transform 0.3s, box-shadow 0.3s;">
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
				
				<!-- Blog Hero Section -->
				<section class="blog-hero" style="background: #fff; padding: 40px 40px 60px 40px; margin: 0 0 40px 0; text-align: center; border-bottom: 1px solid var(--border-color);">
					<div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; margin-bottom: 20px; background: var(--accent-primary); border-radius: 50%;">
						<svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #fff;">
							<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<polyline points="10 9 9 9 8 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>
					<h1 style="font-size: 3rem; font-weight: 700; margin: 0 0 15px; color: var(--accent-primary);">
						<?php echo esc_html( get_bloginfo( 'name' ) ); ?> Blog
					</h1>
					<div style="width: 80px; height: 3px; background: var(--accent-primary); margin: 0 auto 20px; border-radius: 2px;"></div>
					<p style="font-size: 1.2rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto; line-height: 1.6;">
						Discover the latest insights, trends, and expert analysis from our team of business professionals.
					</p>
				</section>

				<!-- Blog Posts Grid -->
				<div class="blog-posts-grid">
					<?php
					if ( have_posts() ) :
						while ( have_posts() ) : the_post();
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
					endif;
					?>
				</div>

				<!-- Pagination -->
				<?php if ( $wp_query->max_num_pages > 1 ) : ?>
					<div class="blog-pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 50px; padding: 20px 0;">
						<?php
						$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
						$max   = intval( $wp_query->max_num_pages );
						
						// Previous button
						if ( $paged > 1 ) {
							echo '<a href="' . esc_url( get_pagenum_link( $paged - 1 ) ) . '" class="pagination-btn">' . esc_html__( 'Previous', 'sme-insights' ) . '</a>';
						}
						
						// Page numbers
						for ( $i = 1; $i <= $max; $i++ ) {
							if ( $i == 1 || $i == $max || ( $i >= $paged - 2 && $i <= $paged + 2 ) ) {
								$class = ( $i == $paged ) ? 'pagination-btn active' : 'pagination-btn';
								echo '<a href="' . esc_url( get_pagenum_link( $i ) ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $i ) . '</a>';
							} elseif ( $i == $paged - 3 || $i == $paged + 3 ) {
								echo '<span class="pagination-btn" style="pointer-events: none;">...</span>';
							}
						}
						
						// Next button
						if ( $paged < $max ) {
							echo '<a href="' . esc_url( get_pagenum_link( $paged + 1 ) ) . '" class="pagination-btn">' . esc_html__( 'Next', 'sme-insights' ) . '</a>';
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

