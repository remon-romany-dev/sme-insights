<?php
/**
 * Legal Content Sections
 * Reusable components for legal pages (Privacy Policy, Terms, Disclaimer)
 *
 * @package SME_Insights
 * @since 1.0.0
 */

$page_slug = get_post_field( 'post_name', get_the_ID() );
?>

<?php if ( $page_slug === 'privacy-policy' ) : ?>
	<!-- Privacy Policy Content -->
	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>1. Information We Collect</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 20px;">We collect information that you provide directly to us, such as when you subscribe to our newsletter, submit a contact form, or interact with our website.</p>
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 25px 0 15px;">1.1. Information You Provide to Us</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We collect information that you provide directly to us when you:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Subscribe to our newsletter: We collect your name and email address.</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Submit a contact form: We collect your name, email address, subject, and message.</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Leave a comment: We collect your name, email address, and comment content.</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Apply to become a contributor: We collect your name, email address, and any additional information you provide in your application.</li>
		</ul>

		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">1.2. Information We Collect Automatically</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">When you visit our website, we automatically collect certain information about your device and how you interact with our site, including:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">IP address</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Browser type and version</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Operating system</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Pages you visit and time spent on each page</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Referring website addresses</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Date and time of your visit</li>
		</ul>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">This information is collected through cookies and similar tracking technologies. For more details, please see our Cookies section below.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>2. How We Use Your Information</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 20px;">We use the information we collect to provide, maintain, and improve our services, send you newsletters and updates, and respond to your inquiries.</p>
		
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Specifically, we use your information to:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Provide and improve our services and website functionality</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Send you our newsletter and other communications (if you have subscribed)</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Respond to your inquiries and provide customer support</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Analyze website usage and understand our audience better</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Prevent fraud and ensure the security of our website</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Comply with legal obligations</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Personalize your experience on our website</li>
		</ul>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>3. Cookies and Tracking Technologies</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We use cookies and similar tracking technologies to collect and store information about your preferences and activity on our website.</p>
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">What are Cookies?</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Cookies are small text files that are placed on your device when you visit a website. They help us remember your preferences and improve your browsing experience.</p>
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">How We Use Cookies</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We use cookies for:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;"><strong>Analytics:</strong> To understand how visitors use our website (e.g., Google Analytics)</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;"><strong>Preferences:</strong> To remember your settings and preferences</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;"><strong>Functionality:</strong> To enable certain features and functionality of our website</li>
		</ul>
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">Managing Cookies</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">You can control or disable cookies through your browser settings. However, please note that disabling cookies may affect the functionality of our website. Most browsers allow you to:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">See what cookies you have and delete them individually</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Block third-party cookies</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Block all cookies from specific sites</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Block all cookies</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Delete all cookies when you close your browser</li>
		</ul>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>4. How We Share Your Information</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We do not sell your personal information to third parties. We may share your information only in the following circumstances:</p>
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">4.1. Service Providers</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We may share your information with trusted third-party service providers who assist us in operating our website and conducting our business. These providers are contractually obligated to protect your information and use it only for the purposes we specify. Examples include:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Email service providers (for sending newsletters)</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Analytics providers (for understanding website usage)</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Hosting providers (for storing and serving our website)</li>
		</ul>
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">4.2. Legal Requirements</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We may disclose your information if required by law or in response to valid requests by public authorities (e.g., a court or government agency).</p>
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">4.3. Business Transfers</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">In the event of a merger, acquisition, or sale of assets, your information may be transferred as part of that transaction. We will notify you of any such change in ownership or control of your personal information.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>5. Data Security</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 20px;">We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
		
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">These security measures include:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Using SSL/HTTPS encryption for data transmission</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Regular security assessments and updates</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Access controls and authentication procedures</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Secure data storage practices</li>
		</ul>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">However, please be aware that no method of transmission over the Internet or electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your information, we cannot guarantee absolute security.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>6. Your Rights</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Depending on your location, you may have certain rights regarding your personal information, including:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;"><strong>Right to Access:</strong> You can request a copy of the personal information we hold about you.</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;"><strong>Right to Rectification:</strong> You can request that we correct any inaccurate or incomplete information.</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;"><strong>Right to Erasure:</strong> You can request that we delete your personal information (also known as the "right to be forgotten").</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;"><strong>Right to Object:</strong> You can object to our processing of your personal information for certain purposes.</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;"><strong>Right to Data Portability:</strong> You can request that we transfer your information to another service provider.</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;"><strong>Right to Withdraw Consent:</strong> If we process your information based on consent, you can withdraw that consent at any time.</li>
		</ul>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">To exercise any of these rights, please contact us using the information provided in the "Contact Us" section below.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>7. Links to Other Websites</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Our website may contain links to other websites that are not operated by us. If you click on a third-party link, you will be directed to that third party's site. We strongly advise you to review the Privacy Policy of every site you visit.</p>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>8. Children's Privacy</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Our website is not intended for children under the age of 13. We do not knowingly collect personal information from children under 13. If you are a parent or guardian and believe that your child has provided us with personal information, please contact us immediately, and we will delete such information from our records.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>9. Changes to This Privacy Policy</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date at the top of this page.</p>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>
		</div>
	</section>

	<div class="contact-box" style="background: var(--bg-secondary); padding: 30px; border-radius: 12px; margin-top: 50px; text-align: center;">
		<h3 style="font-size: 1.5rem; margin-bottom: 15px; color: var(--text-primary);">Contact Us</h3>
		<p style="color: var(--text-secondary); margin-bottom: 10px;">If you have any questions about this Privacy Policy, please contact us:</p>
		<p style="color: var(--text-secondary); margin-bottom: 10px;"><strong>Email:</strong> <a href="mailto:privacy@smeinsights.com" style="color: var(--accent-secondary); font-weight: 600;">privacy@smeinsights.com</a></p>
		<p style="color: var(--text-secondary); margin-bottom: 10px;"><strong>General Inquiries:</strong> <a href="mailto:info@smeinsights.com" style="color: var(--accent-secondary); font-weight: 600;">info@smeinsights.com</a></p>
		<p style="margin-top: 20px;"><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" style="color: var(--accent-secondary); font-weight: 600;">Visit our Contact Page →</a></p>
	</div>

