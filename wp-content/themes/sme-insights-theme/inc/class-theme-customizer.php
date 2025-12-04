<?php
/**
 * Theme Customizer
 * Allows users to customize colors, fonts, and design from WordPress Dashboard
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Theme_Customizer {
	
	/**
	 * Instance
	 */
	private static $instance = null;
	
	/**
	 * Get instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'customize_register', array( $this, 'register_customizer_settings' ) );
		add_action( 'wp_head', array( $this, 'output_customizer_css' ), 999 );
		add_action( 'customize_preview_init', array( $this, 'enqueue_customizer_preview' ) );
	}
	
	/**
	 * Enqueue customizer preview script
	 */
	public function enqueue_customizer_preview() {
		wp_enqueue_script(
			'sme-customizer-preview',
			SME_THEME_ASSETS . '/js/customizer-preview.js',
			array( 'customize-preview', 'jquery' ),
			SME_THEME_VERSION,
			true
		);
	}
	
	/**
	 * Register Customizer settings
	 */
	public function register_customizer_settings( $wp_customize ) {
		
		// Add Theme Options Panel
		$wp_customize->add_panel( 'sme_theme_options', array(
			'title'       => __( 'SME Insights Theme Options', 'sme-insights' ),
			'description' => __( 'Customize your theme colors, fonts, and design settings', 'sme-insights' ),
			'priority'    => 30,
		) );
		
		// Colors Section
		$wp_customize->add_section( 'sme_colors', array(
			'title'       => __( 'Colors', 'sme-insights' ),
			'description' => __( 'Customize your theme colors', 'sme-insights' ),
			'panel'       => 'sme_theme_options',
			'priority'    => 10,
		) );
		
		// Accent Primary Color
		$wp_customize->add_setting( 'sme_accent_primary', array(
			'default'           => '#1a365d',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sme_accent_primary', array(
			'label'       => __( 'Primary Accent Color', 'sme-insights' ),
			'description' => __( 'Main brand color (used in headers, buttons, links)', 'sme-insights' ),
			'section'     => 'sme_colors',
			'settings'    => 'sme_accent_primary',
		) ) );
		
		// Accent Secondary Color
		$wp_customize->add_setting( 'sme_accent_secondary', array(
			'default'           => '#2563eb',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sme_accent_secondary', array(
			'label'       => __( 'Secondary Accent Color', 'sme-insights' ),
			'description' => __( 'Secondary brand color (used in CTAs, highlights)', 'sme-insights' ),
			'section'     => 'sme_colors',
			'settings'    => 'sme_accent_secondary',
		) ) );
		
		// Accent Hover Color
		$wp_customize->add_setting( 'sme_accent_hover', array(
			'default'           => '#1e40af',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sme_accent_hover', array(
			'label'       => __( 'Hover Color', 'sme-insights' ),
			'description' => __( 'Color for hover states on buttons and links', 'sme-insights' ),
			'section'     => 'sme_colors',
			'settings'    => 'sme_accent_hover',
		) ) );
		
		// Text Primary Color
		$wp_customize->add_setting( 'sme_text_primary', array(
			'default'           => '#1a202c',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sme_text_primary', array(
			'label'       => __( 'Primary Text Color', 'sme-insights' ),
			'description' => __( 'Main text color for headings and body', 'sme-insights' ),
			'section'     => 'sme_colors',
			'settings'    => 'sme_text_primary',
		) ) );
		
		// Text Secondary Color
		$wp_customize->add_setting( 'sme_text_secondary', array(
			'default'           => '#4a5568',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sme_text_secondary', array(
			'label'       => __( 'Secondary Text Color', 'sme-insights' ),
			'description' => __( 'Secondary text color for descriptions and meta', 'sme-insights' ),
			'section'     => 'sme_colors',
			'settings'    => 'sme_text_secondary',
		) ) );
		
		// Background Primary Color
		$wp_customize->add_setting( 'sme_bg_primary', array(
			'default'           => '#ffffff',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sme_bg_primary', array(
			'label'       => __( 'Primary Background', 'sme-insights' ),
			'description' => __( 'Main background color', 'sme-insights' ),
			'section'     => 'sme_colors',
			'settings'    => 'sme_bg_primary',
		) ) );
		
		// Background Secondary Color
		$wp_customize->add_setting( 'sme_bg_secondary', array(
			'default'           => '#f7fafc',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sme_bg_secondary', array(
			'label'       => __( 'Secondary Background', 'sme-insights' ),
			'description' => __( 'Secondary background color for cards and sections', 'sme-insights' ),
			'section'     => 'sme_colors',
			'settings'    => 'sme_bg_secondary',
		) ) );
		
		// Border Color
		$wp_customize->add_setting( 'sme_border_color', array(
			'default'           => '#e2e8f0',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sme_border_color', array(
			'label'       => __( 'Border Color', 'sme-insights' ),
			'description' => __( 'Color for borders and dividers', 'sme-insights' ),
			'section'     => 'sme_colors',
			'settings'    => 'sme_border_color',
		) ) );
		
		// Typography Section
		$wp_customize->add_section( 'sme_typography', array(
			'title'       => __( 'Typography', 'sme-insights' ),
			'description' => __( 'Customize fonts and typography', 'sme-insights' ),
			'panel'       => 'sme_theme_options',
			'priority'    => 20,
		) );
		
		// Font Family
		$wp_customize->add_setting( 'sme_font_family', array(
			'default'           => '-apple-system, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial, sans-serif',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'sme_font_family', array(
			'label'       => __( 'Font Family', 'sme-insights' ),
			'description' => __( 'Enter font family (e.g., "Arial, sans-serif" or "Roboto, sans-serif")', 'sme-insights' ),
			'section'     => 'sme_typography',
			'type'        => 'text',
		) );
		
		// Base Font Size
		$wp_customize->add_setting( 'sme_font_size', array(
			'default'           => '16',
			'sanitize_callback' => 'absint',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'sme_font_size', array(
			'label'       => __( 'Base Font Size (px)', 'sme-insights' ),
			'description' => __( 'Base font size in pixels', 'sme-insights' ),
			'section'     => 'sme_typography',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 12,
				'max'  => 24,
				'step' => 1,
			),
		) );
		
		// Line Height
		$wp_customize->add_setting( 'sme_line_height', array(
			'default'           => '1.6',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'sme_line_height', array(
			'label'       => __( 'Line Height', 'sme-insights' ),
			'description' => __( 'Line height (e.g., 1.6, 1.8)', 'sme-insights' ),
			'section'     => 'sme_typography',
			'type'        => 'text',
		) );
		
		// Layout Section
		$wp_customize->add_section( 'sme_layout', array(
			'title'       => __( 'Layout & Spacing', 'sme-insights' ),
			'description' => __( 'Customize layout and spacing', 'sme-insights' ),
			'panel'       => 'sme_theme_options',
			'priority'    => 30,
		) );
		
		// Container Max Width
		$wp_customize->add_setting( 'sme_container_width', array(
			'default'           => '1200',
			'sanitize_callback' => 'absint',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'sme_container_width', array(
			'label'       => __( 'Container Max Width (px)', 'sme-insights' ),
			'description' => __( 'Maximum width of main container', 'sme-insights' ),
			'section'     => 'sme_layout',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 800,
				'max'  => 1920,
				'step' => 20,
			),
		) );
		
		// Section Padding
		$wp_customize->add_setting( 'sme_section_padding', array(
			'default'           => '60',
			'sanitize_callback' => 'absint',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'sme_section_padding', array(
			'label'       => __( 'Section Padding (px)', 'sme-insights' ),
			'description' => __( 'Default padding for sections', 'sme-insights' ),
			'section'     => 'sme_layout',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 20,
				'max'  => 120,
				'step' => 10,
			),
		) );
		
		// Border Radius
		$wp_customize->add_setting( 'sme_border_radius', array(
			'default'           => '8',
			'sanitize_callback' => 'absint',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'sme_border_radius', array(
			'label'       => __( 'Border Radius (px)', 'sme-insights' ),
			'description' => __( 'Default border radius for buttons and cards', 'sme-insights' ),
			'section'     => 'sme_layout',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 2,
			),
		) );
		
		// Social Media Section - Add both inside panel and as standalone section
		$wp_customize->add_section( 'sme_social_media', array(
			'title'       => __( 'Social Media', 'sme-insights' ),
			'description' => __( 'Add your social media links. These links will appear in the header, footer, and contact page.', 'sme-insights' ),
			'panel'       => 'sme_theme_options',
			'priority'    => 40,
			'capability'  => 'edit_theme_options',
		) );
		
		// Facebook
		$wp_customize->add_setting( 'social_facebook', array(
			'default'           => 'https://facebook.com/smeinsights',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_facebook', array(
			'label'       => __( 'Facebook URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// Twitter
		$wp_customize->add_setting( 'social_twitter', array(
			'default'           => 'https://twitter.com/smeinsights',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_twitter', array(
			'label'       => __( 'Twitter URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// LinkedIn
		$wp_customize->add_setting( 'social_linkedin', array(
			'default'           => 'https://linkedin.com/company/smeinsights',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_linkedin', array(
			'label'       => __( 'LinkedIn URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// YouTube
		$wp_customize->add_setting( 'social_youtube', array(
			'default'           => 'https://youtube.com/@smeinsights',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_youtube', array(
			'label'       => __( 'YouTube URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// Instagram
		$wp_customize->add_setting( 'social_instagram', array(
			'default'           => 'https://instagram.com/smeinsights',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_instagram', array(
			'label'       => __( 'Instagram URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// TikTok
		$wp_customize->add_setting( 'social_tiktok', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_tiktok', array(
			'label'       => __( 'TikTok URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// Pinterest
		$wp_customize->add_setting( 'social_pinterest', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_pinterest', array(
			'label'       => __( 'Pinterest URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// Snapchat
		$wp_customize->add_setting( 'social_snapchat', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_snapchat', array(
			'label'       => __( 'Snapchat URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// WhatsApp
		$wp_customize->add_setting( 'social_whatsapp', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_whatsapp', array(
			'label'       => __( 'WhatsApp URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// Telegram
		$wp_customize->add_setting( 'social_telegram', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_telegram', array(
			'label'       => __( 'Telegram URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// Discord
		$wp_customize->add_setting( 'social_discord', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_discord', array(
			'label'       => __( 'Discord URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// Reddit
		$wp_customize->add_setting( 'social_reddit', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_reddit', array(
			'label'       => __( 'Reddit URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// Medium
		$wp_customize->add_setting( 'social_medium', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_medium', array(
			'label'       => __( 'Medium URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// GitHub
		$wp_customize->add_setting( 'social_github', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_github', array(
			'label'       => __( 'GitHub URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// Behance
		$wp_customize->add_setting( 'social_behance', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_behance', array(
			'label'       => __( 'Behance URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// Dribbble
		$wp_customize->add_setting( 'social_dribbble', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'social_dribbble', array(
			'label'       => __( 'Dribbble URL', 'sme-insights' ),
			'section'     => 'sme_social_media',
			'type'        => 'url',
		) );
		
		// Header Section - Edit Header Content
		$wp_customize->add_section( 'sme_header', array(
			'title'       => __( 'Header Settings', 'sme-insights' ),
			'description' => __( 'Customize your header. Changes appear in real-time preview.', 'sme-insights' ),
			'priority'    => 25,
		) );
		
		// Header Logo Text
		$wp_customize->add_setting( 'header_logo_text', array(
			'default'           => 'SME INSIGHTS',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'header_logo_text', array(
			'label'       => __( 'Logo Text', 'sme-insights' ),
			'description' => __( 'Change the "SME INSIGHTS" logo text', 'sme-insights' ),
			'section'     => 'sme_header',
			'type'        => 'text',
		) );
		
		// Top Bar - Become a Contributor Text
		$wp_customize->add_setting( 'header_top_bar_text', array(
			'default'           => 'Become a Contributor',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'header_top_bar_text', array(
			'label'       => __( 'Top Bar Link Text', 'sme-insights' ),
			'description' => __( 'Text for the "Become a Contributor" link in top bar', 'sme-insights' ),
			'section'     => 'sme_header',
			'type'        => 'text',
		) );
		
		// Search Placeholder Text
		$wp_customize->add_setting( 'header_search_placeholder', array(
			'default'           => 'Search articles...',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'header_search_placeholder', array(
			'label'       => __( 'Search Placeholder Text', 'sme-insights' ),
			'description' => __( 'Placeholder text in the search input field', 'sme-insights' ),
			'section'     => 'sme_header',
			'type'        => 'text',
		) );
		
		// Subscribe Button Text
		$wp_customize->add_setting( 'header_subscribe_text', array(
			'default'           => 'SUBSCRIBE',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'header_subscribe_text', array(
			'label'       => __( 'Subscribe Button Text', 'sme-insights' ),
			'description' => __( 'Text for the subscribe button in header', 'sme-insights' ),
			'section'     => 'sme_header',
			'type'        => 'text',
		) );
		
		// Show Top Bar
		$wp_customize->add_setting( 'header_show_top_bar', array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'header_show_top_bar', array(
			'label'       => __( 'Show Top Bar', 'sme-insights' ),
			'description' => __( 'Display the top bar with social icons', 'sme-insights' ),
			'section'     => 'sme_header',
			'type'        => 'checkbox',
		) );
		
		// Show Niche Topics
		$wp_customize->add_setting( 'header_show_niche_topics', array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'header_show_niche_topics', array(
			'label'       => __( 'Show Niche Topics Section', 'sme-insights' ),
			'description' => __( 'Display the "NICHE TOPICS" section', 'sme-insights' ),
			'section'     => 'sme_header',
			'type'        => 'checkbox',
		) );
		
		// Show Breaking News
		$wp_customize->add_setting( 'header_show_breaking_news', array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'header_show_breaking_news', array(
			'label'       => __( 'Show Breaking News Bar', 'sme-insights' ),
			'description' => __( 'Display the breaking news ticker slider', 'sme-insights' ),
			'section'     => 'sme_header',
			'type'        => 'checkbox',
		) );
		
		// Social Media Section - Standalone (outside panel, next to Header and Footer)
		$wp_customize->add_section( 'sme_social_media_standalone', array(
			'title'       => __( 'Social Media Links', 'sme-insights' ),
			'description' => __( 'Add your social media links. These links will appear in the header, footer, and contact page.', 'sme-insights' ),
			'priority'    => 27,
			'capability'  => 'edit_theme_options',
		) );
		
		// Add social media controls to standalone section (reuse existing settings from panel section)
		// Facebook
		$wp_customize->add_control( 'social_facebook_standalone', array(
			'label'       => __( 'Facebook URL', 'sme-insights' ),
			'section'     => 'sme_social_media_standalone',
			'type'        => 'url',
			'settings'    => 'social_facebook',
		) );
		
		// Twitter
		$wp_customize->add_control( 'social_twitter_standalone', array(
			'label'       => __( 'Twitter URL', 'sme-insights' ),
			'section'     => 'sme_social_media_standalone',
			'type'        => 'url',
			'settings'    => 'social_twitter',
		) );
		
		// LinkedIn
		$wp_customize->add_control( 'social_linkedin_standalone', array(
			'label'       => __( 'LinkedIn URL', 'sme-insights' ),
			'section'     => 'sme_social_media_standalone',
			'type'        => 'url',
			'settings'    => 'social_linkedin',
		) );
		
		// YouTube
		$wp_customize->add_control( 'social_youtube_standalone', array(
			'label'       => __( 'YouTube URL', 'sme-insights' ),
			'section'     => 'sme_social_media_standalone',
			'type'        => 'url',
			'settings'    => 'social_youtube',
		) );
		
		// Instagram
		$wp_customize->add_control( 'social_instagram_standalone', array(
			'label'       => __( 'Instagram URL', 'sme-insights' ),
			'section'     => 'sme_social_media_standalone',
			'type'        => 'url',
			'settings'    => 'social_instagram',
		) );
		
		// Footer Section - Edit Footer Content
		$wp_customize->add_section( 'sme_footer', array(
			'title'       => __( 'Footer Settings', 'sme-insights' ),
			'description' => __( 'Customize your footer. Changes appear in real-time preview.', 'sme-insights' ),
			'priority'    => 28,
		) );
		
		// Footer Company Name
		$wp_customize->add_setting( 'footer_company_name', array(
			'default'           => 'SME INSIGHTS',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'footer_company_name', array(
			'label'       => __( 'Company Name', 'sme-insights' ),
			'description' => __( 'Company name displayed in footer (Column 1 heading)', 'sme-insights' ),
			'section'     => 'sme_footer',
			'type'        => 'text',
		) );
		
		// Footer Column 1 - Links (About Us, Our Team, etc.)
		$wp_customize->add_setting( 'footer_column1_about', array(
			'default'           => 'About Us',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'footer_column1_about', array(
			'label'       => __( 'Footer Column 1 - About Us Link Text', 'sme-insights' ),
			'section'     => 'sme_footer',
			'type'        => 'text',
		) );
		
		$wp_customize->add_setting( 'footer_column1_team', array(
			'default'           => 'Our Team',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'footer_column1_team', array(
			'label'       => __( 'Footer Column 1 - Our Team Link Text', 'sme-insights' ),
			'section'     => 'sme_footer',
			'type'        => 'text',
		) );
		
		$wp_customize->add_setting( 'footer_column1_contributor', array(
			'default'           => 'Become a Contributor',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'footer_column1_contributor', array(
			'label'       => __( 'Footer Column 1 - Become a Contributor Link Text', 'sme-insights' ),
			'section'     => 'sme_footer',
			'type'        => 'text',
		) );
		
		$wp_customize->add_setting( 'footer_column1_contact', array(
			'default'           => 'Contact Us',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'footer_column1_contact', array(
			'label'       => __( 'Footer Column 1 - Contact Us Link Text', 'sme-insights' ),
			'section'     => 'sme_footer',
			'type'        => 'text',
		) );
		
		// Footer Bottom Copyright Text
		$wp_customize->add_setting( 'footer_copyright_text', array(
			'default'           => 'Copyright © {year} {site_name}. | Privacy Policy | Terms of Service | Your trusted source for Small Business News & Insights.',
			'sanitize_callback' => 'wp_kses_post',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'footer_copyright_text', array(
			'label'       => __( 'Copyright Text', 'sme-insights' ),
			'description' => __( 'Use {year} for current year and {site_name} for site name', 'sme-insights' ),
			'section'     => 'sme_footer',
			'type'        => 'textarea',
		) );
		
		// Contact Form Section
		$wp_customize->add_section( 'sme_contact_form', array(
			'title'       => __( 'Contact Form', 'sme-insights' ),
			'description' => __( 'Configure contact form email settings', 'sme-insights' ),
			'panel'       => 'sme_theme_options',
			'priority'    => 50,
		) );
		
		// Contact Form Email
		$wp_customize->add_setting( 'sme_contact_form_email', array(
			'default'           => get_option( 'admin_email' ),
			'sanitize_callback' => 'sanitize_email',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'sme_contact_form_email', array(
			'label'       => __( 'Contact Form Email', 'sme-insights' ),
			'description' => __( 'Email address where contact form submissions will be sent. Leave empty to use the default admin email.', 'sme-insights' ),
			'section'     => 'sme_contact_form',
			'type'        => 'email',
		) );
		
		// Newsletter Notification Email
		$wp_customize->add_setting( 'sme_newsletter_notification_email', array(
			'default'           => get_option( 'admin_email' ),
			'sanitize_callback' => 'sanitize_email',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'sme_newsletter_notification_email', array(
			'label'       => __( 'Newsletter Notification Email', 'sme-insights' ),
			'description' => __( 'Email address where newsletter subscription notifications will be sent. Leave empty to use the default admin email.', 'sme-insights' ),
			'section'     => 'sme_contact_form',
			'type'        => 'email',
		) );
		
		// Show Footer Columns
		$wp_customize->add_setting( 'footer_show_columns', array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'footer_show_columns', array(
			'label'       => __( 'Show Footer Columns', 'sme-insights' ),
			'description' => __( 'Display footer columns (SME INSIGHTS, Our Topics, Legal, Connect)', 'sme-insights' ),
			'section'     => 'sme_footer',
			'type'        => 'checkbox',
		) );
		
		// Coming Soon settings are only available in Dashboard: SME Insights > Coming Soon Settings
	}
	
	/**
	 * Output customizer CSS
	 */
	public function output_customizer_css() {
		$accent_primary = get_theme_mod( 'sme_accent_primary', '#1a365d' );
		$accent_secondary = get_theme_mod( 'sme_accent_secondary', '#2563eb' );
		$accent_hover = get_theme_mod( 'sme_accent_hover', '#1e40af' );
		$text_primary = get_theme_mod( 'sme_text_primary', '#1a202c' );
		$text_secondary = get_theme_mod( 'sme_text_secondary', '#4a5568' );
		$bg_primary = get_theme_mod( 'sme_bg_primary', '#ffffff' );
		$bg_secondary = get_theme_mod( 'sme_bg_secondary', '#f7fafc' );
		$border_color = get_theme_mod( 'sme_border_color', '#e2e8f0' );
		$font_family = get_theme_mod( 'sme_font_family', '-apple-system, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial, sans-serif' );
		$font_size = get_theme_mod( 'sme_font_size', '16' );
		$line_height = get_theme_mod( 'sme_line_height', '1.6' );
		$container_width = get_theme_mod( 'sme_container_width', '1200' );
		$section_padding = get_theme_mod( 'sme_section_padding', '60' );
		$border_radius = get_theme_mod( 'sme_border_radius', '8' );
		
		?>
		<style id="sme-customizer-css">
		:root {
			--accent-primary: <?php echo esc_attr( $accent_primary ); ?>;
			--accent-secondary: <?php echo esc_attr( $accent_secondary ); ?>;
			--accent-hover: <?php echo esc_attr( $accent_hover ); ?>;
			--text-primary: <?php echo esc_attr( $text_primary ); ?>;
			--text-secondary: <?php echo esc_attr( $text_secondary ); ?>;
			--bg-primary: <?php echo esc_attr( $bg_primary ); ?>;
			--bg-secondary: <?php echo esc_attr( $bg_secondary ); ?>;
			--border-color: <?php echo esc_attr( $border_color ); ?>;
			--breaking-gradient: linear-gradient(135deg, <?php echo esc_attr( $accent_primary ); ?> 0%, <?php echo esc_attr( $accent_secondary ); ?> 100%);
		}
		body {
			font-family: <?php echo esc_attr( $font_family ); ?>;
			font-size: <?php echo esc_attr( $font_size ); ?>px;
			line-height: <?php echo esc_attr( $line_height ); ?>;
		}
		.container,
		.container-inner {
			max-width: <?php echo esc_attr( $container_width ); ?>px;
		}
		section,
		.section {
			padding: <?php echo esc_attr( $section_padding ); ?>px 0;
		}
		button,
		.btn,
		.button,
		.card {
			border-radius: <?php echo esc_attr( $border_radius ); ?>px;
		}
		</style>
		<?php
	}
}

