/**
 * SME Insights Theme - Main JavaScript
 * Using Modern JavaScript Best Practices
 * 
 * @package SME_Insights
 * @since 1.0.0
 */

(function() {
    'use strict';

    // Hide JQMIGRATE console messages
    if (typeof console !== 'undefined' && console.warn) {
        const originalWarn = console.warn;
        console.warn = function(...args) {
            if (args[0] && typeof args[0] === 'string' && args[0].includes('JQMIGRATE')) {
                return;
            }
            originalWarn.apply(console, args);
        };
    }

    // Fix wp.coreCommands.initializeCommandPalette error
    // Ensure wp.coreCommands exists before WordPress tries to call it
    if (typeof window.wp === 'undefined') {
        window.wp = {};
    }
    if (typeof window.wp.coreCommands === 'undefined') {
        window.wp.coreCommands = {};
    }
    if (typeof window.wp.coreCommands.initializeCommandPalette === 'undefined') {
        window.wp.coreCommands.initializeCommandPalette = function() {
            // Silently fail if called before the actual function is loaded
            return;
        };
    }

    /**
     * Utility Functions
     * Reusable helper functions following best practices
     */
    const Utils = {
        /**
         * Debounce function to limit function calls
         * @param {Function} func - Function to debounce
         * @param {number} wait - Wait time in milliseconds
         * @returns {Function} Debounced function
         */
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Throttle function to limit function calls
         * @param {Function} func - Function to throttle
         * @param {number} limit - Time limit in milliseconds
         * @returns {Function} Throttled function
         */
        throttle(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        /**
         * Check if element exists
         * @param {string|Element} selector - CSS selector or element
         * @returns {Element|null} Element or null
         */
        $(selector) {
            if (typeof selector === 'string') {
                return document.querySelector(selector);
            }
            return selector || null;
        },

        /**
         * Get all elements matching selector
         * @param {string} selector - CSS selector
         * @returns {NodeList} NodeList of elements
         */
        $$(selector) {
            return document.querySelectorAll(selector);
        },

        /**
         * Safe addEventListener with error handling
         * @param {Element} element - Element to attach listener
         * @param {string} event - Event type
         * @param {Function} handler - Event handler
         * @param {Object} options - Event options
         */
        on(element, event, handler, options = {}) {
            if (element && typeof handler === 'function') {
                element.addEventListener(event, handler, options);
            }
        },

        /**
         * Remove event listener safely
         * @param {Element} element - Element to remove listener from
         * @param {string} event - Event type
         * @param {Function} handler - Event handler
         */
        off(element, event, handler) {
            if (element && typeof handler === 'function') {
                element.removeEventListener(event, handler);
            }
        }
    };

    /**
     * Main Slider Module
     * Handles hero slider functionality with best practices
     */
    const MainSlider = {
        index: 0,
        interval: null,
        total: 0,
        slider: null,
        dots: null,
        items: null,
        autoplayDelay: 6000,

        /**
         * Initialize slider
         */
        init() {
            this.slider = Utils.$('#mainSlider');
            this.dots = Utils.$('#mainSliderDots');
            
            if (!this.slider || !this.dots) {
                return;
            }

            this.items = this.slider.querySelectorAll('.slider-item');
            this.total = this.items.length;

            if (this.total === 0) {
                return;
            }

            this.createDots();
            this.bindEvents();
            this.startAutoplay();
        },

        /**
         * Create navigation dots
         */
        createDots() {
            while (this.dots.firstChild) {
                this.dots.removeChild(this.dots.firstChild);
            }
            
            this.items.forEach((_, index) => {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = `slider-dot${index === 0 ? ' active' : ''}`;
                dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
                dot.setAttribute('aria-current', index === 0 ? 'true' : 'false');
                dot.dataset.index = index;
                
                Utils.on(dot, 'click', () => this.goTo(index));
                this.dots.appendChild(dot);
            });
        },

        /**
         * Update slider position and dots
         */
        update() {
            if (!this.slider || this.total === 0) {
                return;
            }

            this.slider.style.transform = `translateX(-${this.index * 100}%)`;
            
            const dots = this.dots.querySelectorAll('.slider-dot');
            dots.forEach((dot, index) => {
                const isActive = index === this.index;
                dot.classList.toggle('active', isActive);
                dot.setAttribute('aria-current', isActive ? 'true' : 'false');
            });
        },

        /**
         * Go to specific slide
         * @param {number} index - Slide index
         */
        goTo(index) {
            if (index >= 0 && index < this.total) {
                this.index = index;
                this.update();
                this.resetAutoplay();
            }
        },

        /**
         * Go to next slide
         */
        next() {
            if (this.total > 0) {
                this.index = (this.index + 1) % this.total;
                this.update();
            }
        },

        /**
         * Go to previous slide
         */
        prev() {
            if (this.total > 0) {
                this.index = (this.index - 1 + this.total) % this.total;
                this.update();
            }
        },

        /**
         * Start autoplay
         */
        startAutoplay() {
            const container = Utils.$('.slider-container');
            if (!container || this.total === 0) {
                return;
            }

            this.pauseAutoplay();
            this.interval = setInterval(() => this.next(), this.autoplayDelay);

            if (!this._mouseEnterHandler) {
                this._mouseEnterHandler = () => this.pauseAutoplay();
                this._mouseLeaveHandler = () => this.startAutoplay();
                Utils.on(container, 'mouseenter', this._mouseEnterHandler);
                Utils.on(container, 'mouseleave', this._mouseLeaveHandler);
            }
        },

        /**
         * Pause autoplay
         */
        pauseAutoplay() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
        },

        /**
         * Cleanup - remove event listeners and clear intervals
         */
        cleanup() {
            this.pauseAutoplay();
            const container = Utils.$('.slider-container');
            if (container && this._mouseEnterHandler) {
                Utils.off(container, 'mouseenter', this._mouseEnterHandler);
                Utils.off(container, 'mouseleave', this._mouseLeaveHandler);
                this._mouseEnterHandler = null;
                this._mouseLeaveHandler = null;
            }
        },

        /**
         * Reset autoplay
         */
        resetAutoplay() {
            this.pauseAutoplay();
            this.startAutoplay();
        },

        /**
         * Bind event listeners
         */
        bindEvents() {
            const prevBtn = Utils.$('.slider-prev');
            const nextBtn = Utils.$('.slider-next');
            const container = Utils.$('.slider-container');

            if (prevBtn) {
                Utils.on(prevBtn, 'click', () => this.prev());
            }
            if (nextBtn) {
                Utils.on(nextBtn, 'click', () => this.next());
            }

            // Keyboard navigation - only when slider container is focused
            if (container) {
                Utils.on(container, 'keydown', (e) => {
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        this.prev();
                    } else if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        this.next();
                    }
                });
                // Make container focusable for keyboard navigation
                container.setAttribute('tabindex', '0');
            }
        }
    };

    /**
     * Latest Articles Slider Module
     * Handles latest articles slider functionality
     */
    const LatestArticlesSlider = {
        index: 0,
        interval: null,
        total: 0,
        slider: null,
        items: null,
        autoplayDelay: 5000,

        /**
         * Initialize slider
         */
        init() {
            this.slider = Utils.$('#latestArticlesSlider');
            
            if (!this.slider) {
                return;
            }

            this.items = this.slider.querySelectorAll('.latest-articles-slider-item');
            this.total = this.items.length;

            if (this.total === 0) {
                return;
            }

            this.bindEvents();
            this.startAutoplay();
        },

        /**
         * Update slider position
         */
        update() {
            if (!this.slider || this.total === 0) {
                return;
            }

            this.slider.style.transform = `translateX(-${this.index * 100}%)`;
        },

        /**
         * Go to specific slide
         */
        goTo(index) {
            if (index >= 0 && index < this.total) {
                this.index = index;
                this.update();
                this.resetAutoplay();
            }
        },

        /**
         * Go to next slide
         */
        next() {
            if (this.total > 0) {
                this.index = (this.index + 1) % this.total;
                this.update();
            }
        },

        /**
         * Go to previous slide
         */
        prev() {
            if (this.total > 0) {
                this.index = (this.index - 1 + this.total) % this.total;
                this.update();
            }
        },

        /**
         * Start autoplay
         */
        startAutoplay() {
            const container = Utils.$('.latest-articles-slider-container');
            if (!container || this.total === 0) {
                return;
            }

            this.pauseAutoplay();
            this.interval = setInterval(() => this.next(), this.autoplayDelay);

            if (!this._mouseEnterHandler) {
                this._mouseEnterHandler = () => this.pauseAutoplay();
                this._mouseLeaveHandler = () => this.startAutoplay();
                Utils.on(container, 'mouseenter', this._mouseEnterHandler);
                Utils.on(container, 'mouseleave', this._mouseLeaveHandler);
            }
        },

        /**
         * Pause autoplay
         */
        pauseAutoplay() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
        },

        /**
         * Reset autoplay
         */
        resetAutoplay() {
            this.pauseAutoplay();
            this.startAutoplay();
        },

        /**
         * Bind event listeners
         */
        bindEvents() {
            // Keyboard navigation
            const container = Utils.$('.latest-articles-slider-container');
            if (container) {
                Utils.on(container, 'keydown', (e) => {
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        this.prev();
                    } else if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        this.next();
                    }
                });
                container.setAttribute('tabindex', '0');
            }
        }
    };

    /**
     * Back to Top Button Module
     */
    const BackToTop = {
        button: null,
        threshold: 300,

        init() {
            this.button = Utils.$('#backToTop');
            if (!this.button) {
                return;
            }

            // Use throttled scroll handler for better performance
            const scrollHandler = Utils.throttle(() => {
                const shouldShow = window.pageYOffset > this.threshold;
                this.button.classList.toggle('show', shouldShow);
            }, 100);

            Utils.on(window, 'scroll', scrollHandler, { passive: true });
            Utils.on(this.button, 'click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    };

    /**
     * Modal Module - Reusable modal functionality
     */
    const Modal = {
        /**
         * Open modal
         * @param {string} modalId - Modal element ID
         */
        open(modalId) {
            const modal = Utils.$(modalId);
            if (!modal) {
                return;
            }

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Focus management for accessibility
            const focusableElement = modal.querySelector('input, button, textarea, select, [tabindex]:not([tabindex="-1"])');
            if (focusableElement) {
                setTimeout(() => focusableElement.focus(), 100);
            }

            // Close on Escape key
            const escapeHandler = (e) => {
                if (e.key === 'Escape') {
                    this.close(modalId);
                    Utils.off(document, 'keydown', escapeHandler);
                }
            };
            Utils.on(document, 'keydown', escapeHandler);
        },

        /**
         * Close modal
         * @param {string} modalId - Modal element ID
         */
        close(modalId) {
            const modal = Utils.$(modalId);
            if (!modal) {
                return;
            }

            modal.classList.remove('active');
            document.body.style.overflow = '';
        },

        /**
         * Initialize modal with close handlers
         * @param {string} modalId - Modal element ID
         * @param {string} triggerId - Trigger button ID
         * @param {string} closeId - Close button ID
         */
        init(modalId, triggerId, closeId) {
            const modal = Utils.$(modalId);
            const trigger = Utils.$(triggerId);
            const closeBtn = Utils.$(closeId);

            if (!modal) {
                return;
            }

            if (trigger) {
                trigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    this.open(modalId);
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.close(modalId);
                });
            }

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.close(modalId);
                }
            });
        }
    };

    /**
     * Subscription Module
     */
    const Subscription = {
        init() {
            const modal = document.querySelector('#subscriptionModal');
            const subscribeBtn = document.querySelector('#subscribeBtn');
            
            if (modal && subscribeBtn) {
                Modal.init('#subscriptionModal', '#subscribeBtn', '#subscriptionModalClose');
            }
            
            const form = Utils.$('#subscriptionForm');
            if (form) {
                form.removeAttribute('onsubmit');
                Utils.on(form, 'submit', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.handleSubmit(e);
                });
            }

            const ctaForms = Utils.$$('.cta-subscription-form, .custom-cta-form');
            ctaForms.forEach((form) => {
                form.removeAttribute('onsubmit');
                form.setAttribute('action', 'javascript:void(0);');
                form.setAttribute('method', 'post');
                Utils.on(form, 'submit', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.handleSubmit(e);
                });
            });

            const newsletterForms = Utils.$$('.newsletter-form');
            newsletterForms.forEach((form) => {
                form.removeAttribute('onsubmit');
                form.setAttribute('action', 'javascript:void(0);');
                form.setAttribute('method', 'post');
                Utils.on(form, 'submit', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.handleSubmit(e);
                });
            });
        },

        /**
         * Handle subscription form submission
         * @param {Event} event - Form submit event
         */
        handleSubmit(event) {
            const form = event.target || event.currentTarget;
            const emailInput = form.querySelector('input[type="email"]');
            const email = emailInput?.value.trim() || '';

            if (!email || !this.validateEmail(email)) {
                this.showError(form, 'Please enter a valid email address');
                return;
            }

            // Disable submit button during submission
            const submitBtn = form.querySelector('button[type="submit"], .modal-submit-btn, .cta-subscribe-button');
            let originalText = '';
            if (submitBtn) {
                submitBtn.disabled = true;
                originalText = submitBtn.textContent || submitBtn.innerText || 'Subscribe';
                submitBtn.setAttribute('data-original-text', originalText);
                // Only show "Subscribing..." for modal, keep button text for CTA form
                if (form.id === 'subscriptionForm') {
                    submitBtn.textContent = 'Subscribing...';
                } else {
                    submitBtn.textContent = 'Subscribing...';
                    submitBtn.style.opacity = '0.7';
                }
            }

            // Send AJAX request
            const ajaxUrl = (typeof smeTheme !== 'undefined' && smeTheme.ajaxurl) 
                ? smeTheme.ajaxurl 
                : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
            
            const formData = new FormData();
            formData.append('action', 'sme_newsletter_subscribe');
            formData.append('email', email);
            formData.append('nonce', this.getNonce());


            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.showSuccess(form, data.data?.message || 'Thank you for subscribing! We will send you the latest insights.');
                    this.resetForm(form);
                    
                    if (form.id === 'subscriptionForm') {
                        setTimeout(() => {
                            Modal.close('#subscriptionModal');
                        }, 2000);
                    }
                } else {
                    this.showError(form, data.data?.message || 'Something went wrong. Please try again.');
                }
            })
            .catch(error => {
                this.showError(form, 'Network error. Please check your connection and try again.');
            })
            .finally(() => {
                // Re-enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = submitBtn.getAttribute('data-original-text') || 'Subscribe';
                    submitBtn.style.opacity = '1';
                }
            });
        },

        /**
         * Get nonce for AJAX requests
         * @returns {string} Nonce value
         */
        getNonce() {
            // Try to get nonce from localized script
            if (typeof smeTheme !== 'undefined' && smeTheme.newsletterNonce) {
                return smeTheme.newsletterNonce;
            }
            // Fallback: return empty string (will fail security check but won't break)
            return '';
        },

        /**
         * Validate email format
         * @param {string} email - Email to validate
         * @returns {boolean} Is valid email
         */
        validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        /**
         * Show success message
         * @param {Element} form - Form element
         * @param {string} customMessage - Custom success message
         */
        showSuccess(form, customMessage = null) {
            // Remove existing messages
            const existingMessage = form.querySelector('.subscription-success-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            // Check if it's the CTA form (inline form on homepage)
            const isCtaForm = form.classList.contains('cta-subscription-form') || form.classList.contains('newsletter-form');
            
            if (isCtaForm) {
                // For CTA form, replace the form with success message
                const formWrapper = form.parentElement;
                const message = document.createElement('div');
                message.className = 'subscription-success-message cta-success-message';
                message.setAttribute('role', 'alert');
                message.setAttribute('aria-live', 'polite');
                
                // Create SVG icon safely
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('width', '24');
                svg.setAttribute('height', '24');
                svg.setAttribute('viewBox', '0 0 24 24');
                svg.setAttribute('fill', 'none');
                svg.style.marginRight = '10px';
                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', 'M20 6L9 17l-5-5');
                path.setAttribute('stroke', 'currentColor');
                path.setAttribute('stroke-width', '2');
                path.setAttribute('stroke-linecap', 'round');
                path.setAttribute('stroke-linejoin', 'round');
                svg.appendChild(path);
                message.appendChild(svg);
                
                // Add text content safely
                const text = document.createTextNode(customMessage || 'Thank you for subscribing! We will send you the latest insights.');
                message.appendChild(text);
                
                // Hide form and show message
                form.style.display = 'none';
                formWrapper.appendChild(message);
                
                // Reset after delay
                setTimeout(() => {
                    message.remove();
                    form.style.display = 'flex';
                    form.reset();
                }, 4000);
            } else {
                // For modal form, show message inside form
                const message = document.createElement('div');
                message.className = 'subscription-success-message';
                message.setAttribute('role', 'alert');
                message.setAttribute('aria-live', 'polite');
                message.style.cssText = 'background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-top: 15px; border: 1px solid #c3e6cb; display: flex; align-items: center; gap: 10px;';
                
                // Create SVG icon safely
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('width', '20');
                svg.setAttribute('height', '20');
                svg.setAttribute('viewBox', '0 0 24 24');
                svg.setAttribute('fill', 'none');
                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', 'M20 6L9 17l-5-5');
                path.setAttribute('stroke', 'currentColor');
                path.setAttribute('stroke-width', '2');
                path.setAttribute('stroke-linecap', 'round');
                path.setAttribute('stroke-linejoin', 'round');
                svg.appendChild(path);
                message.appendChild(svg);
                
                // Add text in span safely
                const span = document.createElement('span');
                span.textContent = customMessage || 'Thank you for subscribing! We will send you the latest insights.';
                message.appendChild(span);
                
                form.appendChild(message);
                
                setTimeout(() => {
                    message.remove();
                }, 5000);
            }
        },

        /**
         * Show error message
         * @param {Element} input - Input element or form
         * @param {string} message - Error message
         */
        showError(input, message) {
            if (!input) {
                return;
            }

            // Check if input is a form (for CTA form errors)
            const isForm = input.tagName === 'FORM';
            const form = isForm ? input : input.closest('form');
            const emailInput = isForm ? form.querySelector('input[type="email"]') : input;
            
            if (emailInput) {
                emailInput.setAttribute('aria-invalid', 'true');
                emailInput.setAttribute('aria-describedby', 'email-error');
            }
            
            // Remove existing error messages
            const existingError = form ? form.querySelector('.error-message, .subscription-error-message') : input.parentElement.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }

            // Check if it's the CTA form
            const isCtaForm = form && form.classList.contains('cta-subscription-form');
            
            if (isCtaForm) {
                // For CTA form, show error message above the form
                const formWrapper = form.parentElement;
                const errorDiv = document.createElement('div');
                errorDiv.className = 'subscription-error-message cta-error-message';
                errorDiv.setAttribute('role', 'alert');
                errorDiv.setAttribute('aria-live', 'polite');
                
                // Create SVG icon safely
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('width', '20');
                svg.setAttribute('height', '20');
                svg.setAttribute('viewBox', '0 0 24 24');
                svg.setAttribute('fill', 'none');
                svg.style.marginRight = '10px';
                
                const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('cx', '12');
                circle.setAttribute('cy', '12');
                circle.setAttribute('r', '10');
                circle.setAttribute('stroke', 'currentColor');
                circle.setAttribute('stroke-width', '2');
                circle.setAttribute('stroke-linecap', 'round');
                circle.setAttribute('stroke-linejoin', 'round');
                svg.appendChild(circle);
                
                const line1 = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line1.setAttribute('x1', '12');
                line1.setAttribute('y1', '8');
                line1.setAttribute('x2', '12');
                line1.setAttribute('y2', '12');
                line1.setAttribute('stroke', 'currentColor');
                line1.setAttribute('stroke-width', '2');
                line1.setAttribute('stroke-linecap', 'round');
                line1.setAttribute('stroke-linejoin', 'round');
                svg.appendChild(line1);
                
                const line2 = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line2.setAttribute('x1', '12');
                line2.setAttribute('y1', '16');
                line2.setAttribute('x2', '12.01');
                line2.setAttribute('y2', '16');
                line2.setAttribute('stroke', 'currentColor');
                line2.setAttribute('stroke-width', '2');
                line2.setAttribute('stroke-linecap', 'round');
                line2.setAttribute('stroke-linejoin', 'round');
                svg.appendChild(line2);
                
                errorDiv.appendChild(svg);
                
                // Add text content safely
                const text = document.createTextNode(message);
                errorDiv.appendChild(text);
                
                formWrapper.insertBefore(errorDiv, form);
                
                // Remove error after delay
                setTimeout(() => {
                    errorDiv.remove();
                }, 5000);
            } else {
                // For modal form, show error below input
                const error = document.createElement('span');
                error.id = 'email-error';
                error.className = 'error-message';
                error.textContent = message;
                const parent = emailInput ? emailInput.parentElement : input.parentElement;
                if (parent) {
                    parent.appendChild(error);
                }
            }
        },

        /**
         * Reset form
         * @param {Element} form - Form element
         */
        resetForm(form) {
            const emailInput = form.querySelector('input[type="email"]');
            if (emailInput) {
                emailInput.value = '';
                emailInput.removeAttribute('aria-invalid');
                emailInput.removeAttribute('aria-describedby');
            }
        }
    };

    /**
     * Contact Form Module
     */
    const ContactForm = {
        init() {
            const form = Utils.$('#contactForm');
            if (form) {
                Utils.on(form, 'submit', (e) => {
                    e.preventDefault();
                    this.handleSubmit(e);
                });
            }
        },

        handleSubmit(event) {
            const form = event.target || event.currentTarget;
            const submitBtn = form.querySelector('.submit-btn');
            const originalText = submitBtn ? submitBtn.textContent : 'Send Message';
            
            // Disable submit button
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Sending...';
            }

            // Get form data
            const formData = new FormData(form);
            formData.append('action', 'sme_contact_form');

            // Send AJAX request
            const ajaxUrl = (typeof smeTheme !== 'undefined' && smeTheme.ajaxurl) 
                ? smeTheme.ajaxurl 
                : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.showSuccess(form, data.data?.message || 'Thank you for your message! We will get back to you soon.');
                    form.reset();
                } else {
                    this.showError(form, data.data?.message || 'Something went wrong. Please try again.');
                }
            })
            .catch(error => {
                this.showError(form, 'Network error. Please check your connection and try again.');
            })
            .finally(() => {
                // Re-enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
        },

        showSuccess(form, message) {
            // Remove existing messages
            const existingMessage = form.querySelector('.contact-form-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            const messageDiv = document.createElement('div');
            messageDiv.className = 'contact-form-message contact-form-success';
            messageDiv.setAttribute('role', 'alert');
            messageDiv.setAttribute('aria-live', 'polite');
            messageDiv.textContent = message;
            messageDiv.style.cssText = 'background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb;';
            
            form.insertBefore(messageDiv, form.firstChild);
            
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        },

        showError(form, message) {
            // Remove existing messages
            const existingMessage = form.querySelector('.contact-form-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            const messageDiv = document.createElement('div');
            messageDiv.className = 'contact-form-message contact-form-error';
            messageDiv.setAttribute('role', 'alert');
            messageDiv.setAttribute('aria-live', 'polite');
            messageDiv.textContent = message;
            messageDiv.style.cssText = 'background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb;';
            
            form.insertBefore(messageDiv, form.firstChild);
            
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }
    };

    /**
     * Search Overlay Module
     */
    const SearchOverlay = {
        overlay: null,
        initialized: false,
        
        init() {
            // Prevent double initialization
            if (this.initialized) {
                return;
            }
            
            this.overlay = document.querySelector('#searchOverlay');
            if (!this.overlay) {
                return;
            }

            const searchBtn = document.querySelector('#searchBtn');
            const mobileSearchBtn = document.querySelector('#mobileSearchBtn');
            const self = this;
            
            if (searchBtn) {
                searchBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    self.open();
                }, false);
            }
            
            if (mobileSearchBtn) {
                mobileSearchBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    self.open();
                });
            }

            const closeBtn = this.overlay.querySelector('.search-overlay-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.close();
                });
            }

            this.overlay.addEventListener('click', function(e) {
                if (e.target === self.overlay) {
                    self.close();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && self.overlay && self.overlay.classList.contains('active')) {
                    self.close();
                }
            });
            
            this.initialized = true;
        },

        open() {
            // Always get fresh reference to overlay
            const overlay = document.querySelector('#searchOverlay');
            
            if (!overlay) {
                return;
            }

            overlay.classList.add('active');
            overlay.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important; position: fixed !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; z-index: 100000 !important;';
            document.body.style.overflow = 'hidden';
            
            // Update aria-expanded for search button
            const searchBtn = document.querySelector('#searchBtn');
            if (searchBtn) {
                searchBtn.setAttribute('aria-expanded', 'true');
            }
            
            setTimeout(() => {
            const input = overlay.querySelector('.search-overlay-input');
            if (input) {
                    input.focus();
            }
            }, 150);
        },

        close() {
            if (!this.overlay) {
                this.overlay = document.querySelector('#searchOverlay');
            }
            
            if (!this.overlay) {
                return;
            }

            this.overlay.classList.remove('active');
            this.overlay.style.display = 'none';
            this.overlay.style.visibility = 'hidden';
            this.overlay.style.opacity = '0';
            document.body.style.overflow = '';
            
            // Update aria-expanded for search button
            const searchBtn = document.querySelector('#searchBtn');
            if (searchBtn) {
                searchBtn.setAttribute('aria-expanded', 'false');
            }
        }
    };

    /**
     * Mobile Menu Module
     */
    const MobileMenu = {
        toggle: null,
        wrapper: null,
        closeBtn: null,
        isOpen: false,

        init() {
            this.toggle = Utils.$('#mobileMenuToggle');
            this.wrapper = Utils.$('.mobile-nav-wrapper');
            this.closeBtn = Utils.$('#mobileMenuClose');

            if (!this.toggle || !this.wrapper) {
                return;
            }

            // Toggle button - use direct event listener for better compatibility
            this.toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleMenu();
            });

            // Close button
            if (this.closeBtn) {
                this.closeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.close();
                });
            }

            // Close on background click
            this.wrapper.addEventListener('click', (e) => {
                if (e.target === this.wrapper) {
                    this.close();
                }
            });

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.close();
                }
            });

            // Handle menu links - use event delegation
            this.wrapper.addEventListener('click', (e) => {
                const link = e.target.closest('.main-nav a');
                if (!link) return;
                
                const href = link.getAttribute('href');
                if (!href || href === '#') return;
                
                // Only close for anchor links, let regular links navigate
                if (href.startsWith('#')) {
                    e.preventDefault();
                    this.close();
                    const target = document.querySelector(href);
                    if (target) {
                        setTimeout(() => {
                            target.scrollIntoView({ behavior: 'smooth' });
                        }, 100);
                    }
                }
                // For regular links, don't prevent default - let browser navigate
            });
        },

        toggleMenu() {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        },

        open() {
            if (!this.wrapper || !this.toggle) return;
            
            this.toggle.classList.add('active');
            this.wrapper.classList.add('active');
            document.body.classList.add('mobile-menu-open');
            document.documentElement.classList.add('mobile-menu-open');
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
            this.isOpen = true;
        },

        close() {
            if (!this.wrapper || !this.toggle) return;
            
            this.toggle.classList.remove('active');
            this.wrapper.classList.remove('active');
            document.body.classList.remove('mobile-menu-open');
            document.documentElement.classList.remove('mobile-menu-open');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            this.isOpen = false;
        },

        toggleMenu() {
            // Prevent rapid toggling
            if (this._isToggling) {
                return;
            }
            
            this._isToggling = true;
            
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
            
            // Reset toggle lock after a short delay
            setTimeout(() => {
                this._isToggling = false;
            }, 300);
        },

        /**
         * Cleanup - remove all event listeners
         */
        cleanup() {
            // Clear any pending timeouts
            if (this._touchTimeout) {
                clearTimeout(this._touchTimeout);
                this._touchTimeout = null;
            }
            
            // Remove event listeners
            if (this.toggle && this._toggleHandler) {
                Utils.off(this.toggle, 'click', this._toggleHandler);
                Utils.off(this.toggle, 'touchstart', this._toggleHandler);
                Utils.off(this.toggle, 'touchend', this._toggleHandler);
                Utils.off(this.toggle, 'mousedown', this._toggleHandler);
            }
            
            if (this.closeBtn && this._closeHandler) {
                Utils.off(this.closeBtn, 'click', this._closeHandler);
                Utils.off(this.closeBtn, 'touchstart', this._closeHandler);
                Utils.off(this.closeBtn, 'touchend', this._closeHandler);
                Utils.off(this.closeBtn, 'mousedown', this._closeHandler);
            }
            
            if (this.nav && this._linkClickHandler) {
                Utils.off(this.nav, 'click', this._linkClickHandler);
            }
            
            if (this._outsideClickHandler) {
                Utils.off(document, 'mousedown', this._outsideClickHandler);
                Utils.off(document, 'touchstart', this._outsideClickHandler);
            }
            
            if (this._escapeHandler) {
                Utils.off(document, 'keydown', this._escapeHandler);
            }
        }
    };

    /**
     * Contributor Accordion Module
     */
    const ContributorAccordion = {
        initialized: false,
        handlers: new Map(), // Store handlers for cleanup

        init() {
            // Prevent double initialization
            if (this.initialized) {
                return;
            }

            const accordionItems = Utils.$$('.contributor-accordion-item');
            if (accordionItems.length === 0) {
                return;
            }

            accordionItems.forEach((item, index) => {
                const header = item.querySelector('.contributor-accordion-header');
                if (header) {
                    // Open first section by default
                    if (index === 0) {
                        header.setAttribute('aria-expanded', 'true');
                        item.setAttribute('aria-expanded', 'true');
                    }
                    
                    // Create handler and store it
                    const handler = () => {
                        const isExpanded = header.getAttribute('aria-expanded') === 'true';
                        // Toggle current item
                        header.setAttribute('aria-expanded', !isExpanded);
                        item.setAttribute('aria-expanded', !isExpanded);
                    };
                    
                    this.handlers.set(header, handler);
                    Utils.on(header, 'click', handler);
                }
            });
            
            this.initialized = true;
        },

        /**
         * Cleanup - remove all event listeners
         */
        cleanup() {
            this.handlers.forEach((handler, header) => {
                Utils.off(header, 'click', handler);
            });
            this.handlers.clear();
            this.initialized = false;
        }
    };

    /**
     * Footer Accordion Module (Mobile)
     */
    const FooterAccordion = {
        initialized: false,
        handlers: new Map(), // Store handlers for cleanup

        init() {
            // Only initialize on mobile - use requestAnimationFrame to avoid forced reflow
            requestAnimationFrame(() => {
                const width = window.innerWidth;
                if (width > 768) {
                    this.cleanup();
                    return;
                }
                
                // Continue initialization only on mobile
                this._initMobile();
            });
        },
        
        _initMobile() {

            // Prevent double initialization
            if (this.initialized) {
                return;
            }

            const columns = Utils.$$('.footer-column');
            if (columns.length === 0) {
                return;
            }

            columns.forEach((column, index) => {
                const heading = column.querySelector('h4');
                if (heading) {
                    // Create handler and store it
                    const handler = () => {
                        // Close other columns
                        columns.forEach(otherColumn => {
                            if (otherColumn !== column) {
                                otherColumn.classList.remove('active');
                            }
                        });
                        // Toggle current column
                        column.classList.toggle('active');
                    };
                    
                    this.handlers.set(heading, handler);
                    Utils.on(heading, 'click', handler);
                }
            });

            // Open first column by default
            if (columns.length > 0) {
                columns[0].classList.add('active');
            }
            
            this.initialized = true;
        },

        /**
         * Cleanup - remove all event listeners
         */
        cleanup() {
            this.handlers.forEach((handler, heading) => {
                Utils.off(heading, 'click', handler);
            });
            this.handlers.clear();
            this.initialized = false;
        }
    };

    /**
     * Initialize all modules when DOM is ready
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }

        try {
            if (typeof MainSlider !== 'undefined') {
                MainSlider.init();
            }
            
            if (typeof FeaturedArticlesSlider !== 'undefined') {
                FeaturedArticlesSlider.init();
            }
            
            if (typeof LatestArticlesSlider !== 'undefined') {
                LatestArticlesSlider.init();
            }
            
            BackToTop.init();
            Subscription.init();
            ContactForm.init();
            SearchOverlay.init();
            MobileMenu.init();
            FooterAccordion.init();
            ContributorAccordion.init();
        } catch (error) {}
        
        // Ensure SearchOverlay is initialized even if there's an error
        setTimeout(function() {
            if (typeof SearchOverlay !== 'undefined' && !SearchOverlay.initialized) {
                try {
                    SearchOverlay.init();
                } catch (e) {}
        }
        }, 500);

        // Re-initialize footer accordion on resize (for responsive behavior)
        const resizeHandler = Utils.debounce(() => {
            FooterAccordion.cleanup();
            FooterAccordion.init();
        }, 250);
        Utils.on(window, 'resize', resizeHandler, { passive: true });
        
        // Cleanup on page unload
        Utils.on(window, 'beforeunload', () => {
            MainSlider.cleanup();
            FooterAccordion.cleanup();
            ContributorAccordion.cleanup();
        });
    }

    // Start initialization
    init();

    // Expose global functions for backward compatibility
    window.nextSlide = () => MainSlider.next();
    window.prevSlide = () => MainSlider.prev();
    window.goToMainSlide = (index) => MainSlider.goTo(index);
    
    // Featured Articles Slider - only if defined
    if (typeof FeaturedArticlesSlider !== 'undefined') {
        window.nextFeaturedSlide = () => FeaturedArticlesSlider.next();
        window.prevFeaturedSlide = () => FeaturedArticlesSlider.prev();
    }
    
    // Latest Articles Slider - only if defined
    if (typeof LatestArticlesSlider !== 'undefined') {
        window.nextLatestSlide = () => LatestArticlesSlider.next();
        window.prevLatestSlide = () => LatestArticlesSlider.prev();
    }
    window.openSubscriptionModal = () => Modal.open('#subscriptionModal');
    window.closeSubscriptionModal = () => Modal.close('#subscriptionModal');
    window.handleSubscription = (event) => {
        if (event && event.target) {
            Subscription.handleSubmit(event);
        } else {
            // Fallback: find the form and submit it
            const form = Utils.$('.cta-subscription-form');
            if (form) {
                const fakeEvent = { target: form, preventDefault: () => {} };
                Subscription.handleSubmit(fakeEvent);
            }
        }
    };
    
    // Make ajaxurl available globally if not already defined
    if (typeof ajaxurl === 'undefined') {
        window.ajaxurl = (typeof smeTheme !== 'undefined' && smeTheme.ajaxurl) 
            ? smeTheme.ajaxurl 
            : (typeof smeAjax !== 'undefined' && smeAjax.ajaxurl)
            ? smeAjax.ajaxurl
            : '/wp-admin/admin-ajax.php';
    }

})();

/**
 * Blog Category Filter
 * Filters blog posts by category
 */
