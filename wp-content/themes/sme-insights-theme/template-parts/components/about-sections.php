<?php
/**
 * About Page Sections Template
 * Matches about-page.html design exactly
 *
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!-- The Problem We Solve -->
<section class="challenge-section" style="margin-bottom: 40px; padding: 40px; background: var(--bg-secondary); border-radius: 12px;">
	<h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 20px; color: var(--text-primary); text-align: center;">The Challenge for Small Businesses</h2>
	<p style="font-size: 1rem; line-height: 1.6; color: var(--text-secondary); text-align: center; max-width: 800px; margin: 0 auto;">In today's rapidly changing business landscape, small business owners face an enormous challenge in accessing reliable information and practical strategies. Between conflicting advice and theoretical content, it's difficult to know what actually works in the real world. Many entrepreneurs find themselves drowning in generic advice that doesn't address their specific needs or circumstances.</p>
</section>

<!-- Our Solution & Mission -->
<section class="mission-section" style="margin-bottom: 40px; padding: 40px; background: #fff; border-radius: 12px; border: 1px solid transparent; transition: all 0.3s ease;">
	<h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 20px; color: var(--text-primary); text-align: center; transition: color 0.3s ease;">Our Mission: To Cut Through the Noise</h2>
	<p style="font-size: 1rem; line-height: 1.6; color: var(--text-secondary); margin-bottom: 20px; text-align: center; max-width: 800px; margin-left: auto; margin-right: auto; transition: color 0.3s ease;">That's why we founded SME Insights. Our mission is simple: to provide small and medium business leaders with practical insights, deep analysis, and guidance from real experts in the field. We believe that the right information at the right time is the most powerful tool for growth.</p>
	<p style="font-size: 1rem; line-height: 1.6; color: var(--text-secondary); text-align: center; max-width: 800px; margin: 0 auto; transition: color 0.3s ease;">Every article we publish is either written by or reviewed by industry professionals who have walked the path you're on. We don't just report what's happening—we explain what it means for your business and how you can act on it.</p>
</section>

<!-- Meet the Team -->
<section style="margin-bottom: 40px;">
	<h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 30px; color: var(--text-primary); text-align: center;">The Experts Behind the Insights</h2>
	<div class="team-members-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
		<!-- Team Member 1 -->
		<div class="team-member-card" style="background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); text-align: center; transition: all 0.3s;">
			<img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop" alt="Sarah Mitchell" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin: 0 auto 20px; display: block; border: 4px solid var(--bg-secondary);">
			<h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 8px; color: var(--accent-secondary);">Sarah Mitchell</h3>
			<p style="font-size: 1rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 15px;">Founder & Editor-in-Chief</p>
			<p style="font-size: 0.95rem; line-height: 1.7; color: var(--text-secondary); margin-bottom: 20px;">With over 15 years of experience in business journalism and entrepreneurship, Sarah has helped thousands of small business owners navigate complex challenges. She holds an MBA from Harvard Business School.</p>
			<?php
			// Get social media links from theme customizer
			$social_linkedin = get_theme_mod( 'social_linkedin', 'https://linkedin.com/company/smeinsights' );
			$social_twitter = get_theme_mod( 'social_twitter', 'https://twitter.com/smeinsights' );
			?>
			<div style="display: flex; justify-content: center; gap: 15px;">
				<?php if ( ! empty( $social_linkedin ) && $social_linkedin !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_linkedin ); ?>" style="color: var(--accent-secondary); text-decoration: none; font-size: 1.2rem;" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">LinkedIn</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_twitter ) && $social_twitter !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_twitter ); ?>" style="color: var(--accent-secondary); text-decoration: none; font-size: 1.2rem;" aria-label="Twitter" target="_blank" rel="noopener noreferrer">Twitter</a>
				<?php endif; ?>
			</div>
		</div>

		<!-- Team Member 2 -->
		<div class="team-member-card" style="background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); text-align: center; transition: all 0.3s;">
			<img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop" alt="David Chen" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin: 0 auto 20px; display: block; border: 4px solid var(--bg-secondary);">
			<h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 8px; color: var(--accent-secondary);">David Chen</h3>
			<p style="font-size: 1rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 15px;">Lead Financial Analyst</p>
			<p style="font-size: 0.95rem; line-height: 1.7; color: var(--text-secondary); margin-bottom: 20px;">A certified financial planner with 17 years of experience helping SMEs optimize their finances. David has worked with over 500 small businesses on funding, tax strategy, and financial planning.</p>
			<div style="display: flex; justify-content: center; gap: 15px;">
				<?php if ( ! empty( $social_linkedin ) && $social_linkedin !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_linkedin ); ?>" style="color: var(--accent-secondary); text-decoration: none; font-size: 1.2rem;" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">LinkedIn</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_twitter ) && $social_twitter !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_twitter ); ?>" style="color: var(--accent-secondary); text-decoration: none; font-size: 1.2rem;" aria-label="Twitter" target="_blank" rel="noopener noreferrer">Twitter</a>
				<?php endif; ?>
			</div>
		</div>

		<!-- Team Member 3 -->
		<div class="team-member-card" style="background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); text-align: center; transition: all 0.3s;">
			<img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop" alt="Emily Rodriguez" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin: 0 auto 20px; display: block; border: 4px solid var(--bg-secondary);">
			<h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 8px; color: var(--accent-secondary);">Emily Rodriguez</h3>
			<p style="font-size: 1rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 15px;">Marketing Strategy Expert</p>
			<p style="font-size: 0.95rem; line-height: 1.7; color: var(--text-secondary); margin-bottom: 20px;">Serial entrepreneur and digital marketing specialist, Emily has built and scaled three successful businesses, generating over $50M in combined revenue. She shares real-world strategies that actually work.</p>
			<div style="display: flex; justify-content: center; gap: 15px;">
				<?php if ( ! empty( $social_linkedin ) && $social_linkedin !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_linkedin ); ?>" style="color: var(--accent-secondary); text-decoration: none; font-size: 1.2rem;" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">LinkedIn</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_twitter ) && $social_twitter !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_twitter ); ?>" style="color: var(--accent-secondary); text-decoration: none; font-size: 1.2rem;" aria-label="Twitter" target="_blank" rel="noopener noreferrer">Twitter</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<!-- Our Core Values -->
<section style="margin-bottom: 40px; padding: 40px; background: var(--bg-secondary); border-radius: 12px;">
	<h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 20px; color: var(--text-primary); text-align: center;">What We Stand For</h2>
	<p style="text-align: center; font-size: 1rem; color: var(--text-secondary); margin-bottom: 30px; max-width: 700px; margin-left: auto; margin-right: auto;">Our core values guide everything we do and shape the content we deliver to you.</p>
	<div class="core-values-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
		<div class="core-value-card" style="background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); padding: 40px 30px; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); transition: all 0.3s; border: 1px solid var(--border-color); position: relative; overflow: hidden; text-align: center;">
			<div style="position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(234, 88, 12, 0.05) 0%, transparent 100%); border-radius: 0 0 0 100px;"></div>
			<div class="core-value-icon" style="width: 70px; height: 70px; margin: 0 auto 25px; background: #ea580c; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1;">
				<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #fff;">
					<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<h3 class="core-value-title" style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px; color: var(--text-primary);">Practicality Over Theory</h3>
			<p class="core-value-description" style="font-size: 1rem; line-height: 1.7; color: var(--text-secondary);">We focus on strategies you can implement today, not abstract concepts. Every piece of content is designed to be actionable and immediately useful.</p>
		</div>

		<div class="core-value-card" style="background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); padding: 40px 30px; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); transition: all 0.3s; border: 1px solid var(--border-color); position: relative; overflow: hidden; text-align: center;">
			<div style="position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, transparent 100%); border-radius: 0 0 0 100px;"></div>
			<div class="core-value-icon" style="width: 70px; height: 70px; margin: 0 auto 25px; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1;">
				<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #fff;">
					<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<h3 class="core-value-title" style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px; color: var(--text-primary);">Expert-Driven Content</h3>
			<p class="core-value-description" style="font-size: 1rem; line-height: 1.7; color: var(--text-secondary);">Every article is written by or reviewed by a real expert in their field. We don't publish generic content—we deliver insights from people who've been there.</p>
		</div>

		<div class="core-value-card" style="background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); padding: 40px 30px; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); transition: all 0.3s; border: 1px solid var(--border-color); position: relative; overflow: hidden; text-align: center;">
			<div style="position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(14, 165, 233, 0.05) 0%, transparent 100%); border-radius: 0 0 0 100px;"></div>
			<div class="core-value-icon" style="width: 70px; height: 70px; margin: 0 auto 25px; background: #0ea5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1;">
				<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #fff;">
					<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<h3 class="core-value-title" style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px; color: var(--text-primary);">Community-Focused</h3>
			<p class="core-value-description" style="font-size: 1rem; line-height: 1.7; color: var(--text-secondary);">We're a platform for experts to share their knowledge, not just a publication. Our community of contributors and readers drives everything we do.</p>
		</div>

		<div class="core-value-card" style="background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); padding: 40px 30px; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); transition: all 0.3s; border: 1px solid var(--border-color); position: relative; overflow: hidden; text-align: center;">
			<div style="position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(6, 95, 70, 0.05) 0%, transparent 100%); border-radius: 0 0 0 100px;"></div>
			<div class="core-value-icon" style="width: 70px; height: 70px; margin: 0 auto 25px; background: #065f46; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1;">
				<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #fff;">
					<circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<h3 class="core-value-title" style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px; color: var(--text-primary);">Transparency & Trust</h3>
			<p class="core-value-description" style="font-size: 1rem; line-height: 1.7; color: var(--text-secondary);">We're transparent about our sources, our experts, and our methods. Trust is earned, and we work every day to maintain yours.</p>
		</div>
	</div>
</section>

<!-- Final Call-to-Action -->
<section style="background: var(--breaking-gradient); color: #fff; padding: 40px; border-radius: 12px; text-align: center;">
	<h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 20px;">Join Our Community</h2>
	<p style="font-size: 1rem; margin-bottom: 30px; opacity: 0.95; max-width: 700px; margin-left: auto; margin-right: auto;">Become part of a community of business leaders who are committed to growth, learning, and success. Whether you want to stay informed, share your expertise, or connect with like-minded entrepreneurs, we're here for you.</p>
	<div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
		<a href="#" onclick="event.preventDefault(); if(typeof openSubscriptionModal === 'function') { openSubscriptionModal(); } return false;" class="about-cta-button about-cta-button-primary" style="background: #fff; color: var(--accent-primary); padding: 15px 35px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 1.1rem; transition: all 0.3s; display: inline-block;">Subscribe to Newsletter</a>
		<?php
		// Get Become a Contributor page - try multiple methods
		$contributor_page = null;
		$contributor_slugs = array( 'become-contributor', 'become-a-contributor' );
		foreach ( $contributor_slugs as $slug ) {
			$contributor_page = get_page_by_path( $slug );
			if ( $contributor_page ) {
				break;
			}
		}
		if ( ! $contributor_page ) {
			$contributor_page = sme_get_page_by_title( 'Become a Contributor' );
		}
		if ( ! $contributor_page ) {
			// Try to find by searching all pages
			$pages = get_pages( array( 'post_status' => 'publish' ) );
			foreach ( $pages as $page ) {
				if ( stripos( $page->post_title, 'contributor' ) !== false || 
				     stripos( $page->post_name, 'contributor' ) !== false ) {
					$contributor_page = $page;
					break;
				}
			}
		}
		$contributor_url = $contributor_page ? get_permalink( $contributor_page->ID ) : '#';
		
		// Get Contact page - try multiple methods
		$contact_page = null;
		$contact_slugs = array( 'contact', 'contact-us', 'contactus' );
		foreach ( $contact_slugs as $slug ) {
			$contact_page = get_page_by_path( $slug );
			if ( $contact_page ) {
				break;
			}
		}
		if ( ! $contact_page ) {
			$contact_page = sme_get_page_by_title( 'Contact' );
		}
		if ( ! $contact_page ) {
			$contact_page = sme_get_page_by_title( 'Contact Us' );
		}
		if ( ! $contact_page ) {
			// Try to find by searching all pages
			$pages = get_pages( array( 'post_status' => 'publish' ) );
			foreach ( $pages as $page ) {
				if ( stripos( $page->post_title, 'contact' ) !== false || 
				     stripos( $page->post_name, 'contact' ) !== false ) {
					$contact_page = $page;
					break;
				}
			}
		}
		$contact_url = $contact_page ? get_permalink( $contact_page->ID ) : '#';
		
		// Fallback: if permalink is empty or invalid, try home_url
		if ( empty( $contributor_url ) || $contributor_url === '#' ) {
			$contributor_url = home_url( '/become-contributor' );
		}
		if ( empty( $contact_url ) || $contact_url === '#' ) {
			$contact_url = home_url( '/contact' );
		}
		?>
		<a href="<?php echo esc_url( $contributor_url ); ?>" class="about-cta-button about-cta-button-secondary" style="background: transparent; color: #fff; padding: 15px 35px; border: 2px solid #fff; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 1.1rem; transition: all 0.3s; display: inline-block;">Become a Contributor</a>
		<a href="<?php echo esc_url( $contact_url ); ?>" class="about-cta-button about-cta-button-secondary" style="background: transparent; color: #fff; padding: 15px 35px; border: 2px solid #fff; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 1.1rem; transition: all 0.3s; display: inline-block;">Contact Us</a>
	</div>
</section>
