# SME Insights Theme & Plugin

**Project:** SME Insights Theme & Plugin  
**Delivered:** Wednesday, Dec 3, 2025  
**Author:** Remon Romany  

---

## Overview

SME Insights is a flexible and powerful publishing ecosystem designed for business news, insights, and educational content. Unlike single-function themes, this project is architected to be adaptable to different client needs and workflows, providing a robust foundation for small and medium enterprises.

## Architectural Philosophy & Status

The goal was to build a comprehensive ecosystem rather than just a theme. The architecture supports various client needs and workflows.

### Core Functionality
- **Status:** 100% Complete.
- **AI Plugin:** Fully operational.
- **Theme Core Systems:** Performance, caching, and security systems are fully implemented and optimized.

### Editing Workflow Demonstration
The system is designed to support multiple editing workflows based on client preference. Not all elements are currently editable via custom Gutenberg blocks; this was a deliberate strategic choice to demonstrate the system's flexibility across different models:

1.  **Quick & Simple:** Using the native WordPress "Quick Edit" for fast, text-based changes.
2.  **Visual & Live:** Using the front-end "Design Editor" for live visual adjustments.
3.  **Block-Based:** Using a full suite of custom Gutenberg blocks (acting as a Page Builder).
4.  **Hybrid Model:** A mix of the above workflows.

The foundation is ready to be extended into any of these models to perfectly match specific technical skills and desired workflows.

### Known Issues / Next Steps
- **Design Editor:** The main "Design Editor" button is temporarily hidden via CSS. I am currently refactoring its database save function to ensure instantaneous performance.

---

## Technical Documentation & Code Review

The codebase adheres to WordPress best practices and is thoroughly commented for clarity and maintainability.

- **Code Quality:** Well-commented structure following standard WordPress coding conventions.
- **Detailed Docs:** A detailed technical `readme.md` is located inside the theme's folder (`wp-content/themes/sme-insights-theme/README.md`) outlining specific technical implementations.

### Upcoming Resources
I will be providing:
- A series of short video walkthroughs.
- Full documentation explaining advanced features of both the theme and the plugin.
- Links will be shared via a Google Drive folder upon completion.

---

## Project Structure & Installation

### Directory Structure
- **WordPress Core:** Standard WP files in root.
- **Custom Theme:** `wp-content/themes/sme-insights-theme`
    - Contains custom templates, page builder logic, SEO/Performance helpers, and the `coming-soon.php` template.
- **Plugins:** `wp-content/plugins/`

### Running Locally (XAMPP / Localhost)
1.  **Setup:** Place the project folder inside your XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs\sme_insights`).
2.  **Database:** Create a new MySQL database via phpMyAdmin and import the provided SQL dump (if available).
3.  **Configuration:** Update `wp-config.php` with your local database credentials (`DB_NAME`, `DB_USER`, `DB_PASSWORD`).
4.  **Launch:** Start Apache/MySQL and visit `http://localhost/sme_insights`.

---

*Looking forward to your feedback and code review.*
