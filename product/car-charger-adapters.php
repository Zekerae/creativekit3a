<?php
/**
 * product/car-charger-adapters.php — Product Items / Variants Page (database-driven)
 *
 * One template serves every product that has rows in product_variants.
 * The product, its variants and related products are read from the single
 * creativekit3a_db database through config/db.php; car-charger-adapters.js
 * only renders what this file prints into window.ITEMS_DATA / SIBLING_DATA.
 *
 * URL example:
 *   /product/car-charger-adapters.php?cat=accessories-gadgets&subcat=car-accessories&item=car-charger-adapter
 *
 * SQL used: SELECT, WHERE, ORDER BY, AS, INNER JOIN, LEFT JOIN, CONCAT()
 */

require_once __DIR__ . '/../config/db.php';

$item_slug    = trim($_GET['item'] ?? 'car-charger-adapter');
$items_data   = [];   // { catSlug: { itemSlug: { name, eyebrow, price, heroImg, desc, catName, variantTypes[], items[] } } }
$sibling_data = [];   // { catSlug: [ { name, price, img, url } ] }
$product      = null;

/** Format a DECIMAL price the way the storefront shows it */
function ck_price_label(float $price): string {
    return $price > 0 ? 'As low as ₱' . number_format($price, 2) : 'Ask for Quote';
}

if ($pdo) {
    try {
        // 1. The product with its subcategory and main category
        //    (INNER JOIN to subcategory, LEFT JOIN to parent in case it is top-level)
        $stmt = $pdo->prepare(
            "SELECT p.id, p.name, p.slug, p.description, p.price_min, p.image,
                    sc.name AS subcat_name, sc.slug AS subcat_slug,
                    COALESCE(mc.slug, sc.slug) AS cat_slug,
                    COALESCE(mc.name, sc.name) AS cat_name,
                    COALESCE(mc.id,   sc.id)   AS cat_id
             FROM products AS p
             INNER JOIN categories AS sc ON p.category_id = sc.id
             LEFT JOIN categories  AS mc ON sc.parent_id  = mc.id
             WHERE p.slug = :slug AND p.status = 'active'
             LIMIT 1"
        );
        $stmt->execute([':slug' => $item_slug]);
        $product = $stmt->fetch() ?: null;

        if ($product) {
            // 2. Variants of this product (CONCAT builds the price label)
            $vstmt = $pdo->prepare(
                "SELECT v.id, v.name, v.specs, v.price, v.image, v.badge,
                        v.variant_type, v.variant_label,
                        CONCAT('As low as ₱', FORMAT(v.price, 2)) AS price_label
                 FROM product_variants AS v
                 WHERE v.product_id = :pid
                 ORDER BY v.type_order ASC, v.sort_order ASC, v.id ASC"
            );
            $vstmt->execute([':pid' => $product['id']]);
            $variants = $vstmt->fetchAll();

            $variant_types = [];
            $items = [];
            foreach ($variants as $v) {
                if ($v['variant_type'] && !isset($variant_types[$v['variant_type']])) {
                    $variant_types[$v['variant_type']] = [
                        'key'   => $v['variant_type'],
                        'label' => $v['variant_label'] ?: $v['variant_type'],
                    ];
                }
                $items[] = [
                    'id'          => (int)$v['id'],
                    'name'        => $v['name'],
                    'specs'       => $v['specs'] ?? '',
                    'price'       => ((float)$v['price'] > 0) ? $v['price_label'] : 'Ask for Quote',
                    'priceValue'  => (float)$v['price'],
                    'img'         => url($v['image'] ?: '/assets/images/placeholder.png'),
                    'badge'       => $v['badge'] ?? '',
                    'variantType' => $v['variant_type'] ?? '',
                ];
            }

            $items_data[$product['cat_slug']][$product['slug']] = [
                'id'           => (int)$product['id'],
                'name'         => $product['name'],
                'eyebrow'      => $product['subcat_name'],
                'catName'      => $product['cat_name'],
                'price'        => ck_price_label((float)$product['price_min']),
                'heroImg'      => url($product['image'] ?: '/assets/images/placeholder.png'),
                'desc'         => $product['description'] ?? '',
                'variantTypes' => array_values($variant_types),
                'items'        => $items,
            ];

            // 3. Related products: other active products in the same main category
            //    (LEFT JOIN + COUNT so products with variants are listed first)
            $rstmt = $pdo->prepare(
                "SELECT p.name, p.slug, p.price_min, p.image, sc.slug AS subcat_slug,
                        COUNT(v.id) AS variant_count
                 FROM products AS p
                 INNER JOIN categories AS sc ON p.category_id = sc.id
                 LEFT JOIN product_variants AS v ON v.product_id = p.id
                 WHERE (sc.parent_id = :cat_id OR sc.id = :cat_id2)
                   AND p.status = 'active'
                   AND p.id <> :pid
                 GROUP BY p.id, p.name, p.slug, p.price_min, p.image, sc.slug
                 ORDER BY variant_count DESC, p.name ASC
                 LIMIT 4"
            );
            $rstmt->execute([':cat_id' => $product['cat_id'], ':cat_id2' => $product['cat_id'], ':pid' => $product['id']]);
            foreach ($rstmt->fetchAll() as $r) {
                $sibling_data[$product['cat_slug']][] = [
                    'name' => $r['name'],
                    'slug' => $r['slug'],
                    'price'=> ck_price_label((float)$r['price_min']),
                    'img'  => url($r['image'] ?: '/assets/images/placeholder.png'),
                    'url'  => url(((int)$r['variant_count'] > 0)
                        ? '/product/car-charger-adapters.php?' . http_build_query([
                              'cat' => $product['cat_slug'], 'subcat' => $r['subcat_slug'], 'item' => $r['slug'],
                          ])
                        : '/category-page.php?cat=' . urlencode($product['cat_slug'])),
                ];
            }
        }
    } catch (PDOException $e) {
        $product = null;
    }
}

