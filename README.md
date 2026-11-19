<<<<<<< HEAD
# CreativeKit 3A — Corporate Giveaways & Custom Merchandising Platform

A comprehensive, modular full-stack PHP, JavaScript, CSS, and MySQL e-commerce and administrative merchandising management platform for **CreativeKit 3A** (Philippines).

---

## 1. Directory Tree & Architecture

The codebase strictly adheres to a **Per-Page CSS and JavaScript architecture**, ensuring each HTML/PHP page is cleanly paired with its own dedicated stylesheet and script for modular debugging, supplemented by minimal global helpers.

```text
creativekit3a/
├── config/
│   └── db.php                           # PDO Database Connection & Global Helpers
├── auth/
│   ├── guard.php                        # Route Guard & Role-Based Access Control (RBAC)
│   ├── login.php                        # POST Login Handler (password_verify & audit)
│   └── logout.php                       # Session Destruction & Audit Logging
├── actions/
│   ├── product-actions.php              # Product CRUD Handler (switch, dual validation, PDO)
│   └── quote-actions.php                # Quote Submission & Workflow Handler
├── includes/
│   ├── head.php                         # HTML <head>, Dynamic CSS Loader
│   ├── topbar.php                       # Role-aware Topbar (user greeting, status)
│   ├── navbar.php                       # Responsive Navbar & Mobile Navigation Drawer
│   ├── footer.php                       # Global Footer & Dynamic JS Loader
│   └── quote-modal.php                  # Multi-step Quotation Inquiry Modal Partial
├── pages/
│   ├── login.php                        # Portal Authentication Page
│   ├── dashboard.php                    # Role-Based Executive KPI Dashboard
│   ├── products.php                     # Full CRUD Product Catalog Management
│   ├── product-form.php                 # Add / Edit Product with Dual Validation
│   ├── quotes.php                       # Customer Quote Inquiries & Workflow Management
│   └── reports.php                      # Executive Summary Reports & SQL Analytics
├── index.php                            # Public Homepage & Promotional Showcase
├── category-page.php                    # Public Category Catalog Browser
├── product/
│   └── car-charger-adapters.php         # Product Variant Detail Showcase
├── assets/
│   ├── css/
│   │   ├── global.css                   # Shared Design Tokens, Navbar, Footer, Modals, Alerts
│   │   ├── home.css                     # Dedicated CSS: Homepage & Carousel
│   │   ├── login.css                    # Dedicated CSS: Login Portal
│   │   ├── dashboard.css                # Dedicated CSS: KPI Cards & Activity Timeline
│   │   ├── products.css                 # Dedicated CSS: Products Data Table & Filters
│   │   ├── product-form.css             # Dedicated CSS: Product Form & Live Preview
│   │   ├── quotes.css                   # Dedicated CSS: Quote Tabs & Detail Modal
│   │   ├── reports.css                  # Dedicated CSS: Reports, Progress Bars, Print Stylesheet
│   │   ├── category-page.css            # Dedicated CSS: Public Category Browser
│   │   ├── car-charger-adapters.css     # Dedicated CSS: Product Variant Page
│   │   └── quote-modal.css              # Dedicated CSS: Quote Modal Multi-Step Form
│   ├── js/
│   │   ├── global.js                    # Shared JS: Mobile Drawer, Modal Controller, Tooltips
│   │   ├── home.js                      # Dedicated JS: Carousel Timer & Card Navigation
│   │   ├── login.js                     # Dedicated JS: Client Validation & 1-Click Autofill
│   │   ├── dashboard.js                 # Dedicated JS: Real-Time Activity Log Filter
│   │   ├── products.js                  # Dedicated JS: Live Search & Delete Confirmation Modal
│   │   ├── product-form.js              # Dedicated JS: Form Validation & Dependent SKU/Specs
│   │   ├── quotes.js                    # Dedicated JS: Live Lead Filter & View Detail Modal
│   │   ├── reports.js                   # Dedicated JS: Print Report & CSV Export
│   │   ├── category-page.js             # Dedicated JS: Public Catalog Controller
│   │   ├── car-charger-adapters.js      # Dedicated JS: Variant Controller
│   │   └── quote-modal.js               # Dedicated JS: Multi-Step Quote Submission
│   └── images/
│       ├── logo.png                     # Standardized Brand Logo
│       └── [30+ Product Images]         # High-resolution merchandise photos & banners
├── database/
│   └── creativekit3a.sql                # THE single database: 7 related tables, seed data & SQL demos
├── _trash/                              # Deprecated / Archived Files (Ignored by Git)
├── .gitignore                           # Git Exclusion Rules
└── README.md                            # Comprehensive Setup & Defense Documentation
```

---

## 2. Local Setup & Installation (XAMPP)

### Step 1: Place the Project in XAMPP
Ensure the project is located inside your XAMPP web root directory:
- **macOS:** `/Applications/XAMPP/xamppfiles/htdocs/creativekit3a`
- **Windows:** `C:\xampp\htdocs\creativekit3a`

### Step 2: Start Apache & MySQL
Launch the **XAMPP Control Panel** and start both **Apache** and **MySQL**.

### Step 3: Import the Database
1. Open your browser and navigate to **phpMyAdmin**:
   ```text
   http://localhost/phpmyadmin/
   ```
2. Click on the **Import** tab at the top.
3. Choose the SQL file located at:
   ```text
   database/creativekit3a.sql
   ```
