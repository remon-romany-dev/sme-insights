# SME Insights Theme

**Author:** Remon Romany  
**Portfolio:** [https://prortec.com/remon-romany](https://prortec.com/remon-romany)  
**Live Demo:** [https://sme-insight.prortec.com](https://sme-insight.prortec.com)  
**Version:** 1.0.0  
**License:** GPL v2 or later

---

## Overview

SME Insights is a flexible and powerful WordPress theme designed for business news, insights, and educational content. This theme is architected to be adaptable to different client needs and workflows, providing a robust foundation for small and medium enterprises.

### Key Highlights

- **No External Dependencies**: All features built-in, no premium plugins required
- **Auto Content Setup**: Pages and sample content created automatically on activation
- **Multiple Editing Workflows**: Quick Edit, Visual Editor, and Gutenberg blocks
- **Performance Optimized**: Critical CSS, lazy loading, CDN support, query optimization
- **SEO Ready**: Schema.org markup, XML sitemap, meta tags, Open Graph support
- **Production Ready**: Fully operational and tested

---

## Installation

### Quick Start

1. Upload the `sme-insights-theme` folder to `/wp-content/themes/` in your WordPress installation
2. Go to **Appearance > Themes** in WordPress admin
3. Click **Activate** on SME Insights Theme
4. The theme will automatically create necessary pages and import sample content
5. Customize settings via **Appearance > Customize**

### System Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

---

## Features

### Content Management

- **Custom Post Types & Taxonomies**: Flexible content organization
- **Custom Flexible Content System**: Built-in page builder without requiring ACF Pro
- **Frontend Quick Editor**: Edit text and content directly from the front-end interface
- **Custom Gutenberg Blocks**: Drag-and-drop page building
- **Content Importer**: Automatic import of pages, posts, categories, and sample data
- **Content Manager Dashboard**: Bulk actions for managing content

### Design & Customization

- **Multiple Editing Workflows**: 
  - Quick & Simple: Native WordPress Quick Edit
  - Visual & Live: Front-end Design Editor
  - Block-Based: Custom Gutenberg blocks
  - Hybrid Model: Mix and match workflows
- **Responsive Design**: Mobile-first approach with accessibility compliance
- **Coming Soon Mode**: Built-in maintenance page with countdown and subscription form
- **Template Customizer**: Flexible template management system

### SEO Features

- Schema.org markup (NewsArticle, WebSite, CollectionPage)
- Automatic meta descriptions
- Canonical URLs
- Open Graph and Twitter Card tags
- XML Sitemap generation (accessible at `/sitemap.xml`)
- Image alt text optimization

### Performance Features

- Critical CSS inline in `<head>`
- Deferred JavaScript loading
- Lazy loading for images
- CDN support for assets
- Browser caching headers
- Preconnect hints for external resources
- Optimized database queries
- Image performance attributes (width, height, loading, decoding, fetchpriority)
- Automatic image compression and optimization on upload

### Security & Standards

- **Enhanced Security**: Security headers, input sanitization, output escaping, nonce verification
- **Accessibility**: WCAG compliant with proper ARIA attributes, semantic HTML, and keyboard navigation
- **Internationalization**: Full translation support with load_theme_textdomain
- **WordPress Best Practices**: Follows WordPress coding standards and best practices
- **System Health Check**: Integrated tools to monitor theme requirements and status

---

## Theme Structure

### File Organization

```
sme-insights-theme/
├── assets/
│   ├── css/
│   │   └── main.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── inc/
│   ├── class-theme-setup.php
│   ├── class-post-types.php
│   ├── class-taxonomies.php
│   ├── class-flexible-content.php
│   ├── class-post-meta.php
│   ├── class-assets.php
│   ├── class-blocks.php
│   ├── class-seo-optimizer.php
│   ├── class-helpers.php
│   ├── class-theme-customizer.php
│   ├── class-theme-dashboard.php
│   ├── class-page-setup.php
│   ├── class-content-importer.php
│   ├── class-content-manager.php
│   ├── class-image-optimizer.php
│   ├── class-sitemap.php
│   ├── class-cache.php
│   ├── class-cache-helper.php
│   ├── class-security.php
│   ├── class-database-cleaner.php
│   ├── class-quick-editor.php
│   └── functions-helpers.php
├── template-parts/
│   ├── blocks/
│   │   ├── hero-slider.php
│   │   ├── trending-news.php
│   │   ├── featured-insights.php
│   │   ├── expertise-sections.php
│   │   ├── latest-posts-section.php
│   │   ├── posts-grid.php
│   │   └── cta-section.php
│   └── components/
│       ├── about-sections.php
│       ├── contact-sections.php
│       ├── contributor-sections.php
│       ├── advertise-sections.php
│       ├── legal-content.php
│       └── niche-topic-sections.php
├── header.php
├── footer.php
├── index.php
├── front-page.php
├── single.php
├── archive.php
├── page.php
├── search.php
├── 404.php
├── coming-soon.php
├── page-about.php
├── page-contact.php
├── page-privacy-policy.php
├── page-terms-of-service.php
├── page-disclaimer.php
├── page-advertise-with-us.php
├── page-become-contributor.php
├── page-niche-topic.php
├── taxonomy-main_category.php
├── functions.php
└── style.css
```

### Content Organization

#### Main Categories

- Finance
- Marketing
- Technology
- Growth
- Strategy

#### Taxonomies

- **main_category**: Primary categorization (Finance, Marketing, etc.)
- **sub_topic**: Detailed sub-categorization
- **article_tag**: Flexible tagging system

---

## Using the Theme

### Page Builder

Build custom pages using the built-in Page Builder:

1. Edit any page in WordPress
2. Scroll to "Page Builder" meta box
3. Click "Add Section" and choose a section type
4. Configure each section's settings
5. Use ↑/↓ buttons to reorder sections

### Post Settings

Each post includes:

- Featured Post toggle
- Custom Excerpt field
- Featured Image Alt Text

### Content Management

Access the Content Manager at **Appearance > Content Manager**:

- **Delete Posts & Images**: Remove all posts and their featured images
- **Clear Page Content**: Clear content from all pages while keeping templates
- **Delete All Pages**: Permanently delete all pages
- **Re-import Content**: Re-import all content (pages, posts, categories, tags, images)

### Automatic Content Import

On theme activation, the following are automatically created:

- All static pages (About, Contact, Privacy Policy, Terms, etc.)
- Sample posts with categories and tags
- Featured images for posts
- Category descriptions

### Image Optimization

Configure image optimization at **Appearance > Image Optimizer**:

- Enable/disable automatic optimization
- Set maximum image dimensions
- Set compression quality
- Enable auto alt text generation

---

## Technical Details

### Code Architecture

This theme follows WordPress coding standards and best practices:

- **Object-Oriented Architecture**: Singleton pattern for all classes
- **Namespaced Functions**: Proper code organization
- **Proper Sanitization & Escaping**: Security-first approach
- **Internationalization Ready**: Full translation support
- **Performance Optimized**: Lazy loading, critical CSS, deferred scripts, CDN support
- **SEO-Friendly**: Schema.org markup and comprehensive meta tags
- **Responsive Design**: Mobile-first approach
- **Accessibility Compliant**: WCAG standards, ARIA attributes, semantic HTML
- **Well-Commented Code**: Easy to understand and maintain

---

## Changelog

### Version 1.0.0 - December 2025

**Initial Release**

- Custom post types and taxonomies for flexible content organization
- Built-in page builder with custom Gutenberg blocks
- Frontend Quick Editor for live content editing
- Multiple editing workflow support (Quick Edit, Visual Editor, Block-Based)
- Comprehensive SEO features (Schema.org, XML sitemap, meta tags)
- Performance optimization (critical CSS, lazy loading, CDN support)
- Automatic image optimization and compression
- Content importer and manager dashboard
- Coming Soon mode with countdown timer
- Security enhancements and WCAG accessibility compliance
- System health check and monitoring tools
- Full internationalization support

---

## Support

For support or questions, contact: **remon.romany.dev@gmail.com**
