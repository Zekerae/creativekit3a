<<<<<<< HEAD

=======
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
│   └── creativekit3a.sql                # 5 Normalized Tables with Seed Data & Verified Hashes
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
5. The script will automatically create `creativekit3a_db`, establish all 5 normalized tables with Foreign Key constraints, and insert all sample seed data.

### Step 4: Open the Application
Navigate to the storefront in your web browser:
```text
http://localhost/creativekit3a/
```

---
>>>>>>> 60fed33 (feat: complete full-stack upgrade with modular per-page architecture, RBAC, CRUD, and advanced reports)

## 3. Demo Credentials for Defense

The system includes two distinct roles demonstrating **Role-Based Access Control (RBAC)**:

| Role | Email Address | Password | Permissions & Capabilities |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@creativekit3a.com` | `admin123` | Full CRUD, Delete Products (with confirmation modal), Assign Quote Leads, System Audit Trail, Summary Reports |
| **Staff Member** | `maria@creativekit3a.com` | `staff123` | View & Add Products, Edit Products, Update Quote Workflow Statuses (Delete is blocked) |

<<<<<<< HEAD
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
All rights reserved © CreativeKit 3A.
=======
*(Note: The login page at `/pages/login.php` features **1-Click Autofill buttons** for both roles to make testing seamless during defense).*
>>>>>>> 60fed33 (feat: complete full-stack upgrade with modular per-page architecture, RBAC, CRUD, and advanced reports)
