/**
 * Template Customizer Admin JavaScript
 *
 * @package SME_Insights
 * @since 1.0.0
 */

(function($) {
	'use strict';
	
	$(document).ready(function() {
		// Initialize color pickers
		if ($.fn.wpColorPicker) {
			$('.sme-color-picker').wpColorPicker();
		}
		
		// Form submission
		$('#smeTemplateCustomizerForm').on('submit', function(e) {
			e.preventDefault();
			
			const form = $(this);
			const templateType = form.data('template-type');
			const formData = form.serializeArray();
			const styles = {};
			
			// Convert form data to object
			$.each(formData, function(i, field) {
				if (field.name && field.value !== undefined) {
					styles[field.name] = field.value;
				}
			});
			
			if (Object.keys(styles).length === 0) {
				showNotice('error', 'No styles to save');
				return;
			}
			
			$.ajax({
				url: (typeof smeTemplateCustomizer !== 'undefined') ? smeTemplateCustomizer.ajaxUrl : ajaxurl,
				type: 'POST',
				data: {
					action: 'sme_save_template_styles',
					nonce: (typeof smeTemplateCustomizer !== 'undefined') ? smeTemplateCustomizer.nonce : '',
					template_type: templateType,
					styles: styles,
				},
				success: function(response) {
					if (response.success) {
						showNotice('success', response.data.message || 'Changes saved successfully!');
					} else {
						showNotice('error', response.data.message || 'Error saving changes');
					}
				},
				error: function() {
					showNotice('error', (typeof smeTemplateCustomizer !== 'undefined' && smeTemplateCustomizer.strings) ? smeTemplateCustomizer.strings.error : 'Error saving changes');
				}
			});
		});
		
		// Reset styles
		$('#smeResetStyles').on('click', function() {
			if (!confirm('Are you sure you want to reset all styles to default?')) {
				return;
			}
			
			const form = $('#smeTemplateCustomizerForm');
			const templateType = form.data('template-type');
			
			if (!templateType) {
				showNotice('error', 'Template type not found');
				return;
			}
			
			$.ajax({
				url: (typeof smeTemplateCustomizer !== 'undefined') ? smeTemplateCustomizer.ajaxUrl : ajaxurl,
				type: 'POST',
				data: {
					action: 'sme_reset_template_styles',
					nonce: (typeof smeTemplateCustomizer !== 'undefined') ? smeTemplateCustomizer.nonce : '',
					template_type: templateType,
				},
				success: function(response) {
					if (response.success) {
						// Reload page to show default values
						location.reload();
					} else {
						showNotice('error', response.data.message || 'Error resetting styles');
					}
				},
				error: function() {
					showNotice('error', (typeof smeTemplateCustomizer !== 'undefined' && smeTemplateCustomizer.strings) ? smeTemplateCustomizer.strings.error : 'Error resetting styles');
				}
			});
		});
		
		// Show notice
		function showNotice(type, message) {
			const notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
			$('.wrap').prepend(notice);
			
			setTimeout(function() {
				notice.fadeOut(function() {
					notice.remove();
				});
			}, 3000);
		}
	});
	
})(jQuery);

