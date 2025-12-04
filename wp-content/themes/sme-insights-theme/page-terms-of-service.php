<?php
/**
 * Template Name: Terms of Service
 * Template for Terms of Service page
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
		<!-- Terms of Service Hero -->
		<div class="contact-hero">
			<div class="container-inner">
				<h1>Terms of Service</h1>
				<p>Last Updated: <?php echo esc_html( get_the_modified_date( 'F j, Y' ) ); ?>. By using this website, you agree to be bound by these terms.</p>
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
									<p style="font-size: 1.1rem; line-height: 1.8; color: var(--text-secondary); margin: 0;">Welcome to SME Insights. These Terms of Service ("Terms") govern your access to and use of our website (smeinsights.com) and all content and services provided through it. By continuing to use our website, you agree to be legally bound by these Terms. If you do not agree with any part of these Terms, please do not use our website.</p>
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
