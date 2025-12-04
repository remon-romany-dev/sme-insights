<?php
/**
 * SME Insights Theme Functions
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set default tagline on theme activation
 */
function sme_set_default_tagline() {
	$current_desc = get_bloginfo( 'description' );
	$default_wp_desc = 'Just another WordPress site';
	$new_desc = 'Empowering SMEs with Strategic Business Insights';
	
	if ( empty( $current_desc ) || $current_desc === $default_wp_desc ) {
		update_option( 'blogdescription', $new_desc );
	}
}
add_action( 'after_switch_theme', 'sme_set_default_tagline' );

/**
 * Restore AI in Business category description
 * Run by visiting: /wp-admin/admin.php?page=restore-ai-in-business
 */
function sme_restore_ai_in_business() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Permission denied' );
	}
	
	if ( ! isset( $_GET['restore'] ) || $_GET['restore'] !== '1' ) {
		return;
	}
	
	$term = get_term_by( 'slug', 'ai-in-business', 'main_category' );
	
	if ( ! $term || is_wp_error( $term ) ) {
		wp_die( 'Category "ai-in-business" not found.' );
	}
	
	$default_description = 'Explore how artificial intelligence is transforming small businesses. From automation to customer service, discover the latest AI tools and strategies that can help your business grow.';
	
	$result = wp_update_term( $term->term_id, 'main_category', array(
		'description' => $default_description
	) );
	
	if ( is_wp_error( $result ) ) {
		wp_die( 'Error: ' . $result->get_error_message() );
	}
	
	$icon = get_term_meta( $term->term_id, 'category_icon', true );
	$color = get_term_meta( $term->term_id, 'category_color', true );
	
	if ( empty( $icon ) ) {
		update_term_meta( $term->term_id, 'category_icon', '🤖' );
	}
	
	if ( empty( $color ) ) {
		update_term_meta( $term->term_id, 'category_color', '#2563eb' );
	}
	
	wp_redirect( admin_url( 'edit-tags.php?taxonomy=main_category&restored=1' ) );
	exit;
}
add_action( 'admin_init', 'sme_restore_ai_in_business' );

/**
 * Add editor notice for Niche Topic Page template
 */
function sme_niche_topic_editor_notice() {
	global $post;
	
	if ( ! $post || ! is_admin() ) {
		return;
	}
	
	$template = get_page_template_slug( $post->ID );
	
	if ( $template === 'page-niche-topic.php' ) {
		wp_enqueue_script( 'sme-niche-topic-notice', get_template_directory_uri() . '/assets/js/niche-topic-notice.js', array( 'wp-element', 'wp-editor', 'wp-data', 'wp-notices' ), SME_THEME_VERSION, true );
	}
}
add_action( 'enqueue_block_editor_assets', 'sme_niche_topic_editor_notice' );

/**
 * Fix WordPress core commands errors in admin panel
 */
function sme_fix_admin_js_errors() {
	if ( ! is_admin() ) {
		return;
	}
	?>
	<script>
	(function() {
		'use strict';
		
		// Fix wp.coreCommands.initializeCommandPalette error
		if (typeof window.wp === 'undefined') {
			window.wp = {};
		}
		if (typeof window.wp.coreCommands === 'undefined') {
			window.wp.coreCommands = {};
		}
		if (typeof window.wp.coreCommands.initializeCommandPalette === 'undefined') {
			window.wp.coreCommands.initializeCommandPalette = function() {
				return;
			};
		}
		
		// Fix wp.router.privateApis error
		if (typeof window.wp.router === 'undefined') {
			window.wp.router = {};
		}
		if (typeof window.wp.router.privateApis === 'undefined') {
			window.wp.router.privateApis = {};
		}
		
		// Fix wp.editPost.initializeEditor error
		if (typeof window.wp.editPost === 'undefined') {
			window.wp.editPost = {};
		}
		if (typeof window.wp.editPost.initializeEditor === 'undefined') {
			window.wp.editPost.initializeEditor = function() {
				return;
			};
		}
	})();
	</script>
	<?php
}
add_action( 'admin_head', 'sme_fix_admin_js_errors', 1 );

define( 'SME_THEME_VERSION', '1.1.0' );
define( 'SME_THEME_DIR', get_template_directory() );
define( 'SME_THEME_URI', get_template_directory_uri() );
define( 'SME_THEME_ASSETS', SME_THEME_URI . '/assets' );
define( 'SME_THEME_INC', SME_THEME_DIR . '/inc' );

spl_autoload_register( function( $class_name ) {
	if ( strpos( $class_name, 'SME_' ) !== 0 ) {
		return;
	}
	
	$class_file = str_replace( array( 'SME_', '_' ), array( '', '-' ), $class_name );
	$class_file = strtolower( $class_file );
	$file_path = SME_THEME_INC . '/class-' . $class_file . '.php';
	
	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	}
} );

$required_files = array(
	'class-theme-setup.php',
	'class-post-types.php',
	'class-taxonomies.php',
	'class-flexible-content.php',
	'class-post-meta.php',
	'class-assets.php',
	'class-blocks.php',
	'class-helpers.php',
	'functions-helpers.php',
	'class-theme-customizer.php',
	'class-page-setup.php',
	'class-content-importer.php',
	'class-content-manager.php',
	'class-image-optimizer.php',
	'class-sitemap.php',
	'class-export-helper.php',
	'class-cache.php',
	'class-cache-helper.php',
	'class-theme-dashboard.php',
	'class-visual-editor.php',
	'class-page-builder-blocks.php',
	'class-template-customizer.php',
	'class-template-manager.php',
	'class-system-check.php',
	'class-design-flexibility.php',
	'class-universal-editor.php',
	'class-code-review.php',
	'class-quick-editor.php',
	'class-theme-independence.php',
	'class-code-validator.php',
	'class-seo-optimizer.php',
	'class-security.php',
	'class-database-cleaner.php',
);

foreach ( $required_files as $file ) {
	$file_path = SME_THEME_INC . '/' . $file;
	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( sprintf( 'SME Insights: Missing file %s', $file ) );
	}
}

$theme_classes = array(
	'SME_Security',
	'SME_Theme_Setup',
	'SME_Post_Types',
	'SME_Taxonomies',
	'SME_Flexible_Content',
	'SME_Post_Meta',
	'SME_Assets',
	'SME_Blocks',
	'SME_Page_Setup',
	'SME_Content_Importer',
	'SME_Content_Manager',
	'SME_Image_Optimizer',
	'SME_Sitemap',
	'SME_Export_Helper',
	'SME_Cache',
	'SME_Cache_Helper',
	'SME_Theme_Dashboard',
	'SME_Visual_Editor',
	'SME_Page_Builder_Blocks',
	'SME_Template_Customizer',
	'SME_Template_Manager',
	'SME_System_Check',
	'SME_Design_Flexibility',
	'SME_Universal_Editor',
	'SME_Code_Review',
	'SME_Quick_Editor',
	'SME_Theme_Independence',
	'SME_Code_Validator',
	'SME_SEO_Optimizer',
	'SME_Theme_Customizer',
	'SME_Database_Cleaner',
);

foreach ( $theme_classes as $class_name ) {
	if ( class_exists( $class_name ) && method_exists( $class_name, 'get_instance' ) ) {
		$class_name::get_instance();
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( sprintf( 'SME Insights: Class %s not found or missing get_instance method', $class_name ) );
	}
}

/**
 * Add custom user contact methods
 * 
 * @param array $contactmethods Existing contact methods
 * @return array Modified contact methods with LinkedIn and Twitter
 */
add_filter( 'user_contactmethods', 'sme_add_user_contact_methods' );
function sme_add_user_contact_methods( $contactmethods ) {
	if ( ! is_array( $contactmethods ) ) {
		$contactmethods = array();
	}
	
	$contactmethods['linkedin'] = __( 'LinkedIn URL', 'sme-insights' );
	$contactmethods['twitter'] = __( 'Twitter URL', 'sme-insights' );
	
	return $contactmethods;
}

/**
 * Add Meta Boxes for Niche Topic Pages
 */
add_action( 'add_meta_boxes', 'sme_add_niche_topic_meta_boxes' );
function sme_add_niche_topic_meta_boxes() {
	global $post;
	if ( ! $post ) {
		return;
	}
	
	$template = get_page_template_slug( $post->ID );
	if ( $template === 'page-niche-topic.php' || $post->post_name === 'ai-in-business' || $post->post_name === 'ecommerce-trends' || $post->post_name === 'startup-funding' || $post->post_name === 'green-economy' || $post->post_name === 'remote-work' ) {
		// Hero Section Meta Box
		add_meta_box(
			'sme_hero_section',
			__( 'Hero Section', 'sme-insights' ),
			'sme_render_hero_meta_box',
			'page',
			'normal',
			'high'
		);
		
		// Tools & Resources Meta Box
		add_meta_box(
			'sme_tools_resources',
			__( 'Tools & Resources Section', 'sme-insights' ),
			'sme_render_tools_resources_meta_box',
			'page',
			'normal',
			'high'
		);
		
		// Experts Meta Box
		add_meta_box(
			'sme_experts_meta_box',
			__( 'Our Experts Section', 'sme-insights' ),
			'sme_render_experts_meta_box',
			'page',
			'normal',
			'high'
		);
	}
}

/**
 * Render Hero Section Meta Box
 */
