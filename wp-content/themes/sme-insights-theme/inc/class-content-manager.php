<?php
/**
 * Content Manager
 * Dashboard page for managing content (delete posts, images, page content, re-import)
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Content_Manager {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'admin_post_sme_delete_posts', array( $this, 'handle_delete_posts' ) );
		add_action( 'admin_post_sme_delete_page_content', array( $this, 'handle_delete_page_content' ) );
		add_action( 'admin_post_sme_delete_pages', array( $this, 'handle_delete_pages' ) );
		add_action( 'admin_post_sme_reimport_content', array( $this, 'handle_reimport_content' ) );
		add_action( 'admin_post_sme_restore_pages', array( $this, 'handle_restore_pages' ) );
	}
	
	/**
	 * Add admin menu page
	 * Note: This is now handled by SME_Theme_Dashboard
	 * Keeping this for backward compatibility
	 */
	public function add_admin_menu() {
		// Menu is now added by SME_Theme_Dashboard
		// This method is kept for backward compatibility but does nothing
		// The dashboard will call render_admin_page() directly
	}
	
	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Show success/error messages
		if ( isset( $_GET['deleted'] ) && $_GET['deleted'] === 'posts' ) {
			$count = isset( $_GET['count'] ) ? absint( $_GET['count'] ) : 0;
			$images = isset( $_GET['images'] ) ? absint( $_GET['images'] ) : 0;
			$meta = isset( $_GET['meta'] ) ? absint( $_GET['meta'] ) : 0;
			$comments = isset( $_GET['comments'] ) ? absint( $_GET['comments'] ) : 0;
			$message = sprintf(
				'Successfully deleted %d posts, %d images, %d meta entries, and %d comments.',
				$count,
				$images,
				$meta,
				$comments
			);
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
		}
		
		if ( isset( $_GET['cleared'] ) && $_GET['cleared'] === 'pages' ) {
			$count = isset( $_GET['count'] ) ? intval( $_GET['count'] ) : 0;
			echo '<div class="notice notice-success is-dismissible"><p>Successfully cleared content from ' . esc_html( $count ) . ' pages. Templates are preserved.</p></div>';
		}
		
		if ( isset( $_GET['deleted'] ) && $_GET['deleted'] === 'pages' ) {
			$count = isset( $_GET['count'] ) ? intval( $_GET['count'] ) : 0;
			echo '<div class="notice notice-success is-dismissible"><p>Successfully deleted ' . esc_html( $count ) . ' pages. Templates are still available for future use.</p></div>';
		}
		
		if ( isset( $_GET['restored'] ) && $_GET['restored'] === 'success' ) {
			$count = isset( $_GET['count'] ) ? intval( $_GET['count'] ) : 0;
			echo '<div class="notice notice-success is-dismissible"><p>Successfully restored ' . esc_html( $count ) . ' pages from trash. You can now edit them.</p></div>';
		}
		
		if ( isset( $_GET['restored'] ) && $_GET['restored'] === 'none' ) {
			$errors = isset( $_GET['errors'] ) ? intval( $_GET['errors'] ) : 0;
			if ( $errors > 0 ) {
				echo '<div class="notice notice-error is-dismissible"><p>No pages were restored. There were ' . esc_html( $errors ) . ' errors. Please check if there are any pages in trash.</p></div>';
			} else {
				echo '<div class="notice notice-info is-dismissible"><p>No pages found in trash. All pages are already published.</p></div>';
			}
		}
		
		if ( isset( $_GET['reimported'] ) && $_GET['reimported'] === 'success' ) {
			echo '<div class="notice notice-success is-dismissible"><p>Content re-imported successfully! All pages have been restored and are ready to edit.</p></div>';
		}
		
		$posts_count = wp_count_posts( 'post' )->publish;
		$pages_count = wp_count_posts( 'page' )->publish;
		$media_count = wp_count_posts( 'attachment' )->inherit;
		?>
		<div class="wrap">
			<h1>Content Manager</h1>
			<p class="description">Manage your theme content: delete posts, images, page content, and re-import everything.</p>
			
			<div class="sme-content-manager" style="max-width: 1200px; margin-top: 30px;">
				
				<!-- Statistics -->
				<div class="sme-stats" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
					<div class="stat-box" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
						<h3 style="margin: 0 0 10px; color: #2271b1;">Posts</h3>
						<p style="font-size: 2rem; font-weight: 700; margin: 0; color: #2271b1;"><?php echo esc_html( $posts_count ); ?></p>
					</div>
					<div class="stat-box" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
						<h3 style="margin: 0 0 10px; color: #2271b1;">Pages</h3>
						<p style="font-size: 2rem; font-weight: 700; margin: 0; color: #2271b1;"><?php echo esc_html( $pages_count ); ?></p>
					</div>
					<div class="stat-box" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
						<h3 style="margin: 0 0 10px; color: #2271b1;">Media</h3>
						<p style="font-size: 2rem; font-weight: 700; margin: 0; color: #2271b1;"><?php echo esc_html( $media_count ); ?></p>
					</div>
				</div>
				
				<!-- Actions -->
				<div class="sme-actions" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
					
					<!-- Delete Posts & Images -->
					<div class="action-card" style="background: #fff; padding: 25px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
						<h2 style="margin-top: 0; color: #d63638;">Delete Posts & Images</h2>
						<p style="color: #646970; line-height: 1.6;">
							This will permanently delete all posts and their featured images from your site. This action cannot be undone.
						</p>
						<p style="color: #d63638; font-weight: 600;">
							⚠️ Warning: This will delete <?php echo esc_html( $posts_count ); ?> posts and their images.
						</p>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Are you sure you want to delete all posts and images? This cannot be undone!');">
							<?php wp_nonce_field( 'sme_delete_posts', 'sme_delete_posts_nonce' ); ?>
							<input type="hidden" name="action" value="sme_delete_posts">
							<button type="submit" class="button button-secondary" style="background: #d63638; color: #fff; border-color: #d63638; margin-top: 15px;">
								Delete All Posts & Images
							</button>
						</form>
					</div>
					
					<!-- Delete Page Content -->
					<div class="action-card" style="background: #fff; padding: 25px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
						<h2 style="margin-top: 0; color: #d63638;">Clear Page Content</h2>
						<p style="color: #646970; line-height: 1.6;">
							This will clear the content of all pages but keep the pages and their templates. Useful for resetting page content while preserving page structure.
						</p>
						<p style="color: #2271b1; font-weight: 600;">
							ℹ️ Pages and templates will remain, only content will be cleared.
						</p>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Are you sure you want to clear all page content? This cannot be undone!');">
							<?php wp_nonce_field( 'sme_delete_page_content', 'sme_delete_page_content_nonce' ); ?>
							<input type="hidden" name="action" value="sme_delete_page_content">
							<button type="submit" class="button button-secondary" style="background: #d63638; color: #fff; border-color: #d63638; margin-top: 15px;">
								Clear All Page Content
							</button>
						</form>
					</div>
					
					<!-- Delete All Pages -->
					<div class="action-card" style="background: #fff; padding: 25px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
						<h2 style="margin-top: 0; color: #d63638;">Delete All Pages</h2>
						<p style="color: #646970; line-height: 1.6;">
							This will permanently delete all pages from your site. Templates will remain available but pages will need to be recreated.
						</p>
						<p style="color: #d63638; font-weight: 600;">
							⚠️ Warning: This will delete <?php echo esc_html( $pages_count ); ?> pages. Templates will remain but pages will be gone.
						</p>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Are you sure you want to delete all pages? This cannot be undone!');">
							<?php wp_nonce_field( 'sme_delete_pages', 'sme_delete_pages_nonce' ); ?>
							<input type="hidden" name="action" value="sme_delete_pages">
							<button type="submit" class="button button-secondary" style="background: #d63638; color: #fff; border-color: #d63638; margin-top: 15px;">
								Delete All Pages
							</button>
						</form>
					</div>
					
					<!-- Restore Pages from Trash -->
					<div class="action-card" style="background: #fff; padding: 25px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); grid-column: 1 / -1;">
						<h2 style="margin-top: 0; color: #d63638;">Restore Pages from Trash</h2>
						<p style="color: #646970; line-height: 1.6;">
							If your pages (Contact, About, etc.) are in the trash and you cannot edit them, use this button to restore them.
						</p>
						<p style="color: #00a32a; font-weight: 600;">
							✅ This will restore all pages from trash and make them editable again.
						</p>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Are you sure you want to restore all pages from trash?');">
							<?php wp_nonce_field( 'sme_restore_pages', 'sme_restore_pages_nonce' ); ?>
							<input type="hidden" name="action" value="sme_restore_pages">
							<button type="submit" class="button button-secondary" style="background: #f0f0f1; color: #2c3338; border-color: #dcdcde; margin-top: 15px; padding: 10px 20px; font-size: 14px;">
								Restore Pages from Trash
							</button>
						</form>
					</div>
					
					<!-- Re-import Content -->
					<div class="action-card" style="background: #fff; padding: 25px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); grid-column: 1 / -1;">
						<h2 style="margin-top: 0; color: #2271b1;">Re-import Content</h2>
						<p style="color: #646970; line-height: 1.6;">
							Re-import all content: pages, posts, categories, tags, and images. This will create new content or update existing content. This will also restore pages from trash.
						</p>
						<p style="color: #00a32a; font-weight: 600;">
							✅ Safe: This will not delete existing content, only add or update. Pages in trash will be restored.
						</p>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<?php wp_nonce_field( 'sme_reimport_content', 'sme_reimport_content_nonce' ); ?>
							<input type="hidden" name="action" value="sme_reimport_content">
							<button type="submit" class="button button-primary" style="background: #2271b1; color: #fff; border-color: #2271b1; margin-top: 15px; padding: 10px 20px; font-size: 14px;">
								Re-import All Content
							</button>
						</form>
					</div>
					
				</div>
				
				<!-- Instructions -->
				<div class="sme-instructions" style="background: #f0f6fc; padding: 20px; border-left: 4px solid #2271b1; margin-top: 30px; border-radius: 4px;">
					<h3 style="margin-top: 0;">📋 Instructions</h3>
					<ol style="line-height: 1.8;">
						<li><strong>Delete Posts & Images:</strong> Permanently removes all posts and their featured images. Use this to start fresh.</li>
						<li><strong>Clear Page Content:</strong> Removes content from pages but keeps pages and templates. Templates will remain intact.</li>
						<li><strong>Delete All Pages:</strong> Permanently deletes all pages. Templates will remain available but pages will need to be recreated.</li>
						<li><strong>Re-import Content:</strong> Imports all content (pages, posts, images, categories, tags). Safe to use multiple times.</li>
					</ol>
					<p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #c3c4c7;">
						<strong>💡 Tip:</strong> You can delete posts/images/pages, then re-import to get fresh content with new images. Templates will always remain available.
					</p>
				</div>
				
			</div>
		</div>
		<?php
	}
	
	/**
	 * Handle delete posts action
	 * Deletes all posts, their featured images, attached images, post meta, comments, and term relationships
	 */
	public function handle_delete_posts() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}
		
		check_admin_referer( 'sme_delete_posts', 'sme_delete_posts_nonce' );
		
		@set_time_limit( 600 );
		@ini_set( 'memory_limit', '512M' );
		
		// Disable trash to force permanent deletion
		// We'll delete directly from DB, so this is just a safety measure
		
		// Add filter to prevent posts from going to trash
		add_filter( 'pre_trash_post', '__return_false', 999 );
		add_filter( 'pre_delete_post', function( $delete, $post, $force_delete ) {
			// Allow deletion if force_delete is true or if we're in our delete function
			return $force_delete ? $delete : false;
		}, 999, 3 );
		
		global $wpdb;
		
		$deleted_count = 0;
		$images_deleted = 0;
		$attachments_deleted = 0;
		$meta_deleted = 0;
		$comments_deleted = 0;
		
		$offset = 0;
		$posts_per_page = 100;
		
		// Delete all posts regardless of status (publish, draft, trash, private, etc.)
		// Only exclude auto-draft to avoid deleting unsaved drafts
		// Include trash posts to delete them permanently
		// Use a simpler query that gets all posts except auto-draft
		while ( true ) {
			// Get all post IDs except auto-draft (including trash, draft, publish, etc.)
			$post_ids = $wpdb->get_col( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} 
				WHERE post_type = %s 
				AND post_status != %s
				ORDER BY ID ASC
				LIMIT %d OFFSET %d",
				'post',
				'auto-draft',
				$posts_per_page,
				$offset
			) );
			
			if ( empty( $post_ids ) ) {
				break;
			}
			
			foreach ( $post_ids as $post_id ) {
				$post_id = absint( $post_id );
				
				$thumbnail_id = get_post_thumbnail_id( $post_id );
				if ( $thumbnail_id ) {
					$deleted = wp_delete_attachment( $thumbnail_id, true );
					if ( $deleted ) {
						$images_deleted++;
					}
				}
				
				$attachments = $wpdb->get_col( $wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} 
					WHERE post_type = %s 
					AND post_parent = %d",
					'attachment',
					$post_id
				) );
				
				if ( ! empty( $attachments ) ) {
					foreach ( $attachments as $attachment_id ) {
						$deleted = wp_delete_attachment( absint( $attachment_id ), true );
						if ( $deleted ) {
							$attachments_deleted++;
						}
					}
				}
				
				$meta_deleted_count = $wpdb->query( $wpdb->prepare(
					"DELETE FROM {$wpdb->postmeta} WHERE post_id = %d",
					$post_id
				) );
				if ( $meta_deleted_count !== false ) {
					$meta_deleted += $meta_deleted_count;
				}
				
				$comments = $wpdb->get_col( $wpdb->prepare(
					"SELECT comment_ID FROM {$wpdb->comments} WHERE comment_post_ID = %d",
					$post_id
				) );
				if ( ! empty( $comments ) ) {
					foreach ( $comments as $comment_id ) {
						wp_delete_comment( absint( $comment_id ), true );
						$comments_deleted++;
					}
				}
				
				$wpdb->delete( $wpdb->term_relationships, array( 'object_id' => $post_id ), array( '%d' ) );
				
				// Force delete post directly from database (bypass trash)
				// Use direct SQL DELETE to bypass WordPress hooks and filters
				$deleted = $wpdb->query( $wpdb->prepare(
					"DELETE FROM {$wpdb->posts} WHERE ID = %d",
					$post_id
				) );
				
				if ( $deleted !== false ) {
					$deleted_count++;
				}
				
				clean_post_cache( $post_id );
				wp_cache_delete( $post_id, 'posts' );
			}
			
			if ( count( $post_ids ) < $posts_per_page ) {
				break;
			}
			
			$offset += $posts_per_page;
		}
		
		// Final cleanup: Delete any remaining posts that might have been missed
		// This handles edge cases where posts might not have been deleted properly
		$remaining_posts = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} 
			WHERE post_type = %s 
			AND post_status != %s",
			'post',
			'auto-draft'
		) );
		
		if ( ! empty( $remaining_posts ) ) {
			foreach ( $remaining_posts as $post_id ) {
				$post_id = absint( $post_id );
				
				// Delete post meta
				$wpdb->delete( $wpdb->postmeta, array( 'post_id' => $post_id ), array( '%d' ) );
				
				// Delete term relationships
				$wpdb->delete( $wpdb->term_relationships, array( 'object_id' => $post_id ), array( '%d' ) );
				
				// Delete comments
				$comments = $wpdb->get_col( $wpdb->prepare(
					"SELECT comment_ID FROM {$wpdb->comments} WHERE comment_post_ID = %d",
					$post_id
				) );
				if ( ! empty( $comments ) ) {
					foreach ( $comments as $comment_id ) {
						wp_delete_comment( absint( $comment_id ), true );
					}
				}
				
				// Force delete from database using direct SQL (bypass trash)
				$wpdb->query( $wpdb->prepare(
					"DELETE FROM {$wpdb->posts} WHERE ID = %d",
					$post_id
				) );
				$deleted_count++;
				
				clean_post_cache( $post_id );
				wp_cache_delete( $post_id, 'posts' );
			}
		}
		
		// Final check: Delete any posts that might still be in trash
		// This handles cases where posts were moved to trash instead of being deleted
		$trashed_posts = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} 
			WHERE post_type = %s 
			AND post_status = %s",
			'post',
			'trash'
		) );
		
		if ( ! empty( $trashed_posts ) ) {
			foreach ( $trashed_posts as $post_id ) {
				$post_id = absint( $post_id );
				
				// Delete post meta
				$wpdb->delete( $wpdb->postmeta, array( 'post_id' => $post_id ), array( '%d' ) );
				
				// Delete term relationships
				$wpdb->delete( $wpdb->term_relationships, array( 'object_id' => $post_id ), array( '%d' ) );
				
				// Delete comments
				$comments = $wpdb->get_col( $wpdb->prepare(
					"SELECT comment_ID FROM {$wpdb->comments} WHERE comment_post_ID = %d",
					$post_id
				) );
				if ( ! empty( $comments ) ) {
					foreach ( $comments as $comment_id ) {
						wp_delete_comment( absint( $comment_id ), true );
					}
				}
				
				// Force delete from database
				$wpdb->delete( $wpdb->posts, array( 'ID' => $post_id ), array( '%d' ) );
				$deleted_count++;
				
				clean_post_cache( $post_id );
				wp_cache_delete( $post_id, 'posts' );
			}
		}
		
		// Delete all remaining images (orphaned or attached to deleted posts)
		// Get all image attachments
		$all_image_attachments = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} 
			WHERE post_type = %s 
			AND post_mime_type LIKE %s",
			'attachment',
			'image/%'
		) );
		
		if ( ! empty( $all_image_attachments ) ) {
			$upload_dir = wp_upload_dir();
			
			foreach ( $all_image_attachments as $attachment_id ) {
				$attachment_id = absint( $attachment_id );
				
				// Get attachment metadata for file deletion
				$attachment_meta = $wpdb->get_var( $wpdb->prepare(
					"SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s",
					$attachment_id,
					'_wp_attached_file'
				) );
				
				// Delete physical files first
				if ( $attachment_meta ) {
					$file_path = $upload_dir['basedir'] . '/' . $attachment_meta;
					if ( file_exists( $file_path ) ) {
						@unlink( $file_path );
					}
					
					// Delete all image sizes
					$metadata = $wpdb->get_var( $wpdb->prepare(
						"SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s",
						$attachment_id,
						'_wp_attachment_metadata'
					) );
					
					if ( $metadata ) {
						$metadata = maybe_unserialize( $metadata );
						if ( isset( $metadata['sizes'] ) && is_array( $metadata['sizes'] ) ) {
							foreach ( $metadata['sizes'] as $size ) {
								if ( isset( $size['file'] ) ) {
									$size_path = $upload_dir['basedir'] . '/' . dirname( $attachment_meta ) . '/' . $size['file'];
									if ( file_exists( $size_path ) ) {
										@unlink( $size_path );
									}
								}
							}
						}
					}
				}
				
				// Delete attachment from database
				$deleted = wp_delete_attachment( $attachment_id, true );
				if ( $deleted ) {
					$attachments_deleted++;
				} else {
					// Force delete if wp_delete_attachment failed
					$wpdb->delete( $wpdb->posts, array( 'ID' => $attachment_id ), array( '%d' ) );
					$wpdb->delete( $wpdb->postmeta, array( 'post_id' => $attachment_id ), array( '%d' ) );
					$wpdb->delete( $wpdb->term_relationships, array( 'object_id' => $attachment_id ), array( '%d' ) );
					$attachments_deleted++;
				}
			}
		}
		
		// Final cleanup: Delete any remaining orphaned image files in uploads directory
		// This handles cases where files exist but database records are missing
		$upload_dir = wp_upload_dir();
		$upload_path = $upload_dir['basedir'];
		
		// Get all image files in uploads directory that might be orphaned
		// Note: This is a safety measure, be careful with recursive deletion
		if ( is_dir( $upload_path ) ) {
			$year_month_dirs = glob( $upload_path . '/[0-9][0-9][0-9][0-9]/*', GLOB_ONLYDIR );
			if ( ! empty( $year_month_dirs ) ) {
				foreach ( $year_month_dirs as $dir ) {
					$image_files = glob( $dir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE );
					if ( ! empty( $image_files ) ) {
						foreach ( $image_files as $file ) {
							$filename = basename( $file );
							// Check if this file has a database record
							$file_exists_in_db = $wpdb->get_var( $wpdb->prepare(
								"SELECT COUNT(*) FROM {$wpdb->postmeta} 
								WHERE meta_key = %s 
								AND meta_value LIKE %s",
								'_wp_attached_file',
								'%' . $wpdb->esc_like( $filename ) . '%'
							) );
							
							// If no database record exists, delete the file
							if ( ! $file_exists_in_db ) {
								@unlink( $file );
							}
						}
					}
				}
			}
		}
		
		// Clear all cache
		if ( class_exists( 'SME_Cache_Helper' ) ) {
			SME_Cache_Helper::clear_all_cache();
		} else {
			wp_cache_flush();
			clean_post_cache( 0 );
		}
		
		// 10. Update term counts
		$taxonomies = get_taxonomies( array( 'public' => true ), 'names' );
		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms = get_terms( array(
					'taxonomy' => $taxonomy,
					'hide_empty' => false,
				) );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						wp_update_term_count_now( array( $term->term_id ), $taxonomy );
					}
				}
			}
		}
		
		// Remove filters we added
		remove_filter( 'pre_trash_post', '__return_false', 999 );
		remove_all_filters( 'pre_delete_post' );
		
		// Redirect with success message
		$redirect_url = add_query_arg( array(
			'page' => 'sme-content-manager',
			'deleted' => 'posts',
			'count' => $deleted_count,
			'images' => $images_deleted + $attachments_deleted,
			'meta' => $meta_deleted,
			'comments' => $comments_deleted,
		), admin_url( 'admin.php' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}
	
	/**
	 * Handle delete page content action
	 */
	public function handle_delete_page_content() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}
		
		check_admin_referer( 'sme_delete_page_content', 'sme_delete_page_content_nonce' );
		
		// Get all pages
		$pages = get_pages( array(
			'post_status' => 'any',
			'number' => -1,
		) );
		
		$cleared_count = 0;
		
		if ( ! empty( $pages ) ) {
			global $wpdb;
			
			foreach ( $pages as $page ) {
				// Use direct database update to avoid hooks interference
				$updated = $wpdb->update(
					$wpdb->posts,
					array( 'post_content' => '' ),
					array( 'ID' => $page->ID ),
					array( '%s' ),
					array( '%d' )
				);
				
				if ( $updated !== false ) {
					// Clean post cache
					clean_post_cache( $page->ID );
					$cleared_count++;
				}
			}
		}
		
		// Redirect with success message
		$redirect_url = add_query_arg( array(
			'page' => 'sme-content-manager',
			'cleared' => 'pages',
			'count' => $cleared_count,
		), admin_url( 'admin.php' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}
	
	/**
	 * Handle delete pages action
	 */
	public function handle_delete_pages() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}
		
		check_admin_referer( 'sme_delete_pages', 'sme_delete_pages_nonce' );
		
		global $wpdb;
		
		// Get all page IDs directly from database
		$page_ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s",
			'page'
		) );
		
		$deleted_count = 0;
		
		if ( ! empty( $page_ids ) ) {
			foreach ( $page_ids as $page_id ) {
				// Force delete (bypass trash) - permanently delete
				$result = wp_delete_post( $page_id, true );
				
				// If wp_delete_post failed, use direct database deletion
				if ( ! $result || is_wp_error( $result ) ) {
					// Delete from posts table
					$wpdb->delete( $wpdb->posts, array( 'ID' => $page_id ), array( '%d' ) );
					// Delete from postmeta table
					$wpdb->delete( $wpdb->postmeta, array( 'post_id' => $page_id ), array( '%d' ) );
					// Delete from term_relationships (categories, tags)
					$wpdb->delete( $wpdb->term_relationships, array( 'object_id' => $page_id ), array( '%d' ) );
					// Clean cache
					clean_post_cache( $page_id );
					wp_cache_delete( $page_id, 'posts' );
				}
				$deleted_count++;
			}
		}
		
		// Redirect with success message
		$redirect_url = add_query_arg( array(
			'page' => 'sme-content-manager',
			'deleted' => 'pages',
			'count' => $deleted_count,
		), admin_url( 'admin.php' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}
	
	/**
	 * Handle restore pages from trash action
	 */
	public function handle_restore_pages() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}
		
		check_admin_referer( 'sme_restore_pages', 'sme_restore_pages_nonce' );
		
		global $wpdb;
		
		// Get all pages in trash directly from database (including any status that might be trash)
		$trashed_page_ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s",
			'page',
			'trash'
		) );
		
		$restored_count = 0;
		$errors = array();
		
		if ( ! empty( $trashed_page_ids ) ) {
			foreach ( $trashed_page_ids as $page_id ) {
				$page_id = intval( $page_id );
				
				if ( ! $page_id ) {
					continue;
				}
				
				// Try wp_untrash_post first
				$untrashed = wp_untrash_post( $page_id );
				
				if ( $untrashed && ! is_wp_error( $untrashed ) ) {
					// Update status to publish
					$updated = wp_update_post( array(
						'ID'          => $page_id,
						'post_status' => 'publish',
					), true );
					
					if ( is_wp_error( $updated ) ) {
						// If wp_update_post failed, use direct database update
						$db_updated = $wpdb->update(
							$wpdb->posts,
							array( 'post_status' => 'publish' ),
							array( 'ID' => $page_id ),
							array( '%s' ),
							array( '%d' )
						);
						
						if ( $db_updated !== false ) {
							clean_post_cache( $page_id );
							wp_cache_delete( $page_id, 'posts' );
							$restored_count++;
						} else {
							$errors[] = sprintf( 'Failed to restore page ID %d', $page_id );
						}
					} else {
						clean_post_cache( $page_id );
						wp_cache_delete( $page_id, 'posts' );
						$restored_count++;
					}
				} else {
					// If wp_untrash_post failed, use direct database update
					$db_updated = $wpdb->update(
						$wpdb->posts,
						array( 'post_status' => 'publish' ),
						array( 'ID' => $page_id, 'post_status' => 'trash' ),
						array( '%s' ),
						array( '%d', '%s' )
					);
					
					if ( $db_updated !== false ) {
						// Also update post_modified and post_modified_gmt
						$wpdb->update(
							$wpdb->posts,
							array( 
								'post_modified' => current_time( 'mysql' ),
								'post_modified_gmt' => current_time( 'mysql', 1 ),
							),
							array( 'ID' => $page_id ),
							array( '%s', '%s' ),
							array( '%d' )
						);
						
						clean_post_cache( $page_id );
						wp_cache_delete( $page_id, 'posts' );
						$restored_count++;
					} else {
						$errors[] = sprintf( 'Failed to restore page ID %d from database', $page_id );
					}
				}
			}
		}
		
		// Clear all caches
		if ( class_exists( 'SME_Cache_Helper' ) ) {
			SME_Cache_Helper::clear_all_cache();
		} else {
			wp_cache_flush();
			clean_post_cache( 0 );
		}
		
		// Flush rewrite rules to ensure URLs work
		flush_rewrite_rules( false );
		
		// Build redirect URL
		$redirect_args = array(
			'page' => 'sme-content-manager',
		);
		
		if ( $restored_count > 0 ) {
			$redirect_args['restored'] = 'success';
			$redirect_args['count'] = $restored_count;
		} else {
			$redirect_args['restored'] = 'none';
			if ( ! empty( $errors ) ) {
				$redirect_args['errors'] = count( $errors );
			}
		}
		
		$redirect_url = add_query_arg( $redirect_args, admin_url( 'admin.php' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}
	
	/**
	 * Handle re-import content action
	 */
	public function handle_reimport_content() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}
		
		check_admin_referer( 'sme_reimport_content', 'sme_reimport_content_nonce' );
		
		// Re-import content (this will also restore pages from trash)
		if ( class_exists( 'SME_Content_Importer' ) ) {
			$importer = SME_Content_Importer::get_instance();
			$importer->import_all_content();
		}
		
		// Redirect with success message
		$redirect_url = add_query_arg( array(
			'page' => 'sme-content-manager',
			'reimported' => 'success',
		), admin_url( 'admin.php' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}
}

