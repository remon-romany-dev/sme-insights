<?php
/**
 * 404 Error Template
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

get_header();
?>

<div class="main-content-layout">
	<div class="container">
		<div class="main-content-area">
			<div style="text-align: center; padding: 80px 20px;">
				<h1 style="font-size: 6rem; font-weight: 700; color: var(--accent-primary); margin-bottom: 20px;">404</h1>
				<h2 style="font-size: 2rem; margin-bottom: 20px; color: var(--text-primary);">Page Not Found</h2>
				<p style="font-size: 1.1rem; color: var(--text-secondary); margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">
					The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
				</p>
				<div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="padding: 15px 30px; background: var(--accent-primary); color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; display: inline-block;">
						Go to Homepage
					</a>
					<?php get_search_form(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();

