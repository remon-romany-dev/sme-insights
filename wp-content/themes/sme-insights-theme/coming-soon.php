<?php
/**
 * Template Name: Coming Soon
 * 
 * Coming Soon page template
 * 
 * @package SME_Insights
 * @since 1.0.0
 * @author Remon Romany
 * @link https://prortec.com/remon-romany/
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get theme mods
$title = get_theme_mod( 'sme_coming_soon_title', "We're Launching Soon!" );
$description = get_theme_mod( 'sme_coming_soon_description', "We're working hard to bring you the latest insights, strategies, and tools to help your small business thrive. Stay tuned for something amazing!" );
$email_placeholder = get_theme_mod( 'sme_coming_soon_email_placeholder', 'Enter your email to get notified' );
$accent_primary = get_theme_mod( 'sme_accent_primary', '#1a365d' );
$accent_secondary = get_theme_mod( 'sme_accent_secondary', '#2563eb' );

// Countdown Timer Settings
$countdown_enabled = get_theme_mod( 'sme_coming_soon_countdown_enable', false );
$countdown_date = get_theme_mod( 'sme_coming_soon_countdown_date', '' );
$countdown_time = get_theme_mod( 'sme_coming_soon_countdown_time', '12:00' );

// Progress Bar Settings
$progress_enabled = get_theme_mod( 'sme_coming_soon_progress_enable', false );
$progress_percentage = get_theme_mod( 'sme_coming_soon_progress_percentage', 75 );

// Contact Settings
$show_contact_email = get_theme_mod( 'sme_coming_soon_show_contact_email', false );
$contact_email = get_theme_mod( 'sme_coming_soon_contact_email', '' );
$show_social_media = get_theme_mod( 'sme_coming_soon_show_social_media', true );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo esc_html( $title ); ?> - <?php bloginfo( 'name' ); ?></title>
	
	<!-- SEO Meta Tags -->
	<meta name="description" content="<?php echo esc_attr( wp_strip_all_tags( $description ) ); ?>">
	<meta name="robots" content="noindex, nofollow">
	<meta name="author" content="<?php bloginfo( 'name' ); ?>">
	
	<!-- Open Graph Meta Tags -->
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?> - <?php bloginfo( 'name' ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( wp_strip_all_tags( $description ) ); ?>">
	<meta property="og:type" content="website">
	<meta property="og:url" content="<?php echo esc_url( home_url( '/' ) ); ?>">
	<meta property="og:site_name" content="<?php bloginfo( 'name' ); ?>">
	
	<!-- Twitter Card Meta Tags -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?> - <?php bloginfo( 'name' ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( wp_strip_all_tags( $description ) ); ?>">
	
	<?php wp_head(); ?>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		
	body {
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
		background: linear-gradient(135deg, #f0f7ff 0%, #e0f2fe 25%, #dbeafe 50%, #e0f2fe 75%, #f0f7ff 100%);
		background-size: 400% 400%;
		background-attachment: fixed;
		animation: gradientShift 20s ease infinite;
		color: #1a365d;
		min-height: 100vh;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 20px;
		overflow-x: hidden;
		position: relative;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}
	
	@keyframes gradientShift {
		0% { background-position: 0% 50%; }
		50% { background-position: 100% 50%; }
		100% { background-position: 0% 50%; }
	}
	
	body::after {
		content: '';
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-image: 
			radial-gradient(circle at 20% 30%, rgba(37, 99, 235, 0.08) 0%, transparent 50%),
			radial-gradient(circle at 80% 70%, rgba(59, 130, 246, 0.06) 0%, transparent 50%),
			radial-gradient(circle at 50% 50%, rgba(96, 165, 250, 0.04) 0%, transparent 60%);
		pointer-events: none;
		z-index: 0;
	}
		
	/* Subtle animated background elements */
	body::before {
		content: '';
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-image: 
			radial-gradient(circle at 15% 25%, rgba(37, 99, 235, 0.05) 0%, transparent 40%),
			radial-gradient(circle at 85% 75%, rgba(59, 130, 246, 0.04) 0%, transparent 40%),
			radial-gradient(circle at 50% 50%, rgba(96, 165, 250, 0.03) 0%, transparent 50%);
		animation: float 25s ease-in-out infinite;
		pointer-events: none;
		z-index: 1;
	}
	
	@keyframes float {
		0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 1; }
		50% { transform: translateY(-30px) rotate(5deg); opacity: 0.8; }
	}
		
	.coming-soon-container {
		max-width: 850px;
		width: 100%;
		text-align: center;
		position: relative;
		z-index: 2;
		background: rgba(255, 255, 255, 0.95);
		backdrop-filter: blur(30px) saturate(180%);
		-webkit-backdrop-filter: blur(30px) saturate(180%);
		padding: 70px 50px;
		border-radius: 32px;
		border: 2px solid rgba(59, 130, 246, 0.2);
		box-shadow: 0 25px 80px rgba(37, 99, 235, 0.15), 
		            0 0 0 1px rgba(59, 130, 246, 0.1) inset,
		            0 15px 50px rgba(37, 99, 235, 0.1);
		transition: transform 0.3s ease, box-shadow 0.3s ease;
	}
	
	.coming-soon-container:hover {
		transform: translateY(-5px);
		box-shadow: 0 30px 100px rgba(37, 99, 235, 0.2), 
		            0 0 0 1px rgba(59, 130, 246, 0.15) inset,
		            0 20px 60px rgba(37, 99, 235, 0.15);
	}
		
	.coming-soon-logo {
		font-size: 3.5rem;
		font-weight: 900;
		letter-spacing: 4px;
		margin-bottom: 35px;
		text-transform: uppercase;
		opacity: 1;
		animation: fadeInDown 0.6s ease;
		background: linear-gradient(135deg, #1a365d 0%, #2563eb 100%);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		background-clip: text;
		text-shadow: none;
	}
		
		@keyframes fadeInDown {
			from {
				opacity: 0;
				transform: translateY(-30px);
			}
			to {
				opacity: 0.95;
				transform: translateY(0);
			}
		}
		
	.coming-soon-title {
		font-size: 4rem;
		font-weight: 800;
		margin-bottom: 25px;
		line-height: 1.15;
		animation: fadeInUp 0.8s ease;
		color: #1a365d;
		text-shadow: none;
		letter-spacing: -1px;
	}
	
	@media (max-width: 768px) {
		.coming-soon-title {
			font-size: 2.75rem;
			letter-spacing: -0.5px;
		}
	}
	
	@media (max-width: 480px) {
		.coming-soon-title {
			font-size: 2.25rem;
		}
	}
		
		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(30px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
		
	.coming-soon-description {
		font-size: 1.25rem;
		line-height: 1.8;
		margin-bottom: 40px;
		color: #4a5568;
		max-width: 600px;
		margin-left: auto;
		margin-right: auto;
		animation: fadeInUp 1s ease;
	}
		
		.coming-soon-form {
			max-width: 500px;
			margin: 0 auto 50px;
			animation: fadeInUp 1.2s ease;
		}
		
		.coming-soon-form-group {
			display: flex;
			gap: 10px;
			flex-wrap: wrap;
		}
		
	.coming-soon-email-input {
		flex: 1;
		min-width: 250px;
		padding: 16px 20px;
		font-size: 16px;
		border: 2px solid #e2e8f0;
		border-radius: 50px;
		background: #ffffff;
		color: #1a202c;
		transition: all 0.3s ease;
	}
	
	.coming-soon-email-input::placeholder {
		color: #a0aec0;
	}
	
	.coming-soon-email-input:focus {
		outline: none;
		border-color: #2563eb;
		background: #ffffff;
		box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
	}
		
	.coming-soon-submit-btn {
		padding: 18px 45px;
		font-size: 17px;
		font-weight: 700;
		border: none;
		border-radius: 50px;
		background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
		color: <?php echo esc_attr( $accent_primary ); ?>;
		cursor: pointer;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		white-space: nowrap;
		box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2),
		            0 0 0 1px rgba(255, 255, 255, 0.5) inset;
		position: relative;
		overflow: hidden;
	}
	
	.coming-soon-submit-btn::before {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
		transition: left 0.5s ease;
	}
	
	.coming-soon-submit-btn:hover {
		transform: translateY(-4px) scale(1.03);
		box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35),
		            0 0 0 1px rgba(255, 255, 255, 0.7) inset;
		background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
	}
	
	.coming-soon-submit-btn:hover::before {
		left: 100%;
	}
	
	.coming-soon-submit-btn:active {
		transform: translateY(-1px) scale(1.01);
	}
		
		.coming-soon-submit-btn:active {
			transform: translateY(0);
		}
		
		.coming-soon-submit-btn:disabled {
			opacity: 0.6;
			cursor: not-allowed;
			transform: none;
		}
		
		.coming-soon-message {
			margin-top: 20px;
			padding: 15px 20px;
			border-radius: 10px;
			display: none;
			font-size: 0.95rem;
			line-height: 1.5;
			animation: slideDown 0.3s ease-out;
		}
		
		@keyframes slideDown {
			from {
				opacity: 0;
				transform: translateY(-10px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
		
		.coming-soon-message.success {
			background: rgba(34, 197, 94, 0.15);
			border: 1px solid rgba(34, 197, 94, 0.4);
			color: #16a34a;
			display: block;
		}
		
		.coming-soon-message.error {
			background: rgba(239, 68, 68, 0.15);
			border: 1px solid rgba(239, 68, 68, 0.4);
			color: #dc2626;
			display: block;
		}
		
		.coming-soon-features {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 30px;
			margin-top: 60px;
			animation: fadeInUp 1.4s ease;
		}
		
	.coming-soon-feature {
		padding: 35px 25px;
		background: rgba(255, 255, 255, 0.12);
		border-radius: 24px;
		backdrop-filter: blur(15px) saturate(150%);
		-webkit-backdrop-filter: blur(15px) saturate(150%);
		border: 2px solid rgba(255, 255, 255, 0.25);
		transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
		position: relative;
		overflow: hidden;
		box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
	}
		
		.coming-soon-feature::before {
			content: '';
			position: absolute;
			top: -50%;
			left: -50%;
			width: 200%;
			height: 200%;
			background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
			opacity: 0;
			transition: opacity 0.3s ease;
		}
		
	.coming-soon-feature:hover {
		transform: translateY(-10px) scale(1.03);
		background: rgba(255, 255, 255, 0.18);
		box-shadow: 0 15px 45px rgba(0, 0, 0, 0.25),
		            0 0 0 1px rgba(255, 255, 255, 0.3) inset;
		border-color: rgba(255, 255, 255, 0.35);
	}
		
		.coming-soon-feature:hover::before {
			opacity: 1;
		}
		
		.coming-soon-feature-icon {
			font-size: 3rem;
			margin-bottom: 15px;
		}
		
		.coming-soon-feature-title {
			font-size: 1.1rem;
			font-weight: 600;
			margin-bottom: 10px;
		}
		
		.coming-soon-feature-text {
			font-size: 0.9rem;
			opacity: 0.8;
			line-height: 1.6;
		}
		
		.coming-soon-social {
			margin-top: 50px;
			animation: fadeInUp 1.6s ease;
		}
		
		.coming-soon-social-title {
			font-size: 1rem;
			margin-bottom: 20px;
			opacity: 0.8;
		}
		
		.coming-soon-social-links {
			display: flex;
			justify-content: center;
			gap: 15px;
			flex-wrap: wrap;
		}
		
	.coming-soon-social-link {
		width: 48px;
		height: 48px;
		border-radius: 50%;
		background: rgba(255, 255, 255, 0.12);
		backdrop-filter: blur(10px);
		-webkit-backdrop-filter: blur(10px);
		display: flex;
		align-items: center;
		justify-content: center;
		color: #fff;
		text-decoration: none;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		border: 2px solid rgba(255, 255, 255, 0.25);
		font-size: 18px;
		font-weight: 600;
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
	}
	
	.coming-soon-social-link:hover {
		background: rgba(255, 255, 255, 0.25);
		transform: translateY(-5px) scale(1.12);
		box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
		border-color: rgba(255, 255, 255, 0.4);
	}
	
	.coming-soon-social-link:active {
		transform: translateY(-2px) scale(1.05);
	}
		
		/* Countdown Timer Styles */
		.coming-soon-countdown {
			margin: 40px 0;
			animation: fadeInUp 1.3s ease;
		}
		
		.coming-soon-countdown-title {
			font-size: 1.1rem;
			margin-bottom: 25px;
			opacity: 0.9;
			font-weight: 600;
		}
		
		.countdown-timer {
			display: flex;
			justify-content: center;
			gap: 20px;
			flex-wrap: wrap;
		}
		
		.countdown-item {
			background: rgba(255, 255, 255, 0.15);
			backdrop-filter: blur(10px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			border-radius: 15px;
			padding: 20px 25px;
			min-width: 80px;
			text-align: center;
			transition: all 0.3s ease;
		}
		
		.countdown-item:hover {
			transform: translateY(-5px);
			background: rgba(255, 255, 255, 0.2);
			box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
		}
		
		.countdown-number {
			font-size: 2.5rem;
			font-weight: 700;
			line-height: 1;
			margin-bottom: 8px;
			text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
		}
		
		.countdown-label {
			font-size: 0.85rem;
			opacity: 0.8;
			text-transform: uppercase;
			letter-spacing: 1px;
		}
		
		/* Progress Bar Styles */
		.coming-soon-progress {
			margin: 40px 0;
			animation: fadeInUp 1.5s ease;
		}
		
		.coming-soon-progress-title {
			font-size: 1.1rem;
			margin-bottom: 15px;
			opacity: 0.9;
			font-weight: 600;
		}
		
		.progress-bar-container {
			background: rgba(255, 255, 255, 0.1);
			backdrop-filter: blur(10px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			border-radius: 50px;
			height: 30px;
			overflow: hidden;
			position: relative;
		}
		
		.progress-bar-fill {
			height: 100%;
			background: linear-gradient(90deg, #fff 0%, rgba(255, 255, 255, 0.8) 100%);
			border-radius: 50px;
			transition: width 1s ease;
			position: relative;
			overflow: hidden;
		}
		
		.progress-bar-fill::after {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			bottom: 0;
			right: 0;
			background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
			animation: shimmer 2s infinite;
		}
		
		@keyframes shimmer {
			0% { transform: translateX(-100%); }
			100% { transform: translateX(100%); }
		}
		
		.progress-percentage {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			font-size: 0.9rem;
			font-weight: 600;
			color: <?php echo esc_attr( $accent_primary ); ?>;
			text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
		}
		
		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(30px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
		
		@media (max-width: 768px) {
			body {
				padding: 15px;
			}
			
			.coming-soon-container {
				padding: 45px 25px;
				border-radius: 24px;
				max-width: 100%;
			}
			
			.coming-soon-logo {
				font-size: 2.5rem;
				letter-spacing: 2px;
				margin-bottom: 25px;
			}
			
			.coming-soon-title {
				font-size: 2.5rem;
				margin-bottom: 20px;
			}
			
			.coming-soon-description {
				font-size: 1.05rem;
				margin-bottom: 35px;
				line-height: 1.7;
			}
			
			.coming-soon-form {
				margin-bottom: 40px;
			}
			
			.coming-soon-form-group {
				flex-direction: column;
				gap: 12px;
			}
			
			.coming-soon-email-input {
				width: 100%;
				min-width: 100%;
				padding: 15px 20px;
				font-size: 16px;
			}
			
			.coming-soon-submit-btn {
				width: 100%;
				padding: 16px 40px;
			}
			
			.coming-soon-features {
				grid-template-columns: 1fr;
				gap: 20px;
				margin-top: 50px;
			}
			
			.coming-soon-feature {
				padding: 25px 18px;
			}
			
			.coming-soon-feature-icon {
				font-size: 2.5rem;
				margin-bottom: 12px;
			}
			
			.countdown-timer {
				gap: 8px;
			}
			
			.countdown-item {
				padding: 15px 18px;
				min-width: 65px;
				border-radius: 12px;
			}
			
			.countdown-number {
				font-size: 1.75rem;
			}
			
			.countdown-label {
				font-size: 0.7rem;
			}
			
			.progress-bar-container {
				height: 24px;
			}
			
			.progress-percentage {
				font-size: 0.75rem;
			}
			
			.coming-soon-social {
				margin-top: 40px;
			}
			
			.coming-soon-social-link {
				width: 42px;
				height: 42px;
			}
		}
		
		@media (max-width: 480px) {
			body {
				padding: 10px;
			}
			
			.coming-soon-container {
				padding: 35px 20px;
				border-radius: 20px;
			}
			
			.coming-soon-logo {
				font-size: 2rem;
				letter-spacing: 1.5px;
				margin-bottom: 20px;
			}
			
			.coming-soon-title {
				font-size: 2rem;
				margin-bottom: 15px;
			}
			
			.coming-soon-description {
				font-size: 1rem;
				margin-bottom: 30px;
			}
			
			.coming-soon-features {
				gap: 15px;
				margin-top: 40px;
			}
			
			.coming-soon-feature {
				padding: 20px 15px;
			}
			
			.coming-soon-feature-icon {
				font-size: 2rem;
			}
			
			.coming-soon-feature-title {
				font-size: 1rem;
			}
			
			.coming-soon-feature-text {
				font-size: 0.85rem;
			}
			
			.countdown-item {
				padding: 12px 15px;
				min-width: 55px;
			}
			
			.countdown-number {
				font-size: 1.5rem;
			}
			
			.countdown-label {
				font-size: 0.65rem;
			}
		}
		
		@media (max-width: 360px) {
			.coming-soon-container {
				padding: 30px 15px;
			}
			
			.coming-soon-logo {
				font-size: 1.75rem;
			}
			
			.coming-soon-title {
				font-size: 1.75rem;
			}
		}
	</style>
</head>
<body>
	<main id="main" role="main">
	<div class="coming-soon-container">
		<div class="coming-soon-logo"><?php echo esc_html( strtoupper( get_bloginfo( 'name' ) ) ); ?></div>
		
		<h1 class="coming-soon-title"><?php echo esc_html( $title ); ?></h1>
		
		<p class="coming-soon-description"><?php echo esc_html( $description ); ?></p>
		
		<?php if ( $countdown_enabled && ! empty( $countdown_date ) ) : ?>
			<?php
			// Parse countdown date and time
			$countdown_datetime = $countdown_date . ' ' . $countdown_time;
			$countdown_timestamp = strtotime( $countdown_datetime );
			?>
			<div class="coming-soon-countdown">
				<div class="coming-soon-countdown-title">We're Launching In</div>
				<div class="countdown-timer" id="countdownTimer" data-target="<?php echo esc_attr( $countdown_timestamp ); ?>">
					<div class="countdown-item">
						<div class="countdown-number" id="days">00</div>
						<div class="countdown-label">Days</div>
					</div>
					<div class="countdown-item">
						<div class="countdown-number" id="hours">00</div>
						<div class="countdown-label">Hours</div>
					</div>
					<div class="countdown-item">
						<div class="countdown-number" id="minutes">00</div>
						<div class="countdown-label">Minutes</div>
					</div>
					<div class="countdown-item">
						<div class="countdown-number" id="seconds">00</div>
						<div class="countdown-label">Seconds</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		
		<?php if ( $progress_enabled ) : ?>
			<div class="coming-soon-progress">
				<div class="coming-soon-progress-title">We're <?php echo esc_html( $progress_percentage ); ?>% Complete</div>
				<div class="progress-bar-container">
					<div class="progress-bar-fill" style="width: <?php echo esc_attr( $progress_percentage ); ?>%;">
						<span class="progress-percentage"><?php echo esc_html( $progress_percentage ); ?>%</span>
					</div>
				</div>
			</div>
		<?php endif; ?>
		
		<form class="coming-soon-form" id="comingSoonForm">
			<div class="coming-soon-form-group">
				<input 
					type="email" 
					class="coming-soon-email-input" 
					placeholder="<?php echo esc_attr( $email_placeholder ); ?>" 
					required 
					id="comingSoonEmail"
				>
				<button type="submit" class="coming-soon-submit-btn">Notify Me</button>
			</div>
			<div class="coming-soon-message" id="comingSoonMessage"></div>
		</form>
		
		<div class="coming-soon-features">
			<div class="coming-soon-feature">
				<div class="coming-soon-feature-icon">📊</div>
				<div class="coming-soon-feature-title">Business Insights</div>
				<div class="coming-soon-feature-text">Expert analysis and strategies to grow your business</div>
			</div>
			<div class="coming-soon-feature">
				<div class="coming-soon-feature-icon">💡</div>
				<div class="coming-soon-feature-title">Latest Trends</div>
				<div class="coming-soon-feature-text">Stay ahead with the latest industry trends and news</div>
			</div>
			<div class="coming-soon-feature">
				<div class="coming-soon-feature-icon">🚀</div>
				<div class="coming-soon-feature-title">Growth Tools</div>
				<div class="coming-soon-feature-text">Practical tools and resources to accelerate growth</div>
			</div>
		</div>
		
		<?php
		// Contact Email Section
		if ( $show_contact_email && ! empty( $contact_email ) ) :
		?>
		<div class="coming-soon-contact" style="margin-top: 40px; animation: fadeInUp 1.7s ease;">
			<div class="coming-soon-contact-title" style="font-size: 1rem; margin-bottom: 15px; opacity: 0.9; font-weight: 600;">Have Questions?</div>
			<div class="coming-soon-contact-email" style="font-size: 1.1rem;">
				<a href="mailto:<?php echo esc_attr( $contact_email ); ?>" style="color: #fff; text-decoration: none; border-bottom: 1px solid rgba(255, 255, 255, 0.5); transition: all 0.3s ease;" onmouseover="this.style.borderBottomColor='rgba(255,255,255,0.8)'" onmouseout="this.style.borderBottomColor='rgba(255,255,255,0.5)'">
					<?php echo esc_html( $contact_email ); ?>
				</a>
			</div>
		</div>
		<?php endif; ?>
		
		<?php
		// Social Media Section
		if ( $show_social_media ) :
			$social_links = array(
				'facebook'  => get_theme_mod( 'social_facebook', '' ),
				'twitter'   => get_theme_mod( 'social_twitter', '' ),
				'linkedin'  => get_theme_mod( 'social_linkedin', '' ),
				'youtube'   => get_theme_mod( 'social_youtube', '' ),
				'instagram' => get_theme_mod( 'social_instagram', '' ),
			);
			
			$has_social = false;
			foreach ( $social_links as $url ) {
				if ( ! empty( $url ) && $url !== '#' ) {
					$has_social = true;
					break;
				}
			}
			
			if ( $has_social ) :
		?>
		<div class="coming-soon-social">
			<div class="coming-soon-social-title">Follow Us</div>
			<div class="coming-soon-social-links">
				<?php if ( ! empty( $social_links['facebook'] ) && $social_links['facebook'] !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_links['facebook'] ); ?>" class="coming-soon-social-link" target="_blank" rel="noopener">f</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links['twitter'] ) && $social_links['twitter'] !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_links['twitter'] ); ?>" class="coming-soon-social-link" target="_blank" rel="noopener">t</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links['linkedin'] ) && $social_links['linkedin'] !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_links['linkedin'] ); ?>" class="coming-soon-social-link" target="_blank" rel="noopener">in</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links['youtube'] ) && $social_links['youtube'] !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_links['youtube'] ); ?>" class="coming-soon-social-link" target="_blank" rel="noopener">▶</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links['instagram'] ) && $social_links['instagram'] !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_links['instagram'] ); ?>" class="coming-soon-social-link" target="_blank" rel="noopener">📷</a>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
		<?php endif; ?>
	</div>
	</main>
	
	<script>
		(function() {
			const form = document.getElementById('comingSoonForm');
			const emailInput = document.getElementById('comingSoonEmail');
			const messageDiv = document.getElementById('comingSoonMessage');
			const submitBtn = form.querySelector('button[type="submit"]');
			
			if (!form || !emailInput || !messageDiv) return;
			
			form.addEventListener('submit', function(e) {
				e.preventDefault();
				
				const email = emailInput.value.trim();
				
				// Validation
				if (!email || !email.includes('@')) {
					messageDiv.className = 'coming-soon-message error';
					messageDiv.textContent = 'Please enter a valid email address.';
					messageDiv.style.display = 'block';
					return;
				}
				
				// Disable submit button
				submitBtn.disabled = true;
				submitBtn.textContent = 'Subscribing...';
				messageDiv.style.display = 'none';
				
				// Create form data
				const formData = new FormData();
				formData.append('action', 'sme_coming_soon_subscribe');
				formData.append('email', email);
				formData.append('nonce', '<?php echo wp_create_nonce( 'sme_coming_soon_subscribe' ); ?>');
				
				// Send AJAX request
				fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						messageDiv.className = 'coming-soon-message success';
						messageDiv.textContent = data.data.message || 'Thank you! We\'ll notify you when we launch.';
						emailInput.value = '';
					} else {
						messageDiv.className = 'coming-soon-message error';
						messageDiv.textContent = data.data.message || 'Something went wrong. Please try again.';
					}
					messageDiv.style.display = 'block';
					submitBtn.disabled = false;
					submitBtn.textContent = 'Notify Me';
				})
				.catch(error => {
					messageDiv.className = 'coming-soon-message error';
					messageDiv.textContent = 'Network error. Please check your connection and try again.';
					messageDiv.style.display = 'block';
					submitBtn.disabled = false;
					submitBtn.textContent = 'Notify Me';
				});
			});
		})();
		
		// Countdown Timer
		(function() {
			const countdownTimer = document.getElementById('countdownTimer');
			if (!countdownTimer) return;
			
			const targetTimestamp = parseInt(countdownTimer.getAttribute('data-target'), 10);
			if (!targetTimestamp) return;
			
			function updateCountdown() {
				const now = Math.floor(Date.now() / 1000);
				const difference = targetTimestamp - now;
				
				const daysEl = document.getElementById('days');
				const hoursEl = document.getElementById('hours');
				const minutesEl = document.getElementById('minutes');
				const secondsEl = document.getElementById('seconds');
				
				if (difference <= 0) {
					// Countdown finished
					daysEl.textContent = '00';
					hoursEl.textContent = '00';
					minutesEl.textContent = '00';
					secondsEl.textContent = '00';
					return;
				}
				
				const days = Math.floor(difference / 86400);
				const hours = Math.floor((difference % 86400) / 3600);
				const minutes = Math.floor((difference % 3600) / 60);
				const seconds = difference % 60;
				
				// Batch DOM updates to reduce forced reflow
				requestAnimationFrame(function() {
					daysEl.textContent = String(days).padStart(2, '0');
					hoursEl.textContent = String(hours).padStart(2, '0');
					minutesEl.textContent = String(minutes).padStart(2, '0');
					secondsEl.textContent = String(seconds).padStart(2, '0');
				});
			}
			
			// Update immediately
			updateCountdown();
			
			// Update every second
			setInterval(updateCountdown, 1000);
		})();
	</script>
	
	<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "WebSite",
		"name": "<?php echo esc_js( get_bloginfo( 'name' ) ); ?>",
		"url": "<?php echo esc_url( home_url( '/' ) ); ?>",
		"description": "<?php echo esc_js( wp_strip_all_tags( $description ) ); ?>",
		"potentialAction": {
			"@type": "SearchAction",
			"target": {
				"@type": "EntryPoint",
				"urlTemplate": "<?php echo esc_url( home_url( '/?s={search_term_string}' ) ); ?>"
			},
			"query-input": "required name=search_term_string"
		}
	}
	</script>
	
	<?php wp_footer(); ?>
</body>
</html>

