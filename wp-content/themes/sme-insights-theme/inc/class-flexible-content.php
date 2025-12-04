<?php
/**
 * Custom Flexible Content System
 * Built-in page builder using WordPress core features
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Flexible_Content {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_flexible_content' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}
	
	/**
	 * Add meta boxes
	 */
	public function add_meta_boxes() {
		global $post;
		
		// Don't show Page Builder for Niche Topic Pages (they use template)
		if ( $post ) {
			$template = get_page_template_slug( $post->ID );
			if ( $template === 'page-niche-topic.php' ) {
				return; // Don't show Page Builder for niche topic pages
			}
		}
		
		add_meta_box(
			'sme_page_builder',
			__( 'Page Builder', 'sme-insights' ),
			array( $this, 'render_page_builder' ),
			array( 'page', 'post' ),
			'normal',
			'high'
		);
	}
	
	/**
	 * Render page builder
	 */
	public function render_page_builder( $post ) {
		wp_nonce_field( 'sme_flexible_content', 'sme_flexible_content_nonce' );
		
		$sections = get_post_meta( $post->ID, '_sme_page_sections', true );
		if ( ! is_array( $sections ) ) {
			$sections = array();
		}
		
		?>
		<div id="sme-page-builder">
			<div class="sme-sections-container">
				<?php foreach ( $sections as $index => $section ) : ?>
					<?php $this->render_section( $index, $section ); ?>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button button-primary sme-add-section" data-section-type="hero_slider">
				+ Add Hero Slider
			</button>
			<button type="button" class="button button-primary sme-add-section" data-section-type="trending_news">
				+ Add Trending News
			</button>
			<button type="button" class="button button-primary sme-add-section" data-section-type="posts_grid">
				+ Add Posts Grid
			</button>
			<button type="button" class="button button-primary sme-add-section" data-section-type="cta_section">
				+ Add CTA Section
			</button>
		</div>
		
		<script type="text/html" id="sme-section-template">
			<?php $this->render_section( '{{INDEX}}', array( 'type' => '{{TYPE}}' ) ); ?>
		</script>
		
		<style>
		.sme-section {
			border: 1px solid #ddd;
			margin: 15px 0;
			padding: 15px;
			background: #f9f9f9;
		}
		.sme-section-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 15px;
			padding-bottom: 10px;
			border-bottom: 1px solid #ddd;
		}
		.sme-section-title {
			font-weight: bold;
			font-size: 16px;
		}
		.sme-section-controls {
			display: flex;
			gap: 10px;
		}
		.sme-section-fields {
			display: grid;
			grid-template-columns: repeat(2, 1fr);
			gap: 15px;
		}
		.sme-field {
			display: flex;
			flex-direction: column;
		}
		.sme-field label {
			font-weight: 600;
			margin-bottom: 5px;
		}
		.sme-field input,
		.sme-field select,
		.sme-field textarea {
			width: 100%;
			padding: 8px;
		}
		.sme-field-full {
			grid-column: 1 / -1;
		}
		</style>
		
		<script type="text/javascript">
		(function($) {
			$(document).ready(function() {
				var sectionIndex = <?php echo count( $sections ); ?>;
				
				$('.sme-add-section').on('click', function() {
					var type = $(this).data('section-type');
					var template = $('#sme-section-template').html()
						.replace(/\{\{INDEX\}\}/g, sectionIndex)
						.replace(/\{\{TYPE\}\}/g, type);
					
					$('.sme-sections-container').append(template);
					sectionIndex++;
					updateSectionIndices();
				});
				
				$(document).on('click', '.sme-remove-section', function() {
					if (confirm('Are you sure you want to remove this section?')) {
						$(this).closest('.sme-section').remove();
						updateSectionIndices();
					}
				});
				
				$(document).on('click', '.sme-move-up', function() {
					var $section = $(this).closest('.sme-section');
					var $prev = $section.prev('.sme-section');
					if ($prev.length) {
						$prev.before($section);
						updateSectionIndices();
					}
				});
				
				$(document).on('click', '.sme-move-down', function() {
					var $section = $(this).closest('.sme-section');
					var $next = $section.next('.sme-section');
					if ($next.length) {
						$next.after($section);
						updateSectionIndices();
					}
				});
				
				function updateSectionIndices() {
					$('.sme-section').each(function(index) {
						$(this).find('input, select, textarea').each(function() {
							var name = $(this).attr('name');
							if (name && name.includes('sme_sections')) {
								$(this).attr('name', name.replace(/\[\d+\]/, '[' + index + ']'));
							}
						});
					});
				}
			});
		})(jQuery);
		</script>
		<?php
	}
	
	/**
	 * Render single section
	 */
	private function render_section( $index, $section ) {
		$type = isset( $section['type'] ) ? $section['type'] : 'hero_slider';
		$section_title = isset( $section['section_title'] ) ? $section['section_title'] : '';
		$section_name = ucwords( str_replace( '_', ' ', $type ) );
		
		?>
		<div class="sme-section" data-index="<?php echo esc_attr( $index ); ?>">
			<input type="hidden" name="sme_sections[<?php echo esc_attr( $index ); ?>][type]" value="<?php echo esc_attr( $type ); ?>">
			
			<div class="sme-section-header">
				<span class="sme-section-title"><?php echo esc_html( $section_name ); ?></span>
				<div class="sme-section-controls">
					<button type="button" class="button sme-move-up">↑</button>
					<button type="button" class="button sme-move-down">↓</button>
					<button type="button" class="button sme-remove-section">Remove</button>
				</div>
			</div>
			
			<div class="sme-section-fields">
				<?php if ( $type === 'hero_slider' ) : ?>
					<div class="sme-field">
						<label>Section Title</label>
						<input type="text" name="sme_sections[<?php echo esc_attr( $index ); ?>][section_title]" value="<?php echo esc_attr( $section_title ?: 'Main News' ); ?>">
					</div>
					<div class="sme-field">
						<label>Number of Posts</label>
						<input type="number" name="sme_sections[<?php echo esc_attr( $index ); ?>][slider_posts_count]" value="<?php echo esc_attr( isset( $section['slider_posts_count'] ) ? $section['slider_posts_count'] : 3 ); ?>" min="1" max="10">
					</div>
					<div class="sme-field">
						<label>Category (optional)</label>
						<?php
						$categories = get_terms( array( 'taxonomy' => 'main_category', 'hide_empty' => false ) );
						$selected_category = isset( $section['slider_category'] ) ? $section['slider_category'] : '';
						?>
						<select name="sme_sections[<?php echo esc_attr( $index ); ?>][slider_category]">
							<option value="">All Categories</option>
							<?php foreach ( $categories as $cat ) : ?>
								<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( $selected_category, $cat->term_id ); ?>>
									<?php echo esc_html( $cat->name ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="sme-field">
						<label>
							<input type="checkbox" name="sme_sections[<?php echo esc_attr( $index ); ?>][slider_autoplay]" value="1" <?php checked( isset( $section['slider_autoplay'] ) ? $section['slider_autoplay'] : 1, 1 ); ?>>
							Auto-play
						</label>
					</div>
					<div class="sme-field">
						<label>Auto-play Interval (seconds)</label>
						<input type="number" name="sme_sections[<?php echo esc_attr( $index ); ?>][slider_interval]" value="<?php echo esc_attr( isset( $section['slider_interval'] ) ? $section['slider_interval'] : 6 ); ?>" min="3" max="10">
					</div>
					
				<?php elseif ( $type === 'trending_news' ) : ?>
					<div class="sme-field">
						<label>Section Title</label>
						<input type="text" name="sme_sections[<?php echo esc_attr( $index ); ?>][section_title]" value="<?php echo esc_attr( $section_title ?: 'Trending News' ); ?>">
					</div>
					<div class="sme-field">
						<label>Number of Posts</label>
						<input type="number" name="sme_sections[<?php echo esc_attr( $index ); ?>][trending_count]" value="<?php echo esc_attr( isset( $section['trending_count'] ) ? $section['trending_count'] : 5 ); ?>" min="1" max="10">
					</div>
					<div class="sme-field">
						<label>Category (optional)</label>
						<?php
						$categories = get_terms( array( 'taxonomy' => 'main_category', 'hide_empty' => false ) );
						$selected_category = isset( $section['trending_category'] ) ? $section['trending_category'] : '';
						?>
						<select name="sme_sections[<?php echo esc_attr( $index ); ?>][trending_category]">
							<option value="">All Categories</option>
							<?php foreach ( $categories as $cat ) : ?>
								<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( $selected_category, $cat->term_id ); ?>>
									<?php echo esc_html( $cat->name ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					
				<?php elseif ( $type === 'posts_grid' ) : ?>
					<div class="sme-field">
						<label>Section Title</label>
						<input type="text" name="sme_sections[<?php echo esc_attr( $index ); ?>][section_title]" value="<?php echo esc_attr( $section_title ?: 'Latest Posts' ); ?>">
					</div>
					<div class="sme-field">
						<label>Number of Columns</label>
						<select name="sme_sections[<?php echo esc_attr( $index ); ?>][grid_columns]">
							<option value="1" <?php selected( isset( $section['grid_columns'] ) ? $section['grid_columns'] : '3', '1' ); ?>>1 Column</option>
							<option value="2" <?php selected( isset( $section['grid_columns'] ) ? $section['grid_columns'] : '3', '2' ); ?>>2 Columns</option>
							<option value="3" <?php selected( isset( $section['grid_columns'] ) ? $section['grid_columns'] : '3', '3' ); ?>>3 Columns</option>
							<option value="4" <?php selected( isset( $section['grid_columns'] ) ? $section['grid_columns'] : '3', '4' ); ?>>4 Columns</option>
						</select>
					</div>
					<div class="sme-field">
						<label>Number of Posts</label>
						<input type="number" name="sme_sections[<?php echo esc_attr( $index ); ?>][grid_posts_count]" value="<?php echo esc_attr( isset( $section['grid_posts_count'] ) ? $section['grid_posts_count'] : 9 ); ?>" min="1" max="30">
					</div>
					<div class="sme-field">
						<label>Category (optional)</label>
						<?php
						$categories = get_terms( array( 'taxonomy' => 'main_category', 'hide_empty' => false ) );
						$selected_category = isset( $section['grid_category'] ) ? $section['grid_category'] : '';
						?>
						<select name="sme_sections[<?php echo esc_attr( $index ); ?>][grid_category]">
							<option value="">All Categories</option>
							<?php foreach ( $categories as $cat ) : ?>
								<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( $selected_category, $cat->term_id ); ?>>
									<?php echo esc_html( $cat->name ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="sme-field">
						<label>
							<input type="checkbox" name="sme_sections[<?php echo esc_attr( $index ); ?>][grid_pagination]" value="1" <?php checked( isset( $section['grid_pagination'] ) ? $section['grid_pagination'] : 1, 1 ); ?>>
							Show Pagination
						</label>
					</div>
					
				<?php elseif ( $type === 'cta_section' ) : ?>
					<div class="sme-field sme-field-full">
						<label>Headline</label>
						<input type="text" name="sme_sections[<?php echo esc_attr( $index ); ?>][cta_headline]" value="<?php echo esc_attr( isset( $section['cta_headline'] ) ? $section['cta_headline'] : 'Join 10,000+ Small Business Leaders' ); ?>">
					</div>
					<div class="sme-field sme-field-full">
						<label>Sub-headline</label>
						<textarea name="sme_sections[<?php echo esc_attr( $index ); ?>][cta_subheadline]" rows="3"><?php echo esc_textarea( isset( $section['cta_subheadline'] ) ? $section['cta_subheadline'] : 'Stay ahead with the latest news, strategies, and resources for small businesses.' ); ?></textarea>
					</div>
					<div class="sme-field">
						<label>
							<input type="checkbox" name="sme_sections[<?php echo esc_attr( $index ); ?>][cta_show_partners]" value="1" <?php checked( isset( $section['cta_show_partners'] ) ? $section['cta_show_partners'] : 1, 1 ); ?>>
							Show Partners Logos
						</label>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Save flexible content
	 */
	public function save_flexible_content( $post_id ) {
		// Check nonce
		if ( ! isset( $_POST['sme_flexible_content_nonce'] ) || ! wp_verify_nonce( $_POST['sme_flexible_content_nonce'], 'sme_flexible_content' ) ) {
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
		
		// Save sections
		if ( isset( $_POST['sme_sections'] ) && is_array( $_POST['sme_sections'] ) ) {
			$sections = array();
			foreach ( $_POST['sme_sections'] as $index => $section ) {
				$sections[] = array_map( 'sanitize_text_field', $section );
			}
			update_post_meta( $post_id, '_sme_page_sections', $sections );
		} else {
			delete_post_meta( $post_id, '_sme_page_sections' );
		}
	}
	
	/**
	 * Enqueue admin scripts
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}
		
		wp_enqueue_script( 'jquery' );
	}
}

