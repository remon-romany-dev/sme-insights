/**
 * Design Flexibility JavaScript
 * Ensures visual editor works regardless of design changes
 *
 * @package SME_Insights
 * @since 1.0.0
 */

(function() {
	'use strict';
	
	/**
	 * Flexible element finder
	 */
	const DesignFlexibility = {
		
		/**
		 * Find header element with multiple selectors
		 */
		findHeader: function() {
			const selectors = [
				'header',
				'.header',
				'.main-header',
				'[role="banner"]',
				'header.site-header',
				'.site-header',
				'header[class*="header"]',
				'[class*="header"]:first-of-type'
			];
			
			for (let i = 0; i < selectors.length; i++) {
				const element = document.querySelector(selectors[i]);
				if (element) {
					element.setAttribute('data-sme-editable', 'header');
					element.setAttribute('data-sme-element-type', 'header');
					return element;
				}
			}
			
			return null;
		},
		
		/**
		 * Find footer element with multiple selectors
		 */
		findFooter: function() {
			const selectors = [
				'footer',
				'.footer',
				'.main-footer',
				'[role="contentinfo"]',
				'footer.site-footer',
				'.site-footer',
				'footer[class*="footer"]',
				'[class*="footer"]:last-of-type'
			];
			
			for (let i = 0; i < selectors.length; i++) {
				const element = document.querySelector(selectors[i]);
				if (element) {
					element.setAttribute('data-sme-editable', 'footer');
					element.setAttribute('data-sme-element-type', 'footer');
					return element;
				}
			}
			
			return null;
		},
		
		/**
		 * Find page content element
		 */
		findPageContent: function() {
			const selectors = [
				'.sme-page-editable',
				'body.page',
				'body.single',
				'body.category',
				'body.archive',
				'.content-area',
				'.site-content',
				'#content',
				'main',
				'[role="main"]'
			];
			
			for (let i = 0; i < selectors.length; i++) {
				const element = document.querySelector(selectors[i]);
				if (element) {
					element.setAttribute('data-sme-editable', 'page');
					return element;
				}
			}
			
			return document.body;
		},
		
		/**
		 * Initialize
		 */
		init: function() {
			// Wait for DOM
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', this.onReady.bind(this));
			} else {
				this.onReady();
			}
			
			// Re-check after dynamic content loads
			setTimeout(this.onReady.bind(this), 1000);
			setTimeout(this.onReady.bind(this), 3000);
		},
		
		/**
		 * On ready
		 */
		onReady: function() {
			// Find and mark header
			this.findHeader();
			
			// Find and mark footer
			this.findFooter();
			
			// Find and mark page content
			this.findPageContent();
			
			// Trigger custom event for visual editor
			if (typeof window.dispatchEvent !== 'undefined') {
				window.dispatchEvent(new CustomEvent('smeDesignFlexibilityReady', {
					detail: {
						header: this.findHeader(),
						footer: this.findFooter(),
						page: this.findPageContent()
					}
				}));
			}
		}
	};
	
	// Initialize
	DesignFlexibility.init();
	
	// Export for use in visual editor
	window.SME_DesignFlexibility = DesignFlexibility;
	
})();

