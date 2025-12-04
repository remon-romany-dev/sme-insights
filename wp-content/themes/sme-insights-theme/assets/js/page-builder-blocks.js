/**
 * Page Builder Blocks JavaScript
 * Enhanced Gutenberg blocks with drag & drop and style controls
 *
 * @package SME_Insights
 * @since 1.0.0
 */

(function() {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, RichText, MediaUpload, BlockControls, AlignmentToolbar } = wp.blockEditor;
	const { PanelBody, RangeControl, TextControl, SelectControl, ColorPicker, Button, ToggleControl } = wp.components;
	const { __ } = wp.i18n;
	const { useState } = wp.element;
	const { useBlockProps } = wp.blockEditor;

	// Text Block
	registerBlockType('sme-insights/text-block', {
		title: __('Text Block', 'sme-insights'),
		icon: 'text',
		category: 'sme-insights',
		description: __('Rich text block with full style customization', 'sme-insights'),
		attributes: {
			content: {
				type: 'string',
				default: '',
			},
			fontSize: {
				type: 'number',
				default: 16,
			},
			fontWeight: {
				type: 'string',
				default: '400',
			},
			textColor: {
				type: 'string',
				default: '#000000',
			},
			backgroundColor: {
				type: 'string',
				default: 'transparent',
			},
			textAlign: {
				type: 'string',
				default: 'left',
			},
			padding: {
				type: 'object',
				default: { top: 0, right: 0, bottom: 0, left: 0 },
			},
			margin: {
				type: 'object',
				default: { top: 0, right: 0, bottom: 0, left: 0 },
			},
			lineHeight: {
				type: 'number',
				default: 1.6,
			},
		},
		edit: function(props) {
			const { attributes, setAttributes } = props;
			const blockProps = useBlockProps();
			
			return wp.element.createElement('div', blockProps, [
				wp.element.createElement(InspectorControls, { key: 'controls' },
					wp.element.createElement(PanelBody, { title: __('Text Settings', 'sme-insights'), initialOpen: true },
						wp.element.createElement(RangeControl, {
							label: __('Font Size', 'sme-insights'),
							value: attributes.fontSize,
							onChange: (value) => setAttributes({ fontSize: value }),
							min: 10,
							max: 100,
						}),
						wp.element.createElement(SelectControl, {
							label: __('Font Weight', 'sme-insights'),
							value: attributes.fontWeight,
							options: [
								{ label: 'Light', value: '300' },
								{ label: 'Normal', value: '400' },
								{ label: 'Semi Bold', value: '600' },
								{ label: 'Bold', value: '700' },
								{ label: 'Black', value: '900' },
							],
							onChange: (value) => setAttributes({ fontWeight: value }),
						}),
						wp.element.createElement('div', { style: { marginBottom: '10px' } },
							wp.element.createElement('label', { style: { display: 'block', marginBottom: '5px' } }, __('Text Color', 'sme-insights')),
							wp.element.createElement('input', {
								type: 'color',
								value: attributes.textColor,
								onChange: (e) => setAttributes({ textColor: e.target.value }),
							})
						),
						wp.element.createElement('div', { style: { marginBottom: '10px' } },
							wp.element.createElement('label', { style: { display: 'block', marginBottom: '5px' } }, __('Background Color', 'sme-insights')),
							wp.element.createElement('input', {
								type: 'color',
								value: attributes.backgroundColor,
								onChange: (e) => setAttributes({ backgroundColor: e.target.value }),
							})
						),
						wp.element.createElement(SelectControl, {
							label: __('Text Align', 'sme-insights'),
							value: attributes.textAlign,
							options: [
								{ label: 'Left', value: 'left' },
								{ label: 'Center', value: 'center' },
								{ label: 'Right', value: 'right' },
								{ label: 'Justify', value: 'justify' },
							],
							onChange: (value) => setAttributes({ textAlign: value }),
						}),
						wp.element.createElement(RangeControl, {
							label: __('Line Height', 'sme-insights'),
							value: attributes.lineHeight,
							onChange: (value) => setAttributes({ lineHeight: value }),
							min: 1,
							max: 3,
							step: 0.1,
						})
					),
					wp.element.createElement(PanelBody, { title: __('Spacing', 'sme-insights') },
						wp.element.createElement(RangeControl, {
							label: __('Padding Top', 'sme-insights'),
							value: attributes.padding.top,
							onChange: (value) => setAttributes({ padding: { ...attributes.padding, top: value } }),
							min: 0,
							max: 200,
						}),
						wp.element.createElement(RangeControl, {
							label: __('Padding Right', 'sme-insights'),
							value: attributes.padding.right,
							onChange: (value) => setAttributes({ padding: { ...attributes.padding, right: value } }),
							min: 0,
							max: 200,
						}),
						wp.element.createElement(RangeControl, {
							label: __('Padding Bottom', 'sme-insights'),
							value: attributes.padding.bottom,
							onChange: (value) => setAttributes({ padding: { ...attributes.padding, bottom: value } }),
							min: 0,
							max: 200,
						}),
						wp.element.createElement(RangeControl, {
							label: __('Padding Left', 'sme-insights'),
							value: attributes.padding.left,
							onChange: (value) => setAttributes({ padding: { ...attributes.padding, left: value } }),
							min: 0,
							max: 200,
						}),
						wp.element.createElement(RangeControl, {
							label: __('Margin Top', 'sme-insights'),
							value: attributes.margin.top,
							onChange: (value) => setAttributes({ margin: { ...attributes.margin, top: value } }),
							min: 0,
							max: 200,
						}),
						wp.element.createElement(RangeControl, {
							label: __('Margin Bottom', 'sme-insights'),
							value: attributes.margin.bottom,
							onChange: (value) => setAttributes({ margin: { ...attributes.margin, bottom: value } }),
							min: 0,
							max: 200,
						})
					)
				),
				wp.element.createElement(BlockControls, { key: 'block-controls' },
					wp.element.createElement(AlignmentToolbar, {
						value: attributes.textAlign,
						onChange: (value) => setAttributes({ textAlign: value }),
					})
				),
				wp.element.createElement('div', {
					key: 'editor',
					style: {
						fontSize: attributes.fontSize + 'px',
						fontWeight: attributes.fontWeight,
						color: attributes.textColor,
						backgroundColor: attributes.backgroundColor !== 'transparent' ? attributes.backgroundColor : 'transparent',
						textAlign: attributes.textAlign,
						padding: attributes.padding.top + 'px ' + attributes.padding.right + 'px ' + attributes.padding.bottom + 'px ' + attributes.padding.left + 'px',
						margin: attributes.margin.top + 'px ' + attributes.margin.right + 'px ' + attributes.margin.bottom + 'px ' + attributes.margin.left + 'px',
						lineHeight: attributes.lineHeight,
						minHeight: '50px',
						border: '1px dashed #ccc',
						padding: '10px',
					}
				},
					wp.element.createElement(RichText, {
						tagName: 'p',
						value: attributes.content,
						onChange: (value) => setAttributes({ content: value }),
						placeholder: __('Enter text...', 'sme-insights'),
					})
				)
			]);
		},
		save: function() {
			return null; // Rendered server-side
		}
	});

	// Image Block
	registerBlockType('sme-insights/image-block', {
		title: __('Image Block', 'sme-insights'),
		icon: 'format-image',
		category: 'sme-insights',
		description: __('Image block with resize and style controls', 'sme-insights'),
		attributes: {
			url: {
				type: 'string',
				default: '',
			},
			alt: {
				type: 'string',
				default: '',
			},
			width: {
				type: 'number',
				default: 100,
			},
			height: {
				type: 'number',
				default: 0,
			},
			align: {
				type: 'string',
				default: 'center',
			},
			borderRadius: {
				type: 'number',
				default: 0,
			},
			margin: {
				type: 'object',
				default: { top: 0, right: 0, bottom: 0, left: 0 },
			},
		},
		edit: function(props) {
			const { attributes, setAttributes } = props;
			const blockProps = useBlockProps();
			
			return wp.element.createElement('div', blockProps, [
				wp.element.createElement(InspectorControls, { key: 'controls' },
					wp.element.createElement(PanelBody, { title: __('Image Settings', 'sme-insights'), initialOpen: true },
						wp.element.createElement(MediaUpload, {
							onSelect: (media) => setAttributes({ url: media.url, alt: media.alt }),
							allowedTypes: ['image'],
							value: attributes.url,
							render: ({ open }) => wp.element.createElement(Button, {
								onClick: open,
								isPrimary: true,
							}, attributes.url ? __('Change Image', 'sme-insights') : __('Select Image', 'sme-insights'))
						}),
						wp.element.createElement(TextControl, {
							label: __('Alt Text', 'sme-insights'),
							value: attributes.alt,
							onChange: (value) => setAttributes({ alt: value }),
						}),
						wp.element.createElement(RangeControl, {
							label: __('Width (%)', 'sme-insights'),
							value: attributes.width,
							onChange: (value) => setAttributes({ width: value }),
							min: 10,
							max: 100,
						}),
						wp.element.createElement(RangeControl, {
							label: __('Border Radius', 'sme-insights'),
							value: attributes.borderRadius,
							onChange: (value) => setAttributes({ borderRadius: value }),
							min: 0,
							max: 50,
						}),
						wp.element.createElement(SelectControl, {
							label: __('Align', 'sme-insights'),
							value: attributes.align,
							options: [
								{ label: 'Left', value: 'left' },
								{ label: 'Center', value: 'center' },
								{ label: 'Right', value: 'right' },
							],
							onChange: (value) => setAttributes({ align: value }),
						})
					)
				),
				wp.element.createElement('div', {
					key: 'editor',
					style: {
						textAlign: attributes.align,
						margin: attributes.margin.top + 'px ' + attributes.margin.right + 'px ' + attributes.margin.bottom + 'px ' + attributes.margin.left + 'px',
					}
				},
					attributes.url ? wp.element.createElement('img', {
						src: attributes.url,
						alt: attributes.alt,
						style: {
							width: attributes.width + '%',
							borderRadius: attributes.borderRadius + 'px',
							maxWidth: '100%',
							height: 'auto',
						}
					}) : wp.element.createElement('div', {
						style: {
							padding: '40px',
							textAlign: 'center',
							border: '2px dashed #ccc',
							background: '#f5f5f5',
						}
					}, __('Click to select image', 'sme-insights'))
				)
			]);
		},
		save: function() {
			return null; // Rendered server-side
		}
	});

	// Button Block
	registerBlockType('sme-insights/button-block', {
		title: __('Button Block', 'sme-insights'),
		icon: 'button',
		category: 'sme-insights',
		description: __('Customizable button with colors and sizes', 'sme-insights'),
		attributes: {
			text: {
				type: 'string',
				default: 'Click Here',
			},
			url: {
				type: 'string',
				default: '#',
			},
			backgroundColor: {
				type: 'string',
				default: '#2563eb',
			},
			textColor: {
				type: 'string',
				default: '#ffffff',
			},
			fontSize: {
				type: 'number',
				default: 16,
			},
			padding: {
				type: 'object',
				default: { top: 12, right: 24, bottom: 12, left: 24 },
			},
			borderRadius: {
				type: 'number',
				default: 6,
			},
			align: {
				type: 'string',
				default: 'left',
			},
		},
		edit: function(props) {
			const { attributes, setAttributes } = props;
			const blockProps = useBlockProps();
			
			return wp.element.createElement('div', blockProps, [
				wp.element.createElement(InspectorControls, { key: 'controls' },
					wp.element.createElement(PanelBody, { title: __('Button Settings', 'sme-insights'), initialOpen: true },
						wp.element.createElement(TextControl, {
							label: __('Button Text', 'sme-insights'),
							value: attributes.text,
							onChange: (value) => setAttributes({ text: value }),
						}),
						wp.element.createElement(TextControl, {
							label: __('Button URL', 'sme-insights'),
							value: attributes.url,
							onChange: (value) => setAttributes({ url: value }),
						}),
						wp.element.createElement('div', { style: { marginBottom: '10px' } },
							wp.element.createElement('label', { style: { display: 'block', marginBottom: '5px' } }, __('Background Color', 'sme-insights')),
							wp.element.createElement('input', {
								type: 'color',
								value: attributes.backgroundColor,
								onChange: (e) => setAttributes({ backgroundColor: e.target.value }),
							})
						),
						wp.element.createElement('div', { style: { marginBottom: '10px' } },
							wp.element.createElement('label', { style: { display: 'block', marginBottom: '5px' } }, __('Text Color', 'sme-insights')),
							wp.element.createElement('input', {
								type: 'color',
								value: attributes.textColor,
								onChange: (e) => setAttributes({ textColor: e.target.value }),
							})
						),
						wp.element.createElement(RangeControl, {
							label: __('Font Size', 'sme-insights'),
							value: attributes.fontSize,
							onChange: (value) => setAttributes({ fontSize: value }),
							min: 12,
							max: 32,
						}),
						wp.element.createElement(RangeControl, {
							label: __('Border Radius', 'sme-insights'),
							value: attributes.borderRadius,
							onChange: (value) => setAttributes({ borderRadius: value }),
							min: 0,
							max: 50,
						}),
						wp.element.createElement(SelectControl, {
							label: __('Align', 'sme-insights'),
							value: attributes.align,
							options: [
								{ label: 'Left', value: 'left' },
								{ label: 'Center', value: 'center' },
								{ label: 'Right', value: 'right' },
							],
							onChange: (value) => setAttributes({ align: value }),
						})
					)
				),
				wp.element.createElement('div', {
					key: 'editor',
					style: {
						textAlign: attributes.align,
						margin: '10px 0',
					}
				},
					wp.element.createElement('a', {
						href: attributes.url,
						style: {
							display: 'inline-block',
							backgroundColor: attributes.backgroundColor,
							color: attributes.textColor,
							fontSize: attributes.fontSize + 'px',
							padding: attributes.padding.top + 'px ' + attributes.padding.right + 'px ' + attributes.padding.bottom + 'px ' + attributes.padding.left + 'px',
							borderRadius: attributes.borderRadius + 'px',
							textDecoration: 'none',
							fontWeight: '600',
						}
					}, attributes.text)
				)
			]);
		},
		save: function() {
			return null; // Rendered server-side
		}
	});

	// Hero Block
	registerBlockType('sme-insights/hero-block', {
		title: __('Hero Block', 'sme-insights'),
		icon: 'cover-image',
		category: 'sme-insights',
		description: __('Full-width hero section with title and background', 'sme-insights'),
		attributes: {
			title: {
				type: 'string',
				default: 'Hero Title',
			},
			subtitle: {
				type: 'string',
				default: 'Hero Subtitle',
			},
			backgroundImage: {
				type: 'string',
				default: '',
			},
			backgroundColor: {
				type: 'string',
				default: '#2563eb',
			},
			textColor: {
				type: 'string',
				default: '#ffffff',
			},
			height: {
				type: 'number',
				default: 400,
			},
			textAlign: {
				type: 'string',
				default: 'center',
			},
		},
		edit: function(props) {
			const { attributes, setAttributes } = props;
			const blockProps = useBlockProps();
			
			return wp.element.createElement('div', blockProps, [
				wp.element.createElement(InspectorControls, { key: 'controls' },
					wp.element.createElement(PanelBody, { title: __('Hero Settings', 'sme-insights'), initialOpen: true },
						wp.element.createElement(TextControl, {
							label: __('Title', 'sme-insights'),
							value: attributes.title,
							onChange: (value) => setAttributes({ title: value }),
						}),
						wp.element.createElement(TextControl, {
							label: __('Subtitle', 'sme-insights'),
							value: attributes.subtitle,
							onChange: (value) => setAttributes({ subtitle: value }),
						}),
						wp.element.createElement(MediaUpload, {
							onSelect: (media) => setAttributes({ backgroundImage: media.url }),
							allowedTypes: ['image'],
							value: attributes.backgroundImage,
							render: ({ open }) => wp.element.createElement(Button, {
								onClick: open,
								isSecondary: true,
							}, attributes.backgroundImage ? __('Change Background', 'sme-insights') : __('Select Background Image', 'sme-insights'))
						}),
						wp.element.createElement('div', { style: { marginBottom: '10px' } },
							wp.element.createElement('label', { style: { display: 'block', marginBottom: '5px' } }, __('Background Color', 'sme-insights')),
							wp.element.createElement('input', {
								type: 'color',
								value: attributes.backgroundColor,
								onChange: (e) => setAttributes({ backgroundColor: e.target.value }),
							})
						),
						wp.element.createElement('div', { style: { marginBottom: '10px' } },
							wp.element.createElement('label', { style: { display: 'block', marginBottom: '5px' } }, __('Text Color', 'sme-insights')),
							wp.element.createElement('input', {
								type: 'color',
								value: attributes.textColor,
								onChange: (e) => setAttributes({ textColor: e.target.value }),
							})
						),
						wp.element.createElement(RangeControl, {
							label: __('Height', 'sme-insights'),
							value: attributes.height,
							onChange: (value) => setAttributes({ height: value }),
							min: 200,
							max: 800,
						}),
						wp.element.createElement(SelectControl, {
							label: __('Text Align', 'sme-insights'),
							value: attributes.textAlign,
							options: [
								{ label: 'Left', value: 'left' },
								{ label: 'Center', value: 'center' },
								{ label: 'Right', value: 'right' },
							],
							onChange: (value) => setAttributes({ textAlign: value }),
						})
					)
				),
				wp.element.createElement('div', {
					key: 'editor',
					style: {
						backgroundImage: attributes.backgroundImage ? 'url(' + attributes.backgroundImage + ')' : 'none',
						backgroundColor: attributes.backgroundColor,
						color: attributes.textColor,
						height: attributes.height + 'px',
						textAlign: attributes.textAlign,
						display: 'flex',
						flexDirection: 'column',
						justifyContent: 'center',
						alignItems: 'center',
						backgroundSize: 'cover',
						backgroundPosition: 'center',
						padding: '40px',
						border: '1px dashed #ccc',
					}
				},
					wp.element.createElement('h1', {
						style: { margin: '0 0 10px 0', fontSize: '48px', fontWeight: 'bold' }
					}, attributes.title),
					wp.element.createElement('p', {
						style: { margin: 0, fontSize: '20px' }
					}, attributes.subtitle)
				)
			]);
		},
		save: function() {
			return null; // Rendered server-side
		}
	});

	// Spacer Block
	registerBlockType('sme-insights/spacer-block', {
		title: __('Spacer Block', 'sme-insights'),
		icon: 'minus',
		category: 'sme-insights',
		description: __('Add vertical spacing between elements', 'sme-insights'),
		attributes: {
			height: {
				type: 'number',
				default: 50,
			},
		},
		edit: function(props) {
			const { attributes, setAttributes } = props;
			const blockProps = useBlockProps();
			
			return wp.element.createElement('div', blockProps, [
				wp.element.createElement(InspectorControls, { key: 'controls' },
					wp.element.createElement(PanelBody, { title: __('Spacer Settings', 'sme-insights'), initialOpen: true },
						wp.element.createElement(RangeControl, {
							label: __('Height (px)', 'sme-insights'),
							value: attributes.height,
							onChange: (value) => setAttributes({ height: value }),
							min: 10,
							max: 500,
						})
					)
				),
				wp.element.createElement('div', {
					key: 'editor',
					style: {
						height: attributes.height + 'px',
						background: 'repeating-linear-gradient(45deg, #f0f0f0, #f0f0f0 10px, #e0e0e0 10px, #e0e0e0 20px)',
						border: '1px dashed #ccc',
						display: 'flex',
						alignItems: 'center',
						justifyContent: 'center',
					}
				}, attributes.height + 'px')
			]);
		},
		save: function() {
			return null; // Rendered server-side
		}
	});

	// Divider Block
	registerBlockType('sme-insights/divider-block', {
		title: __('Divider Block', 'sme-insights'),
		icon: 'minus',
		category: 'sme-insights',
		description: __('Horizontal divider line', 'sme-insights'),
		attributes: {
			color: {
				type: 'string',
				default: '#e5e7eb',
			},
			width: {
				type: 'number',
				default: 100,
			},
			height: {
				type: 'number',
				default: 1,
			},
		},
		edit: function(props) {
			const { attributes, setAttributes } = props;
			const blockProps = useBlockProps();
			
			return wp.element.createElement('div', blockProps, [
				wp.element.createElement(InspectorControls, { key: 'controls' },
					wp.element.createElement(PanelBody, { title: __('Divider Settings', 'sme-insights'), initialOpen: true },
						wp.element.createElement('div', { style: { marginBottom: '10px' } },
							wp.element.createElement('label', { style: { display: 'block', marginBottom: '5px' } }, __('Color', 'sme-insights')),
							wp.element.createElement('input', {
								type: 'color',
								value: attributes.color,
								onChange: (e) => setAttributes({ color: e.target.value }),
							})
						),
						wp.element.createElement(RangeControl, {
							label: __('Width (%)', 'sme-insights'),
							value: attributes.width,
							onChange: (value) => setAttributes({ width: value }),
							min: 10,
							max: 100,
						}),
						wp.element.createElement(RangeControl, {
							label: __('Height (px)', 'sme-insights'),
							value: attributes.height,
							onChange: (value) => setAttributes({ height: value }),
							min: 1,
							max: 10,
						})
					)
				),
				wp.element.createElement('div', {
					key: 'editor',
					style: {
						width: attributes.width + '%',
						height: attributes.height + 'px',
						backgroundColor: attributes.color,
						margin: '20px auto',
					}
				})
			]);
		},
		save: function() {
			return null; // Rendered server-side
		}
	});

	// HTML Block
	registerBlockType('sme-insights/html-block', {
		title: __('Custom HTML/CSS Block', 'sme-insights'),
		icon: 'editor-code',
		category: 'sme-insights',
		description: __('Add custom HTML and CSS code', 'sme-insights'),
		attributes: {
			html: {
				type: 'string',
				default: '',
			},
			css: {
				type: 'string',
				default: '',
			},
		},
		edit: function(props) {
			const { attributes, setAttributes } = props;
			const blockProps = useBlockProps();
			
			return wp.element.createElement('div', blockProps, [
				wp.element.createElement(InspectorControls, { key: 'controls' },
					wp.element.createElement(PanelBody, { title: __('HTML/CSS Settings', 'sme-insights'), initialOpen: true },
						wp.element.createElement('label', { style: { display: 'block', marginBottom: '5px', fontWeight: 'bold' } }, __('HTML Code', 'sme-insights')),
						wp.element.createElement('textarea', {
							value: attributes.html,
							onChange: (e) => setAttributes({ html: e.target.value }),
							style: {
								width: '100%',
								height: '150px',
								fontFamily: 'monospace',
								fontSize: '12px',
							},
							placeholder: __('Enter HTML code...', 'sme-insights'),
						}),
						wp.element.createElement('label', { style: { display: 'block', marginBottom: '5px', marginTop: '15px', fontWeight: 'bold' } }, __('CSS Code', 'sme-insights')),
						wp.element.createElement('textarea', {
							value: attributes.css,
							onChange: (e) => setAttributes({ css: e.target.value }),
							style: {
								width: '100%',
								height: '150px',
								fontFamily: 'monospace',
								fontSize: '12px',
							},
							placeholder: __('Enter CSS code...', 'sme-insights'),
						})
					)
				),
				wp.element.createElement('div', {
					key: 'editor',
					style: {
						padding: '20px',
						border: '1px dashed #ccc',
						background: '#f9f9f9',
					}
				},
					attributes.html ? wp.element.createElement('div', {
						dangerouslySetInnerHTML: { __html: attributes.html }
					}) : wp.element.createElement('p', { style: { color: '#999' } }, __('Add HTML and CSS code in the sidebar', 'sme-insights'))
				)
			]);
		},
		save: function() {
			return null; // Rendered server-side
		}
	});

})();

