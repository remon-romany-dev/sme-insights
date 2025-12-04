/**
 * Template Manager JavaScript
 *
 * @package SME_Insights
 * @since 1.0.0
 */

(function($) {
	'use strict';
	
	$(document).ready(function() {
		// Export template
		$('.sme-export-btn').on('click', function() {
			const $btn = $(this);
			const type = $btn.data('type');
			const templateId = $btn.data('template-id');
			
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'sme_export_template',
					nonce: smeTemplateManager.nonce,
					type: type,
					template_id: templateId,
				},
				success: function(response) {
					if (response.success) {
						const dataStr = JSON.stringify(response.data.data, null, 2);
						const dataBlob = new Blob([dataStr], {type: 'application/json'});
						const url = window.URL.createObjectURL(dataBlob);
						const a = document.createElement('a');
						a.href = url;
						a.download = response.data.filename;
						document.body.appendChild(a);
						a.click();
						document.body.removeChild(a);
						window.URL.revokeObjectURL(url);
					} else {
						alert('Error: ' + response.data.message);
					}
				},
				error: function() {
					alert('Error exporting template');
				}
			});
		});
		
		// Import template
		$('#smeImportForm').on('submit', function(e) {
			e.preventDefault();
			
			const fileInput = $('#smeImportFile')[0];
			if (!fileInput || !fileInput.files || !fileInput.files[0]) {
				alert('Please select a file to import');
				return;
			}
			
			const formData = new FormData();
			formData.append('action', 'sme_import_template');
			formData.append('nonce', (typeof smeTemplateManager !== 'undefined') ? smeTemplateManager.nonce : '');
			formData.append('template_file', fileInput.files[0]);
			
			$.ajax({
				url: (typeof ajaxurl !== 'undefined') ? ajaxurl : '/wp-admin/admin-ajax.php',
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: function(response) {
					if (response.success) {
						alert(response.data.message || 'Template imported successfully');
						location.reload();
					} else {
						alert('Error: ' + (response.data ? response.data.message : 'Unknown error'));
					}
				},
				error: function() {
					alert('Error importing template');
				}
			});
		});
		
		// Duplicate template
		$('.sme-duplicate-btn').on('click', function() {
			const templateId = $(this).data('template-id');
			
			if (!templateId) {
				alert('Template ID not found');
				return;
			}
			
			if (!confirm('Are you sure you want to duplicate this template?')) {
				return;
			}
			
			$.ajax({
				url: (typeof ajaxurl !== 'undefined') ? ajaxurl : '/wp-admin/admin-ajax.php',
				type: 'POST',
				data: {
					action: 'sme_duplicate_template',
					nonce: (typeof smeTemplateManager !== 'undefined') ? smeTemplateManager.nonce : '',
					template_id: templateId,
				},
				success: function(response) {
					if (response.success) {
						alert(response.data.message || 'Template duplicated successfully');
						location.reload();
					} else {
						alert('Error: ' + (response.data ? response.data.message : 'Unknown error'));
					}
				},
				error: function() {
					alert('Error duplicating template');
				}
			});
		});
		
		// Delete template
		$('.sme-delete-btn').on('click', function() {
			const templateId = $(this).data('template-id');
			
			if (!templateId) {
				alert('Template ID not found');
				return;
			}
			
			if (!confirm('Are you sure you want to delete this template?')) {
				return;
			}
			
			$.ajax({
				url: (typeof ajaxurl !== 'undefined') ? ajaxurl : '/wp-admin/admin-ajax.php',
				type: 'POST',
				data: {
					action: 'sme_delete_template',
					nonce: (typeof smeTemplateManager !== 'undefined') ? smeTemplateManager.nonce : '',
					template_id: templateId,
				},
				success: function(response) {
					if (response.success) {
						alert('Template deleted successfully');
						location.reload();
					} else {
						alert('Error: ' + (response.data ? response.data.message : 'Unknown error'));
					}
				},
				error: function() {
					alert('Error deleting template');
				}
			});
		});
		
		// Reset header/footer
		$('.sme-reset-btn').on('click', function() {
			const type = $(this).data('type');
			
			if (!confirm('Are you sure you want to reset ' + type + ' styles to default?')) {
				return;
			}
			
			const action = type === 'header' ? 'sme_reset_header_styles' : 'sme_reset_footer_styles';
			
			$.ajax({
				url: (typeof ajaxurl !== 'undefined') ? ajaxurl : '/wp-admin/admin-ajax.php',
				type: 'POST',
				data: {
					action: action,
					nonce: (typeof smeTemplateManager !== 'undefined') ? smeTemplateManager.nonce : '',
				},
				success: function(response) {
					if (response.success) {
						alert(response.data.message || 'Styles reset successfully');
						location.reload();
					} else {
						alert('Error: ' + (response.data ? response.data.message : 'Unknown error'));
					}
				},
				error: function() {
					alert('Error resetting styles');
				}
			});
		});
	});
	
})(jQuery);

