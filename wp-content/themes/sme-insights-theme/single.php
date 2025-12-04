<?php
/**
 * Single Post Template
 * 
 * This template displays single posts and allows full editing via WordPress Gutenberg Editor.
 * Users can edit post content directly in WordPress admin using Gutenberg blocks.
 * 
 * HOW TO EDIT:
 * 1. Go to Posts > All Posts
 * 2. Click "Edit" on any post
 * 3. Use Gutenberg blocks to edit content
 * 4. All content is fully editable via WordPress Editor
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

get_header();

while ( have_posts() ) : the_post();
	$categories = get_the_terms( get_the_ID(), 'main_category' );
	$category_color = $categories && ! is_wp_error( $categories ) && isset( $categories[0] ) ? SME_Helpers::get_category_color( $categories[0]->term_id ) : '#2563eb';
?>

<main id="main" role="main">
<div class="main-content-layout">
	<div class="container">
		<div class="main-content-area">
			<div class="article-wrapper">
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'article-container' ); ?> itemscope itemtype="https://schema.org/NewsArticle">
						<header class="article-header">
							<h1 class="article-title" itemprop="headline"><?php the_title(); ?></h1>
							
							<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
								<div class="article-categories">
									<?php foreach ( array_slice( $categories, 0, 2 ) as $cat ) : 
										$color = SME_Helpers::get_category_color( $cat->term_id );
									?>
										<span class="article-category" style="background: <?php echo esc_attr( $color ); ?>; color: #fff;">
											<?php echo esc_html( $cat->name ); ?>
										</span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							
							<div class="article-meta">
								<span itemprop="datePublished" content="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
									<?php echo esc_html( get_the_date( 'F j, Y' ) ); ?>
								</span>
								<span>•</span>
								<span><?php echo number_format( get_comments_number() ); ?> comments</span>
								<?php if ( get_the_author() ) : ?>
									<span>•</span>
									<span itemprop="author" itemscope itemtype="https://schema.org/Person">
										By <span itemprop="name"><?php the_author(); ?></span>
									</span>
								<?php endif; ?>
								<?php 
								$views = get_post_meta( get_the_ID(), 'post_views_count', true );
								if ( $views ) :
								?>
									<span>•</span>
									<span><?php echo number_format( $views ); ?> views</span>
								<?php endif; ?>
							</div>
						</header>
						
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="article-featured-image-wrapper">
								<?php the_post_thumbnail( 'large', array( 
									'class' => 'article-featured-image',
									'alt' => get_the_title(),
									'itemprop' => 'image'
								) ); ?>
							</div>
						<?php endif; ?>
						
						<div class="article-content wp-block-group" itemprop="articleBody">
							<?php
							the_content();
							?>
							
							<?php
							wp_link_pages( array(
								'before' => '<div class="page-links">' . __( 'Pages:', 'sme-insights' ),
								'after'  => '</div>',
							) );
							?>
						</div>
						
						<?php
						$tags = get_the_terms( get_the_ID(), 'article_tag' );
						if ( $tags && ! is_wp_error( $tags ) ) :
						?>
							<div class="article-tags">
								<span class="tags-label">Tag:</span>
								<?php foreach ( $tags as $tag ) : ?>
									<span class="tag">
										<?php echo esc_html( $tag->name ); ?>
									</span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						
						<!-- Social Sharing -->
						<div class="social-sharing">
							<span style="font-weight: 600; color: var(--text-primary); margin-right: 10px;"><?php esc_html_e( 'Share this article:', 'sme-insights' ); ?></span>
							<?php
							$post_url = urlencode( get_permalink() );
							$post_title = urlencode( get_the_title() );
							$post_excerpt = urlencode( wp_trim_words( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 20 ) );
							?>
							<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $post_url ); ?>" target="_blank" rel="noopener noreferrer" class="share-btn facebook" aria-label="Share on Facebook">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
									<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
								</svg>
								Facebook
							</a>
							<a href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $post_url ); ?>&text=<?php echo esc_attr( $post_title ); ?>" target="_blank" rel="noopener noreferrer" class="share-btn twitter" aria-label="Share on Twitter">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
									<path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
								</svg>
								Twitter
							</a>
							<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo esc_attr( $post_url ); ?>&title=<?php echo esc_attr( $post_title ); ?>&summary=<?php echo esc_attr( $post_excerpt ); ?>" target="_blank" rel="noopener noreferrer" class="share-btn linkedin" aria-label="Share on LinkedIn">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
									<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/>
									<circle cx="4" cy="4" r="2"/>
								</svg>
								LinkedIn
							</a>
							<a href="mailto:?subject=<?php echo esc_attr( $post_title ); ?>&body=<?php echo esc_attr( $post_url ); ?>" class="share-btn email" aria-label="Share via Email">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
									<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
									<polyline points="22,6 12,13 2,6"/>
								</svg>
								Email
							</a>
						</div>
						
						<!-- Author Box -->
						<?php
						$author_id = get_the_author_meta( 'ID' );
						$author_name = get_the_author();
						$author_description = get_the_author_meta( 'description' );
						$author_linkedin = get_the_author_meta( 'linkedin' );
						$author_twitter = get_the_author_meta( 'twitter' );
						$author_email = get_the_author_meta( 'user_email' );
						
						if ( empty( $author_description ) ) {
							$author_description = 'Expert business writer with years of experience helping small businesses grow and succeed. Specialized in finance, marketing, and business strategy.';
						}
						if ( empty( $author_linkedin ) ) {
							$author_linkedin = 'https://linkedin.com/in/sme-insights';
						}
						if ( empty( $author_twitter ) ) {
							$author_twitter = 'https://twitter.com/smeinsights';
						}
						if ( empty( $author_email ) ) {
							$author_email = get_option( 'admin_email' );
						}
						
						if ( $author_name || $author_description ) :
						?>
							<div class="author-box">
								<?php 
								$author_avatar = get_avatar( $author_id, 100, '', $author_name, array( 'class' => 'author-avatar' ) );
								// Ensure avatar is displayed as img tag with proper attributes
								if ( $author_avatar ) {
									echo $author_avatar;
								} else {
									echo '<img src="' . esc_url( get_avatar_url( $author_id, array( 'size' => 100 ) ) ) . '" alt="' . esc_attr( $author_name ) . '" class="author-avatar">';
								}
								?>
								<div class="author-info">
									<h3><?php echo esc_html( $author_name ? $author_name : 'SME Insights Team' ); ?></h3>
									<?php if ( $author_description ) : ?>
										<p class="author-bio"><?php echo esc_html( $author_description ); ?></p>
									<?php endif; ?>
									<div class="author-social">
										<?php if ( $author_linkedin ) : ?>
											<a href="<?php echo esc_url( $author_linkedin ); ?>" target="_blank" rel="noopener" aria-label="LinkedIn" title="LinkedIn">
												<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
													<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/>
													<circle cx="4" cy="4" r="2"/>
												</svg>
											</a>
										<?php endif; ?>
										
										<?php if ( $author_twitter ) : ?>
											<a href="<?php echo esc_url( $author_twitter ); ?>" target="_blank" rel="noopener" aria-label="Twitter" title="Twitter">
												<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
													<path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
												</svg>
											</a>
										<?php endif; ?>
										
										<?php if ( $author_email ) : ?>
											<a href="mailto:<?php echo esc_attr( $author_email ); ?>" aria-label="Email" title="Email">
												<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
													<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
													<polyline points="22,6 12,13 2,6"/>
												</svg>
											</a>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
						
						<!-- Comments Section -->
						<?php if ( comments_open() || get_comments_number() ) : ?>
							<div class="comments-section">
								<?php comments_template(); ?>
							</div>
						<?php endif; ?>
						
						<!-- Related Posts -->
						<?php
						if ( $categories && ! is_wp_error( $categories ) ) {
							$related_query = new WP_Query( array(
								'post_type'      => 'post',
								'posts_per_page' => 3,
								'post__not_in'   => array( get_the_ID() ),
								'tax_query'      => array(
									array(
										'taxonomy' => 'main_category',
										'field'    => 'term_id',
										'terms'    => $categories[0]->term_id,
									),
								),
								'orderby'        => 'date',
								'order'          => 'DESC',
							) );
							
							if ( $related_query->have_posts() ) :
							?>
								<section class="related-posts" id="related-posts">
									<h2><?php esc_html_e( 'Related Posts', 'sme-insights' ); ?></h2>
									<div class="related-posts-grid">
										<?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
											<article class="related-post-card">
												<?php if ( has_post_thumbnail() ) : ?>
													<a href="<?php echo esc_url( get_permalink() ); ?>">
														<?php the_post_thumbnail( 'sme-medium', array( 
															'class' => 'related-post-image',
															'alt' => get_the_title(),
															'loading' => 'lazy'
														) ); ?>
													</a>
												<?php endif; ?>
												<div class="related-post-content">
													<h3 class="related-post-title">
														<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
													</h3>
													<div style="font-size: 12px; color: var(--text-light);">
														<?php echo esc_html( get_the_date( 'F j, Y' ) ); ?>
													</div>
												</div>
											</article>
										<?php endwhile; ?>
									</div>
								</section>
							<?php
							wp_reset_postdata();
							endif;
						}
						?>
					</article>
					
					<!-- Sidebar -->
					<aside class="article-sidebar">
						<!-- Table of Contents Widget -->
						<div class="sidebar-widget">
							<h3><?php esc_html_e( 'Table of Contents', 'sme-insights' ); ?></h3>
							<ul class="table-of-contents">
								<?php
								$content = get_the_content();
								// Apply the same filter to get IDs
								$content = apply_filters( 'the_content', $content );
								
								// Match headings with IDs
								preg_match_all( '/<h([2-3])[^>]*id=["\']([^"\']+)["\'][^>]*>(.*?)<\/h\1>/i', $content, $headings_with_ids );
								
								$has_headings = false;
								
								// If no IDs found, try without IDs
								if ( empty( $headings_with_ids[3] ) ) {
									preg_match_all( '/<h([2-3])[^>]*>(.*?)<\/h\1>/i', $content, $headings );
									if ( ! empty( $headings[2] ) ) {
										$has_headings = true;
										foreach ( $headings[2] as $index => $heading ) {
											$id = 'heading-' . $index;
											$heading_text = strip_tags( $heading );
											if ( ! empty( $heading_text ) ) {
												// Limit text length for display
												$display_text = mb_strlen( $heading_text ) > 60 ? mb_substr( $heading_text, 0, 60 ) . '...' : $heading_text;
												echo '<li><a href="#' . esc_attr( $id ) . '">' . esc_html( $display_text ) . '</a></li>';
											}
										}
									}
								} else {
									$has_headings = true;
									foreach ( $headings_with_ids[3] as $index => $heading ) {
										$id = isset( $headings_with_ids[2][$index] ) ? $headings_with_ids[2][$index] : 'heading-' . $index;
										$heading_text = strip_tags( $heading );
										if ( ! empty( $heading_text ) ) {
											// Limit text length for display
											$display_text = mb_strlen( $heading_text ) > 60 ? mb_substr( $heading_text, 0, 60 ) . '...' : $heading_text;
											echo '<li><a href="#' . esc_attr( $id ) . '">' . esc_html( $display_text ) . '</a></li>';
										}
									}
								}
								
								// If no headings found, use paragraphs as fallback
								if ( ! $has_headings ) {
									// Get raw content to extract paragraphs
									$raw_content = get_the_content();
									$raw_content = apply_filters( 'the_content', $raw_content );
									
									// Match paragraphs
									preg_match_all( '/<p[^>]*>(.*?)<\/p>/i', $raw_content, $paragraphs );
									
									if ( ! empty( $paragraphs[1] ) ) {
										// Filter out empty paragraphs and get first 5-7 non-empty ones
										$valid_paragraphs = array();
										foreach ( $paragraphs[1] as $para ) {
											$text = strip_tags( $para );
											$text = trim( $text );
											// Only include paragraphs with meaningful content (at least 20 characters)
											if ( ! empty( $text ) && mb_strlen( $text ) >= 20 ) {
												$valid_paragraphs[] = $text;
											}
											// Limit to 7 paragraphs
											if ( count( $valid_paragraphs ) >= 7 ) {
												break;
											}
										}
										
										// If we have valid paragraphs, display them
										if ( ! empty( $valid_paragraphs ) ) {
											// Shuffle to make it "random" as requested
											shuffle( $valid_paragraphs );
											// Take first 5-7
											$selected_paragraphs = array_slice( $valid_paragraphs, 0, min( 7, count( $valid_paragraphs ) ) );
											
											foreach ( $selected_paragraphs as $index => $para_text ) {
												$para_id = 'paragraph-' . ( $index + 1 );
												// Limit text length for display (first 50-60 characters)
												$display_text = mb_strlen( $para_text ) > 60 ? mb_substr( $para_text, 0, 60 ) . '...' : $para_text;
												echo '<li><a href="#' . esc_attr( $para_id ) . '">' . esc_html( $display_text ) . '</a></li>';
											}
										} else {
											// Fallback: Show generic items if no valid paragraphs
											$fallback_items = array(
												'Introduction',
												'Key Points',
												'Main Content',
												'Important Details',
												'Conclusion'
											);
											foreach ( $fallback_items as $index => $item ) {
												echo '<li><a href="#content-start">' . esc_html( $item ) . '</a></li>';
											}
										}
									} else {
										// Final fallback: Show generic items
										$fallback_items = array(
											'Introduction',
											'Key Points',
											'Main Content',
											'Important Details',
											'Conclusion'
										);
										foreach ( $fallback_items as $index => $item ) {
											echo '<li><a href="#content-start">' . esc_html( $item ) . '</a></li>';
										}
									}
								}
								?>
							</ul>
						</div>
						
						<!-- Newsletter CTA Widget -->
						<div class="sidebar-widget newsletter-cta">
							<h3><?php esc_html_e( 'Stay Informed', 'sme-insights' ); ?></h3>
							<p><?php esc_html_e( 'Get the latest insights delivered to your inbox.', 'sme-insights' ); ?></p>
							<form class="newsletter-form">
								<input type="email" name="email" placeholder="<?php esc_attr_e( 'Enter your email', 'sme-insights' ); ?>" required>
								<button type="submit"><?php esc_html_e( 'Subscribe', 'sme-insights' ); ?></button>
							</form>
						</div>
						
						<!-- Related Articles Sidebar -->
						<?php
						$sidebar_posts = new WP_Query( array(
							'post_type'      => 'post',
							'posts_per_page' => 5,
							'post__not_in'   => array( get_the_ID() ),
							'orderby'        => 'date',
							'order'          => 'DESC',
						) );
						
						if ( $sidebar_posts->have_posts() ) :
						?>
							<div class="sidebar-widget">
								<h3><?php esc_html_e( 'Latest Articles', 'sme-insights' ); ?></h3>
								<ul class="related-articles-sidebar">
									<?php while ( $sidebar_posts->have_posts() ) : $sidebar_posts->the_post(); ?>
										<li>
											<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
										</li>
									<?php endwhile; ?>
								</ul>
							</div>
						<?php
						wp_reset_postdata();
						endif;
						?>
					</aside>
				</div>
		</div>
	</div>
</div>
</main>

<?php
endwhile;
?>
<?php
get_footer();
