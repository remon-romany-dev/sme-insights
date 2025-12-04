<?php
/**
 * Template Name: Become a Contributor Page
 * Become a Contributor Page Template
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
		<?php
		// Hero Section
		$hero_title = get_post_meta( get_the_ID(), '_sme_hero_title', true );
		if ( empty( $hero_title ) ) {
			$hero_title = get_the_title();
		}
		if ( empty( $hero_title ) || $hero_title === 'Become a Contributor' ) {
			$hero_title = 'Share Your Expertise, Shape Business Success';
		}
		
		$hero_subtitle = get_post_meta( get_the_ID(), '_sme_hero_subtitle', true );
		if ( empty( $hero_subtitle ) ) {
			$hero_subtitle = 'Become an SME Insights Contributor and help shape the future of small business through your insights, experience, and expertise.';
		}
		?>
		
		<!-- Contributor Hero -->
		<div class="contact-hero">
			<div class="container-inner">
				<h1><?php echo esc_html( $hero_title ); ?></h1>
				<p><?php echo esc_html( $hero_subtitle ); ?></p>
			</div>
		</div>

		<!-- Main Content -->
		<div class="container contributor-page-container">
			<div class="main-content-area">
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="container-inner contributor-content-wrapper">
						<div class="contributor-content">
							<?php
							// Include contributor sections
							get_template_part( 'template-parts/components/contributor-sections' );
							?>
						</div>
					</div>
				</article>
			</div>
		</div>
	<?php endwhile; ?>
</div>

<script>
(function() {
	function preventSubmit(e) {
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		return false;
	}
	
	function setupFormProtection() {
		const form = document.querySelector('.submission-form');
		if (form) {
			// Remove any existing listeners
			form.removeEventListener('submit', preventSubmit);
			// Add protection listener with capture phase
			form.addEventListener('submit', preventSubmit, true);
		}
	}
	
	// Setup immediately if DOM is ready
	if (document.readyState !== 'loading') {
		setupFormProtection();
	}
	
	// Also setup on DOMContentLoaded
	document.addEventListener('DOMContentLoaded', setupFormProtection);
})();
</script>

<?php
get_footer();


