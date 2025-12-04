<?php
/**
 * Footer Template
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<footer class="footer">
	<div class="container-inner">
		<?php if ( get_theme_mod( 'footer_show_columns', true ) ) : ?>
		<div class="footer-grid">
			<!-- Column 1: SME Insights -->
			<div class="footer-column">
				<h4><?php echo esc_html( get_theme_mod( 'footer_company_name', 'SME INSIGHTS' ) ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ?: '#' ); ?>"><?php echo esc_html( get_theme_mod( 'footer_column1_about', 'About Us' ) ); ?></a></li>
					<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'our-team' ) ) ?: '#' ); ?>"><?php echo esc_html( get_theme_mod( 'footer_column1_team', 'Our Team' ) ); ?></a></li>
					<?php
					$contributor_page = get_page_by_path( 'become-contributor' );
					if ( ! $contributor_page ) {
						$contributor_page = get_page_by_path( 'become-a-contributor' );
					}
					if ( ! $contributor_page ) {
						$contributor_page = sme_get_page_by_title( 'Become a Contributor' );
					}
					$contributor_url = $contributor_page ? get_permalink( $contributor_page->ID ) : '#';
					?>
					<li><a href="<?php echo esc_url( $contributor_url ); ?>"><?php echo esc_html( get_theme_mod( 'footer_column1_contributor', 'Become a Contributor' ) ); ?></a></li>
					<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ?: '#' ); ?>"><?php echo esc_html( get_theme_mod( 'footer_column1_contact', 'Contact Us' ) ); ?></a></li>
				</ul>
			</div>

			<!-- Column 2: Our Topics -->
			<div class="footer-column">
				<h4><?php echo esc_html__( 'Our Topics', 'sme-insights' ); ?></h4>
				<ul>
					<?php
					$topics = array(
						'Technology' => 'Technology',
						'Marketing'  => 'Marketing',
						'Strategy'   => 'Strategy',
						'Finance'    => 'Finance',
						'Growth'     => 'Growth',
					);
					
					foreach ( $topics as $display_name => $title ) {
						$term = null;
						
						$term = get_term_by( 'name', $display_name, 'main_category' );
						
						if ( ! $term ) {
							$slug = sanitize_title( $display_name );
							$term = get_term_by( 'slug', $slug, 'main_category' );
						}
						
						if ( ! $term ) {
							$alternative_slugs = array(
								'Technology' => array( 'technology', 'tech' ),
								'Marketing'  => array( 'marketing' ),
								'Strategy'   => array( 'strategy' ),
								'Finance'    => array( 'finance' ),
								'Growth'     => array( 'growth' ),
							);
							if ( isset( $alternative_slugs[ $display_name ] ) ) {
								foreach ( $alternative_slugs[ $display_name ] as $alt_slug ) {
									$term = get_term_by( 'slug', $alt_slug, 'main_category' );
									if ( $term ) {
										break;
									}
								}
							}
						}
						
						if ( $term ) {
							echo '<li><a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $title ) . '</a></li>';
						} else {
							echo '<li><a href="#">' . esc_html( $title ) . '</a></li>';
						}
					}
					?>
				</ul>
			</div>

			<!-- Column 3: Legal -->
			<div class="footer-column">
				<h4><?php echo esc_html__( 'Legal', 'sme-insights' ); ?></h4>
				<ul>
					<?php
					$privacy_page = get_page_by_path( 'privacy-policy' );
					if ( ! $privacy_page ) {
						$privacy_page = sme_get_page_by_title( 'Privacy Policy' );
					}
					$privacy_url = $privacy_page ? get_permalink( $privacy_page->ID ) : '#';
					
					$terms_page = get_page_by_path( 'terms-of-service' );
					if ( ! $terms_page ) {
						$terms_page = sme_get_page_by_title( 'Terms of Service' );
					}
					$terms_url = $terms_page ? get_permalink( $terms_page->ID ) : '#';
					
					$disclaimer_page = get_page_by_path( 'disclaimer' );
					if ( ! $disclaimer_page ) {
						$disclaimer_page = sme_get_page_by_title( 'Disclaimer' );
					}
					$disclaimer_url = $disclaimer_page ? get_permalink( $disclaimer_page->ID ) : '#';
					
					$advertise_page = get_page_by_path( 'advertise-with-us' );
					if ( ! $advertise_page ) {
						$advertise_page = sme_get_page_by_title( 'Advertise With Us' );
					}
					$advertise_url = $advertise_page ? get_permalink( $advertise_page->ID ) : '#';
					?>
					<li><a href="<?php echo esc_url( $privacy_url ); ?>"><?php echo esc_html__( 'Privacy Policy', 'sme-insights' ); ?></a></li>
					<li><a href="<?php echo esc_url( $terms_url ); ?>"><?php echo esc_html__( 'Terms of Service', 'sme-insights' ); ?></a></li>
					<li><a href="<?php echo esc_url( $disclaimer_url ); ?>"><?php echo esc_html__( 'Disclaimer', 'sme-insights' ); ?></a></li>
					<li><a href="<?php echo esc_url( $advertise_url ); ?>"><?php echo esc_html__( 'Advertise With Us', 'sme-insights' ); ?></a></li>
				</ul>
			</div>

			<!-- Column 4: Connect -->
			<div class="footer-column">
				<h4><?php echo esc_html__( 'Connect', 'sme-insights' ); ?></h4>
				<ul>
					<?php
					$social_links_row1 = array(
						'facebook'  => get_theme_mod( 'social_facebook', 'https://facebook.com/smeinsights' ),
						'twitter'   => get_theme_mod( 'social_twitter', 'https://twitter.com/smeinsights' ),
						'linkedin'  => get_theme_mod( 'social_linkedin', 'https://linkedin.com/company/smeinsights' ),
						'youtube'   => get_theme_mod( 'social_youtube', 'https://youtube.com/@smeinsights' ),
						'instagram' => get_theme_mod( 'social_instagram', 'https://instagram.com/smeinsights' ),
					);
					$social_names = array(
						'facebook'  => __( 'Facebook', 'sme-insights' ),
						'twitter'   => __( 'Twitter', 'sme-insights' ),
						'linkedin'  => __( 'LinkedIn', 'sme-insights' ),
						'youtube'   => __( 'Youtube', 'sme-insights' ),
						'instagram' => __( 'Instagram', 'sme-insights' ),
					);
					foreach ( $social_links_row1 as $platform => $url ) {
						if ( ! empty( $url ) && $url !== '#' ) {
							echo '<li><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $social_names[ $platform ] ) . '</a></li>';
						}
					}
					
					$social_links_row2 = array(
						'tiktok'    => get_theme_mod( 'social_tiktok', '' ),
						'pinterest' => get_theme_mod( 'social_pinterest', '' ),
						'snapchat'  => get_theme_mod( 'social_snapchat', '' ),
						'whatsapp'  => get_theme_mod( 'social_whatsapp', '' ),
						'telegram'  => get_theme_mod( 'social_telegram', '' ),
						'discord'   => get_theme_mod( 'social_discord', '' ),
						'reddit'    => get_theme_mod( 'social_reddit', '' ),
						'medium'    => get_theme_mod( 'social_medium', '' ),
						'github'    => get_theme_mod( 'social_github', '' ),
						'behance'   => get_theme_mod( 'social_behance', '' ),
						'dribbble'  => get_theme_mod( 'social_dribbble', '' ),
					);
					$social_names_row2 = array(
						'tiktok'    => __( 'TikTok', 'sme-insights' ),
						'pinterest' => __( 'Pinterest', 'sme-insights' ),
						'snapchat'  => __( 'Snapchat', 'sme-insights' ),
						'whatsapp'  => __( 'WhatsApp', 'sme-insights' ),
						'telegram'  => __( 'Telegram', 'sme-insights' ),
						'discord'   => __( 'Discord', 'sme-insights' ),
						'reddit'    => __( 'Reddit', 'sme-insights' ),
						'medium'    => __( 'Medium', 'sme-insights' ),
						'github'    => __( 'GitHub', 'sme-insights' ),
						'behance'   => __( 'Behance', 'sme-insights' ),
						'dribbble'  => __( 'Dribbble', 'sme-insights' ),
					);
					foreach ( $social_links_row2 as $platform => $url ) {
						if ( ! empty( $url ) && $url !== '#' ) {
							echo '<li><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $social_names_row2[ $platform ] ) . '</a></li>';
						}
					}
					?>
				</ul>
			</div>
		</div>
		<?php endif; ?>

		<!-- Footer Bottom -->
		<div class="footer-bottom">
			<div class="footer-bottom-content">
				<div class="footer-bottom-left">
					<p>
						<?php
						$copyright_text = get_theme_mod( 'footer_copyright_text', 'Copyright © {year} {site_name}. | Privacy Policy | Terms of Service | Your trusted source for Small Business News & Insights.' );
						if ( empty( trim( $copyright_text ) ) ) {
							$copyright_text = 'Copyright © {year} {site_name}. | Privacy Policy | Terms of Service | Your trusted source for Small Business News & Insights.';
						}
						$copyright_text = str_replace( '{year}', '2025', $copyright_text );
						$copyright_text = str_replace( '{site_name}', get_theme_mod( 'footer_company_name', 'SME INSIGHTS' ), $copyright_text );
						
						// Get page URLs
						$privacy_page = get_page_by_path( 'privacy-policy' );
						if ( ! $privacy_page ) {
							$privacy_page = sme_get_page_by_title( 'Privacy Policy' );
						}
						$privacy_url = $privacy_page ? get_permalink( $privacy_page->ID ) : '#';
						
						$terms_page = get_page_by_path( 'terms-of-service' );
						if ( ! $terms_page ) {
							$terms_page = sme_get_page_by_title( 'Terms of Service' );
						}
						$terms_url = $terms_page ? get_permalink( $terms_page->ID ) : '#';
						
						$copyright_text = str_replace( 
							'Privacy Policy', 
							'<a href="' . esc_url( $privacy_url ) . '" style="color: rgba(255, 255, 255, 0.9); text-decoration: underline; text-underline-offset: 2px;">Privacy Policy</a>', 
							$copyright_text 
						);
						$copyright_text = str_replace( 
							'Terms of Service', 
							'<a href="' . esc_url( $terms_url ) . '" style="color: rgba(255, 255, 255, 0.9); text-decoration: underline; text-underline-offset: 2px;">Terms of Service</a>', 
							$copyright_text 
						);
						
						echo wp_kses_post( $copyright_text );
						?>
					</p>
					<p style="margin-top: 10px; font-size: 0.875rem; color: rgba(255, 255, 255, 0.7);">
						<?php echo esc_html__( 'Theme developed by', 'sme-insights' ); ?> 
						<a href="https://prortec.com/remon-romany/" target="_blank" rel="noopener noreferrer" style="color: rgba(255, 255, 255, 0.9); text-decoration: underline;">Remon Romany</a>
					</p>
				</div>
			</div>
		</div>
	</div>
</footer>

<!-- Back to Top Button -->
<button class="back-to-top" id="backToTop" aria-label="<?php echo esc_attr__( 'Back to top', 'sme-insights' ); ?>" type="button">
	<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
		<path d="M18 15l-6-6-6 6"/>
	</svg>
</button>

<!-- Subscription Modal -->
<div class="subscription-modal" id="subscriptionModal">
	<div class="subscription-modal-content">
		<button class="subscription-modal-close" id="subscriptionModalClose" aria-label="<?php echo esc_attr__( 'Close', 'sme-insights' ); ?>" type="button">&times;</button>
		<div class="modal-header">
			<h2><?php echo esc_html__( 'Join 15,000+ Business Leaders', 'sme-insights' ); ?></h2>
			<p><?php echo esc_html__( 'Receive our weekly newsletter with the latest insights, strategies, and tools to grow your small business.', 'sme-insights' ); ?></p>
		</div>
		<form class="modal-form" id="subscriptionForm" aria-label="<?php echo esc_attr__( 'Newsletter subscription form', 'sme-insights' ); ?>">
			<div class="modal-form-group">
				<label for="subscriptionEmail" class="screen-reader-text"><?php echo esc_html__( 'Email Address', 'sme-insights' ); ?></label>
				<input type="email" placeholder="<?php echo esc_attr__( 'Enter Your Email Address', 'sme-insights' ); ?>" required id="subscriptionEmail" aria-required="true">
			</div>
			<button type="submit" class="modal-submit-btn"><?php echo esc_html__( 'Subscribe Now', 'sme-insights' ); ?></button>
			<p class="modal-privacy"><?php echo esc_html__( 'We respect your privacy. Unsubscribe at any time.', 'sme-insights' ); ?></p>
		</form>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
