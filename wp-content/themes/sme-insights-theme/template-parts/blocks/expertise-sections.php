<?php
/**
 * Template Part: Expertise Sections Block
 *
 * @package SME_Insights
 * @since 1.0.0
 */
?>

<section class="expertise-sections">
	<div class="expertise-grid">
		<!-- Finance Column -->
		<div class="expertise-column">
			<?php
			$finance_term = get_term_by( 'name', 'Finance', 'main_category' );
			$finance_link = $finance_term ? get_term_link( $finance_term->term_id, 'main_category' ) : '#';
			?>
			<h2 class="section-title">
				<a href="<?php echo esc_url( $finance_link ); ?>">Finance</a>
			</h2>
			<div class="expertise-posts-list">
				<?php
				// Exclude already displayed posts
				$displayed_ids = sme_get_displayed_post_ids();
				$finance_args = array(
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => 3,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'ignore_sticky_posts' => true,
					'post__not_in'   => $displayed_ids, // Exclude already displayed posts
					'tax_query'      => array(
						array(
							'taxonomy' => 'main_category',
							'field'    => 'term_id',
							'terms'    => $finance_term ? $finance_term->term_id : 0,
						),
					),
					'meta_query'     => array(
						array(
							'key'     => '_thumbnail_id',
							'compare' => 'EXISTS',
						),
					),
				);
				$finance_query = new WP_Query( $finance_args );

				if ( $finance_query->have_posts() ) :
					while ( $finance_query->have_posts() ) : $finance_query->the_post();
						// Mark this post as displayed
						sme_mark_post_displayed( get_the_ID() );
						?>
						<div class="expertise-post-item">
							<?php if ( has_post_thumbnail() ) : ?>
								<img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="expertise-post-image" loading="lazy">
							<?php endif; ?>
							<div class="expertise-post-content">
								<h3 class="expertise-post-title">
									<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
								</h3>
								<div class="expertise-post-meta"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></div>
							</div>
						</div>
					<?php
					endwhile;
					wp_reset_postdata();
				endif;
				?>
			</div>
		</div>

		<!-- Marketing Column -->
		<div class="expertise-column">
			<?php
			$marketing_term = get_term_by( 'name', 'Marketing', 'main_category' );
			$marketing_link = $marketing_term ? get_term_link( $marketing_term->term_id, 'main_category' ) : '#';
			?>
			<h2 class="section-title">
				<a href="<?php echo esc_url( $marketing_link ); ?>">Marketing</a>
			</h2>
			<div class="expertise-posts-list">
				<?php
				// Exclude already displayed posts
				$displayed_ids = sme_get_displayed_post_ids();
				$marketing_args = array(
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => 3,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'ignore_sticky_posts' => true,
					'post__not_in'   => $displayed_ids, // Exclude already displayed posts
					'tax_query'      => array(
						array(
							'taxonomy' => 'main_category',
							'field'    => 'term_id',
							'terms'    => $marketing_term ? $marketing_term->term_id : 0,
						),
					),
					'meta_query'     => array(
						array(
							'key'     => '_thumbnail_id',
							'compare' => 'EXISTS',
						),
					),
				);
				$marketing_query = new WP_Query( $marketing_args );

				if ( $marketing_query->have_posts() ) :
					while ( $marketing_query->have_posts() ) : $marketing_query->the_post();
						// Mark this post as displayed
						sme_mark_post_displayed( get_the_ID() );
						?>
						<div class="expertise-post-item">
							<?php if ( has_post_thumbnail() ) : ?>
								<img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="expertise-post-image" loading="lazy">
							<?php endif; ?>
							<div class="expertise-post-content">
								<h3 class="expertise-post-title">
									<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
								</h3>
								<div class="expertise-post-meta"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></div>
							</div>
						</div>
					<?php
					endwhile;
					wp_reset_postdata();
				endif;
				?>
			</div>
		</div>
	</div>
</section>

