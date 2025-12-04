<?php
/**
 * Advanced Page Builder Blocks
 * Enhanced Gutenberg blocks with drag & drop and style customization
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Page_Builder_Blocks {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Register blocks with multiple hooks to ensure they're always registered
		// Use plugins_loaded for theme-independent registration
		add_action( 'plugins_loaded', array( $this, 'register_blocks' ), 5 );
		add_action( 'init', array( $this, 'register_blocks' ), 20 );
		add_action( 'after_setup_theme', array( $this, 'register_blocks' ), 20 );
		
		// Editor assets
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		
		// Ensure blocks are registered even after theme switch
		add_action( 'switch_theme', array( $this, 'register_blocks' ) );
		
		// Re-register on theme activation
		add_action( 'after_switch_theme', array( $this, 'register_blocks' ) );
	}
	
	/**
	 * Register all page builder blocks
	 */
	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		
		// Prevent duplicate registrations
		static $registered = false;
		if ( $registered ) {
			return;
		}
		$registered = true;
		
		$this->register_text_block();
		$this->register_image_block();
		$this->register_button_block();
		$this->register_columns_block();
		$this->register_hero_block();
		$this->register_spacer_block();
		$this->register_divider_block();
		$this->register_html_block();
	}
	
	/**
	 * Register Text Block with style controls
	 */
	private function register_text_block() {
		register_block_type( 'sme-insights/text-block', array(
			'render_callback' => array( $this, 'render_text_block' ),
			'attributes' => array(
				'content' => array(
					'type' => 'string',
					'default' => '',
				),
				'fontSize' => array(
					'type' => 'number',
					'default' => 16,
				),
				'fontWeight' => array(
					'type' => 'string',
					'default' => '400',
				),
				'textColor' => array(
					'type' => 'string',
					'default' => '#000000',
				),
				'backgroundColor' => array(
					'type' => 'string',
					'default' => 'transparent',
				),
				'textAlign' => array(
					'type' => 'string',
					'default' => 'left',
				),
				'padding' => array(
					'type' => 'object',
					'default' => array(
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					),
				),
				'margin' => array(
					'type' => 'object',
					'default' => array(
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					),
				),
				'lineHeight' => array(
					'type' => 'number',
					'default' => 1.6,
				),
			),
		) );
	}
	
	/**
	 * Register Image Block with resize controls
	 */
	private function register_image_block() {
		register_block_type( 'sme-insights/image-block', array(
			'render_callback' => array( $this, 'render_image_block' ),
			'attributes' => array(
				'url' => array(
					'type' => 'string',
					'default' => '',
				),
				'alt' => array(
					'type' => 'string',
					'default' => '',
				),
				'width' => array(
					'type' => 'number',
					'default' => 100,
				),
				'height' => array(
					'type' => 'number',
					'default' => 'auto',
				),
				'align' => array(
					'type' => 'string',
					'default' => 'center',
				),
				'borderRadius' => array(
					'type' => 'number',
					'default' => 0,
				),
				'margin' => array(
					'type' => 'object',
					'default' => array(
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					),
				),
			),
		) );
	}
	
	/**
	 * Register Button Block
	 */
	private function register_button_block() {
		register_block_type( 'sme-insights/button-block', array(
			'render_callback' => array( $this, 'render_button_block' ),
			'attributes' => array(
				'text' => array(
					'type' => 'string',
					'default' => 'Click Here',
				),
				'url' => array(
					'type' => 'string',
					'default' => '#',
				),
				'backgroundColor' => array(
					'type' => 'string',
					'default' => '#2563eb',
				),
				'textColor' => array(
					'type' => 'string',
					'default' => '#ffffff',
				),
				'fontSize' => array(
					'type' => 'number',
					'default' => 16,
				),
				'padding' => array(
					'type' => 'object',
					'default' => array(
						'top' => 12,
						'right' => 24,
						'bottom' => 12,
						'left' => 24,
					),
				),
				'borderRadius' => array(
					'type' => 'number',
					'default' => 6,
				),
				'align' => array(
					'type' => 'string',
					'default' => 'left',
				),
			),
		) );
	}
	
	/**
	 * Register Columns Block (Grid Layout)
	 */
	private function register_columns_block() {
		register_block_type( 'sme-insights/columns-block', array(
			'render_callback' => array( $this, 'render_columns_block' ),
			'attributes' => array(
				'columns' => array(
					'type' => 'number',
					'default' => 2,
				),
				'gap' => array(
					'type' => 'number',
					'default' => 20,
				),
				'content' => array(
					'type' => 'array',
					'default' => array(),
				),
			),
		) );
	}
	
	/**
	 * Register Hero Block
	 */
	private function register_hero_block() {
		register_block_type( 'sme-insights/hero-block', array(
			'render_callback' => array( $this, 'render_hero_block' ),
			'attributes' => array(
				'title' => array(
					'type' => 'string',
					'default' => 'Hero Title',
				),
				'subtitle' => array(
					'type' => 'string',
					'default' => 'Hero Subtitle',
				),
				'backgroundImage' => array(
					'type' => 'string',
					'default' => '',
				),
				'backgroundColor' => array(
					'type' => 'string',
					'default' => '#2563eb',
				),
				'textColor' => array(
					'type' => 'string',
					'default' => '#ffffff',
				),
				'height' => array(
					'type' => 'number',
					'default' => 400,
				),
				'textAlign' => array(
					'type' => 'string',
					'default' => 'center',
				),
			),
		) );
	}
	
	/**
	 * Register Spacer Block
	 */
	private function register_spacer_block() {
		register_block_type( 'sme-insights/spacer-block', array(
			'render_callback' => array( $this, 'render_spacer_block' ),
			'attributes' => array(
				'height' => array(
					'type' => 'number',
					'default' => 50,
				),
			),
		) );
	}
	
	/**
	 * Register Divider Block
	 */
	private function register_divider_block() {
		register_block_type( 'sme-insights/divider-block', array(
			'render_callback' => array( $this, 'render_divider_block' ),
			'attributes' => array(
				'color' => array(
					'type' => 'string',
					'default' => '#e5e7eb',
				),
				'width' => array(
					'type' => 'number',
					'default' => 100,
				),
				'height' => array(
					'type' => 'number',
					'default' => 1,
				),
			),
		) );
	}
	
	/**
	 * Register Custom HTML/CSS Block
	 */
	private function register_html_block() {
		register_block_type( 'sme-insights/html-block', array(
			'render_callback' => array( $this, 'render_html_block' ),
			'attributes' => array(
				'html' => array(
					'type' => 'string',
					'default' => '',
				),
				'css' => array(
					'type' => 'string',
					'default' => '',
				),
			),
		) );
	}
	
	/**
	 * Render Text Block
	 */
	public function render_text_block( $attributes ) {
		$styles = array();
		
		if ( isset( $attributes['fontSize'] ) ) {
			$styles[] = 'font-size: ' . intval( $attributes['fontSize'] ) . 'px';
		}
		if ( isset( $attributes['fontWeight'] ) ) {
			$styles[] = 'font-weight: ' . esc_attr( $attributes['fontWeight'] );
		}
		if ( isset( $attributes['textColor'] ) ) {
			$styles[] = 'color: ' . esc_attr( $attributes['textColor'] );
		}
		if ( isset( $attributes['backgroundColor'] ) && $attributes['backgroundColor'] !== 'transparent' ) {
			$styles[] = 'background-color: ' . esc_attr( $attributes['backgroundColor'] );
		}
		if ( isset( $attributes['textAlign'] ) ) {
			$styles[] = 'text-align: ' . esc_attr( $attributes['textAlign'] );
		}
		if ( isset( $attributes['lineHeight'] ) ) {
			$styles[] = 'line-height: ' . floatval( $attributes['lineHeight'] );
		}
		
		// Padding
		if ( isset( $attributes['padding'] ) && is_array( $attributes['padding'] ) ) {
			$padding = $attributes['padding'];
			$styles[] = 'padding: ' . intval( $padding['top'] ) . 'px ' . intval( $padding['right'] ) . 'px ' . intval( $padding['bottom'] ) . 'px ' . intval( $padding['left'] ) . 'px';
		}
		
		// Margin
		if ( isset( $attributes['margin'] ) && is_array( $attributes['margin'] ) ) {
			$margin = $attributes['margin'];
			$styles[] = 'margin: ' . intval( $margin['top'] ) . 'px ' . intval( $margin['right'] ) . 'px ' . intval( $margin['bottom'] ) . 'px ' . intval( $margin['left'] ) . 'px';
		}
		
		$style_attr = ! empty( $styles ) ? ' style="' . esc_attr( implode( '; ', $styles ) ) . '"' : '';
		
		return '<div class="sme-text-block"' . $style_attr . '>' . wp_kses_post( $attributes['content'] ) . '</div>';
	}
	
	/**
	 * Render Image Block
	 */
	public function render_image_block( $attributes ) {
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}
		
		if ( empty( $attributes['url'] ) ) {
			return '';
		}
		
		$styles = array();
		
		if ( isset( $attributes['width'] ) ) {
			$styles[] = 'width: ' . intval( $attributes['width'] ) . '%';
		}
		if ( isset( $attributes['height'] ) && $attributes['height'] !== 'auto' ) {
			$styles[] = 'height: ' . intval( $attributes['height'] ) . 'px';
		}
		if ( isset( $attributes['borderRadius'] ) ) {
			$styles[] = 'border-radius: ' . intval( $attributes['borderRadius'] ) . 'px';
		}
		
		// Margin
		if ( isset( $attributes['margin'] ) && is_array( $attributes['margin'] ) ) {
			$margin = $attributes['margin'];
			$styles[] = 'margin: ' . intval( $margin['top'] ) . 'px ' . intval( $margin['right'] ) . 'px ' . intval( $margin['bottom'] ) . 'px ' . intval( $margin['left'] ) . 'px';
		}
		
		$style_attr = ! empty( $styles ) ? ' style="' . esc_attr( implode( '; ', $styles ) ) . '"' : '';
		$align_class = isset( $attributes['align'] ) ? ' align-' . esc_attr( $attributes['align'] ) : '';
		
		return '<div class="sme-image-block' . $align_class . '"><img src="' . esc_url( $attributes['url'] ) . '" alt="' . esc_attr( $attributes['alt'] ) . '"' . $style_attr . '></div>';
	}
	
	/**
	 * Render Button Block
	 */
	public function render_button_block( $attributes ) {
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}
		
		$styles = array();
		
		$styles[] = 'background-color: ' . esc_attr( $attributes['backgroundColor'] ?? '#2563eb' );
		$styles[] = 'color: ' . esc_attr( $attributes['textColor'] ?? '#ffffff' );
		$styles[] = 'font-size: ' . intval( $attributes['fontSize'] ?? 16 ) . 'px';
		$styles[] = 'border-radius: ' . intval( $attributes['borderRadius'] ?? 6 ) . 'px';
		
		if ( isset( $attributes['padding'] ) && is_array( $attributes['padding'] ) ) {
			$padding = $attributes['padding'];
			$styles[] = 'padding: ' . intval( $padding['top'] ) . 'px ' . intval( $padding['right'] ) . 'px ' . intval( $padding['bottom'] ) . 'px ' . intval( $padding['left'] ) . 'px';
		}
		
		$style_attr = ' style="' . esc_attr( implode( '; ', $styles ) ) . '"';
		$align_class = isset( $attributes['align'] ) ? ' align-' . esc_attr( $attributes['align'] ) : '';
		
		return '<div class="sme-button-block' . $align_class . '"><a href="' . esc_url( $attributes['url'] ) . '" class="sme-button"' . $style_attr . '>' . esc_html( $attributes['text'] ) . '</a></div>';
	}
	
	/**
	 * Render Columns Block
	 */
	public function render_columns_block( $attributes ) {
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}
		
		$columns = isset( $attributes['columns'] ) ? intval( $attributes['columns'] ) : 2;
		$gap = isset( $attributes['gap'] ) ? intval( $attributes['gap'] ) : 20;
		
		$style = 'display: grid; grid-template-columns: repeat(' . $columns . ', 1fr); gap: ' . $gap . 'px;';
		
		$content = '';
		if ( isset( $attributes['content'] ) && is_array( $attributes['content'] ) ) {
			foreach ( $attributes['content'] as $column_content ) {
				$content .= '<div class="sme-column">' . wp_kses_post( $column_content ) . '</div>';
			}
		}
		
		return '<div class="sme-columns-block" style="' . esc_attr( $style ) . '">' . $content . '</div>';
	}
	
	/**
	 * Render Hero Block
	 */
	public function render_hero_block( $attributes ) {
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}
		
		$styles = array();
		
		if ( ! empty( $attributes['backgroundImage'] ) ) {
			$styles[] = 'background-image: url(' . esc_url( $attributes['backgroundImage'] ) . ')';
			$styles[] = 'background-size: cover';
			$styles[] = 'background-position: center';
		} else {
			$styles[] = 'background-color: ' . esc_attr( $attributes['backgroundColor'] ?? '#2563eb' );
		}
		
		$styles[] = 'color: ' . esc_attr( $attributes['textColor'] ?? '#ffffff' );
		$styles[] = 'height: ' . intval( $attributes['height'] ?? 400 ) . 'px';
		$styles[] = 'text-align: ' . esc_attr( $attributes['textAlign'] ?? 'center' );
		
		$style_attr = ' style="' . esc_attr( implode( '; ', $styles ) ) . '"';
		
		return '<div class="sme-hero-block"' . $style_attr . '>
			<h1 class="sme-hero-title">' . esc_html( $attributes['title'] ?? 'Hero Title' ) . '</h1>
			<p class="sme-hero-subtitle">' . esc_html( $attributes['subtitle'] ?? 'Hero Subtitle' ) . '</p>
		</div>';
	}
	
	/**
	 * Render Spacer Block
	 */
	public function render_spacer_block( $attributes ) {
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}
		
		$height = isset( $attributes['height'] ) ? intval( $attributes['height'] ) : 50;
		return '<div class="sme-spacer-block" style="height: ' . $height . 'px;"></div>';
	}
	
	/**
	 * Render Divider Block
	 */
	public function render_divider_block( $attributes ) {
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}
		
		$width = isset( $attributes['width'] ) ? intval( $attributes['width'] ) : 100;
		$height = isset( $attributes['height'] ) ? intval( $attributes['height'] ) : 1;
		$color = isset( $attributes['color'] ) ? esc_attr( $attributes['color'] ) : '#e5e7eb';
		
		$style = 'width: ' . $width . '%; height: ' . $height . 'px; background-color: ' . $color . '; margin: 20px auto;';
		
		return '<div class="sme-divider-block" style="' . esc_attr( $style ) . '"></div>';
	}
	
	/**
	 * Render HTML Block
	 */
	public function render_html_block( $attributes ) {
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}
		
		$html = isset( $attributes['html'] ) ? $attributes['html'] : '';
		$css = isset( $attributes['css'] ) ? $attributes['css'] : '';
		
		$output = '';
		if ( ! empty( $css ) ) {
			$output .= '<style>' . wp_strip_all_tags( $css ) . '</style>';
		}
		$output .= '<div class="sme-html-block">' . wp_kses_post( $html ) . '</div>';
		
		return $output;
	}
	
	/**
	 * Enqueue block editor assets
	 */
	public function enqueue_block_editor_assets() {
		// Check if already enqueued to prevent duplicates
		if ( wp_script_is( 'sme-page-builder-blocks', 'enqueued' ) ) {
			return;
		}
		
		// Check if we're in widgets editor or customize widgets (WordPress 5.8+)
		$is_widgets_editor = ( function_exists( 'wp_use_widgets_block_editor' ) && wp_use_widgets_block_editor() );
		$is_customize_widgets = ( isset( $_GET['customize-widgets'] ) && ! empty( sanitize_text_field( $_GET['customize-widgets'] ) ) );
		
		// Use wp-block-editor instead of deprecated wp-editor (WordPress 5.8+)
		$dependencies = array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
			'wp-components',
			'wp-data',
		);
		
		// Only add wp-block-editor if not in widgets editor
		if ( ! $is_widgets_editor && ! $is_customize_widgets ) {
			$dependencies[] = 'wp-block-editor';
		}
		
		wp_enqueue_script(
			'sme-page-builder-blocks',
			SME_THEME_ASSETS . '/js/page-builder-blocks.js',
			$dependencies,
			SME_THEME_VERSION,
			true
		);
		
		if ( ! wp_style_is( 'sme-page-builder-blocks-editor', 'enqueued' ) ) {
			wp_enqueue_style(
				'sme-page-builder-blocks-editor',
				SME_THEME_ASSETS . '/css/page-builder-blocks-editor.css',
				array( 'wp-edit-blocks' ),
				SME_THEME_VERSION
			);
		}
	}
	
	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_frontend_assets() {
		// Check if already enqueued to prevent duplicates
		if ( wp_style_is( 'sme-page-builder-blocks', 'enqueued' ) ) {
			return;
		}
		
		wp_enqueue_style(
			'sme-page-builder-blocks',
			SME_THEME_ASSETS . '/css/page-builder-blocks.css',
			array(),
			SME_THEME_VERSION
		);
	}
}

