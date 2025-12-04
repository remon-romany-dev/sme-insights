<?php
/**
 * Database Cleaner
 * Cleans up old transients, orphaned options, and optimizes database
 *
 * @package SME_Insights
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SME_Database_Cleaner {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Add cleanup action (run weekly)
		add_action( 'wp_scheduled_delete', array( $this, 'cleanup_old_transients' ) );
		
		// Add admin menu for manual cleanup
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}
	
	/**
	 * Clean up expired transients
	 */
	public function cleanup_old_transients() {
		global $wpdb;
		
		// Delete expired transients
		$deleted = $wpdb->query(
			"DELETE a, b FROM {$wpdb->options} a, {$wpdb->options} b
			WHERE a.option_name LIKE '_transient_%'
			AND a.option_name NOT LIKE '_transient_timeout_%'
			AND b.option_name = CONCAT('_transient_timeout_', SUBSTRING(a.option_name, 12))
			AND b.option_value < UNIX_TIMESTAMP()"
		);
		
		// Delete expired site transients
		$deleted += $wpdb->query(
			"DELETE a, b FROM {$wpdb->options} a, {$wpdb->options} b
			WHERE a.option_name LIKE '_site_transient_%'
			AND a.option_name NOT LIKE '_site_transient_timeout_%'
			AND b.option_name = CONCAT('_site_transient_timeout_', SUBSTRING(a.option_name, 17))
			AND b.option_value < UNIX_TIMESTAMP()"
		);
		
		// Clean orphaned transient timeouts
		$wpdb->query(
			"DELETE FROM {$wpdb->options}
			WHERE option_name LIKE '_transient_timeout_%'
			AND option_value < UNIX_TIMESTAMP()"
		);
		
		$wpdb->query(
			"DELETE FROM {$wpdb->options}
			WHERE option_name LIKE '_site_transient_timeout_%'
			AND option_value < UNIX_TIMESTAMP()"
		);
		
		return $deleted;
	}
	
	/**
	 * Clean up orphaned post meta
	 */
	public function cleanup_orphaned_postmeta() {
		global $wpdb;
		
		$deleted = $wpdb->query(
			"DELETE pm FROM {$wpdb->postmeta} pm
			LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			WHERE p.ID IS NULL"
		);
		
		return $deleted;
	}
	
	/**
	 * Clean up orphaned comment meta
	 */
	public function cleanup_orphaned_commentmeta() {
		global $wpdb;
		
		$deleted = $wpdb->query(
			"DELETE cm FROM {$wpdb->commentmeta} cm
			LEFT JOIN {$wpdb->comments} c ON cm.comment_id = c.comment_ID
			WHERE c.comment_ID IS NULL"
		);
		
		return $deleted;
	}
	
	/**
	 * Clean up orphaned term relationships
	 */
	public function cleanup_orphaned_term_relationships() {
		global $wpdb;
		
		$deleted = $wpdb->query(
			"DELETE tr FROM {$wpdb->term_relationships} tr
			LEFT JOIN {$wpdb->posts} p ON tr.object_id = p.ID
			WHERE p.ID IS NULL"
		);
		
		return $deleted;
	}
	
	/**
	 * Clean up old revisions (keep last 5 per post)
	 */
	public function cleanup_old_revisions() {
		global $wpdb;
		
		$revisions = $wpdb->get_col(
			"SELECT ID FROM {$wpdb->posts}
			WHERE post_type = 'revision'
			AND post_parent > 0
			ORDER BY post_date DESC"
		);
		
		$revisions_by_parent = array();
		foreach ( $revisions as $revision_id ) {
			$parent_id = wp_get_post_parent_id( $revision_id );
			if ( $parent_id ) {
				if ( ! isset( $revisions_by_parent[ $parent_id ] ) ) {
					$revisions_by_parent[ $parent_id ] = array();
				}
				$revisions_by_parent[ $parent_id ][] = $revision_id;
			}
		}
		
		$deleted = 0;
		foreach ( $revisions_by_parent as $parent_id => $revision_ids ) {
			if ( count( $revision_ids ) > 5 ) {
				$to_delete = array_slice( $revision_ids, 5 );
				foreach ( $to_delete as $revision_id ) {
					wp_delete_post_revision( $revision_id );
					$deleted++;
				}
			}
		}
		
		return $deleted;
	}
	
	/**
	 * Run full cleanup
	 */
	public function run_full_cleanup() {
		$results = array(
			'transients' => $this->cleanup_old_transients(),
			'orphaned_postmeta' => $this->cleanup_orphaned_postmeta(),
			'orphaned_commentmeta' => $this->cleanup_orphaned_commentmeta(),
			'orphaned_term_relationships' => $this->cleanup_orphaned_term_relationships(),
			'old_revisions' => $this->cleanup_old_revisions(),
		);
		
		// Flush cache
		wp_cache_flush();
		
		return $results;
	}
	
	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'sme-theme-dashboard',
			'Database Cleaner',
			'Database Cleaner',
			'manage_options',
			'sme-database-cleaner',
			array( $this, 'render_admin_page' )
		);
	}
	
	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		if ( isset( $_POST['sme_run_cleanup'] ) && check_admin_referer( 'sme_database_cleanup', 'sme_cleanup_nonce' ) ) {
			$results = $this->run_full_cleanup();
			?>
			<div class="notice notice-success is-dismissible">
				<p><strong>Cleanup completed!</strong></p>
				<ul>
					<li>Expired transients: <?php echo esc_html( $results['transients'] ); ?> deleted</li>
					<li>Orphaned post meta: <?php echo esc_html( $results['orphaned_postmeta'] ); ?> deleted</li>
					<li>Orphaned comment meta: <?php echo esc_html( $results['orphaned_commentmeta'] ); ?> deleted</li>
					<li>Orphaned term relationships: <?php echo esc_html( $results['orphaned_term_relationships'] ); ?> deleted</li>
					<li>Old revisions: <?php echo esc_html( $results['old_revisions'] ); ?> deleted</li>
				</ul>
			</div>
			<?php
		}
		?>
		<div class="wrap">
			<h1>Database Cleaner</h1>
			<p class="description">Clean up old transients, orphaned data, and optimize your database for better performance.</p>
			
			<div class="card" style="max-width: 800px; margin-top: 20px;">
				<h2>What will be cleaned:</h2>
				<ul>
					<li><strong>Expired Transients:</strong> Old cached data that's no longer needed</li>
					<li><strong>Orphaned Post Meta:</strong> Metadata without associated posts</li>
					<li><strong>Orphaned Comment Meta:</strong> Metadata without associated comments</li>
					<li><strong>Orphaned Term Relationships:</strong> Category/tag relationships without posts</li>
					<li><strong>Old Revisions:</strong> Post revisions (keeps last 5 per post)</li>
				</ul>
				
				<form method="post" action="" style="margin-top: 20px;">
					<?php wp_nonce_field( 'sme_database_cleanup', 'sme_cleanup_nonce' ); ?>
					<p>
						<input type="submit" name="sme_run_cleanup" class="button button-primary" value="Run Cleanup Now" onclick="return confirm('Are you sure you want to run database cleanup? This action cannot be undone.');">
					</p>
				</form>
			</div>
		</div>
		<?php
	}
}

SME_Database_Cleaner::get_instance();

