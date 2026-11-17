<?php
/**
 * product/car-charger-adapters.php — Items / Variants Page
 * Converted from car-charger-adapters.html (was items-page.html).
 *
 * This is a TEMPLATE for any product variant page.
 * To create a new product page (e.g. mobile-powerbanks.php):
 *   1. Copy this file.
 *   2. Update $title, $meta_desc, and $extra_js.
 *   3. The JS file (car-charger-adapters.js) already contains
 *      all product data — just make sure the URL params match:
 *      ?cat=accessories-gadgets&subcat=car&item=car-charger-adapter
 *
 * URL example:
 *   /product/car-charger-adapters?cat=accessories-gadgets&subcat=car&item=car-charger-adapter
 */

$title      = 'Car Charger Adapters | CreativeKit3A';
$meta_desc  = 'Custom-branded car charger adapters — single, dual, fast-charge, and wireless variants. Ideal for corporate giveaways.';
$extra_css  = ['/assets/css/car-charger-adapters.css'];
$active_nav = 'accessories-gadgets';

include __DIR__ . '/../includes/head.php';
?>
<body>
<?php
include __DIR__ . '/../includes/topbar.php';
include __DIR__ . '/../includes/navbar.php';
?>

  <!-- ============================================================
       BREADCRUMB BAR
       JS fills in: Home > Category > Subcategory > Item Name
       ============================================================ -->
  <div class="items-breadcrumb-bar">
    <div class="container">
      <nav class="items-breadcrumb" aria-label="Breadcrumb">
        <a href="/"><i class="fas fa-home"></i> Home</a>
        <span class="sep"><i class="fas fa-chevron-right"></i></span>
        <a href="#" id="bc-category-link" class="bc-cat">Accessories &amp; Gadgets</a>
        <span class="sep"><i class="fas fa-chevron-right"></i></span>
        <a href="#" id="bc-subcat-link" class="bc-subcat">Car Accessories</a>
        <span class="sep"><i class="fas fa-chevron-right"></i></span>
        <span class="current" id="bc-item-name">Car Charger Adapters</span>
      </nav>
    </div>
  </div>

  <!-- ============================================================
       ITEM HERO BANNER
       JS populates all text & image content from ITEMS_DATA.
       ============================================================ -->
  <section class="item-hero" id="item-hero">
    <div class="container item-hero-inner">

      <div class="item-hero-text">
        <span class="item-hero-eyebrow" id="item-eyebrow">Car Accessories</span>
        <h1 class="item-hero-title" id="item-hero-title">Car Charger Adapters</h1>
        <p class="item-hero-desc" id="item-hero-desc">
          Custom-branded car charger adapters perfect for corporate giveaways, events, and daily use.
        </p>
        <div class="item-hero-meta">
          <span class="item-hero-price" id="item-hero-price">As low as ₱85.00</span>
          <button class="btn btn-primary item-hero-cta" id="item-hero-cta">
            <i class="fas fa-paint-brush"></i> Request a Quote
          </button>
        </div>
      </div>

      <div class="item-hero-img-wrap">
        <img class="item-hero-img" id="item-hero-img"
             src="/assets/images/car-charger-adapters.png"
             alt="Car Charger Adapters">
      </div>

    </div>
  </section>

  <!-- ============================================================
       VARIANT FILTER TABS (injected by JS)
       ============================================================ -->
  <div class="item-variants-tabs-wrapper" id="item-variants-tabs-wrapper">
    <div class="container" id="item-variants-tabs-container">
      <!-- Tabs injected by car-charger-adapters.js -->
    </div>
  </div>

  <!-- ============================================================
       ITEMS LISTING
       ============================================================ -->
  <section class="items-listing-section">
    <div class="container">

      <!-- Sort / results bar -->
      <div class="items-sort-bar">
        <div class="items-sort-left">
          Showing <strong id="items-showing-count">all</strong> variants of
          <strong id="items-showing-name">Car Charger Adapters</strong>
        </div>
        <div class="items-sort-right">
          <label for="items-sort-select"
                 style="font-size:0.8rem;color:var(--grey-mid);font-weight:600;">Sort by:</label>
          <select class="sort-select" id="items-sort-select" aria-label="Sort items">
            <option value="default">Default</option>
            <option value="name-asc">Name A–Z</option>
            <option value="name-desc">Name Z–A</option>
            <option value="price-asc">Price: Low to High</option>
            <option value="price-desc">Price: High to Low</option>
          </select>
        </div>
      </div>

      <!-- Items grid (JS renders cards here) -->
      <div class="items-grid" id="items-grid">
        <!-- Item cards injected by car-charger-adapters.js -->
      </div>

    </div>
  </section>

  <!-- ============================================================
       RELATED PRODUCTS (JS populates from SIBLING_DATA)
       ============================================================ -->
  <section class="related-section" id="related-section">
    <div class="container">
      <h2 class="related-heading">You May Also Like</h2>
      <div class="related-grid" id="related-grid">
        <!-- Related cards injected by JS -->
      </div>
    </div>
  </section>

<?php
$extra_js = ['/assets/js/car-charger-adapters.js'];
include __DIR__ . '/../includes/footer.php';
?>
