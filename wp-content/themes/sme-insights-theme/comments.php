<?php
/**
 * Comments Template
 * 
 * Displays comments and comment form for single posts
 * Matches the design from single-page.html
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

if ( post_password_required() ) {
	return;
}
?>

<div class="comments-section">
	<h2><?php esc_html_e( 'Join the Discussion', 'sme-insights' ); ?></h2>
	
	<?php if ( have_comments() ) : ?>
		<ul class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ul',
				'short_ping'  => true,
				'avatar_size' => 50,
				'callback'    => 'sme_comment_callback',
			) );
			?>
		</ul>
		
		<?php
		the_comments_pagination( array(
			'prev_text' => __( '&laquo; Previous', 'sme-insights' ),
			'next_text' => __( 'Next &raquo;', 'sme-insights' ),
		) );
		?>
	<?php endif; ?>
	
	<?php
	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	
	$comment_form_args = array(
		'class_form'           => 'comment-form',
		'title_reply'          => '',
		'title_reply_to'       => __( 'Reply to %s', 'sme-insights' ),
		'cancel_reply_link'    => __( 'Cancel Reply', 'sme-insights' ),
		'label_submit'         => __( 'Post Comment', 'sme-insights' ),
		'submit_button'        => '<button type="submit" id="submit" class="submit-comment">%4$s</button>',
		'submit_field'         => '<div class="form-submit">%1$s %2$s</div>',
		'format'               => 'xhtml',
		'fields'               => array(
			'author' => '<div class="form-row"><div class="form-group">' .
				'<label for="author">' . __( 'Name', 'sme-insights' ) . ( $req ? ' *' : '' ) . '</label>' .
				'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' required /></div>',
			'email'  => '<div class="form-group">' .
				'<label for="email">' . __( 'Email', 'sme-insights' ) . ( $req ? ' *' : '' ) . '</label>' .
				'<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' required /></div></div>',
			'url'    => '',
		),
		'comment_field'        => '<div class="form-group">' .
			'<label for="comment">' . __( 'Your Comment', 'sme-insights' ) . ' *</label>' .
			'<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required></textarea></div>',
	);
	
	comment_form( $comment_form_args );
	?>
</div>