function sme_render_hero_meta_box( $post ) {
	wp_nonce_field( 'sme_hero_meta_box', 'sme_hero_meta_box_nonce' );
	
	$hero_tagline = get_post_meta( $post->ID, '_sme_hero_tagline', true );
	$hero_description = get_post_meta( $post->ID, '_sme_hero_description', true );
	
	// Get default values from template config
	$page_slug = $post->post_name;
	$topic_config = array(
		'ai-in-business' => array(
			'tagline' => 'Transform Your Business with Artificial Intelligence',
			'description' => 'Here at SME Insights, we cover everything you need to know about implementing AI in your business, from choosing the right tools to building AI strategies that drive real results. Whether you\'re exploring AI for the first time or looking to scale your existing AI initiatives, our comprehensive guides and expert insights will help you harness the power of artificial intelligence.',
		),
	);
	
	$default_tagline = isset( $topic_config[ $page_slug ]['tagline'] ) ? $topic_config[ $page_slug ]['tagline'] : '';
	$default_description = isset( $topic_config[ $page_slug ]['description'] ) ? $topic_config[ $page_slug ]['description'] : '';
	
	?>
	<div class="sme-hero-meta-box">
		<p style="margin-bottom: 20px; color: #666;">
			<strong>Instructions:</strong> Customize the Hero Section that appears at the top of the page. Leave fields empty to use default values.
		</p>
		
		<table class="form-table" style="width: 100%;">
			<tr>
				<th scope="row" style="width: 150px; padding: 10px 0;">
					<label for="hero_tagline">Tagline</label>
				</th>
				<td>
					<input type="text" id="hero_tagline" name="hero_tagline" value="<?php echo esc_attr( $hero_tagline ); ?>" style="width: 100%; max-width: 600px;" placeholder="<?php echo esc_attr( $default_tagline ); ?>">
					<p class="description">The subtitle that appears below the main title (e.g., "Transform Your Business with Artificial Intelligence")</p>
				</td>
			</tr>
			<tr>
				<th scope="row" style="padding: 10px 0;">
					<label for="hero_description">Description</label>
				</th>
				<td>
					<textarea id="hero_description" name="hero_description" rows="5" style="width: 100%; max-width: 800px;" placeholder="<?php echo esc_attr( $default_description ); ?>"><?php echo esc_textarea( $hero_description ); ?></textarea>
					<p class="description">The main description paragraph that appears in the Hero Section.</p>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

/**
 * Render Tools & Resources Meta Box
 */
function sme_render_tools_resources_meta_box( $post ) {
	wp_nonce_field( 'sme_tools_resources_meta_box', 'sme_tools_resources_meta_box_nonce' );
	
	$tools_data = get_post_meta( $post->ID, '_sme_tools_resources', true );
	if ( ! is_array( $tools_data ) ) {
		$tools_data = array(
			array( 'icon' => '🤖', 'title' => '', 'description' => '', 'link' => '#', 'link_text' => 'View Guide' ),
			array( 'icon' => '📈', 'title' => '', 'description' => '', 'link' => '#', 'link_text' => 'Calculate ROI' ),
			array( 'icon' => '📚', 'title' => '', 'description' => '', 'link' => '#', 'link_text' => 'Get Checklist' ),
			array( 'icon' => '💡', 'title' => '', 'description' => '', 'link' => '#', 'link_text' => 'Explore Library' ),
		);
	}
	
	while ( count( $tools_data ) < 4 ) {
		$tools_data[] = array( 'icon' => '', 'title' => '', 'description' => '', 'link' => '#', 'link_text' => '' );
	}
	$tools_data = array_slice( $tools_data, 0, 4 );
	
	$default_icons = array( '🤖', '📈', '📚', '💡' );
	$default_titles = array( 'Tools Comparison Guide', 'ROI Calculator', 'Implementation Checklist', 'Use Cases Library' );
	$default_descriptions = array(
		'Compare the top tools for small businesses with our comprehensive comparison guide. Find the perfect solution for your needs and budget.',
		'Calculate the potential return on investment for implementations in your business. Estimate savings, efficiency gains, and revenue impact.',
		'A step-by-step checklist to guide you through implementation, from planning to deployment. Ensure nothing is missed in your journey.',
		'Browse our library of real-world use cases across different industries. Get inspired and find solutions that match your business needs.',
	);
	$default_link_texts = array( 'View Guide', 'Calculate ROI', 'Get Checklist', 'Explore Library' );
	
	?>
	<div class="sme-tools-resources-meta-box">
		<p style="margin-bottom: 20px; color: #666;">
			<strong>Instructions:</strong> Customize the 4 resource cards in the "Tools & Resources" section. Leave fields empty to use default values. The title will automatically include the page topic name.
		</p>
		
		<?php for ( $i = 0; $i < 4; $i++ ) : 
			$tool = isset( $tools_data[ $i ] ) ? $tools_data[ $i ] : array( 'icon' => $default_icons[ $i ], 'title' => '', 'description' => '', 'link' => '#', 'link_text' => $default_link_texts[ $i ] );
		?>
			<div style="border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; background: #f9f9f9; border-radius: 4px;">
				<h3 style="margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #0073aa;">Resource Card <?php echo $i + 1; ?></h3>
				
				<table class="form-table" style="width: 100%;">
					<tr>
						<th scope="row" style="width: 150px; padding: 10px 0;">
							<label for="tool_<?php echo $i; ?>_icon">Icon (Emoji)</label>
						</th>
						<td>
							<input type="text" id="tool_<?php echo $i; ?>_icon" name="tools[<?php echo $i; ?>][icon]" value="<?php echo esc_attr( $tool['icon'] ); ?>" style="width: 100px; font-size: 24px; text-align: center;" placeholder="<?php echo esc_attr( $default_icons[ $i ] ); ?>">
							<p class="description">Enter an emoji icon (e.g., 🤖, 📈, 📚, 💡)</p>
						</td>
					</tr>
					<tr>
						<th scope="row" style="padding: 10px 0;">
							<label for="tool_<?php echo $i; ?>_title">Title</label>
						</th>
						<td>
							<input type="text" id="tool_<?php echo $i; ?>_title" name="tools[<?php echo $i; ?>][title]" value="<?php echo esc_attr( $tool['title'] ); ?>" style="width: 100%; max-width: 500px;" placeholder="<?php echo esc_attr( $default_titles[ $i ] ); ?>">
							<p class="description">The page topic name will be automatically added before the title (e.g., "AI in Business Tools Comparison Guide")</p>
						</td>
					</tr>
					<tr>
						<th scope="row" style="padding: 10px 0;">
							<label for="tool_<?php echo $i; ?>_description">Description</label>
						</th>
						<td>
							<textarea id="tool_<?php echo $i; ?>_description" name="tools[<?php echo $i; ?>][description]" rows="3" style="width: 100%; max-width: 700px;" placeholder="<?php echo esc_attr( $default_descriptions[ $i ] ); ?>"><?php echo esc_textarea( $tool['description'] ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row" style="padding: 10px 0;">
							<label for="tool_<?php echo $i; ?>_link">Link URL</label>
						</th>
						<td>
							<input type="url" id="tool_<?php echo $i; ?>_link" name="tools[<?php echo $i; ?>][link]" value="<?php echo esc_url( $tool['link'] ); ?>" style="width: 100%; max-width: 500px;" placeholder="#">
							<p class="description">The URL for the button link (use # for placeholder)</p>
						</td>
					</tr>
					<tr>
						<th scope="row" style="padding: 10px 0;">
							<label for="tool_<?php echo $i; ?>_link_text">Link Text</label>
						</th>
						<td>
							<input type="text" id="tool_<?php echo $i; ?>_link_text" name="tools[<?php echo $i; ?>][link_text]" value="<?php echo esc_attr( $tool['link_text'] ); ?>" style="width: 100%; max-width: 300px;" placeholder="<?php echo esc_attr( $default_link_texts[ $i ] ); ?>">
							<p class="description">The text for the button (e.g., "View Guide", "Calculate ROI")</p>
						</td>
					</tr>
				</table>
			</div>
		<?php endfor; ?>
	</div>
	<?php
}

/**
 * Render Experts Meta Box
 */
function sme_render_experts_meta_box( $post ) {
	wp_nonce_field( 'sme_experts_meta_box', 'sme_experts_meta_box_nonce' );
	
	$experts_data = get_post_meta( $post->ID, '_sme_experts_data', true );
	if ( ! is_array( $experts_data ) ) {
		$experts_data = array(
			array( 'name' => '', 'title' => 'Specialist', 'bio' => '', 'avatar' => '', 'linkedin' => '', 'twitter' => '' ),
			array( 'name' => '', 'title' => 'Consultant', 'bio' => '', 'avatar' => '', 'linkedin' => '', 'twitter' => '' ),
			array( 'name' => '', 'title' => 'Advisor', 'bio' => '', 'avatar' => '', 'linkedin' => '', 'twitter' => '' ),
		);
	}
	
	while ( count( $experts_data ) < 3 ) {
		$experts_data[] = array( 'name' => '', 'title' => '', 'bio' => '', 'avatar' => '', 'linkedin' => '', 'twitter' => '' );
	}
	$experts_data = array_slice( $experts_data, 0, 3 );
	
	$titles = array( 'Specialist', 'Consultant', 'Advisor' );
	?>
	<div class="sme-experts-meta-box">
		<p style="margin-bottom: 20px; color: #666;">
			<strong>Instructions:</strong> Fill in the information for up to 3 experts. Leave fields empty to use default data. The title (Specialist/Consultant/Advisor) will be combined with the page topic.
		</p>
		
		<?php for ( $i = 0; $i < 3; $i++ ) : 
			$expert = isset( $experts_data[ $i ] ) ? $experts_data[ $i ] : array( 'name' => '', 'title' => $titles[ $i ], 'bio' => '', 'avatar' => '', 'linkedin' => '', 'twitter' => '' );
		?>
			<div style="border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; background: #f9f9f9; border-radius: 4px;">
				<h3 style="margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #0073aa;">Expert <?php echo $i + 1; ?></h3>
				
				<table class="form-table" style="width: 100%;">
					<tr>
						<th scope="row" style="width: 150px; padding: 10px 0;">
							<label for="expert_<?php echo $i; ?>_name">Name</label>
						</th>
						<td>
							<input type="text" id="expert_<?php echo $i; ?>_name" name="experts[<?php echo $i; ?>][name]" value="<?php echo esc_attr( $expert['name'] ); ?>" style="width: 100%; max-width: 400px;" placeholder="e.g., Sarah Mitchell">
						</td>
					</tr>
					<tr>
						<th scope="row" style="padding: 10px 0;">
							<label for="expert_<?php echo $i; ?>_title">Title</label>
						</th>
						<td>
							<select id="expert_<?php echo $i; ?>_title" name="experts[<?php echo $i; ?>][title]" style="width: 100%; max-width: 400px;">
								<option value="Specialist" <?php selected( $expert['title'], 'Specialist' ); ?>>Specialist</option>
								<option value="Consultant" <?php selected( $expert['title'], 'Consultant' ); ?>>Consultant</option>
								<option value="Advisor" <?php selected( $expert['title'], 'Advisor' ); ?>>Advisor</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" style="padding: 10px 0;">
							<label for="expert_<?php echo $i; ?>_bio">Bio</label>
						</th>
						<td>
							<textarea id="expert_<?php echo $i; ?>_bio" name="experts[<?php echo $i; ?>][bio]" rows="4" style="width: 100%; max-width: 600px;" placeholder="Expert biography..."><?php echo esc_textarea( $expert['bio'] ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row" style="padding: 10px 0;">
							<label for="expert_<?php echo $i; ?>_avatar">Avatar URL</label>
						</th>
						<td>
							<input type="url" id="expert_<?php echo $i; ?>_avatar" name="experts[<?php echo $i; ?>][avatar]" value="<?php echo esc_url( $expert['avatar'] ); ?>" style="width: 100%; max-width: 600px;" placeholder="https://images.unsplash.com/...">
							<p class="description">Enter image URL (e.g., from Unsplash) or leave empty for default avatar.</p>
						</td>
					</tr>
					<tr>
						<th scope="row" style="padding: 10px 0;">
							<label for="expert_<?php echo $i; ?>_linkedin">LinkedIn URL</label>
						</th>
						<td>
							<input type="url" id="expert_<?php echo $i; ?>_linkedin" name="experts[<?php echo $i; ?>][linkedin]" value="<?php echo esc_url( $expert['linkedin'] ); ?>" style="width: 100%; max-width: 600px;" placeholder="https://linkedin.com/in/...">
						</td>
					</tr>
					<tr>
						<th scope="row" style="padding: 10px 0;">
							<label for="expert_<?php echo $i; ?>_twitter">Twitter URL</label>
						</th>
						<td>
							<input type="url" id="expert_<?php echo $i; ?>_twitter" name="experts[<?php echo $i; ?>][twitter]" value="<?php echo esc_url( $expert['twitter'] ); ?>" style="width: 100%; max-width: 600px;" placeholder="https://twitter.com/...">
						</td>
					</tr>
				</table>
			</div>
		<?php endfor; ?>
	</div>
	<?php
}

/**
 * Save Experts Meta Box Data
 */
add_action( 'save_post', 'sme_save_experts_meta_box' );
function sme_save_experts_meta_box( $post_id ) {
	if ( ! isset( $_POST['sme_experts_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['sme_experts_meta_box_nonce'], 'sme_experts_meta_box' ) ) {
		return;
	}
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	
	if ( get_post_type( $post_id ) !== 'page' ) {
		return;
	}
	
	if ( isset( $_POST['experts'] ) && is_array( $_POST['experts'] ) ) {
		$experts_data = array();
		
		foreach ( $_POST['experts'] as $index => $expert ) {
			$experts_data[] = array(
				'name'    => sanitize_text_field( $expert['name'] ),
				'title'   => sanitize_text_field( $expert['title'] ),
				'bio'     => sanitize_textarea_field( $expert['bio'] ),
				'avatar'  => esc_url_raw( $expert['avatar'] ),
				'linkedin' => esc_url_raw( $expert['linkedin'] ),
				'twitter' => esc_url_raw( $expert['twitter'] ),
			);
		}
		
		update_post_meta( $post_id, '_sme_experts_data', $experts_data );
	} else {
		delete_post_meta( $post_id, '_sme_experts_data' );
	}
}

/**
 * Create sample posts for niche topics
 * Creates 5 posts for each niche topic (AI in Business, E-commerce Trends, etc.)
 */
add_action( 'admin_init', 'sme_create_niche_topic_posts' );
function sme_create_niche_topic_posts() {
	$option_key = 'sme_niche_topic_posts_created';
	if ( get_option( $option_key ) ) {
		return;
	}
	
	$admin_users = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
	$author_id = ! empty( $admin_users ) ? $admin_users[0] : 1;
	
	$niche_topics = array(
		'ai-in-business' => array(
			'tag' => 'ai-in-business',
			'category' => 'Technology',
			'posts' => array(
				array(
					'title' => 'How AI is Transforming Small Business Operations',
					'excerpt' => 'Discover how artificial intelligence is revolutionizing the way small businesses operate, from automation to customer service.',
					'content' => '<h2>Introduction</h2><p>Artificial intelligence is no longer just for large corporations. Small businesses are increasingly adopting AI technologies to streamline operations, improve customer service, and drive growth. In fact, studies show that small businesses using AI see an average of 20% increase in productivity and 15% reduction in operational costs.</p><h2>The AI Revolution in Small Business</h2><p>From chatbots handling customer inquiries 24/7 to predictive analytics helping with inventory management, AI tools are becoming more accessible and affordable for small businesses. What once required a team of data scientists can now be implemented with user-friendly platforms designed specifically for small business owners.</p><h2>Key Areas of Transformation</h2><h3>Customer Service Automation</h3><p>AI-powered chatbots can handle routine customer inquiries, freeing up your team to focus on complex issues. These systems learn from each interaction, continuously improving their responses.</p><h3>Inventory Management</h3><p>Predictive analytics can forecast demand, helping you maintain optimal inventory levels and reduce waste. This is especially valuable for businesses with seasonal fluctuations.</p><h3>Marketing Personalization</h3><p>AI can analyze customer behavior and preferences to deliver personalized marketing messages, increasing engagement and conversion rates.</p><h2>Getting Started</h2><p>In this comprehensive guide, we\'ll explore the practical applications of AI for small businesses and how you can get started with implementation. The key is to start small, focus on one area where AI can make an immediate impact, and gradually expand as you see results.</p>',
					'image' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'Top 10 AI Tools Every Small Business Should Know',
					'excerpt' => 'Explore the best AI tools available for small businesses that can help automate tasks and improve efficiency.',
					'content' => '<h2>Introduction</h2><p>The AI tool landscape is vast, but not all tools are created equal. For small businesses, it\'s important to choose solutions that offer real value without breaking the bank. With hundreds of AI tools available, finding the right ones can be overwhelming.</p><h2>Our Top 10 AI Tools for Small Business</h2><h3>1. ChatGPT for Content Creation</h3><p>Perfect for generating marketing copy, blog posts, and customer communications. The free version offers substantial capabilities, while the paid version provides even more advanced features.</p><h3>2. Grammarly for Writing</h3><p>AI-powered writing assistant that helps improve grammar, tone, and clarity. Essential for professional communications and content creation.</p><h3>3. Canva AI for Design</h3><p>Create professional graphics and marketing materials with AI assistance. The AI features help generate designs based on simple text descriptions.</p><h3>4. HubSpot for CRM</h3><p>AI-powered customer relationship management that helps track leads, automate follow-ups, and predict sales opportunities.</p><h3>5. QuickBooks AI for Accounting</h3><p>Automated bookkeeping and financial insights powered by AI. Helps categorize expenses, generate reports, and identify financial trends.</p><h3>6. Zendesk for Customer Support</h3><p>AI chatbot that can handle customer inquiries 24/7, routing complex issues to human agents when needed.</p><h3>7. Mailchimp for Email Marketing</h3><p>AI-driven email campaigns that optimize send times, subject lines, and content for better engagement rates.</p><h3>8. Buffer for Social Media</h3><p>AI helps determine the best times to post and suggests content that resonates with your audience.</p><h3>9. Calendly for Scheduling</h3><p>AI-powered scheduling that eliminates back-and-forth emails and automatically finds the best meeting times.</p><h3>10. Shopify AI for E-commerce</h3><p>Product recommendations, inventory management, and customer insights powered by machine learning.</p><h2>Choosing the Right Tools</h2><p>We\'ve compiled this list of the top 10 AI tools that are specifically designed for small businesses, covering everything from marketing automation to accounting. When choosing tools, consider your specific needs, budget, and the learning curve required.</p>',
					'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'AI Implementation Guide for Small Businesses',
					'excerpt' => 'A step-by-step guide to implementing AI in your small business, from planning to execution.',
					'content' => '<h2>Introduction</h2><p>Implementing AI in your small business doesn\'t have to be overwhelming. With the right approach, you can start seeing results quickly. This comprehensive guide walks you through the entire process, from identifying opportunities to measuring ROI.</p><h2>Step 1: Assess Your Needs</h2><p>Before diving into AI, identify areas where automation and intelligence can make the biggest impact. Common starting points include customer service, inventory management, marketing, and administrative tasks.</p><h2>Step 2: Set Clear Goals</h2><p>Define what success looks like. Are you looking to reduce costs, increase efficiency, improve customer satisfaction, or drive revenue? Clear goals help you choose the right tools and measure success.</p><h2>Step 3: Start Small</h2><p>Don\'t try to implement everything at once. Choose one area to start with, such as a chatbot for customer service or AI-powered email marketing. Master that before moving to the next area.</p><h2>Step 4: Choose the Right Tools</h2><p>Research tools that fit your budget and needs. Many AI tools offer free trials or freemium models, allowing you to test before committing.</p><h2>Step 5: Train Your Team</h2><p>Ensure your team understands how to use the new AI tools effectively. Most modern AI tools are designed to be user-friendly, but proper training ensures maximum benefit.</p><h2>Step 6: Monitor and Optimize</h2><p>Track key metrics to measure the impact of your AI implementation. Use this data to optimize and expand your AI initiatives over time.</p>',
					'image' => 'https://images.unsplash.com/photo-1555255707-c07966088b7b?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'The ROI of AI: Measuring Success in Small Business',
					'excerpt' => 'Learn how to measure the return on investment when implementing AI solutions in your small business.',
					'content' => '<h2>Introduction</h2><p>Understanding ROI is crucial when investing in AI. Many small business owners hesitate to adopt AI because they\'re unsure about the return on investment. This article helps you identify key metrics and measure the success of your AI initiatives.</p><h2>Key Metrics to Track</h2><h3>Time Savings</h3><p>Measure how much time AI tools save your team. For example, if a chatbot handles 50 customer inquiries per day that would take 2 minutes each, that\'s 100 minutes saved daily.</p><h3>Cost Reduction</h3><p>Track reductions in operational costs. AI can reduce the need for additional staff, lower error rates, and optimize resource allocation.</p><h3>Revenue Impact</h3><p>Monitor increases in sales, conversion rates, and customer lifetime value. AI-powered personalization and recommendations often lead to higher sales.</p><h3>Customer Satisfaction</h3><p>Use surveys and feedback to measure improvements in customer satisfaction. Faster response times and 24/7 availability typically improve customer experience.</p><h2>Calculating ROI</h2><p>ROI = (Gains from AI - Cost of AI) / Cost of AI × 100</p><p>Include both direct costs (subscription fees) and indirect costs (training time, implementation) in your calculations. Most small businesses see positive ROI within 3-6 months of implementation.</p><h2>Real-World Examples</h2><p>Small businesses report average ROI of 200-300% within the first year, with some seeing returns as high as 500% in specific areas like customer service automation.</p>',
					'image' => 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'AI and Customer Service: Revolutionizing Small Business Support',
					'excerpt' => 'Discover how AI-powered customer service tools can help small businesses provide better support 24/7.',
					'content' => '<h2>Introduction</h2><p>Customer service is one of the most impactful areas where AI can make a difference for small businesses. With limited resources, providing excellent customer support can be challenging. AI-powered solutions level the playing field, allowing small businesses to compete with larger companies.</p><h2>The Power of AI Chatbots</h2><p>AI chatbots can handle routine inquiries instantly, 24/7, without requiring additional staff. They can answer frequently asked questions, process orders, schedule appointments, and escalate complex issues to human agents when needed.</p><h2>Benefits for Small Businesses</h2><h3>24/7 Availability</h3><p>Your customers can get help anytime, even outside business hours. This improves customer satisfaction and can lead to increased sales.</p><h3>Cost Efficiency</h3><p>One chatbot can handle hundreds of conversations simultaneously, reducing the need for a large customer service team.</p><h3>Consistency</h3><p>AI provides consistent, accurate information every time, reducing errors and improving customer trust.</p><h2>Implementation Tips</h2><p>Start with a simple chatbot that handles your most common questions. As you gather data on customer interactions, you can expand its capabilities. Most modern chatbot platforms are easy to set up and don\'t require technical expertise.</p><h2>Best Practices</h2><p>Ensure your chatbot has a clear path to human support when needed. Train it on your specific products and services, and regularly review conversations to identify areas for improvement.</p>',
					'image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop',
				),
			),
		),
		'ecommerce-trends' => array(
			'tag' => 'ecommerce-trends',
			'category' => 'Marketing',
			'posts' => array(
				array(
					'title' => 'E-commerce Trends Shaping 2024: What Small Businesses Need to Know',
					'excerpt' => 'Stay ahead of the curve with the latest e-commerce trends that are reshaping online retail.',
					'content' => '<h2>Introduction</h2><p>The e-commerce landscape is constantly evolving. From social commerce to voice shopping, new trends are emerging that small businesses need to understand. Staying ahead of these trends can give you a competitive advantage and help you reach more customers.</p><h2>Key Trends for 2024</h2><h3>Social Commerce</h3><p>Shopping directly on social media platforms is becoming mainstream. Instagram Shopping, Facebook Marketplace, and TikTok Shop allow customers to purchase without leaving the platform.</p><h3>Sustainable E-commerce</h3><p>Consumers are increasingly conscious of environmental impact. Eco-friendly packaging, carbon-neutral shipping, and sustainable products are becoming differentiators.</p><h3>Personalization at Scale</h3><p>AI-powered personalization allows small businesses to deliver customized shopping experiences similar to major retailers, improving conversion rates and customer satisfaction.</p><h3>Mobile-First Shopping</h3><p>Over 60% of online purchases now happen on mobile devices. Optimizing for mobile is no longer optional—it\'s essential.</p><h3>Voice Commerce</h3><p>Voice shopping through smart speakers is growing. Optimizing your product listings for voice search can capture this emerging market.</p><h2>How to Leverage These Trends</h2><p>This article covers the most important e-commerce trends for 2024 and how small businesses can leverage them. Start by identifying which trends align with your business model and customer base, then prioritize implementation based on potential impact.</p>',
					'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'Mobile Commerce: Optimizing Your Store for Mobile Shoppers',
					'excerpt' => 'Learn how to optimize your e-commerce store for mobile devices and capture the growing mobile shopping market.',
					'content' => '<h2>Introduction</h2><p>Mobile commerce is no longer optional. With over 60% of online purchases happening on mobile devices, optimizing for mobile is essential. A poor mobile experience can cost you sales and damage your brand reputation.</p><h2>Why Mobile Optimization Matters</h2><p>Studies show that 53% of mobile site visits are abandoned if pages take longer than 3 seconds to load. Additionally, 61% of users are unlikely to return to a mobile site they had trouble accessing.</p><h2>Key Optimization Strategies</h2><h3>Fast Loading Times</h3><p>Optimize images, minimize code, and use a mobile-friendly hosting solution. Aim for page load times under 3 seconds.</p><h3>Responsive Design</h3><p>Ensure your site looks and functions perfectly on all screen sizes. Test on various devices to ensure consistency.</p><h3>Simplified Navigation</h3><p>Mobile users prefer simple, intuitive navigation. Use hamburger menus, clear categories, and easy-to-tap buttons.</p><h3>Streamlined Checkout</h3><p>Reduce friction in the checkout process. Offer guest checkout, save payment information, and minimize form fields.</p><h3>Touch-Friendly Elements</h3><p>Make buttons and links large enough for easy tapping. The recommended minimum size is 44x44 pixels.</p><h2>Mobile-Specific Features</h2><p>Consider implementing features like one-click purchasing, mobile wallets (Apple Pay, Google Pay), and push notifications for cart abandonment.</p>',
					'image' => 'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'Social Commerce: Selling Directly on Social Media Platforms',
					'excerpt' => 'Explore how small businesses can leverage social commerce to sell products directly on social media.',
					'content' => '<h2>Introduction</h2><p>Social commerce is transforming how businesses sell online. Platforms like Instagram and Facebook now offer integrated shopping features that allow customers to purchase products without leaving the social media app. This seamless experience is driving significant sales growth.</p><h2>What is Social Commerce?</h2><p>Social commerce combines social media and e-commerce, allowing businesses to sell products directly through social platforms. Instead of redirecting users to your website, they can complete purchases within the social media app.</p><h2>Major Platforms</h2><h3>Instagram Shopping</h3><p>Tag products in posts and stories, create a shop tab on your profile, and enable in-app checkout. Instagram\'s visual nature makes it perfect for product discovery.</p><h3>Facebook Shops</h3><p>Create a customizable online store on Facebook and Instagram. Customers can browse, save, and purchase products directly.</p><h3>TikTok Shop</h3><p>The newest player in social commerce, TikTok Shop allows creators and businesses to sell products through short-form video content.</p><h3>Pinterest Shopping</h3><p>Pinterest\'s shopping features help users discover and purchase products through visual search and product pins.</p><h2>Benefits for Small Businesses</h2><p>Social commerce reduces friction in the buying process, increases impulse purchases, and allows you to reach customers where they already spend time. It\'s particularly effective for visual products and lifestyle brands.</p><h2>Getting Started</h2><p>Start with one platform that aligns with your target audience. Set up your shop, optimize product listings with high-quality images, and promote your products through organic content and paid ads.</p>',
					'image' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'E-commerce Conversion Rate Optimization: A Complete Guide',
					'excerpt' => 'Improve your online store\'s conversion rate with these proven optimization strategies.',
					'content' => '<h2>Introduction</h2><p>Conversion rate optimization is crucial for e-commerce success. Even a small improvement in conversion rate can significantly impact your revenue. This guide covers proven strategies that can help you turn more visitors into customers.</p><h2>Understanding Conversion Rates</h2><p>The average e-commerce conversion rate is around 2-3%. However, top-performing stores achieve rates of 5% or higher. The key is continuous testing and optimization.</p><h2>Key Optimization Areas</h2><h3>Product Pages</h3><p>High-quality images, detailed descriptions, customer reviews, and clear pricing are essential. Include multiple product angles and lifestyle images.</p><h3>Checkout Process</h3><p>Reduce steps, offer guest checkout, show progress indicators, and display trust signals like security badges and return policies.</p><h3>Site Speed</h3><p>Fast-loading pages reduce bounce rates. Optimize images, use a CDN, and minimize code to improve load times.</p><h3>Mobile Experience</h3><p>Ensure your site is fully optimized for mobile devices. Test the checkout process on various mobile devices.</p><h3>Trust Signals</h3><p>Display customer reviews, security badges, money-back guarantees, and clear return policies to build trust.</p><h2>Testing and Measurement</h2><p>Use A/B testing to compare different versions of your pages. Test one element at a time (headlines, images, buttons, etc.) and measure the impact on conversions.</p><h2>Common Mistakes to Avoid</h2><p>Avoid cluttered pages, hidden shipping costs, complicated forms, and slow checkout processes. These are major conversion killers.</p>',
					'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'The Future of Online Payments: What Small E-commerce Businesses Should Know',
					'excerpt' => 'Stay updated on the latest payment technologies and how they can benefit your online store.',
					'content' => '<h2>Introduction</h2><p>Payment technology is evolving rapidly. From digital wallets to buy now, pay later options, understanding payment trends is essential for staying competitive. The payment experience can make or break a sale.</p><h2>Emerging Payment Methods</h2><h3>Digital Wallets</h3><p>Apple Pay, Google Pay, and PayPal offer one-click checkout that reduces friction and increases conversions. They\'re becoming standard expectations for online shoppers.</p><h3>Buy Now, Pay Later (BNPL)</h3><p>Services like Afterpay, Klarna, and Affirm allow customers to split payments into installments. This can increase average order value and attract price-sensitive customers.</p><h3>Cryptocurrency Payments</h3><p>While still emerging, accepting cryptocurrency can attract tech-savvy customers and position your business as innovative.</p><h3>Contactless Payments</h3><p>NFC technology enables quick, secure payments through mobile devices, improving the checkout experience.</p><h2>Benefits for Small Businesses</h2><p>Offering multiple payment options can increase conversion rates by 30% or more. Customers prefer familiar payment methods, and providing options reduces cart abandonment.</p><h2>Security Considerations</h2><p>Ensure all payment methods comply with PCI DSS standards. Use reputable payment processors and implement fraud detection measures.</p><h2>Implementation Tips</h2><p>Start with the most popular payment methods in your market. Monitor which methods your customers prefer and add more options based on demand.</p>',
					'image' => 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=800&h=600&fit=crop',
				),
			),
		),
		'startup-funding' => array(
			'tag' => 'startup-funding',
			'category' => 'Finance',
			'posts' => array(
				array(
					'title' => 'Complete Guide to Startup Funding Options in 2024',
					'excerpt' => 'Explore all available funding options for startups, from bootstrapping to venture capital.',
					'content' => '<h2>Introduction</h2><p>Finding the right funding for your startup can be challenging. With so many options available, it\'s important to understand which type of funding aligns with your business stage, goals, and values. This comprehensive guide covers all your options.</p><h2>Types of Startup Funding</h2><h3>Bootstrapping</h3><p>Self-funding your startup using personal savings, revenue, or credit. This gives you complete control but limits growth speed.</p><h3>Friends and Family</h3><p>Raising capital from personal networks. Often the first external funding source, but requires clear agreements to protect relationships.</p><h3>Angel Investors</h3><p>High-net-worth individuals who invest in early-stage startups. They often provide mentorship in addition to capital.</p><h3>Venture Capital</h3><p>Professional investment firms that provide larger amounts of capital in exchange for equity. Typically for high-growth startups with significant potential.</p><h3>Crowdfunding</h3><p>Raising small amounts from many people through platforms like Kickstarter or Indiegogo. Great for product validation and marketing.</p><h3>Bank Loans</h3><p>Traditional financing through banks or SBA loans. Requires good credit and often collateral, but doesn\'t dilute ownership.</p><h3>Grants</h3><p>Non-repayable funds from government or organizations. Highly competitive but ideal for specific industries or research-focused startups.</p><h2>Choosing the Right Option</h2><p>Consider your business stage, funding needs, growth plans, and willingness to give up equity. Each option has pros and cons that should align with your long-term vision.</p>',
					'image' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'How to Pitch to Investors: Tips for Startup Founders',
					'excerpt' => 'Learn how to create a compelling pitch that gets investors interested in your startup.',
					'content' => '<h2>Introduction</h2><p>A great pitch can make all the difference when seeking funding. Investors see hundreds of pitches, so yours needs to stand out. Discover the key elements of a successful investor pitch.</p><h2>Essential Pitch Components</h2><h3>The Problem</h3><p>Clearly define the problem you\'re solving. Make it relatable and demonstrate that it\'s a real pain point for your target market.</p><h3>Your Solution</h3><p>Explain how your product or service solves this problem. Keep it simple and focus on the unique value proposition.</p><h3>Market Opportunity</h3><p>Show the size and potential of your market. Use data to demonstrate there\'s a significant opportunity worth pursuing.</p><h3>Business Model</h3><p>Explain how you make money. Be specific about pricing, revenue streams, and unit economics.</p><h3>Traction</h3><p>Share any progress you\'ve made: customers, revenue, partnerships, or key milestones. Traction validates your idea.</p><h3>Team</h3><p>Highlight why your team is uniquely qualified to execute this vision. Investors invest in people as much as ideas.</p><h3>Financial Projections</h3><p>Provide realistic financial forecasts. Show how you\'ll use the funding and when you expect to reach profitability.</p><h2>Pitch Best Practices</h2><p>Keep it concise (10-15 slides), tell a story, practice extensively, and be prepared for tough questions. Confidence and clarity are key.</p>',
					'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'Bootstrapping vs. External Funding: Which is Right for Your Startup?',
					'excerpt' => 'Compare bootstrapping and external funding to determine the best path for your startup.',
					'content' => '<h2>Introduction</h2><p>Both bootstrapping and external funding have their advantages. The right choice depends on your business model, growth goals, and personal preferences. This article helps you decide which approach fits your startup.</p><h2>Bootstrapping: Pros and Cons</h2><h3>Advantages</h3><p>Complete control over decisions, no dilution of ownership, focus on profitability from day one, and flexibility to pivot without investor approval.</p><h3>Disadvantages</h3><p>Limited resources can slow growth, personal financial risk, may miss market opportunities due to lack of capital, and can be stressful managing cash flow.</p><h2>External Funding: Pros and Cons</h2><h3>Advantages</h3><p>Faster growth potential, access to investor networks and expertise, reduced personal financial risk, and ability to scale quickly.</p><h3>Disadvantages</h3><p>Loss of equity and control, pressure to meet investor expectations, focus on growth over profitability, and time spent on fundraising.</p><h2>Hybrid Approach</h2><p>Many successful startups use a combination: bootstrap initially to validate the idea, then raise funding to scale. This approach minimizes dilution while maximizing growth potential.</p><h2>Making the Decision</h2><p>Consider your industry, competition, capital requirements, growth timeline, and personal risk tolerance. There\'s no one-size-fits-all answer.</p>',
					'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'Government Grants for Small Businesses: How to Apply',
					'excerpt' => 'Navigate the world of government grants and learn how to successfully apply for funding.',
					'content' => '<h2>Introduction</h2><p>Government grants can be a valuable source of funding that doesn\'t require repayment or equity. However, the application process can be complex and competitive. Learn how to find and apply for grants that match your business.</p><h2>Types of Government Grants</h2><h3>Federal Grants</h3><p>Available through agencies like the Small Business Administration (SBA), Department of Commerce, and National Science Foundation. Often focused on research, innovation, or specific industries.</p><h3>State Grants</h3><p>State-level programs supporting local businesses, economic development, and job creation. Requirements and amounts vary by state.</p><h3>Local Grants</h3><p>City or county programs designed to support local economic development and small business growth.</p><h2>Finding Grants</h2><p>Use resources like Grants.gov, SBA.gov, and your state\'s economic development website. Also check with local chambers of commerce and business development centers.</p><h2>Application Process</h2><h3>Eligibility Requirements</h3><p>Carefully review eligibility criteria. Grants often target specific industries, business sizes, or purposes.</p><h3>Application Components</h3><p>Most applications require: business plan, financial statements, project proposal, budget, and proof of need. Be thorough and specific.</p><h3>Tips for Success</h3><p>Start early, follow instructions precisely, demonstrate clear need and impact, provide supporting documentation, and consider hiring a grant writer for complex applications.</p><h2>Common Mistakes to Avoid</h2><p>Missing deadlines, incomplete applications, not demonstrating need, unrealistic budgets, and poor project descriptions are common reasons for rejection.</p>',
					'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'Crowdfunding for Startups: A Practical Guide',
					'excerpt' => 'Discover how crowdfunding can help you raise capital and validate your business idea.',
					'content' => '<h2>Introduction</h2><p>Crowdfunding has become a popular way to raise funds while simultaneously validating your business idea and building a customer base. Learn how to run a successful crowdfunding campaign.</p><h2>Types of Crowdfunding</h2><h3>Reward-Based</h3><p>Platforms like Kickstarter and Indiegogo where backers receive products or rewards. Great for product launches and validation.</p><h3>Equity Crowdfunding</h3><p>Investors receive equity in your company. Platforms like SeedInvest and Republic allow non-accredited investors to participate.</p><h3>Donation-Based</h3><p>Supporters contribute without expecting returns. Common for social causes and non-profits.</p><h3>Debt Crowdfunding</h3><p>Peer-to-peer lending where you borrow money from multiple lenders. Platforms like LendingClub facilitate these transactions.</p><h2>Planning Your Campaign</h2><h3>Set Realistic Goals</h3><p>Calculate exactly how much you need and why. Be transparent about how funds will be used.</p><h3>Create Compelling Content</h3><p>High-quality video, clear value proposition, and engaging story are essential. Show, don\'t just tell.</p><h3>Build Pre-Launch Audience</h3><p>Start building your audience weeks before launch. Email lists, social media, and personal networks are crucial.</p><h2>Running the Campaign</h2><p>Launch with momentum (aim for 30% funding in first 48 hours), update regularly, engage with backers, and leverage social media and press coverage.</p><h2>Post-Campaign</h2><p>Fulfill rewards on time, communicate regularly, and use the momentum to build your business and customer base.</p>',
					'image' => 'https://images.unsplash.com/photo-1553729459-efe14ef6055d?w=800&h=600&fit=crop',
				),
			),
		),
		'green-economy' => array(
			'tag' => 'green-economy',
			'category' => 'Growth',
			'posts' => array(
				array(
					'title' => 'Building a Sustainable Business: A Guide for Small Business Owners',
					'excerpt' => 'Learn how to build a business that\'s both profitable and environmentally responsible.',
					'content' => '<h2>Introduction</h2><p>Sustainability is no longer just a trend—it\'s a business imperative. Consumers increasingly prefer businesses that prioritize environmental responsibility, and sustainable practices often lead to cost savings. Discover how to make your business more sustainable.</p><h2>Why Sustainability Matters</h2><p>Sustainable businesses often see reduced operating costs, improved brand reputation, increased customer loyalty, and better employee retention. Additionally, regulations are increasingly favoring sustainable practices.</p><h2>Key Areas to Focus On</h2><h3>Energy Efficiency</h3><p>Switch to LED lighting, optimize HVAC systems, use energy-efficient equipment, and consider renewable energy sources like solar panels.</p><h3>Waste Reduction</h3><p>Implement recycling programs, reduce packaging, go paperless where possible, and find ways to repurpose or donate unused materials.</p><h3>Sustainable Sourcing</h3><p>Choose suppliers with sustainable practices, prioritize local suppliers to reduce transportation emissions, and select eco-friendly materials.</p><h3>Water Conservation</h3><p>Install low-flow fixtures, fix leaks promptly, use water-efficient equipment, and consider water recycling systems.</p><h2>Getting Started</h2><p>Start with an audit of your current practices. Identify the biggest environmental impacts and prioritize changes that offer both environmental and financial benefits. Set clear goals and track progress.</p><h2>Certifications and Recognition</h2><p>Consider pursuing certifications like B-Corp, LEED, or Energy Star. These can enhance your brand and demonstrate commitment to sustainability.</p>',
					'image' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'Green Technology Solutions for Small Businesses',
					'excerpt' => 'Explore affordable green technology solutions that can help reduce your business\'s environmental impact.',
					'content' => '<h2>Introduction</h2><p>Green technology is becoming more accessible and affordable for small businesses. These solutions not only reduce environmental impact but often save money in the long run. Learn about affordable solutions that can help your business go green.</p><h2>Affordable Green Tech Solutions</h2><h3>Smart Thermostats</h3><p>Programmable thermostats like Nest or Ecobee optimize heating and cooling, reducing energy costs by 10-15% while improving comfort.</p><h3>LED Lighting</h3><p>LED bulbs use 75% less energy and last 25 times longer than incandescent bulbs. The initial investment pays for itself quickly.</p><h3>Solar Panels</h3><p>Solar technology has become more affordable. Many businesses see ROI within 5-7 years, and tax incentives can reduce upfront costs.</p><h3>Energy Monitoring Systems</h3><p>Track energy usage in real-time to identify waste and optimize consumption. Many systems are now affordable for small businesses.</p><h3>Electric Vehicles</h3><p>If your business uses vehicles, consider electric options. Lower operating costs and environmental benefits make them increasingly attractive.</p><h3>Cloud Computing</h3><p>Moving to cloud services reduces the need for on-site servers, lowering energy consumption and IT costs.</p><h2>ROI Considerations</h2><p>While some green tech requires upfront investment, many solutions offer quick payback periods. Calculate ROI based on energy savings, tax incentives, and reduced maintenance costs.</p><h2>Getting Started</h2><p>Start with low-cost, high-impact solutions like LED lighting and smart thermostats. As you see savings, reinvest in larger projects like solar panels.</p>',
					'image' => 'https://images.unsplash.com/photo-1497435334941-8c899ee9e8e9?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'The Business Case for Sustainability: Why Going Green Makes Sense',
					'excerpt' => 'Understand the financial and strategic benefits of adopting sustainable business practices.',
					'content' => '<h2>Introduction</h2><p>Going green isn\'t just good for the planet—it\'s good for business. Beyond environmental benefits, sustainable practices offer significant financial and strategic advantages. Learn about the financial benefits of sustainability.</p><h2>Financial Benefits</h2><h3>Cost Savings</h3><p>Energy-efficient practices reduce utility bills. Waste reduction lowers disposal costs. Sustainable sourcing can reduce material costs over time.</p><h3>Tax Incentives</h3><p>Many governments offer tax credits and deductions for sustainable practices, renewable energy, and green technology investments.</p><h3>Increased Revenue</h3><p>Consumers increasingly prefer sustainable brands. Studies show customers are willing to pay more for sustainable products and services.</p><h2>Strategic Advantages</h2><h3>Competitive Differentiation</h3><p>Sustainability can differentiate your business in crowded markets. It\'s a unique selling proposition that resonates with modern consumers.</p><h3>Risk Mitigation</h3><p>Preparing for future regulations, reducing dependence on finite resources, and building resilience against climate-related disruptions.</p><h3>Attracting Talent</h3><p>Employees, especially younger generations, prefer working for companies with strong environmental values. This improves recruitment and retention.</p><h2>Long-Term Value</h2><p>Sustainable businesses are better positioned for long-term success. They\'re more resilient, adaptable, and aligned with evolving consumer and regulatory expectations.</p><h2>Getting Started</h2><p>Start with quick wins that offer immediate cost savings. Use those savings to fund larger sustainability initiatives, creating a self-reinforcing cycle of improvement.</p>',
					'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'How to Reduce Your Business\'s Carbon Footprint',
					'excerpt' => 'Practical steps small businesses can take to reduce their environmental impact.',
					'content' => '<h2>Introduction</h2><p>Reducing your carbon footprint doesn\'t have to be expensive or complicated. Many changes are simple, cost-effective, and can be implemented immediately. Here are practical steps you can take today.</p><h2>Quick Wins</h2><h3>Switch to Renewable Energy</h3><p>Many utilities offer green energy options. You can also purchase renewable energy certificates (RECs) to offset your energy use.</p><h3>Optimize Transportation</h3><p>Encourage carpooling, provide bike parking, offer remote work options, and consider electric or hybrid company vehicles.</p><h3>Reduce Paper Usage</h3><p>Go digital for invoices, receipts, and documents. When printing is necessary, use both sides and recycled paper.</p><h2>Medium-Term Changes</h2><h3>Improve Energy Efficiency</h3><p>Upgrade to LED lighting, install programmable thermostats, improve insulation, and maintain HVAC systems regularly.</p><h3>Waste Reduction</h3><p>Implement comprehensive recycling, compost organic waste, reduce packaging, and choose suppliers with sustainable practices.</p><h3>Water Conservation</h3><p>Install low-flow fixtures, fix leaks immediately, use drought-resistant landscaping, and consider water recycling.</p><h2>Long-Term Investments</h2><p>Consider solar panels, electric vehicle fleets, green building certifications, and sustainable supply chain partnerships.</p><h2>Measuring Impact</h2><p>Use carbon footprint calculators to measure your current impact and track improvements over time. Set reduction goals and celebrate milestones.</p>',
					'image' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'Sustainable Supply Chain Management for Small Businesses',
					'excerpt' => 'Learn how to build a sustainable supply chain that aligns with your business values.',
					'content' => '<h2>Introduction</h2><p>Your supply chain has a significant environmental impact, often representing the largest portion of your carbon footprint. For small businesses, building a sustainable supply chain can seem daunting, but it\'s achievable with the right approach. Discover how to make it more sustainable.</p><h2>Assessing Your Supply Chain</h2><p>Start by mapping your entire supply chain. Identify all suppliers, transportation methods, and processes. This helps you understand where the biggest environmental impacts occur.</p><h2>Key Strategies</h2><h3>Local Sourcing</h3><p>Prioritize local suppliers to reduce transportation emissions. Local sourcing also supports your community and can improve supply chain resilience.</p><h3>Supplier Selection</h3><p>Choose suppliers with sustainable practices. Ask about their environmental policies, certifications, and sustainability initiatives.</p><h3>Packaging Optimization</h3><p>Work with suppliers to reduce packaging, use recyclable materials, and eliminate unnecessary packaging layers.</p><h3>Transportation Efficiency</h3><p>Consolidate shipments, use efficient transportation modes, and plan routes to minimize distance and fuel consumption.</p><h2>Building Relationships</h2><p>Develop long-term relationships with sustainable suppliers. Work together to improve practices and share sustainability goals.</p><h2>Challenges and Solutions</h2><p>Cost concerns can be addressed by focusing on long-term savings and efficiency gains. Limited supplier options can be overcome by gradually building a network of sustainable partners.</p>',
					'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop',
				),
			),
		),
		'remote-work' => array(
			'tag' => 'remote-work',
			'category' => 'Growth',
			'posts' => array(
				array(
					'title' => 'Remote Work Best Practices for Small Businesses',
					'excerpt' => 'Learn how to effectively manage remote teams and maintain productivity.',
					'content' => '<h2>Introduction</h2><p>Remote work is here to stay. For small businesses, remote teams offer access to talent, reduced overhead costs, and increased flexibility. Discover best practices for managing remote teams in your small business.</p><h2>Communication is Key</h2><h3>Regular Check-ins</h3><p>Schedule daily or weekly team meetings to maintain connection and alignment. Use video calls to maintain personal connections.</p><h3>Clear Expectations</h3><p>Set clear goals, deadlines, and communication protocols. Document processes and make information easily accessible.</p><h3>Multiple Channels</h3><p>Use various communication tools: email for formal communication, instant messaging for quick questions, video for meetings, and project management tools for tracking work.</p><h2>Building Culture Remotely</h2><h3>Virtual Team Building</h3><p>Organize online social events, virtual coffee breaks, and team activities to build relationships and maintain morale.</p><h3>Recognition and Feedback</h3><p>Regularly recognize achievements and provide constructive feedback. Public recognition in team channels can boost morale.</p><h3>Shared Values</h3><p>Clearly communicate company values and ensure they\'re reflected in remote work practices and team interactions.</p><h2>Productivity Management</h2><p>Focus on outcomes rather than hours worked. Use project management tools to track progress. Trust your team and avoid micromanagement.</p><h2>Tools and Technology</h2><p>Invest in reliable communication and collaboration tools. Ensure your team has the necessary equipment and technical support.</p>',
					'image' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'Essential Tools for Remote Team Collaboration',
					'excerpt' => 'Discover the best tools and platforms for keeping your remote team connected and productive.',
					'content' => '<h2>Introduction</h2><p>The right tools can make all the difference in remote collaboration. With so many options available, choosing the right stack for your small business is crucial. Here are the essential tools every remote team needs.</p><h2>Communication Tools</h2><h3>Slack</h3><p>Team messaging platform with channels, direct messaging, and integrations. Great for real-time communication and team collaboration.</p><h3>Microsoft Teams</h3><p>Comprehensive platform combining chat, video calls, and file sharing. Ideal if you already use Microsoft 365.</p><h3>Zoom</h3><p>Video conferencing platform for meetings, webinars, and virtual events. Reliable and user-friendly.</p><h2>Project Management</h2><h3>Asana</h3><p>Task and project management with timelines, boards, and team collaboration features.</p><h3>Trello</h3><p>Simple kanban-style boards for visual project management. Great for smaller teams.</p><h3>Monday.com</h3><p>Customizable work management platform that adapts to your workflow.</p><h2>File Sharing and Storage</h2><h3>Google Drive</h3><p>Cloud storage with real-time collaboration on documents, spreadsheets, and presentations.</p><h3>Dropbox</h3><p>File sharing and cloud storage with easy collaboration features.</p><h3>OneDrive</h3><p>Microsoft\'s cloud storage solution, integrated with Office 365.</p><h2>Time Tracking</h2><p>Tools like Toggl, Harvest, or Clockify help track time, manage projects, and generate reports for billing and productivity analysis.</p><h2>Choosing Your Stack</h2><p>Start with free or low-cost options, then upgrade as your team grows. Consider integration capabilities and choose tools that work well together.</p>',
					'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'Building Company Culture in a Remote Work Environment',
					'excerpt' => 'Learn how to maintain and strengthen company culture when your team works remotely.',
					'content' => '<h2>Introduction</h2><p>Company culture doesn\'t have to suffer with remote work. In fact, remote work can strengthen culture when approached intentionally. Discover strategies for building a strong remote culture.</p><h2>Defining Your Culture</h2><p>Clearly articulate your company values, mission, and cultural norms. Document these and ensure they\'re reflected in all remote work practices and communications.</p><h2>Building Connection</h2><h3>Regular Virtual Gatherings</h3><p>Schedule regular team meetings, virtual coffee breaks, and social events. These informal interactions are crucial for relationship building.</p><h3>Onboarding</h3><p>Create a comprehensive remote onboarding process that introduces new hires to your culture, values, and team members.</p><h3>Celebrations</h3><p>Celebrate milestones, birthdays, and achievements virtually. Small gestures go a long way in building culture.</p><h2>Communication and Transparency</h2><p>Maintain open communication channels. Share company updates, celebrate wins, and be transparent about challenges. Regular all-hands meetings keep everyone aligned.</p><h2>Work-Life Balance</h2><p>Respect boundaries and encourage work-life balance. Model healthy practices and discourage after-hours communication unless urgent.</p><h2>Inclusive Practices</h2><p>Ensure all team members feel included regardless of location or time zone. Use asynchronous communication when needed and accommodate different schedules.</p><h2>Measuring Culture</h2><p>Regularly survey your team about culture, engagement, and satisfaction. Use feedback to continuously improve your remote culture initiatives.</p>',
					'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'Remote Work Security: Protecting Your Business Data',
					'excerpt' => 'Essential security practices for small businesses with remote teams.',
					'content' => '<h2>Introduction</h2><p>Security is crucial when working remotely. Remote work introduces new security challenges that small businesses must address. Learn how to protect your business data and systems.</p><h2>Essential Security Measures</h2><h3>VPN Usage</h3><p>Require employees to use a Virtual Private Network (VPN) when accessing company systems. This encrypts data transmission and protects against interception.</p><h3>Strong Passwords</h3><p>Enforce strong password policies and consider password managers. Implement two-factor authentication (2FA) for all accounts.</p><h3>Secure Wi-Fi</h3><p>Ensure employees use secure, password-protected Wi-Fi networks. Discourage use of public Wi-Fi for work activities.</p><h2>Device Management</h2><h3>Company Devices</h3><p>Provide company-owned devices when possible, or implement Bring Your Own Device (BYOD) policies with security requirements.</p><h3>Software Updates</h3><p>Keep all software, operating systems, and security tools updated. Enable automatic updates where possible.</p><h3>Antivirus and Firewalls</h3><p>Ensure all devices have updated antivirus software and firewalls enabled.</p><h2>Data Protection</h2><p>Use encrypted cloud storage, implement data backup procedures, limit access to sensitive data, and train employees on data handling best practices.</p><h2>Incident Response</h2><p>Have a plan for security incidents. Know who to contact, how to isolate threats, and how to recover from breaches.</p>',
					'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop',
				),
				array(
					'title' => 'The Future of Work: Hybrid Models for Small Businesses',
					'excerpt' => 'Explore hybrid work models and how they can benefit your small business.',
					'content' => '<h2>Introduction</h2><p>Hybrid work models offer the best of both worlds: the flexibility of remote work and the collaboration benefits of in-person interaction. Learn how to implement a hybrid model that works for your business.</p><h2>What is Hybrid Work?</h2><p>Hybrid work combines remote and in-office work. Employees split their time between working from home and the office, offering flexibility while maintaining team connection.</p><h2>Hybrid Model Options</h2><h3>Flexible Hybrid</h3><p>Employees choose when to work remotely and when to come to the office. Offers maximum flexibility but requires clear guidelines.</p><h3>Fixed Hybrid</h3><p>Set days for remote and in-office work (e.g., 3 days remote, 2 days in-office). Provides predictability and easier planning.</p><h3>Team-Based Hybrid</h3><p>Different teams or departments have different hybrid arrangements based on their needs and work styles.</p><h2>Benefits for Small Businesses</h2><p>Access to wider talent pool, reduced office space costs, improved work-life balance for employees, and increased productivity and satisfaction.</p><h2>Implementation Challenges</h2><p>Managing fairness between remote and in-office employees, maintaining team cohesion, ensuring equal opportunities, and coordinating schedules can be challenging.</p><h2>Best Practices</h2><p>Establish clear policies, invest in technology for seamless collaboration, schedule regular in-person team time, and ensure all employees have equal access to opportunities and information.</p><h2>Getting Started</h2><p>Start with a pilot program, gather feedback, and adjust your model based on what works best for your team and business needs.</p>',
					'image' => 'https://images.unsplash.com/photo-1497215842964-222b430dc094?w=800&h=600&fit=crop',
				),
			),
		),
	);
	
	foreach ( $niche_topics as $topic_slug => $topic_data ) {
		$tag = get_term_by( 'slug', $topic_data['tag'], 'article_tag' );
		if ( ! $tag ) {
			$tag_result = wp_insert_term( ucwords( str_replace( '-', ' ', $topic_data['tag'] ) ), 'article_tag', array( 'slug' => $topic_data['tag'] ) );
			if ( ! is_wp_error( $tag_result ) ) {
				$tag_id = $tag_result['term_id'];
			} else {
				continue;
			}
		} else {
			$tag_id = $tag->term_id;
		}
		
		$category = get_term_by( 'name', $topic_data['category'], 'main_category' );
		if ( ! $category ) {
			$categories = get_terms( array( 'taxonomy' => 'main_category', 'hide_empty' => false ) );
			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				$category = $categories[0];
			} else {
				continue;
			}
		}
		
		$days_ago = 0;
		foreach ( $topic_data['posts'] as $post_data ) {
			global $wpdb;
			$existing = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'post' AND post_status != 'trash' LIMIT 1",
				$post_data['title']
			) );
			
			if ( $existing ) {
				$days_ago++;
				continue;
			}
			
			$post_date = date( 'Y-m-d H:i:s', strtotime( '-' . $days_ago . ' days' ) );
			
			$post_id = wp_insert_post( array(
				'post_title'    => $post_data['title'],
				'post_content'  => $post_data['content'],
				'post_excerpt'  => $post_data['excerpt'],
				'post_status'   => 'publish',
				'post_type'     => 'post',
				'post_author'   => $author_id,
				'post_date'     => $post_date,
			) );
			
			if ( is_wp_error( $post_id ) ) {
				$days_ago++;
				continue;
			}
			
			wp_set_object_terms( $post_id, array( $category->term_id ), 'main_category', true );
			
			wp_set_object_terms( $post_id, array( $tag_id ), 'article_tag', true );
			
			if ( ! empty( $post_data['image'] ) ) {
				sme_set_featured_image_from_url( $post_id, $post_data['image'], $post_data['title'] );
			}
			
			if ( ! empty( $post_data['excerpt'] ) ) {
				update_post_meta( $post_id, '_sme_custom_excerpt', $post_data['excerpt'] );
			}
			
			$days_ago++;
		}
	}
	
	update_option( $option_key, true );
}

/**
 * Create sample posts for all main categories
 * Creates 5 posts for each main category (Finance, Marketing, Technology, Growth, Strategy)
 */
add_action( 'admin_init', 'sme_create_category_posts' );
function sme_create_category_posts() {
	$option_key = 'sme_category_posts_created';
	
	$admin_users = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
	$author_id = ! empty( $admin_users ) ? $admin_users[0] : 1;
	
	$category_posts = array(
		'Finance' => array(
			array(
				'title' => 'Small Business Budgeting: A Complete Guide for 2024',
				'excerpt' => 'Learn how to create and maintain an effective budget that helps your small business thrive and grow.',
				'content' => '<h2>Introduction</h2><p>Budgeting is the foundation of financial success for any small business. A well-planned budget helps you track expenses, plan for growth, and make informed financial decisions. This complete guide will walk you through creating and maintaining an effective budget for your small business.</p><h2>Why Budgeting Matters</h2><p>Without a budget, it\'s easy to overspend, miss opportunities, or face cash flow problems. A budget gives you control over your finances and helps you achieve your business goals.</p><h2>Creating Your First Budget</h2><h3>1. Calculate Your Income</h3><p>Start by estimating your monthly revenue. Look at past sales data, consider seasonal variations, and factor in any expected growth.</p><h3>2. List Fixed Expenses</h3><p>These are expenses that stay the same each month: rent, insurance, salaries, loan payments, and subscriptions.</p><h3>3. Estimate Variable Expenses</h3><p>These fluctuate based on business activity: materials, utilities, marketing, and travel costs.</p><h3>4. Plan for Unexpected Costs</h3><p>Set aside 10-15% of your budget for emergencies and unexpected expenses.</p><h2>Budgeting Best Practices</h2><p>Review your budget monthly, adjust as needed, track actual spending against your budget, and use budgeting software to simplify the process.</p>',
				'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Understanding Cash Flow: The Lifeblood of Your Business',
				'excerpt' => 'Master cash flow management to ensure your business always has the funds it needs to operate and grow.',
				'content' => '<h2>Introduction</h2><p>Cash flow is the movement of money in and out of your business. Positive cash flow means more money coming in than going out, which is essential for business survival and growth.</p><h2>Why Cash Flow Matters</h2><p>Even profitable businesses can fail due to poor cash flow management. Understanding and managing cash flow is crucial for long-term success.</p><h2>Improving Cash Flow</h2><h3>Speed Up Receivables</h3><p>Invoice promptly, offer early payment discounts, and follow up on overdue accounts.</p><h3>Manage Payables</h3><p>Negotiate better payment terms with suppliers, take advantage of early payment discounts when beneficial, and schedule payments strategically.</p><h3>Control Inventory</h3><p>Don\'t tie up cash in excess inventory. Use inventory management systems to optimize stock levels.</p><h2>Cash Flow Forecasting</h2><p>Create monthly cash flow forecasts to anticipate shortages and plan accordingly. This helps you make informed decisions about expenses and investments.</p>',
				'image' => 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Tax Planning Strategies for Small Business Owners',
				'excerpt' => 'Discover tax-saving strategies that can help reduce your business tax burden legally and effectively.',
				'content' => '<h2>Introduction</h2><p>Effective tax planning can save your small business thousands of dollars each year. By understanding tax deductions, credits, and strategies, you can minimize your tax burden while staying compliant.</p><h2>Key Tax Deductions</h2><h3>Business Expenses</h3><p>Deduct ordinary and necessary business expenses including office supplies, travel, meals, and professional services.</p><h3>Home Office Deduction</h3><p>If you work from home, you may qualify for a home office deduction based on the space used exclusively for business.</p><h3>Equipment and Depreciation</h3><p>Take advantage of Section 179 deductions and bonus depreciation for business equipment purchases.</p><h2>Tax Planning Tips</h2><p>Keep detailed records throughout the year, work with a tax professional, plan major purchases around tax benefits, and consider retirement contributions for tax advantages.</p>',
				'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Small Business Loans: How to Choose the Right Financing Option',
				'excerpt' => 'Navigate the world of small business loans and find the financing solution that best fits your needs.',
				'content' => '<h2>Introduction</h2><p>Finding the right loan for your small business can be challenging. With so many options available, it\'s important to understand the different types of loans and choose one that aligns with your business needs and financial situation.</p><h2>Types of Small Business Loans</h2><h3>Term Loans</h3><p>Traditional loans with fixed repayment terms. Best for established businesses with good credit.</p><h3>SBA Loans</h3><p>Government-backed loans with favorable terms. Ideal for businesses that may not qualify for traditional loans.</p><h3>Business Lines of Credit</h3><p>Flexible financing that allows you to borrow as needed. Great for managing cash flow fluctuations.</p><h3>Equipment Financing</h3><p>Loans specifically for purchasing business equipment. The equipment serves as collateral.</p><h2>Choosing the Right Loan</h2><p>Consider your credit score, business history, loan purpose, repayment ability, and interest rates. Compare multiple lenders before deciding.</p>',
				'image' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Financial Reporting: Understanding Your Business Numbers',
				'excerpt' => 'Learn to read and interpret financial statements to make better business decisions.',
				'content' => '<h2>Introduction</h2><p>Financial reports provide crucial insights into your business\'s health and performance. Understanding these reports helps you make informed decisions and identify areas for improvement.</p><h2>Key Financial Statements</h2><h3>Income Statement</h3><p>Shows revenue, expenses, and profit over a period. Helps you understand profitability.</p><h3>Balance Sheet</h3><p>Provides a snapshot of assets, liabilities, and equity at a specific point in time.</p><h3>Cash Flow Statement</h3><p>Tracks the movement of cash in and out of your business. Essential for understanding liquidity.</p><h2>Key Metrics to Monitor</h2><p>Gross profit margin, net profit margin, current ratio, debt-to-equity ratio, and accounts receivable turnover are all important indicators of financial health.</p><h2>Using Financial Reports</h2><p>Review reports monthly, compare against budgets, identify trends, and use insights to make strategic decisions.</p>',
				'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Investment Strategies for Small Business Growth',
				'excerpt' => 'Learn how to make smart investment decisions that fuel business growth and expansion.',
				'content' => '<h2>Introduction</h2><p>Strategic investments can accelerate your business growth, but knowing where and when to invest is crucial. This guide helps you make informed investment decisions that align with your business goals.</p><h2>Types of Business Investments</h2><h3>Equipment and Technology</h3><p>Investing in modern equipment and technology can improve efficiency, reduce costs, and increase productivity. Consider ROI and long-term benefits.</p><h3>Marketing and Branding</h3><p>Building brand awareness and reaching new customers requires consistent marketing investment. Track results to measure effectiveness.</p><h3>Employee Development</h3><p>Training and developing your team improves skills, increases retention, and enhances service quality. This investment pays dividends in productivity.</p><h3>Research and Development</h3><p>Innovation keeps your business competitive. Allocate resources to develop new products, services, or processes.</p><h2>Investment Planning</h2><p>Prioritize investments based on impact, assess risk and return, plan for cash flow, and monitor results regularly.</p>',
				'image' => 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Managing Business Debt: A Strategic Guide',
				'excerpt' => 'Navigate business debt effectively to maintain financial health while funding growth.',
				'content' => '<h2>Introduction</h2><p>Debt can be a powerful tool for business growth when managed properly. However, excessive or poorly managed debt can threaten your business\'s survival. Learn how to use debt strategically.</p><h2>Good Debt vs. Bad Debt</h2><h3>Good Debt</h3><p>Debt that generates income or increases value: equipment loans, real estate mortgages, or expansion loans that lead to revenue growth.</p><h3>Bad Debt</h3><p>Debt used for non-productive purposes or that doesn\'t generate returns: high-interest credit cards for daily expenses or loans you can\'t afford.</p><h2>Debt Management Strategies</h2><h3>Consolidate High-Interest Debt</h3><p>Combine multiple debts into a single, lower-interest loan to reduce monthly payments and total interest costs.</p><h3>Prioritize High-Interest Payments</h3><p>Pay off high-interest debt first while making minimum payments on lower-interest loans.</p><h3>Negotiate Better Terms</h3><p>Contact lenders to negotiate lower interest rates, extended payment terms, or debt restructuring.</p><h2>Preventing Debt Problems</h2><p>Maintain cash reserves, avoid unnecessary borrowing, monitor debt-to-income ratio, and have a clear repayment plan.</p>',
				'image' => 'https://images.unsplash.com/photo-1497435334941-8c899ee9e8e9?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Understanding Business Credit: Building and Maintaining Your Score',
				'excerpt' => 'Learn how to build strong business credit to access better financing options and terms.',
				'content' => '<h2>Introduction</h2><p>Business credit is separate from personal credit and plays a crucial role in your ability to secure financing, negotiate better terms, and grow your business. Building strong business credit takes time but offers significant benefits.</p><h2>Why Business Credit Matters</h2><p>Strong business credit helps you secure loans, get better interest rates, negotiate payment terms with suppliers, and protect your personal credit.</p><h2>Building Business Credit</h2><h3>Establish Business Entity</h3><p>Form a legal business entity (LLC, corporation) to separate business and personal finances.</p><h3>Get an EIN</h3><p>Obtain an Employer Identification Number (EIN) from the IRS. This is your business\'s tax ID.</p><h3>Open Business Accounts</h3><p>Open business bank accounts and credit cards. Use them regularly and pay on time.</p><h3>Work with Credit-Reporting Vendors</h3><p>Establish relationships with suppliers and vendors who report to business credit bureaus.</p><h2>Maintaining Good Credit</h2><p>Pay bills on time, keep credit utilization low, monitor your credit reports regularly, and address any errors promptly.</p>',
				'image' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=800&h=600&fit=crop',
			),
		),
		'Marketing' => array(
			array(
				'title' => 'Digital Marketing Essentials for Small Businesses',
				'excerpt' => 'Master the fundamentals of digital marketing to reach more customers and grow your business online.',
				'content' => '<h2>Introduction</h2><p>Digital marketing is essential for small businesses in today\'s connected world. With the right strategies, you can reach more customers, build your brand, and grow your business without breaking the bank.</p><h2>Core Digital Marketing Channels</h2><h3>Social Media Marketing</h3><p>Engage with customers on platforms where they spend time. Choose platforms that align with your target audience.</p><h3>Email Marketing</h3><p>One of the most cost-effective marketing channels. Build your email list and send regular, valuable content.</p><h3>Content Marketing</h3><p>Create valuable content that attracts and engages your target audience. Blog posts, videos, and guides can establish your expertise.</p><h3>Search Engine Optimization (SEO)</h3><p>Optimize your website to rank higher in search results. This brings organic traffic without paid advertising.</p><h2>Getting Started</h2><p>Start with one or two channels, create a content calendar, measure your results, and adjust your strategy based on what works.</p>',
				'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Social Media Strategy: Building Your Brand Online',
				'excerpt' => 'Develop a social media strategy that builds brand awareness and drives customer engagement.',
				'content' => '<h2>Introduction</h2><p>A strong social media presence can significantly impact your business. It helps you connect with customers, build brand awareness, and drive sales. This guide will help you create an effective social media strategy.</p><h2>Choosing the Right Platforms</h2><p>Focus on platforms where your target audience is most active. Don\'t try to be everywhere—quality over quantity.</p><h2>Content Strategy</h2><h3>Educational Content</h3><p>Share tips, how-tos, and industry insights that provide value to your audience.</p><h3>Behind-the-Scenes</h3><p>Show the human side of your business. People connect with authentic stories.</p><h3>User-Generated Content</h3><p>Encourage customers to share their experiences with your products or services.</p><h2>Engagement Best Practices</h2><p>Respond to comments and messages promptly, ask questions to encourage interaction, and share content from others in your industry.</p>',
				'image' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Email Marketing: Growing Your Customer Base',
				'excerpt' => 'Learn how to build an email list and create campaigns that convert subscribers into customers.',
				'content' => '<h2>Introduction</h2><p>Email marketing remains one of the most effective marketing channels, offering excellent ROI and direct access to your audience. Learn how to build and leverage an email list for business growth.</p><h2>Building Your Email List</h2><h3>Offer Value</h3><p>Provide something valuable in exchange for email addresses: discounts, free guides, or exclusive content.</p><h3>Optimize Signup Forms</h3><p>Make it easy to subscribe. Place forms prominently on your website and keep them simple.</p><h3>Use Multiple Touchpoints</h3><p>Include signup opportunities on your website, social media, receipts, and in-store.</p><h2>Creating Effective Campaigns</h2><p>Write compelling subject lines, personalize your messages, segment your list, and include clear calls to action.</p><h2>Best Practices</h2><p>Send consistently but not too frequently, provide value in every email, test different approaches, and respect unsubscribe requests promptly.</p>',
				'image' => 'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Content Marketing: Creating Content That Converts',
				'excerpt' => 'Develop a content marketing strategy that attracts customers and drives business results.',
				'content' => '<h2>Introduction</h2><p>Content marketing is about creating and sharing valuable content to attract and engage your target audience. When done right, it builds trust, establishes expertise, and drives sales.</p><h2>Content Types</h2><h3>Blog Posts</h3><p>Regular blog posts help with SEO and provide value to your audience. Focus on topics your customers care about.</p><h3>Videos</h3><p>Video content is highly engaging. Create tutorials, behind-the-scenes content, or product demonstrations.</p><h3>Infographics</h3><p>Visual content that\'s easy to share and understand. Great for explaining complex topics.</p><h3>Case Studies</h3><p>Show how you\'ve helped customers solve problems. This builds credibility and trust.</p><h2>Content Planning</h2><p>Create a content calendar, plan topics in advance, repurpose content across platforms, and measure what resonates with your audience.</p>',
				'image' => 'https://images.unsplash.com/photo-1432888622747-4eb9a8f2d1c6?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Local SEO: Getting Found in Your Community',
				'excerpt' => 'Optimize your online presence to attract local customers and dominate local search results.',
				'content' => '<h2>Introduction</h2><p>Local SEO helps your business appear in search results when people in your area are looking for your products or services. For small businesses, this is crucial for attracting nearby customers.</p><h2>Google Business Profile</h2><p>Claim and optimize your Google Business Profile. Keep information accurate, add photos, respond to reviews, and post regularly.</p><h2>Local Keywords</h2><p>Include location-based keywords in your website content, meta descriptions, and headings. Think "best [service] in [city]".</p><h2>Local Citations</h2><p>Get listed in local directories, industry-specific directories, and local business associations. Consistent NAP (Name, Address, Phone) information is crucial.</p><h2>Customer Reviews</h2><p>Encourage satisfied customers to leave reviews. Respond to all reviews, both positive and negative, professionally.</p>',
				'image' => 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Influencer Marketing: Partnering with Content Creators',
				'excerpt' => 'Leverage influencer partnerships to reach new audiences and build brand credibility.',
				'content' => '<h2>Introduction</h2><p>Influencer marketing has become a powerful tool for small businesses to reach targeted audiences and build trust. By partnering with content creators who align with your brand, you can expand your reach authentically.</p><h2>Finding the Right Influencers</h2><h3>Relevance Over Reach</h3><p>Choose influencers whose audience matches your target market. A smaller, engaged audience is often more valuable than a large, disengaged one.</p><h3>Authenticity Matters</h3><p>Partner with influencers who genuinely use or would use your products. Authentic recommendations resonate more with audiences.</p><h3>Engagement Rates</h3><p>Look at engagement rates, not just follower counts. High engagement indicates an active, interested audience.</p><h2>Building Partnerships</h2><p>Reach out professionally, offer fair compensation, provide creative freedom, and build long-term relationships rather than one-off campaigns.</p><h2>Measuring Success</h2><p>Track metrics like reach, engagement, website traffic, and conversions. Use unique discount codes or tracking links to measure ROI.</p>',
				'image' => 'https://images.unsplash.com/photo-1497215842964-222b430dc094?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Pay-Per-Click Advertising: Maximizing Your Ad Budget',
				'excerpt' => 'Create effective PPC campaigns that drive traffic and conversions without wasting your budget.',
				'content' => '<h2>Introduction</h2><p>Pay-per-click (PPC) advertising allows you to reach potential customers at the exact moment they\'re searching for your products or services. With proper strategy, PPC can deliver immediate, measurable results.</p><h2>PPC Platforms</h2><h3>Google Ads</h3><p>The largest search advertising platform. Target users actively searching for your products or services.</p><h3>Social Media Ads</h3><p>Facebook, Instagram, LinkedIn, and other platforms offer highly targeted advertising based on demographics, interests, and behaviors.</p><h3>Display Advertising</h3><p>Reach users across websites with visual banner ads. Great for brand awareness and retargeting.</p><h2>Creating Effective Campaigns</h2><h3>Keyword Research</h3><p>Identify high-intent keywords your customers use. Focus on long-tail keywords for better targeting and lower costs.</p><h3>Compelling Ad Copy</h3><p>Write clear, benefit-focused headlines and descriptions. Include a strong call-to-action.</p><h3>Landing Page Optimization</h3><p>Ensure your landing pages match your ad messaging and are optimized for conversions.</p><h2>Budget Management</h2><p>Start with a small budget, test different ad variations, monitor performance daily, and adjust bids based on results.</p>',
				'image' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Marketing Analytics: Measuring What Matters',
				'excerpt' => 'Use data and analytics to understand your marketing performance and make informed decisions.',
				'content' => '<h2>Introduction</h2><p>Marketing analytics helps you understand which marketing efforts are working and which aren\'t. By tracking the right metrics, you can optimize your marketing spend and improve ROI.</p><h2>Key Marketing Metrics</h2><h3>Website Traffic</h3><p>Monitor total visitors, unique visitors, page views, and traffic sources. Understand where your audience comes from.</p><h3>Conversion Rates</h3><p>Track how many visitors take desired actions: purchases, sign-ups, downloads, or inquiries. This shows marketing effectiveness.</p><h3>Customer Acquisition Cost (CAC)</h3><p>Calculate how much it costs to acquire each new customer. Lower CAC means more efficient marketing.</p><h3>Customer Lifetime Value (CLV)</h3><p>Determine the total value a customer brings over their relationship with your business. Compare to CAC for profitability.</p><h2>Tools and Platforms</h2><p>Use Google Analytics for website data, social media insights for platform performance, email marketing analytics for campaigns, and CRM systems for customer data.</p><h2>Making Data-Driven Decisions</h2><p>Set clear goals, track relevant metrics, review data regularly, test different approaches, and adjust strategies based on results.</p>',
				'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop',
			),
		),
		'Technology' => array(
			array(
				'title' => 'Cloud Computing for Small Businesses: Getting Started',
				'excerpt' => 'Discover how cloud computing can improve efficiency, reduce costs, and scale your business operations.',
				'content' => '<h2>Introduction</h2><p>Cloud computing has revolutionized how small businesses operate. By moving to the cloud, you can access powerful tools and services without large upfront investments in hardware or IT infrastructure.</p><h2>Benefits of Cloud Computing</h2><h3>Cost Savings</h3><p>Eliminate the need for expensive servers and IT infrastructure. Pay only for what you use with subscription-based services.</p><h3>Scalability</h3><p>Easily scale your resources up or down based on business needs. No need to invest in hardware you might outgrow.</p><h3>Accessibility</h3><p>Access your data and applications from anywhere, on any device. Perfect for remote work and mobile teams.</p><h3>Security</h3><p>Cloud providers invest heavily in security, often providing better protection than small businesses can afford independently.</p><h2>Getting Started</h2><p>Start with one service (like cloud storage), choose reputable providers, ensure data backup, and train your team on new tools.</p>',
				'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Cybersecurity Basics Every Small Business Should Know',
				'excerpt' => 'Protect your business from cyber threats with these essential security practices.',
				'content' => '<h2>Introduction</h2><p>Small businesses are increasingly targeted by cybercriminals. Implementing basic cybersecurity measures is essential to protect your business data, customer information, and reputation.</p><h2>Essential Security Measures</h2><h3>Strong Passwords</h3><p>Use complex passwords and consider a password manager. Enable two-factor authentication wherever possible.</p><h3>Regular Updates</h3><p>Keep all software, operating systems, and applications updated. Updates often include security patches.</p><h3>Firewall and Antivirus</h3><p>Install and maintain firewall and antivirus software. This is your first line of defense against threats.</p><h3>Employee Training</h3><p>Educate your team about phishing, social engineering, and safe online practices. Human error is a major security risk.</p><h2>Data Protection</h2><p>Back up data regularly, encrypt sensitive information, limit access to data, and have an incident response plan.</p>',
				'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Automation Tools: Streamlining Your Business Operations',
				'excerpt' => 'Discover automation tools that can save time and reduce errors in your daily business operations.',
				'content' => '<h2>Introduction</h2><p>Business automation can significantly improve efficiency by handling repetitive tasks, reducing errors, and freeing up time for strategic work. Many automation tools are affordable and easy to implement for small businesses.</p><h2>Areas to Automate</h2><h3>Customer Service</h3><p>Chatbots and automated email responses can handle common inquiries, providing 24/7 support.</p><h3>Accounting</h3><p>Automate invoicing, expense tracking, and financial reporting. This reduces errors and saves time.</p><h3>Marketing</h3><p>Automate email campaigns, social media posting, and lead nurturing. Maintain consistent communication with minimal effort.</p><h3>Inventory Management</h3><p>Automated inventory tracking can alert you when stock is low and help prevent stockouts or overstocking.</p><h2>Getting Started</h2><p>Identify repetitive tasks, research automation tools, start with one area, and gradually expand automation as you see results.</p>',
				'image' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Website Optimization: Speed and Performance Tips',
				'excerpt' => 'Improve your website\'s speed and performance to provide better user experience and boost conversions.',
				'content' => '<h2>Introduction</h2><p>A fast, responsive website is crucial for user experience and search engine rankings. Slow websites frustrate visitors and can hurt your business. Learn how to optimize your website for speed and performance.</p><h2>Speed Optimization Techniques</h2><h3>Image Optimization</h3><p>Compress images, use appropriate formats (WebP when possible), and implement lazy loading.</p><h3>Caching</h3><p>Implement browser caching and server-side caching to reduce load times for returning visitors.</p><h3>Minimize Code</h3><p>Minify CSS, JavaScript, and HTML. Remove unused code and plugins.</p><h3>Content Delivery Network (CDN)</h3><p>Use a CDN to serve content from servers closer to your visitors, reducing latency.</p><h2>Performance Monitoring</h2><p>Use tools like Google PageSpeed Insights to identify issues, monitor site speed regularly, and test on various devices and connections.</p>',
				'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Customer Relationship Management (CRM) Systems for Small Business',
				'excerpt' => 'Choose and implement a CRM system to better manage customer relationships and grow sales.',
				'content' => '<h2>Introduction</h2><p>A CRM system helps you manage customer interactions, track sales, and improve customer relationships. For small businesses, the right CRM can be a game-changer for growth.</p><h2>Benefits of CRM</h2><h3>Centralized Customer Data</h3><p>All customer information in one place makes it easier to provide personalized service.</p><h3>Sales Pipeline Management</h3><p>Track leads through your sales process, identify bottlenecks, and close more deals.</p><h3>Improved Communication</h3><p>Keep track of all customer interactions, ensuring nothing falls through the cracks.</p><h3>Analytics and Reporting</h3><p>Gain insights into sales performance, customer behavior, and marketing effectiveness.</p><h2>Choosing a CRM</h2><p>Consider your budget, team size, required features, ease of use, and integration capabilities. Many CRMs offer free or low-cost plans for small businesses.</p>',
				'image' => 'https://images.unsplash.com/photo-1555255707-c07966088b7b?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Mobile App Development for Small Businesses',
				'excerpt' => 'Explore whether a mobile app can benefit your business and how to get started.',
				'content' => '<h2>Introduction</h2><p>Mobile apps can provide a direct channel to customers, improve engagement, and offer unique features that websites can\'t. However, they require investment and ongoing maintenance. Learn when and how to develop a mobile app for your business.</p><h2>Benefits of Mobile Apps</h2><h3>Direct Customer Access</h3><p>Apps provide instant access to your business from customers\' phones, increasing engagement and convenience.</p><h3>Push Notifications</h3><p>Send timely updates, promotions, and reminders directly to customers, improving communication and retention.</p><h3>Enhanced Features</h3><p>Apps can offer features like offline access, mobile payments, location services, and device-specific capabilities.</p><h2>When to Build an App</h2><p>Consider an app if you have frequent repeat customers, offer services that benefit from mobile features, want to improve customer loyalty, or need to differentiate from competitors.</p><h2>Getting Started</h2><p>Define your app\'s purpose, research development options (native vs. hybrid), plan your budget, and start with essential features.</p>',
				'image' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Data Backup and Recovery: Protecting Your Business Information',
				'excerpt' => 'Implement robust backup strategies to protect your business from data loss disasters.',
				'content' => '<h2>Introduction</h2><p>Data loss can cripple a small business. Whether from hardware failure, cyberattacks, or human error, losing critical business data can be devastating. A comprehensive backup and recovery plan is essential.</p><h2>Types of Backups</h2><h3>Full Backups</h3><p>Complete copies of all data. Most comprehensive but requires more storage and time.</p><h3>Incremental Backups</h3><p>Only backs up changes since the last backup. Faster and uses less storage.</p><h3>Cloud Backups</h3><p>Automatic, off-site backups that protect against local disasters. Many services offer automated daily backups.</p><h2>Backup Best Practices</h2><h3>3-2-1 Rule</h3><p>Keep 3 copies of data, on 2 different media types, with 1 copy off-site.</p><h3>Automated Backups</h3><p>Set up automatic backups to eliminate human error and ensure consistency.</p><h3>Regular Testing</h3><p>Test your backup restoration process regularly to ensure backups work when needed.</p><h2>Recovery Planning</h2><p>Document your recovery procedures, identify critical systems, establish recovery time objectives, and train your team on procedures.</p>',
				'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'E-commerce Platforms: Choosing the Right Solution',
				'excerpt' => 'Select the best e-commerce platform for your business needs and budget.',
				'content' => '<h2>Introduction</h2><p>Choosing the right e-commerce platform is crucial for online business success. The platform you select affects your website\'s functionality, scalability, costs, and ease of management.</p><h2>Popular E-commerce Platforms</h2><h3>Shopify</h3><p>User-friendly, all-in-one solution with extensive app marketplace. Great for beginners and growing businesses.</p><h3>WooCommerce</h3><p>WordPress plugin that offers flexibility and customization. Ideal if you already use WordPress.</p><h3>BigCommerce</h3><p>Scalable platform with built-in features. Good for businesses planning significant growth.</p><h3>Squarespace</h3><p>Design-focused platform with beautiful templates. Best for businesses prioritizing aesthetics.</p><h2>Key Considerations</h2><p>Evaluate costs (monthly fees, transaction fees, add-ons), ease of use, customization options, payment processing, inventory management, and scalability.</p><h2>Making Your Choice</h2><p>Start with a free trial, consider your technical skills, think about future growth, and ensure the platform supports your business model.</p>',
				'image' => 'https://images.unsplash.com/photo-1556740758-90de374c12ad?w=800&h=600&fit=crop',
			),
		),
		'Growth' => array(
			array(
				'title' => 'Scaling Your Small Business: Growth Strategies That Work',
				'excerpt' => 'Learn proven strategies to scale your business sustainably and avoid common growth pitfalls.',
				'content' => '<h2>Introduction</h2><p>Scaling a business requires careful planning and execution. While growth is exciting, it can also bring challenges. Learn strategies to scale your business successfully.</p><h2>Scaling Strategies</h2><h3>Systematize Operations</h3><p>Document processes, create standard operating procedures, and build systems that can run without constant oversight.</p><h3>Build a Strong Team</h3><p>Hire the right people, invest in training, and create a culture that attracts and retains top talent.</p><h3>Leverage Technology</h3><p>Use technology to automate tasks, improve efficiency, and handle increased volume without proportional cost increases.</p><h3>Focus on Customer Retention</h3><p>It\'s more cost-effective to retain existing customers than acquire new ones. Build loyalty programs and maintain excellent service.</p><h2>Avoiding Common Pitfalls</h2><p>Don\'t scale too fast, maintain quality standards, manage cash flow carefully, and stay true to your core values.</p>',
				'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Hiring Your First Employees: A Small Business Guide',
				'excerpt' => 'Navigate the hiring process and build a team that helps your business grow.',
				'content' => '<h2>Introduction</h2><p>Hiring your first employees is a major milestone. The right team can accelerate growth, while poor hiring decisions can set you back. Learn how to hire effectively.</p><h2>The Hiring Process</h2><h3>Define the Role</h3><p>Clearly outline job responsibilities, required skills, and expectations. This helps attract the right candidates.</p><h3>Write Compelling Job Descriptions</h3><p>Highlight your company culture, growth opportunities, and what makes the role attractive. Be specific about requirements.</p><h3>Screen Candidates</h3><p>Review resumes carefully, conduct phone screenings, and use structured interviews to assess fit.</p><h3>Check References</h3><p>Always verify employment history and speak with references to confirm candidate qualifications and work ethic.</p><h2>Onboarding</h2><p>Create a comprehensive onboarding process, provide necessary training, set clear expectations, and assign a mentor or buddy.</p>',
				'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Expanding to New Markets: A Strategic Approach',
				'excerpt' => 'Plan and execute market expansion to grow your business into new territories or customer segments.',
				'content' => '<h2>Introduction</h2><p>Expanding to new markets can significantly grow your business, but it requires careful planning and execution. Whether entering new geographic areas or targeting new customer segments, a strategic approach is essential.</p><h2>Market Research</h2><p>Understand the new market, identify customer needs, analyze competition, and assess market size and potential.</p><h2>Expansion Strategies</h2><h3>Geographic Expansion</h3><p>Enter new cities, states, or countries. Consider local regulations, cultural differences, and logistics.</p><h3>Product Line Extension</h3><p>Introduce new products or services to existing markets. Leverage your brand and customer relationships.</p><h3>New Customer Segments</h3><p>Target different demographics or industries. Adapt your marketing and messaging accordingly.</p><h2>Planning for Expansion</h2><p>Ensure you have the resources, maintain quality standards, adapt to local needs, and monitor performance closely.</p>',
				'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Building Strategic Partnerships for Business Growth',
				'excerpt' => 'Leverage partnerships to expand your reach, share resources, and accelerate business growth.',
				'content' => '<h2>Introduction</h2><p>Strategic partnerships can be a powerful growth tool for small businesses. By collaborating with complementary businesses, you can access new markets, share resources, and achieve mutual benefits.</p><h2>Types of Partnerships</h2><h3>Marketing Partnerships</h3><p>Co-market products or services, cross-promote to each other\'s audiences, and share marketing costs.</p><h3>Distribution Partnerships</h3><p>Partner with businesses that can sell or distribute your products, expanding your reach without direct sales efforts.</p><h3>Technology Partnerships</h3><p>Integrate with complementary technology platforms, creating more value for customers.</p><h2>Finding Partners</h2><p>Look for businesses with complementary products or services, similar target audiences, and shared values. Attend industry events and network actively.</p><h2>Making Partnerships Work</h2><p>Define clear expectations, communicate regularly, ensure mutual benefit, and review partnership performance periodically.</p>',
				'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Customer Retention Strategies: Keeping Customers Coming Back',
				'excerpt' => 'Implement strategies to retain customers and build long-term relationships that drive repeat business.',
				'content' => '<h2>Introduction</h2><p>Retaining existing customers is more cost-effective than acquiring new ones. Loyal customers also tend to spend more and refer others. Learn strategies to keep customers coming back.</p><h2>Retention Strategies</h2><h3>Excellent Customer Service</h3><p>Provide prompt, helpful, and friendly service. Go above and beyond to solve problems and exceed expectations.</p><h3>Loyalty Programs</h3><p>Reward repeat customers with discounts, points, or exclusive offers. This encourages return visits and purchases.</p><h3>Regular Communication</h3><p>Stay in touch through email newsletters, social media, and personalized messages. Share valuable content and updates.</p><h3>Ask for Feedback</h3><p>Regularly solicit customer feedback and act on it. This shows you value their opinions and helps improve your business.</p><h2>Measuring Retention</h2><p>Track customer retention rate, repeat purchase rate, customer lifetime value, and churn rate to measure your success.</p>',
				'image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Franchising Your Business: A Growth Strategy',
				'excerpt' => 'Explore franchising as a way to expand your business with reduced capital investment.',
				'content' => '<h2>Introduction</h2><p>Franchising allows you to expand your business by licensing your brand and business model to franchisees. This can accelerate growth while reducing your capital investment and operational risk.</p><h2>Is Franchising Right for You?</h2><p>Franchising works best for businesses with proven success, replicable systems, strong brand identity, and clear operational procedures. Your business model must be teachable and profitable for franchisees.</p><h2>Franchise Development</h2><h3>Legal Requirements</h3><p>Create a Franchise Disclosure Document (FDD), comply with federal and state regulations, and work with franchise attorneys.</p><h3>Operations Manual</h3><p>Document every aspect of your business operations so franchisees can replicate your success.</p><h3>Training Programs</h3><p>Develop comprehensive training for franchisees covering operations, marketing, and management.</p><h2>Supporting Franchisees</h2><p>Provide ongoing support, marketing assistance, operational guidance, and regular communication to ensure franchise success.</p>',
				'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Exporting and International Expansion',
				'excerpt' => 'Take your business global by exploring international markets and export opportunities.',
				'content' => '<h2>Introduction</h2><p>Expanding internationally can open new revenue streams and reduce dependence on domestic markets. However, it requires careful planning, research, and understanding of different markets and regulations.</p><h2>Market Research</h2><p>Identify target markets, understand local demand, analyze competition, research cultural differences, and assess regulatory requirements before entering new countries.</p><h2>Export Strategies</h2><h3>Direct Exporting</h3><p>Sell directly to international customers. Requires handling logistics, customs, and international payments.</p><h3>Export Intermediaries</h3><p>Work with export management companies or trading companies that handle logistics and sales.</p><h3>Licensing and Partnerships</h3><p>License your products or partner with local businesses to enter markets with reduced risk.</p><h2>Key Considerations</h2><p>Understand international regulations, handle currency exchange, manage logistics and shipping, adapt products for local markets, and comply with tax requirements.</p>',
				'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Product Development: From Idea to Market',
				'excerpt' => 'Navigate the product development process to bring new offerings to market successfully.',
				'content' => '<h2>Introduction</h2><p>Developing new products or services is essential for business growth and staying competitive. A structured approach helps you bring successful products to market while minimizing risk.</p><h2>Product Development Process</h2><h3>Ideation</h3><p>Generate ideas based on customer needs, market gaps, or improvements to existing products. Validate ideas through research.</p><h3>Market Research</h3><p>Assess market demand, analyze competition, identify target customers, and estimate market size and potential.</p><h3>Prototype Development</h3><p>Create a minimum viable product (MVP) to test your concept with real customers before full development.</p><h3>Testing and Refinement</h3><p>Gather customer feedback, make improvements, test pricing, and refine features based on user experience.</p><h2>Launch Strategy</h2><p>Plan your launch carefully: set pricing, develop marketing materials, train your team, prepare inventory, and create a timeline for rollout.</p>',
				'image' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=800&h=600&fit=crop',
			),
		),
		'Strategy' => array(
			array(
				'title' => 'Business Planning: Creating a Roadmap for Success',
				'excerpt' => 'Develop a comprehensive business plan that guides your decisions and helps secure funding.',
				'content' => '<h2>Introduction</h2><p>A well-crafted business plan is essential for any small business. It serves as a roadmap, helps secure funding, and guides strategic decisions. Learn how to create an effective business plan.</p><h2>Key Components</h2><h3>Executive Summary</h3><p>Provide an overview of your business, mission, and key highlights. This is often the first section investors read.</p><h3>Market Analysis</h3><p>Research your industry, target market, and competition. Demonstrate your understanding of the market landscape.</p><h3>Organization and Management</h3><p>Describe your business structure, management team, and organizational chart.</p><h3>Products or Services</h3><p>Detail what you offer, how it benefits customers, and what makes it unique.</p><h3>Marketing Strategy</h3><p>Explain how you\'ll attract and retain customers. Include pricing, promotion, and distribution strategies.</p><h3>Financial Projections</h3><p>Provide realistic financial forecasts including income statements, cash flow, and balance sheets.</p>',
				'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Competitive Analysis: Understanding Your Market Position',
				'excerpt' => 'Conduct thorough competitive analysis to identify opportunities and differentiate your business.',
				'content' => '<h2>Introduction</h2><p>Understanding your competition is crucial for strategic planning. Competitive analysis helps you identify opportunities, avoid threats, and position your business effectively in the market.</p><h2>Identifying Competitors</h2><p>List direct competitors (same products/services), indirect competitors (different solutions to same problem), and potential new entrants to the market.</p><h2>Analyzing Competitors</h2><h3>Products and Services</h3><p>What do they offer? What are their strengths and weaknesses? How do they price their offerings?</p><h3>Marketing Strategies</h3><p>How do they market? What channels do they use? What messaging resonates with their audience?</p><h3>Market Position</h3><p>What is their market share? How are they perceived? What is their brand reputation?</p><h2>Using Analysis Results</h2><p>Identify gaps in the market, differentiate your offerings, learn from their successes and failures, and anticipate competitive moves.</p>',
				'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'SWOT Analysis: Evaluating Your Business Strengths and Weaknesses',
				'excerpt' => 'Conduct a SWOT analysis to identify internal strengths and weaknesses, and external opportunities and threats.',
				'content' => '<h2>Introduction</h2><p>SWOT analysis is a strategic planning tool that helps you evaluate your business by examining Strengths, Weaknesses, Opportunities, and Threats. This analysis provides valuable insights for decision-making.</p><h2>Conducting a SWOT Analysis</h2><h3>Strengths</h3><p>Internal factors that give you an advantage: unique products, strong brand, skilled team, financial resources, or market position.</p><h3>Weaknesses</h3><p>Internal factors that put you at a disadvantage: limited resources, lack of experience, weak brand, or operational inefficiencies.</p><h3>Opportunities</h3><p>External factors you can capitalize on: market trends, new technologies, regulatory changes, or competitor weaknesses.</p><h3>Threats</h3><p>External factors that could harm your business: competition, economic downturns, changing customer preferences, or regulatory changes.</p><h2>Using SWOT Results</h2><p>Leverage strengths, address weaknesses, capitalize on opportunities, and mitigate threats. Use insights to inform strategic decisions.</p>',
				'image' => 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Legal Considerations for Small Business Owners',
				'excerpt' => 'Understand key legal requirements and protect your business with proper legal structures and documentation.',
				'content' => '<h2>Introduction</h2><p>Understanding legal requirements is crucial for protecting your business. From business structure to contracts and compliance, proper legal planning prevents costly problems down the road.</p><h2>Business Structure</h2><p>Choose the right legal structure: sole proprietorship, partnership, LLC, or corporation. Each has different implications for liability, taxes, and operations.</p><h2>Essential Legal Documents</h2><h3>Contracts</h3><p>Use written contracts for all business agreements. This protects both parties and clarifies expectations.</p><h3>Terms of Service and Privacy Policy</h3><p>If you have a website, these documents are essential for legal protection and compliance.</p><h3>Employment Agreements</h3><p>Clearly define roles, responsibilities, and terms of employment to avoid disputes.</p><h2>Compliance Requirements</h2><p>Understand licensing requirements, tax obligations, employment laws, and industry-specific regulations. Consult with legal professionals when needed.</p>',
				'image' => 'https://images.unsplash.com/photo-1450101499163-8841044c5b8c?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Risk Management: Protecting Your Business from Threats',
				'excerpt' => 'Identify and mitigate risks to protect your business and ensure long-term sustainability.',
				'content' => '<h2>Introduction</h2><p>Every business faces risks. Effective risk management helps you identify potential threats, assess their impact, and take steps to mitigate or eliminate them. This protects your business and ensures continuity.</p><h2>Types of Business Risks</h2><h3>Financial Risks</h3><p>Cash flow problems, economic downturns, or unexpected expenses. Maintain emergency funds and monitor finances closely.</p><h3>Operational Risks</h3><p>Equipment failures, supply chain disruptions, or key employee departures. Have backup plans and cross-train staff.</p><h3>Legal Risks</h3><p>Lawsuits, regulatory violations, or contract disputes. Maintain proper documentation and insurance.</p><h3>Reputational Risks</h3><p>Negative reviews, public relations issues, or data breaches. Monitor your online presence and have crisis communication plans.</p><h2>Risk Management Strategies</h2><p>Identify risks proactively, assess likelihood and impact, develop mitigation strategies, purchase appropriate insurance, and create contingency plans.</p>',
				'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Succession Planning: Preparing for Business Transitions',
				'excerpt' => 'Plan for the future of your business with effective succession planning strategies.',
				'content' => '<h2>Introduction</h2><p>Succession planning ensures your business continues to thrive when you retire, sell, or step away. Whether passing to family, selling to employees, or finding a buyer, proper planning is essential.</p><h2>Types of Succession</h2><h3>Family Succession</h3><p>Transferring ownership to family members. Requires clear communication, training, and legal documentation.</p><h3>Employee Buyout</h3><p>Selling to key employees who understand the business. Can be structured over time to ease financial burden.</p><h3>External Sale</h3><p>Selling to an outside buyer. Maximizes value but requires finding the right buyer and negotiating terms.</p><h2>Succession Planning Steps</h2><p>Start planning early (5-10 years ahead), identify and train successors, document all processes, get business valuations, and work with legal and financial advisors.</p><h2>Key Considerations</h2><p>Ensure business can operate without you, maintain customer relationships, preserve company culture, and plan for tax implications.</p>',
				'image' => 'https://images.unsplash.com/photo-1556155092-8707de31f9c4?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Innovation and Adaptability: Staying Competitive',
				'excerpt' => 'Foster innovation and adaptability to keep your business competitive in changing markets.',
				'content' => '<h2>Introduction</h2><p>In today\'s rapidly changing business environment, innovation and adaptability are essential for survival and growth. Businesses that fail to evolve risk becoming obsolete. Learn how to foster innovation and adapt to change.</p><h2>Fostering Innovation</h2><h3>Encourage Ideas</h3><p>Create a culture where employees feel comfortable sharing ideas. Reward innovation and experimentation.</p><h3>Customer Feedback</h3><p>Listen to customers to identify needs and opportunities for improvement. They often have the best insights.</p><h3>Monitor Trends</h3><p>Stay informed about industry trends, technology changes, and market shifts that could impact your business.</p><h2>Building Adaptability</h2><p>Develop flexible business models, cross-train employees, maintain financial reserves, build strong relationships, and stay open to change.</p><h2>Implementing Change</h2><p>Communicate changes clearly, involve your team, provide training and support, start small and scale, and measure results.</p>',
				'image' => 'https://images.unsplash.com/photo-1556761175-8b4e62c6b464?w=800&h=600&fit=crop',
			),
			array(
				'title' => 'Building a Strong Company Culture',
				'excerpt' => 'Create a positive company culture that attracts talent, improves retention, and drives performance.',
				'content' => '<h2>Introduction</h2><p>Company culture is the shared values, beliefs, and behaviors that define your organization. A strong culture attracts top talent, improves employee satisfaction, and drives business performance. It\'s one of your most valuable assets.</p><h2>Elements of Strong Culture</h2><h3>Clear Values</h3><p>Define and communicate your core values. These should guide decision-making and behavior throughout the organization.</p><h3>Open Communication</h3><p>Foster an environment where employees feel comfortable sharing ideas, concerns, and feedback. Regular communication builds trust.</p><h3>Recognition and Rewards</h3><p>Acknowledge and reward employees for their contributions. Recognition doesn\'t always require money—appreciation goes a long way.</p><h3>Work-Life Balance</h3><p>Support employees\' well-being by promoting healthy work-life balance. Happy employees are more productive and loyal.</p><h2>Building Your Culture</h2><p>Lead by example, hire for cultural fit, invest in employee development, create team-building opportunities, and regularly assess and refine your culture.</p>',
				'image' => 'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=800&h=600&fit=crop',
			),
		),
	);
	
	foreach ( $category_posts as $category_name => $posts ) {
		$category = get_term_by( 'name', $category_name, 'main_category' );
		if ( ! $category ) {
			$categories = get_terms( array( 'taxonomy' => 'main_category', 'hide_empty' => false ) );
			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				$category = $categories[0];
			} else {
				continue;
			}
		}
		
		$days_ago = 0;
		foreach ( $posts as $post_data ) {
			global $wpdb;
			$existing = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'post' AND post_status != 'trash' LIMIT 1",
				$post_data['title']
			) );
			
			if ( $existing ) {
				$days_ago++;
				continue;
			}
			
			$post_date = date( 'Y-m-d H:i:s', strtotime( '-' . $days_ago . ' days' ) );
			
			$post_id = wp_insert_post( array(
				'post_title'    => $post_data['title'],
				'post_content'  => $post_data['content'],
				'post_excerpt'  => $post_data['excerpt'],
				'post_status'   => 'publish',
				'post_type'     => 'post',
				'post_author'   => $author_id,
				'post_date'     => $post_date,
			) );
			
			if ( is_wp_error( $post_id ) ) {
				$days_ago++;
				continue;
			}
			
			wp_set_object_terms( $post_id, array( $category->term_id ), 'main_category', true );
			
			if ( ! empty( $post_data['image'] ) ) {
				sme_set_featured_image_from_url( $post_id, $post_data['image'], $post_data['title'] );
			}
			
			if ( ! empty( $post_data['excerpt'] ) ) {
				update_post_meta( $post_id, '_sme_custom_excerpt', $post_data['excerpt'] );
			}
			
			$days_ago++;
		}
	}
	
	update_option( $option_key, true );
}