// Unknown / inactive product → back to the catalog
if (!$product) {
    header('Location: ' . BASE_URL . '/category-page.php');
    exit;
}

$title      = $product['name'] . ' | CreativeKit3A';
$meta_desc  = $product['description'] ?: 'Custom-branded corporate giveaways from CreativeKit3A.';
$extra_css  = ['/assets/css/car-charger-adapters.css'];
$active_nav = $product['cat_slug'];

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
        <a href="<?= BASE_URL ?>/"><i class="fas fa-home"></i> Home</a>
        <span class="sep"><i class="fas fa-chevron-right"></i></span>
        <a href="<?= BASE_URL ?>/category-page.php?cat=<?= urlencode($product['cat_slug']) ?>" id="bc-category-link" class="bc-cat"><?= e($product['cat_name']) ?></a>
        <span class="sep"><i class="fas fa-chevron-right"></i></span>
        <a href="<?= BASE_URL ?>/category-page.php?cat=<?= urlencode($product['cat_slug']) ?>" id="bc-subcat-link" class="bc-subcat"><?= e($product['subcat_name']) ?></a>
        <span class="sep"><i class="fas fa-chevron-right"></i></span>
        <span class="current" id="bc-item-name"><?= e($product['name']) ?></span>
      </nav>
    </div>
  </div>

  <!-- ============================================================
       ITEM HERO BANNER
       Rendered from the products table (JS re-applies the same ITEMS_DATA).
       ============================================================ -->
  <section class="item-hero" id="item-hero">
    <div class="container item-hero-inner">

      <div class="item-hero-text">
        <span class="item-hero-eyebrow" id="item-eyebrow"><?= e($product['subcat_name']) ?></span>
        <h1 class="item-hero-title" id="item-hero-title"><?= e($product['name']) ?></h1>
        <p class="item-hero-desc" id="item-hero-desc"><?= e($product['description']) ?></p>
        <div class="item-hero-meta">
          <span class="item-hero-price" id="item-hero-price"><?= e(ck_price_label((float)$product['price_min'])) ?></span>
          <button class="btn btn-primary item-hero-cta" id="item-hero-cta">
            <i class="fas fa-paint-brush"></i> Request a Quote
          </button>
        </div>
      </div>

      <div class="item-hero-img-wrap">
        <img class="item-hero-img" id="item-hero-img"
             src="<?= e(url($product['image'] ?: '/assets/images/placeholder.png')) ?>"
             alt="<?= e($product['name']) ?>">
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
          <strong id="items-showing-name"><?= e($product['name']) ?></strong>
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

  <!-- Item data from MySQL (creativekit3a_db) for car-charger-adapters.js -->
  <script>
    window.ITEMS_DATA   = <?= json_encode($items_data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.SIBLING_DATA = <?= json_encode($sibling_data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  </script>

<?php
$extra_js = ['/assets/js/car-charger-adapters.js'];
include __DIR__ . '/../includes/footer.php';
?>