(function() {
    'use strict';

    function initCategoryFilter() {
        const filterButtons = document.querySelectorAll( '.category-filter-btn' );
        const postCards = document.querySelectorAll( '.blog-post-card' );
        const postsContainer = document.getElementById( 'blog-posts-container' );
        const noPostsMessage = document.getElementById( 'no-posts-message' );

        if ( ! filterButtons.length || ! postCards.length ) {
            return;
        }

        filterButtons.forEach( button => {
            button.addEventListener( 'click', function() {
                const categoryId = this.getAttribute( 'data-category' );
                
                // Update active button
                filterButtons.forEach( btn => {
                    btn.classList.remove( 'active' );
                    const btnColor = btn.style.borderColor || 'var(--accent-primary)';
                    btn.style.background = 'transparent';
                    btn.style.color = btnColor;
                });
                
                this.classList.add( 'active' );
                if ( categoryId === 'all' ) {
                    this.style.background = 'var(--accent-primary)';
                    this.style.color = '#fff';
                    this.style.borderColor = 'var(--accent-primary)';
                } else {
                    const color = this.style.borderColor || 'var(--accent-primary)';
                    this.style.background = color;
                    this.style.color = '#fff';
                    this.style.borderColor = color;
                }

                // Filter posts
                let visibleCount = 0;
                postCards.forEach( card => {
                    const cardCategories = card.getAttribute( 'data-categories' );
                    
                    if ( categoryId === 'all' ) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        const categoryIds = cardCategories ? cardCategories.split( ',' ) : [];
                        if ( categoryIds.includes( categoryId ) ) {
                            card.style.display = 'block';
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    }
                });

                // Show/hide no posts message
                if ( visibleCount === 0 ) {
                    if ( noPostsMessage ) {
                        noPostsMessage.style.display = 'block';
                    }
                    if ( postsContainer ) {
                        postsContainer.style.display = 'none';
                    }
                } else {
                    if ( noPostsMessage ) {
                        noPostsMessage.style.display = 'none';
                    }
                    if ( postsContainer ) {
                        postsContainer.style.display = 'grid';
                    }
                }
            });
        });
    }

    // Initialize on page load
    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initCategoryFilter );
    } else {
        initCategoryFilter();
    }
})();

