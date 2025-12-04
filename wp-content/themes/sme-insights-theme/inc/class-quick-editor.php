<?php
/**
 * Quick Editor - Easy Content Editing
 * Allows users to edit content directly from frontend with hover buttons
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Quick_Editor {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init_hooks' ), 5 );
		add_action( 'after_setup_theme', array( $this, 'init_hooks' ), 999 );
	}
	
	public function init_hooks() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_items' ), 100 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_footer', array( $this, 'output_quick_edit_buttons' ) );
		add_action( 'wp_ajax_sme_quick_edit_content', array( $this, 'quick_edit_content' ) );
		add_action( 'wp_ajax_sme_quick_edit_style', array( $this, 'quick_edit_style' ) );
		add_action( 'wp_footer', array( $this, 'output_saved_edits' ), 999 );
	}
	
	public function add_admin_bar_items( $wp_admin_bar ) {
		// Only show in frontend, not in admin area
		if ( is_admin() ) {
			return;
		}
		
		$current_page_slug = '';
		if ( is_page() ) {
			global $post;
			$current_page_slug = $post->post_name;
		}
		
		$legal_pages = array( 'privacy-policy', 'terms-of-service', 'disclaimer' );
		if ( in_array( $current_page_slug, $legal_pages ) ) {
			return;
		}
		
		$is_active = isset( $_GET['sme_quick_edit'] ) && sanitize_text_field( wp_unslash( $_GET['sme_quick_edit'] ) ) === '1';
		$toggle_url = $is_active ? remove_query_arg( 'sme_quick_edit' ) : add_query_arg( 'sme_quick_edit', '1' );
		
		$wp_admin_bar->add_menu( array(
			'id'    => 'sme-quick-edit',
			'title' => '<span class="ab-icon"></span> <span class="ab-label">' . __( 'Quick Edit', 'sme-insights' ) . '</span>',
			'href'  => esc_url( $toggle_url ),
			'meta'  => array(
				'title' => $is_active ? __( 'Disable Quick Edit', 'sme-insights' ) : __( 'Enable Quick Edit', 'sme-insights' ),
			),
		) );
	}
	
	/**
	 * Enqueue assets
	 */
	public function enqueue_assets() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		
		// Don't enqueue Quick Edit assets on Legal pages
		$current_page_slug = '';
		if ( is_page() ) {
			global $post;
			$current_page_slug = $post->post_name;
		}
		
		$legal_pages = array( 'privacy-policy', 'terms-of-service', 'disclaimer' );
		if ( in_array( $current_page_slug, $legal_pages ) ) {
			return;
		}
		
		wp_enqueue_style(
			'sme-quick-editor',
			SME_THEME_ASSETS . '/css/quick-editor.css',
			array(),
			SME_THEME_VERSION
		);
		
		wp_enqueue_style(
			'sme-quick-editor',
			SME_THEME_ASSETS . '/css/quick-editor.css',
			array(),
			SME_THEME_VERSION
		);
		
		wp_enqueue_script(
			'sme-quick-editor',
			SME_THEME_ASSETS . '/js/quick-editor.js',
			array( 'jquery' ),
			SME_THEME_VERSION,
			true
		);
		
		$current_page_id = 0;
		$current_page_identifier = '';
		if ( is_front_page() ) {
			$current_page_id = get_option( 'page_on_front' );
			$current_page_identifier = $current_page_id > 0 ? '_page_' . $current_page_id : '';
		} elseif ( is_page() || is_single() || is_home() ) {
			$current_page_id = get_queried_object_id();
			$current_page_identifier = $current_page_id > 0 ? '_page_' . $current_page_id : '';
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( $term && isset( $term->taxonomy ) && isset( $term->term_id ) ) {
				$current_page_identifier = '_tax_' . $term->taxonomy . '_' . $term->term_id;
			}
		} elseif ( is_archive() ) {
			$post_type = get_post_type();
			if ( $post_type ) {
				$current_page_identifier = '_archive_' . $post_type;
			}
		}
		
		$saved_content = get_option( 'sme_quick_edit_content', array() );
		$saved_styles = get_option( 'sme_quick_edit_styles', array() );

		wp_localize_script( 'sme-quick-editor', 'smeQuickEditor', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'sme_quick_editor_nonce' ),
			'isActive' => isset( $_GET['sme_quick_edit'] ) && sanitize_text_field( wp_unslash( $_GET['sme_quick_edit'] ) ) === '1',
			'pageId' => $current_page_id,
			'pageIdentifier' => $current_page_identifier,
			'savedContent' => $saved_content,
			'savedStyles' => $saved_styles,
			'strings' => array(
				'edit' => __( 'Edit', 'sme-insights' ),
				'save' => __( 'Save', 'sme-insights' ),
				'cancel' => __( 'Cancel', 'sme-insights' ),
			),
		) );
	}
	
	public function output_quick_edit_buttons() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		
		$current_page_slug = '';
		if ( is_page() ) {
			global $post;
			$current_page_slug = $post->post_name;
		}
		
		$legal_pages = array( 'privacy-policy', 'terms-of-service', 'disclaimer' );
		if ( in_array( $current_page_slug, $legal_pages ) ) {
			return;
		}
		
		$is_quick_edit = isset( $_GET['sme_quick_edit'] ) && sanitize_text_field( wp_unslash( $_GET['sme_quick_edit'] ) ) === '1';
		
		if ( ! $is_quick_edit ) {
			return;
		}
		
		?>
		<div id="sme-quick-edit-overlay" class="sme-quick-edit-overlay">
			<div class="sme-quick-edit-toolbar">
				<div class="sme-quick-edit-title">
					<span class="sme-icon">✏️</span>
					<?php esc_html_e( 'Quick Edit Mode Active', 'sme-insights' ); ?>
				</div>
				<div class="sme-quick-edit-actions">
					<button class="sme-quick-btn sme-quick-btn-primary" id="smeQuickSaveAll">
						<?php esc_html_e( 'Save All', 'sme-insights' ); ?>
					</button>
					<button type="button" class="sme-quick-btn sme-quick-btn-secondary" id="smeQuickExit">
						<?php esc_html_e( 'Exit', 'sme-insights' ); ?>
					</button>
				</div>
			</div>
		</div>
		<?php
	}
	
	public function quick_edit_content() {
		// Log start of request
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'SME Quick Editor: Starting content save request' );
			error_log( 'POST data: ' . print_r( $_POST, true ) );
		}

		check_ajax_referer( 'sme_quick_editor_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'SME Quick Editor: Permission denied' );
			}
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		$element_id = isset( $_POST['element_id'] ) ? sanitize_text_field( wp_unslash( $_POST['element_id'] ) ) : '';
		$stable_id = isset( $_POST['stable_id'] ) ? sanitize_text_field( wp_unslash( $_POST['stable_id'] ) ) : $element_id;
		$element_type = isset( $_POST['element_type'] ) ? sanitize_text_field( wp_unslash( $_POST['element_type'] ) ) : 'element';
		$selector = isset( $_POST['selector'] ) ? sanitize_text_field( wp_unslash( $_POST['selector'] ) ) : '';
		$content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';
		
		if ( empty( $element_id ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'SME Quick Editor: Invalid element ID' );
			}
			wp_send_json_error( array( 'message' => __( 'Invalid element', 'sme-insights' ) ) );
		}
		
		$page_id = 0;
		$page_identifier = '';
		if ( is_page() || is_single() || is_home() || is_front_page() ) {
			$page_id = get_queried_object_id();
			$page_identifier = $page_id > 0 ? '_page_' . $page_id : '';
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( $term && isset( $term->taxonomy ) && isset( $term->term_id ) ) {
				$page_identifier = '_tax_' . $term->taxonomy . '_' . $term->term_id;
			}
		} elseif ( is_archive() ) {
			$post_type = get_post_type();
			if ( $post_type ) {
				$page_identifier = '_archive_' . $post_type;
			}
		}
		
		if ( ! $page_id && isset( $_POST['page_id'] ) ) {
			$page_id = absint( $_POST['page_id'] );
		}
		if ( empty( $page_identifier ) && isset( $_POST['page_identifier'] ) ) {
			$page_identifier = sanitize_text_field( wp_unslash( $_POST['page_identifier'] ) );
		}
		
		$base_key = $stable_id ? $stable_id : $element_id;
		$save_key = $base_key . $page_identifier;
		
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "SME Quick Editor: Saving content for key: $save_key (Page ID: $page_id)" );
		}

		$saved_content = get_option( 'sme_quick_edit_content', array() );
		if ( ! is_array( $saved_content ) ) {
			$saved_content = array();
		}
		$saved_content[ $save_key ] = array(
			'content' => $content,
			'selector' => $selector ? $selector : '#' . $save_key,
			'element_id' => $element_id,
			'stable_id' => $stable_id,
			'element_type' => $element_type,
			'page_id' => $page_id,
			'page_identifier' => $page_identifier,
			'timestamp' => time()
		);
		
		$update_result_all = update_option( 'sme_quick_edit_content', $saved_content );
		$update_result_single = update_option( 'sme_quick_edit_' . $save_key, $content );
		
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "SME Quick Editor: Save results - All: " . ( $update_result_all ? 'true' : 'false' ) . ", Single: " . ( $update_result_single ? 'true' : 'false' ) );
		}

		wp_send_json_success( array( 
			'message' => __( 'Content saved', 'sme-insights' ),
			'element_id' => $element_id,
			'stable_id' => $stable_id,
			'selector' => $selector,
			'content' => $content
		) );
	}
	
	public function quick_edit_style() {
		// Log start of request
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'SME Quick Editor: Starting style save request' );
			error_log( 'POST data: ' . print_r( $_POST, true ) );
		}

		check_ajax_referer( 'sme_quick_editor_nonce', 'nonce' );
		
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'SME Quick Editor: Permission denied' );
			}
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'sme-insights' ) ) );
		}
		
		$element_id = isset( $_POST['element_id'] ) ? sanitize_text_field( wp_unslash( $_POST['element_id'] ) ) : '';
		$stable_id = isset( $_POST['stable_id'] ) ? sanitize_text_field( wp_unslash( $_POST['stable_id'] ) ) : $element_id;
		$element_type = isset( $_POST['element_type'] ) ? sanitize_text_field( wp_unslash( $_POST['element_type'] ) ) : 'element';
		$selector = isset( $_POST['selector'] ) ? sanitize_text_field( wp_unslash( $_POST['selector'] ) ) : '';
		
		$styles = array();
		if ( isset( $_POST['styles_json'] ) && ! empty( $_POST['styles_json'] ) ) {
			$styles_json = wp_unslash( $_POST['styles_json'] );
			$decoded = json_decode( $styles_json, true );
			if ( is_array( $decoded ) ) {
				$styles = array_map( 'sanitize_text_field', $decoded );
			}
		} elseif ( isset( $_POST['styles'] ) ) {
			if ( is_array( $_POST['styles'] ) ) {
				$styles = array_map( 'sanitize_text_field', wp_unslash( $_POST['styles'] ) );
			} else {
				$styles_raw = wp_unslash( $_POST['styles'] );
				if ( is_string( $styles_raw ) ) {
					$decoded = json_decode( $styles_raw, true );
					if ( is_array( $decoded ) ) {
						$styles = array_map( 'sanitize_text_field', $decoded );
					}
				}
			}
		}
		
		if ( empty( $element_id ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'SME Quick Editor: Invalid element ID' );
			}
			wp_send_json_error( array( 'message' => __( 'Invalid element ID', 'sme-insights' ) ) );
		}
		
		$page_id = 0;
		$page_identifier = '';
		if ( is_page() || is_single() || is_home() || is_front_page() ) {
			$page_id = get_queried_object_id();
			$page_identifier = $page_id > 0 ? '_page_' . $page_id : '';
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( $term && isset( $term->taxonomy ) && isset( $term->term_id ) ) {
				$page_identifier = '_tax_' . $term->taxonomy . '_' . $term->term_id;
			}
		} elseif ( is_archive() ) {
			$post_type = get_post_type();
			if ( $post_type ) {
				$page_identifier = '_archive_' . $post_type;
			}
		}
		
		if ( ! $page_id && isset( $_POST['page_id'] ) ) {
			$page_id = absint( $_POST['page_id'] );
		}
		if ( empty( $page_identifier ) && isset( $_POST['page_identifier'] ) ) {
			$page_identifier = sanitize_text_field( wp_unslash( $_POST['page_identifier'] ) );
		}
		
		$base_key = $stable_id ? $stable_id : $element_id;
		$save_key = $base_key . $page_identifier;
		
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "SME Quick Editor: Saving styles for key: $save_key (Page ID: $page_id)" );
		}

		$saved_styles = get_option( 'sme_quick_edit_styles', array() );
		if ( ! is_array( $saved_styles ) ) {
			$saved_styles = array();
		}
		$saved_styles[ $save_key ] = array(
			'styles' => $styles,
			'selector' => $selector ? $selector : '#' . $save_key,
			'element_id' => $element_id,
			'stable_id' => $stable_id,
			'element_type' => $element_type,
			'page_id' => $page_id,
			'page_identifier' => $page_identifier,
			'timestamp' => time()
		);
		
		$update_result_all = update_option( 'sme_quick_edit_styles', $saved_styles );
		$update_result_single = update_option( 'sme_quick_edit_styles_' . $save_key, $styles );
		
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "SME Quick Editor: Style save results - All: " . ( $update_result_all ? 'true' : 'false' ) . ", Single: " . ( $update_result_single ? 'true' : 'false' ) );
		}

		wp_send_json_success( array( 
			'message' => __( 'Styles saved', 'sme-insights' ),
			'element_id' => $element_id,
			'stable_id' => $stable_id,
			'selector' => $selector,
			'styles' => $styles
		) );
	}
	
	public function output_saved_edits() {
		$this->cleanup_legal_page_edits();
		
		$current_page_slug = '';
		if ( is_page() ) {
			global $post;
			$current_page_slug = $post->post_name;
		}
		
		$legal_pages = array( 'privacy-policy', 'terms-of-service', 'disclaimer' );
		if ( in_array( $current_page_slug, $legal_pages ) ) {
			return;
		}
		$current_page_id = 0;
		$current_page_identifier = '';
		if ( is_page() || is_single() || is_home() || is_front_page() ) {
			$current_page_id = get_queried_object_id();
			$current_page_identifier = $current_page_id > 0 ? '_page_' . $current_page_id : '';
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( $term && isset( $term->taxonomy ) && isset( $term->term_id ) ) {
				$current_page_identifier = '_tax_' . $term->taxonomy . '_' . $term->term_id;
			}
		} elseif ( is_archive() ) {
			$post_type = get_post_type();
			if ( $post_type ) {
				$current_page_identifier = '_archive_' . $post_type;
			}
		}
		
		// Get all saved content
		$saved_content = get_option( 'sme_quick_edit_content', array() );
		if ( ! is_array( $saved_content ) ) {
			$saved_content = array();
		}
		
		// Get all saved styles
		$saved_styles = get_option( 'sme_quick_edit_styles', array() );
		if ( ! is_array( $saved_styles ) ) {
			$saved_styles = array();
		}
		
		$filtered_content = array();
		$filtered_styles = array();
		
		foreach ( $saved_content as $key => $data ) {
			$edit_page_identifier = isset( $data['page_identifier'] ) ? $data['page_identifier'] : '';
			if ( empty( $edit_page_identifier ) ) {
				// Backward compatibility: use page_id to generate identifier
				$edit_page_id = isset( $data['page_id'] ) ? absint( $data['page_id'] ) : 0;
				$edit_page_identifier = $edit_page_id > 0 ? '_page_' . $edit_page_id : '';
			}
			
			// Match if page_identifier matches current page
			if ( $edit_page_identifier === $current_page_identifier ) {
				$filtered_content[ $key ] = $data;
			}
		}
		
		foreach ( $saved_styles as $key => $data ) {
			$edit_page_identifier = isset( $data['page_identifier'] ) ? $data['page_identifier'] : '';
			if ( empty( $edit_page_identifier ) ) {
				// Backward compatibility: use page_id to generate identifier
				$edit_page_id = isset( $data['page_id'] ) ? absint( $data['page_id'] ) : 0;
				$edit_page_identifier = $edit_page_id > 0 ? '_page_' . $edit_page_id : '';
			}
			
			// Match if page_identifier matches current page
			if ( $edit_page_identifier === $current_page_identifier ) {
				$filtered_styles[ $key ] = $data;
			}
		}
		
		// Use filtered content and styles (only for current page)
		$saved_content = $filtered_content;
		$saved_styles = $filtered_styles;
		
		// Clean up legal content from filtered results only
		$legal_content_keywords = array(
			'تعديل تم عن طريق',
			'كان بايظ',
			'privacy policy',
			'terms of service',
			'disclaimer',
			'Disclaimer',
			'No Professional Advice',
			'Accuracy of Information',
			'No Warranties',
			'Limitation of Liability',
			'The information provided on SME Insights',
			'general informational purposes',
			'professional advice',
			'qualified professionals',
			'Not Professional Advice',
			'External Links',
			'Third-Party Content',
			'We make no representations',
			'express or implied',
			'warranties of merchantability',
			'general informational',
			'informational purposes',
			'seek the advice',
			'qualified professionals',
			'loss or damage',
			'Deep analysis of market trends and expert legal advice',
			'Deep analysis',
			'expert legal advice',
			'market trends and expert legal'
		);
		
		$filtered_content = array();
		$content_changed = false;
		
		foreach ( $saved_content as $key => $data ) {
			$should_skip = false;
			$content_value = is_array( $data ) && isset( $data['content'] ) ? $data['content'] : ( is_string( $data ) ? $data : '' );
			
			foreach ( $legal_content_keywords as $keyword ) {
				if ( stripos( $content_value, $keyword ) !== false ) {
					$should_skip = true;
					$content_changed = true;
					delete_option( 'sme_quick_edit_' . $key );
					break;
				}
			}
			if ( ! $should_skip && is_array( $data ) && isset( $data['selector'] ) ) {
				$legal_selectors = array(
					'.contributor-content',
					'.contributor-accordion-item',
					'.legal-intro',
					'.contributor-accordion-header',
					'.contributor-accordion-content',
					'.main-content-area',
					'.contributor-content-wrapper',
					'.contributor-page-container',
					'.contact-hero',
					'.legal-content'
				);
				foreach ( $legal_selectors as $legal_selector ) {
					if ( strpos( $data['selector'], $legal_selector ) !== false ) {
						$should_skip = true;
						$content_changed = true;
						delete_option( 'sme_quick_edit_' . $key );
						break;
					}
				}
			}
			
			if ( ! $should_skip ) {
				$filtered_content[ $key ] = $data;
			}
		}
		
		if ( $content_changed ) {
			// Update the main option: remove only legal content, keep all other edits
			$all_saved_content = get_option( 'sme_quick_edit_content', array() );
			// Remove only the keys that were filtered out (legal content)
			foreach ( $saved_content as $key => $data ) {
				if ( ! isset( $filtered_content[ $key ] ) ) {
					unset( $all_saved_content[ $key ] );
				}
			}
			update_option( 'sme_quick_edit_content', $all_saved_content );
			// Use filtered content only for current page output
			$saved_content = $filtered_content;
		}
		
		// Only apply edits for current page (already filtered)
		if ( empty( $saved_content ) && empty( $saved_styles ) ) {
			return;
		}
		
		?>
		<style type="text/css">
		<?php
		// Hide elements that will be modified by JavaScript to prevent FOUC
		foreach ( $saved_content as $save_key => $data ) :
			$content_selector = isset( $data['selector'] ) ? $data['selector'] : '#' . $save_key;
			$stable_id = isset( $data['stable_id'] ) ? $data['stable_id'] : $save_key;
			?>
			<?php echo esc_html( $content_selector ); ?>,
			#<?php echo esc_html( $stable_id ); ?>,
			#<?php echo esc_html( $save_key ); ?> {
				visibility: hidden;
				opacity: 0;
			}
		<?php endforeach; ?>
		
		<?php foreach ( $saved_styles as $save_key => $data ) :
			$style_selector = isset( $data['selector'] ) ? $data['selector'] : '#' . $save_key;
			$stable_id = isset( $data['stable_id'] ) ? $data['stable_id'] : $save_key;
			?>
			<?php echo esc_html( $style_selector ); ?>,
			#<?php echo esc_html( $stable_id ); ?>,
			#<?php echo esc_html( $save_key ); ?> {
				visibility: hidden;
				opacity: 0;
			}
		<?php endforeach; ?>
		</style>
		<script type="text/javascript">
		(function($) {
			'use strict';
			
			// Show elements after applying edits
			function showElementsAfterEdit() {
				<?php foreach ( $saved_content as $save_key => $data ) :
					$content_selector = isset( $data['selector'] ) ? $data['selector'] : '#' . $save_key;
					$stable_id = isset( $data['stable_id'] ) ? $data['stable_id'] : $save_key;
					$var_name = preg_replace( '/[^a-zA-Z0-9_]/', '_', $save_key );
				?>
				var contentElement<?php echo esc_js( $var_name ); ?> = null;
				if ('<?php echo esc_js( $stable_id ); ?>' && '<?php echo esc_js( $stable_id ); ?>' !== '<?php echo esc_js( $save_key ); ?>') {
					contentElement<?php echo esc_js( $var_name ); ?> = $('#<?php echo esc_js( $stable_id ); ?>');
				}
				if (!contentElement<?php echo esc_js( $var_name ); ?> || contentElement<?php echo esc_js( $var_name ); ?>.length === 0) {
					if ('<?php echo esc_js( $content_selector ); ?>'.indexOf('#') === 0) {
						contentElement<?php echo esc_js( $var_name ); ?> = $('<?php echo esc_js( $content_selector ); ?>');
					} else {
						contentElement<?php echo esc_js( $var_name ); ?> = $('<?php echo esc_js( $content_selector ); ?>').first();
					}
				}
				if (!contentElement<?php echo esc_js( $var_name ); ?> || contentElement<?php echo esc_js( $var_name ); ?>.length === 0) {
					contentElement<?php echo esc_js( $var_name ); ?> = $('#<?php echo esc_js( $save_key ); ?>');
				}
				if (contentElement<?php echo esc_js( $var_name ); ?> && contentElement<?php echo esc_js( $var_name ); ?>.length > 0) {
					contentElement<?php echo esc_js( $var_name ); ?>.css({
						'visibility': 'visible',
						'opacity': '1'
					});
				}
				<?php endforeach; ?>
				
				<?php foreach ( $saved_styles as $save_key => $data ) :
					$style_selector = isset( $data['selector'] ) ? $data['selector'] : '#' . $save_key;
					$stable_id = isset( $data['stable_id'] ) ? $data['stable_id'] : $save_key;
					$var_name = preg_replace( '/[^a-zA-Z0-9_]/', '_', $save_key );
				?>
				var styleElement<?php echo esc_js( $var_name ); ?> = null;
				if ('<?php echo esc_js( $stable_id ); ?>' && '<?php echo esc_js( $stable_id ); ?>' !== '<?php echo esc_js( $save_key ); ?>') {
					styleElement<?php echo esc_js( $var_name ); ?> = $('#<?php echo esc_js( $stable_id ); ?>');
				}
				if (!styleElement<?php echo esc_js( $var_name ); ?> || styleElement<?php echo esc_js( $var_name ); ?>.length === 0) {
					if ('<?php echo esc_js( $style_selector ); ?>'.indexOf('#') === 0) {
						styleElement<?php echo esc_js( $var_name ); ?> = $('<?php echo esc_js( $style_selector ); ?>');
					} else {
						styleElement<?php echo esc_js( $var_name ); ?> = $('<?php echo esc_js( $style_selector ); ?>').first();
					}
				}
				if (!styleElement<?php echo esc_js( $var_name ); ?> || styleElement<?php echo esc_js( $var_name ); ?>.length === 0) {
					styleElement<?php echo esc_js( $var_name ); ?> = $('#<?php echo esc_js( $save_key ); ?>');
				}
				if (styleElement<?php echo esc_js( $var_name ); ?> && styleElement<?php echo esc_js( $var_name ); ?>.length > 0) {
					styleElement<?php echo esc_js( $var_name ); ?>.css({
						'visibility': 'visible',
						'opacity': '1'
					});
				}
				<?php endforeach; ?>
			}
			
			try {
				var legalKeywords = ['Disclaimer', 'disclaimer', 'No Professional Advice', 'Accuracy of Information', 'No Warranties', 'Limitation of Liability', 'The information provided on SME Insights', 'general informational purposes', 'professional advice', 'qualified professionals', 'تعديل تم عن طريق', 'كان بايظ', 'While we strive', 'accurate and up-to-date', 'completeness, accuracy', 'reliability, or suitability', 'seek the advice', 'loss or damage'];
				var quickEditData = localStorage.getItem('sme_quick_edit_content');
				if (quickEditData) {
					try {
						var parsed = JSON.parse(quickEditData);
						var cleaned = {};
						var changed = false;
						for (var key in parsed) {
							if (parsed.hasOwnProperty(key)) {
								var content = parsed[key].content || parsed[key] || '';
								var isLegal = false;
								for (var i = 0; i < legalKeywords.length; i++) {
									if (content.toString().toLowerCase().indexOf(legalKeywords[i].toLowerCase()) !== -1) {
										isLegal = true;
										changed = true;
										break;
									}
								}
								if (!isLegal) {
									cleaned[key] = parsed[key];
								}
							}
						}
						if (changed) {
							localStorage.setItem('sme_quick_edit_content', JSON.stringify(cleaned));
						}
					} catch(e) {
						localStorage.removeItem('sme_quick_edit_content');
					}
				}
			} catch(e) {
			}
			
			$(document).ready(function() {
				
				<?php 
				// Legal page selectors to skip
				$legal_selectors = array(
					'.contributor-content',
					'.contributor-accordion-item',
					'.legal-intro',
					'.contributor-accordion-header',
					'.contributor-accordion-content',
					'.main-content-area',
					'.contributor-content-wrapper',
					'.contributor-page-container',
					'.contact-hero',
					'.legal-content'
				);
				$legal_content_keywords = array(
					'تعديل تم عن طريق',
					'كان بايظ',
					'privacy policy',
					'terms of service',
					'disclaimer',
					'Disclaimer',
					'No Professional Advice',
					'Accuracy of Information',
					'No Warranties',
					'Limitation of Liability',
					'The information provided on SME Insights',
					'general informational purposes',
					'professional advice',
					'qualified professionals',
					'Not Professional Advice',
					'External Links',
					'Third-Party Content'
				);
				foreach ( $saved_content as $save_key => $data ) : 
					$content_data = is_array( $data ) ? $data : array( 'content' => $data, 'selector' => '#' . $save_key );
					$content_value = isset( $content_data['content'] ) ? $content_data['content'] : $data;
					$content_selector = isset( $content_data['selector'] ) ? $content_data['selector'] : '#' . $save_key;
					
					$is_legal_edit = false;
					foreach ( $legal_selectors as $legal_selector ) {
						if ( strpos( $content_selector, $legal_selector ) !== false ) {
							$is_legal_edit = true;
							break;
						}
					}
					if ( ! $is_legal_edit ) {
						foreach ( $legal_content_keywords as $keyword ) {
							if ( stripos( $content_value, $keyword ) !== false ) {
								$is_legal_edit = true;
								break;
							}
						}
					}
					
					if ( $is_legal_edit ) {
						continue;
					}
					
					$stable_id = isset( $content_data['stable_id'] ) ? $content_data['stable_id'] : $save_key;
					$var_name = preg_replace( '/[^a-zA-Z0-9_]/', '_', $save_key );
					?>
					var contentElement<?php echo esc_js( $var_name ); ?> = null;
					
					<?php if ( $stable_id && $stable_id !== $save_key ) : ?>
					contentElement<?php echo esc_js( $var_name ); ?> = $('#<?php echo esc_js( $stable_id ); ?>');
					<?php endif; ?>
					
					if (!contentElement<?php echo esc_js( $var_name ); ?> || contentElement<?php echo esc_js( $var_name ); ?>.length === 0) {
						if ('<?php echo esc_js( $content_selector ); ?>'.indexOf('#') === 0) {
							contentElement<?php echo esc_js( $var_name ); ?> = $('<?php echo esc_js( $content_selector ); ?>');
						} else {
							contentElement<?php echo esc_js( $var_name ); ?> = $('<?php echo esc_js( $content_selector ); ?>').first();
						}
					}
					
					if (!contentElement<?php echo esc_js( $var_name ); ?> || contentElement<?php echo esc_js( $var_name ); ?>.length === 0) {
						contentElement<?php echo esc_js( $var_name ); ?> = $('#<?php echo esc_js( $save_key ); ?>');
					}
					
					if (contentElement<?php echo esc_js( $var_name ); ?> && contentElement<?php echo esc_js( $var_name ); ?>.length === 1) {
						var contentToApply = <?php echo wp_json_encode( $content_value ); ?>;
						var legalKeywords = ['Disclaimer', 'disclaimer', 'No Professional Advice', 'Accuracy of Information', 'No Warranties', 'Limitation of Liability', 'The information provided on SME Insights', 'general informational purposes', 'professional advice', 'qualified professionals', 'تعديل تم عن طريق', 'كان بايظ'];
						var isLegalContent = false;
						for (var i = 0; i < legalKeywords.length; i++) {
							if (contentToApply && contentToApply.toString().toLowerCase().indexOf(legalKeywords[i].toLowerCase()) !== -1) {
								isLegalContent = true;
								break;
							}
						}
						if (!isLegalContent) {
							contentElement<?php echo esc_js( $var_name ); ?>.html(contentToApply);
						}
					} else {
					}
				<?php endforeach; ?>
				
				<?php 
				foreach ( $saved_styles as $save_key => $data ) : 
					$style_data = is_array( $data ) && isset( $data['styles'] ) ? $data : array( 'styles' => $data, 'selector' => '#' . $save_key );
					$style_value = isset( $style_data['styles'] ) ? $style_data['styles'] : $data;
					$style_selector = isset( $style_data['selector'] ) ? $style_data['selector'] : '#' . $save_key;
					
					$is_legal_edit = false;
					foreach ( $legal_selectors as $legal_selector ) {
						if ( strpos( $style_selector, $legal_selector ) !== false ) {
							$is_legal_edit = true;
							break;
						}
					}
					
					if ( $is_legal_edit ) {
						continue;
					}
					
					$stable_id = isset( $style_data['stable_id'] ) ? $style_data['stable_id'] : $save_key;
					$var_name = preg_replace( '/[^a-zA-Z0-9_]/', '_', $save_key );
					?>
					var styleElement<?php echo esc_js( $var_name ); ?> = null;
					
					<?php if ( $stable_id && $stable_id !== $save_key ) : ?>
					styleElement<?php echo esc_js( $var_name ); ?> = $('#<?php echo esc_js( $stable_id ); ?>');
					<?php endif; ?>
					
					if (!styleElement<?php echo esc_js( $var_name ); ?> || styleElement<?php echo esc_js( $var_name ); ?>.length === 0) {
						if ('<?php echo esc_js( $style_selector ); ?>'.indexOf('#') === 0) {
							styleElement<?php echo esc_js( $var_name ); ?> = $('<?php echo esc_js( $style_selector ); ?>');
						} else {
							styleElement<?php echo esc_js( $var_name ); ?> = $('<?php echo esc_js( $style_selector ); ?>').first();
						}
					}
					
					if (!styleElement<?php echo esc_js( $var_name ); ?> || styleElement<?php echo esc_js( $var_name ); ?>.length === 0) {
						styleElement<?php echo esc_js( $var_name ); ?> = $('#<?php echo esc_js( $save_key ); ?>');
					}
					
					if (styleElement<?php echo esc_js( $var_name ); ?> && styleElement<?php echo esc_js( $var_name ); ?>.length === 1) {
						var hasGradient = false;
						var bgImage = styleElement<?php echo esc_js( $var_name ); ?>.css('background-image');
						var bgGradient = styleElement<?php echo esc_js( $var_name ); ?>.css('background');
						
						if (bgImage && bgImage !== 'none' && (bgImage.indexOf('gradient') !== -1 || bgImage.indexOf('linear-gradient') !== -1 || bgImage.indexOf('radial-gradient') !== -1)) {
							hasGradient = true;
						}
						
						// Remove background-color from styles if element has gradient
						var stylesToApply = <?php echo wp_json_encode( $style_value ); ?>;
						if (hasGradient && stylesToApply && stylesToApply['background-color']) {
							delete stylesToApply['background-color'];
						}
						
						// Apply styles (without background-color if gradient exists)
						if (Object.keys(stylesToApply).length > 0) {
							styleElement<?php echo esc_js( $var_name ); ?>.css(stylesToApply);
						}
					} else {
					}
				<?php endforeach; ?>
				
				
				// Show elements after applying edits
				showElementsAfterEdit();
			});
		})(jQuery);
		</script>
		<?php
	}
	
	/**
	 * Clean up any saved edits that might have been mistakenly saved for Legal pages
	 */
	public function cleanup_legal_page_edits() {
		// Get all saved content and styles
		$saved_content = get_option( 'sme_quick_edit_content', array() );
		$saved_styles = get_option( 'sme_quick_edit_styles', array() );
		
		if ( ! is_array( $saved_content ) ) {
			$saved_content = array();
		}
		if ( ! is_array( $saved_styles ) ) {
			$saved_styles = array();
		}
		
		$content_changed = false;
		$styles_changed = false;
		
		// Remove edits that target elements on Legal pages
		// Common selectors that might be on Legal pages
		$legal_selectors = array(
			'.contributor-content',
			'.contributor-accordion-item',
			'.legal-intro',
			'.contributor-accordion-header',
			'.contributor-accordion-content',
			'.main-content-area',
			'.contributor-content-wrapper',
			'.contributor-page-container',
			'.contact-hero',
			'.legal-content',
			'article',
			'.entry-content'
		);
		
		// Also check for content that contains common Legal page text
		$legal_content_keywords = array(
			'تعديل تم عن طريق',
			'كان بايظ',
			'privacy policy',
			'terms of service',
			'disclaimer',
			'Disclaimer',
			'No Professional Advice',
			'Accuracy of Information',
			'No Warranties',
			'Limitation of Liability',
			'The information provided on SME Insights',
			'general informational purposes',
			'professional advice',
			'qualified professionals',
			'Not Professional Advice',
			'External Links',
			'Third-Party Content',
			'We make no representations',
			'express or implied',
			'warranties of merchantability',
			'general informational',
			'informational purposes',
			'seek the advice',
			'loss or damage',
			'While we strive',
			'accurate and up-to-date',
			'completeness, accuracy',
			'reliability, or suitability'
		);
		
		foreach ( $saved_content as $key => $data ) {
			$should_remove = false;
			
			// Check selector
			$selector = isset( $data['selector'] ) ? $data['selector'] : '';
			foreach ( $legal_selectors as $legal_selector ) {
				if ( strpos( $selector, $legal_selector ) !== false ) {
					$should_remove = true;
					break;
				}
			}
			
			// Check content for Legal page keywords (more aggressive check)
			if ( ! $should_remove ) {
				$content = isset( $data['content'] ) ? $data['content'] : '';
				// Also check if data is a string directly
				if ( is_string( $data ) && ! isset( $data['content'] ) ) {
					$content = $data;
				}
				foreach ( $legal_content_keywords as $keyword ) {
					if ( stripos( $content, $keyword ) !== false ) {
						$should_remove = true;
						break;
					}
				}
			}
			
			if ( $should_remove ) {
				unset( $saved_content[ $key ] );
				delete_option( 'sme_quick_edit_' . $key );
				$content_changed = true;
			}
		}
		
		foreach ( $saved_styles as $key => $data ) {
			$selector = isset( $data['selector'] ) ? $data['selector'] : '';
			foreach ( $legal_selectors as $legal_selector ) {
				if ( strpos( $selector, $legal_selector ) !== false ) {
					unset( $saved_styles[ $key ] );
					delete_option( 'sme_quick_edit_styles_' . $key );
					$styles_changed = true;
					break;
				}
			}
		}
		
		if ( $content_changed ) {
			update_option( 'sme_quick_edit_content', $saved_content );
		}
		if ( $styles_changed ) {
			update_option( 'sme_quick_edit_styles', $saved_styles );
		}
	}
	
	public function reset_all_quick_edit() {
		delete_option( 'sme_quick_edit_content' );
		delete_option( 'sme_quick_edit_styles' );
		
		global $wpdb;
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND option_name != 'sme_quick_edit_content' AND option_name != 'sme_quick_edit_styles'",
			'sme_quick_edit_%'
		) );
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
			'sme_quick_edit_styles_%'
		) );
		
		delete_transient( 'sme_quick_edit_cleaned_problematic' );
		delete_transient( 'sme_quick_edit_cleaned_gradients' );
		
		return true;
	}
	
	public function remove_problematic_edits() {
		if ( get_transient( 'sme_quick_edit_cleaned_problematic' ) ) {
			return;
		}
		
		$problematic_keywords = array(
			'Deep analysis of market trends and expert legal advice',
			'Deep analysis',
			'expert legal advice',
			'market trends and expert legal'
		);
		
		$saved_content = get_option( 'sme_quick_edit_content', array() );
		if ( ! is_array( $saved_content ) ) {
			$saved_content = array();
		}
		
		$content_changed = false;
		$keys_to_remove = array();
		
		foreach ( $saved_content as $key => $data ) {
			$content = isset( $data['content'] ) ? $data['content'] : '';
			if ( is_string( $data ) ) {
				$content = $data;
			}
			
			// Check if content contains problematic keywords
			foreach ( $problematic_keywords as $keyword ) {
				if ( stripos( $content, $keyword ) !== false ) {
					$keys_to_remove[] = $key;
					$content_changed = true;
					break;
				}
			}
			
			// Also remove old edits without page_id that might be global
			if ( ! isset( $data['page_id'] ) || ( isset( $data['page_id'] ) && absint( $data['page_id'] ) === 0 ) ) {
				// Check if this old edit might be problematic
				if ( stripos( $content, 'Deep analysis' ) !== false || stripos( $content, 'expert legal' ) !== false ) {
					$keys_to_remove[] = $key;
					$content_changed = true;
				}
			}
		}
		
		// Remove problematic edits
		foreach ( $keys_to_remove as $key ) {
			unset( $saved_content[ $key ] );
			delete_option( 'sme_quick_edit_' . $key );
		}
		
		if ( $content_changed ) {
			update_option( 'sme_quick_edit_content', $saved_content );
		}
		
		// Also clean up styles for these elements
		$saved_styles = get_option( 'sme_quick_edit_styles', array() );
		if ( is_array( $saved_styles ) ) {
			$styles_changed = false;
			foreach ( $keys_to_remove as $key ) {
				if ( isset( $saved_styles[ $key ] ) ) {
					unset( $saved_styles[ $key ] );
					$styles_changed = true;
				}
			}
			if ( $styles_changed ) {
				update_option( 'sme_quick_edit_styles', $saved_styles );
			}
		}
		
		// Mark as cleaned (expires in 1 hour, but only runs once per site)
		set_transient( 'sme_quick_edit_cleaned_problematic', true, HOUR_IN_SECONDS );
	}
	
	/**
	 * Remove edits that destroyed gradient backgrounds
	 * This removes background-color from all style edits to preserve gradients
	 */
	public function remove_gradient_destroying_edits() {
		// Check if we've already cleaned up (using transient to run only once)
		if ( get_transient( 'sme_quick_edit_cleaned_gradients' ) ) {
			return;
		}
		
		$saved_styles = get_option( 'sme_quick_edit_styles', array() );
		if ( ! is_array( $saved_styles ) ) {
			$saved_styles = array();
		}
		
		$styles_changed = false;
		
		// Remove background-color from all style edits to preserve gradients
		foreach ( $saved_styles as $key => $data ) {
			$styles = isset( $data['styles'] ) ? $data['styles'] : array();
			
			// If styles is not an array, try to decode it
			if ( ! is_array( $styles ) && is_string( $styles ) ) {
				$styles = json_decode( $styles, true );
				if ( ! is_array( $styles ) ) {
					$styles = array();
				}
			}
			
			// Check if styles contain background-color (which destroys gradients)
			if ( is_array( $styles ) && isset( $styles['background-color'] ) ) {
				// Remove background-color to preserve gradients
				unset( $styles['background-color'] );
				$styles_changed = true;
				
				if ( empty( $styles ) ) {
					// If no other styles, remove entire edit
					unset( $saved_styles[ $key ] );
					delete_option( 'sme_quick_edit_styles_' . $key );
				} else {
					// Update styles without background-color
					$data['styles'] = $styles;
					$saved_styles[ $key ] = $data;
				}
			}
		}
		
		if ( $styles_changed ) {
			update_option( 'sme_quick_edit_styles', $saved_styles );
		}
		
		// Mark as cleaned (expires in 1 hour, but only runs once per site)
		set_transient( 'sme_quick_edit_cleaned_gradients', true, HOUR_IN_SECONDS );
	}
}

SME_Quick_Editor::get_instance();

