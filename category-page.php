<?php
/**
 * category-page.php — Product Category Page
 * Converted from category-page.html.
 *
 * Serves all 8 categories from one file.
 * The active category is detected from the URL by the inline JS
 * (same logic as the original), so no PHP URL parsing is needed.
 *
 * URL examples:
 *   /product-category/accessories-gadgets  → Accessories & Gadgets
 *   /product-category/drinkware            → Drinkware
 *   /product-category-pu-leather/          → PU Leather
 */

$title      = 'Product Categories | CreativeKit3A';
$meta_desc  = 'Browse our complete collection of corporate giveaways — accessories, apparel, bags, drinkware, and more.';
$extra_css  = ['/assets/css/category-page.css'];
$active_nav = 'accessories-gadgets';  // highlights the correct mega-menu item (update per route if needed)

include __DIR__ . '/includes/head.php';
?>
<body>
<?php
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';
?>

  <!-- ============================================================
       BREADCRUMB BAR
       JS (renderHero) writes the active category name into #hero-breadcrumb-current
       ============================================================ -->
  <div class="cat-breadcrumb-bar">
    <div class="container">
      <nav class="cat-breadcrumb" aria-label="Breadcrumb">
        <a href="/"><i class="fas fa-home"></i> Home</a>
        <span class="sep"><i class="fas fa-chevron-right"></i></span>
        <span class="current" id="hero-breadcrumb-current">Categories</span>
      </nav>
    </div>
  </div>

  <!-- ============================================================
       CATEGORY HERO BANNER
       ============================================================ -->
  <section class="cat-hero" aria-labelledby="hero-title">
    <div class="container cat-hero-inner">
      <div class="cat-hero-left">
        <div class="cat-hero-icon-box" id="hero-icon" aria-hidden="true">
          <i class="fas fa-mobile-alt"></i>
        </div>
        <div>
          <h1 class="cat-hero-title">
            <span id="hero-title">Accessories &amp; Gadgets</span>
            <span class="cat-hero-count" id="hero-product-count">25+</span>
          </h1>
          <p class="cat-hero-desc" id="hero-desc">
            Browse our full range of custom accessories, gadgets, tech items, and desk essentials.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       SUBCATEGORY FILTER TABS (injected by JS)
       ============================================================ -->
  <div class="subcat-tabs-wrapper" id="subcat-tabs-wrapper">
    <div class="container" id="subcat-tabs-container">
      <!-- Tabs injected by JS -->
    </div>
  </div>

  <!-- ============================================================
       PRODUCT LISTING
       ============================================================ -->
  <section class="cat-listing-section">
    <div class="container">

      <!-- Sort bar -->
      <div class="sort-bar">
        <div class="sort-bar-left">
          Showing <strong id="showing-count">all</strong> products
          <span id="showing-in"> in <strong id="showing-subcat">all categories</strong></span>
        </div>
        <div class="sort-bar-right">
          <label for="sort-select" style="font-size:0.8rem;color:var(--grey-mid);font-weight:600;">Sort by:</label>
          <select class="sort-select" id="sort-select" aria-label="Sort products">
            <option value="default">Default</option>
            <option value="name-asc">Name A–Z</option>
            <option value="name-desc">Name Z–A</option>
          </select>
        </div>
      </div>

      <!-- Product grid (JS renders cards here) -->
      <div class="cat-product-grid" id="cat-product-grid">
        <!-- Products rendered by JS based on active tab -->
      </div>

    </div>
  </section>

<?php
// Pass category-page.js as a page-specific script
$extra_js = ['/assets/js/category-page.js'];
include __DIR__ . '/includes/footer.php';
?>