/**
 * Article Submission Form Module
 */
(function() {
    'use strict';

    const ArticleSubmission = {
        init() {
            const form = document.querySelector('.submission-form');
            if (!form) {
                return;
            }

            // Remove any action attribute and set to prevent default submission
            form.setAttribute('action', 'javascript:void(0);');
            form.setAttribute('onsubmit', 'return false;');
            form.setAttribute('data-ajax', 'true');

            // Remove any existing event listeners and add new one
            const submitHandler = (e) => {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                this.handleSubmit(e);
                return false;
            };
            
            // Remove old listener if exists (try both with and without capture)
            form.removeEventListener('submit', submitHandler, true);
            form.removeEventListener('submit', submitHandler, false);
            
            // Add new listener with capture phase to run before inline script
            form.addEventListener('submit', submitHandler, false);
            
            // Also add click handler to submit button as backup
            const submitBtn = form.querySelector('.submit-btn');
            if (submitBtn) {
                submitBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.handleSubmit(e);
                });
            }
        },

        handleSubmit(event) {
            // Get form - handle both click and submit events
            let form;
            if (event && event.target) {
                // If clicked on button, get the form
                if (event.target.closest) {
                    form = event.target.closest('.submission-form');
                } else {
                    form = event.target.form || document.querySelector('.submission-form');
                }
            }
            
            if (!form) {
                form = document.querySelector('.submission-form');
            }
            
            if (!form) {
                return false;
            }
            
            if (event) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
            }
            const submitBtn = form.querySelector('.submit-btn');
            const originalText = submitBtn ? submitBtn.textContent : 'Submit Your Article';
            
            // Disable submit button during submission
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
                submitBtn.style.opacity = '0.7';
            }

            // Get form data
            const formData = new FormData(form);
            
            // Ensure action is set correctly
            formData.set('action', 'sme_submit_article');
            
            // Ensure nonce is included
            const nonceField = form.querySelector('input[name="article_nonce"]');
            if (nonceField) {
                formData.set('article_nonce', nonceField.value);
            }

            // Send AJAX request
            const ajaxUrl = (typeof smeTheme !== 'undefined' && smeTheme.ajaxurl) 
                ? smeTheme.ajaxurl 
                : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                }

                if (data.success) {
                    this.showSuccess(form, data.data.message || 'Thank you for your submission! We have received it and will review it within two weeks.');
                    form.reset();
                } else {
                    this.showError(form, data.data.message || 'There was an error submitting your article. Please try again.');
                    if (submitBtn) {
                        submitBtn.textContent = originalText;
                    }
                }
            })
            .catch(error => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    submitBtn.style.opacity = '1';
                }
                this.showError(form, 'There was an error submitting your article. Please check your connection and try again.');
            });
        },

        showSuccess(form, message) {
            // Remove any existing messages
            const existingMsg = form.parentElement.querySelector('.submission-message');
            if (existingMsg) {
                existingMsg.remove();
            }

            // Create success message
            const messageDiv = document.createElement('div');
            messageDiv.className = 'submission-message submission-success';
            
            // Create SVG icon safely
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('width', '20');
            svg.setAttribute('height', '20');
            svg.setAttribute('viewBox', '0 0 20 20');
            svg.setAttribute('fill', 'none');
            svg.style.marginRight = '10px';
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', 'M16.667 5L7.5 14.167 3.333 10');
            path.setAttribute('stroke', 'currentColor');
            path.setAttribute('stroke-width', '2');
            path.setAttribute('stroke-linecap', 'round');
            path.setAttribute('stroke-linejoin', 'round');
            svg.appendChild(path);
            messageDiv.appendChild(svg);
            
            // Add text in span safely
            const span = document.createElement('span');
            span.textContent = message;
            messageDiv.appendChild(span);
            
            form.parentElement.insertBefore(messageDiv, form);
            
            // Scroll to message
            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            
            // Remove message after 8 seconds
            setTimeout(() => {
                if (messageDiv.parentElement) {
                    messageDiv.style.transition = 'opacity 0.3s ease';
                    messageDiv.style.opacity = '0';
                    setTimeout(() => {
                        if (messageDiv.parentElement) {
                            messageDiv.remove();
                        }
                    }, 300);
                }
            }, 8000);
        },

        showError(form, message) {
            // Remove any existing messages
            const existingMsg = form.parentElement.querySelector('.submission-message');
            if (existingMsg) {
                existingMsg.remove();
            }

            // Create error message
            const messageDiv = document.createElement('div');
            messageDiv.className = 'submission-message submission-error';
            
            // Create SVG icon safely
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('width', '20');
            svg.setAttribute('height', '20');
            svg.setAttribute('viewBox', '0 0 20 20');
            svg.setAttribute('fill', 'none');
            svg.style.marginRight = '10px';
            
            const path1 = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path1.setAttribute('d', 'M10 18.333a8.333 8.333 0 1 0 0-16.666 8.333 8.333 0 0 0 0 16.666z');
            path1.setAttribute('stroke', 'currentColor');
            path1.setAttribute('stroke-width', '2');
            path1.setAttribute('stroke-linecap', 'round');
            path1.setAttribute('stroke-linejoin', 'round');
            svg.appendChild(path1);
            
            const path2 = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path2.setAttribute('d', 'M10 6.667v3.333M10 13.333h.008');
            path2.setAttribute('stroke', 'currentColor');
            path2.setAttribute('stroke-width', '2');
            path2.setAttribute('stroke-linecap', 'round');
            path2.setAttribute('stroke-linejoin', 'round');
            svg.appendChild(path2);
            
            messageDiv.appendChild(svg);
            
            // Add text in span safely
            const span = document.createElement('span');
            span.textContent = message;
            messageDiv.appendChild(span);
            
            form.parentElement.insertBefore(messageDiv, form);
            
            // Scroll to message
            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    // Initialize on page load
    function initArticleSubmission() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => ArticleSubmission.init());
        } else {
            ArticleSubmission.init();
        }
    }

    initArticleSubmission();
})();