/**
 * Update all posts with unique images
 * This function ensures all posts have unique images and replaces any duplicates
 */
add_action( 'admin_init', 'sme_update_all_posts_images' );
function sme_update_all_posts_images() {
	if ( ! isset( $_GET['update_all_images'] ) || sanitize_text_field( wp_unslash( $_GET['update_all_images'] ) ) !== '1' ) {
		return;
	}
	
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	$all_posts = get_posts( array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );
	
	if ( empty( $all_posts ) ) {
		return;
	}
	
	$used_images = array();
	
	$image_pool = array(
		'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1432888622747-4eb9a8f2d1c6?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1555255707-c07966088b7b?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1450101499163-8841044c5b8c?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1553729459-efe14ef6055d?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556155092-8707de31f9c4?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-b3cde8e0d4e8?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-8b4e62c6b464?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-8b4e62c6b464?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-b3cde8e0d4e8?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-8b4e62c6b464?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-b3cde8e0d4e8?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-8b4e62c6b464?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-b3cde8e0d4e8?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-8b4e62c6b464?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-b3cde8e0d4e8?w=800&h=600&fit=crop',
	);
	
	shuffle( $image_pool );
	
	$updated_count = 0;
	$image_index = 0;
	
	$all_image_urls = array();
	foreach ( $all_posts as $post_id ) {
		if ( has_post_thumbnail( $post_id ) ) {
			$thumbnail_id = get_post_thumbnail_id( $post_id );
			$image_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );
			if ( $image_url ) {
				$all_image_urls[ $post_id ] = $image_url;
			}
		}
	}
	
	$image_counts = array_count_values( $all_image_urls );
	$duplicate_images = array();
	foreach ( $image_counts as $image_url => $count ) {
		if ( $count > 1 ) {
			$duplicate_images[] = $image_url;
		}
	}
	
	foreach ( $all_posts as $post_id ) {
		$post_title = get_the_title( $post_id );
		$needs_update = false;
		$current_image_url = '';
		
		if ( has_post_thumbnail( $post_id ) ) {
			$thumbnail_id = get_post_thumbnail_id( $post_id );
			$current_image_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );
			
			if ( in_array( $current_image_url, $duplicate_images, true ) ) {
				$needs_update = true;
			}
		} else {
			$needs_update = true;
		}
		
		if ( $needs_update ) {
			$new_image_url = $image_pool[ $image_index % count( $image_pool ) ];
			
			while ( in_array( $new_image_url, $used_images, true ) && count( $used_images ) < count( $image_pool ) ) {
				$image_index++;
				$new_image_url = $image_pool[ $image_index % count( $image_pool ) ];
			}
			
			if ( has_post_thumbnail( $post_id ) ) {
				$old_thumbnail_id = get_post_thumbnail_id( $post_id );
				delete_post_thumbnail( $post_id );
				wp_delete_attachment( $old_thumbnail_id, true );
			}
			
			if ( sme_set_featured_image_from_url( $post_id, $new_image_url, $post_title, true ) ) {
				$used_images[] = $new_image_url;
				$updated_count++;
			}
			
			$image_index++;
		} else {
			if ( ! empty( $current_image_url ) ) {
				$used_images[] = $current_image_url;
			}
		}
	}
	
	wp_cache_flush();
	
	add_action( 'admin_notices', function() use ( $updated_count ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . sprintf( 'Updated %d posts with unique images.', $updated_count ) . '</p></div>';
	} );
}

