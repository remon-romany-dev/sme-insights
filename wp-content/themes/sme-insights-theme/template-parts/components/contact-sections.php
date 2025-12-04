<?php
/**
 * Contact Page Sections
 * Reusable components for Contact page
 *
 * @package SME_Insights
 * @since 1.0.0
 */
?>

<!-- Before You Reach Out Section -->
<div class="container-inner" style="padding: 60px 20px 40px;">
	<h2 style="text-align: center; font-size: 2rem; margin-bottom: 15px; color: var(--text-primary);">Before You Reach Out</h2>
	<p style="text-align: center; font-size: 1.1rem; color: var(--text-secondary); margin-bottom: 50px; max-width: 700px; margin-left: auto; margin-right: auto;">Find quick answers to common questions or get directed to the right place for your inquiry.</p>
	<div class="quick-links-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; margin-bottom: 60px;">
		<div class="quick-link-card" style="background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); padding: 35px; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); transition: all 0.3s; cursor: pointer; border: 1px solid var(--border-color); position: relative; overflow: hidden; text-align: center;">
			<div style="position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, transparent 100%); border-radius: 0 0 0 100px;"></div>
			<div class="contact-icon" style="margin: 0 auto 25px; background: var(--accent-secondary); color: #fff; border: none; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
				<svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<h3 style="font-size: 1.4rem; margin-bottom: 15px; color: var(--accent-primary); text-align: center; font-weight: 700;">Question about an article?</h3>
			<p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 25px; text-align: center; font-size: 0.95rem;">The best place to discuss our content is in the comments section below each article. This allows everyone to benefit from the conversation.</p>
			<?php
			// Get Blog page URL - try multiple methods
			$blog_page_url = '';
			
			// Method 1: Try to find page with 'blog' slug
			$blog_page = get_page_by_path( 'blog' );
			if ( $blog_page ) {
				$blog_page_url = get_permalink( $blog_page->ID );
			} else {
				$blog_page = sme_get_page_by_title( 'Business News & Insights' );
				if ( $blog_page ) {
					$blog_page_url = get_permalink( $blog_page->ID );
				} else {
					// Method 3: Search for page using page-blog.php template
					$pages = get_pages( array(
						'meta_key'   => '_wp_page_template',
						'meta_value' => 'page-blog.php',
						'number'     => 1,
					) );
					if ( ! empty( $pages ) ) {
						$blog_page_url = get_permalink( $pages[0]->ID );
					} else {
						// Method 4: Try page_for_posts option
						$page_for_posts = get_option( 'page_for_posts' );
						if ( $page_for_posts ) {
							$blog_page_url = get_permalink( $page_for_posts );
						} else {
							// Method 5: Fallback to posts archive
							$blog_page_url = get_post_type_archive_link( 'post' );
						}
					}
				}
			}
			?>
			<a href="<?php echo esc_url( $blog_page_url ); ?>" style="color: var(--accent-secondary); font-weight: 600; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 24px; background: rgba(37, 99, 235, 0.1); border-radius: 8px; transition: all 0.3s;">Visit Blog Page <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
		</div>

		<div class="quick-link-card" style="background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); padding: 35px; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); transition: all 0.3s; cursor: pointer; border: 1px solid var(--border-color); position: relative; overflow: hidden; text-align: center;">
			<div style="position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, transparent 100%); border-radius: 0 0 0 100px;"></div>
			<div class="contact-icon" style="margin: 0 auto 25px; background: var(--accent-secondary); color: #fff; border: none; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
				<svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<h3 style="font-size: 1.4rem; margin-bottom: 15px; color: var(--accent-primary); text-align: center; font-weight: 700;">Want to write for us?</h3>
			<p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 25px; text-align: center; font-size: 0.95rem;">We're always welcoming expert contributors. Please review our submission guidelines and learn how to become a contributor.</p>
			<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'become-contributor' ) ) ); ?>" style="color: var(--accent-secondary); font-weight: 600; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 24px; background: rgba(37, 99, 235, 0.1); border-radius: 8px; transition: all 0.3s;">Become a Contributor <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
		</div>

		<div class="quick-link-card" style="background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); padding: 35px; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); transition: all 0.3s; cursor: pointer; border: 1px solid var(--border-color); position: relative; overflow: hidden; text-align: center;">
			<div style="position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, transparent 100%); border-radius: 0 0 0 100px;"></div>
			<div class="contact-icon" style="margin: 0 auto 25px; background: var(--accent-secondary); color: #fff; border: none; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
				<svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<h3 style="font-size: 1.4rem; margin-bottom: 15px; color: var(--accent-primary); text-align: center; font-weight: 700;">Interested in advertising?</h3>
			<p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 25px; text-align: center; font-size: 0.95rem;">For advertising and partnership inquiries, please use the form below and select "Advertising & Partnerships" from the subject menu.</p>
			<a href="#contactForm" style="color: var(--accent-secondary); font-weight: 600; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 24px; background: rgba(37, 99, 235, 0.1); border-radius: 8px; transition: all 0.3s;">Fill Out the Form <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
		</div>
	</div>
