<?php
/**
 * Post Meta Fields
 * Custom meta boxes for post settings using WordPress core
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Post_Meta {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_post_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post_meta' ) );
		add_filter( 'manage_edit-post_columns', array( $this, 'add_breaking_news_column' ), 10 );
		add_action( 'manage_post_posts_custom_column', array( $this, 'render_breaking_news_column' ), 10, 2 );
		add_filter( 'manage_edit-post_sortable_columns', array( $this, 'make_breaking_news_sortable' ) );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_breaking_news' ), 10, 2 );
		add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit_breaking_news' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_quick_edit' ) );
		add_action( 'wp_ajax_save_bulk_breaking_news', array( $this, 'save_bulk_breaking_news' ) );
		add_action( 'admin_footer', array( $this, 'add_quick_edit_script' ) );
	}
	
	/**
	 * Add post meta boxes
	 */
	public function add_post_meta_boxes() {
		add_meta_box(
			'sme_post_settings',
			__( 'Post Settings', 'sme-insights' ),
			array( $this, 'render_post_settings' ),
			'post',
			'side',
			'default'
		);
	}
	
	/**
	 * Render post settings
	 */
	public function render_post_settings( $post ) {
		wp_nonce_field( 'sme_post_meta', 'sme_post_meta_nonce' );
		
		$is_featured = get_post_meta( $post->ID, '_sme_is_featured', true );
		$is_breaking = get_post_meta( $post->ID, 'breaking_news', true );
		$custom_excerpt = get_post_meta( $post->ID, '_sme_custom_excerpt', true );
		$featured_image_alt = get_post_meta( $post->ID, '_sme_featured_image_alt', true );
		
		?>
		<div class="sme-post-settings">
			<p>
				<label>
					<input type="checkbox" name="sme_is_breaking" value="1" <?php checked( $is_breaking, '1' ); ?>>
					<strong>Breaking News</strong>
				</label>
				<br>
				<small>Mark this post as breaking news to display it in the header breaking news bar.</small>
			</p>
			
			<p>
				<label>
					<input type="checkbox" name="sme_is_featured" value="1" <?php checked( $is_featured, '1' ); ?>>
					<strong>Featured Post</strong>
				</label>
				<br>
				<small>Mark this post as featured to display it prominently.</small>
			</p>
			
			<p>
				<label>
					<strong>Custom Excerpt</strong>
					<textarea name="sme_custom_excerpt" rows="3" style="width: 100%; margin-top: 5px;"><?php echo esc_textarea( $custom_excerpt ); ?></textarea>
				</label>
				<br>
				<small>Override default excerpt with custom text.</small>
			</p>
			
			<p>
				<label>
					<strong>Featured Image Alt Text</strong>
					<input type="text" name="sme_featured_image_alt" value="<?php echo esc_attr( $featured_image_alt ); ?>" style="width: 100%; margin-top: 5px;">
				</label>
				<br>
				<small>Custom alt text for SEO (if different from image default).</small>
			</p>
		</div>
		<?php
	}
	
	/**
	 * Save post meta
	 */
	public function save_post_meta( $post_id ) {
		// Check nonce
		if ( ! isset( $_POST['sme_post_meta_nonce'] ) || ! wp_verify_nonce( $_POST['sme_post_meta_nonce'], 'sme_post_meta' ) ) {
			return;
		}
		
		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		// Save breaking news
		$is_breaking = isset( $_POST['sme_is_breaking'] ) ? '1' : '0';
		update_post_meta( $post_id, 'breaking_news', $is_breaking );
		
		// Save featured post
		$is_featured = isset( $_POST['sme_is_featured'] ) ? '1' : '0';
		update_post_meta( $post_id, '_sme_is_featured', $is_featured );
		
		// Save custom excerpt
		if ( isset( $_POST['sme_custom_excerpt'] ) ) {
			update_post_meta( $post_id, '_sme_custom_excerpt', sanitize_textarea_field( $_POST['sme_custom_excerpt'] ) );
		}
		
		// Save featured image alt
		if ( isset( $_POST['sme_featured_image_alt'] ) ) {
			update_post_meta( $post_id, '_sme_featured_image_alt', sanitize_text_field( $_POST['sme_featured_image_alt'] ) );
		}
	}
	
	/**
	 * Add Breaking News column to posts list
	 */
	public function add_breaking_news_column( $columns ) {
		// Just add the column if it doesn't exist
		// The ordering will be handled by sme_change_categories_column_name in functions.php
		if ( ! isset( $columns['breaking_news'] ) ) {
			$columns['breaking_news'] = __( 'Breaking News', 'sme-insights' );
		}
		return $columns;
	}
	
	/**
	 * Render Breaking News column
	 */
	public function render_breaking_news_column( $column, $post_id ) {
		if ( $column === 'breaking_news' ) {
			$is_breaking = get_post_meta( $post_id, 'breaking_news', true );
			$breaking_value = $is_breaking === '1' ? '1' : '0';
			if ( $is_breaking === '1' ) {
				echo '<span class="breaking-news-status" data-breaking="' . esc_attr( $breaking_value ) . '" style="color: #d63638; font-weight: bold;">● Breaking</span>';
			} else {
				echo '<span class="breaking-news-status" data-breaking="' . esc_attr( $breaking_value ) . '" style="color: #999;">—</span>';
			}
		}
	}
	
	/**
	 * Make Breaking News column sortable
	 */
	public function make_breaking_news_sortable( $columns ) {
		$columns['breaking_news'] = 'breaking_news';
		return $columns;
	}
	
	/**
	 * Quick Edit Breaking News
	 */
	public function quick_edit_breaking_news( $column_name, $post_type ) {
		if ( $column_name !== 'breaking_news' || $post_type !== 'post' ) {
			return;
		}
		?>
		<fieldset class="inline-edit-col-right">
			<div class="inline-edit-col">
				<label class="inline-edit-breaking-news">
					<input type="checkbox" name="sme_is_breaking" value="1">
					<span class="checkbox-title"><?php _e( 'Breaking News', 'sme-insights' ); ?></span>
				</label>
			</div>
		</fieldset>
		<?php
	}
	
	/**
	 * Bulk Edit Breaking News
	 */
	public function bulk_edit_breaking_news( $column_name, $post_type ) {
		if ( $column_name !== 'breaking_news' || $post_type !== 'post' ) {
			return;
		}
		?>
		<fieldset class="inline-edit-col-right">
			<div class="inline-edit-col">
				<label>
					<span class="title"><?php _e( 'Breaking News', 'sme-insights' ); ?></span>
					<select name="sme_bulk_breaking_news">
						<option value="-1"><?php _e( '— No Change —', 'sme-insights' ); ?></option>
						<option value="1"><?php _e( 'Mark as Breaking News', 'sme-insights' ); ?></option>
						<option value="0"><?php _e( 'Remove from Breaking News', 'sme-insights' ); ?></option>
					</select>
				</label>
			</div>
		</fieldset>
		<?php
	}
	
	/**
	 * Save Quick Edit
	 */
	public function save_quick_edit( $post_id ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		if ( isset( $_POST['sme_is_breaking'] ) ) {
			update_post_meta( $post_id, 'breaking_news', '1' );
		} elseif ( isset( $_POST['_inline_edit'] ) ) {
			// Only update if it's a quick edit
			update_post_meta( $post_id, 'breaking_news', '0' );
		}
	}
	
	/**
	 * Save Bulk Edit Breaking News
	 */
	public function save_bulk_breaking_news() {
		check_ajax_referer( 'bulk-posts' );
		
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( __( 'You do not have permission to edit posts.', 'sme-insights' ) );
		}
		
		$post_ids = isset( $_POST['post_ids'] ) ? array_map( 'intval', $_POST['post_ids'] ) : array();
		$breaking_value = isset( $_POST['breaking_value'] ) ? sanitize_text_field( $_POST['breaking_value'] ) : '-1';
		
		if ( $breaking_value === '-1' ) {
			wp_send_json_error( array( 'message' => __( 'No change selected.', 'sme-insights' ) ) );
		}
		
		foreach ( $post_ids as $post_id ) {
			if ( current_user_can( 'edit_post', $post_id ) ) {
				update_post_meta( $post_id, 'breaking_news', $breaking_value );
			}
		}
		
		wp_send_json_success( array( 'message' => __( 'Breaking News status updated.', 'sme-insights' ) ) );
	}
	
	/**
	 * Add JavaScript for Quick Edit
	 */
	public function add_quick_edit_script() {
		$screen = get_current_screen();
		if ( $screen && $screen->id === 'edit-post' ) {
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				// Populate Quick Edit with current Breaking News value
				$('#the-list').on('click', '.editinline', function() {
					var $row = $(this).closest('tr');
					var $breakingStatus = $row.find('.breaking-news-status');
					var $checkbox = $('input[name="sme_is_breaking"]', '.inline-edit-row');
					
					if ($breakingStatus.length && $breakingStatus.data('breaking') === '1') {
						$checkbox.prop('checked', true);
					} else {
						$checkbox.prop('checked', false);
					}
				});
				
				// Handle Bulk Edit save
				$(document).on('click', '#bulk_edit', function() {
					var $bulkRow = $('#bulk-edit');
					var $postIds = [];
					$bulkRow.find('input[name="post[]"]:checked').each(function() {
						$postIds.push($(this).val());
					});
					
					var $breakingValue = $('select[name="sme_bulk_breaking_news"]', $bulkRow).val();
					
					if ($breakingValue !== '-1' && $postIds.length > 0) {
						$.ajax({
							url: ajaxurl,
							type: 'POST',
							data: {
								action: 'save_bulk_breaking_news',
								post_ids: $postIds,
								breaking_value: $breakingValue,
								_ajax_nonce: '<?php echo wp_create_nonce( "bulk-posts" ); ?>'
							},
							success: function(response) {
								if (response.success) {
									location.reload();
								}
							}
						});
					}
				});
			});
			</script>
			<?php
		}
	}
}

