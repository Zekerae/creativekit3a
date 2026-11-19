<?php
/**
 * index.php — Homepage & Product Showcase
 * Dedicated Assets:
 *   - CSS: /assets/css/home.css
 *   - JS:  /assets/js/home.js
 */

require_once __DIR__ . '/config/db.php';

$title     = 'Corporate Giveaways & Custom Merchandising | CreativeKit 3A';
$meta_desc = 'Customized corporate giveaways, branded accessories, and promotional items in the Philippines. CreativeKit 3A — your trusted merchandise partner.';
$extra_css = ['/assets/css/home.css'];
$extra_js  = ['/assets/js/home.js'];

// Fetch live featured products from database if available
$featured_products = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT p.*, c.name AS category_name, c.slug AS cat_slug 
                             FROM products AS p 
                             INNER JOIN categories AS c ON p.category_id = c.id 
                             WHERE p.is_featured = 1 AND p.status = 'active' 
                             ORDER BY p.id ASC 
                             LIMIT 8");
        $featured_products = $stmt->fetchAll();
    } catch (Exception $e) {
        $featured_products = [];
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
       HERO CAROUSEL
       ============================================================ -->
  <section class="hero-section" aria-label="Hero banner">
    <div class="container hero-carousel">

      <!-- Slide 1 -->
      <div class="hero-slide active">
        <div class="hero-slide-img">
          <img src="/assets/images/28.png" alt="Get Your Bamboo Tumbler with your desired design">
        </div>
        <div class="hero-slide-content">
          <span class="eyebrow">Get Your</span>
          <h1>BAMBOO TUMBLER</h1>
          <h2>with your desired design</h2>
          <p>We specialize in rush orders and make custom promotional item personalization quick, seamless, and high-quality.</p>
          <a href="/category-page.php" class="btn btn-primary btn-lg">BROWSE CATALOG</a>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="hero-slide">
        <div class="hero-slide-img">
          <img src="/assets/images/27.png" alt="Custom Corporate Jackets">
        </div>
        <div class="hero-slide-content">
          <span class="eyebrow">Showcase Your Brand with</span>
          <h1>CUSTOM CORPORATE JACKETS</h1>
          <h2>Stylish &amp; Durable Uniforms</h2>
          <p>High-end custom corporate jackets tailored for company events, employee recognition gifts, and team merchandise.</p>
          <a href="/category-page.php" class="btn btn-primary btn-lg">BROWSE CATALOG</a>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="hero-slide">
        <div class="hero-slide-img">
          <img src="/assets/images/laptop-bag.png" alt="Executive Bags">
        </div>
        <div class="hero-slide-content">
          <span class="eyebrow">Executive Gear</span>
          <h1>PROFESSIONAL LAPTOP BAGS</h1>
          <h2>Durable &amp; Water Resistant</h2>
          <p>Durable branded business bags and backpacks that leave a lasting impression at every corporate conference.</p>
          <a href="/category-page.php" class="btn btn-primary btn-lg">BROWSE CATALOG</a>
        </div>
      </div>

      <!-- Carousel Navigation Dots (Event: 'click' in home.js) -->
      <div class="carousel-controls" aria-label="Carousel navigation">
        <button type="button" class="carousel-dot active" aria-label="Slide 1"></button>
        <button type="button" class="carousel-dot" aria-label="Slide 2"></button>
        <button type="button" class="carousel-dot" aria-label="Slide 3"></button>
      </div>

    </div>
  </section>

  <!-- ============================================================
       FEATURED PRODUCTS SHOWCASE
       ============================================================ -->
  <section class="products-section" aria-labelledby="featured-heading">
    <div class="container">
      <div class="section-head">
        <h2 id="featured-heading">Featured Promotional Merchandise</h2>
        <p>Explore our most popular corporate giveaways, custom-branded to perfection.</p>
      </div>

      <div class="products-grid">

        <?php if (!empty($featured_products)): ?>
          <!-- Rendered dynamically from MySQL database -->
          <?php foreach ($featured_products as $prod): ?>
            <div class="product-card" data-url="/category-page.php" data-tooltip="View <?= htmlspecialchars($prod['name']) ?>">
              <div class="product-card-img">
                <img src="<?= htmlspecialchars($prod['image'] ?: '/assets/images/1.png') ?>" 
                     alt="<?= htmlspecialchars($prod['name']) ?>" 
                     loading="lazy">
              </div>
              <div class="product-card-body">
                <p class="product-card-title"><?= htmlspecialchars($prod['name']) ?></p>
                <?php if (!empty($prod['badge'])): ?>
                  <span class="prod-inline-badge" style="width:fit-content; margin-bottom:6px;"><?= htmlspecialchars($prod['badge']) ?></span>
                <?php endif; ?>
                <p class="product-card-price">As low as ₱<?= number_format($prod['price_min'], 2) ?> ea</p>
              </div>
              <div class="product-card-footer">
                <span class="customize-btn">
                  <i class="fas fa-chevron-right" aria-hidden="true"></i> CUSTOMIZE
                </span>
                <span style="font-size:0.75rem; color:var(--grey-mid);"><?= htmlspecialchars($prod['category_name']) ?></span>
              </div>
            </div>
          <?php endforeach; ?>

        <?php else: ?>
          <!-- Fallback featured merchandise cards -->
          <div class="product-card" data-url="/product/car-charger-adapters.php">
            <div class="product-card-img">
              <img src="/assets/images/1.png" alt="Single USB Logo Car Charger" loading="lazy">
            </div>
            <div class="product-card-body">
              <p class="product-card-title">Single USB Logo Car Charger</p>
              <p class="product-card-price">As low as ₱85.00 ea</p>
            </div>
            <div class="product-card-footer">
              <span class="customize-btn"><i class="fas fa-chevron-right"></i> CUSTOMIZE</span>
              <span style="font-size:0.75rem; color:var(--grey-mid);">Car Accessories</span>
            </div>
          </div>

          <div class="product-card" data-url="/product/car-charger-adapters.php">
            <div class="product-card-img">
              <img src="/assets/images/dual-usb-adapter.png" alt="Dual USB Fast Charging Car Adapter" loading="lazy">
            </div>
            <div class="product-card-body">
              <p class="product-card-title">Dual USB Fast Charging Car Adapter</p>
              <p class="product-card-price">As low as ₱110.00 ea</p>
            </div>
            <div class="product-card-footer">
              <span class="customize-btn"><i class="fas fa-chevron-right"></i> CUSTOMIZE</span>
              <span style="font-size:0.75rem; color:var(--grey-mid);">Car Accessories</span>
            </div>
          </div>

          <div class="product-card" data-url="/category-page.php">
            <div class="product-card-img">
              <img src="/assets/images/car-organizer.png" alt="Car Organizer" loading="lazy">
            </div>
            <div class="product-card-body">
              <p class="product-card-title">Car Organizer</p>
              <p class="product-card-price">As low as ₱88.00 ea</p>
            </div>
            <div class="product-card-footer">
              <span class="customize-btn"><i class="fas fa-chevron-right"></i> CUSTOMIZE</span>
              <span style="font-size:0.75rem; color:var(--grey-mid);">Car Accessories</span>
            </div>
          </div>

          <div class="product-card" data-url="/category-page.php">
            <div class="product-card-img">
              <img src="/assets/images/dash-cam.png" alt="Dash Cam" loading="lazy">
            </div>
            <div class="product-card-body">
              <p class="product-card-title">Dash Cam</p>
              <p class="product-card-price">As low as ₱379.90 ea</p>
            </div>
            <div class="product-card-footer">
              <span class="customize-btn"><i class="fas fa-chevron-right"></i> CUSTOMIZE</span>
              <span style="font-size:0.75rem; color:var(--grey-mid);">Car Accessories</span>
            </div>
          </div>

          <div class="product-card" data-url="/category-page.php">
            <div class="product-card-img">
              <img src="/assets/images/laptop-bag.png" alt="Business Laptop Bag" loading="lazy">
            </div>
            <div class="product-card-body">
              <p class="product-card-title">Business Laptop Bag</p>
              <p class="product-card-price">As low as ₱167.90 ea</p>
            </div>
            <div class="product-card-footer">
              <span class="customize-btn"><i class="fas fa-chevron-right"></i> CUSTOMIZE</span>
              <span style="font-size:0.75rem; color:var(--grey-mid);">Bags &amp; Pouches</span>
            </div>
          </div>

          <div class="product-card" data-url="/category-page.php">
            <div class="product-card-img">
              <img src="/assets/images/mouse-pad.png" alt="Mouse Pad" loading="lazy">
            </div>
            <div class="product-card-body">
              <p class="product-card-title">Custom Mouse Pad</p>
              <p class="product-card-price">As low as ₱80.00 ea</p>
            </div>
            <div class="product-card-footer">
              <span class="customize-btn"><i class="fas fa-chevron-right"></i> CUSTOMIZE</span>
              <span style="font-size:0.75rem; color:var(--grey-mid);">Computer Accessories</span>
            </div>
          </div>

          <div class="product-card" data-url="/category-page.php">
            <div class="product-card-img">
              <img src="/assets/images/handheld-vacuum.png" alt="Portable Handheld Vacuum" loading="lazy">
            </div>
            <div class="product-card-body">
              <p class="product-card-title">Portable Handheld Vacuum</p>
              <p class="product-card-price">As low as ₱499.00 ea</p>
            </div>
            <div class="product-card-footer">
              <span class="customize-btn"><i class="fas fa-chevron-right"></i> CUSTOMIZE</span>
              <span style="font-size:0.75rem; color:var(--grey-mid);">Home Living</span>
            </div>
          </div>

          <div class="product-card" data-url="/category-page.php">
            <div class="product-card-img">
              <img src="/assets/images/wooven-clock.png" alt="Woven Clock" loading="lazy">
            </div>
            <div class="product-card-body">
              <p class="product-card-title">Woven Bamboo Clock</p>
              <p class="product-card-price">As low as ₱149.40 ea</p>
            </div>
            <div class="product-card-footer">
              <span class="customize-btn"><i class="fas fa-chevron-right"></i> CUSTOMIZE</span>
              <span style="font-size:0.75rem; color:var(--grey-mid);">Desk Accessories</span>
            </div>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </section>

  <!-- ============================================================
       CORPORATE PROFILE SECTION
       ============================================================ -->
  <section class="corporate-section" id="corporate-heading" aria-labelledby="corporate-title">
    <div class="container">
      <div class="corporate-inner">
        <div class="corporate-img">
          <img src="/assets/images/hero.jpg" alt="Custom Corporate Merchandising Showcase">
        </div>
        <div class="corporate-text">
          <h3 id="corporate-title">CreativeKit 3A — Your Trusted Corporate Merchandising Partner</h3>
          <p>Discover premium customized giveaways with CreativeKit 3A, your reliable Philippine source for personalized wristbands, promotional items, drinkware, and branded tech accessories. More than simple giveaways, our products strengthen brand recognition and create memorable impressions for your clients, employees, and special events.</p>
          <p>We make customization hassle-free with dedicated account coordinators, rapid rush turnarounds, laser engraving, screen printing, and full-color sublimation services.</p>
          <div style="margin-top:20px;">
            <button class="btn btn-primary" onclick="openModal('quote-modal')">
              <i class="fas fa-paper-plane"></i> Inquire for Custom Branding
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       DIRECT CONTACT CHANNELS SECTION
       ============================================================ -->
  <section class="contact-section" id="contact-heading" aria-labelledby="contact-title">
    <div class="container">
      <h6 id="contact-title">GET IN TOUCH WITH OUR TEAM</h6>
      <div class="contact-numbers">
        <a href="tel:+639177142774"><i class="fas fa-mobile-alt"></i> +63 917 714 2774</a>
        <a href="tel:+639339927410"><i class="fas fa-phone"></i> +63 933 992 7410</a>
      </div>
      <p class="contact-email">
        Email our sales team at <a href="mailto:salesandmarketing@creativekit3a.com" style="color:var(--orange-primary); font-weight:600;">salesandmarketing@creativekit3a.com</a>
      </p>
      <button class="btn btn-primary btn-lg" onclick="openModal('quote-modal')">
        <i class="fas fa-envelope-open-text"></i> Submit Quotation Request
      </button>
    </div>
  </section>

<?php
include __DIR__ . '/includes/footer.php';
?>