</div>

<!-- Contact Container -->
<div class="contact-container" style="max-width: 1200px; margin: -60px auto 80px; padding: 0 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 50px;">
	<!-- Contact Form -->
	<div class="contact-form-section" style="background: #fff; padding: 50px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
		<h2 style="font-size: 2rem; margin-bottom: 30px;">Send Us a Message</h2>
		<?php
		// Use Contact Form 7 or default form
		if ( function_exists( 'wpcf7_contact_form' ) ) {
			echo do_shortcode( '[contact-form-7]' );
		} else {
		?>
		<form id="contactForm" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<input type="hidden" name="action" value="sme_contact_form">
			<?php wp_nonce_field( 'sme_contact_form', 'sme_contact_nonce' ); ?>
			
			<div class="form-group" style="margin-bottom: 25px;">
				<label for="name" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Full Name *</label>
				<input type="text" id="name" name="name" required placeholder="John Smith" style="width: 100%; padding: 15px; border: 2px solid var(--border-color); border-radius: 6px; font-size: 1rem; font-family: inherit; transition: border-color 0.3s;">
			</div>

			<div class="form-group" style="margin-bottom: 25px;">
				<label for="email" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Email Address *</label>
				<input type="email" id="email" name="email" required placeholder="john@example.com" style="width: 100%; padding: 15px; border: 2px solid var(--border-color); border-radius: 6px; font-size: 1rem; font-family: inherit; transition: border-color 0.3s;">
			</div>

			<div class="form-group" style="margin-bottom: 25px;">
				<label for="subject" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Subject *</label>
				<select id="subject" name="subject" required style="width: 100%; padding: 15px; border: 2px solid var(--border-color); border-radius: 6px; font-size: 1rem; font-family: inherit; transition: border-color 0.3s;">
					<option value="">Select a subject</option>
					<option value="general">General Inquiry</option>
					<option value="advertising">Advertising & Partnerships</option>
					<option value="technical">Report a Technical Issue</option>
					<option value="feedback">Feedback & Suggestions</option>
				</select>
			</div>

			<div class="form-group" style="margin-bottom: 25px;">
				<label for="message" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Message *</label>
				<textarea id="message" name="message" required placeholder="Tell us how we can help you..." style="width: 100%; padding: 15px; border: 2px solid var(--border-color); border-radius: 6px; font-size: 1rem; font-family: inherit; transition: border-color 0.3s; resize: vertical; min-height: 150px;"></textarea>
			</div>

			<div class="form-group" style="margin-bottom: 25px;">
				<label style="display: flex; align-items: center; cursor: pointer; font-size: 0.95rem; color: var(--text-secondary);">
					<input type="checkbox" id="newsletter_subscribe_contact" name="newsletter_subscribe" value="1" style="margin-right: 10px; width: 18px; height: 18px; cursor: pointer;">
					<span>Subscribe to our newsletter for the latest business insights and updates</span>
				</label>
			</div>

			<button type="submit" class="submit-btn" style="background: var(--accent-secondary); color: #fff; padding: 15px 40px; border: none; border-radius: 6px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s; width: 100%;">Send Message</button>
		</form>
		<?php } ?>
	</div>

	<!-- Contact Info -->
	<div class="contact-info-section" style="background: #fff; padding: 50px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
		<h2 style="font-size: 2rem; margin-bottom: 30px;">Contact Information</h2>
		
		<div class="contact-info-item" style="display: flex; align-items: flex-start; gap: 20px; margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid var(--border-color);">
			<div class="contact-icon" style="color: var(--accent-primary); width: 50px; height: 50px; min-width: 50px; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 50%; flex-shrink: 0;">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<polyline points="22,6 12,13 2,6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<div class="contact-info-content">
				<h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 10px; color: var(--text-primary);">Email Us</h3>
				<p style="color: var(--text-secondary); margin-bottom: 8px;">For general inquiries:<br>
				<a href="mailto:info@smeinsights.com" style="color: var(--accent-secondary);">info@smeinsights.com</a></p>
				<p style="color: var(--text-secondary);">For editorial submissions:<br>
				<a href="mailto:editor@smeinsights.com" style="color: var(--accent-secondary);">editor@smeinsights.com</a></p>
			</div>
		</div>

		<div class="contact-info-item" style="display: flex; align-items: flex-start; gap: 20px; margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid var(--border-color);">
			<div class="contact-icon" style="color: var(--accent-primary); width: 50px; height: 50px; min-width: 50px; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 50%; flex-shrink: 0;">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<div class="contact-info-content">
				<h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 10px; color: var(--text-primary);">Call Us</h3>
				<p style="color: var(--text-secondary); margin-bottom: 8px;">Business Hours: Monday - Friday<br>
				9:00 AM - 5:00 PM EST</p>
				<p style="color: var(--text-secondary);">Phone: <a href="tel:+11234567890" style="color: var(--accent-secondary);">+1 (123) 456-7890</a></p>
			</div>
		</div>

		<div class="contact-info-item" style="display: flex; align-items: flex-start; gap: 20px; margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid var(--border-color);">
			<div class="contact-icon" style="color: var(--accent-primary); width: 50px; height: 50px; min-width: 50px; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 50%; flex-shrink: 0;">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<div class="contact-info-content">
				<h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 10px; color: var(--text-primary);">Visit Us</h3>
				<p style="color: var(--text-secondary);">SME Insights Headquarters<br>
				123 Business Road<br>
				Dubai, UAE</p>
			</div>
		</div>

		<div class="contact-info-item" style="display: flex; align-items: flex-start; gap: 20px; margin-bottom: 0; padding-bottom: 0; border-bottom: none;">
			<div class="contact-icon" style="color: var(--accent-primary); width: 50px; height: 50px; min-width: 50px; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 50%; flex-shrink: 0;">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<div class="contact-info-content">
				<h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 10px; color: var(--text-primary);">Follow Us</h3>
				<p style="color: var(--text-secondary); margin-bottom: 20px;">Stay connected on social media</p>
				<?php
				// Get social media links from theme customizer
				$social_links = array(
					'facebook'  => get_theme_mod( 'social_facebook', 'https://facebook.com/smeinsights' ),
					'twitter'   => get_theme_mod( 'social_twitter', 'https://twitter.com/smeinsights' ),
					'linkedin'  => get_theme_mod( 'social_linkedin', 'https://linkedin.com/company/smeinsights' ),
					'instagram' => get_theme_mod( 'social_instagram', 'https://instagram.com/smeinsights' ),
				);
				?>
				<div class="social-links" style="display: flex; gap: 15px; margin-top: 20px;">
					<?php if ( ! empty( $social_links['facebook'] ) && $social_links['facebook'] !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_links['facebook'] ); ?>" class="social-link" title="Facebook" aria-label="Facebook" target="_blank" rel="noopener noreferrer" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); color: var(--text-primary); border: 2px solid var(--border-color); border-radius: 50%; text-decoration: none; transition: all 0.3s ease;">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
					<?php endif; ?>
					<?php if ( ! empty( $social_links['twitter'] ) && $social_links['twitter'] !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_links['twitter'] ); ?>" class="social-link" title="Twitter" aria-label="Twitter" target="_blank" rel="noopener noreferrer" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); color: var(--text-primary); border: 2px solid var(--border-color); border-radius: 50%; text-decoration: none; transition: all 0.3s ease;">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
					<?php endif; ?>
					<?php if ( ! empty( $social_links['linkedin'] ) && $social_links['linkedin'] !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_links['linkedin'] ); ?>" class="social-link" title="LinkedIn" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); color: var(--text-primary); border: 2px solid var(--border-color); border-radius: 50%; text-decoration: none; transition: all 0.3s ease;">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<circle cx="4" cy="4" r="2" stroke="currentColor" stroke-width="2"/>
						</svg>
					</a>
					<?php endif; ?>
					<?php if ( ! empty( $social_links['instagram'] ) && $social_links['instagram'] !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_links['instagram'] ); ?>" class="social-link" title="Instagram" aria-label="Instagram" target="_blank" rel="noopener noreferrer" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); color: var(--text-primary); border: 2px solid var(--border-color); border-radius: 50%; text-decoration: none; transition: all 0.3s ease;">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

