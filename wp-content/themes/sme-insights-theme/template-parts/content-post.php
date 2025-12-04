<?php
/**
 * Template part for displaying posts
 *
 * @package SME_Insights
 * @since 1.0.0
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h2 class="entry-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>
		<div class="entry-meta">
			<span><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></span>
			<?php if ( get_the_author() ) : ?>
				<span>•</span>
				<span><?php the_author(); ?></span>
			<?php endif; ?>
		</div>
	</header>
	
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="entry-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'sme-thumbnail', array( 'alt' => get_the_title(), 'loading' => 'lazy' ) ); ?>
			</a>
		</div>
	<?php endif; ?>
	
	<div class="entry-content">
		<?php the_excerpt(); ?>
	</div>
	
	<footer class="entry-footer">
		<a href="<?php the_permalink(); ?>" class="read-more">
			<?php esc_html_e( 'Read More', 'sme-insights' ); ?> →
		</a>
	</footer>
</article>

