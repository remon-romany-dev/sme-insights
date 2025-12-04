/**
 * SME Insights Gutenberg Blocks
 * JavaScript for custom blocks in the editor
 *
 * @package SME_Insights
 * @since 1.0.0
 */

(function() {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, RangeControl, TextControl, SelectControl, ToggleControl } = wp.components;
	const { __ } = wp.i18n;


	// Posts Grid Block
	registerBlockType('sme-insights/posts-grid', {
		title: __('Posts Grid', 'sme-insights'),
		icon: 'grid-view',
		category: 'sme-insights',
		description: __('Display posts in a customizable grid layout', 'sme-insights'),
		attributes: {
			postsPerPage: {
				type: 'number',
				default: 6
			},
			columns: {
				type: 'number',
				default: 3
			}
		},
		edit: function(props) {
			const { attributes, setAttributes } = props;
			return wp.element.createElement('div', {
				className: 'sme-posts-grid-block'
			}, [
				wp.element.createElement('p', { key: 'title' }, __('Posts Grid', 'sme-insights')),
				wp.element.createElement('p', { key: 'desc', style: { fontSize: '12px', opacity: 0.7, marginTop: '8px' } }, __('' + attributes.postsPerPage + ' posts • ' + attributes.columns + ' columns', 'sme-insights')),
				wp.element.createElement(InspectorControls, { key: 'controls' },
					wp.element.createElement(PanelBody, { title: __('Settings', 'sme-insights') },
						wp.element.createElement(RangeControl, {
							label: __('Posts Per Page', 'sme-insights'),
							value: attributes.postsPerPage,
							onChange: (value) => setAttributes({ postsPerPage: value }),
							min: 1,
							max: 12
						}),
						wp.element.createElement(RangeControl, {
							label: __('Columns', 'sme-insights'),
							value: attributes.columns,
							onChange: (value) => setAttributes({ columns: value }),
							min: 1,
							max: 4
						})
					)
				)
			]);
		},
		save: function() {
			return null; // Rendered server-side
		}
	});

	// Category Hero Block
	registerBlockType('sme-insights/category-hero', {
		title: __('Category Hero', 'sme-insights'),
		icon: 'admin-page',
		category: 'sme-insights',
		description: __('Category hero section with title, description, and icon', 'sme-insights'),
		attributes: {
			title: {
				type: 'string',
				default: ''
			},
			description: {
				type: 'string',
				default: ''
			},
			icon: {
				type: 'string',
				default: ''
			},
			color: {
				type: 'string',
				default: '#2563eb'
			}
		},
		edit: function(props) {
			const { attributes, setAttributes } = props;
			return wp.element.createElement('div', {
				className: 'sme-category-hero-block'
			}, [
				wp.element.createElement('p', { key: 'title' }, __('Category Hero', 'sme-insights')),
				wp.element.createElement('p', { key: 'desc', style: { fontSize: '12px', opacity: 0.7, marginTop: '8px' } }, 
					attributes.title ? __('Title: ' + attributes.title, 'sme-insights') : __('Configure in sidebar settings', 'sme-insights')
				),
				wp.element.createElement(InspectorControls, { key: 'controls' },
					wp.element.createElement(PanelBody, { title: __('Settings', 'sme-insights') },
						wp.element.createElement(TextControl, {
							label: __('Title', 'sme-insights'),
							value: attributes.title,
							onChange: (value) => setAttributes({ title: value })
						}),
						wp.element.createElement(TextControl, {
							label: __('Description', 'sme-insights'),
							value: attributes.description,
							onChange: (value) => setAttributes({ description: value })
						}),
						wp.element.createElement(TextControl, {
							label: __('Icon (emoji or $)', 'sme-insights'),
							value: attributes.icon,
							onChange: (value) => setAttributes({ icon: value })
						}),
						wp.element.createElement(TextControl, {
							label: __('Color', 'sme-insights'),
							value: attributes.color,
							onChange: (value) => setAttributes({ color: value })
						})
					)
				)
			]);
		},
		save: function() {
			return null; // Rendered server-side
		}
	});

	// Featured Article Block
	registerBlockType('sme-insights/featured-article', {
		title: __('Featured Article', 'sme-insights'),
		icon: 'star-filled',
		category: 'sme-insights',
		description: __('Display a featured article in large format', 'sme-insights'),
		edit: function(props) {
			return wp.element.createElement('div', {
				className: 'sme-featured-article-block'
			}, [
				wp.element.createElement('p', { key: 'title' }, __('Featured Article', 'sme-insights')),
				wp.element.createElement('p', { key: 'desc', style: { fontSize: '12px', opacity: 0.7, marginTop: '8px' } }, __('Large Display - Shows featured post', 'sme-insights'))
			]);
		},
		save: function() {
			return null; // Rendered server-side
		}
	});

	// Sub Topics Bar Block
	registerBlockType('sme-insights/sub-topics-bar', {
		title: __('Sub Topics Bar', 'sme-insights'),
		icon: 'tag',
		category: 'sme-insights',
		description: __('Display sub-topics navigation bar', 'sme-insights'),
		edit: function(props) {
			return wp.element.createElement('div', {
				className: 'sme-sub-topics-bar-block'
			}, [
				wp.element.createElement('p', { key: 'title' }, __('Sub Topics Bar', 'sme-insights')),
				wp.element.createElement('p', { key: 'desc', style: { fontSize: '12px', opacity: 0.7, marginTop: '8px' } }, __('Navigation Bar - Shows sub-topics links', 'sme-insights'))
			]);
		},
		save: function() {
			return null; // Rendered server-side
		}
	});

	// Popular Articles Block
	registerBlockType('sme-insights/popular-articles', {
		title: __('Popular Articles', 'sme-insights'),
		icon: 'chart-line',
		category: 'sme-insights',
		description: __('Display popular articles section', 'sme-insights'),
		attributes: {
			postsPerPage: {
				type: 'number',
				default: 3
			},
			title: {
				type: 'string',
				default: 'Popular Articles'
			}
		},
		edit: function(props) {
			const { attributes, setAttributes } = props;
			return wp.element.createElement('div', {
				className: 'sme-popular-articles-block'
			}, [
				wp.element.createElement('p', { key: 'title' }, __('Popular Articles', 'sme-insights')),
				wp.element.createElement('p', { key: 'desc', style: { fontSize: '12px', opacity: 0.7, marginTop: '8px' } }, 
					__('' + attributes.title + ' • ' + attributes.postsPerPage + ' posts', 'sme-insights')
				),
				wp.element.createElement(InspectorControls, { key: 'controls' },
					wp.element.createElement(PanelBody, { title: __('Settings', 'sme-insights') },
						wp.element.createElement(TextControl, {
							label: __('Section Title', 'sme-insights'),
							value: attributes.title,
							onChange: (value) => setAttributes({ title: value })
						}),
						wp.element.createElement(RangeControl, {
							label: __('Posts Per Page', 'sme-insights'),
							value: attributes.postsPerPage,
							onChange: (value) => setAttributes({ postsPerPage: value }),
							min: 1,
							max: 6
						})
					)
				)
			]);
		},
		save: function() {
			return null; // Rendered server-side
		}
	});
})();

