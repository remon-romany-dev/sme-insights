/**
 * Quick Editor JavaScript
 * Easy content editing with hover buttons
 *
 * @package SME_Insights
 * @since 1.0.0
 */

(function ($) {
	'use strict';

	const QuickEditor = {
		isActive: false,
		selectedElement: null,
		editPanel: null,
		autoSaveTimer: null,
		hasUnsavedChanges: false,
		shouldExitAfterSave: false,
		pendingEdits: {},

		init: function () {
			if (typeof smeQuickEditor === 'undefined') {
				return;
			}

			this.isActive = smeQuickEditor.isActive;
			this.cleanupLocalStorageLegalContent();

			try {
				localStorage.removeItem('sme_quick_edits');
				localStorage.removeItem('sme_quick_edit_content');
				const pendingData = localStorage.getItem('sme_pending_edits');
				if (pendingData) {
					try {
						this.pendingEdits = JSON.parse(pendingData);
					} catch (e) {
						this.pendingEdits = {};
					}
				}
			} catch (e) { }

			// Always setup editable elements first (adds data attributes)
			this.setupEditableElements(false);
			this.ensureElementIds();
			
			// Apply saved edits after a short delay to ensure DOM is ready
			const self = this;
			setTimeout(function() {
				self.applySavedEdits();
			}, 100);

			if (!this.isActive) {
				return;
			}

			$('body').addClass('sme-quick-edit-active');

			this.createEditPanel();
			this.setupEditableElements(true);  // Now add edit buttons
			this.setupEventListeners();
			this.showOverlay();
		},

		ensureElementIds: function () {
			const pageIdentifier = typeof smeQuickEditor !== 'undefined' && smeQuickEditor.pageIdentifier ? smeQuickEditor.pageIdentifier : '';
			$('.sme-quick-editable').each(function () {
				const element = $(this);
				if (!element.attr('data-sme-quick-edit-id')) {
					const elementType = element.attr('data-sme-element') || 'element';
					const elementIndex = $('[data-sme-element="' + elementType + '"]').index(element);
					const stableId = 'sme-qe-' + elementType + '-' + elementIndex + (pageIdentifier ? '-' + pageIdentifier.replace(/[^a-zA-Z0-9]/g, '') : '');
					element.attr('data-sme-quick-edit-id', stableId);
				}

				if (!element.attr('id')) {
					const elementType = element.attr('data-sme-element') || 'element';
					const elementIndex = $('[data-sme-element="' + elementType + '"]').index(element);
					const stableId = 'sme-qe-' + elementType + '-' + elementIndex + (pageIdentifier ? '-' + pageIdentifier.replace(/[^a-zA-Z0-9]/g, '') : '');
					element.attr('id', stableId);
				}
			});
		},

		cleanupLocalStorageLegalContent: function () {
			try {
				const savedEdits = JSON.parse(localStorage.getItem('sme_quick_edits') || '{}');
				if (Object.keys(savedEdits).length === 0) {
					return;
				}

				const legalKeywords = [
					'Disclaimer', 'disclaimer', 'No Professional Advice', 'Accuracy of Information',
					'No Warranties', 'Limitation of Liability', 'The information provided on SME Insights',
					'general informational purposes', 'professional advice', 'qualified professionals',
					'تعديل تم عن طريق', 'كان بايظ', 'While we strive', 'accurate and up-to-date',
					'completeness, accuracy', 'reliability, or suitability', 'seek the advice',
					'loss or damage', 'Not Professional Advice', 'External Links', 'Third-Party Content',
					'Deep analysis of market trends and expert legal advice', 'Deep analysis',
					'expert legal advice', 'market trends and expert legal', 'market trends and expert',
					'expert legal', 'analysis of market trends'
				];

				let cleanedEdits = {};
				let hasLegalContent = false;
				Object.keys(savedEdits).forEach((elementId) => {
					const edit = savedEdits[elementId];
					if (!edit) {
						return;
					}

					const content = edit.content || '';
					const contentText = typeof content === 'string' ? content : (content.toString ? content.toString() : '');
					let isLegal = false;

					for (let i = 0; i < legalKeywords.length; i++) {
						if (contentText.toLowerCase().indexOf(legalKeywords[i].toLowerCase()) !== -1) {
							isLegal = true;
							hasLegalContent = true;
							break;
						}
					}

					if (!isLegal && edit.selector) {
						const legalSelectors = [
							'.contributor-content', '.contributor-accordion-item', '.legal-intro',
							'.contributor-accordion-header', '.contributor-accordion-content',
							'.main-content-area', '.contributor-content-wrapper',
							'.contributor-page-container', '.contact-hero', '.legal-content'
						];
						for (let i = 0; i < legalSelectors.length; i++) {
							if (edit.selector.indexOf(legalSelectors[i]) !== -1) {
								isLegal = true;
								hasLegalContent = true;
								break;
							}
						}
					}

					if (!isLegal) {
						cleanedEdits[elementId] = edit;
					}
				});

				if (hasLegalContent) {
					localStorage.setItem('sme_quick_edits', JSON.stringify(cleanedEdits));
				}

				if (Object.keys(cleanedEdits).length === 0) {
					return;
				}
			} catch (e) { }
		},

		resetAllQuickEdit: function () {
			try {
				localStorage.removeItem('sme_quick_edits');
				localStorage.removeItem('sme_quick_edit_content');
				return true;
			} catch (e) {
				return false;
			}
		},

		applySavedEdits: function () {
			if (typeof smeQuickEditor === 'undefined') {
				return;
			}

			const savedContent = smeQuickEditor.savedContent || {};
			const savedStyles = smeQuickEditor.savedStyles || {};

			if (Object.keys(savedContent).length === 0 && Object.keys(savedStyles).length === 0) {
				return;
			}

			const self = this;

			// Apply saved content
			Object.keys(savedContent).forEach(function (elementId) {
				const contentData = savedContent[elementId];
				if (!contentData) {
					return;
				}

				const content = typeof contentData === 'string' ? contentData : (contentData.content || '');
				const selector = (typeof contentData === 'object' && contentData.selector) ? contentData.selector : '#' + elementId;
				const stableId = (typeof contentData === 'object' && contentData.stable_id) ? contentData.stable_id : elementId;

				let element = null;
				if (stableId && stableId !== elementId) {
					element = $('#' + stableId);
				}
				if (!element || element.length === 0) {
					if (selector.indexOf('#') === 0) {
						element = $(selector);
					} else {
						element = $(selector).first();
					}
				}
				if (!element || element.length === 0) {
					element = $('#' + elementId);
				}

				if (element && element.length > 0 && content) {
					if (element.is('img')) {
						element.attr('alt', content);
					} else if (element.is('input') || element.is('textarea')) {
						element.val(content);
					} else {
						element.html(content);
					}
				}
			});

			// Apply saved styles
			Object.keys(savedStyles).forEach(function (elementId) {
				const styleData = savedStyles[elementId];
				if (!styleData) {
					return;
				}

				const styles = (typeof styleData === 'object' && styleData.styles) ? styleData.styles : styleData;
				const selector = (typeof styleData === 'object' && styleData.selector) ? styleData.selector : '#' + elementId;
				const stableId = (typeof styleData === 'object' && styleData.stable_id) ? styleData.stable_id : elementId;

				if (!styles || typeof styles !== 'object' || Object.keys(styles).length === 0) {
					return;
				}

				let element = null;
				if (stableId && stableId !== elementId) {
					element = $('#' + stableId);
				}
				if (!element || element.length === 0) {
					if (selector.indexOf('#') === 0) {
						element = $(selector);
					} else {
						element = $(selector).first();
					}
				}
				if (!element || element.length === 0) {
					element = $('#' + elementId);
				}

				if (element && element.length > 0) {
					// Check if element has gradient background
					const bgImage = element.css('background-image');
					const hasGradient = bgImage && bgImage !== 'none' && (bgImage.indexOf('gradient') !== -1 || bgImage.indexOf('linear-gradient') !== -1 || bgImage.indexOf('radial-gradient') !== -1);

					// Remove background-color from styles if element has gradient
					const stylesToApply = $.extend({}, styles);
					if (hasGradient && stylesToApply['background-color']) {
						delete stylesToApply['background-color'];
					}

					// Apply styles
					if (Object.keys(stylesToApply).length > 0) {
						element.css(stylesToApply);
					}
				}
			});
		},

		createEditPanel: function () {
					const panel = $('<div>', {
						id: 'smeQuickEditPanel',
						class: 'sme-quick-edit-panel'
					});

					panel.html(`
				<div class="sme-quick-edit-panel-header">
					<div class="sme-quick-edit-panel-title">${smeQuickEditor.strings.edit}</div>
					<button class="sme-quick-edit-panel-close">&times;</button>
				</div>
				<div class="sme-quick-edit-panel-content">
					<div class="sme-quick-edit-control-group">
						<label>Text Content</label>
						<textarea id="smeQuickEditContent" placeholder="Enter content..."></textarea>
					</div>
					<div class="sme-quick-edit-control-group">
						<label>Font Size</label>
						<input type="number" id="smeQuickEditFontSize" min="10" max="100" value="16">
					</div>
					<div class="sme-quick-edit-control-group">
						<label>Text Color</label>
						<input type="color" id="smeQuickEditTextColor" value="#000000">
					</div>
					<div class="sme-quick-edit-control-group">
						<label>Background Color</label>
						<input type="color" id="smeQuickEditBgColor" value="#ffffff">
					</div>
					<div class="sme-quick-edit-control-group">
						<label>Padding</label>
						<input type="number" id="smeQuickEditPadding" min="0" max="100" value="0">
					</div>
				</div>
				<div class="sme-quick-edit-panel-footer">
					<button class="sme-quick-btn sme-quick-btn-primary" id="smeQuickSave">${smeQuickEditor.strings.save}</button>
					<button class="sme-quick-btn sme-quick-btn-secondary" id="smeQuickCancel">${smeQuickEditor.strings.cancel}</button>
				</div>
			`);

					$('body').append(panel);
					this.editPanel = panel;
				},

				setupEditableElements: function (addButtons) {
					$('header, .header, .main-header').not('.sme-quick-edit-panel, .sme-quick-edit-panel *').addClass('sme-quick-editable').attr('data-sme-element', 'header');
					$('footer, .footer, .main-footer').not('.sme-quick-edit-panel, .sme-quick-edit-panel *').addClass('sme-quick-editable').attr('data-sme-element', 'footer');
					$('.entry-content, .post-content, .page-content, main article, .content-area').not('.sme-quick-edit-panel, .sme-quick-edit-panel *').addClass('sme-quick-editable').attr('data-sme-element', 'content');
					$('h1, h2, h3, h4, h5, h6').not('.sme-quick-edit-panel, .sme-quick-edit-panel *').addClass('sme-quick-editable').attr('data-sme-element', 'heading');
					$('p').not('.sme-quick-edit-panel, .sme-quick-edit-panel *').addClass('sme-quick-editable').attr('data-sme-element', 'paragraph');
					$('img').not('.sme-quick-edit-panel, .sme-quick-edit-panel *').addClass('sme-quick-editable').attr('data-sme-element', 'image');
					$('a.button, .button, button').not('.sme-quick-edit-panel, .sme-quick-edit-panel *, #smeQuickSave, #smeQuickCancel, .sme-quick-edit-panel-close').addClass('sme-quick-editable').attr('data-sme-element', 'button');

					if (addButtons) {
						$('.sme-quick-editable').each(function () {
							if ($(this).find('.sme-quick-edit-btn').length === 0) {
								$(this).css('position', 'relative');
								$(this).append('<button class="sme-quick-edit-btn" data-element-id="' + $(this).attr('data-sme-element') + '"></button>');
							}
						});
					}
				},

				setupEventListeners: function () {
					const self = this;

					$(document).on('click', '.sme-quick-edit-btn', function (e) {
						e.stopPropagation();
						e.preventDefault();
						const element = $(this).closest('.sme-quick-editable');
						self.selectElement(element);
					});

					$(document).on('click', '.sme-quick-editable', function (e) {
						if ($(e.target).hasClass('sme-quick-edit-btn')) {
							return;
						}
						if (self.isActive) {
							self.selectElement($(this));
						}
					});

					$(document).on('click', '.sme-quick-edit-panel-close, #smeQuickCancel', function () {
						self.closePanel();
					});

					$(document).on('click', '#smeQuickSave', function (e) {
						e.preventDefault();
						e.stopPropagation();
						if (self.selectedElement) {
							self.shouldExitAfterSave = false;
							self.saveChanges(true);
						}
					});

					$(document).on('click', '#smeQuickSaveAll', function () {
						self.saveAllChanges();
					});

					$(document).on('click', '#smeQuickExit', function (e) {
						e.preventDefault();
						self.exitQuickEdit();
					});

					$(document).on('input change keyup paste', '#smeQuickEditPanel input, #smeQuickEditPanel textarea, #smeQuickEditPanel select', function () {
						self.updatePreview();
						self.hasUnsavedChanges = true;
						clearTimeout(self.autoSaveTimer);
					});
				},

				selectElement: function (element) {
					this.selectedElement = element;
					element.addClass('editing');

					const pageIdentifier = typeof smeQuickEditor !== 'undefined' && smeQuickEditor.pageIdentifier ? smeQuickEditor.pageIdentifier : '';
					if (!element.attr('data-sme-quick-edit-id')) {
						const elementType = element.attr('data-sme-element') || 'element';
						const elementIndex = $('[data-sme-element="' + elementType + '"]').index(element);
						const stableId = 'sme-qe-' + elementType + '-' + elementIndex + (pageIdentifier ? '-' + pageIdentifier.replace(/[^a-zA-Z0-9]/g, '') : '');
						element.attr('data-sme-quick-edit-id', stableId);
					}

					const stableId = element.attr('data-sme-quick-edit-id');
					if (!element.attr('id') || element.attr('id') !== stableId) {
						element.attr('id', stableId);
					}

					const originalHTML = element.html();
					const originalStyles = {};
					const computedStyles = window.getComputedStyle(element[0]);

					originalStyles.color = computedStyles.color;
					originalStyles.fontSize = computedStyles.fontSize;
					originalStyles.padding = computedStyles.padding;
					originalStyles.backgroundColor = computedStyles.backgroundColor;
					originalStyles.backgroundImage = computedStyles.backgroundImage;
					originalStyles.background = computedStyles.background;

					const originalInlineStyle = element.attr('style') || '';

					element.data('sme-original-html', originalHTML);
					element.data('sme-original-styles', originalStyles);
					element.data('sme-original-inline-style', originalInlineStyle);

					const text = element.text().trim();
					const styles = window.getComputedStyle(element[0]);

					const bgImage = element.css('background-image');
					const bgColor = element.css('background-color');
					const bgGradient = element.css('background');

					let originalBg = 'none';
					if (bgImage && bgImage !== 'none' && bgImage !== '') {
						originalBg = bgGradient || bgImage || bgColor || 'none';
						element.data('sme-original-bg-type', 'gradient');
						element.data('sme-original-bg', originalBg);
					} else {
						const computedBg = this.rgbToHex(bgColor);
						if (computedBg === '#ffffff' || computedBg === '#000000' || bgColor === 'rgba(0, 0, 0, 0)' || bgColor === 'transparent') {
							const inlineBg = element.attr('style') ? (element.attr('style').match(/background[^;]*/gi) || []).join('; ') : '';
							if (inlineBg) {
								element.data('sme-original-bg-type', 'inline');
								element.data('sme-original-bg', inlineBg);
								originalBg = inlineBg;
							} else {
								element.data('sme-original-bg-type', 'transparent');
								element.data('sme-original-bg', 'transparent');
								originalBg = 'transparent';
							}
						} else {
							element.data('sme-original-bg-type', 'color');
							element.data('sme-original-bg', computedBg);
							originalBg = computedBg;
						}
					}

					$('#smeQuickEditContent').val(text);
					$('#smeQuickEditFontSize').val(parseInt(styles.fontSize) || 16);
					$('#smeQuickEditTextColor').val(this.rgbToHex(styles.color));

					if (originalBg && (originalBg.includes('gradient') || originalBg.includes('linear-gradient') || originalBg.includes('radial-gradient'))) {
						$('#smeQuickEditBgColor').val('transparent').prop('disabled', true);
						element.data('sme-has-gradient', true);
					} else {
						$('#smeQuickEditBgColor').val(originalBg === 'transparent' || originalBg === 'none' ? '#ffffff' : (originalBg || '#ffffff')).prop('disabled', false);
						element.data('sme-has-gradient', false);
					}

					$('#smeQuickEditPadding').val(parseInt(styles.paddingTop) || 0);

					// Show panel
					this.editPanel.addClass('active');
				},

				updatePreview: function () {
					if (!this.selectedElement) {
						return;
					}

					const content = $('#smeQuickEditContent').val();
					const fontSize = $('#smeQuickEditFontSize').val() + 'px';
					const textColor = $('#smeQuickEditTextColor').val();
					let bgColor = $('#smeQuickEditBgColor').val();
					const padding = $('#smeQuickEditPadding').val() + 'px';

					const originalBg = this.selectedElement.data('sme-original-bg');
					const originalBgType = this.selectedElement.data('sme-original-bg-type');
					const hasGradient = this.selectedElement.data('sme-has-gradient');

					if (content !== undefined && content !== null) {
						this.selectedElement.html(content);
					}

					const stylesToApply = {
						'font-size': fontSize,
						'color': textColor,
						'padding': padding
					};

					if (hasGradient || originalBgType === 'gradient') {
					} else if (originalBgType === 'inline' || originalBgType === 'transparent') {
						if (bgColor && bgColor !== 'transparent' && bgColor !== '#ffffff') {
							stylesToApply['background-color'] = bgColor;
						}
					} else {
						if (bgColor && bgColor !== 'transparent' && bgColor !== originalBg) {
							stylesToApply['background-color'] = bgColor;
						} else if (bgColor === 'transparent' || originalBg === 'transparent') {
							stylesToApply['background-color'] = 'transparent';
						}
					}

					this.selectedElement.css(stylesToApply);
				},

				saveChanges: function (saveToPendingOnly) {
					if (!this.selectedElement) {
						return;
					}

					saveToPendingOnly = saveToPendingOnly || false;
					const self = this;

					const pageIdentifier = typeof smeQuickEditor !== 'undefined' && smeQuickEditor.pageIdentifier ? smeQuickEditor.pageIdentifier : '';
					let stableId = this.selectedElement.attr('data-sme-quick-edit-id');
					if (!stableId) {
						const elementType = this.selectedElement.attr('data-sme-element') || 'element';
						const elementIndex = $('[data-sme-element="' + elementType + '"]').index(this.selectedElement);
						stableId = 'sme-qe-' + elementType + '-' + elementIndex + (pageIdentifier ? '-' + pageIdentifier.replace(/[^a-zA-Z0-9]/g, '') : '');
						this.selectedElement.attr('data-sme-quick-edit-id', stableId);
					}

					if (!this.selectedElement.attr('id') || this.selectedElement.attr('id') !== stableId) {
						this.selectedElement.attr('id', stableId);
					}

					const elementId = stableId;
					const elementType = this.selectedElement.attr('data-sme-element') || 'element';
					const selector = '#' + elementId;

					const content = $('#smeQuickEditContent').val();
					let bgColor = $('#smeQuickEditBgColor').val();
					const originalBg = this.selectedElement.data('sme-original-bg');

					if ((bgColor === '#ffffff' || bgColor === '#000000') && originalBg === 'transparent') {
						bgColor = 'transparent';
					}

					const originalBgType = this.selectedElement.data('sme-original-bg-type');
					const hasGradient = this.selectedElement.data('sme-has-gradient');

					const styles = {
						'font-size': $('#smeQuickEditFontSize').val() + 'px',
						'color': $('#smeQuickEditTextColor').val(),
						'padding': $('#smeQuickEditPadding').val() + 'px'
					};

					if (hasGradient || originalBgType === 'gradient') {
					} else if (originalBgType === 'transparent' || originalBg === 'transparent') {
						if (bgColor && bgColor !== 'transparent' && bgColor !== '#ffffff') {
							styles['background-color'] = bgColor;
						}
					} else {
						if (bgColor && bgColor !== 'transparent' && bgColor !== originalBg && bgColor !== '#ffffff') {
							styles['background-color'] = bgColor;
						} else if (bgColor === 'transparent') {
							styles['background-color'] = 'transparent';
						}
					}

					this.selectedElement.html(content || '');
					this.selectedElement.css(styles);

					if (saveToPendingOnly) {
						// Save to pendingEdits object
						this.pendingEdits[elementId] = {
							elementId: elementId,
							stableId: stableId,
							elementType: elementType,
							selector: selector,
							content: content || '',
							styles: styles,
							pageId: smeQuickEditor.pageId || 0,
							pageIdentifier: typeof smeQuickEditor !== 'undefined' && smeQuickEditor.pageIdentifier ? smeQuickEditor.pageIdentifier : ''
						};

						// Also save to localStorage as backup
						try {
							const pendingData = JSON.parse(localStorage.getItem('sme_pending_edits') || '{}');
							// Merge with existing pendingEdits to ensure we don't lose any edits
							const mergedPending = Object.assign({}, pendingData, this.pendingEdits);
							localStorage.setItem('sme_pending_edits', JSON.stringify(mergedPending));
							// Update this.pendingEdits to include all edits from localStorage
							this.pendingEdits = mergedPending;
						} catch (e) { }

						this.showSaveMessage('success');
						this.hasUnsavedChanges = false;

						if (this.selectedElement) {
							const newHTML = this.selectedElement.html();
							const newStyles = {
								color: this.selectedElement.css('color'),
								fontSize: this.selectedElement.css('font-size'),
								padding: this.selectedElement.css('padding'),
								backgroundColor: this.selectedElement.css('background-color'),
								backgroundImage: this.selectedElement.css('background-image'),
								background: this.selectedElement.css('background')
							};
							this.selectedElement.data('sme-original-html', newHTML);
							this.selectedElement.data('sme-original-styles', newStyles);
							this.selectedElement.data('sme-original-inline-style', this.selectedElement.attr('style') || '');
						}

						setTimeout(function () {
							self.closePanel();
						}, 1000);

						return;
					}

					let contentSaved = false;
					let stylesSaved = false;
					let saveComplete = false;

					const checkSaveComplete = function () {
						if (contentSaved && stylesSaved && !saveComplete) {
							saveComplete = true;
							self.showSaveMessage('success');
							self.hasUnsavedChanges = false;

							if (self.selectedElement) {
								const newHTML = self.selectedElement.html();
								const newStyles = {
									color: self.selectedElement.css('color'),
									fontSize: self.selectedElement.css('font-size'),
									padding: self.selectedElement.css('padding'),
									backgroundColor: self.selectedElement.css('background-color'),
									backgroundImage: self.selectedElement.css('background-image'),
									background: self.selectedElement.css('background')
								};
								self.selectedElement.data('sme-original-html', newHTML);
								self.selectedElement.data('sme-original-styles', newStyles);
								self.selectedElement.data('sme-original-inline-style', self.selectedElement.attr('style') || '');
							}

							setTimeout(function () {
								self.closePanel();
								if (self.shouldExitAfterSave) {
									self.shouldExitAfterSave = false;
									setTimeout(function () {
										self.exitQuickEdit();
									}, 500);
								}
							}, 1000);
						}
					};

					$.ajax({
						url: smeQuickEditor.ajaxUrl,
						type: 'POST',
						data: {
							action: 'sme_quick_edit_content',
							nonce: smeQuickEditor.nonce,
							element_id: elementId,
							stable_id: stableId,
							element_type: elementType,
							selector: selector,
							content: content || '',
							page_id: smeQuickEditor.pageId || 0,
							page_identifier: typeof smeQuickEditor !== 'undefined' && smeQuickEditor.pageIdentifier ? smeQuickEditor.pageIdentifier : ''
						},
						success: function (response) {
							if (response && response.success) {
								contentSaved = true;
							} else {
								contentSaved = true;
								self.showSaveMessage('error');
								return;
							}
							checkSaveComplete();
						},
						error: function (xhr, status, error) {
							contentSaved = true;
							self.showSaveMessage('error');
							checkSaveComplete();
						}
					});

					$.ajax({
						url: smeQuickEditor.ajaxUrl,
						type: 'POST',
						data: {
							action: 'sme_quick_edit_style',
							nonce: smeQuickEditor.nonce,
							element_id: elementId,
							stable_id: stableId,
							element_type: elementType,
							selector: selector,
							styles: styles,
							page_id: smeQuickEditor.pageId || 0,
							page_identifier: typeof smeQuickEditor !== 'undefined' && smeQuickEditor.pageIdentifier ? smeQuickEditor.pageIdentifier : ''
						},
						success: function (response) {
							if (response && response.success) {
								stylesSaved = true;
							} else {
								stylesSaved = true;
								self.showSaveMessage('error');
								return;
							}
							checkSaveComplete();
						},
						error: function (xhr, status, error) {
							stylesSaved = true;
							self.showSaveMessage('error');
							checkSaveComplete();
						}
					});
				},

				saveAllChanges: function () {
					const self = this;

					// Show saving indicator
					const saveBtn = $('#smeQuickSaveAll');
					const originalText = saveBtn.text();
					saveBtn.text('Saving...').prop('disabled', true);

					// Always ensure current edit is saved to pending first
					if (this.editPanel && this.editPanel.hasClass('active') && this.selectedElement && this.hasUnsavedChanges) {
						// Save current edit to pending (synchronous)
						this.saveChanges(true);
					}

					// Reload pendingEdits from localStorage to ensure we have all edits
					try {
						const pendingData = localStorage.getItem('sme_pending_edits');
						if (pendingData) {
							const parsed = JSON.parse(pendingData);
							// Merge with current pendingEdits
							this.pendingEdits = Object.assign({}, this.pendingEdits, parsed);
						}
					} catch (e) { }

					// Save all pending to DB
					setTimeout(function () {
						self.saveAllPendingToDatabase(function () {
							saveBtn.text(originalText).prop('disabled', false);
							self.redirectToNormalPage();
						});
					}, 300);
				},

				saveAllPendingToDatabase: function (callback) {
					const self = this;
					
					// Reload pendingEdits from localStorage to ensure we have all edits
					try {
						const pendingData = localStorage.getItem('sme_pending_edits');
						if (pendingData) {
							const parsed = JSON.parse(pendingData);
							// Merge with current pendingEdits to ensure we have all edits
							this.pendingEdits = Object.assign({}, this.pendingEdits, parsed);
						}
					} catch (e) { }
					
					const pendingKeys = Object.keys(this.pendingEdits);

					if (pendingKeys.length === 0) {
						if (this.editPanel && this.editPanel.hasClass('active')) {
							this.closePanel();
						}
						// Don't exit quick edit mode, just reload if callback provided
						if (callback) {
							callback();
						} else {
							this.exitQuickEdit();
						}
						return;
					}

					const editStatus = {};

					pendingKeys.forEach(function (elementId) {
						editStatus[elementId] = { content: false, styles: false };
					});

					const checkAllComplete = function () {
						let allComplete = true;
						pendingKeys.forEach(function (elementId) {
							if (!editStatus[elementId].content || !editStatus[elementId].styles) {
								allComplete = false;
							}
						});

						if (allComplete) {
							self.pendingEdits = {};
							try {
								localStorage.removeItem('sme_pending_edits');
							} catch (e) { }

							if (self.editPanel && self.editPanel.hasClass('active')) {
								self.closePanel();
							}

							if (callback) {
								callback();
							} else {
								setTimeout(function () {
									self.exitQuickEdit();
								}, 500);
							}
						}
					};

					pendingKeys.forEach(function (elementId) {
						const edit = self.pendingEdits[elementId];

						if (!edit) {
							editStatus[elementId].content = true;
							editStatus[elementId].styles = true;
							checkAllComplete();
							return;
						}

						$.ajax({
							url: smeQuickEditor.ajaxUrl,
							type: 'POST',
							data: {
								action: 'sme_quick_edit_content',
								nonce: smeQuickEditor.nonce,
								element_id: edit.elementId,
								stable_id: edit.stableId,
								element_type: edit.elementType,
								selector: edit.selector,
								content: edit.content || '',
								page_id: edit.pageId || 0,
								page_identifier: edit.pageIdentifier || ''
							},
							dataType: 'json',
							success: function (response) {
								if (response && (response.success === true || (response.success !== false && response.data))) {
									editStatus[elementId].content = true;
								} else {
									editStatus[elementId].content = true;
								}
								checkAllComplete();
							},
							error: function (xhr, status, error) {
								editStatus[elementId].content = true;
								checkAllComplete();
							}
						});

						const stylesToSend = edit.styles || {};
						const stylesJson = JSON.stringify(stylesToSend);

						$.ajax({
							url: smeQuickEditor.ajaxUrl,
							type: 'POST',
							data: {
								action: 'sme_quick_edit_style',
								nonce: smeQuickEditor.nonce,
								element_id: edit.elementId,
								stable_id: edit.stableId,
								element_type: edit.elementType,
								selector: edit.selector,
								styles_json: stylesJson,
								page_id: edit.pageId || 0,
								page_identifier: edit.pageIdentifier || ''
							},
							dataType: 'json',
							success: function (response) {
								if (response && (response.success === true || (response.success !== false && response.data))) {
									editStatus[elementId].styles = true;
								} else {
									editStatus[elementId].styles = true;
								}
								checkAllComplete();
							},
							error: function (xhr, status, error) {
								editStatus[elementId].styles = true;
								checkAllComplete();
							}
						});
					});
				},

				exitQuickEdit: function () {
					if (this.selectedElement && this.hasUnsavedChanges) {
						this.revertToOriginalState();
					}

					if (Object.keys(this.pendingEdits).length > 0) {
						const self = this;
						Object.keys(this.pendingEdits).forEach(function (elementId) {
							const element = $('#' + elementId);
							if (element.length > 0) {
								const originalHTML = element.data('sme-original-html');
								const originalStyles = element.data('sme-original-styles');
								const originalInlineStyle = element.data('sme-original-inline-style');

								if (originalHTML !== undefined) {
									element.html(originalHTML);
								}
								if (originalStyles) {
									element.css(originalStyles);
								}
								if (originalInlineStyle !== undefined) {
									if (originalInlineStyle) {
										element.attr('style', originalInlineStyle);
									} else {
										element.removeAttr('style');
									}
								}
							}
						});

						this.pendingEdits = {};
						try {
							localStorage.removeItem('sme_pending_edits');
						} catch (e) { }
					}

					if (this.editPanel && this.editPanel.hasClass('active')) {
						this.finalizeClose();
					}

					this.redirectToNormalPage();
				},

				redirectToNormalPage: function () {
					$('body').removeClass('sme-quick-edit-active');
					const currentUrl = window.location.href;
					const url = new URL(currentUrl);
					url.searchParams.delete('sme_quick_edit');
					window.location.href = url.toString();
				},

				closePanel: function () {
					if (this.selectedElement && this.hasUnsavedChanges) {
						this.revertToOriginalState();
					}
					this.finalizeClose();
				},

				revertToOriginalState: function () {
					if (!this.selectedElement) {
						return;
					}

					const originalHTML = this.selectedElement.data('sme-original-html');
					const originalStyles = this.selectedElement.data('sme-original-styles');
					const originalInlineStyle = this.selectedElement.data('sme-original-inline-style');

					if (originalHTML !== undefined) {
						this.selectedElement.html(originalHTML);
					}

					if (originalStyles && typeof originalStyles === 'object') {
						if (originalStyles.color) {
							this.selectedElement.css('color', originalStyles.color);
						}
						if (originalStyles.fontSize) {
							this.selectedElement.css('font-size', originalStyles.fontSize);
						}
						if (originalStyles.padding) {
							this.selectedElement.css('padding', originalStyles.padding);
						}
						if (originalStyles.backgroundColor) {
							this.selectedElement.css('background-color', originalStyles.backgroundColor);
						}
						if (originalStyles.backgroundImage) {
							this.selectedElement.css('background-image', originalStyles.backgroundImage);
						}
						if (originalStyles.background) {
							this.selectedElement.css('background', originalStyles.background);
						}
					}

					if (originalInlineStyle) {
						this.selectedElement.attr('style', originalInlineStyle);
					} else {
						const currentInlineStyle = this.selectedElement.attr('style') || '';
						if (currentInlineStyle.includes('font-size') || currentInlineStyle.includes('color') || currentInlineStyle.includes('padding') || currentInlineStyle.includes('background')) {
							this.selectedElement.removeAttr('style');
						}
					}

					this.hasUnsavedChanges = false;
				},

				finalizeClose: function () {
					if (this.selectedElement) {
						this.selectedElement.removeClass('editing');
						this.selectedElement.removeData('sme-original-html');
						this.selectedElement.removeData('sme-original-styles');
						this.selectedElement.removeData('sme-original-inline-style');
						this.selectedElement.removeData('sme-original-bg');
						this.selectedElement.removeData('sme-original-bg-type');
						this.selectedElement.removeData('sme-has-gradient');
					}

					this.selectedElement = null;
					this.hasUnsavedChanges = false;
					this.editPanel.removeClass('active');
					$('.sme-quick-edit-save-message').fadeOut(300, function () {
						$(this).remove();
					});
				},

				showSaveMessage: function (type) {
					$('.sme-quick-edit-save-message').remove();

					const message = type === 'success'
						? '<div class="sme-quick-edit-save-message sme-quick-edit-save-success active">✓ Saved successfully</div>'
						: '<div class="sme-quick-edit-save-message sme-quick-edit-save-error active">✗ Error saving</div>';

					const $message = $(message);
					$('body').append($message);

					$message.css({
						display: 'block',
						opacity: '0',
						visibility: 'visible'
					});

					setTimeout(function () {
						$message.css({
							opacity: '1',
							transform: 'translateX(0)'
						});
					}, 10);

					setTimeout(function () {
						$message.css({
							opacity: '0',
							transform: 'translateX(100%)'
						});
						setTimeout(function () {
							$message.remove();
						}, 300);
					}, 3000);
				},

				showOverlay: function () {
					$('.sme-quick-edit-overlay').addClass('active');
				},

				rgbToHex: function (rgb) {
					if (rgb.indexOf('rgb') === -1) {
						return rgb;
					}

					const matches = rgb.match(/\d+/g);
					if (!matches || matches.length < 3) {
						return '#000000';
					}

					const r = parseInt(matches[0]).toString(16).padStart(2, '0');
					const g = parseInt(matches[1]).toString(16).padStart(2, '0');
					const b = parseInt(matches[2]).toString(16).padStart(2, '0');

					return '#' + r + g + b;
				},

				generateSelector: function ($element) {
					if ($element.attr('data-sme-quick-edit-id')) {
						return '#' + $element.attr('data-sme-quick-edit-id');
					}

					if ($element.attr('id')) {
						return '#' + $element.attr('id');
					}

					if ($element.attr('data-sme-element')) {
						const elementType = $element.attr('data-sme-element');
						const index = $('[data-sme-element="' + elementType + '"]').index($element);
						return '[data-sme-element="' + elementType + '"]:eq(' + index + ')';
					}

					if ($element.attr('class')) {
						const classes = $element.attr('class').split(' ').filter(c => c && !c.includes('sme-') && !c.includes('editing'));
						if (classes.length > 0) {
							const firstClass = classes[0];
							const index = $('.' + firstClass).index($element);
							return '.' + firstClass + ':eq(' + index + ')';
						}
					}

					const tagName = $element.prop('tagName').toLowerCase();
					const path = [];
					let current = $element;

					while (current.length && current.prop('tagName') !== 'BODY') {
						const parent = current.parent();
						const siblings = parent.children(current.prop('tagName'));
						const index = siblings.index(current);
						path.unshift(tagName + ':eq(' + index + ')');
						current = parent;
					}
					return path.join(' > ') || tagName;
				}
			};

			window.smeQuickEditorInstance = QuickEditor;

			$(document).ready(function () {
				QuickEditor.init();
			});

		})(jQuery);
