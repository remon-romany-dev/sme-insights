<?php
/**
 * Template Name: Advertise With Us
 * Template for Advertise With Us page
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

get_header();
?>

<div class="main-content-layout">
	<!-- Advertise Hero -->
	<div class="advertise-hero">
		<div class="container-inner">
			<h1>Partner with SME Insights</h1>
			<p>Connect with a highly engaged audience of small business owners, entrepreneurs, and decision-makers.</p>
		</div>
	</div>

	<!-- Main Content -->
	<div class="advertise-content">
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php
				// Include Advertise page sections
				get_template_part( 'template-parts/components/advertise-sections' );
				?>
				<div class="entry-content" style="line-height: 1.8; font-size: 1.1rem; color: var(--text-primary); margin-top: 40px;">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
</div>

<?php
get_footer();

