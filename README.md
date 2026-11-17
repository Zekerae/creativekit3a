# CreativeKit 3A — Corporate Giveaways & Custom Merchandising

A clean, modular PHP-based e-commerce showcase and quote-request platform for **CreativeKit 3A**, a premier supplier of customized corporate giveaways, branded accessories, and promotional items in the Philippines.

---

## Project Structure

```text
creativekit3a/
├── index.php                          # Homepage
├── category-page.php                  # Dynamic category catalog page
├── product/
│   └── car-charger-adapters.php       # Product variant detail & template page
│
├── includes/                          # Reusable PHP chrome partials
│   ├── head.php                       # HTML <head>, SEO metadata, & CSS loader
│   ├── topbar.php                     # Social media links, contact info, auth triggers
│   ├── navbar.php                     # Primary header with brand logo & mega menu
│   ├── footer.php                     # Global footer, auth modals, & JS loader
│   └── quote-modal.php                # Multi-step "Request a Quote" modal partial
│
├── assets/                            # Static web assets
│   ├── css/
│   │   ├── style.css                  # Core global stylesheet
│   │   ├── category-page.css          # Category catalog styles
│   │   ├── car-charger-adapters.css   # Product variant page styles
│   │   └── quote-modal.css            # Quote modal styles
│   ├── js/
│   │   ├── main.js                    # Global scripts (carousel, navigation, modals)
│   │   ├── category-page.js           # Category filtering, sorting & DOM rendering
│   │   ├── car-charger-adapters.js    # Variant data & rendering logic
│   │   └── quote-modal.js             # Quote request multi-step form logic
│   └── images/
│       ├── logo.png                   # Brand logo
│       └── [product/hero images]      # Product images, banner graphics, and icons
│
├── database/                          # SQL schemas and seed data
│   ├── schema.sql                     # Primary schema definitions
│   └── items-page.sql                 # Product catalog tables & items data
│
├── _trash/                            # Archived legacy / temporary files (git-ignored)
├── .gitignore                         # Git exclusion rules
└── README.md                          # Project documentation
```

---

## Tech Stack

- **Backend:** PHP 8.x
- **Frontend:** Vanilla JavaScript (ES6+), HTML5, CSS3, Font Awesome 6
- **Database:** MySQL / MariaDB (via XAMPP)

---

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
