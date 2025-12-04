/**
 * Visual Editor JavaScript
 * Handles frontend editing functionality
 *
 * @package SME_Insights
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    const VisualEditor = {
        selectedElement: null,
        currentStyles: {},
        isEditing: false,
        initialized: false,
        
        init: function() {
            if (this.initialized) {
                return;
            }
            
            if (!$('body').hasClass('sme-visual-editor-active')) {
                return;
            }
            
            this.initialized = true;
            this.isEditing = true;
            this.setupEditableElements();
            this.setupEventListeners();
            this.loadSavedStyles();
            this.ensurePanelVisible();
            
            const urlParams = new URLSearchParams(window.location.search);
            const smeElement = urlParams.get('sme_element');
            if (smeElement) {
                this.autoSelectElement(smeElement);
            }
        },
        
        autoSelectElement: function(elementType) {
            const self = this;
            let $element = null;
            
            setTimeout(function() {
                if (elementType === 'header') {
                    // Try multiple header selectors
                    $element = $('header, .header, .main-header').first();
                    if ($element.length === 0) {
                        $element = $('[role="banner"]').first();
                    }
                    if ($element.length === 0) {
                        $element = $('.top-bar').closest('header, .header, .main-header').first();
                    }
                } else if (elementType === 'footer') {
                    // Try multiple footer selectors
                    $element = $('footer, .footer').first();
                    if ($element.length === 0) {
                        $element = $('[role="contentinfo"]').first();
                    }
                }
                
                if ($element && $element.length > 0) {
                    if (!$element.attr('data-sme-editable')) {
                        $element.attr('data-sme-editable', elementType);
                    }
                    self.selectElement($element);
                    $('html, body').animate({
                        scrollTop: $element.offset().top - 100
                    }, 500);
                }
            }, 300);
        },
        
        ensurePanelVisible: function() {
            const panel = $('#smeVisualEditorPanel');
            if (panel.length === 0) {
                return;
            }
            
            const adminBarHeight = $('body').hasClass('admin-bar') ? 32 : 0;
            const panelTop = adminBarHeight;
            const panelHeight = adminBarHeight > 0 ? 'calc(100vh - ' + adminBarHeight + 'px)' : '100vh';
            
            panel.css({
                'position': 'fixed',
                'display': 'flex',
                'visibility': 'visible',
                'opacity': '1',
                'right': '0',
                'top': panelTop + 'px',
                'bottom': '0',
                'left': 'auto',
                'width': '400px',
                'height': panelHeight,
                'max-height': panelHeight,
                'z-index': '999998',
                'margin': '0',
                'padding': '0'
            });
            
            if (!$('body').hasClass('sme-visual-editor-active')) {
                $('body').addClass('sme-visual-editor-active');
            }
        },
        
        setupEditableElements: function() {
            $('header, .header, .main-header, .top-bar, .popular-tags-section, .breaking-news-bar').attr('data-sme-editable', 'header');
            $('footer, .footer').attr('data-sme-editable', 'footer');
            $('.entry-content, .post-content, .page-content, main article, .main-content-layout, .container-inner, section, .section').attr('data-sme-editable', 'page');
            $('h1, h2, h3, h4, h5, h6').attr('data-sme-editable', 'heading');
            $('p').attr('data-sme-editable', 'text');
            $('a, button, .btn, .button').attr('data-sme-editable', 'link');
            $('img').attr('data-sme-editable', 'image');
            $('.card, .post-card, .article-card, .blog-post-card, .team-member-card, .core-value-card').attr('data-sme-editable', 'card');
            $('.category-template, .single-template, .archive-template').attr('data-sme-editable', 'template');
            $('.hero, .hero-section, .about-hero, .contact-hero, .blog-hero, .niche-hero').attr('data-sme-editable', 'hero');
        },
        
        setupEventListeners: function() {
            const self = this;
            
            $(document).on('click', '[data-sme-editable]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.selectElement($(this));
            });
            
            $(document).on('click', 'body *', function(e) {
                if ($(this).attr('data-sme-editable') || $(this).closest('.sme-visual-editor-panel').length) {
                    return;
                }
                
                if ($(this).is('script, style, link, meta, noscript, iframe, embed, object')) {
                    return;
                }
                
                e.stopPropagation();
                $(this).attr('data-sme-editable', 'custom');
                self.selectElement($(this));
            });
            
            // Tab switching
            $('.sme-tab').on('click', function() {
                const tab = $(this).data('tab');
                self.switchTab(tab);
            });
            
            // Style controls
            $('.sme-range').on('input', function() {
                self.updateStyle($(this));
            });
            
            $('.sme-select, .sme-color, .sme-number').on('change input', function() {
                self.updateStyle($(this));
            });
            
            // Save changes
            $('#smeSaveChanges').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.saveChanges();
            });
            
            // Save as template
            $('#smeSaveTemplate').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.saveAsTemplate();
            });
            
            // Exit button
            $('.sme-btn-danger').on('click', function(e) {
                if ($(this).attr('href')) {
                    // It's a link, let it work normally but ensure it removes sme_edit and sme_element
                    const href = $(this).attr('href');
                    const url = new URL(href, window.location.origin);
                    url.searchParams.delete('sme_edit');
                    url.searchParams.delete('sme_element');
                    window.location.href = url.toString();
                }
            });
            
            // Load template
            $('#smeLoadTemplate').on('click', function() {
                self.loadTemplate();
            });
            
            // Delete template
            $(document).on('click', '[data-delete-template]', function() {
                const templateId = $(this).data('delete-template');
                self.deleteTemplate(templateId);
            });
            
            // Close on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.sme-visual-editor-panel, [data-sme-editable]').length) {
                    self.deselectElement();
                }
            });
        },
        
        selectElement: function($element) {
            if (!$element || $element.length === 0) {
                return;
            }
            
            this.deselectElement();
            
            this.selectedElement = $element;
            $element.addClass('sme-selected');
            
            // Get current styles
            const styles = window.getComputedStyle($element[0]);
            this.currentStyles = {
                'font-size': styles.fontSize,
                'font-weight': styles.fontWeight,
                'color': styles.color,
                'background-color': styles.backgroundColor,
                'padding-top': styles.paddingTop,
                'padding-right': styles.paddingRight,
                'padding-bottom': styles.paddingBottom,
                'padding-left': styles.paddingLeft,
                'margin-top': styles.marginTop,
                'margin-right': styles.marginRight,
                'margin-bottom': styles.marginBottom,
                'margin-left': styles.marginLeft,
                'width': styles.width,
                'height': styles.height,
                'border-radius': styles.borderRadius,
            };
            
            // Update controls
            this.updateControls();
            
            // Switch to styles tab
            this.switchTab('styles');
        },
        
        deselectElement: function() {
            if (this.selectedElement) {
                this.selectedElement.removeClass('sme-selected');
                this.selectedElement = null;
            }
        },
        
        updateControls: function() {
            const styles = this.currentStyles;
            
            // Font size
            const fontSize = parseInt(styles['font-size']) || 16;
            $('[data-style="font-size"]').val(fontSize).trigger('input');
            $('[data-style="font-size"]').siblings('.sme-value').text(fontSize + 'px');
            
            // Font weight
            $('[data-style="font-weight"]').val(styles['font-weight'] || '400');
            
            // Colors
            $('[data-style="color"]').val(this.rgbToHex(styles['color']));
            $('[data-style="background-color"]').val(this.rgbToHex(styles['background-color']));
            
            // Padding
            $('[data-style="padding-top"]').val(parseInt(styles['padding-top']) || 0);
            $('[data-style="padding-right"]').val(parseInt(styles['padding-right']) || 0);
            $('[data-style="padding-bottom"]').val(parseInt(styles['padding-bottom']) || 0);
            $('[data-style="padding-left"]').val(parseInt(styles['padding-left']) || 0);
            
            // Margin
            $('[data-style="margin-top"]').val(parseInt(styles['margin-top']) || 0);
            $('[data-style="margin-right"]').val(parseInt(styles['margin-right']) || 0);
            $('[data-style="margin-bottom"]').val(parseInt(styles['margin-bottom']) || 0);
            $('[data-style="margin-left"]').val(parseInt(styles['margin-left']) || 0);
            
            // Width
            const width = parseInt(styles['width']) || 100;
            $('[data-style="width"]').val(width).trigger('input');
            $('[data-style="width"]').siblings('.sme-value').text(width + '%');
            
            // Height
            const height = parseInt(styles['height']) || 0;
            $('[data-style="height"]').val(height).trigger('input');
            $('[data-style="height"]').siblings('.sme-value').text(height ? height + 'px' : 'auto');
            
            // Border radius
            const borderRadius = parseInt(styles['border-radius']) || 0;
            $('[data-style="border-radius"]').val(borderRadius).trigger('input');
            $('[data-style="border-radius"]').siblings('.sme-value').text(borderRadius + 'px');
        },
        
        updateStyle: function($control) {
            if (!this.selectedElement) {
                return;
            }
            
            const style = $control.data('style');
            const unit = $control.data('unit') || '';
            let value = $control.val();
            
            if (unit && value !== 'auto') {
                value = value + unit;
            }
            
            this.currentStyles[style] = value;
            this.selectedElement.css(style, value);
            
            // Update value display
            if ($control.hasClass('sme-range')) {
                $control.siblings('.sme-value').text(value);
            }
            
            // Apply style
            this.selectedElement.css(style, value);
            this.currentStyles[style] = value;
        },
        
        switchTab: function(tab) {
            $('.sme-tab').removeClass('active');
            $('.sme-tab-content').removeClass('active');
            
            $(`.sme-tab[data-tab="${tab}"]`).addClass('active');
            $(`.sme-tab-content[data-content="${tab}"]`).addClass('active');
        },
        
        saveChanges: function() {
            if (!this.selectedElement) {
                alert('Please select an element to edit');
                return;
            }
            
            const elementType = this.selectedElement.data('sme-editable');
            const elementId = this.selectedElement.attr('id') || 'element_' + Date.now();
            
            const self = this;
            Object.keys(this.currentStyles).forEach(function(style) {
                self.selectedElement.css(style, self.currentStyles[style]);
            });
            
            try {
                const savedStyles = JSON.parse(localStorage.getItem('sme_visual_editor_styles') || '{}');
                savedStyles[elementId] = {
                    type: elementType,
                    styles: this.currentStyles,
                    timestamp: Date.now()
                };
                localStorage.setItem('sme_visual_editor_styles', JSON.stringify(savedStyles));
            } catch (e) {}
            
            // Try to save via AJAX
            $.ajax({
                url: (typeof smeVisualEditor !== 'undefined') ? smeVisualEditor.ajaxUrl : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'sme_update_element',
                    nonce: (typeof smeVisualEditor !== 'undefined') ? smeVisualEditor.nonce : '',
                    element_id: elementId,
                    type: elementType,
                    styles: JSON.stringify(this.currentStyles),
                    post_id: $('#post_ID').val() || 0,
                    template_name: elementType === 'template' ? this.selectedElement.data('template-name') : '',
                },
                success: function(response) {
                    if (response.success) {
                        alert('Changes saved successfully!');
                    } else {
                        alert('Error: ' + (response.data && response.data.message ? response.data.message : 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('Changes applied! Note: Server save may have failed, but changes are saved locally.');
                }
            });
        },
        
        saveAsTemplate: function() {
            if (!this.selectedElement) {
                alert('Please select an element to save as template');
                return;
            }
            
            const name = prompt('Enter template name:');
            if (!name) return;
            
            const elementType = this.selectedElement.data('sme-editable');
            
            $.ajax({
                url: (typeof smeVisualEditor !== 'undefined') ? smeVisualEditor.ajaxUrl : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'sme_save_template',
                    nonce: (typeof smeVisualEditor !== 'undefined') ? smeVisualEditor.nonce : '',
                    name: name,
                    type: elementType,
                    data: JSON.stringify({
                        styles: this.currentStyles,
                        element: this.selectedElement.prop('outerHTML'),
                    }),
                },
                success: function(response) {
                    if (response.success) {
                        alert('Template saved successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('Error saving template');
                }
            });
        },
        
        loadTemplate: function() {
            const templateId = prompt('Enter template ID (check Templates tab):');
            if (!templateId) return;
            
            $.ajax({
                url: (typeof smeVisualEditor !== 'undefined') ? smeVisualEditor.ajaxUrl : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'sme_load_template',
                    nonce: (typeof smeVisualEditor !== 'undefined') ? smeVisualEditor.nonce : '',
                    template_id: templateId,
                },
                success: function(response) {
                    if (response.success) {
                        VisualEditor.applyTemplate(response.data.data);
                        alert('Template loaded successfully!');
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('Error loading template');
                }
            });
        },
        
        applyTemplate: function(templateData) {
            if (templateData.styles) {
                this.currentStyles = templateData.styles;
                if (this.selectedElement) {
                    this.selectedElement.css(templateData.styles);
                }
            }
        },
        
        deleteTemplate: function(templateId) {
            if (!confirm('Are you sure you want to delete this template?')) {
                return;
            }
            
            $.ajax({
                url: (typeof smeVisualEditor !== 'undefined') ? smeVisualEditor.ajaxUrl : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'sme_delete_template',
                    nonce: (typeof smeVisualEditor !== 'undefined') ? smeVisualEditor.nonce : '',
                    template_id: templateId,
                },
                success: function(response) {
                    if (response.success) {
                        $('[data-template-id="' + templateId + '"]').remove();
                        alert('Template deleted');
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('Error deleting template');
                }
            });
        },
        
        loadSavedStyles: function() {
            // Styles are loaded via PHP in wp_head
            // This function can be used for additional client-side processing if needed
        },
        
        rgbToHex: function(rgb) {
            if (!rgb || rgb === 'rgba(0, 0, 0, 0)' || rgb === 'transparent') {
                return '#000000';
            }
            
            const match = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
            if (match) {
                return '#' + [1, 2, 3].map(i => {
                    const hex = parseInt(match[i]).toString(16);
                    return hex.length === 1 ? '0' + hex : hex;
                }).join('');
            }
            return '#000000';
        }
    };
    
    $(document).ready(function() {
        if ($('body').hasClass('sme-visual-editor-active') && !VisualEditor.initialized) {
            VisualEditor.init();
        }
    });
    
    if (window.location.search.indexOf('sme_edit=1') !== -1) {
        $(window).on('load', function() {
            setTimeout(function() {
                if ($('body').hasClass('sme-visual-editor-active') && !VisualEditor.initialized) {
                    VisualEditor.init();
                }
            }, 100);
        });
    }
    
})(jQuery);

