<?php
/**
 * Main Template
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

get_header();
?>

<main id="main" role="main">
<div class="main-content-layout">
	<div class="container">
		<div class="main-content-area">
			<?php
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', get_post_type() );
				}
				
				// Pagination
				the_posts_pagination( array(
					'mid_size'  => 2,
					'prev_text' => __( 'Previous', 'sme-insights' ),
					'next_text' => __( 'Next', 'sme-insights' ),
				) );
			} else {
				get_template_part( 'template-parts/content', 'none' );
			}
			?>
		</div>
	</div>
</div>
</main>

<?php
get_footer();