/**
 * Advertising Form Module
 */
(function() {
    'use strict';

    const AdvertisingForm = {
        init() {
            const form = document.getElementById('advertisingForm');
            if (!form) {
                return;
            }

            // Prevent default form submission
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleSubmit(e);
            });
        },

        handleSubmit(event) {
            const form = event.target;
            const submitBtn = form.querySelector('.submit-btn');
            const originalText = submitBtn ? submitBtn.textContent : 'Send Inquiry';
            
            // Validate checkboxes
            const checkboxes = form.querySelectorAll('input[name="opportunities[]"]:checked');
            if (checkboxes.length === 0) {
                this.showMessage('Please select at least one advertising opportunity.', 'error');
                return;
            }
            
            // Disable submit button
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Sending...';
            }
            
            // Get form data
            const formData = new FormData(form);
            
            // Add AJAX action
            formData.append('action', 'sme_advertising_form');
            
            // Send AJAX request
            const ajaxUrl = (typeof smeAjax !== 'undefined' && smeAjax.ajaxurl) ? smeAjax.ajaxurl : '/wp-admin/admin-ajax.php';
            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showMessage(data.data.message || 'Thank you for your inquiry! We have received it and will get back to you soon.', 'success');
                    form.reset();
                } else {
                    this.showMessage(data.data.message || 'An error occurred. Please try again.', 'error');
                }
            })
            .catch(error => {
                this.showMessage('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                // Re-enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
        },

        showMessage(message, type) {
            // Remove existing messages
            const existingMessages = document.querySelectorAll('.advertising-form-message');
            existingMessages.forEach(msg => msg.remove());
            
            // Create message element
            const messageDiv = document.createElement('div');
            messageDiv.className = `advertising-form-message advertising-form-message-${type}`;
            messageDiv.style.cssText = `
                padding: 15px 20px;
                margin-bottom: 20px;
                border-radius: 6px;
                font-size: 1rem;
                font-weight: 500;
                ${type === 'success' 
                    ? 'background: #d4edda; color: #155724; border: 1px solid #c3e6cb;' 
                    : 'background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'
                }
            `;
            messageDiv.textContent = message;
            
            // Insert message before form
            const form = document.getElementById('advertisingForm');
            if (form && form.parentNode) {
                form.parentNode.insertBefore(messageDiv, form);
                
                // Scroll to message
                messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                
                // Auto-remove success message after 5 seconds
                if (type === 'success') {
                    setTimeout(() => {
                        messageDiv.style.transition = 'opacity 0.3s';
                        messageDiv.style.opacity = '0';
                        setTimeout(() => messageDiv.remove(), 300);
                    }, 5000);
                }
            }
        }
    };

    // Initialize on page load
    function initAdvertisingForm() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => AdvertisingForm.init());
        } else {
            AdvertisingForm.init();
        }
    }

    initAdvertisingForm();
})();
