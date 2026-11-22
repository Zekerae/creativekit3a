<?php
/**
 * index.php — Homepage (CreativeKit3A Main UI)
 * Hero carousel, OUR PRODUCTS, corporate profile and contact sections.
 * Slides and products come from the hero_banners / products tables.
 *
 * Dedicated Assets:
 *   - CSS: /assets/css/home.css
 *   - JS:  /assets/js/home.js  (carousel: arrows, dots, autoplay, swipe)
 */

require_once __DIR__ . '/config/db.php';

$title     = 'Corporate Giveaways | CreativeKit3A';
$meta_desc = 'Customized corporate giveaways, branded accessories, and promotional items in the Philippines. CreativeKit3A — your trusted partner.';
$extra_css = ['/assets/css/home.css'];
$extra_js  = ['/assets/js/home.js'];

$featured_products = [];
$hero_banners      = [];
if ($pdo) {
    try {
        // OUR PRODUCTS: featured active products
        // (INNER JOIN subcategory, LEFT JOIN parent category + variants, AS aliases)
        $featured_products = $pdo->query(
            "SELECT p.id, p.name, p.slug, p.price_min, p.image,
                    c.slug AS subcat_slug,
                    COALESCE(mc.slug, c.slug) AS cat_slug,
                    COUNT(v.id) AS variant_count
             FROM products AS p
             INNER JOIN categories AS c  ON p.category_id = c.id
             LEFT JOIN categories  AS mc ON c.parent_id   = mc.id
             LEFT JOIN product_variants AS v ON v.product_id = p.id
             WHERE p.is_featured = 1 AND p.status = 'active'
             GROUP BY p.id, p.name, p.slug, p.price_min, p.image, c.slug, mc.slug
             ORDER BY p.id ASC
             LIMIT 8"
        )->fetchAll();

        // Hero carousel slides (hero_banners LEFT JOIN the promoted product for the link)
        $hero_banners = $pdo->query(
            "SELECT b.id, b.eyebrow, b.title, b.subtitle, b.body, b.image,
                    p.slug AS product_slug, sc.slug AS subcat_slug,
                    COALESCE(mc.slug, sc.slug) AS cat_slug,
                    COUNT(v.id) AS variant_count
             FROM hero_banners AS b
             LEFT JOIN products   AS p  ON b.product_id = p.id AND p.status = 'active'
             LEFT JOIN categories AS sc ON p.category_id = sc.id
             LEFT JOIN categories AS mc ON sc.parent_id  = mc.id
             LEFT JOIN product_variants AS v ON v.product_id = p.id
             WHERE b.is_active = 1
             GROUP BY b.id, b.eyebrow, b.title, b.subtitle, b.body, b.image, p.slug, sc.slug, mc.slug, b.sort_order
             ORDER BY b.sort_order ASC, b.id ASC"
        )->fetchAll();
    } catch (PDOException $e) {
        $featured_products = [];
        $hero_banners      = [];
    }
}

