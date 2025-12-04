<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package SME_Insights
 * @since 1.0.0
 */

?>
<div class="no-results">
	<h2><?php esc_html_e( 'Nothing Found', 'sme-insights' ); ?></h2>
	<p><?php esc_html_e( 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'sme-insights' ); ?></p>
	<?php get_search_form(); ?>
</div>