4. Click **Go** (or **Import**).
5. The script will automatically create `creativekit3a_db`, establish all 7 related tables with Primary/Foreign Key constraints, insert all sample seed data, and run the SQL demonstration queries.

> **One database only.** `database/creativekit3a.sql` is the only SQL file. Do not create any other database — every page connects through `config/db.php` to `creativekit3a_db`.

### Step 4: Open the Application
Navigate to the storefront in your web browser:
```text
http://localhost/creativekit3a/
```

---

## 3. Database Design (Single MySQL Database: `creativekit3a_db`)

| # | Table | Primary Key | Foreign Keys | Used by |
| :- | :--- | :--- | :--- | :--- |
| 1 | `users` | `id` | — | Login, RBAC, dashboard, reports |
| 2 | `categories` | `id` | `parent_id → categories.id` (subcategories) | Catalog, quote modal, product form (SKU prefix / spec hints), reports |
| 3 | `products` | `id` | `category_id → categories.id`, `created_by → users.id` | Homepage, catalog, quote modal, product CRUD (image & badge pickers), reports |
| 4 | `product_variants` | `id` | `product_id → products.id` | Product items page |
| 5 | `quote_requests` | `id` | `product_id → products.id`, `assigned_to → users.id` | Quote modal, quotes page, dashboard, reports |
| 6 | `activity_log` | `id` | `user_id → users.id` | Audit trail on dashboard |
| 7 | `hero_banners` | `id` | `product_id → products.id` | Homepage carousel |

### SQL requirement coverage

| Requirement | In `creativekit3a.sql` (demo section) | Live in the app |
| :--- | :--- | :--- |
| `INSERT` | Demo 1 | `actions/product-actions.php` (create), `actions/quote-actions.php` (submit), activity logging |
| `SELECT` / `WHERE` | Demo 2 | Every page — e.g. `pages/dashboard.php` KPI cards |
| `UPDATE` | Demo 3 | Product edit, quote status/assignment, `auth/login.php` (last login) |
| `DELETE` | Demo 4 | Product delete, quote delete (admin only) |
| `LIKE` | Demo 5 | Search on `pages/products.php` and `pages/quotes.php` |
| `ORDER BY` | Demos 5–9 | Sorting on products page, catalog, reports |
| `INNER JOIN` | Demo 6 | `index.php`, `category-page.php`, `pages/reports.php` (Active Catalog) |
| `LEFT JOIN` | Demo 7 | `pages/quotes.php`, `pages/dashboard.php`, `pages/reports.php` (Category Breakdown) |
| `RIGHT JOIN` | Demo 8 | `pages/reports.php` (Staff Assignments) |
| `AS` | All demos | Column and table aliases throughout |
| `CONCAT()` | Demos 6–9 | Staff full names, product labels, variant price labels |

**No product data is hardcoded.** Every product, variant, category, carousel slide, price and image — on the homepage, catalog (`category-page.php`), product items page (`product/car-charger-adapters.php`), quote modal and admin product form — is queried from this database. The JavaScript files only render data the PHP page passes in. To add or change items, add/edit rows (e.g. via the admin Products page or phpMyAdmin).

## 4. Main UI & Images

The storefront uses the original **CreativeKit3A Main UI** (`assets/css/style.css`): dark top bar with **Sign In / Register**, white header with search, **Request a Quote** and **Cart**, the 8-category **mega menu** (built from the database), the sliding **hero carousel** (arrows, dots, autoplay), and **OUR PRODUCTS**. Admin pages share the same header, colors and buttons.

| Folder | Contents | Used by |
| :--- | :--- | :--- |
| `assets/images/hero/` | Carousel slides (`slide-1-custom-corporate-jackets.png`, …) | `hero_banners.image` |
| `assets/images/items/` | One file per product and per variant (`<product-slug>.png`, `<product-slug>--<variant>.png`) | `products.image`, `product_variants.image` |
| `assets/images/categories/` | One banner per main category (`<category-slug>.png`) | `categories.image` |
| `assets/images/placeholder.png` | Shown automatically if any image fails to load (`onerror` handler in `assets/js/global.js`) | every page |

**Staff portal (admin UI):** every page in `pages/` (Dashboard, Products, Product form, Quotes, Reports) uses a separate admin layout — a fixed dark sidebar with navigation and a live "new quotes" badge, plus a slim top header with product search and the signed-in user. It is built by `includes/admin-layout.php` / `includes/admin-footer.php` with `assets/css/admin.css` and `assets/js/admin.js`. The Dashboard adds a **Quote Pipeline** chart (requests per status from a `GROUP BY` query).

**To use your real photos:** overwrite a file with your photo using the **same file name** — no code or database change needed.

## 5. Demo Credentials for Defense

The system includes two distinct roles demonstrating **Role-Based Access Control (RBAC)**:

| Role | Email Address | Password | Permissions & Capabilities |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@creativekit3a.com` | `admin123` | Full CRUD, Delete Products (with confirmation modal), Assign Quote Leads, System Audit Trail, Summary Reports |
| **Staff Member** | `maria@creativekit3a.com` | `staff123` | View & Add Products, Edit Products, Update Quote Workflow Statuses (Delete is blocked) |
| **Customer** | *(create one with the top-bar **Register** button)* | — | Browse the catalog and submit quote requests; blocked from the staff portal |

*(Note: The login page at `/pages/login.php` features **1-Click Autofill buttons** for both roles to make testing seamless during defense).*
=======

>>>>>>> refs/remotes/origin/main
