/**
 * Theme Customizer Live Preview
 * Updates CSS variables in real-time as user changes settings
 *
 * @package SME_Insights
 * @since 1.0.0
 */

(function( $ ) {
	'use strict';
	
	// Colors
	wp.customize( 'sme_accent_primary', function( value ) {
		value.bind( function( to ) {
			document.documentElement.style.setProperty( '--accent-primary', to );
			document.documentElement.style.setProperty( '--bg-dark', to );
			updateGradient();
		} );
	} );
	
	wp.customize( 'sme_accent_secondary', function( value ) {
		value.bind( function( to ) {
			document.documentElement.style.setProperty( '--accent-secondary', to );
			document.documentElement.style.setProperty( '--accent-success', to );
			updateGradient();
		} );
	} );
	
	wp.customize( 'sme_accent_hover', function( value ) {
		value.bind( function( to ) {
			document.documentElement.style.setProperty( '--accent-hover', to );
		} );
	} );
	
	wp.customize( 'sme_text_primary', function( value ) {
		value.bind( function( to ) {
			document.documentElement.style.setProperty( '--text-primary', to );
		} );
	} );
	
	wp.customize( 'sme_text_secondary', function( value ) {
		value.bind( function( to ) {
			document.documentElement.style.setProperty( '--text-secondary', to );
		} );
	} );
	
	wp.customize( 'sme_bg_primary', function( value ) {
		value.bind( function( to ) {
			document.documentElement.style.setProperty( '--bg-primary', to );
		} );
	} );
	
	wp.customize( 'sme_bg_secondary', function( value ) {
		value.bind( function( to ) {
			document.documentElement.style.setProperty( '--bg-secondary', to );
		} );
	} );
	
	wp.customize( 'sme_border_color', function( value ) {
		value.bind( function( to ) {
			document.documentElement.style.setProperty( '--border-color', to );
		} );
	} );
	
	// Typography
	wp.customize( 'sme_font_family', function( value ) {
		value.bind( function( to ) {
			document.body.style.fontFamily = to;
		} );
	} );
	
	wp.customize( 'sme_font_size', function( value ) {
		value.bind( function( to ) {
			document.body.style.fontSize = to + 'px';
		} );
	} );
	
	wp.customize( 'sme_line_height', function( value ) {
		value.bind( function( to ) {
			document.body.style.lineHeight = to;
		} );
	} );
	
	// Layout
	wp.customize( 'sme_container_width', function( value ) {
		value.bind( function( to ) {
			$( '.container, .container-inner' ).css( 'max-width', to + 'px' );
		} );
	} );
	
	wp.customize( 'sme_section_padding', function( value ) {
		value.bind( function( to ) {
			$( 'section, .section' ).css( 'padding', to + 'px 0' );
		} );
	} );
	
	wp.customize( 'sme_border_radius', function( value ) {
		value.bind( function( to ) {
			$( 'button, .btn, .button, .card' ).css( 'border-radius', to + 'px' );
		} );
	} );
	
	// Update gradient when colors change
	function updateGradient() {
		var primary = wp.customize( 'sme_accent_primary' ).get();
		var secondary = wp.customize( 'sme_accent_secondary' ).get();
		var gradient = 'linear-gradient(135deg, ' + primary + ' 0%, ' + secondary + ' 100%)';
		document.documentElement.style.setProperty( '--breaking-gradient', gradient );
	}
	
	// Header Settings - Live Preview
	wp.customize( 'header_logo_text', function( value ) {
		value.bind( function( to ) {
			$( '.site-logo' ).text( to );
		} );
	} );
	
	wp.customize( 'header_top_bar_text', function( value ) {
		value.bind( function( to ) {
			$( '.become-contributor-link' ).text( to );
		} );
	} );
	
	wp.customize( 'header_search_placeholder', function( value ) {
		value.bind( function( to ) {
			$( '.header-search-input' ).attr( 'placeholder', to );
		} );
	} );
	
	wp.customize( 'header_subscribe_text', function( value ) {
		value.bind( function( to ) {
			$( '#subscribeBtn' ).text( to );
		} );
	} );
	
	wp.customize( 'header_show_top_bar', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '.top-bar' ).show();
			} else {
				$( '.top-bar' ).hide();
			}
		} );
	} );
	
	wp.customize( 'header_show_niche_topics', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '.niche-topics-section' ).show();
			} else {
				$( '.niche-topics-section' ).hide();
			}
		} );
	} );
	
	wp.customize( 'header_show_breaking_news', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '.breaking-news-bar' ).show();
			} else {
				$( '.breaking-news-bar' ).hide();
			}
		} );
	} );
	
	// Footer Settings - Live Preview
	wp.customize( 'footer_company_name', function( value ) {
		value.bind( function( to ) {
			$( '.footer-column:first h4' ).text( to );
		} );
	} );
	
	wp.customize( 'footer_column1_about', function( value ) {
		value.bind( function( to ) {
			$( '.footer-column:first ul li:first a' ).text( to );
		} );
	} );
	
	wp.customize( 'footer_column1_team', function( value ) {
		value.bind( function( to ) {
			$( '.footer-column:first ul li:nth-child(2) a' ).text( to );
		} );
	} );
	
	wp.customize( 'footer_column1_contributor', function( value ) {
		value.bind( function( to ) {
			$( '.footer-column:first ul li:nth-child(3) a' ).text( to );
		} );
	} );
	
	wp.customize( 'footer_column1_contact', function( value ) {
		value.bind( function( to ) {
			$( '.footer-column:first ul li:last a' ).text( to );
		} );
	} );
	
	wp.customize( 'footer_copyright_text', function( value ) {
		value.bind( function( to ) {
			var year = new Date().getFullYear();
			var siteName = wp.customize( 'footer_company_name' ).get();
			var text = to.replace( '{year}', year ).replace( '{site_name}', siteName );
			$( '.footer-bottom-left p' ).html( text );
		} );
	} );
	
	wp.customize( 'footer_show_columns', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '.footer-grid' ).show();
			} else {
				$( '.footer-grid' ).hide();
			}
		} );
	} );
	
})( jQuery );