function sme_get_relevant_image_for_post( $post_id, $image_pool ) {
	$post_title = get_the_title( $post_id );
	$post_content = get_post_field( 'post_content', $post_id );
	$post_excerpt = get_post_field( 'post_excerpt', $post_id );
	
	$categories = get_the_terms( $post_id, 'main_category' );
	$category_name = '';
	if ( $categories && ! is_wp_error( $categories ) && ! empty( $categories ) ) {
		$category_name = strtolower( $categories[0]->name );
	}
	
	$text_to_analyze = strtolower( $post_title . ' ' . $post_excerpt . ' ' . $post_content . ' ' . $category_name );
	
	$keyword_mappings = array(
		'finance' => array( 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&h=600&fit=crop' ),
		'budget' => array( 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop' ),
		'cash' => array( 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&h=600&fit=crop' ),
		'loan' => array( 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&h=600&fit=crop' ),
		'tax' => array( 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop' ),
		'credit' => array( 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop' ),
		'debt' => array( 'https://images.unsplash.com/photo-1497435334941-8c899ee9e8e9?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&h=600&fit=crop' ),
		'investment' => array( 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop' ),
		
		'marketing' => array( 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?w=800&h=600&fit=crop' ),
		'social media' => array( 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop' ),
		'email' => array( 'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1432888622747-4eb9a8f2d1c6?w=800&h=600&fit=crop' ),
		'seo' => array( 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1432888622747-4eb9a8f2d1c6?w=800&h=600&fit=crop' ),
		'content' => array( 'https://images.unsplash.com/photo-1432888622747-4eb9a8f2d1c6?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop' ),
		'influencer' => array( 'https://images.unsplash.com/photo-1497215842964-222b430dc094?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=600&fit=crop' ),
		'advertising' => array( 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop' ),
		'analytics' => array( 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop' ),
		
		'technology' => array( 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop' ),
		'cloud' => array( 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop' ),
		'cybersecurity' => array( 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&h=600&fit=crop' ),
		'automation' => array( 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop' ),
		'website' => array( 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop' ),
		'crm' => array( 'https://images.unsplash.com/photo-1555255707-c07966088b7b?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop' ),
		'app' => array( 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop' ),
		'backup' => array( 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop' ),
		'e-commerce' => array( 'https://images.unsplash.com/photo-1556740758-90de374c12ad?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop' ),
		
		'growth' => array( 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop' ),
		'scaling' => array( 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop' ),
		'hiring' => array( 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop' ),
		'employee' => array( 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop' ),
		'expansion' => array( 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&h=600&fit=crop' ),
		'partnership' => array( 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop' ),
		'retention' => array( 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop' ),
		'franchise' => array( 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop' ),
		'export' => array( 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop' ),
		'product' => array( 'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop' ),
		
		'strategy' => array( 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop' ),
		'planning' => array( 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop' ),
		'competitive' => array( 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop' ),
		'swot' => array( 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop' ),
		'legal' => array( 'https://images.unsplash.com/photo-1450101499163-8841044c5b8c?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&h=600&fit=crop' ),
		'risk' => array( 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1450101499163-8841044c5b8c?w=800&h=600&fit=crop' ),
		'succession' => array( 'https://images.unsplash.com/photo-1556155092-8707de31f9c4?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop' ),
		'innovation' => array( 'https://images.unsplash.com/photo-1556761175-8b4e62c6b464?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=800&h=600&fit=crop' ),
		'culture' => array( 'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop' ),
	);
	
	foreach ( $keyword_mappings as $keyword => $images ) {
		if ( strpos( $text_to_analyze, $keyword ) !== false ) {
			return $images[ array_rand( $images ) ];
		}
	}
	if ( $category_name ) {
		$category_images = array();
		switch ( $category_name ) {
			case 'finance':
				$category_images = array( 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop' );
				break;
			case 'marketing':
				$category_images = array( 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?w=800&h=600&fit=crop' );
				break;
			case 'technology':
				$category_images = array( 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop' );
				break;
			case 'growth':
				$category_images = array( 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop' );
				break;
			case 'strategy':
				$category_images = array( 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop' );
				break;
		}
		if ( ! empty( $category_images ) ) {
			return $category_images[ array_rand( $category_images ) ];
		}
	}
	
	return $image_pool[ array_rand( $image_pool ) ];
}

add_action( 'admin_init', 'sme_ensure_all_posts_have_images' );
function sme_ensure_all_posts_have_images() {
	if ( ! isset( $_GET['ensure_all_images'] ) || sanitize_text_field( wp_unslash( $_GET['ensure_all_images'] ) ) !== '1' ) {
		return;
	}
	
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	$all_posts = get_posts( array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );
	
	if ( empty( $all_posts ) ) {
		return;
	}
	
	$image_pool = array(
		'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1432888622747-4eb9a8f2d1c6?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1555255707-c07966088b7b?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1450101499163-8841044c5b8c?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1553729459-efe14ef6055d?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556155092-8707de31f9c4?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-8b4e62c6b464?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556761175-b3cde8e0d4e8?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1497435334941-8c899ee9e8e9?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1497215842964-222b430dc094?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&h=600&fit=crop',
		'https://images.unsplash.com/photo-1556740758-90de374c12ad?w=800&h=600&fit=crop',
	);
	
	$updated_count = 0;
	$used_images = array();
	
	foreach ( $all_posts as $post_id ) {
		if ( ! has_post_thumbnail( $post_id ) ) {
			$post_title = get_the_title( $post_id );
			$new_image_url = sme_get_relevant_image_for_post( $post_id, $image_pool );
			$attempts = 0;
			while ( in_array( $new_image_url, $used_images, true ) && $attempts < 10 ) {
				$new_image_url = sme_get_relevant_image_for_post( $post_id, $image_pool );
				$attempts++;
			}
			
			if ( sme_set_featured_image_from_url( $post_id, $new_image_url, $post_title, true ) ) {
				$used_images[] = $new_image_url;
				$updated_count++;
			}
		}
	}
	
	wp_cache_flush();
	wp_redirect( admin_url( 'edit.php?post_type=post&images_ensured=' . $updated_count ) );
	exit;
}

add_action( 'template_redirect', 'sme_redirect_404_to_homepage' );
function sme_redirect_404_to_homepage() {
	if ( is_404() && ! is_admin() ) {
		wp_redirect( home_url( '/' ), 301 );
		exit;
	}
}

/**
 * Update existing niche topic posts with images
 * This function can be called manually to add images to posts that were created without them
 */
add_action( 'admin_init', 'sme_update_niche_topic_posts_images' );
function sme_update_niche_topic_posts_images() {
	if ( ! isset( $_GET['update_niche_images'] ) || sanitize_text_field( wp_unslash( $_GET['update_niche_images'] ) ) !== '1' || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	$niche_topics = array(
		'ai-in-business' => array(
			array( 'title' => 'How AI is Transforming Small Business Operations', 'image' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800&h=600&fit=crop' ),
			array( 'title' => 'Top 10 AI Tools Every Small Business Should Know', 'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop' ),
			array( 'title' => 'AI Implementation Guide for Small Businesses', 'image' => 'https://images.unsplash.com/photo-1555255707-c07966088b7b?w=800&h=600&fit=crop' ),
			array( 'title' => 'The ROI of AI: Measuring Success in Small Business', 'image' => 'https://images.unsplash.com/photo-1551288049-8d5ba95c4e9a?w=800&h=600&fit=crop' ),
			array( 'title' => 'AI and Customer Service: Revolutionizing Small Business Support', 'image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop' ),
		),
		'ecommerce-trends' => array(
			array( 'title' => 'E-commerce Trends Shaping 2024: What Small Businesses Need to Know', 'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop' ),
			array( 'title' => 'Mobile Commerce: Optimizing Your Store for Mobile Shoppers', 'image' => 'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?w=800&h=600&fit=crop' ),
			array( 'title' => 'Social Commerce: Selling Directly on Social Media Platforms', 'image' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=600&fit=crop' ),
			array( 'title' => 'E-commerce Conversion Rate Optimization: A Complete Guide', 'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop' ),
			array( 'title' => 'The Future of Online Payments: What Small E-commerce Businesses Should Know', 'image' => 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=800&h=600&fit=crop' ),
		),
		'startup-funding' => array(
			array( 'title' => 'Complete Guide to Startup Funding Options in 2024', 'image' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=600&fit=crop' ),
			array( 'title' => 'How to Pitch to Investors: Tips for Startup Founders', 'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop' ),
			array( 'title' => 'Bootstrapping vs. External Funding: Which is Right for Your Startup?', 'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop' ),
			array( 'title' => 'Government Grants for Small Businesses: How to Apply', 'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&h=600&fit=crop' ),
			array( 'title' => 'Crowdfunding for Startups: A Practical Guide', 'image' => 'https://images.unsplash.com/photo-1553729459-efe14ef6055d?w=800&h=600&fit=crop' ),
		),
		'green-economy' => array(
			array( 'title' => 'Building a Sustainable Business: A Guide for Small Business Owners', 'image' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=800&h=600&fit=crop' ),
			array( 'title' => 'Green Technology Solutions for Small Businesses', 'image' => 'https://images.unsplash.com/photo-1497435334941-8c899ee9e8e9?w=800&h=600&fit=crop' ),
			array( 'title' => 'The Business Case for Sustainability: Why Going Green Makes Sense', 'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop' ),
			array( 'title' => 'How to Reduce Your Business\'s Carbon Footprint', 'image' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=800&h=600&fit=crop' ),
			array( 'title' => 'Sustainable Supply Chain Management for Small Businesses', 'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop' ),
		),
		'remote-work' => array(
			array( 'title' => 'Remote Work Best Practices for Small Businesses', 'image' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=800&h=600&fit=crop' ),
			array( 'title' => 'Essential Tools for Remote Team Collaboration', 'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop' ),
			array( 'title' => 'Building Company Culture in a Remote Work Environment', 'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop' ),
			array( 'title' => 'Remote Work Security: Protecting Your Business Data', 'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop' ),
			array( 'title' => 'The Future of Work: Hybrid Models for Small Businesses', 'image' => 'https://images.unsplash.com/photo-1497215842964-222b430dc094?w=800&h=600&fit=crop' ),
		),
	);
	
	$updated_count = 0;
	
	foreach ( $niche_topics as $topic_slug => $posts ) {
		foreach ( $posts as $post_data ) {
			global $wpdb;
			$post_id = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'post' AND post_status = 'publish' LIMIT 1",
				$post_data['title']
			) );
			
			if ( $post_id && ! has_post_thumbnail( $post_id ) ) {
				$result = sme_set_featured_image_from_url( $post_id, $post_data['image'], $post_data['title'] );
				if ( $result ) {
					$updated_count++;
				}
			}
		}
	}
	
	if ( $updated_count > 0 ) {
		add_action( 'admin_notices', function() use ( $updated_count ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . sprintf( 'Updated %d posts with featured images.', $updated_count ) . '</p></div>';
		} );
	}
}

/**
 * Get attachment ID by image URL (check if image already exists in media library)
 */
function sme_get_attachment_by_url( $image_url ) {
	global $wpdb;
	
	$normalized_url = rtrim( preg_replace( '/\?.*/', '', $image_url ), '/' );
	
	$attachment_id = $wpdb->get_var( $wpdb->prepare(
		"SELECT post_id FROM {$wpdb->postmeta} 
		WHERE meta_key = '_wp_attached_file' 
		AND meta_value LIKE %s 
		LIMIT 1",
		'%' . $wpdb->esc_like( basename( $normalized_url ) ) . '%'
	) );
	
	if ( $attachment_id ) {
		$attachment = get_post( $attachment_id );
		if ( $attachment && $attachment->post_type === 'attachment' ) {
			$attachment_url = wp_get_attachment_image_url( $attachment_id, 'full' );
			$attachment_url_normalized = rtrim( preg_replace( '/\?.*/', '', $attachment_url ), '/' );
			
			if ( $attachment_url_normalized === $normalized_url ) {
				return $attachment_id;
			}
		}
	}
	
	return false;
}

function sme_set_featured_image_from_url( $post_id, $image_url, $alt_text = '', $force_update = false ) {
	if ( $force_update && has_post_thumbnail( $post_id ) ) {
		$old_thumbnail_id = get_post_thumbnail_id( $post_id );
		delete_post_thumbnail( $post_id );
		wp_delete_attachment( $old_thumbnail_id, true );
	}
	
	if ( ! $force_update && has_post_thumbnail( $post_id ) ) {
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		$current_image_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );
		
		$current_url_normalized = rtrim( preg_replace( '/\?.*/', '', $current_image_url ), '/' );
		$new_url_normalized = rtrim( preg_replace( '/\?.*/', '', $image_url ), '/' );
		
		if ( $current_url_normalized === $new_url_normalized ) {
			return true;
		}
	}
	
	$existing_attachment_id = sme_get_attachment_by_url( $image_url );
	if ( $existing_attachment_id ) {
		set_post_thumbnail( $post_id, $existing_attachment_id );
		if ( ! empty( $alt_text ) ) {
			update_post_meta( $existing_attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt_text ) );
		}
		return $existing_attachment_id;
	}
	
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );
	
	$timeout = 30;
	$tmp = download_url( $image_url, $timeout );
	
	if ( is_wp_error( $tmp ) ) {
		$attachment_id = sme_create_attachment_from_url( $image_url, $post_id, $alt_text );
		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
			return $attachment_id;
		}
		return false;
	}
	
	$parsed_url = parse_url( $image_url );
	$path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
	
	$file_extension = pathinfo( $path, PATHINFO_EXTENSION );
	if ( empty( $file_extension ) ) {
		if ( strpos( $image_url, '.png' ) !== false ) {
			$file_extension = 'png';
		} elseif ( strpos( $image_url, '.webp' ) !== false ) {
			$file_extension = 'webp';
		} else {
		$file_extension = 'jpg';
	}
	}
	
	$base_name = basename( $path );
	if ( ! empty( $base_name ) && preg_match( '/\.(jpg|jpeg|png|gif|webp)$/i', $base_name ) ) {
		$file_name = sanitize_file_name( $base_name );
	} else {
		if ( preg_match( '/photo-([a-z0-9-]+)/i', $image_url, $matches ) ) {
			$photo_id = sanitize_file_name( $matches[1] );
			$file_name = 'unsplash-' . substr( $photo_id, 0, 20 ) . '.' . $file_extension;
		} else {
			$file_name = 'image-' . $post_id . '-' . time() . '.' . $file_extension;
		}
	}
	
	$file_array = array(
		'name'     => $file_name,
		'tmp_name' => $tmp,
	);
	
	$attachment_id = media_handle_sideload( $file_array, $post_id );
	
	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $file_array['tmp_name'] );
		$attachment_id = sme_create_attachment_from_url( $image_url, $post_id, $alt_text );
		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
			return $attachment_id;
		}
		return false;
	}
	
	set_post_thumbnail( $post_id, $attachment_id );
	
	if ( ! empty( $alt_text ) ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt_text ) );
	}
	
	return $attachment_id;
}

function sme_create_attachment_from_url( $image_url, $post_id, $alt_text = '' ) {
	$response = wp_remote_get( $image_url, array(
		'timeout' => 30,
		'redirection' => 5,
	) );
	
	if ( is_wp_error( $response ) ) {
		return false;
	}
	
	$body = wp_remote_retrieve_body( $response );
	if ( empty( $body ) ) {
		return false;
	}
	
	$upload_dir = wp_upload_dir();
	
	$parsed_url = parse_url( $image_url );
	$path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
	
	$file_extension = pathinfo( $path, PATHINFO_EXTENSION );
	if ( empty( $file_extension ) ) {
		if ( strpos( $image_url, '.png' ) !== false ) {
			$file_extension = 'png';
		} elseif ( strpos( $image_url, '.webp' ) !== false ) {
			$file_extension = 'webp';
		} else {
			$file_extension = 'jpg';
		}
	}
	
	$base_name = basename( $path );
	if ( ! empty( $base_name ) && preg_match( '/\.(jpg|jpeg|png|gif|webp)$/i', $base_name ) ) {
		$file_name = sanitize_file_name( $base_name );
	} else {
		if ( preg_match( '/photo-([a-z0-9-]+)/i', $image_url, $matches ) ) {
			$photo_id = sanitize_file_name( $matches[1] );
			$file_name = 'unsplash-' . substr( $photo_id, 0, 20 ) . '.' . $file_extension;
		} else {
			$file_name = 'image-' . $post_id . '-' . time() . '.' . $file_extension;
		}
	}
	
	$file_path = $upload_dir['path'] . '/' . $file_name;
	file_put_contents( $file_path, $body );
	
	$file_type = wp_check_filetype( $file_name, null );
	$attachment_data = array(
		'post_mime_type' => $file_type['type'],
		'post_title'     => sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);
	
	$attachment_id = wp_insert_attachment( $attachment_data, $file_path, $post_id );
	
	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $file_path );
		return false;
	}
	
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	$attach_data = wp_generate_attachment_metadata( $attachment_id, $file_path );
	wp_update_attachment_metadata( $attachment_id, $attach_data );
	
	if ( ! empty( $alt_text ) ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt_text ) );
	}
	
	return $attachment_id;
}

add_action( 'admin_menu', 'sme_hide_default_categories', 999 );
function sme_hide_default_categories() {
	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
}

add_action( 'admin_menu', 'sme_hide_sub_topics', 999 );
function sme_hide_sub_topics() {
	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=sub_topic' );
}

add_filter( 'pre_delete_term', 'sme_allow_category_deletion', 10, 3 );
function sme_allow_category_deletion( $delete, $term, $taxonomy = '' ) {
	if ( empty( $taxonomy ) ) {
		$taxonomy = 'main_category';
	}
	
	if ( $taxonomy !== 'main_category' ) {
		return $delete;
	}
	
	if ( $delete === false ) {
		$term_obj = get_term( $term, $taxonomy );
		if ( $term_obj && ! is_wp_error( $term_obj ) && $term_obj->count > 0 ) {
			return true;
		}
	}
	
	return $delete;
}

add_action( 'admin_notices', 'sme_category_deletion_error_handler' );
function sme_category_deletion_error_handler() {
	if ( ! isset( $_GET['taxonomy'] ) || sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) !== 'main_category' ) {
		return;
	}
	
	if ( isset( $_GET['deleted'] ) && $_GET['deleted'] === '0' ) {
		$term_id = isset( $_GET['term_id'] ) ? intval( $_GET['term_id'] ) : 0;
		
		if ( $term_id > 0 ) {
			$term = get_term( $term_id, 'main_category' );
			
			if ( $term && ! is_wp_error( $term ) ) {
				echo '<div class="notice notice-error is-dismissible">';
				echo '<p><strong>Error deleting category:</strong> "' . esc_html( $term->name ) . '"</p>';
				
				if ( $term->count > 0 ) {
					echo '<p>This category has ' . $term->count . ' posts. Please use the <a href="' . esc_url( get_template_directory_uri() . '/delete-category-safe.php?term_id=' . $term_id ) . '" target="_blank">Safe Deletion Tool</a> to move posts first, or delete the category directly from the database.</p>';
				} else {
					echo '<p>Please try again or contact support if the problem persists.</p>';
				}
				
				echo '</div>';
			}
		}
	}
}

add_action( 'admin_menu', 'sme_hide_default_tags', 999 );
function sme_hide_default_tags() {
	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
}

add_filter( 'manage_edit-post_columns', 'sme_change_categories_column_name', 20 );
function sme_change_categories_column_name( $columns ) {
	if ( isset( $columns['categories'] ) ) {
		unset( $columns['categories'] );
	}
	
	if ( isset( $columns['tags'] ) ) {
		unset( $columns['tags'] );
	}
	
	if ( isset( $columns['taxonomy-sub_topic'] ) ) {
		unset( $columns['taxonomy-sub_topic'] );
	}
	
	if ( isset( $columns['taxonomy-main_category'] ) ) {
		$columns['taxonomy-main_category'] = __( 'Main Categories', 'sme-insights' );
	}
	
	$new_columns = array();
	
	if ( isset( $columns['cb'] ) ) {
		$new_columns['cb'] = $columns['cb'];
		unset( $columns['cb'] );
	}
	
	if ( isset( $columns['title'] ) ) {
		$new_columns['title'] = $columns['title'];
		unset( $columns['title'] );
	}
	
	if ( isset( $columns['taxonomy-main_category'] ) ) {
		$new_columns['taxonomy-main_category'] = $columns['taxonomy-main_category'];
		unset( $columns['taxonomy-main_category'] );
	}
	
	if ( isset( $columns['taxonomy-article_tag'] ) ) {
		$new_columns['taxonomy-article_tag'] = $columns['taxonomy-article_tag'];
		unset( $columns['taxonomy-article_tag'] );
	}
	
	if ( isset( $columns['author'] ) ) {
		$new_columns['author'] = $columns['author'];
		unset( $columns['author'] );
	}
	
	if ( isset( $columns['breaking_news'] ) ) {
		$new_columns['breaking_news'] = $columns['breaking_news'];
		unset( $columns['breaking_news'] );
	}
	
	if ( isset( $columns['comments'] ) ) {
		$new_columns['comments'] = $columns['comments'];
		unset( $columns['comments'] );
	}
	
	if ( isset( $columns['date'] ) ) {
		$new_columns['date'] = $columns['date'];
		unset( $columns['date'] );
	}
	foreach ( $columns as $key => $value ) {
		if ( ! isset( $new_columns[ $key ] ) ) {
			$new_columns[ $key ] = $value;
		}
	}
	
	return $new_columns;
}

add_action( 'admin_init', 'sme_create_default_menu_if_needed' );
function sme_create_default_menu_if_needed() {
	if ( ! is_admin() ) {
		return;
	}
	
	$menu_name = 'Primary Menu';
	$menu_exists = wp_get_nav_menu_object( $menu_name );
	
	if ( ! $menu_exists ) {
		$theme_setup = SME_Theme_Setup::get_instance();
		if ( method_exists( $theme_setup, 'create_default_menu' ) ) {
			$theme_setup->create_default_menu();
			$menu_exists = wp_get_nav_menu_object( $menu_name );
		}
	} else {
		$menu_items = wp_get_nav_menu_items( $menu_exists->term_id );
		if ( empty( $menu_items ) ) {
			$theme_setup = SME_Theme_Setup::get_instance();
			if ( method_exists( $theme_setup, 'create_default_menu' ) ) {
				$theme_setup->create_default_menu();
			}
		} else {
			sme_ensure_about_contact_in_menu( $menu_exists->term_id );
		}
	}
	
	if ( $menu_exists ) {
		$locations = get_theme_mod( 'nav_menu_locations' );
		if ( ! is_array( $locations ) ) {
			$locations = array();
		}
		
		$locations['primary'] = $menu_exists->term_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}

/**
 * Auto-assign Primary Menu to primary location when menu is saved
 * This ensures the menu is always assigned after saving
 */
add_action( 'wp_update_nav_menu', 'sme_auto_assign_primary_menu', 10, 1 );
function sme_auto_assign_primary_menu( $menu_id ) {
	$menu = wp_get_nav_menu_object( $menu_id );
	
	if ( $menu && $menu->name === 'Primary Menu' ) {
		$locations = get_theme_mod( 'nav_menu_locations' );
		if ( ! is_array( $locations ) ) {
			$locations = array();
		}
		
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
		sme_ensure_about_contact_in_menu( $menu_id );
	}
}

/**
 * Ensure About and Contact pages are always in the menu
 */
function sme_ensure_about_contact_in_menu( $menu_id ) {
	if ( ! $menu_id ) {
		return false;
	}
	
	$menu_items = wp_get_nav_menu_items( $menu_id );
	$existing_urls = array();
	$existing_titles = array();
	$existing_object_ids = array();
	
	if ( $menu_items ) {
		foreach ( $menu_items as $item ) {
			if ( $item->url ) {
				$existing_urls[] = $item->url;
			}
			if ( $item->title ) {
				$existing_titles[] = strtolower( trim( $item->title ) );
			}
			if ( $item->object_id ) {
				$existing_object_ids[] = $item->object_id;
			}
		}
	}
	
	$added = false;
	
	$about_page = get_page_by_path( 'about' );
	if ( ! $about_page ) {
		$about_page = get_page_by_path( 'about-us' );
	}
	if ( ! $about_page ) {
		$about_page = sme_get_page_by_title( 'About' );
	}
	if ( ! $about_page ) {
		$about_page = sme_get_page_by_title( 'About Us' );
	}
	
	if ( $about_page ) {
		$about_url = get_permalink( $about_page->ID );
		$about_title_lower = 'about';
		
		if ( ! in_array( $about_url, $existing_urls ) && 
		     ! in_array( $about_title_lower, $existing_titles ) &&
		     ! in_array( $about_page->ID, $existing_object_ids ) ) {
			$result = wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-title'  => 'About',
				'menu-item-url'    => $about_url,
				'menu-item-status' => 'publish',
				'menu-item-type'   => 'post_type',
				'menu-item-object' => 'page',
				'menu-item-object-id' => $about_page->ID,
			) );
			if ( ! is_wp_error( $result ) ) {
				$added = true;
			}
		}
	}
	
	$contact_page = get_page_by_path( 'contact' );
	if ( ! $contact_page ) {
		$contact_page = get_page_by_path( 'contact-us' );
	}
	if ( ! $contact_page ) {
		$contact_page = sme_get_page_by_title( 'Contact' );
	}
	if ( ! $contact_page ) {
		$contact_page = sme_get_page_by_title( 'Contact Us' );
	}
	
	if ( $contact_page ) {
		$contact_url = get_permalink( $contact_page->ID );
		$contact_title_lower = 'contact';
		
		if ( ! in_array( $contact_url, $existing_urls ) && 
		     ! in_array( $contact_title_lower, $existing_titles ) &&
		     ! in_array( $contact_page->ID, $existing_object_ids ) ) {
			$result = wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-title'  => 'Contact',
				'menu-item-url'    => $contact_url,
				'menu-item-status' => 'publish',
				'menu-item-type'   => 'post_type',
				'menu-item-object' => 'page',
				'menu-item-object-id' => $contact_page->ID,
			) );
			if ( ! is_wp_error( $result ) ) {
				$added = true;
			}
		}
	}
	
	return $added;
}

/**
 * Hook to ensure About and Contact are in menu on every page load (admin only)
 */
add_action( 'admin_init', 'sme_ensure_menu_has_about_contact', 20 );
function sme_ensure_menu_has_about_contact() {
	if ( ! is_admin() ) {
		return;
	}
	
	$menu_name = 'Primary Menu';
	$menu = wp_get_nav_menu_object( $menu_name );
	
	if ( $menu ) {
		sme_ensure_about_contact_in_menu( $menu->term_id );
	}
}

/**
 * Add admin notice and button to create menu manually
 */
add_action( 'admin_notices', 'sme_menu_creation_notice' );
function sme_menu_creation_notice() {
	$screen = get_current_screen();
	if ( ! $screen || $screen->id !== 'nav-menus' ) {
		return;
	}
	
	$menu_name = 'Primary Menu';
	$menu_exists = wp_get_nav_menu_object( $menu_name );
	
	$locations = get_theme_mod( 'nav_menu_locations' );
	$menu_assigned = false;
	if ( is_array( $locations ) && isset( $locations['primary'] ) ) {
		$assigned_menu = wp_get_nav_menu_object( $locations['primary'] );
		if ( $assigned_menu ) {
			$menu_assigned = true;
		}
	}
	
	if ( ! $menu_exists || ! $menu_assigned ) {
		$create_url = add_query_arg( array(
			'action' => 'sme_create_default_menu',
			'_wpnonce' => wp_create_nonce( 'sme_create_menu' ),
		), admin_url( 'nav-menus.php' ) );
		
		echo '<div class="notice notice-info is-dismissible">';
		echo '<p><strong>' . esc_html__( 'SME Insights:', 'sme-insights' ) . '</strong> ';
		if ( ! $menu_exists ) {
			echo esc_html__( 'No default menu found. ', 'sme-insights' );
		} else {
			echo esc_html__( 'Menu exists but is not assigned to Primary Menu location. ', 'sme-insights' );
		}
		echo '<a href="' . esc_url( $create_url ) . '" class="button button-primary">' . esc_html__( 'Create Default Menu', 'sme-insights' ) . '</a>';
		echo '</p></div>';
	}
}

/**
 * Handle menu creation request
 */
add_action( 'admin_init', 'sme_handle_menu_creation' );
function sme_handle_menu_creation() {
	if ( ! isset( $_GET['action'] ) ) {
		return;
	}
	
	$action = sanitize_text_field( $_GET['action'] );
	if ( $action !== 'sme_create_default_menu' ) {
		return;
	}
	
	// Verify nonce
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_GET['_wpnonce'] ), 'sme_create_menu' ) ) {
		wp_die( esc_html__( 'Security check failed', 'sme-insights' ) );
	}
	
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to perform this action', 'sme-insights' ) );
	}
	
		$theme_setup = SME_Theme_Setup::get_instance();
		if ( method_exists( $theme_setup, 'create_default_menu' ) ) {
			$theme_setup->create_default_menu();
			
			$created_menu = wp_get_nav_menu_object( 'Primary Menu' );
			if ( $created_menu && ! is_wp_error( $created_menu ) ) {
				wp_redirect( add_query_arg( array(
					'menu' => $created_menu->term_id,
					'sme_menu_created' => '1',
				), admin_url( 'nav-menus.php' ) ) );
				exit;
			}
		}
}

/**
 * Show success message after menu creation
 */
add_action( 'admin_notices', 'sme_menu_creation_success' );
function sme_menu_creation_success() {
	if ( ! isset( $_GET['sme_menu_created'] ) ) {
		return;
	}
	
	$menu_created = sanitize_text_field( $_GET['sme_menu_created'] );
	if ( $menu_created !== '1' ) {
		return;
	}
	
	$screen = get_current_screen();
	if ( ! $screen || $screen->id !== 'nav-menus' ) {
		return;
	}
	
	echo '<div class="notice notice-success is-dismissible">';
	echo '<p>' . esc_html__( 'Default menu created successfully! You can now edit it below.', 'sme-insights' ) . '</p>';
	echo '</div>';
}


/**
 * Coming Soon Mode
 * Redirect visitors to coming soon page if enabled
 */
add_action( 'template_redirect', 'sme_coming_soon_redirect', 1 );
function sme_coming_soon_redirect() {
	$coming_soon_enabled = get_theme_mod( 'sme_enable_coming_soon', false );
	
	if ( $coming_soon_enabled === false ) {
		$theme_mods = get_option( 'theme_mods_' . get_stylesheet(), array() );
		$coming_soon_enabled = isset( $theme_mods['sme_enable_coming_soon'] ) ? $theme_mods['sme_enable_coming_soon'] : false;
	}
	
	$is_enabled = false;
	if ( $coming_soon_enabled === true || $coming_soon_enabled === '1' || $coming_soon_enabled === 1 || $coming_soon_enabled === 'true' ) {
		$is_enabled = true;
	}
	
	if ( ! $is_enabled ) {
		return;
	}
	
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}
	
	if ( is_admin() || is_login() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}
	
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}
	
	if ( is_page_template( 'coming-soon.php' ) ) {
		return;
	}
	
	if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
		return;
	}
	
	$coming_soon_template = locate_template( 'coming-soon.php' );
	if ( $coming_soon_template ) {
		status_header( 200 );
		nocache_headers();
		// Load the template
		load_template( $coming_soon_template, true );
		exit;
	}
}

function sme_get_page_by_title( $title ) {
	if ( empty( $title ) ) {
		return null;
	}
	
	$title_filter = function( $where ) use ( $title ) {
	global $wpdb;
		$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_title = %s", $title );
		return $where;
	};
	
	add_filter( 'posts_where', $title_filter );
	
	$query = new WP_Query( array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	) );
	
	remove_filter( 'posts_where', $title_filter );
	
	if ( $query->have_posts() ) {
		return $query->posts[0];
	}
	
	return null;
}

/**
 * Add IDs to headings in post content for table of contents
 *
 * @param string $content The post content.
 * @return string Modified content with heading IDs.
 */
function sme_add_heading_ids( $content ) {
	if ( ! is_single() ) {
		return $content;
	}

	$pattern = '/<h([2-3])([^>]*)>(.*?)<\/h\1>/i';
	$index = 0;

	$content = preg_replace_callback( $pattern, function( $matches ) use ( &$index ) {
		$tag = $matches[1];
		$attributes = $matches[2];
		$text = $matches[3];
		
		if ( preg_match( '/id=["\']([^"\']+)["\']/', $attributes, $id_match ) ) {
			return $matches[0];
		}
		
		$heading_text = strip_tags( $text );
		$id = 'heading-' . $index;
		$index++;
		
		return '<h' . $tag . $attributes . ' id="' . esc_attr( $id ) . '">' . $text . '</h' . $tag . '>';
	}, $content );

	return $content;
}
add_filter( 'the_content', 'sme_add_heading_ids', 20 );

/**
 * Create Blog Page automatically if it doesn't exist
 * 
 * @package SME_Insights
 * @since 1.0.0
 */
add_action( 'admin_init', 'sme_create_blog_page_on_admin_init' );
function sme_create_blog_page_on_admin_init() {
	if ( get_option( 'sme_blog_page_created' ) ) {
		return;
	}
	
	$page_title = 'Business News & Insights';
	$page_slug = 'blog';
	
	$existing_page = get_page_by_path( $page_slug );
	
	if ( ! $existing_page ) {
		$page_data = array(
			'post_title'    => $page_title,
			'post_name'     => $page_slug,
			'post_content'  => 'Discover the latest insights, trends, and expert analysis from our team of business professionals.',
			'post_status'   => 'publish',
			'post_type'     => 'page',
			'post_author'   => 1,
		);
		
		$page_id = wp_insert_post( $page_data );
		
		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_post_meta( $page_id, '_wp_page_template', 'page-blog.php' );
			update_option( 'sme_blog_page_created', true );
		}
	} else {
		update_post_meta( $existing_page->ID, '_wp_page_template', 'page-blog.php' );
		update_option( 'sme_blog_page_created', true );
	}
}

/**
 * Ensure every post has a category and tags when saved
 *
 * @param int $post_id Post ID
 */
function sme_ensure_post_has_category_and_tags( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	
	if ( get_post_type( $post_id ) !== 'post' ) {
		return;
	}
	
	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}
	
	$categories = wp_get_post_terms( $post_id, 'main_category', array( 'fields' => 'ids' ) );
	$has_valid_category = false;
	
	if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
		foreach ( $categories as $cat_id ) {
			$term = get_term( $cat_id, 'main_category' );
			if ( $term && $term->slug !== 'uncategorized' && $term->name !== 'Uncategorized' ) {
				$has_valid_category = true;
				break;
			}
		}
	}
	
	if ( ! $has_valid_category ) {
		$uncategorized = get_term_by( 'slug', 'uncategorized', 'main_category' );
		if ( $uncategorized ) {
			wp_remove_object_terms( $post_id, $uncategorized->term_id, 'main_category' );
		}
		
		$all_categories = get_terms( array(
			'taxonomy' => 'main_category',
			'hide_empty' => false,
		) );
		
		if ( ! empty( $all_categories ) && ! is_wp_error( $all_categories ) ) {
			$valid_category = null;
			foreach ( $all_categories as $cat ) {
				if ( $cat->slug !== 'uncategorized' && $cat->name !== 'Uncategorized' ) {
					$valid_category = $cat;
					break;
				}
			}
			
			if ( ! $valid_category && ! empty( $all_categories ) ) {
				$first_cat = reset( $all_categories );
				if ( $first_cat->slug !== 'uncategorized' ) {
					$valid_category = $first_cat;
				}
			}
			
			if ( $valid_category ) {
				wp_set_object_terms( $post_id, array( $valid_category->term_id ), 'main_category', true );
			}
		}
	}
	
	$tags = wp_get_post_terms( $post_id, 'article_tag', array( 'fields' => 'ids' ) );
	
	if ( empty( $tags ) || is_wp_error( $tags ) ) {
		sme_assign_tags_to_post( $post_id, $post->post_title, $post->post_content );
	}
}
add_action( 'save_post', 'sme_ensure_post_has_category_and_tags', 20 );

/**
 * Assign tags to post based on content
 *
 * @param int    $post_id      Post ID
 * @param string $post_title   Post title
 * @param string $post_content Post content
 */
function sme_assign_tags_to_post( $post_id, $post_title = '', $post_content = '' ) {
	if ( empty( $post_title ) ) {
		$post = get_post( $post_id );
		if ( $post ) {
			$post_title = $post->post_title;
			$post_content = $post->post_content;
		}
	}
	
	$text = strtolower( $post_title . ' ' . $post_content );
	$suggested_tags = array();
	
	$keyword_tag_map = array(
		'marketing' => 'Marketing',
		'advertising' => 'Marketing',
		'brand' => 'Branding',
		'social media' => 'Social Media',
		'seo' => 'SEO',
		'content marketing' => 'Content Marketing',
		'email marketing' => 'Email Marketing',
		'digital marketing' => 'Digital Marketing',
		'finance' => 'Finance',
		'financial' => 'Finance',
		'budget' => 'Budgeting',
		'tax' => 'Tax',
		'investment' => 'Investment',
		'loan' => 'Loans',
		'grant' => 'Grants',
		'funding' => 'Funding',
		'cash flow' => 'Cash Flow',
		'technology' => 'Technology',
		'tech' => 'Technology',
		'digital' => 'Digital',
		'ai' => 'Artificial Intelligence',
		'artificial intelligence' => 'Artificial Intelligence',
		'automation' => 'Automation',
		'software' => 'Software',
		'e-commerce' => 'E-commerce',
		'ecommerce' => 'E-commerce',
		'strategy' => 'Strategy',
		'strategies' => 'Strategy',
		'planning' => 'Planning',
		'growth' => 'Growth',
		'scaling' => 'Scaling',
		'operations' => 'Operations',
		'management' => 'Management',
		'productivity' => 'Productivity',
		'sales' => 'Sales',
		'customer' => 'Customer Service',
		'business' => 'Business',
		'entrepreneur' => 'Entrepreneurship',
		'startup' => 'Startups',
		'small business' => 'Small Business',
		'sme' => 'SME',
	);
	
	foreach ( $keyword_tag_map as $keyword => $tag ) {
		if ( strpos( $text, $keyword ) !== false ) {
			if ( ! in_array( $tag, $suggested_tags ) ) {
				$suggested_tags[] = $tag;
			}
		}
	}
	
	if ( empty( $suggested_tags ) ) {
		$categories = wp_get_post_terms( $post_id, 'main_category', array( 'fields' => 'names' ) );
		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
			$suggested_tags[] = $categories[0];
		} else {
			$suggested_tags = array( 'Business', 'Tips' );
		}
	}
	
	$suggested_tags = array_slice( $suggested_tags, 0, 3 );
	
	if ( ! empty( $suggested_tags ) ) {
		$tag_ids = array();
		foreach ( $suggested_tags as $tag_name ) {
			$tag = get_term_by( 'name', $tag_name, 'article_tag' );
			if ( ! $tag ) {
				$tag_result = wp_insert_term( $tag_name, 'article_tag' );
				if ( ! is_wp_error( $tag_result ) ) {
					$tag_ids[] = $tag_result['term_id'];
				}
			} else {
				$tag_ids[] = $tag->term_id;
			}
		}
		
		if ( ! empty( $tag_ids ) ) {
			wp_set_object_terms( $post_id, $tag_ids, 'article_tag', true );
		}
	}
}

/**
 * Add IDs to paragraphs in post content for table of contents fallback
 * Only adds IDs if no headings are found
 *
 * @param string $content The post content.
 * @return string Modified content with paragraph IDs.
 */
function sme_add_paragraph_ids( $content ) {
	if ( ! is_single() ) {
		return $content;
	}

	preg_match_all( '/<h([2-3])[^>]*>/i', $content, $headings );
	
	if ( empty( $headings[0] ) ) {
		$pattern = '/<p([^>]*)>(.*?)<\/p>/i';
		$para_index = 0;

		$content = preg_replace_callback( $pattern, function( $matches ) use ( &$para_index ) {
			$attributes = $matches[1];
			$text = $matches[2];
			
			if ( preg_match( '/id=["\']([^"\']+)["\']/', $attributes, $id_match ) ) {
				return $matches[0];
			}
			
			$text_content = strip_tags( $text );
			$text_content = trim( $text_content );
			
			if ( ! empty( $text_content ) && mb_strlen( $text_content ) >= 20 ) {
				$id = 'paragraph-' . ( $para_index + 1 );
				$para_index++;
				
				return '<p' . $attributes . ' id="' . esc_attr( $id ) . '">' . $text . '</p>';
			}
			
			return $matches[0]; // Return as is if paragraph is too short
		}, $content );
	}

	return $content;
}
add_filter( 'the_content', 'sme_add_paragraph_ids', 25 );

/**
 * Homepage Post Tracking System
 * Prevents posts from appearing multiple times on homepage
 */

// Initialize global array to track displayed post IDs
function sme_init_homepage_post_tracker() {
	global $sme_displayed_posts;
	if ( ! isset( $sme_displayed_posts ) ) {
		$sme_displayed_posts = array();
	}
}
add_action( 'wp', 'sme_init_homepage_post_tracker' );

/**
 * Mark a post as displayed on homepage
 * 
 * @param int $post_id Post ID
 * @return bool True if post was not already displayed, false if it was
 */
function sme_mark_post_displayed( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return false;
	}
	
	global $sme_displayed_posts;
	sme_init_homepage_post_tracker();
	
	if ( in_array( $post_id, $sme_displayed_posts ) ) {
		return false; // Already displayed
	}
	
	$sme_displayed_posts[] = $post_id;
	return true;
}

/**
 * Check if a post has been displayed on homepage
 * 
 * @param int $post_id Post ID
 * @return bool True if already displayed, false if not
 */
function sme_is_post_displayed( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return false;
	}
	
	global $sme_displayed_posts;
	sme_init_homepage_post_tracker();
	
	return in_array( $post_id, $sme_displayed_posts );
}

/**
 * Get array of displayed post IDs (for use in post__not_in)
 * 
 * @return array Array of post IDs
 */
function sme_get_displayed_post_ids() {
	global $sme_displayed_posts;
	sme_init_homepage_post_tracker();
	
	return $sme_displayed_posts;
}

/**
 * Handle Coming Soon email subscription via AJAX
 */
add_action( 'wp_ajax_sme_coming_soon_subscribe', 'sme_coming_soon_subscribe_handler' );
add_action( 'wp_ajax_nopriv_sme_coming_soon_subscribe', 'sme_coming_soon_subscribe_handler' );
function sme_coming_soon_subscribe_handler() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'sme_coming_soon_subscribe' ) ) {
		wp_send_json_error( array( 'message' => 'Security check failed. Please refresh the page and try again.' ) );
	}
	
	$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
	
	if ( empty( $email ) || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
	}
	
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rate_limit_key = 'sme_coming_soon_rate_limit_' . md5( $ip );
	$rate_limit_count = get_transient( $rate_limit_key );
	
	if ( $rate_limit_count && $rate_limit_count >= 5 ) {
		wp_send_json_error( array( 'message' => 'Too many requests. Please try again later.' ) );
	}
	
	if ( $rate_limit_count ) {
		set_transient( $rate_limit_key, $rate_limit_count + 1, 3600 );
	} else {
		set_transient( $rate_limit_key, 1, 3600 );
	}
	
	$saved_emails = get_option( 'sme_coming_soon_subscribers', array() );
	
	if ( in_array( $email, $saved_emails ) ) {
		wp_send_json_success( array( 'message' => 'You are already subscribed! We\'ll notify you when we launch.' ) );
	}
	
	$max_subscribers = apply_filters( 'sme_coming_soon_max_subscribers', 10000 );
	if ( count( $saved_emails ) >= $max_subscribers ) {
		wp_send_json_error( array( 'message' => 'Subscription limit reached. Please contact the site administrator.' ) );
	}
	
	$saved_emails[] = $email;
	update_option( 'sme_coming_soon_subscribers', $saved_emails );
	
	$notification_email = get_theme_mod( 'sme_coming_soon_notification_email', get_option( 'admin_email' ) );
	
	// Send notification email to admin
	if ( ! empty( $notification_email ) && is_email( $notification_email ) ) {
		$site_name = get_bloginfo( 'name' );
		$subject = sprintf( '[%s] New Coming Soon Subscriber', $site_name );
		$message = sprintf(
			"A new subscriber has signed up for your Coming Soon page:\n\n" .
			"Email: %s\n" .
			"Date: %s\n" .
			"Total Subscribers: %d\n\n" .
			"You can view all subscribers in: SME Insights > Coming Soon Settings",
			$email,
			current_time( 'mysql' ),
			count( $saved_emails )
		);
		
		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
		wp_mail( $notification_email, $subject, $message, $headers );
	}
	
	// Send confirmation email to subscriber
	$site_name = get_bloginfo( 'name' );
	$site_url = home_url();
	$subject = sprintf( 'Thank you for subscribing to %s', $site_name );
	$message = sprintf(
		"Hello,\n\n" .
		"Thank you for subscribing to %s!\n\n" .
		"We're working hard to bring you amazing content and we'll notify you as soon as we launch.\n\n" .
		"Stay tuned!\n\n" .
		"Best regards,\n" .
		"The %s Team\n" .
		"%s",
		$site_name,
		$site_name,
		$site_url
	);
	
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	wp_mail( $email, $subject, $message, $headers );
	
	wp_send_json_success( array( 'message' => 'Thank you! We\'ll notify you when we launch.' ) );
}

/**
 * Get Coming Soon subscribers count
 */
function sme_get_coming_soon_subscribers_count() {
	$subscribers = get_option( 'sme_coming_soon_subscribers', array() );
	return count( $subscribers );
}

/**
 * Get Coming Soon subscribers list
 */
function sme_get_coming_soon_subscribers() {
	return get_option( 'sme_coming_soon_subscribers', array() );
}

/**
 * Handle Newsletter subscription via AJAX
 */
add_action( 'wp_ajax_sme_newsletter_subscribe', 'sme_newsletter_subscribe_handler' );
add_action( 'wp_ajax_nopriv_sme_newsletter_subscribe', 'sme_newsletter_subscribe_handler' );
function sme_newsletter_subscribe_handler() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'sme_newsletter_subscribe' ) ) {
		wp_send_json_error( array( 'message' => 'Security check failed. Please refresh the page and try again.' ) );
	}
	
	$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
	
	if ( empty( $email ) || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
	}
	
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rate_limit_key = 'sme_newsletter_rate_limit_' . md5( $ip );
	$rate_limit_count = get_transient( $rate_limit_key );
	
	if ( $rate_limit_count && $rate_limit_count >= 5 ) {
		wp_send_json_error( array( 'message' => 'Too many requests. Please try again later.' ) );
	}
	
	if ( $rate_limit_count ) {
		set_transient( $rate_limit_key, $rate_limit_count + 1, 3600 );
	} else {
		set_transient( $rate_limit_key, 1, 3600 );
	}
	
	$saved_emails = get_option( 'sme_newsletter_subscribers', array() );
	
	if ( in_array( $email, $saved_emails ) ) {
		wp_send_json_success( array( 'message' => 'You are already subscribed! Thank you for your interest.' ) );
	}
	
	$saved_emails[] = $email;
	update_option( 'sme_newsletter_subscribers', $saved_emails );
	
	$subject = 'Welcome to SME Insights Newsletter!';
	$message = "Thank you for subscribing to SME Insights newsletter!\n\n";
	$message .= "You will now receive our weekly newsletter with the latest insights, strategies, and tools to grow your small business.\n\n";
	$message .= "If you have any questions, feel free to contact us.\n\n";
	$message .= "Best regards,\nSME Insights Team";
	
	$admin_email_addr = get_option( 'admin_email' );
	$site_name = get_bloginfo( 'name' );
	
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	$headers[] = 'From: ' . $site_name . ' <' . $admin_email_addr . '>';
	$headers[] = 'Reply-To: ' . $site_name . ' <' . $admin_email_addr . '>';
	
	$email_sent = false;
	if ( function_exists( 'wp_mail' ) ) {
		$email_sent = @wp_mail( $email, $subject, $message, $headers );
	
		if ( ! $email_sent && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Newsletter subscription email failed to send to: ' . $email );
		}
	}
	
	$notification_email = get_theme_mod( 'sme_newsletter_notification_email', $admin_email_addr );
	if ( ! is_email( $notification_email ) ) {
		$notification_email = $admin_email_addr;
	}
	
	$admin_subject = 'New Newsletter Subscription - SME Insights';
	$admin_message = "A new subscriber has joined the newsletter:\n\n";
	$admin_message .= "Email: " . $email . "\n";
	$admin_message .= "Date: " . current_time( 'mysql' ) . "\n";
	$admin_message .= "Total Subscribers: " . count( $saved_emails ) . "\n";
	
	$admin_headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	$admin_headers[] = 'From: ' . $site_name . ' <' . $notification_email . '>';
	$admin_headers[] = 'Reply-To: ' . $site_name . ' <' . $notification_email . '>';
	
	$admin_email_sent = false;
	if ( function_exists( 'wp_mail' ) ) {
		$admin_email_sent = @wp_mail( $notification_email, $admin_subject, $admin_message, $admin_headers );
	
		if ( ! $admin_email_sent && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Newsletter admin notification email failed to send to: ' . $notification_email );
		}
	}
	
	wp_send_json_success( array( 
		'message' => 'Thank you for subscribing! We will send you the latest insights.',
		'email_sent' => $email_sent
	) );
}

/**
 * Get newsletter subscribers count
 */
function sme_get_newsletter_subscribers_count() {
	$subscribers = get_option( 'sme_newsletter_subscribers', array() );
	return count( $subscribers );
}

/**
 * Get newsletter subscribers list
 */
function sme_get_newsletter_subscribers() {
	return get_option( 'sme_newsletter_subscribers', array() );
}

/**
 * Handle Contact Form submission via AJAX
 */
add_action( 'wp_ajax_sme_contact_form', 'sme_contact_form_handler' );
add_action( 'wp_ajax_nopriv_sme_contact_form', 'sme_contact_form_handler' );
add_action( 'admin_post_sme_contact_form', 'sme_contact_form_handler' );
add_action( 'admin_post_nopriv_sme_contact_form', 'sme_contact_form_handler' );
function sme_contact_form_handler() {
	if ( ! isset( $_POST['sme_contact_nonce'] ) || ! wp_verify_nonce( $_POST['sme_contact_nonce'], 'sme_contact_form' ) ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Security check failed. Please refresh the page and try again.' ) );
		} else {
			wp_die( 'Security check failed. Please refresh the page and try again.' );
		}
	}
	
	$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
	$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';
	
	if ( empty( $name ) || empty( $email ) || empty( $subject ) || empty( $message ) ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Please fill in all required fields.' ) );
		} else {
			wp_die( 'Please fill in all required fields.' );
		}
	}
	
	if ( ! is_email( $email ) ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
		} else {
			wp_die( 'Please enter a valid email address.' );
		}
	}
	
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rate_limit_key = 'sme_contact_rate_limit_' . md5( $ip );
	$rate_limit_count = get_transient( $rate_limit_key );
	
	if ( $rate_limit_count && $rate_limit_count >= 3 ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Too many requests. Please try again later.' ) );
		} else {
			wp_die( 'Too many requests. Please try again later.' );
		}
	}
	
	if ( $rate_limit_count ) {
		set_transient( $rate_limit_key, $rate_limit_count + 1, 3600 );
	} else {
		set_transient( $rate_limit_key, 1, 3600 );
	}
	
	$contact_form_email = get_theme_mod( 'sme_contact_form_email', '' );
	$admin_email = ! empty( $contact_form_email ) ? $contact_form_email : get_option( 'admin_email' );
	$email_subject = sprintf( '[%s] New Contact Form Submission: %s', get_bloginfo( 'name' ), $subject );
	
	$email_message = "New contact form submission:\n\n";
	$email_message .= "Name: " . $name . "\n";
	$email_message .= "Email: " . $email . "\n";
	$email_message .= "Subject: " . $subject . "\n";
	$email_message .= "Message:\n" . $message . "\n\n";
	$email_message .= "Submitted on: " . current_time( 'mysql' ) . "\n";
	$email_message .= "IP Address: " . $ip . "\n";
	
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	$headers[] = 'From: ' . $name . ' <' . $email . '>';
	$headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
	
	$submissions = get_option( 'sme_contact_form_submissions', array() );
	$submission_data = array(
		'name'    => $name,
		'email'   => $email,
		'subject' => $subject,
		'message' => $message,
		'date'    => current_time( 'mysql' ),
		'ip'      => $ip,
	);
	$submissions[] = $submission_data;
	if ( count( $submissions ) > 100 ) {
		$submissions = array_slice( $submissions, -100 );
	}
	update_option( 'sme_contact_form_submissions', $submissions );
	
	$sent = false;
	$email_error = '';
	
	if ( function_exists( 'wp_mail' ) ) {
		$sent = @wp_mail( $admin_email, $email_subject, $email_message, $headers );
		if ( ! $sent ) {
			$email_error = 'Email sending failed, but your message has been saved.';
		}
	} else {
		$email_error = 'Email function not available, but your message has been saved.';
	}
	
	if ( function_exists( 'wp_mail' ) ) {
		$user_subject = sprintf( 'Thank you for contacting %s', get_bloginfo( 'name' ) );
		$user_message = "Hello " . $name . ",\n\n";
		$user_message .= "Thank you for contacting us. We have received your message and will get back to you as soon as possible.\n\n";
		$user_message .= "Your message:\n" . $message . "\n\n";
		$user_message .= "Best regards,\n" . get_bloginfo( 'name' ) . " Team";
		
		$user_headers = array( 'Content-Type: text/plain; charset=UTF-8' );
		$user_headers[] = 'From: ' . get_bloginfo( 'name' ) . ' <' . $admin_email . '>';
		
		@wp_mail( $email, $user_subject, $user_message, $user_headers );
	}
	
	// Handle newsletter subscription if checkbox is checked
	if ( isset( $_POST['newsletter_subscribe'] ) && $_POST['newsletter_subscribe'] === '1' ) {
		$subscribers = get_option( 'sme_newsletter_subscribers', array() );
		if ( ! in_array( $email, $subscribers, true ) ) {
			$subscribers[] = $email;
			update_option( 'sme_newsletter_subscribers', $subscribers );
			
			// Send welcome email for newsletter subscription
			$newsletter_email = get_theme_mod( 'sme_newsletter_notification_email', '' );
			if ( empty( $newsletter_email ) ) {
				$newsletter_email = get_option( 'admin_email' );
			}
			$site_name = get_bloginfo( 'name' );
			
			$subscriber_subject = 'Welcome to ' . $site_name . ' Newsletter!';
			$subscriber_message = "Hello " . $name . ",\n\n";
			$subscriber_message .= "Thank you for subscribing to our newsletter! You'll now receive the latest business insights, tips, and updates directly in your inbox.\n\n";
			$subscriber_message .= "We're excited to share valuable content with you.\n\n";
			$subscriber_message .= "Best regards,\n" . $site_name . " Team";
			
			$subscriber_headers = array( 'Content-Type: text/plain; charset=UTF-8' );
			$subscriber_headers[] = 'From: ' . $site_name . ' <' . $newsletter_email . '>';
			$subscriber_headers[] = 'Reply-To: ' . $site_name . ' <' . $newsletter_email . '>';
			
			if ( function_exists( 'wp_mail' ) ) {
				@wp_mail( $email, $subscriber_subject, $subscriber_message, $subscriber_headers );
				
				// Send notification to admin
				$admin_subject = 'New Newsletter Subscription - ' . $site_name;
				$admin_message = "A new subscriber has joined the newsletter:\n\n";
				$admin_message .= "Name: " . $name . "\n";
				$admin_message .= "Email: " . $email . "\n";
				$admin_message .= "Date: " . current_time( 'mysql' ) . "\n";
				$admin_message .= "Total Subscribers: " . count( $subscribers ) . "\n";
				
				$admin_headers = array( 'Content-Type: text/plain; charset=UTF-8' );
				$admin_headers[] = 'From: ' . $site_name . ' <' . $newsletter_email . '>';
				$admin_headers[] = 'Reply-To: ' . $site_name . ' <' . $newsletter_email . '>';
				
				@wp_mail( $newsletter_email, $admin_subject, $admin_message, $admin_headers );
			}
		}
	}
	
	// Always return success since we saved the submission
	// Email sending is optional (may fail in local development)
	if ( wp_doing_ajax() ) {
		$success_message = 'Thank you for your message! We have received it and will get back to you soon.';
		if ( isset( $_POST['newsletter_subscribe'] ) && $_POST['newsletter_subscribe'] === '1' ) {
			$success_message .= ' You have also been subscribed to our newsletter.';
		}
		wp_send_json_success( array( 
			'message' => $success_message
		) );
	} else {
		$redirect_url = add_query_arg( 'contact', 'success', wp_get_referer() ?: home_url( '/contact' ) );
		if ( isset( $_POST['newsletter_subscribe'] ) && $_POST['newsletter_subscribe'] === '1' ) {
			$redirect_url = add_query_arg( 'newsletter', 'subscribed', $redirect_url );
		}
		wp_redirect( $redirect_url );
		exit;
	}
}

/**
 * Handle Advertising Form
 */
add_action( 'admin_post_sme_advertising_form', 'sme_advertising_form_handler' );
add_action( 'admin_post_nopriv_sme_advertising_form', 'sme_advertising_form_handler' );
add_action( 'wp_ajax_sme_advertising_form', 'sme_advertising_form_handler' );
add_action( 'wp_ajax_nopriv_sme_advertising_form', 'sme_advertising_form_handler' );
function sme_advertising_form_handler() {
	// Verify nonce for security
	if ( ! isset( $_POST['sme_advertising_nonce'] ) || ! wp_verify_nonce( $_POST['sme_advertising_nonce'], 'sme_advertising_form' ) ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Security check failed. Please refresh the page and try again.' ) );
		} else {
			wp_die( 'Security check failed. Please refresh the page and try again.' );
		}
	}
	
	// Get and validate form data
	$full_name = isset( $_POST['fullName'] ) ? sanitize_text_field( $_POST['fullName'] ) : '';
	$company_name = isset( $_POST['companyName'] ) ? sanitize_text_field( $_POST['companyName'] ) : '';
	$work_email = isset( $_POST['workEmail'] ) ? sanitize_email( $_POST['workEmail'] ) : '';
	$opportunities = isset( $_POST['opportunities'] ) && is_array( $_POST['opportunities'] ) ? array_map( 'sanitize_text_field', $_POST['opportunities'] ) : array();
	$goals = isset( $_POST['goals'] ) ? sanitize_textarea_field( $_POST['goals'] ) : '';
	
	// Validate required fields
	if ( empty( $full_name ) || empty( $company_name ) || empty( $work_email ) || empty( $goals ) ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Please fill in all required fields.' ) );
		} else {
			wp_die( 'Please fill in all required fields.' );
		}
	}
	
	// Validate at least one opportunity selected
	if ( empty( $opportunities ) ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Please select at least one advertising opportunity.' ) );
		} else {
			wp_die( 'Please select at least one advertising opportunity.' );
		}
	}
	
	if ( ! is_email( $work_email ) ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
		} else {
			wp_die( 'Please enter a valid email address.' );
		}
	}
	
	// Rate limiting
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rate_limit_key = 'sme_advertising_rate_limit_' . md5( $ip );
	$rate_limit_count = get_transient( $rate_limit_key );
	
	if ( $rate_limit_count && $rate_limit_count >= 3 ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Too many requests. Please try again later.' ) );
		} else {
			wp_die( 'Too many requests. Please try again later.' );
		}
	}
	
	// Increment rate limit counter
	if ( $rate_limit_count ) {
		set_transient( $rate_limit_key, $rate_limit_count + 1, 3600 ); // 1 hour
	} else {
		set_transient( $rate_limit_key, 1, 3600 );
	}
	
	// Prepare email - Use custom email from theme customizer if set, otherwise use admin email
	$advertising_form_email = get_theme_mod( 'sme_advertising_form_email', '' );
	$admin_email = ! empty( $advertising_form_email ) ? $advertising_form_email : get_option( 'admin_email' );
	$email_subject = sprintf( '[%s] New Advertising Inquiry', get_bloginfo( 'name' ) );
	
	$opportunities_labels = array(
		'sponsored' => 'Sponsored Content / Branded Articles',
		'newsletter' => 'Newsletter Sponsorship',
		'display' => 'Display Advertising (Banners)',
		'custom' => 'Custom Campaigns',
	);
	
	$opportunities_text = array();
	foreach ( $opportunities as $opp ) {
		if ( isset( $opportunities_labels[ $opp ] ) ) {
			$opportunities_text[] = $opportunities_labels[ $opp ];
		} else {
			$opportunities_text[] = $opp;
		}
	}
	
	$email_message = "New advertising inquiry:\n\n";
	$email_message .= "Full Name: " . $full_name . "\n";
	$email_message .= "Company Name: " . $company_name . "\n";
	$email_message .= "Work Email: " . $work_email . "\n";
	$email_message .= "Interested Opportunities:\n";
	foreach ( $opportunities_text as $opp_text ) {
		$email_message .= "  - " . $opp_text . "\n";
	}
	$email_message .= "\nGoals:\n" . $goals . "\n\n";
	$email_message .= "Submitted on: " . current_time( 'mysql' ) . "\n";
	$email_message .= "IP Address: " . $ip . "\n";
	
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	$headers[] = 'From: ' . $full_name . ' <' . $work_email . '>';
	$headers[] = 'Reply-To: ' . $full_name . ' <' . $work_email . '>';
	
	// Save advertising form submission to database (for backup and logging)
	$submissions = get_option( 'sme_advertising_form_submissions', array() );
	$submission_data = array(
		'full_name'     => $full_name,
		'company_name'  => $company_name,
		'work_email'    => $work_email,
		'opportunities' => $opportunities,
		'goals'         => $goals,
		'date'          => current_time( 'mysql' ),
		'ip'            => $ip,
	);
	$submissions[] = $submission_data;
	// Keep only last 100 submissions to avoid database bloat
	if ( count( $submissions ) > 100 ) {
		$submissions = array_slice( $submissions, -100 );
	}
	update_option( 'sme_advertising_form_submissions', $submissions );
	
	// Try to send email to admin
	$sent = false;
	$email_error = '';
	
	// Only try to send email if wp_mail function exists and is configured
	if ( function_exists( 'wp_mail' ) ) {
		$sent = @wp_mail( $admin_email, $email_subject, $email_message, $headers );
		if ( ! $sent ) {
			$email_error = 'Email sending failed, but your inquiry has been saved.';
		}
	} else {
		$email_error = 'Email function not available, but your inquiry has been saved.';
	}
	
	// Try to send confirmation email to user (non-blocking)
	if ( function_exists( 'wp_mail' ) ) {
		$user_subject = sprintf( 'Thank you for your advertising inquiry - %s', get_bloginfo( 'name' ) );
		$user_message = "Hello " . $full_name . ",\n\n";
		$user_message .= "Thank you for your interest in advertising with " . get_bloginfo( 'name' ) . ". We have received your inquiry and will review it shortly.\n\n";
		$user_message .= "We'll get back to you within 2-3 business days to discuss how we can help you reach our audience.\n\n";
		$user_message .= "Your inquiry details:\n";
		$user_message .= "Company: " . $company_name . "\n";
		$user_message .= "Interested Opportunities:\n";
		foreach ( $opportunities_text as $opp_text ) {
			$user_message .= "  - " . $opp_text . "\n";
		}
		$user_message .= "\nBest regards,\n" . get_bloginfo( 'name' ) . " Team";
		
		$user_headers = array( 'Content-Type: text/plain; charset=UTF-8' );
		$user_headers[] = 'From: ' . get_bloginfo( 'name' ) . ' <' . $admin_email . '>';
		
		@wp_mail( $work_email, $user_subject, $user_message, $user_headers );
	}
	
	// Always return success since we saved the submission
	// Email sending is optional (may fail in local development)
	if ( wp_doing_ajax() ) {
		wp_send_json_success( array( 
			'message' => 'Thank you for your inquiry! We have received it and will get back to you within 2-3 business days.' 
		) );
	} else {
		wp_redirect( add_query_arg( 'advertising', 'success', wp_get_referer() ?: home_url( '/advertise-with-us' ) ) );
		exit;
	}
}

