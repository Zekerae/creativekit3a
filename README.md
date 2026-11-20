# CreativeKit 3A — Corporate Giveaways & Custom Merch

CreativeKit 3A is a corporate giveaways and promotional merchandise web platform. It is engineered with dual-deployment compatibility:
- **Static Deployment:** Ready out-of-the-box for **GitHub Pages**, **VS Code Live Server**, and **Vercel**.
- **Dynamic PHP / MySQL Stack:** Full support for local **XAMPP / Apache** web servers with SQL database schemas.

---

## 🚀 Instant Deployment Options

### 1. GitHub Pages
1. Push your repository to GitHub.
2. In your repository, go to **Settings** > **Pages**.
3. Under **Build and deployment**:
   - **Source:** *Deploy from a branch*
   - **Branch:** `main` / `root`
4. Click **Save**. Within 1–2 minutes, your website is live at `https://<username>.github.io/<repo>/`!

### 2. VS Code Live Server
1. Open this repository in VS Code.
2. Install the **Live Server** extension if you haven't already.
3. Right-click on [index.html](file:///c:/Users/Ree/Documents/GitHub/creativekit3a/index.html) and select **"Open with Live Server"**.
4. The site will launch automatically at `http://127.0.0.1:5500/index.html`.

### 3. Vercel
1. Push this repository to GitHub.
2. Log in to [Vercel](https://vercel.com) and click **"Add New Project"**.
3. Select your GitHub repository.
4. Framework Preset: **Other** (leave Root Directory as `./`).
5. Click **Deploy**. Vercel will automatically read [vercel.json](file:///c:/Users/Ree/Documents/GitHub/creativekit3a/vercel.json) and serve all static pages with clean URLs along with serverless API handlers in `api/`!

---

## 💻 Local Setup with XAMPP (PHP / MySQL)

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (Apache & MySQL, PHP 8+)
- Git

### Installation
1. Move or clone the project folder into your web server root:
   - macOS: `/Applications/XAMPP/xamppfiles/htdocs/creativekit3a`
   - Windows: `C:\xampp\htdocs\creativekit3a`

2. **Database Setup:**
   - Start Apache and MySQL from XAMPP Control Panel.
   - Open phpMyAdmin (`http://localhost/phpmyadmin/`).
   - Create a database named `creativekit3a`.
   - Import [database/schema.sql](file:///c:/Users/Ree/Documents/GitHub/creativekit3a/database/schema.sql), then [database/items-page.sql](file:///c:/Users/Ree/Documents/GitHub/creativekit3a/database/items-page.sql).

3. **Accessing the Site:**
   - Dynamic PHP: `http://localhost/creativekit3a/`
   - Static HTML: `http://localhost/creativekit3a/index.html`

---

## 📂 Project Structure

```text
creativekit3a/
├── index.html                   # Root static homepage (GitHub Pages / Vercel)
├── index.php                    # Dynamic homepage (XAMPP / PHP)
├── category-page.html           # Static category catalog page
├── category-page.php            # Dynamic category catalog page
├── car-charger-adapters.html    # Static product variant page
├── vercel.json                  # Vercel routing & clean URLs config
├── .vercelignore                # Excludes PHP files from Vercel builds
├── api/                         # Vercel Serverless Functions (Node.js)
│   ├── quote.js                 # Multi-step Quote Request endpoint
│   ├── contact.js               # Contact form submission endpoint
│   └── auth/                    # Login & Register endpoints
├── api-php/                     # PHP Backend APIs for XAMPP / Apache
│   ├── quote.php                # Multi-step Quote Request PHP handler
│   ├── contact.php              # Contact form submission PHP handler
│   └── auth/                    # Login & Register PHP handlers
├── assets/
│   ├── css/                     # style.css, category-page.css, quote-modal.css, car-charger-adapters.css
│   ├── js/                      # main.js, category-page.js, quote-modal.js, car-charger-adapters.js
│   └── images/                  # Product imagery & brand assets
├── database/                    # schema.sql and items-page.sql
├── includes/                    # PHP partials for XAMPP
└── product/                     # Legacy PHP/HTML variant routes
```

---

## 📄 License
All rights reserved © CreativeKit 3A.
