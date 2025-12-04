<?php
/**
 * Template Name: Niche Topic Page
 * Niche Topic Page Template
 * Used for: AI in Business, E-commerce Trends, Startup Funding, Green Economy, Remote Work
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

get_header();

// Get page slug to determine topic
$page_slug = get_post_field( 'post_name', get_the_ID() );
$topic_config = array(
	'ai-in-business' => array(
		'title' => 'AI in Business',
		'tagline' => 'Transform Your Business with Artificial Intelligence',
		'description' => 'Here at SME Insights, we cover everything you need to know about implementing AI in your business, from choosing the right tools to building AI strategies that drive real results. Whether you\'re exploring AI for the first time or looking to scale your existing AI initiatives, our comprehensive guides and expert insights will help you harness the power of artificial intelligence.',
	),
	'ecommerce-trends' => array(
		'title' => 'E-commerce Trends',
		'tagline' => 'Stay Ahead of the Digital Commerce Revolution',
		'description' => 'Discover the latest e-commerce trends, strategies, and technologies that are reshaping how small businesses sell online. From marketplace optimization to conversion rate improvements, we provide actionable insights to help your e-commerce business thrive.',
	),
	'startup-funding' => array(
		'title' => 'Startup Funding',
		'tagline' => 'Navigate the World of Startup Financing',
		'description' => 'Everything you need to know about securing funding for your startup. From angel investors to venture capital, bootstrapping to crowdfunding, we cover all funding options and strategies to help you raise the capital you need to grow.',
	),
	'green-economy' => array(
		'title' => 'Green Economy',
		'tagline' => 'Build a Sustainable and Profitable Business',
		'description' => 'Learn how to build a sustainable business that\'s both environmentally responsible and profitable. From green technologies to sustainable business practices, we provide insights on how small businesses can contribute to a greener economy while growing their bottom line.',
	),
	'remote-work' => array(
		'title' => 'Remote Work',
		'tagline' => 'Master the Future of Work',
		'description' => 'Everything you need to know about building and managing remote teams. From tools and technologies to best practices and strategies, we help small businesses navigate the remote work revolution and build high-performing distributed teams.',
	),
);

$default_config = isset( $topic_config[ $page_slug ] ) ? $topic_config[ $page_slug ] : array(
	'title' => get_the_title(),
	'tagline' => '',
	'description' => '',
);

// Override with custom meta if exists
$custom_tagline = get_post_meta( get_the_ID(), '_sme_hero_tagline', true );
$custom_description = get_post_meta( get_the_ID(), '_sme_hero_description', true );

$config = $default_config;
if ( ! empty( $custom_tagline ) ) {
	$config['tagline'] = $custom_tagline;
}
if ( ! empty( $custom_description ) ) {
	$config['description'] = $custom_description;
}
?>

<div class="main-content-layout" style="padding: 0;">
	<div class="container" style="max-width: 100%; padding: 0; margin: 0;">
		<div class="main-content-area" style="width: 100%; padding: 0; margin: 0;">
			<?php while ( have_posts() ) : the_post(); ?>
				<!-- Hero Section -->
				<section class="niche-hero">
					<div class="container-inner">
						<h1>
							<?php echo esc_html( $config['title'] ); ?>
						</h1>
						<?php if ( $config['tagline'] ) : ?>
							<p class="hero-tagline">
								<?php echo esc_html( $config['tagline'] ); ?>
							</p>
						<?php endif; ?>
						<?php if ( $config['description'] ) : ?>
							<p class="hero-description">
								<?php echo esc_html( $config['description'] ); ?>
							</p>
						<?php endif; ?>
					</div>
				</section>

				<main class="container-inner">
					<?php
					// Include niche topic sections
					set_query_var( 'niche_topic_slug', $page_slug );
					set_query_var( 'niche_topic_config', $config );
					get_template_part( 'template-parts/components/niche-topic-sections' );
					?>
				</main>
			<?php endwhile; ?>
		</div>
	</div>
</div>

<?php
get_footer();