/**
 * Handle Article Submission Form
 */
add_action( 'admin_post_submit_article', 'sme_article_submission_handler' );
add_action( 'admin_post_nopriv_submit_article', 'sme_article_submission_handler' );
add_action( 'wp_ajax_sme_submit_article', 'sme_article_submission_handler' );
add_action( 'wp_ajax_nopriv_sme_submit_article', 'sme_article_submission_handler' );
function sme_article_submission_handler() {
	// Verify nonce for security
	if ( ! isset( $_POST['article_nonce'] ) || ! wp_verify_nonce( $_POST['article_nonce'], 'submit_article' ) ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Security check failed. Please refresh the page and try again.' ) );
		} else {
			wp_die( 'Security check failed. Please refresh the page and try again.' );
		}
	}
	
	// Get and validate form data
	$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
	$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
	$title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
	$abstract = isset( $_POST['abstract'] ) ? sanitize_textarea_field( $_POST['abstract'] ) : '';
	$linkedin = isset( $_POST['linkedin'] ) ? esc_url_raw( $_POST['linkedin'] ) : '';
	$bio = isset( $_POST['bio'] ) ? sanitize_textarea_field( $_POST['bio'] ) : '';
	
	// Validate required fields
	if ( empty( $name ) || empty( $email ) || empty( $title ) || empty( $abstract ) || empty( $bio ) ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Please fill in all required fields.' ) );
		} else {
			wp_die( 'Please fill in all required fields.' );
		}
	}
	
	if ( ! is_email( $email ) ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
		} else {
			wp_die( 'Please enter a valid email address.' );
		}
	}
	
	// Rate limiting
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rate_limit_key = 'sme_article_rate_limit_' . md5( $ip );
	$rate_limit_count = get_transient( $rate_limit_key );
	
	if ( $rate_limit_count && $rate_limit_count >= 3 ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => 'Too many requests. Please try again later.' ) );
		} else {
			wp_die( 'Too many requests. Please try again later.' );
		}
	}
	
	// Increment rate limit counter
	if ( $rate_limit_count ) {
		set_transient( $rate_limit_key, $rate_limit_count + 1, 3600 ); // 1 hour
	} else {
		set_transient( $rate_limit_key, 1, 3600 );
	}
	
	// Prepare email - Use admin email or custom email from theme customizer
	$admin_email = get_option( 'admin_email' );
	$email_subject = sprintf( '[%s] New Article Submission: %s', get_bloginfo( 'name' ), $title );
	
	$email_message = "New article submission:\n\n";
	$email_message .= "Name: " . $name . "\n";
	$email_message .= "Email: " . $email . "\n";
	$email_message .= "Proposed Article Title: " . $title . "\n";
	$email_message .= "LinkedIn/Portfolio: " . ( ! empty( $linkedin ) ? $linkedin : 'Not provided' ) . "\n";
	$email_message .= "Author Bio:\n" . $bio . "\n\n";
	$email_message .= "Article Abstract/Content:\n" . $abstract . "\n\n";
	$email_message .= "Submitted on: " . current_time( 'mysql' ) . "\n";
	$email_message .= "IP Address: " . $ip . "\n";
	
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	$headers[] = 'From: ' . $name . ' <' . $email . '>';
	$headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
	
	// Save article submission to database (for backup and logging)
	$submissions = get_option( 'sme_article_submissions', array() );
	$submission_data = array(
		'name'     => $name,
		'email'    => $email,
		'title'    => $title,
		'abstract' => $abstract,
		'linkedin' => $linkedin,
		'bio'      => $bio,
		'date'     => current_time( 'mysql' ),
		'ip'       => $ip,
	);
	$submissions[] = $submission_data;
	// Keep only last 100 submissions to avoid database bloat
	if ( count( $submissions ) > 100 ) {
		$submissions = array_slice( $submissions, -100 );
	}
	update_option( 'sme_article_submissions', $submissions );
	
	// Try to send email to admin
	$sent = false;
	$email_error = '';
	
	// Only try to send email if wp_mail function exists and is configured
	if ( function_exists( 'wp_mail' ) ) {
		$sent = @wp_mail( $admin_email, $email_subject, $email_message, $headers );
		if ( ! $sent ) {
			$email_error = 'Email sending failed, but your submission has been saved.';
		}
	} else {
		$email_error = 'Email function not available, but your submission has been saved.';
	}
	
	// Try to send confirmation email to user (non-blocking)
	if ( function_exists( 'wp_mail' ) ) {
		$user_subject = sprintf( 'Thank you for your article submission to %s', get_bloginfo( 'name' ) );
		$user_message = "Hello " . $name . ",\n\n";
		$user_message .= "Thank you for submitting your article proposal to us. We have received your submission and will review it carefully.\n\n";
		$user_message .= "Your submission details:\n";
		$user_message .= "Article Title: " . $title . "\n";
		$user_message .= "Submitted on: " . current_time( 'mysql' ) . "\n\n";
		$user_message .= "We will get back to you within two weeks if we're interested in publishing your work.\n\n";
		$user_message .= "Best regards,\n" . get_bloginfo( 'name' ) . " Team";
		
		$user_headers = array( 'Content-Type: text/plain; charset=UTF-8' );
		$user_headers[] = 'From: ' . get_bloginfo( 'name' ) . ' <' . $admin_email . '>';
		
		@wp_mail( $email, $user_subject, $user_message, $user_headers );
	}
	
	// Handle newsletter subscription if checkbox is checked
	if ( isset( $_POST['newsletter_subscribe'] ) && $_POST['newsletter_subscribe'] === '1' ) {
		$subscribers = get_option( 'sme_newsletter_subscribers', array() );
		if ( ! in_array( $email, $subscribers, true ) ) {
			$subscribers[] = $email;
			update_option( 'sme_newsletter_subscribers', $subscribers );
			
			// Send welcome email for newsletter subscription
			$newsletter_email = get_theme_mod( 'sme_newsletter_notification_email', '' );
			if ( empty( $newsletter_email ) ) {
				$newsletter_email = get_option( 'admin_email' );
			}
			$site_name = get_bloginfo( 'name' );
			
			$subscriber_subject = 'Welcome to ' . $site_name . ' Newsletter!';
			$subscriber_message = "Hello " . $name . ",\n\n";
			$subscriber_message .= "Thank you for subscribing to our newsletter! You'll now receive the latest business insights, tips, and updates directly in your inbox.\n\n";
			$subscriber_message .= "We're excited to share valuable content with you.\n\n";
			$subscriber_message .= "Best regards,\n" . $site_name . " Team";
			
			$subscriber_headers = array( 'Content-Type: text/plain; charset=UTF-8' );
			$subscriber_headers[] = 'From: ' . $site_name . ' <' . $newsletter_email . '>';
			$subscriber_headers[] = 'Reply-To: ' . $site_name . ' <' . $newsletter_email . '>';
			
			if ( function_exists( 'wp_mail' ) ) {
				@wp_mail( $email, $subscriber_subject, $subscriber_message, $subscriber_headers );
				
				// Send notification to admin
				$admin_subject = 'New Newsletter Subscription - ' . $site_name;
				$admin_message = "A new subscriber has joined the newsletter:\n\n";
				$admin_message .= "Name: " . $name . "\n";
				$admin_message .= "Email: " . $email . "\n";
				$admin_message .= "Date: " . current_time( 'mysql' ) . "\n";
				$admin_message .= "Total Subscribers: " . count( $subscribers ) . "\n";
				
				$admin_headers = array( 'Content-Type: text/plain; charset=UTF-8' );
				$admin_headers[] = 'From: ' . $site_name . ' <' . $newsletter_email . '>';
				$admin_headers[] = 'Reply-To: ' . $site_name . ' <' . $newsletter_email . '>';
				
				@wp_mail( $newsletter_email, $admin_subject, $admin_message, $admin_headers );
			}
		}
	}
	
	// Always return success since we saved the submission
	// Email sending is optional (may fail in local development)
	if ( wp_doing_ajax() ) {
		$success_message = 'Thank you for your submission! We have received it and will review it within two weeks.';
		if ( isset( $_POST['newsletter_subscribe'] ) && $_POST['newsletter_subscribe'] === '1' ) {
			$success_message .= ' You have also been subscribed to our newsletter.';
		}
		wp_send_json_success( array( 
			'message' => $success_message
		) );
	} else {
		$redirect_url = add_query_arg( 'submitted', 'success', wp_get_referer() ?: home_url( '/become-contributor' ) );
		if ( isset( $_POST['newsletter_subscribe'] ) && $_POST['newsletter_subscribe'] === '1' ) {
			$redirect_url = add_query_arg( 'newsletter', 'subscribed', $redirect_url );
		}
		wp_redirect( $redirect_url );
		exit;
	}
}

