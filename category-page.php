<?php
/**
 * category-page.php — Public Product Category Page (database-driven)
 *
 * All categories, subcategories and products are read from the single
 * creativekit3a_db database through config/db.php, then handed to
 * category-page.js (which only renders — it holds no hardcoded data).
 *
 * URL examples:
 *   /category-page.php                      → Accessories & Gadgets (default)
 *   /category-page.php?cat=drinkware        → Drinkware
 *   /category-page.php?cat=pu-leather       → PU Leather
 *
 * SQL used: SELECT, WHERE, ORDER BY, AS, INNER JOIN, LEFT JOIN
 */

require_once __DIR__ . '/config/db.php';

$active_slug   = trim($_GET['cat'] ?? '');
$category_data = [];   // slug => { name, icon, desc, count, subcats[], products[] }

if ($pdo) {
    try {
        // 1. Main categories (parent_id IS NULL)
        $main = $pdo->query(
            "SELECT id, name, slug, description, icon
             FROM categories
             WHERE parent_id IS NULL AND is_active = 1
             ORDER BY sort_order ASC, name ASC"
        )->fetchAll();

        // 2. Subcategories joined to their parent (INNER JOIN on self-reference)
        $subs = $pdo->query(
            "SELECT sc.id, sc.name AS label, sc.slug AS sub_slug, mc.slug AS parent_slug
             FROM categories AS sc
             INNER JOIN categories AS mc ON sc.parent_id = mc.id
             WHERE sc.is_active = 1
             ORDER BY mc.sort_order ASC, sc.sort_order ASC"
        )->fetchAll();

        // 3. Active products with their subcategory, parent and variant count
        //    (LEFT JOIN keeps products that have no variants)
        $products = $pdo->query(
            "SELECT p.id, p.name, p.slug, p.price_min, p.image,
                    sc.slug AS sub_slug,
                    mc.slug AS parent_slug,
                    COUNT(v.id) AS variant_count
             FROM products AS p
             INNER JOIN categories AS sc ON p.category_id = sc.id
             INNER JOIN categories AS mc ON sc.parent_id  = mc.id
             LEFT JOIN product_variants AS v ON v.product_id = p.id
             WHERE p.status = 'active'
             GROUP BY p.id, p.name, p.slug, p.price_min, p.image, sc.slug, mc.slug
             ORDER BY sc.sort_order ASC, p.id ASC"
        )->fetchAll();

        foreach ($main as $m) {
            $category_data[$m['slug']] = [
                'name'     => $m['name'],
                'icon'     => 'fas ' . $m['icon'],
                'desc'     => $m['description'] ?? '',
                'count'    => '0',
                'subcats'  => [],
                'products' => [],
            ];
        }
        foreach ($subs as $s) {
            if (isset($category_data[$s['parent_slug']])) {
                $category_data[$s['parent_slug']]['subcats'][] = ['key' => $s['sub_slug'], 'label' => $s['label']];
            }
        }
        foreach ($products as $p) {
            if (!isset($category_data[$p['parent_slug']])) continue;
            $url = ((int)$p['variant_count'] > 0)
                ? '/product/car-charger-adapters.php?' . http_build_query([
                      'cat' => $p['parent_slug'], 'subcat' => $p['sub_slug'], 'item' => $p['slug'],
                  ])
                : '';
            $category_data[$p['parent_slug']]['products'][] = [
                'id'     => (int)$p['id'],
                'name'   => $p['name'],
                'price'  => ((float)$p['price_min'] > 0) ? 'As low as ₱' . number_format((float)$p['price_min'], 2) : 'Ask for Quote',
                'img'    => $p['image'] ?: '/assets/images/1.png',
                'url'    => $url,
                'subcat' => $p['sub_slug'],
            ];
        }
        foreach ($category_data as &$c) {
            $c['count'] = (string)count($c['products']);
        }
        unset($c);
    } catch (PDOException $e) {
        $category_data = [];
    }
}

if (!isset($category_data[$active_slug])) {
    $active_slug = array_key_first($category_data) ?? '';
}
$active_cat = $category_data[$active_slug] ?? null;

$title      = ($active_cat ? $active_cat['name'] . ' | ' : 'Product Categories | ') . 'CreativeKit3A';
$meta_desc  = 'Browse our complete collection of corporate giveaways — accessories, apparel, bags, drinkware, and more.';
$extra_css  = ['/assets/css/category-page.css'];
$active_nav = $active_slug;

include __DIR__ . '/includes/head.php';
?>
<body>
<?php
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';
?>

  <!-- ============================================================
       BREADCRUMB BAR
       ============================================================ -->
  <div class="cat-breadcrumb-bar">
    <div class="container">
      <nav class="cat-breadcrumb" aria-label="Breadcrumb">
        <a href="/"><i class="fas fa-home"></i> Home</a>
        <span class="sep"><i class="fas fa-chevron-right"></i></span>
        <span class="current" id="hero-breadcrumb-current"><?= e($active_cat['name'] ?? 'Categories') ?></span>
      </nav>
    </div>
  </div>

  <!-- Hidden spans used by JS to push data into the DOM -->
  <span id="hero-title"         style="display:none;"></span>
  <span id="hero-desc"          style="display:none;"></span>
  <span id="hero-icon"          style="display:none;"></span>
  <span id="hero-product-count" style="display:none;"></span>

  <?php if (!$pdo): ?>
    <div class="container" style="padding:24px 0;">
      <div class="alert alert-warning">
        <i class="fas fa-database"></i> The catalog database is not connected. Import <code>database/creativekit3a.sql</code> and check <code>config/db.php</code>.
      </div>
    </div>
  <?php endif; ?>

  <!-- ============================================================
       MAIN CATEGORY SWITCHER (from categories table)
       ============================================================ -->
  <?php if (!empty($category_data)): ?>
  <div class="subcat-tabs-wrapper">
    <div class="container">
      <?php foreach ($category_data as $slug => $c): ?>
        <a class="subcat-tab<?= $slug === $active_slug ? ' active' : '' ?>"
           href="/category-page.php?cat=<?= urlencode($slug) ?>">
          <i class="<?= e($c['icon']) ?>"></i> <?= e($c['name']) ?>
          <span class="tab-count"><?= e($c['count']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

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

  <!-- Catalog data from MySQL (creativekit3a_db) for category-page.js -->
  <script>
    window.CATEGORY_DATA   = <?= json_encode($category_data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.ACTIVE_CATEGORY = <?= json_encode($active_slug, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  </script>

<?php
$extra_js = ['/assets/js/category-page.js'];
include __DIR__ . '/includes/footer.php';
?>
