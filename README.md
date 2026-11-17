

## Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (or any PHP 8+ web server environment with Apache & MySQL)
- Git

### Installation & Local Setup

1. **Clone or move the project into your web server root:**
   - For XAMPP on macOS: `/Applications/XAMPP/xamppfiles/htdocs/creativekit3a`
   - For XAMPP on Windows: `C:\xampp\htdocs\creativekit3a`

2. **Database Setup:**
   - Start Apache and MySQL from the XAMPP Control Panel.
   - Open phpMyAdmin (`http://localhost/phpmyadmin/`).
   - Create a new database named `creativekit3a`.
   - Import `database/schema.sql`, followed by `database/items-page.sql`.

3. **Accessing the Site:**
   - Open your browser and navigate to:
     ```text
     http://localhost/creativekit3a/
     ```

---

## Extending the Catalog

### Adding a New Product Variant Page

1. Duplicate `product/car-charger-adapters.php` (e.g. `product/mobile-powerbanks.php`).
2. Update the page metadata (`$title`, `$meta_desc`, `$extra_css`, `$extra_js`).
3. Create or reuse the corresponding JS dataset in `assets/js/` matching the URL parameters.

### Modifying Categories

- Category definitions and product lists are managed in `assets/js/category-page.js` within `CATEGORY_DATA`.
- Mega-menu categories and links are maintained in `includes/navbar.php`.

---

## License

All rights reserved © CreativeKit 3A.
