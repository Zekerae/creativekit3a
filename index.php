<?php
/**
 * index.php — Homepage
 * Converted from index.html. All shared chrome is now in includes/.
 * Only the unique page content lives here.
 */

$title     = 'Corporate Giveaways | CreativeKit3A';
$meta_desc = 'Customized corporate giveaways, branded accessories, and promotional items in the Philippines. CreativeKit3A — your trusted partner.';
// No extra CSS needed for homepage (style.css covers it all)

include __DIR__ . '/includes/head.php';
?>
<body>
<?php
include __DIR__ . '/includes/topbar.php';
// No active nav on homepage
include __DIR__ . '/includes/navbar.php';
?>

  <!-- ============================================================
       HERO CAROUSEL
       ============================================================ -->
  <section class="hero-section" aria-label="Hero banner">
    <div class="hero-carousel">

      <!-- Slide 1 -->
      <div class="hero-slide active">
        <div class="hero-slide-img">
          <img src="/assets/images/28.png" alt="Get Your Bamboo Tumbler with your desired design">
        </div>
        <div class="hero-slide-content">
          <span class="eyebrow">Get Your</span>
          <h1>BAMBOO TUMBLER</h1>
          <h2>with your desired design</h2>
          <p>We specialize in rush orders and make custom item personalization quick and easy.</p>
          <a href="/category-page.php?cat=drinkware" class="btn btn-white btn-large">BROWSE CATALOG</a>
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
          <p>Stylish and durable custom corporate jackets for events, recognition, and giveaways.</p>
          <a href="/category-page.php?cat=apparel" class="btn btn-white btn-large">BROWSE CATALOG</a>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="hero-slide">
        <div class="hero-slide-img">
          <img src="/assets/images/laptop-bag.png" alt="Custom Logo Bags &amp; Pouches">
        </div>
        <div class="hero-slide-content">
          <span class="eyebrow">Eco-Friendly &amp; Stylish</span>
          <h1>CUSTOM LOGO BAGS</h1>
          <p>Reusable, branded bags that leave a lasting impression at every corporate event.</p>
          <a href="/category-page.php?cat=bags-pouches" class="btn btn-white btn-large">BROWSE CATALOG</a>
        </div>
      </div>

      <!-- Carousel Arrows -->
      <button class="carousel-btn prev" aria-label="Previous slide"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
      <button class="carousel-btn next" aria-label="Next slide"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>

      <!-- Carousel Dots -->
      <div class="carousel-dots" role="tablist" aria-label="Slide dots">
        <button class="carousel-dot active" aria-label="Slide 1"></button>
        <button class="carousel-dot" aria-label="Slide 2"></button>
        <button class="carousel-dot" aria-label="Slide 3"></button>
      </div>

    </div>
  </section>

  <!-- ============================================================
       FEATURED PRODUCTS
       ============================================================ -->
  <section class="products-section" aria-labelledby="featured-heading">
    <div class="container">
      <div class="products-grid">

        <div class="product-card" data-url="/product/car-charger-adapters.php?cat=accessories-gadgets&amp;subcat=car&amp;item=car-charger-adapter">
          <div class="product-card-img">
            <img src="/assets/images/1.png" alt="Single USB Logo Car Charger" loading="lazy">
          </div>
          <div class="product-card-body">
            <p class="product-card-title">Single USB Logo Car Charger</p>
            <p class="product-card-price">As low as ₱129.00 ea</p>
          </div>
          <div class="product-card-footer">
            <span class="customize-btn"><i class="fas fa-chevron-right" aria-hidden="true"></i> CUSTOMIZE</span>
          </div>
        </div>

        <div class="product-card" data-url="/product/car-charger-adapters.php?cat=accessories-gadgets&amp;subcat=car&amp;item=car-charger-adapter">
          <div class="product-card-img">
            <img src="/assets/images/2.png" alt="Dual USB Fast Charging Car Adapter" loading="lazy">
          </div>
          <div class="product-card-body">
            <p class="product-card-title">Dual USB Fast Charging Car Adapter</p>
            <p class="product-card-price">As low as ₱274.00 ea</p>
          </div>
          <div class="product-card-footer">
            <span class="customize-btn"><i class="fas fa-chevron-right" aria-hidden="true"></i> CUSTOMIZE</span>
          </div>
        </div>

        <div class="product-card" data-url="/product/car-charger-adapters.php?cat=accessories-gadgets&amp;subcat=car&amp;item=car-organizers">
          <div class="product-card-img">
            <img src="/assets/images/car-organizer.png" alt="Car Organizer" loading="lazy">
          </div>
          <div class="product-card-body">
            <p class="product-card-title">Car Organizer</p>
            <p class="product-card-price">As low as ₱88.00 ea</p>
          </div>
          <div class="product-card-footer">
            <span class="customize-btn"><i class="fas fa-chevron-right" aria-hidden="true"></i> CUSTOMIZE</span>
          </div>
        </div>

        <div class="product-card" data-url="/product/car-charger-adapters.php?cat=accessories-gadgets&amp;subcat=car&amp;item=car-dashcams">
          <div class="product-card-img">
            <img src="/assets/images/dash-cam.png" alt="Dash Cam" loading="lazy">
          </div>
          <div class="product-card-body">
            <p class="product-card-title">Dash Cam</p>
            <p class="product-card-price">As low as ₱379.90 ea</p>
          </div>
          <div class="product-card-footer">
            <span class="customize-btn"><i class="fas fa-chevron-right" aria-hidden="true"></i> CUSTOMIZE</span>
          </div>
        </div>

        <div class="product-card" data-url="/category-page.php?cat=bags-pouches">
          <div class="product-card-img">
            <img src="/assets/images/laptop-bag.png" alt="Business Laptop Bag" loading="lazy">
          </div>
          <div class="product-card-body">
            <p class="product-card-title">Business Laptop Bag</p>
            <p class="product-card-price">As low as ₱167.90 ea</p>
          </div>
          <div class="product-card-footer">
            <span class="customize-btn"><i class="fas fa-chevron-right" aria-hidden="true"></i> CUSTOMIZE</span>
          </div>
        </div>

        <div class="product-card" data-url="/category-page.php?cat=accessories-gadgets">
          <div class="product-card-img">
            <img src="/assets/images/mouse-pad.png" alt="Mouse Pad" loading="lazy">
          </div>
          <div class="product-card-body">
            <p class="product-card-title">Mouse Pad</p>
            <p class="product-card-price">As low as ₱80.00 ea</p>
          </div>
          <div class="product-card-footer">
            <span class="customize-btn"><i class="fas fa-chevron-right" aria-hidden="true"></i> CUSTOMIZE</span>
          </div>
        </div>

        <div class="product-card" data-url="/category-page.php?cat=home-living">
          <div class="product-card-img">
            <img src="/assets/images/handheld-vacuum.png" alt="Portable Handheld Vacuum" loading="lazy">
          </div>
          <div class="product-card-body">
            <p class="product-card-title">Portable Handheld Vacuum</p>
            <p class="product-card-price">As low as ₱499.00 ea</p>
          </div>
          <div class="product-card-footer">
            <span class="customize-btn"><i class="fas fa-chevron-right" aria-hidden="true"></i> CUSTOMIZE</span>
          </div>
        </div>

        <div class="product-card" data-url="/category-page.php?cat=accessories-gadgets">
          <div class="product-card-img">
            <img src="/assets/images/wooven-clock.png" alt="Woven Clock" loading="lazy">
          </div>
          <div class="product-card-body">
            <p class="product-card-title">Woven Clock</p>
            <p class="product-card-price">As low as ₱149.40 ea</p>
          </div>
          <div class="product-card-footer">
            <span class="customize-btn"><i class="fas fa-chevron-right" aria-hidden="true"></i> CUSTOMIZE</span>
          </div>
        </div>

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
          <img src="/assets/images/hero.jpg" alt="Custom Corporate Items">
        </div>
        <div class="corporate-text">
          <h3 id="corporate-heading">CreativeKit 3A — Your Trusted Partner for Custom Corporate Giveaways</h3>
          <p>Discover premium customized giveaways with CreativeKit 3A, your reliable source for personalized wristbands, promotional items, and branded accessories. More than simple giveaways, our products help strengthen brand recognition and create lasting impressions for your clients, employees, and events.</p>
          <p>At CreativeKit 3A, we make customization simple and hassle-free. Our streamlined design process lets you create products that perfectly match your brand's style and identity.</p>
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
      <p class="contact-email">
        Email us at <a href="mailto:salesandmarketing@creativekit3a.com">salesandmarketing@creativekit3a.com</a>
      </p>
      <a href="/contact-us/" class="btn btn-primary btn-large">Contact Us</a>
    </div>
  </section>

<?php
// main.js handles product card click navigation; no page-specific JS needed
include __DIR__ . '/includes/footer.php';
?>