include __DIR__ . '/includes/head.php';
?>
<body>
<?php
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';
?>

  <!-- ============================================================
       HERO CAROUSEL (hero_banners table)
       ============================================================ -->
  <section class="hero-section" aria-label="Hero banner">
    <div class="hero-carousel">

      <?php foreach ($hero_banners as $i => $hb):
        $hb_url = $hb['product_slug']
            ? product_url($hb['cat_slug'], $hb['subcat_slug'], $hb['product_slug'], (int)$hb['variant_count'])
            : '/category-page.php'; ?>
      <!-- Slide <?= $i + 1 ?> -->
      <div class="hero-slide<?= $i === 0 ? ' active' : '' ?>">
        <div class="hero-slide-img">
          <img src="<?= e(url($hb['image'])) ?>" alt="<?= e(ucwords(strtolower($hb['title']))) ?>">
        </div>
        <div class="hero-slide-content">
          <?php if ($hb['eyebrow']): ?><span class="eyebrow"><?= e($hb['eyebrow']) ?></span><?php endif; ?>
          <h1><?= e($hb['title']) ?></h1>
          <?php if ($hb['subtitle']): ?><h2><?= e($hb['subtitle']) ?></h2><?php endif; ?>
          <?php if ($hb['body']): ?><p><?= e($hb['body']) ?></p><?php endif; ?>
          <a href="<?= e(url($hb_url)) ?>" class="btn btn-white btn-large">BROWSE CATALOG</a>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- Carousel Controls -->
      <button class="carousel-btn prev" aria-label="Previous slide"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
      <button class="carousel-btn next" aria-label="Next slide"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>

      <div class="carousel-dots" role="tablist" aria-label="Slide indicators">
        <?php foreach ($hero_banners as $i => $hb): ?>
          <button class="carousel-dot<?= $i === 0 ? ' active' : '' ?>" role="tab" aria-label="Slide <?= $i + 1 ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"></button>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============================================================
       PRODUCTS SECTION (featured products from the database)
       ============================================================ -->
  <section class="products-section" aria-labelledby="products-heading">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title" id="products-heading">OUR PRODUCTS</h2>
        <a href="<?= BASE_URL ?>/category-page.php" class="view-all-link">View All <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
      </div>

      <div class="product-grid">
        <?php foreach ($featured_products as $prod):
          $prod_url = product_url($prod['cat_slug'], $prod['subcat_slug'], $prod['slug'], (int)$prod['variant_count']); ?>
          <div class="product-card" data-product-id="<?= (int)$prod['id'] ?>" data-url="<?= e(url($prod_url)) ?>">
            <div class="product-card-img">
              <img src="<?= e(url($prod['image'] ?: '/assets/images/placeholder.png')) ?>" alt="<?= e($prod['name']) ?>" loading="lazy">
            </div>
            <div class="product-card-body">
              <p class="product-card-title"><?= e($prod['name']) ?></p>
              <p class="product-card-price"><?= ((float)$prod['price_min'] > 0) ? 'As low as ₱' . number_format((float)$prod['price_min'], 2) . ' ea' : 'Ask for Quote' ?></p>
            </div>
            <div class="product-card-footer">
              <span class="customize-btn"><i class="fas fa-chevron-right" aria-hidden="true"></i> CUSTOMIZE</span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============================================================
       CORPORATE SECTION
       ============================================================ -->
  <section class="corporate-section" aria-labelledby="corporate-heading">
    <div class="container">
      <div class="corporate-inner">
        <div class="corporate-img">
          <img src="<?= BASE_URL ?>/assets/images/hero.jpg" alt="Custom Corporate Items">
        </div>
        <div class="corporate-text">
          <h3 id="corporate-heading">CreativeKit 3A — Your Trusted Partner for Custom Corporate Giveaways</h3>
          <p>Discover premium customized giveaways with CreativeKit 3A, your reliable source for personalized wristbands, promotional items, and branded accessories. More than simple giveaways, our products help strengthen brand recognition and create lasting impressions for your clients, employees, and events.</p>
          <p>At CreativeKit 3A, we make customization simple and hassle-free. Our streamlined design process lets you create products that perfectly match your brand’s style and identity.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       CONTACT SECTION
       ============================================================ -->
  <section class="contact-section" aria-labelledby="contact-heading">
    <div class="container">
      <h6 id="contact-heading">CONTACT US</h6>
      <div class="contact-numbers">
        <a href="tel:+639177142774"><i class="fas fa-mobile-alt" aria-hidden="true"></i> +63 917 714 2774</a>
        <a href="tel:+639339927410"><i class="fas fa-phone" aria-hidden="true"></i> +63 933 992 7410</a>
      </div>
      <p class="contact-email">Email us at <a href="mailto:salesandmarketing@creativekit3a.com">salesandmarketing@creativekit3a.com</a></p>
      <a href="mailto:salesandmarketing@creativekit3a.com" class="btn btn-primary btn-large">Contact Us</a>
    </div>
  </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