/**
 * Update featured images for specific posts
 * This function updates images for posts and prevents re-downloading same images
 */
add_action( 'admin_init', 'sme_update_specific_posts_images' );
function sme_update_specific_posts_images() {
	if ( ! isset( $_GET['sme_update_images'] ) || $_GET['sme_update_images'] !== '1' ) {
		return;
	}
	
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( 'You do not have permission to perform this action.' );
	}
	
	// Verify nonce
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'sme_update_images' ) ) {
		wp_die( 'Security check failed.' );
	}
	
	// Posts to update with their slugs and new image URLs
	$posts_to_update = array(
		'understanding-cash-flow-the-lifeblood-of-your-business' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=1200&h=630&fit=crop',
		'swot-analysis-evaluating-your-business-strengths-and-weaknesses' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&h=630&fit=crop',
		'legal-considerations-for-small-business-owners' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=1200&h=630&fit=crop',
		'content-marketing-creating-content-that-converts' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1200&h=630&fit=crop',
		'the-roi-of-ai-measuring-success-in-small-business' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=1200&h=630&fit=crop',
		'local-seo-getting-found-in-your-community' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&h=630&fit=crop',
		'innovation-and-adaptability-staying-competitive' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&h=630&fit=crop',
	);
	
	$updated_count = 0;
	$not_found = array();
	
	foreach ( $posts_to_update as $slug => $image_url ) {
		$post = get_page_by_path( $slug, OBJECT, 'post' );
		
		if ( ! $post ) {
			$not_found[] = $slug;
			continue;
		}
		
		$post_id = $post->ID;
		$post_title = get_the_title( $post_id );
		
		// Always delete old thumbnail first (even if same URL, to force update)
		if ( has_post_thumbnail( $post_id ) ) {
			$old_thumbnail_id = get_post_thumbnail_id( $post_id );
			delete_post_thumbnail( $post_id );
			// Delete the attachment file
			wp_delete_attachment( $old_thumbnail_id, true );
		}
		
		// Check if image already exists in media library by URL
		$existing_attachment_id = sme_get_attachment_by_url( $image_url );
		if ( $existing_attachment_id ) {
			// Use existing attachment instead of downloading again
			set_post_thumbnail( $post_id, $existing_attachment_id );
			update_post_meta( $existing_attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $post_title ) );
			$updated_count++;
		} else {
			// Set new featured image with force_update = true
			if ( sme_set_featured_image_from_url( $post_id, $image_url, $post_title, true ) ) {
				$updated_count++;
			}
		}
	}
	
	// Redirect with success message
	$redirect_url = add_query_arg( array(
		'page' => 'sme-content-manager',
		'images_updated' => $updated_count,
		'not_found' => count( $not_found ),
	), admin_url( 'admin.php' ) );
	
	wp_redirect( $redirect_url );
	exit;
}