<?php elseif ( $page_slug === 'terms-of-service' ) : ?>
	<!-- Terms of Service Content -->
	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>1. Use of Our Website</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">1.1. Permitted Use</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We grant you a limited, non-exclusive, non-transferable, and revocable license to access and use our website for personal, non-commercial purposes. This license does not include any right to:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Reproduce, distribute, or create derivative works from our content</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Use our content for commercial purposes without our express written permission</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Remove any copyright, trademark, or other proprietary notices from our content</li>
		</ul>

		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">1.2. Prohibited Use</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">You agree not to use our website in any way that:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Is unlawful, illegal, or violates any applicable laws or regulations</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Infringes upon or violates our intellectual property rights or the rights of others</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Contains harmful, abusive, defamatory, harassing, or offensive content</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Involves spamming, phishing, or any form of unsolicited communication</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Attempts to gain unauthorized access to our servers, systems, or data</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Uses automated systems (bots, scrapers) to collect content without our express written permission</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Interferes with or disrupts the website's functionality or security</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Impersonates any person or entity or misrepresents your affiliation with any person or entity</li>
		</ul>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>2. Intellectual Property Rights</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">All content on this website, including but not limited to articles, images, logos, graphics, designs, and software, is the property of SME Insights and is protected by copyright, trademark, and other intellectual property laws.</p>
		
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;"><strong>You may not:</strong></p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Copy, reproduce, distribute, or create derivative works from our content without our express written permission</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Use our trademarks, logos, or brand names without our prior written consent</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Remove or alter any copyright, trademark, or other proprietary notices</li>
		</ul>
		
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;"><strong>You may:</strong></p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Share links to our articles on social media using the share buttons provided on our website</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Quote brief excerpts from our articles for the purpose of commentary, criticism, or news reporting, provided you attribute the source and include a link back to the original article</li>
		</ul>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>3. User-Generated Content (e.g., Comments)</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Our website may allow you to post comments and other user-generated content. By posting content on our website, you agree to the following:</p>
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">3.1. Your Responsibility</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">You are solely responsible for any content you post. You represent and warrant that:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">You own or have the right to post the content</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Your content does not violate any third-party rights (including intellectual property, privacy, or publicity rights)</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Your content is not defamatory, abusive, harassing, or otherwise objectionable</li>
		</ul>
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">3.2. Our Rights</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We reserve the right (but not the obligation) to:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Monitor, review, edit, or delete any user-generated content at any time, without prior notice</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Remove content that we determine, in our sole discretion, violates these Terms or is otherwise objectionable</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Disclose your identity or information if required by law or in response to a valid legal request</li>
		</ul>
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">3.3. License to Us</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">By posting content on our website, you grant us a worldwide, non-exclusive, royalty-free, perpetual, and irrevocable license to use, reproduce, modify, adapt, publish, translate, and distribute your content in connection with the operation of our website.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>4. Disclaimers</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">4.1. No Professional Advice</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;"><strong>Important:</strong> The content provided on this website is for informational purposes only and does not constitute professional advice. Specifically:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;"><strong>Financial Information:</strong> Our articles about finance, taxes, and investments are not financial advice. Always consult with a qualified financial advisor or accountant before making financial decisions.</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;"><strong>Legal Information:</strong> Our articles about business law and legal matters are not legal advice. Always consult with a qualified attorney for legal matters.</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;"><strong>General Business Advice:</strong> While we strive to provide accurate and helpful information, every business situation is unique. Our content should not be considered a substitute for professional consultation.</li>
		</ul>
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">4.2. "As Is" Basis</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Our website and its content are provided "as is" and "as available" without any warranties of any kind, either express or implied, including but not limited to:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Warranties of merchantability</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Warranties of fitness for a particular purpose</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Warranties of accuracy, completeness, or reliability</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Warranties that the website will be uninterrupted, secure, or error-free</li>
		</ul>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>5. Limitation of Liability</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">To the maximum extent permitted by applicable law, SME Insights, its owners, employees, contributors, and affiliates shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Loss of profits, revenue, data, or business opportunities</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Damages arising from your use or inability to use our website</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Damages arising from any errors, omissions, or inaccuracies in our content</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Damages arising from any actions taken based on information provided on our website</li>
		</ul>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">In no event shall our total liability to you exceed the amount you paid to us (if any) in the twelve (12) months preceding the claim, or $100, whichever is greater.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>6. Indemnification</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">You agree to indemnify, defend, and hold harmless SME Insights, its owners, employees, contributors, and affiliates from and against any claims, damages, losses, liabilities, costs, and expenses (including reasonable attorneys' fees) arising out of or related to:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Your use of our website</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Your violation of these Terms</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Your violation of any third-party rights</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Any content you post on our website</li>
		</ul>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>7. Termination</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We reserve the right to terminate or suspend your access to our website, at any time, without prior notice, for any reason, including but not limited to:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Violation of these Terms</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Engaging in fraudulent, illegal, or harmful activities</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">At our sole discretion, for any other reason</li>
		</ul>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Upon termination, your right to use the website will immediately cease, and we may delete any content you have posted.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>8. Governing Law</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">These Terms shall be governed by and construed in accordance with the laws of the United Arab Emirates, without regard to its conflict of law provisions. Any disputes arising from these Terms or your use of our website shall be subject to the exclusive jurisdiction of the courts of the United Arab Emirates.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>9. Changes to These Terms</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We reserve the right to modify or update these Terms at any time. We will indicate the date of the last update at the top of this page. Your continued use of our website after any changes constitutes your acceptance of the new Terms.</p>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">If you do not agree with the updated Terms, you must stop using our website immediately.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>10. Severability</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">If any provision of these Terms is found to be invalid, illegal, or unenforceable, the remaining provisions shall continue in full force and effect.</p>
		</div>
	</section>

	<div class="contact-box" style="background: var(--bg-secondary); padding: 30px; border-radius: 12px; margin-top: 50px; text-align: center;">
		<h3 style="font-size: 1.5rem; margin-bottom: 15px; color: var(--text-primary);">Contact Us</h3>
		<p style="color: var(--text-secondary); margin-bottom: 10px;">If you have any questions about these Terms of Service, please contact us:</p>
		<p style="color: var(--text-secondary); margin-bottom: 10px;"><strong>Email:</strong> <a href="mailto:legal@smeinsights.com" style="color: var(--accent-secondary); font-weight: 600;">legal@smeinsights.com</a></p>
		<p style="color: var(--text-secondary); margin-bottom: 10px;"><strong>General Inquiries:</strong> <a href="mailto:info@smeinsights.com" style="color: var(--accent-secondary); font-weight: 600;">info@smeinsights.com</a></p>
		<p style="margin-top: 20px;"><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" style="color: var(--accent-secondary); font-weight: 600;">Visit our Contact Page →</a></p>
	</div>

<?php elseif ( $page_slug === 'disclaimer' ) : ?>
	<!-- Disclaimer Content -->
	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>1. No Professional Advice</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 20px;">The content on this website, including articles, guides, and resources, is provided for informational purposes only. It is not intended to be a substitute for professional advice, diagnosis, or treatment.</p>
		
		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 25px 0 15px;">1.1. Financial Information</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Articles and content related to finance, taxes, investments, and business funding are for informational purposes only and do not constitute financial, tax, or investment advice. You should always consult with:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">A qualified financial advisor before making investment decisions</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">A certified public accountant (CPA) or tax professional for tax-related matters</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">A licensed financial planner for comprehensive financial planning</li>
		</ul>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;"><strong>We are not responsible for any financial losses or decisions made based on information from our website.</strong></p>

		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">1.2. Legal Information</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Articles and content related to business law, intellectual property, contracts, and legal matters are for informational purposes only and do not constitute legal advice. You should always consult with:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">A qualified attorney licensed in your jurisdiction for legal matters</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">A legal professional before making any legal decisions or entering into contracts</li>
		</ul>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;"><strong>We are not responsible for any legal consequences resulting from information on our website.</strong></p>

		<h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 30px 0 15px;">1.3. Business Advice</h3>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">While we strive to provide accurate and helpful information, every business situation is unique. Our content should not be considered a substitute for:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Professional business consultation</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Industry-specific expertise</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Personalized business strategy development</li>
		</ul>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>2. Accuracy of Information</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We make every effort to ensure the accuracy of the information on our website. However:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">We do not warrant or guarantee the accuracy, completeness, or timeliness of any information</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Information may become outdated over time</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Laws, regulations, and business practices may change</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">We reserve the right to update, modify, or remove content at any time without notice</li>
		</ul>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>3. No Warranties</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">This website and its content are provided "as is" without any warranties, express or implied. We disclaim all warranties, including but not limited to:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Warranties of merchantability</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Warranties of fitness for a particular purpose</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Warranties of accuracy or reliability</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Warranties that the website will be uninterrupted or error-free</li>
		</ul>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>4. Limitation of Liability</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">To the maximum extent permitted by law, SME Insights, its owners, employees, contributors, and affiliates shall not be liable for any direct, indirect, incidental, special, consequential, or punitive damages arising from:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Your use or inability to use our website</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Any errors or omissions in the content</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Any actions taken based on information from our website</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Any loss of profits, revenue, data, or business opportunities</li>
		</ul>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>5. External Links</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Our website may contain links to external websites that are not operated by us. We have no control over the nature, content, and availability of those sites. The inclusion of any links does not necessarily imply a recommendation or endorsement of the views expressed within them.</p>
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We are not responsible for the content, privacy policies, or practices of any third-party websites.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>6. Third-Party Content</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Some content on our website may be provided by third-party contributors or guest authors. We do not necessarily endorse or verify the accuracy of third-party content. The views and opinions expressed in such content are solely those of the authors and do not necessarily reflect our views.</p>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>7. Sponsored Content</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">Some content on our website may be sponsored or contain affiliate links. We will always clearly disclose when content is sponsored or contains affiliate links. However, we are not responsible for:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">The quality, accuracy, or reliability of products or services promoted in sponsored content</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Any transactions between you and third-party advertisers or affiliates</li>
		</ul>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>8. Your Responsibility</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">You are solely responsible for:</p>
		<ul style="margin: 20px 0; padding-left: 30px;">
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Evaluating the accuracy and appropriateness of information for your specific situation</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Seeking professional advice when necessary</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Making your own informed decisions based on your circumstances</li>
			<li style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 10px;">Verifying any information before taking action</li>
		</ul>
		</div>
	</section>

	<section class="contributor-accordion-item">
		<button class="contributor-accordion-header" type="button" aria-expanded="false">
			<h2>9. Changes to This Disclaimer</h2>
			<span class="contributor-accordion-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
		</button>
		<div class="contributor-accordion-content">
		<p style="font-size: 1rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 15px;">We reserve the right to modify this disclaimer at any time. We will indicate the date of the last update when applicable. Your continued use of our website after any changes constitutes your acceptance of the updated disclaimer.</p>
		</div>
	</section>

	<div class="contact-box" style="background: var(--bg-secondary); padding: 30px; border-radius: 12px; margin-top: 50px; text-align: center;">
		<h3 style="font-size: 1.5rem; margin-bottom: 15px; color: var(--text-primary);">Questions?</h3>
		<p style="color: var(--text-secondary); margin-bottom: 10px;">If you have any questions about this disclaimer, please contact us:</p>
		<p style="color: var(--text-secondary); margin-bottom: 10px;"><strong>Email:</strong> <a href="mailto:legal@smeinsights.com" style="color: var(--accent-secondary); font-weight: 600;">legal@smeinsights.com</a></p>
		<p style="color: var(--text-secondary); margin-bottom: 10px;"><strong>General Inquiries:</strong> <a href="mailto:info@smeinsights.com" style="color: var(--accent-secondary); font-weight: 600;">info@smeinsights.com</a></p>
		<p style="margin-top: 20px;"><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" style="color: var(--accent-secondary); font-weight: 600;">Visit our Contact Page →</a></p>
	</div>

<?php endif; ?>

