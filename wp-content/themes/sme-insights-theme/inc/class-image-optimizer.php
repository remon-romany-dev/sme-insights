<?php
/**
 * Image Optimizer
 * Automatically optimizes images on upload
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Image_Optimizer {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'optimize_on_upload' ) );
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'optimize_attachment_metadata' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_ajax_sme_bulk_optimize', array( $this, 'bulk_optimize_ajax' ) );
	}
	
	/**
	 * Optimize image on upload
	 */
	public function optimize_on_upload( $file ) {
		if ( ! $this->is_image( $file['type'] ) ) {
			return $file;
		}
		
		if ( ! extension_loaded( 'gd' ) || ! function_exists( 'imagecreatefromjpeg' ) ) {
			return $file;
		}
		
		if ( ! get_option( 'sme_image_optimizer_enabled', true ) ) {
			return $file;
		}
		
		$max_width = get_option( 'sme_image_max_width', 1920 );
		$max_height = get_option( 'sme_image_max_height', 1080 );
		$quality = get_option( 'sme_image_quality', 85 );
		
		$optimized = $this->resize_and_compress( $file['tmp_name'], $max_width, $max_height, $quality );
		
		if ( $optimized ) {
			$file['size'] = filesize( $file['tmp_name'] );
		}
		
		return $file;
	}
	
	/**
	 * Optimize attachment metadata (generate thumbnails with optimization)
	 */
	public function optimize_attachment_metadata( $metadata, $attachment_id ) {
		if ( ! extension_loaded( 'gd' ) || ! function_exists( 'imagecreatefromjpeg' ) ) {
			return $metadata;
		}
		
		if ( ! get_option( 'sme_image_optimizer_enabled', true ) ) {
			return $metadata;
		}
		
		if ( isset( $metadata['sizes'] ) && is_array( $metadata['sizes'] ) ) {
			$upload_dir = wp_upload_dir();
			$file_path = get_attached_file( $attachment_id );
			$file_dir = dirname( $file_path );
			
			foreach ( $metadata['sizes'] as $size => $size_data ) {
				$thumb_path = $file_dir . '/' . $size_data['file'];
				if ( file_exists( $thumb_path ) ) {
					$this->compress_image( $thumb_path, get_option( 'sme_image_quality', 85 ) );
				}
			}
		}
		
		if ( get_option( 'sme_auto_alt_text', true ) ) {
			$this->auto_generate_alt_text( $attachment_id );
		}
		
		return $metadata;
	}
	
	/**
	 * Check if file is an image
	 */
	private function is_image( $mime_type ) {
		$image_types = array( 'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp' );
		return in_array( $mime_type, $image_types, true );
	}
	
	/**
	 * Resize and compress image
	 */
	private function resize_and_compress( $file_path, $max_width, $max_height, $quality ) {
		if ( ! file_exists( $file_path ) ) {
			return false;
		}
		
		$image_info = getimagesize( $file_path );
		if ( ! $image_info ) {
			return false;
		}
		
		list( $width, $height, $type ) = $image_info;
		
		if ( $width <= $max_width && $height <= $max_height ) {
			return $this->compress_image( $file_path, $quality );
		}
		
		$ratio = min( $max_width / $width, $max_height / $height );
		$new_width = (int) ( $width * $ratio );
		$new_height = (int) ( $height * $ratio );
		
		switch ( $type ) {
			case IMAGETYPE_JPEG:
				$image = imagecreatefromjpeg( $file_path );
				break;
			case IMAGETYPE_PNG:
				$image = imagecreatefrompng( $file_path );
				break;
			case IMAGETYPE_GIF:
				$image = imagecreatefromgif( $file_path );
				break;
			case IMAGETYPE_WEBP:
				if ( function_exists( 'imagecreatefromwebp' ) ) {
					$image = imagecreatefromwebp( $file_path );
				} else {
					return false;
				}
				break;
			default:
				return false;
		}
		
		if ( ! $image ) {
			return false;
		}
		
		$new_image = imagecreatetruecolor( $new_width, $new_height );
		
		if ( $type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF ) {
			imagealphablending( $new_image, false );
			imagesavealpha( $new_image, true );
			$transparent = imagecolorallocatealpha( $new_image, 255, 255, 255, 127 );
			imagefilledrectangle( $new_image, 0, 0, $new_width, $new_height, $transparent );
		}
		
		imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
		
		$result = false;
		switch ( $type ) {
			case IMAGETYPE_JPEG:
				$result = imagejpeg( $new_image, $file_path, $quality );
				break;
			case IMAGETYPE_PNG:
				$png_quality = (int) ( 9 - ( $quality / 100 ) * 9 );
				$result = imagepng( $new_image, $file_path, $png_quality );
				break;
			case IMAGETYPE_GIF:
				$result = imagegif( $new_image, $file_path );
				break;
			case IMAGETYPE_WEBP:
				if ( function_exists( 'imagewebp' ) ) {
					$result = imagewebp( $new_image, $file_path, $quality );
				}
				break;
		}
		
		imagedestroy( $image );
		imagedestroy( $new_image );
		
		return $result;
	}
	
	/**
	 * Compress image without resizing
	 */
	private function compress_image( $file_path, $quality ) {
		if ( ! file_exists( $file_path ) ) {
			return false;
		}
		
		$image_info = getimagesize( $file_path );
		if ( ! $image_info ) {
			return false;
		}
		
		$type = $image_info[2];
		
		switch ( $type ) {
			case IMAGETYPE_JPEG:
				$image = imagecreatefromjpeg( $file_path );
				break;
			case IMAGETYPE_PNG:
				$image = imagecreatefrompng( $file_path );
				break;
			case IMAGETYPE_GIF:
				$image = imagecreatefromgif( $file_path );
				break;
			case IMAGETYPE_WEBP:
				if ( function_exists( 'imagecreatefromwebp' ) ) {
					$image = imagecreatefromwebp( $file_path );
				} else {
					return false;
				}
				break;
			default:
				return false;
		}
		
		if ( ! $image ) {
			return false;
		}
		
		$result = false;
		switch ( $type ) {
			case IMAGETYPE_JPEG:
				$result = imagejpeg( $image, $file_path, $quality );
				break;
			case IMAGETYPE_PNG:
				$png_quality = (int) ( 9 - ( $quality / 100 ) * 9 );
				$result = imagepng( $image, $file_path, $png_quality );
				break;
			case IMAGETYPE_GIF:
				$result = imagegif( $image, $file_path );
				break;
			case IMAGETYPE_WEBP:
				if ( function_exists( 'imagewebp' ) ) {
					$result = imagewebp( $image, $file_path, $quality );
				}
				break;
		}
		
		imagedestroy( $image );
		
		return $result;
	}
	
	/**
	 * Auto-generate alt text from post title or filename
	 */
	private function auto_generate_alt_text( $attachment_id ) {
		$current_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		if ( ! empty( $current_alt ) ) {
			return;
		}
		
		$attachment = get_post( $attachment_id );
		if ( ! $attachment ) {
			return;
		}
		
		$alt_text = $attachment->post_title;
		
		if ( empty( $alt_text ) ) {
			$file_path = get_attached_file( $attachment_id );
			$filename = basename( $file_path );
			$alt_text = pathinfo( $filename, PATHINFO_FILENAME );
		}
		
		$alt_text = sanitize_text_field( $alt_text );
		$alt_text = str_replace( array( '-', '_' ), ' ', $alt_text );
		$alt_text = ucwords( $alt_text );
		
		if ( ! empty( $alt_text ) ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt_text );
		}
	}
	
	/**
	 * Add admin menu
	 * Note: This is now handled by SME_Theme_Dashboard
	 * Keeping this for backward compatibility
	 */
	public function add_admin_menu() {
		// Menu is now added by SME_Theme_Dashboard
		// This method is kept for backward compatibility but does nothing
		// The dashboard will call render_admin_page() directly
	}
	
	/**
	 * Register settings
	 */
	public function register_settings() {
		register_setting( 'sme_image_optimizer', 'sme_image_optimizer_enabled' );
		register_setting( 'sme_image_optimizer', 'sme_image_max_width' );
		register_setting( 'sme_image_optimizer', 'sme_image_max_height' );
		register_setting( 'sme_image_optimizer', 'sme_image_quality' );
		register_setting( 'sme_image_optimizer', 'sme_auto_alt_text' );
		
		if ( get_option( 'sme_image_optimizer_enabled' ) === false ) {
			update_option( 'sme_image_optimizer_enabled', true );
		}
		if ( get_option( 'sme_image_max_width' ) === false ) {
			update_option( 'sme_image_max_width', 1920 );
		}
		if ( get_option( 'sme_image_max_height' ) === false ) {
			update_option( 'sme_image_max_height', 1080 );
		}
		if ( get_option( 'sme_image_quality' ) === false ) {
			update_option( 'sme_image_quality', 85 );
		}
		if ( get_option( 'sme_auto_alt_text' ) === false ) {
			update_option( 'sme_auto_alt_text', true );
		}
	}
	
	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Check if GD library is available
		$gd_available = extension_loaded( 'gd' ) && function_exists( 'imagecreatefromjpeg' );
		
		// Save settings
		if ( isset( $_POST['sme_save_settings'] ) && check_admin_referer( 'sme_image_optimizer_settings' ) ) {
			update_option( 'sme_image_optimizer_enabled', isset( $_POST['sme_image_optimizer_enabled'] ) );
			update_option( 'sme_image_max_width', intval( $_POST['sme_image_max_width'] ) );
			update_option( 'sme_image_max_height', intval( $_POST['sme_image_max_height'] ) );
			update_option( 'sme_image_quality', intval( $_POST['sme_image_quality'] ) );
			update_option( 'sme_auto_alt_text', isset( $_POST['sme_auto_alt_text'] ) );
			
			echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
		}
		
		$enabled = get_option( 'sme_image_optimizer_enabled', true );
		$max_width = get_option( 'sme_image_max_width', 1920 );
		$max_height = get_option( 'sme_image_max_height', 1080 );
		$quality = get_option( 'sme_image_quality', 85 );
		$auto_alt = get_option( 'sme_auto_alt_text', true );
		
		// Count images
		$total_images = $this->count_images();
		
		?>
		<div class="wrap">
			<h1>Image Optimizer</h1>
			<p class="description">Automatically optimize images on upload. Resize, compress, and add alt text automatically.</p>
			
			<div style="max-width: 1200px; margin-top: 30px;">
				<!-- GD Library Check -->
				<?php if ( ! $gd_available ) : ?>
					<div class="notice notice-error">
						<p><strong>Warning:</strong> PHP GD library is not available. Image optimization requires GD library. Please contact your hosting provider to enable it.</p>
					</div>
				<?php else : ?>
					<div class="notice notice-success">
						<p><strong>✓ PHP GD Library:</strong> Available and ready to optimize images.</p>
					</div>
				<?php endif; ?>
				
				<!-- Statistics -->
				<div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 30px;">
					<h2 style="margin-top: 0;">Statistics</h2>
					<p style="font-size: 18px;"><strong>Total Images:</strong> <?php echo esc_html( $total_images ); ?></p>
				</div>
				
				<!-- Settings Form -->
				<form method="post" action="">
					<?php wp_nonce_field( 'sme_image_optimizer_settings' ); ?>
					
					<div style="background: #fff; padding: 25px; border: 1px solid #ddd; border-radius: 8px;">
						<h2 style="margin-top: 0;">Settings</h2>
						
						<table class="form-table">
							<tr>
								<th scope="row">Enable Auto Optimization</th>
								<td>
									<label>
										<input type="checkbox" name="sme_image_optimizer_enabled" value="1" <?php checked( $enabled, true ); ?>>
										Automatically optimize images when uploaded
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row">Maximum Width</th>
								<td>
									<input type="number" name="sme_image_max_width" value="<?php echo esc_attr( $max_width ); ?>" min="800" max="4000" step="100">
									<p class="description">Maximum image width in pixels (default: 1920)</p>
								</td>
							</tr>
							<tr>
								<th scope="row">Maximum Height</th>
								<td>
									<input type="number" name="sme_image_max_height" value="<?php echo esc_attr( $max_height ); ?>" min="600" max="3000" step="100">
									<p class="description">Maximum image height in pixels (default: 1080)</p>
								</td>
							</tr>
							<tr>
								<th scope="row">Image Quality</th>
								<td>
									<input type="number" name="sme_image_quality" value="<?php echo esc_attr( $quality ); ?>" min="60" max="100" step="5">
									<p class="description">JPEG quality (60-100, default: 85). Lower = smaller file size but lower quality.</p>
								</td>
							</tr>
							<tr>
								<th scope="row">Auto Generate Alt Text</th>
								<td>
									<label>
										<input type="checkbox" name="sme_auto_alt_text" value="1" <?php checked( $auto_alt, true ); ?>>
										Automatically generate alt text from image title or filename
									</label>
								</td>
							</tr>
						</table>
						
						<?php submit_button( 'Save Settings', 'primary', 'sme_save_settings' ); ?>
					</div>
				</form>
				
				<!-- Regenerate Thumbnails Section -->
				<div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; margin-top: 30px;">
					<h2 style="margin-top: 0;">🔄 Regenerate Thumbnails</h2>
					<p class="description">Regenerate all image sizes for existing images. This is useful after adding new custom image sizes (like sme-breaking-news, sme-trending-small, sme-mobile-main).</p>
					
					<?php
					// Handle regeneration
					if ( isset( $_POST['sme_regenerate_thumbnails'] ) && check_admin_referer( 'sme_regenerate_thumbnails_action' ) ) {
						if ( ! extension_loaded( 'gd' ) || ! function_exists( 'imagecreatefromjpeg' ) ) {
							echo '<div class="notice notice-error"><p><strong>Error:</strong> PHP GD library is required to regenerate thumbnails.</p></div>';
						} else {
							echo '<div class="notice notice-info"><p><strong>Processing...</strong> Regenerating thumbnails. This may take a few minutes. Please wait...</p></div>';
							flush();
							
							// Set longer execution time for bulk operations
							set_time_limit( 300 );
							
							$regenerated = $this->regenerate_all_thumbnails();
							
							if ( $regenerated > 0 ) {
								echo '<div class="notice notice-success is-dismissible"><p><strong>✓ Success!</strong> Regenerated and optimized thumbnails for ' . esc_html( $regenerated ) . ' images.</p></div>';
							} else {
								echo '<div class="notice notice-warning"><p><strong>Warning:</strong> No images were regenerated. Please check that images exist.</p></div>';
							}
						}
					}
					?>
					
					<form method="post" action="" onsubmit="return confirm('This will regenerate all image thumbnails for <?php echo esc_js( $total_images ); ?> images. This may take several minutes. Continue?');">
						<?php wp_nonce_field( 'sme_regenerate_thumbnails_action' ); ?>
						<p>
							<strong>What will be regenerated:</strong>
						</p>
						<ul style="margin-left: 20px; line-height: 1.8;">
							<li>✓ Standard WordPress sizes (thumbnail, medium, large)</li>
							<li>✓ Custom theme sizes (sme-featured, sme-thumbnail, sme-medium)</li>
							<li>✓ <strong>New optimized sizes:</strong> sme-breaking-news, sme-trending-small, sme-mobile-main</li>
							<li>✓ All thumbnails will also be compressed for better performance</li>
						</ul>
						<p style="margin-top: 15px;">
							<?php submit_button( 'Regenerate All Thumbnails (' . esc_html( $total_images ) . ' images)', 'secondary', 'sme_regenerate_thumbnails', false ); ?>
						</p>
					</form>
				</div>
				
				<!-- Info Box -->
				<div style="background: #f0f6fc; padding: 20px; border-left: 4px solid #2271b1; margin-top: 30px; border-radius: 4px;">
					<h3 style="margin-top: 0;">ℹ️ How It Works</h3>
					<ul style="line-height: 1.8;">
						<li><strong>Automatic Optimization:</strong> When you upload an image, it's automatically resized (if too large) and compressed.</li>
						<li><strong>Alt Text Generation:</strong> If an image doesn't have alt text, it's automatically generated from the image title or filename.</li>
						<li><strong>Thumbnail Optimization:</strong> All WordPress-generated thumbnails are also optimized.</li>
						<li><strong>No External Services:</strong> All optimization happens on your server using PHP GD library.</li>
					</ul>
					<p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #c3c4c7;">
						<strong>💡 Tip:</strong> For best results, keep quality at 85-90. Lower values reduce file size but may affect image quality.
					</p>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Count total images
	 * Uses found_posts to avoid loading all posts into memory
	 */
	private function count_images() {
		$args = array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'posts_per_page' => 1, // Only need count, not posts
			'post_status'    => 'inherit',
			'fields'         => 'ids', // Only get IDs to reduce memory
		);
		
		$query = new WP_Query( $args );
		return $query->found_posts;
	}
	
	/**
	 * Bulk optimize AJAX handler
	 */
	public function bulk_optimize_ajax() {
		check_ajax_referer( 'sme_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}
		
		// This would be implemented for bulk optimization
		wp_send_json_success( array( 'message' => 'Bulk optimization feature coming soon' ) );
	}
	
	/**
	 * Regenerate thumbnails for all images
	 * This will regenerate all registered image sizes including new custom sizes
	 */
	public function regenerate_all_thumbnails() {
		if ( ! extension_loaded( 'gd' ) || ! function_exists( 'imagecreatefromjpeg' ) ) {
			return false;
		}
		
		$args = array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'posts_per_page' => -1,
			'post_status'    => 'inherit',
			'fields'         => 'ids',
		);
		
		$query = new WP_Query( $args );
		$regenerated = 0;
		
		if ( $query->have_posts() ) {
			foreach ( $query->posts as $attachment_id ) {
				$file_path = get_attached_file( $attachment_id );
				
				if ( ! $file_path || ! file_exists( $file_path ) ) {
					continue;
				}
				
				// Regenerate all image sizes
				$metadata = wp_generate_attachment_metadata( $attachment_id, $file_path );
				if ( $metadata && ! is_wp_error( $metadata ) ) {
					wp_update_attachment_metadata( $attachment_id, $metadata );
					
					// Optimize the regenerated thumbnails
					$this->optimize_attachment_metadata( $metadata, $attachment_id );
					
					$regenerated++;
				}
			}
		}
		
		return $regenerated;
	}
}

