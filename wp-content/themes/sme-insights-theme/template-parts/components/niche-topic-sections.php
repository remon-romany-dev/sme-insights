<?php
/**
 * Niche Topic Page Sections
 * Used for: AI in Business, E-commerce Trends, Startup Funding, Green Economy, Remote Work
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$topic_slug = get_query_var( 'niche_topic_slug', '' );
$config = get_query_var( 'niche_topic_config', array() );

// Query featured articles (3 articles for slider)
$featured_args = array(
	'post_type'      => 'post',
	'posts_per_page' => 3,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
	'tax_query'      => array(
		array(
			'taxonomy' => 'article_tag',
			'field'    => 'slug',
			'terms'    => $topic_slug,
		),
	),
);

$featured_query = new WP_Query( $featured_args );

$latest_args = array(
	'post_type'      => 'post',
	'posts_per_page' => 3,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
	'tax_query'      => array(
		array(
			'taxonomy' => 'article_tag',
			'field'    => 'slug',
			'terms'    => $topic_slug,
		),
	),
);

$latest_query = new WP_Query( $latest_args );
?>

<!-- Start Here: Essential Guides -->
<section class="start-here-section">
	<h2 class="section-title">Start Here: Essential Guides</h2>
	<div class="featured-articles-grid">
		<?php if ( $featured_query->have_posts() ) : ?>
			<?php while ( $featured_query->have_posts() ) : $featured_query->the_post(); ?>
				<article class="featured-article-card">
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php echo esc_url( get_permalink() ); ?>">
							<?php the_post_thumbnail( 'sme-medium', array( 
								'class' => 'featured-article-image',
								'alt' => get_the_title()
							) ); ?>
						</a>
					<?php endif; ?>
					<div class="featured-article-content">
						<?php
						$categories = get_the_terms( get_the_ID(), 'main_category' );
						if ( $categories && ! is_wp_error( $categories ) ) :
							$cat = $categories[0];
							$color = SME_Helpers::get_category_color( $cat->term_id );
						?>
							<span class="featured-article-category" style="background: <?php echo esc_attr( $color ); ?>;">
								<?php echo esc_html( $cat->name ); ?>
							</span>
						<?php endif; ?>
						<h3 class="featured-article-title">
							<a href="<?php echo esc_url( get_permalink() ); ?>">
								<?php echo esc_html( get_the_title() ); ?>
							</a>
						</h3>
						<?php 
						$custom_excerpt = get_post_meta( get_the_ID(), '_sme_custom_excerpt', true );
						if ( has_excerpt() || $custom_excerpt ) : 
						?>
							<p class="featured-article-excerpt">
								<?php 
								$excerpt = $custom_excerpt ?: get_the_excerpt();
								echo esc_html( wp_trim_words( $excerpt, 25 ) ); 
								?>
							</p>
						<?php endif; ?>
						<div class="featured-article-meta">
							<?php echo esc_html( get_the_date( 'F j, Y' ) ); ?> • 
							<?php 
							$reading_time = ceil( str_word_count( get_the_content() ) / 200 );
							echo esc_html( $reading_time ); 
							?> min read
						</div>
					</div>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<p style="grid-column: 1 / -1; text-align: center; color: var(--text-secondary); padding: 40px;">
				<?php _e( 'No featured articles found. Check back soon!', 'sme-insights' ); ?>
			</p>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</section>

<!-- Latest News & Analysis -->
<section class="latest-news-section">
	<h2 class="section-title">Latest News & Analysis</h2>
	<div class="articles-list">
		<?php if ( $latest_query->have_posts() ) : ?>
			<?php while ( $latest_query->have_posts() ) : $latest_query->the_post(); ?>
				<article class="article-item">
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php echo esc_url( get_permalink() ); ?>" class="article-item-image">
							<?php the_post_thumbnail( 'sme-medium', array( 
								'alt' => get_the_title()
							) ); ?>
						</a>
					<?php endif; ?>
					<div class="article-item-content">
						<?php
						$categories = get_the_terms( get_the_ID(), 'main_category' );
						if ( $categories && ! is_wp_error( $categories ) ) :
							$cat = $categories[0];
							$color = SME_Helpers::get_category_color( $cat->term_id );
						?>
							<span class="article-item-category" style="background: <?php echo esc_attr( $color ); ?>;">
								<?php echo esc_html( $cat->name ); ?>
							</span>
						<?php endif; ?>
						<h3 class="article-item-title">
							<a href="<?php echo esc_url( get_permalink() ); ?>">
								<?php echo esc_html( get_the_title() ); ?>
							</a>
						</h3>
						<?php 
						$custom_excerpt = get_post_meta( get_the_ID(), '_sme_custom_excerpt', true );
						if ( has_excerpt() || $custom_excerpt ) : 
						?>
							<p class="article-item-excerpt">
								<?php 
								$excerpt = $custom_excerpt ?: get_the_excerpt();
								echo esc_html( wp_trim_words( $excerpt, 30 ) ); 
								?>
							</p>
						<?php endif; ?>
						<div class="article-item-meta">
							<?php echo esc_html( get_the_date( 'F j, Y' ) ); ?> • 
							<?php 
							$reading_time = ceil( str_word_count( get_the_content() ) / 200 );
							echo esc_html( $reading_time ); 
							?> min read
						</div>
					</div>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<p style="text-align: center; color: var(--text-secondary); padding: 40px;">
				<?php _e( 'No articles found. Check back soon!', 'sme-insights' ); ?>
			</p>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</section>

<!-- Tools & Resources -->
<section class="tools-resources-section">
	<div class="container-inner">
		<h2 class="section-title">Tools & Resources</h2>
	<div class="resources-grid">
		<?php
		$page_id = get_the_ID();
		$tools_data = get_post_meta( $page_id, '_sme_tools_resources', true );
		
		// Default tools data
		$default_tools = array(
			array(
				'icon' => '🤖',
				'title' => 'Tools Comparison Guide',
				'description' => 'Compare the top tools for small businesses with our comprehensive comparison guide. Find the perfect solution for your needs and budget.',
				'link' => '#',
				'link_text' => 'View Guide',
			),
			array(
				'icon' => '📈',
				'title' => 'ROI Calculator',
				'description' => 'Calculate the potential return on investment for implementations in your business. Estimate savings, efficiency gains, and revenue impact.',
				'link' => '#',
				'link_text' => 'Calculate ROI',
			),
			array(
				'icon' => '📚',
				'title' => 'Implementation Checklist',
				'description' => 'A step-by-step checklist to guide you through implementation, from planning to deployment. Ensure nothing is missed in your journey.',
				'link' => '#',
				'link_text' => 'Get Checklist',
			),
			array(
				'icon' => '💡',
				'title' => 'Use Cases Library',
				'description' => 'Browse our library of real-world use cases across different industries. Get inspired and find solutions that match your business needs.',
				'link' => '#',
				'link_text' => 'Explore Library',
			),
		);
		
		// Use custom data if exists, otherwise use defaults
		if ( ! is_array( $tools_data ) || empty( $tools_data[0]['title'] ) ) {
			$tools_data = $default_tools;
		}
		
		// Ensure we have exactly 4 tools
		while ( count( $tools_data ) < 4 ) {
			$tools_data[] = array( 'icon' => '', 'title' => '', 'description' => '', 'link' => '#', 'link_text' => '' );
		}
		$tools_data = array_slice( $tools_data, 0, 4 );
		
		foreach ( $tools_data as $tool ) :
			$icon = ! empty( $tool['icon'] ) ? $tool['icon'] : '📄';
			$title = ! empty( $tool['title'] ) ? $tool['title'] : '';
			$description = ! empty( $tool['description'] ) ? $tool['description'] : '';
			$link = ! empty( $tool['link'] ) ? $tool['link'] : '#';
			$link_text = ! empty( $tool['link_text'] ) ? $tool['link_text'] : 'Learn More';
			
			// If title is empty, use default
			if ( empty( $title ) ) {
				$default_index = array_search( $tool, $tools_data );
				if ( $default_index !== false && isset( $default_tools[ $default_index ] ) ) {
					$title = $default_tools[ $default_index ]['title'];
					$description = $default_tools[ $default_index ]['description'];
					$link_text = $default_tools[ $default_index ]['link_text'];
				}
			}
			
			// For first tool, add page title prefix
			if ( $title === 'Tools Comparison Guide' || ( empty( $tool['title'] ) && array_search( $tool, $tools_data ) === 0 ) ) {
				$title = $config['title'] . ' ' . $title;
			}
		?>
			<div class="resource-card">
				<div class="resource-icon"><?php echo esc_html( $icon ); ?></div>
				<h3 class="resource-title">
					<?php echo esc_html( $title ); ?>
				</h3>
				<p class="resource-description">
					<?php echo esc_html( $description ); ?>
				</p>
				<a href="<?php echo esc_url( $link ); ?>" class="resource-link">
					<?php echo esc_html( $link_text ); ?> →
				</a>
			</div>
		<?php endforeach; ?>
	</div>
	</div>
</section>

<!-- Experts Section -->
<section class="experts-section">
	<h2 class="section-title">
		Our Experts on <?php echo esc_html( $config['title'] ); ?>
	</h2>
	<?php
	$page_id = get_the_ID();
	$experts_data = get_post_meta( $page_id, '_sme_experts_data', true );
	
	if ( ! is_array( $experts_data ) || empty( $experts_data[0]['name'] ) ) {
		$experts_data = array(
			array(
				'name' => 'Sarah Mitchell',
				'title' => 'Specialist',
				'bio' => 'With over 12 years of experience in ' . strtolower( $config['title'] ) . ' implementation for small businesses, Sarah has helped hundreds of companies automate their operations and increase efficiency. She holds a Master\'s in Computer Science from MIT.',
				'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop',
				'linkedin' => 'https://linkedin.com/in/sarah-mitchell-ai',
				'twitter' => 'https://twitter.com/sarahmitchellai',
			),
			array(
				'name' => 'David Chen',
				'title' => 'Consultant',
				'bio' => 'David is a leading consultant specializing in ' . strtolower( $config['title'] ) . ' strategy and digital transformation. He has worked with Fortune 500 companies and startups alike, helping them leverage technology to drive growth and innovation.',
				'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop',
				'linkedin' => 'https://linkedin.com/in/david-chen-ai',
				'twitter' => 'https://twitter.com/davidchenai',
			),
			array(
				'name' => 'Emily Rodriguez',
				'title' => 'Advisor',
				'bio' => 'Emily brings a unique perspective combining business acumen with technical expertise. She has advised over 200 small businesses on ' . strtolower( $config['title'] ) . ' adoption, focusing on practical, cost-effective solutions that deliver real results.',
				'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=200&fit=crop',
				'linkedin' => 'https://linkedin.com/in/emily-rodriguez-ai',
				'twitter' => 'https://twitter.com/emilyrodriguezai',
			),
		);
	}
	
	// Ensure we have exactly 3 experts
	while ( count( $experts_data ) < 3 ) {
		$experts_data[] = array( 'name' => '', 'title' => '', 'bio' => '', 'avatar' => '', 'linkedin' => '', 'twitter' => '' );
	}
	$experts_data = array_slice( $experts_data, 0, 3 );
	?>
	<div class="experts-grid">
		<?php foreach ( $experts_data as $expert ) : 
			// Skip empty experts
			if ( empty( $expert['name'] ) ) {
				continue;
			}
		?>
			<div class="expert-card">
				<div class="expert-avatar">
					<?php if ( ! empty( $expert['avatar'] ) ) : ?>
						<img src="<?php echo esc_url( $expert['avatar'] ); ?>" alt="<?php echo esc_attr( $expert['name'] ); ?>" class="expert-avatar-img">
					<?php else : ?>
						👤
					<?php endif; ?>
				</div>
				<h3 class="expert-name">
					<?php echo esc_html( $expert['name'] ); ?>
				</h3>
				<p class="expert-title">
					<?php echo esc_html( $config['title'] ); ?> <?php echo esc_html( $expert['title'] ); ?>
				</p>
				<?php if ( ! empty( $expert['bio'] ) ) : ?>
					<p class="expert-bio">
						<?php echo esc_html( $expert['bio'] ); ?>
					</p>
				<?php endif; ?>
				<?php if ( ! empty( $expert['linkedin'] ) || ! empty( $expert['twitter'] ) ) : ?>
					<div class="expert-social">
						<?php if ( ! empty( $expert['linkedin'] ) ) : ?>
							<a href="<?php echo esc_url( $expert['linkedin'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
									<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
								</svg>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $expert['twitter'] ) ) : ?>
							<a href="<?php echo esc_url( $expert['twitter'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
									<path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
								</svg>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<!-- Custom CTA Section -->
<section class="custom-cta-section">
	<div class="container-inner">
		<h2 class="custom-cta-title">
			Get <?php echo esc_html( $config['title'] ); ?> Insights Straight to Your Inbox
		</h2>
		<p class="custom-cta-text">
			Stay ahead with the latest trends, implementation guides, and expert advice delivered weekly. Join thousands of business owners who trust SME Insights.
		</p>
	<form class="custom-cta-form cta-subscription-form" id="nicheSubscriptionForm">
		<input type="email" placeholder="Enter your email address" required>
		<button type="submit">
			Subscribe Now
		</button>
	</form>
	</div>
</section>

