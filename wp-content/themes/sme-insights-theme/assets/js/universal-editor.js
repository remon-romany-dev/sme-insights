/**
 * Universal Editor JavaScript
 * Works on any design, any theme, any structure - like Elementor
 *
 * @package SME_Insights
 * @since 1.0.0
 */

(function() {
	'use strict';
	
	/**
	 * Universal Editor Core
	 * Prevents conflicts with other scripts
	 */
	if (typeof window.SMEUniversalEditorCore !== 'undefined') {
		// Already exists, don't override
		return;
	} else {
		window.SMEUniversalEditorCore = {
		
		initialized: false,
		observer: null,
		retryCount: 0,
		maxRetries: 10,
		elements: {
			header: null,
			footer: null,
			page: null
		},
		
		/**
		 * Initialize
		 */
		init: function() {
			if (this.initialized && this.elements.header && this.elements.footer) {
				return; // Already initialized
			}
			
			// Find all elements
			this.findElements();
			
			// Mark elements
			this.markElements();
			
			// Setup MutationObserver
			this.setupObserver();
			
			// Setup event listeners
			this.setupEventListeners();
			
			// Retry if elements not found
			if ((!this.elements.header || !this.elements.footer) && this.retryCount < this.maxRetries) {
				this.retryCount++;
				setTimeout(this.init.bind(this), 500 * this.retryCount);
			}
			
			this.initialized = true;
		},
		
		/**
		 * Find elements using multiple strategies
		 */
		findElements: function() {
			// Strategy 1: Data attributes (most reliable)
			this.elements.header = this.findByDataAttribute('header');
			this.elements.footer = this.findByDataAttribute('footer');
			this.elements.page = this.findByDataAttribute('page');
			
			// Strategy 2: Semantic HTML
			if (!this.elements.header) {
				this.elements.header = this.findBySemantic('header');
			}
			if (!this.elements.footer) {
				this.elements.footer = this.findBySemantic('footer');
			}
			if (!this.elements.page) {
				this.elements.page = this.findBySemantic('page');
			}
			
			// Strategy 3: Class names (common patterns)
			if (!this.elements.header) {
				this.elements.header = this.findByClassPattern('header');
			}
			if (!this.elements.footer) {
				this.elements.footer = this.findByClassPattern('footer');
			}
			
			// Strategy 4: Position-based (last resort)
			if (!this.elements.header) {
				this.elements.header = this.findByPosition('header');
			}
			if (!this.elements.footer) {
				this.elements.footer = this.findByPosition('footer');
			}
		},
		
		/**
		 * Find by data attribute
		 */
		findByDataAttribute: function(type) {
			const selectors = [
				'[data-sme-element="' + type + '"]',
				'[data-sme-element-type="' + type + '"]',
				'[data-sme-editable="' + type + '"]',
				'[data-element="' + type + '"]',
				'[data-type="' + type + '"]'
			];
			
			for (let i = 0; i < selectors.length; i++) {
				const element = document.querySelector(selectors[i]);
				if (element) {
					return element;
				}
			}
			
			return null;
		},
		
		/**
		 * Find by semantic HTML
		 */
		findBySemantic: function(type) {
			if (type === 'header') {
				// Try semantic header
				const header = document.querySelector('header[role="banner"]') || 
							   document.querySelector('header:not(footer)') ||
							   document.querySelector('header');
				if (header) return header;
				
				// Try main header
				const mainHeader = document.querySelector('body > header') ||
								   document.querySelector('.site > header') ||
								   document.querySelector('#site-header');
				if (mainHeader) return mainHeader;
			}
			
			if (type === 'footer') {
				// Try semantic footer
				const footer = document.querySelector('footer[role="contentinfo"]') ||
							   document.querySelector('footer');
				if (footer) return footer;
				
				// Try main footer
				const mainFooter = document.querySelector('body > footer') ||
									document.querySelector('.site > footer') ||
									document.querySelector('#site-footer');
				if (mainFooter) return mainFooter;
			}
			
			if (type === 'page') {
				// Try main content
				const main = document.querySelector('main[role="main"]') ||
							 document.querySelector('main') ||
							 document.querySelector('#main') ||
							 document.querySelector('.main') ||
							 document.querySelector('#content') ||
							 document.querySelector('.content') ||
							 document.querySelector('.site-content') ||
							 document.querySelector('.content-area');
				if (main) return main;
			}
			
			return null;
		},
		
		/**
		 * Find by class pattern
		 */
		findByClassPattern: function(type) {
			const patterns = {
				header: [
					'header', '.header', '.main-header', '.site-header',
					'[class*="header"]', '[class*="Header"]', '[id*="header"]',
					'[id*="Header"]', '.top-bar', '.topbar', '.navbar',
					'.navigation', '.nav-header', '.site-nav'
				],
				footer: [
					'footer', '.footer', '.main-footer', '.site-footer',
					'[class*="footer"]', '[class*="Footer"]', '[id*="footer"]',
					'[id*="Footer"]', '.bottom-bar', '.bottombar', '.site-info'
				]
			};
			
			if (!patterns[type]) return null;
			
			for (let i = 0; i < patterns[type].length; i++) {
				const elements = document.querySelectorAll(patterns[type][i]);
				if (elements.length > 0) {
					// Return the first/last based on type
					if (type === 'header') {
						return elements[0];
					} else if (type === 'footer') {
						return elements[elements.length - 1];
					}
				}
			}
			
			return null;
		},
		
		/**
		 * Find by position
		 */
		findByPosition: function(type) {
			if (type === 'header') {
				// First element in body (usually header)
				const bodyChildren = Array.from(document.body.children);
				for (let i = 0; i < Math.min(5, bodyChildren.length); i++) {
					const child = bodyChildren[i];
					if (child.tagName === 'HEADER' || 
						child.classList.contains('header') ||
						child.classList.contains('site-header') ||
						child.getAttribute('role') === 'banner') {
						return child;
					}
				}
			}
			
			if (type === 'footer') {
				// Last element in body (usually footer)
				const bodyChildren = Array.from(document.body.children);
				for (let i = bodyChildren.length - 1; i >= Math.max(0, bodyChildren.length - 5); i--) {
					const child = bodyChildren[i];
					if (child.tagName === 'FOOTER' ||
						child.classList.contains('footer') ||
						child.classList.contains('site-footer') ||
						child.getAttribute('role') === 'contentinfo') {
						return child;
					}
				}
			}
			
			return null;
		},
		
		/**
		 * Mark elements with data attributes
		 */
		markElements: function() {
			if (this.elements.header) {
				this.elements.header.setAttribute('data-sme-element', 'header');
				this.elements.header.setAttribute('data-sme-element-type', 'header');
				this.elements.header.setAttribute('data-sme-editable', 'header');
				document.body.classList.add('sme-has-header');
			}
			
			if (this.elements.footer) {
				this.elements.footer.setAttribute('data-sme-element', 'footer');
				this.elements.footer.setAttribute('data-sme-element-type', 'footer');
				this.elements.footer.setAttribute('data-sme-editable', 'footer');
				document.body.classList.add('sme-has-footer');
			}
			
			if (this.elements.page) {
				this.elements.page.setAttribute('data-sme-element', 'page');
				this.elements.page.setAttribute('data-sme-editable', 'page');
			}
		},
		
		/**
		 * Setup MutationObserver
		 */
		setupObserver: function() {
			if (!window.MutationObserver) {
				return; // Not supported
			}
			
			// Stop existing observer
			if (this.observer) {
				this.observer.disconnect();
			}
			
			// Create new observer
			this.observer = new MutationObserver(function(mutations) {
				let shouldReinit = false;
				
				mutations.forEach(function(mutation) {
					// Check if header/footer was added/removed
					if (mutation.type === 'childList') {
						mutation.addedNodes.forEach(function(node) {
							if (node.nodeType === 1) {
								if (node.tagName === 'HEADER' || 
									node.classList.contains('header') ||
									node.classList.contains('site-header')) {
									shouldReinit = true;
								}
								if (node.tagName === 'FOOTER' ||
									node.classList.contains('footer') ||
									node.classList.contains('site-footer')) {
									shouldReinit = true;
								}
							}
						});
					}
					
					// Check if attributes changed
					if (mutation.type === 'attributes') {
						if (mutation.target.hasAttribute('data-sme-element')) {
							shouldReinit = true;
						}
					}
				});
				
				if (shouldReinit) {
					// Debounce reinit
					clearTimeout(this.reinitTimeout);
					this.reinitTimeout = setTimeout(function() {
						window.SMEUniversalEditorCore.retryCount = 0;
						window.SMEUniversalEditorCore.init();
					}, 500);
				}
			}.bind(this));
			
			// Observe body for changes
			this.observer.observe(document.body, {
				childList: true,
				subtree: true,
				attributes: true,
				attributeFilter: ['data-sme-element', 'class', 'id']
			});
		},
		
		/**
		 * Setup event listeners
		 */
		setupEventListeners: function() {
			// Listen for custom events
			window.addEventListener('sme-reinit-editor', function() {
				window.SMEUniversalEditorCore.retryCount = 0;
				window.SMEUniversalEditorCore.init();
			});
			
			// Listen for AJAX complete (for dynamic content)
			document.addEventListener('ajaxComplete', function() {
				setTimeout(function() {
					window.SMEUniversalEditorCore.retryCount = 0;
					window.SMEUniversalEditorCore.init();
				}, 100);
			});
		},
		
		/**
		 * Get element
		 */
		getElement: function(type) {
			// Try cached first
			if (this.elements[type] && document.contains(this.elements[type])) {
				return this.elements[type];
			}
			
			// Re-find if not in DOM
			this.findElements();
			return this.elements[type] || null;
		},
		
		/**
		 * Log for debugging (only in development)
		 */
		log: function(message, data) {
			// Logging disabled for production
			// Only log in development mode if needed
			if (typeof smeUniversalEditor !== 'undefined' && smeUniversalEditor.debug === true && typeof window !== 'undefined' && window.location.hostname === 'localhost') {
				// Development logging only
			}
		}
	};
	
		// Auto-initialize
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function() {
				if (window.SMEUniversalEditorCore && !window.SMEUniversalEditorCore.initialized) {
					window.SMEUniversalEditorCore.init();
				}
			});
		} else {
			if (window.SMEUniversalEditorCore && !window.SMEUniversalEditorCore.initialized) {
				window.SMEUniversalEditorCore.init();
			}
		}
		
		// Also initialize after delays for dynamic content
		setTimeout(function() {
			if (window.SMEUniversalEditorCore && !window.SMEUniversalEditorCore.initialized) {
				window.SMEUniversalEditorCore.init();
			}
		}, 100);
		
		setTimeout(function() {
			if (window.SMEUniversalEditorCore && !window.SMEUniversalEditorCore.initialized) {
				window.SMEUniversalEditorCore.init();
			}
		}, 500);
		
		setTimeout(function() {
			if (window.SMEUniversalEditorCore && !window.SMEUniversalEditorCore.initialized) {
				window.SMEUniversalEditorCore.init();
			}
		}, 1000);
		
		setTimeout(function() {
			if (window.SMEUniversalEditorCore && !window.SMEUniversalEditorCore.initialized) {
				window.SMEUniversalEditorCore.init();
			}
		}, 2000);
	}
	
})();

