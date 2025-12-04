<?php
/**
 * Header Template
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
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<meta name="theme-color" content="#1a365d">
	<meta name="format-detection" content="telephone=no">
	
	<!-- Google Search Console Verification -->
	<?php
	$google_site_verification = get_theme_mod( 'google_site_verification', '' );
	if ( $google_site_verification ) {
		echo '<meta name="google-site-verification" content="' . esc_attr( $google_site_verification ) . '">' . "\n";
	}
	?>
	
	<!-- Favicon -->
	<?php
	if ( ! has_site_icon() ) {
		$theme_favicon = get_template_directory_uri() . '/assets/images/favicon.png';
		echo '<link rel="icon" href="' . esc_url( $theme_favicon ) . '" type="image/png">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . esc_url( $theme_favicon ) . '">' . "\n";
	}
	?>
	
	<!-- Performance: Preconnect to improve resource loading -->
	<link rel="preconnect" href="<?php echo esc_url( home_url() ); ?>">
	
	<!-- Performance: Preconnect to external resources -->
	<?php
	$social_facebook = get_theme_mod( 'social_facebook', '' );
	$social_twitter = get_theme_mod( 'social_twitter', '' );
	$social_linkedin = get_theme_mod( 'social_linkedin', '' );
	
	if ( $social_facebook && filter_var( $social_facebook, FILTER_VALIDATE_URL ) ) {
		$parsed = wp_parse_url( $social_facebook );
		if ( isset( $parsed['host'] ) ) {
			echo '<link rel="preconnect" href="https://' . esc_attr( $parsed['host'] ) . '">' . "\n";
		}
	}
	if ( $social_twitter && filter_var( $social_twitter, FILTER_VALIDATE_URL ) ) {
		$parsed = wp_parse_url( $social_twitter );
		if ( isset( $parsed['host'] ) ) {
			echo '<link rel="preconnect" href="https://' . esc_attr( $parsed['host'] ) . '">' . "\n";
		}
	}
	if ( $social_linkedin && filter_var( $social_linkedin, FILTER_VALIDATE_URL ) ) {
		$parsed = wp_parse_url( $social_linkedin );
		if ( isset( $parsed['host'] ) ) {
			echo '<link rel="preconnect" href="https://' . esc_attr( $parsed['host'] ) . '">' . "\n";
		}
	}
	?>
	<!-- DNS Prefetch for external resources -->
	<link rel="dns-prefetch" href="//images.unsplash.com">
	<link rel="dns-prefetch" href="//fonts.googleapis.com">
	<link rel="dns-prefetch" href="//fonts.gstatic.com">
	
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> style="background-color: #ffffff; margin: 0; padding: 0; overflow-x: hidden;">
<?php wp_body_open(); ?>

<!-- Top Bar -->
<?php if ( get_theme_mod( 'header_show_top_bar', true ) ) : ?>
<div class="top-bar" role="banner">
	<div class="container-inner">
		<div class="top-bar-content">
			<div class="date-weather">
				<?php
				$contributor_page = get_page_by_path( 'become-a-contributor' );
				if ( ! $contributor_page ) {
					$contributor_page = get_page_by_path( 'become-contributor' );
				}
				if ( ! $contributor_page ) {
					$contributor_page = sme_get_page_by_title( 'Become a Contributor' );
				}
				$contributor_url = $contributor_page ? get_permalink( $contributor_page->ID ) : '#';
				?>
				<a href="<?php echo esc_url( $contributor_url ); ?>" style="color: #fff; text-decoration: none; font-weight: 600;">
					<?php echo esc_html( get_theme_mod( 'header_top_bar_text', 'Become a Contributor' ) ); ?>
				</a>
			</div>
			<div class="top-bar-links">
				<?php
				$social_links_original = array(
					'facebook'  => get_theme_mod( 'social_facebook', 'https://facebook.com/smeinsights' ),
					'twitter'  => get_theme_mod( 'social_twitter', 'https://twitter.com/smeinsights' ),
					'linkedin' => get_theme_mod( 'social_linkedin', 'https://linkedin.com/company/smeinsights' ),
				);
				
				$social_links_new = array(
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
				
				if ( ! empty( $social_links_original['facebook'] ) && $social_links_original['facebook'] !== '#' ) :
				?>
					<a href="<?php echo esc_url( $social_links_original['facebook'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Facebook">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links_original['twitter'] ) && $social_links_original['twitter'] !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_links_original['twitter'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Twitter">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links_original['linkedin'] ) && $social_links_original['linkedin'] !== '#' ) : ?>
					<a href="<?php echo esc_url( $social_links_original['linkedin'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="LinkedIn">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<circle cx="4" cy="4" r="2" stroke="white" stroke-width="2"/>
						</svg>
					</a>
				<?php endif; ?>
				
				<?php
				if ( ! empty( $social_links_new['tiktok'] ) && $social_links_new['tiktok'] !== '#' && filter_var( $social_links_new['tiktok'], FILTER_VALIDATE_URL ) ) :
				?>
					<a href="<?php echo esc_url( $social_links_new['tiktok'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="TikTok">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links_new['pinterest'] ) && $social_links_new['pinterest'] !== '#' && filter_var( $social_links_new['pinterest'], FILTER_VALIDATE_URL ) ) : ?>
					<a href="<?php echo esc_url( $social_links_new['pinterest'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Pinterest">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12 2C6.48 2 2 6.48 2 12c0 4.84 3.01 8.97 7.25 10.63-.1-.94-.19-2.38.04-3.4.21-1.4 1.35-5.96 1.35-5.96s-.34-.68-.34-1.69c0-1.58.92-2.76 2.06-2.76 1.02 0 1.51.76 1.51 1.68 0 1.03-.66 2.57-.99 4-.28 1.19.6 2.16 1.78 2.16 2.14 0 3.79-2.26 3.79-5.52 0-2.88-2.07-4.9-5.03-4.9-3.43 0-5.44 2.57-5.44 5.23 0 1.03.4 2.14.9 2.75.1.12.11.23.08.36-.08.33-.26 1.05-.3 1.2-.05.2-.16.24-.37.15-1.4-.65-2.27-2.69-2.27-4.33 0-3.54 2.58-6.8 7.45-6.8 3.91 0 6.95 2.85 6.95 6.66 0 3.89-2.45 7.01-5.85 7.01-1.14 0-2.22-.59-2.59-1.29l-.7 2.67c-.25.98-.93 2.2-1.38 2.95.99.3 2.03.47 3.11.47 5.52 0 10-4.48 10-10S17.52 2 12 2z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links_new['snapchat'] ) && $social_links_new['snapchat'] !== '#' && filter_var( $social_links_new['snapchat'], FILTER_VALIDATE_URL ) ) : ?>
					<a href="<?php echo esc_url( $social_links_new['snapchat'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Snapchat">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12 2C7.58 2 4 5.58 4 10c0 2.5 1.5 4.75 3.75 5.75L7 18l2.25-1.25c.5.25 1 .5 1.5.75h2.5c.5-.25 1-.5 1.5-.75L17 18l-.75-2.25C18.5 14.75 20 12.5 20 10c0-4.42-3.58-8-8-8z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links_new['whatsapp'] ) && $social_links_new['whatsapp'] !== '#' && filter_var( $social_links_new['whatsapp'], FILTER_VALIDATE_URL ) ) : ?>
					<a href="<?php echo esc_url( $social_links_new['whatsapp'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="WhatsApp">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links_new['telegram'] ) && $social_links_new['telegram'] !== '#' && filter_var( $social_links_new['telegram'], FILTER_VALIDATE_URL ) ) : ?>
					<a href="<?php echo esc_url( $social_links_new['telegram'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Telegram">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.13-.31-1.09-.66.02-.18.27-.37.75-.56 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links_new['discord'] ) && $social_links_new['discord'] !== '#' && filter_var( $social_links_new['discord'], FILTER_VALIDATE_URL ) ) : ?>
					<a href="<?php echo esc_url( $social_links_new['discord'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Discord">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C1.451 6.7.64 8.98.64 11.32c0 2.345.784 4.59 2.22 6.46a.07.07 0 00.01.013c1.25.9 2.457 1.645 3.6 2.24a.074.074 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 18.9 18.9 0 01-2.487-1.18.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 18.818 18.818 0 01-2.49 1.182.076.076 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028c1.143-.595 2.35-1.34 3.6-2.24a.077.077 0 00.01-.013c1.437-1.87 2.22-4.115 2.22-6.46-.002-2.339-.808-4.618-2.003-6.923a.034.034 0 00-.031-.027zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links_new['reddit'] ) && $social_links_new['reddit'] !== '#' && filter_var( $social_links_new['reddit'], FILTER_VALIDATE_URL ) ) : ?>
					<a href="<?php echo esc_url( $social_links_new['reddit'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Reddit">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 0 1 .042.52c0 2.694-3.13 4.87-7.004 4.87-3.874 0-7.004-2.176-7.004-4.87 0-.183.015-.366.043-.534A1.748 1.748 0 0 1 4.028 12c0-.968.786-1.754 1.754-1.754.463 0 .898.196 1.207.49 1.207-.883 2.878-1.43 4.744-1.487l.885-4.182a.342.342 0 0 1 .14-.197.35.35 0 0 1 .238-.042l2.906.617a1.214 1.214 0 0 1 1.108-.701zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 0 0-.231.094.33.33 0 0 0 0 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 0 0 .029-.463.33.33 0 0 0-.464 0c-.547.533-1.684.73-2.512.73-.828 0-1.979-.196-2.512-.73a.326.326 0 0 0-.232-.095z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links_new['medium'] ) && $social_links_new['medium'] !== '#' && filter_var( $social_links_new['medium'], FILTER_VALIDATE_URL ) ) : ?>
					<a href="<?php echo esc_url( $social_links_new['medium'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Medium">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M13.54 12a6.8 6.8 0 01-6.77 6.82A6.8 6.8 0 010 12a6.8 6.8 0 016.77-6.82A6.8 6.8 0 0113.54 12zM20.96 12c0 3.54-2.29 6.41-5.12 6.41-2.83 0-5.12-2.87-5.12-6.41s2.29-6.42 5.12-6.42S20.96 8.46 20.96 12zM24 12c0 3.17-.51 5.75-1.15 5.75-.64 0-1.15-2.58-1.15-5.75s.51-5.75 1.15-5.75C23.49 6.25 24 8.83 24 12z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links_new['github'] ) && $social_links_new['github'] !== '#' && filter_var( $social_links_new['github'], FILTER_VALIDATE_URL ) ) : ?>
					<a href="<?php echo esc_url( $social_links_new['github'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="GitHub">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 00-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0020 4.77 5.07 5.07 0 0019.91 1S18.73.65 16 2.48a13.38 13.38 0 00-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 005 4.77a5.44 5.44 0 00-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 009 18.13V22" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links_new['behance'] ) && $social_links_new['behance'] !== '#' && filter_var( $social_links_new['behance'], FILTER_VALIDATE_URL ) ) : ?>
					<a href="<?php echo esc_url( $social_links_new['behance'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Behance">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M22 7h-7v-2h7v2zm1.726 10c-.442 1.297-2.029 3-5.101 3-3.074 0-5.564-1.729-5.564-5.675 0-3.91 2.325-5.92 5.466-5.92 3.082 0 4.9 1.538 5.101 4.314h-3.855c.051 1.211.54 1.685 1.408 1.685.832 0 1.299-.485 1.299-1.24h3.782c0 2.51-1.175 3.96-3.899 3.96-2.66 0-3.949-1.686-3.949-4.54 0-4.14 3.28-4.79 6.5-4.19V5.5H9v14.5h13.726z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $social_links_new['dribbble'] ) && $social_links_new['dribbble'] !== '#' && filter_var( $social_links_new['dribbble'], FILTER_VALIDATE_URL ) ) : ?>
					<a href="<?php echo esc_url( $social_links_new['dribbble'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Dribbble">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<circle cx="12" cy="12" r="10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M19.13 5.09A15.5 15.5 0 0 1 21 12M19.13 18.91A15.5 15.5 0 0 1 12 21M4.86 18.91A15.5 15.5 0 0 1 3 12M4.86 5.09A15.5 15.5 0 0 1 12 3" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if ( get_theme_mod( 'header_show_niche_topics', true ) ) : ?>
<div class="popular-tags-section" role="navigation" aria-label="<?php echo esc_attr__( 'Niche Topics', 'sme-insights' ); ?>">
	<div class="container-inner">
		<div class="popular-tags-wrapper">
			<span class="popular-tags-label"><?php echo esc_html__( 'Niche Topics', 'sme-insights' ); ?></span>
			<ul class="popular-tags-list">
				<?php
				$niche_topics = array(
					'ai-in-business'      => 'AI in Business',
					'ecommerce-trends'    => 'E-commerce Trends',
					'startup-funding'     => 'Startup Funding',
					'green-economy'       => 'Green Economy',
					'remote-work'         => 'Remote Work',
				);
				foreach ( $niche_topics as $slug => $title ) {
					$page = get_page_by_path( $slug );
					if ( $page ) {
						echo '<li><a href="' . esc_url( get_permalink( $page->ID ) ) . '">' . esc_html( $title ) . '</a></li>';
					} else {
						echo '<li><a href="' . esc_url( home_url( '/?s=' . urlencode( $title ) ) ) . '">' . esc_html( $title ) . '</a></li>';
					}
				}
				?>
			</ul>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if ( get_theme_mod( 'header_show_breaking_news', true ) ) : ?>
<?php
$breaking_posts = get_posts( array(
	'post_type'              => 'post',
	'posts_per_page'         => 6,
	'meta_key'               => 'breaking_news',
	'meta_value'             => '1',
	'orderby'                => 'date',
	'order'                  => 'DESC',
	'post_status'            => 'publish',
	'no_found_rows'          => true, // WordPress Best Practice: Skip pagination count
	'update_post_meta_cache' => false, // WordPress Best Practice: Skip meta cache if not needed
	'update_post_term_cache' => false, // WordPress Best Practice: Skip term cache if not needed
) );

if ( count( $breaking_posts ) < 6 ) {
	$exclude_ids = array();
	foreach ( $breaking_posts as $post ) {
		$exclude_ids[] = $post->ID;
	}
	
	$latest_posts = get_posts( array(
		'post_type'              => 'post',
		'posts_per_page'         => 6 - count( $breaking_posts ),
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'post_status'            => 'publish',
		'post__not_in'           => $exclude_ids,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	) );
	$breaking_posts = array_merge( $breaking_posts, $latest_posts );
}

if ( ! empty( $breaking_posts ) ) :
	?>
	<div class="breaking-news-bar" role="region" aria-label="<?php echo esc_attr__( 'Breaking News', 'sme-insights' ); ?>">
		<div class="container-inner">
			<div class="breaking-news-container">
				<div class="breaking-news-label" aria-hidden="true"><?php echo esc_html__( 'Breaking News', 'sme-insights' ); ?></div>
				<div class="breaking-news-ticker">
					<div class="ticker-content">
						<?php
						$ticker_posts = array_merge( $breaking_posts, $breaking_posts );
						foreach ( $ticker_posts as $post ) :
							setup_postdata( $post );
							$thumbnail_id = get_post_thumbnail_id( $post->ID );
							$thumbnail = '';
							if ( $thumbnail_id ) {
								$image_url = wp_get_attachment_image_url( $thumbnail_id, 'sme-breaking-news' );
								if ( ! $image_url ) {
									$image_url = wp_get_attachment_image_url( $thumbnail_id, array( 80, 60 ) );
								}
								
								$thumbnail_info = wp_get_attachment_image_src( $thumbnail_id, 'sme-breaking-news' );
								if ( ! $thumbnail_info ) {
									$thumbnail_info = wp_get_attachment_image_src( $thumbnail_id, array( 80, 60 ) );
								}
								$width = $thumbnail_info ? $thumbnail_info[1] : 80;
								$height = $thumbnail_info ? $thumbnail_info[2] : 60;
								
								$srcset = wp_get_attachment_image_srcset( $thumbnail_id, 'sme-breaking-news' );
								if ( ! $srcset ) {
									$srcset = wp_get_attachment_image_srcset( $thumbnail_id, array( 80, 60 ) );
								}
								$sizes = '(max-width: 768px) 50px, 80px';
								
								$thumbnail = '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title( $post->ID ) ) . '" width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '" loading="lazy" decoding="async"';
								if ( $srcset ) {
									$thumbnail .= ' srcset="' . esc_attr( $srcset ) . '" sizes="' . esc_attr( $sizes ) . '"';
								}
								$thumbnail .= '>';
							}
							?>
							<div class="ticker-item">
								<?php if ( $thumbnail ) : ?>
									<?php echo wp_kses_post( $thumbnail ); ?>
								<?php else : ?>
									<img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=40&h=30&fit=crop" alt="<?php echo esc_attr__( 'Breaking News', 'sme-insights' ); ?>" width="40" height="30" loading="lazy" decoding="async">
								<?php endif; ?>
								<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
									<?php echo esc_html( get_the_title( $post->ID ) ); ?>
								</a>
							</div>
							<?php
						endforeach;
						wp_reset_postdata();
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
<?php endif; ?>

<header class="main-header" role="banner">
	<div class="container-inner">
		<div class="header-content">
			<?php
			$logo = get_custom_logo();
			if ( $logo ) {
				$logo_id = get_theme_mod( 'custom_logo' );
				if ( $logo_id ) {
					$logo_meta = wp_get_attachment_metadata( $logo_id );
					$logo_width = isset( $logo_meta['width'] ) ? $logo_meta['width'] : 200;
					$logo_height = isset( $logo_meta['height'] ) ? $logo_meta['height'] : 60;
					$logo = str_replace( '<img', '<img width="' . esc_attr( $logo_width ) . '" height="' . esc_attr( $logo_height ) . '" fetchpriority="high"', $logo );
				}
				echo $logo;
			} else {
				echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="site-logo">' . esc_html( strtoupper( get_theme_mod( 'header_logo_text', get_bloginfo( 'name' ) ) ) ) . '</a>';
			}
			?>
			
			<div class="header-actions">
				<button class="search-btn" id="searchBtn" type="button" aria-label="<?php echo esc_attr__( 'Open search', 'sme-insights' ); ?>" aria-expanded="false" aria-controls="searchOverlay"><?php echo esc_html__( 'Search', 'sme-insights' ); ?></button>
				<button class="subscribe-btn" id="subscribeBtn" type="button" aria-label="<?php echo esc_attr__( 'Subscribe to newsletter', 'sme-insights' ); ?>"><?php echo esc_html( get_theme_mod( 'header_subscribe_text', __( 'Subscribe', 'sme-insights' ) ) ); ?></button>
				<button class="mobile-menu-toggle" id="mobileMenuToggle" type="button" aria-label="<?php echo esc_attr__( 'Toggle menu', 'sme-insights' ); ?>" aria-expanded="false" aria-controls="mobile-nav-wrapper">
					<span></span>
					<span></span>
					<span></span>
				</button>
			</div>
		</div>
		
		<nav class="desktop-nav" aria-label="<?php echo esc_attr__( 'Main Navigation', 'sme-insights' ); ?>" role="navigation">
			<?php
			$menu_locations = get_nav_menu_locations();
			$primary_menu_id = isset( $menu_locations['primary'] ) ? $menu_locations['primary'] : false;
			
			if ( $primary_menu_id && ( $menu = wp_get_nav_menu_object( $primary_menu_id ) ) ) {
				add_filter( 'wp_get_nav_menu_items', 'sme_order_menu_items', 10, 3 );
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'main-nav',
					'walker'         => new SME_Menu_Walker(),
					'fallback_cb'    => 'sme_default_menu',
				) );
				remove_filter( 'wp_get_nav_menu_items', 'sme_order_menu_items', 10 );
			} else {
				sme_default_menu();
			}
			?>
		</nav>
	</div>
</header>

<!-- Mobile Menu - Outside header for proper z-index stacking -->
<nav class="mobile-nav-wrapper" id="mobile-nav-wrapper" role="navigation" aria-label="<?php echo esc_attr__( 'Mobile Navigation', 'sme-insights' ); ?>">
	<div class="mobile-nav-content">
		<div class="mobile-nav-header">
			<div class="mobile-nav-logo">
				<?php
				$logo = get_custom_logo();
				if ( $logo ) {
					echo $logo;
				} else {
					echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( strtoupper( get_theme_mod( 'header_logo_text', get_bloginfo( 'name' ) ) ) ) . '</a>';
				}
				?>
			</div>
			<button class="mobile-menu-close" id="mobileMenuClose" type="button" aria-label="<?php echo esc_attr__( 'Close menu', 'sme-insights' ); ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<line x1="18" y1="6" x2="6" y2="18"></line>
					<line x1="6" y1="6" x2="18" y2="18"></line>
				</svg>
			</button>
		</div>
		
		<div class="mobile-menu-section mobile-menu-categories">
			<ul class="mobile-category-list">
				<?php
				$menu_locations = get_nav_menu_locations();
				$primary_menu_id = isset( $menu_locations['primary'] ) ? $menu_locations['primary'] : false;
				
				if ( $primary_menu_id && ( $menu = wp_get_nav_menu_object( $primary_menu_id ) ) ) {
					add_filter( 'wp_get_nav_menu_items', 'sme_order_menu_items', 10, 3 );
					$menu_items = wp_get_nav_menu_items( $primary_menu_id );
					remove_filter( 'wp_get_nav_menu_items', 'sme_order_menu_items', 10 );
					
					if ( $menu_items && ! empty( $menu_items ) ) {
						$labels = array(
							'technology' => 'Technology',
							'marketing'  => 'Marketing',
							'finance'    => 'Finance',
							'growth'     => 'Growth',
							'strategy'   => 'Strategy',
						);
						
						foreach ( $menu_items as $item ) {
							if ( strtolower( trim( $item->title ) ) === 'home' || $item->url === home_url( '/' ) ) {
								continue;
							}
							
							$item_title = $item->title;
							
							if ( $item->type === 'taxonomy' && $item->object === 'main_category' ) {
								$term = get_term( $item->object_id, 'main_category' );
								if ( $term && ! is_wp_error( $term ) && isset( $term->slug ) ) {
									$item_title = isset( $labels[ $term->slug ] ) ? $labels[ $term->slug ] : $item->title;
								}
							}
							
							$active_class = '';
							if ( in_array( 'current-menu-item', $item->classes ) || in_array( 'current_page_item', $item->classes ) ) {
								$active_class = 'active';
							}
							
							$item_classes = ! empty( $item->classes ) ? implode( ' ', array_map( 'esc_attr', $item->classes ) ) : '';
							
							echo '<li class="' . esc_attr( $item_classes ) . '">';
							echo '<a href="' . esc_url( $item->url ) . '" class="' . esc_attr( $active_class ) . '">' . esc_html( $item_title ) . '</a>';
							echo '</li>';
						}
					} else {
						$categories = function_exists( 'sme_get_primary_categories' ) ? sme_get_primary_categories() : array();
						if ( ! empty( $categories ) ) {
							$labels = array(
								'technology' => 'Technology',
								'marketing'  => 'Marketing',
								'finance'    => 'Finance',
								'growth'     => 'Growth',
								'strategy'   => 'Strategy',
							);
							foreach ( $categories as $category ) {
								$category_url = get_term_link( $category );
								if ( is_wp_error( $category_url ) ) {
									continue;
								}
								$menu_name = isset( $labels[ $category->slug ] ) ? $labels[ $category->slug ] : $category->name;
								echo '<li><a href="' . esc_url( $category_url ) . '">' . esc_html( $menu_name ) . '</a></li>';
							}
						}
					}
				} else {
					$categories = function_exists( 'sme_get_primary_categories' ) ? sme_get_primary_categories() : array();
					if ( ! empty( $categories ) ) {
						$labels = array(
							'technology' => 'Technology',
							'marketing'  => 'Marketing',
							'finance'    => 'Finance',
							'growth'     => 'Growth',
							'strategy'   => 'Strategy',
						);
						foreach ( $categories as $category ) {
							$category_url = get_term_link( $category );
							if ( is_wp_error( $category_url ) ) {
								continue;
							}
							$menu_name = isset( $labels[ $category->slug ] ) ? $labels[ $category->slug ] : $category->name;
							echo '<li><a href="' . esc_url( $category_url ) . '">' . esc_html( $menu_name ) . '</a></li>';
						}
					}
				}
				?>
			</ul>
		</div>
	</div>
</nav>

<div class="search-overlay" id="searchOverlay">
	<div class="search-overlay-content">
		<button class="search-overlay-close" id="searchOverlayClose" type="button" aria-label="Close search overlay">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<line x1="18" y1="6" x2="6" y2="18"></line>
				<line x1="6" y1="6" x2="18" y2="18"></line>
			</svg>
		</button>
		<div class="search-overlay-header">
			<h2><?php echo esc_html__( 'Search', 'sme-insights' ); ?></h2>
			<p><?php echo esc_html__( 'Find articles, insights, and resources to help grow your business.', 'sme-insights' ); ?></p>
		</div>
		<form class="search-overlay-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search" aria-label="<?php echo esc_attr__( 'Site search', 'sme-insights' ); ?>">
			<div class="search-overlay-form-group">
				<label for="search-overlay-input" class="screen-reader-text"><?php echo esc_html__( 'Search for articles', 'sme-insights' ); ?></label>
				<input type="search" name="s" id="search-overlay-input" class="search-overlay-input" placeholder="<?php echo esc_attr__( 'Search for articles...', 'sme-insights' ); ?>" required aria-required="true">
			</div>
			<button type="submit" class="search-overlay-submit" aria-label="<?php echo esc_attr__( 'Submit search', 'sme-insights' ); ?>">
				Search
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="11" cy="11" r="8"></circle>
					<path d="m21 21-4.35-4.35"></path>
				</svg>
			</button>
		</form>
	</div>
</div>
