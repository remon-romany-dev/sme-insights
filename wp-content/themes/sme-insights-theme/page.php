<?php
/**
 * Template Name: Default template
 * Page Template - DEFAULT TEMPLATE FOR ALL PAGES
 * 
 * This is the DEFAULT template for ALL pages in WordPress.
 * When you create a NEW page, WordPress automatically uses this template.
 * 
 * This template displays all pages and allows full editing via WordPress Gutenberg Editor.
 * Users can edit page content directly in WordPress admin using Gutenberg blocks.
 * 
 * HOW TO EDIT PAGE CONTENT:
 * 1. Go to Pages > All Pages
 * 2. Click "Edit" on any page
 * 3. Use Gutenberg blocks to edit content
 * 4. All content is fully editable via WordPress Editor
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

get_header();
?>

<main id="main" role="main">
<div class="main-content-layout" style="padding: 40px 0;">
	<div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">
		<div class="main-content-area" style="width: 100%;">
			<div class="container-inner" style="max-width: 1200px; margin: 0 auto; padding: 0;">
				<?php while ( have_posts() ) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<?php
						// Display page title if not using custom template
						if ( ! get_page_template_slug() ) :
						?>
							<header class="entry-header" style="margin-bottom: 30px;">
								<h1 class="entry-title" style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary);">
									<?php the_title(); ?>
								</h1>
							</header>
						<?php endif; ?>
						
						<?php
						?>
						<div class="entry-content wp-block-group" style="line-height: 1.8; font-size: 1.1rem;">
							<?php 
							the_content();
							
							wp_link_pages( array(
								'before' => '<div class="page-links">' . __( 'Pages:', 'sme-insights' ),
								'after'  => '</div>',
							) );
							?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
		</div>
	</div>
</div>
</main>

<?php
get_footer();