/**
 * Add admin notice for image update link
 */
add_action( 'admin_notices', 'sme_image_update_notice' );
function sme_image_update_notice() {
	$screen = get_current_screen();
	if ( ! $screen || $screen->id !== 'toplevel_page_sme-content-manager' ) {
		return;
	}
	
	if ( isset( $_GET['images_updated'] ) ) {
		$updated = intval( $_GET['images_updated'] );
		$not_found = isset( $_GET['not_found'] ) ? intval( $_GET['not_found'] ) : 0;
		
		echo '<div class="notice notice-success is-dismissible">';
		echo '<p><strong>Images Updated:</strong> ' . esc_html( $updated ) . ' posts updated successfully.';
		if ( $not_found > 0 ) {
			echo ' ' . esc_html( $not_found ) . ' posts not found.';
		}
		echo '</p></div>';
	}
	
	// Show update link
	$update_url = wp_nonce_url( 
		add_query_arg( 'sme_update_images', '1', admin_url( 'admin.php' ) ),
		'sme_update_images'
	);
	
	echo '<div class="notice notice-info is-dismissible">';
	echo '<p><strong>Update Featured Images:</strong> ';
	echo '<a href="' . esc_url( $update_url ) . '" class="button button-primary">Update Images for Specific Posts</a>';
	echo '</p></div>';
}

