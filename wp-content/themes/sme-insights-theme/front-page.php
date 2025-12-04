<?php
/**
 * Front Page Template (Homepage)
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

get_header();
sme_init_homepage_post_tracker(); // Initialize post tracker
?>

<main id="main-content" class="main-content-layout">
	<div class="container">
		<div class="main-content-area">
			<!-- Main News Mobile - Only for mobile devices -->
			<?php get_template_part( 'template-parts/blocks/main-news-mobile' ); ?>
			
			<!-- Main Banner Section: Main News (70%) + Trending News (30%) -->
			<section class="main-banner-section">
				<div class="main-banner-wrapper">
					<?php get_template_part( 'template-parts/blocks/hero-slider' ); ?>
					<?php get_template_part( 'template-parts/blocks/trending-news' ); ?>
				</div>
			</section>

			<!-- Featured Insights Section -->
			<?php get_template_part( 'template-parts/blocks/featured-insights' ); ?>

			<!-- Expertise Sections: Finance / Marketing -->
			<?php get_template_part( 'template-parts/blocks/expertise-sections' ); ?>

			<!-- Newsletter CTA Section -->
			<?php get_template_part( 'template-parts/blocks/cta-section' ); ?>

			<!-- Latest Posts Section: Latest from SME Insights / Most Popular -->
			<?php get_template_part( 'template-parts/blocks/latest-posts-section' ); ?>
		</div>
	</div>
</main>

<?php
get_footer();
