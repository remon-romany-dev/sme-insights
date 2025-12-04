<?php
/**
 * Template Name: Contact Page
 * Template for Contact Us page - Matches contact-page.html design
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

get_header();
?>

<div class="main-content-layout">
	<!-- Contact Hero -->
	<div class="contact-hero">
		<div class="container-inner">
			<h1>Get in Touch</h1>
			<p>We'd love to hear from you. Whether you have a question, feedback, or a story to share, our team is ready to answer all your inquiries.</p>
		</div>
	</div>

	<!-- Main Content -->
	<?php
	// Include contact sections template
	get_template_part( 'template-parts/components/contact-sections' );
	?>
</div>

<?php
get_footer();