/**
 * Custom comment callback function
 * 
 * This function is used to render individual comments in the comments list.
 * It's defined here to prevent redeclaration errors if comments.php is loaded multiple times.
 * 
 * @param WP_Comment $comment The comment object.
 * @param array      $args    An array of arguments.
 * @param int        $depth   The depth of the comment.
 */
if ( ! function_exists( 'sme_comment_callback' ) ) {
	function sme_comment_callback( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		?>
		<li <?php comment_class( 'comment-item' ); ?> id="comment-<?php comment_ID(); ?>">
			<div class="comment-header">
				<?php echo get_avatar( $comment, 50, '', '', array( 'class' => 'comment-avatar' ) ); ?>
				<div>
					<div class="comment-author">
						<?php echo get_comment_author_link(); ?>
					</div>
					<div class="comment-date">
						<?php
						printf(
							/* translators: 1: date, 2: time */
							__( '%1$s at %2$s', 'sme-insights' ),
							get_comment_date(),
							get_comment_time()
						);
						?>
					</div>
				</div>
			</div>
			<div class="comment-text">
				<?php comment_text(); ?>
			</div>
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'sme-insights' ); ?></p>
			<?php endif; ?>
			<?php
			comment_reply_link( array_merge( $args, array(
				'depth'     => $depth,
				'max_depth' => $args['max_depth'],
			) ) );
			?>
		</li>
		<?php
	}
}


