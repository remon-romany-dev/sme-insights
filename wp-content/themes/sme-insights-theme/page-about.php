<?php
/**
 * Template Name: About Page
 * Template for About Us page - Matches about-page.html design
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

get_header();
?>

<div class="main-content-layout">
	<!-- Hero Section -->
	<div style="background: var(--breaking-gradient); color: #fff; padding: 100px 0; text-align: center;">
		<div class="container-inner">
			<h1 style="font-size: 3rem; font-weight: 900; margin-bottom: 20px; letter-spacing: -1px;">We Are SME Insights</h1>
			<p style="font-size: 1rem; opacity: 0.95; max-width: 800px; margin: 0 auto; line-height: 1.6;">Empowering Small Businesses with Actionable Insights and Expert Guidance</p>
		</div>
	</div>

	<!-- Hero Image Section -->
	<?php if ( has_post_thumbnail() ) : ?>
		<div style="max-width: 1200px; margin: -50px auto 80px; padding: 0 20px;">
			<div style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
				<?php the_post_thumbnail( 'large', array( 'style' => 'width: 100%; height: 500px; object-fit: cover; display: block;', 'alt' => get_the_title() ) ); ?>
			</div>
		</div>
	<?php else : ?>
		<!-- Fallback image if no featured image -->
		<div style="max-width: 1200px; margin: -50px auto 80px; padding: 0 20px;">
			<div style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
				<img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200&h=500&fit=crop" alt="SME Insights Team" style="width: 100%; height: 500px; object-fit: cover; display: block;">
			</div>
		</div>
	<?php endif; ?>

	<!-- Main Content -->
	<div class="container-inner" style="max-width: 1000px; margin: 0 auto; padding: 0 20px 80px;">
		<?php
		// Include about sections template
		get_template_part( 'template-parts/components/about-sections' );
		?>
	</div>
</div>

<?php
get_footer();
