<?php
/**
 * Template Name: Privacy Policy
 * Template for Privacy Policy page
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

get_header();
?>

<div class="main-content-layout contributor-page-layout">
	<?php while ( have_posts() ) : the_post(); ?>
		<!-- Privacy Policy Hero -->
		<div class="contact-hero">
			<div class="container-inner">
				<h1>Privacy Policy</h1>
				<p>Last Updated: <?php echo esc_html( get_the_modified_date( 'F j, Y' ) ); ?></p>
			</div>
		</div>

		<!-- Main Content -->
		<div class="container contributor-page-container">
			<div class="main-content-area">
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="container-inner contributor-content-wrapper">
						<div class="contributor-content">
							<?php if ( has_excerpt() ) : ?>
								<div class="legal-intro" style="background: var(--bg-secondary); padding: 40px; border-radius: 12px; margin-top: 50px; margin-bottom: 40px; border-left: 4px solid var(--accent-secondary); box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
									<p style="font-size: 1.1rem; line-height: 1.8; color: var(--text-secondary); margin: 0;">
										<?php echo esc_html( get_the_excerpt() ); ?>
									</p>
								</div>
							<?php else : ?>
								<div class="legal-intro" style="background: var(--bg-secondary); padding: 40px; border-radius: 12px; margin-top: 50px; margin-bottom: 40px; border-left: 4px solid var(--accent-secondary); box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
									<p style="font-size: 1.1rem; line-height: 1.8; color: var(--text-secondary); margin: 0;">At SME Insights, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your personal information when you visit our website.</p>
								</div>
							<?php endif; ?>
							
							<?php
							// Include legal content sections with accordion
							get_template_part( 'template-parts/components/legal-content' );
							?>
							
							<?php if ( get_the_content() ) : ?>
								<div class="entry-content" style="line-height: 1.8; font-size: 1rem; color: var(--text-primary); margin-top: 30px;">
									<?php the_content(); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</article>
			</div>
		</div>
	<?php endwhile; ?>
</div>

<?php
get_footer();
