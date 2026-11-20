<?php
/**
 * includes/navbar.php
 * ─────────────────────────────────────────────────────────────
 * Shared main navbar, mega menu bar, and mobile sidenav.
 *
 * Optional variable:
 *   $active_nav  (string) — slug of the active mega-menu item
 *                           e.g. 'accessories-gadgets', 'apparel'
 *                           Adds the 'active-cat' CSS class automatically.
 *
 * Usage:
 *   <?php $active_nav = 'accessories-gadgets'; include __DIR__ . '/includes/navbar.php'; ?>
 * ─────────────────────────────────────────────────────────────
 */

$active_nav = $active_nav ?? '';

/**
 * Helper: returns ' active-cat' if the given slug matches $active_nav.
 */
function nav_active(string $slug): string {
    global $active_nav;
    return $active_nav === $slug ? ' active-cat' : '';
}
?>

<!-- ============================================================
     MAIN NAVBAR
     ============================================================ -->
<nav class="navbar" role="navigation" aria-label="Main navigation">
  <div class="navbar-inner">

    <!-- Hamburger (mobile) -->
    <button class="hamburger" aria-label="Open menu" aria-expanded="false" aria-controls="sidenav">
      <span></span><span></span><span></span>
    </button>

    <!-- Logo -->
    <a href="/" class="navbar-logo" aria-label="CreativeKit3A home">
      <img src="/assets/images/logo.png" alt="CreativeKit3A Logo" class="logo-img">
      <span class="logo-text">CreativeKit3A</span>
    </a>

    <!-- Search -->
    <div class="navbar-search" role="search">
      <input type="search" placeholder="What are you looking for?"
             aria-label="Search products" id="navbar-search-input">
      <i class="fas fa-search search-icon" role="button" tabindex="0" title="Search" aria-label="Search"></i>
    </div>

    <!-- Right side links -->
    <div class="navbar-right">
      <nav class="navbar-links" aria-label="Secondary navigation">
        <a href="/index.php#corporate-heading">About</a>
        <a href="/category-page.php?cat=accessories-gadgets"
          <?= $active_nav ? 'style="color:var(--orange-primary);font-weight:700;"' : '' ?>>Category</a>
        <a href="/index.php#contact-heading">Contact Us</a>
      </nav>
      <button class="btn-chat" onclick="openModal('quote-modal')" aria-label="Request a Quote">Request a Quote</button>
      <a href="/category-page.php?cat=accessories-gadgets" class="navbar-cart" aria-label="Shopping cart">
        <i class="fas fa-shopping-cart" aria-hidden="true"></i>
        <span>Cart</span>
        <span class="cart-count" aria-live="polite">0</span>
      </a>
    </div>

  </div>
</nav>

<!-- ============================================================
     MEGA MENU BAR
     ============================================================ -->
<div class="mega-menu-bar" role="navigation" aria-label="Category navigation">
  <ul class="mega-menu-nav">

    <!-- ACCESSORIES & GADGETS -->
    <li class="mega-menu-item<?= nav_active('accessories-gadgets') ?>">
      <a href="/category-page.php?cat=accessories-gadgets">ACCESSORIES &amp; GADGETS <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
      <div class="mega-dropdown">
        <div class="mega-dropdown-col">
          <h4>Car Accessories</h4>
          <a href="/product/car-charger-adapters.php">Car Charger Adapters</a>
          <a href="/product/car-organizers">Car Organizers &amp; Holders</a>
          <a href="/product/car-tools">Car Tools &amp; Emergency Gear</a>
          <a href="/product/car-dashcams">Dash Cams</a>
          <a href="/product/car-dashboard">Dashboard Tools</a>
          <a href="/product/car-flashlights">Flash Lights</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Computer Accessories</h4>
          <a href="/product/computer-audio">Audio Accessories</a>
          <a href="/product/computer-laptop-keyboard">Laptop &amp; Keyboard Accessories</a>
          <a href="/product/computer-mouse">Mouse &amp; Mouse Pads</a>
          <a href="/product/computer-usb">USB Hub &amp; Connectors</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Desk Accessories</h4>
          <a href="/product/desk-clocks">Clocks &amp; Timepieces</a>
          <a href="/product/desk-organizers">Desk Organizers &amp; Pen Holders</a>
          <a href="/product/desk-memo-holders">Memo Holders &amp; Memo Cubes</a>
          <a href="/product/desk-mobile-stands">Mobile &amp; Gadget Stands</a>
          <a href="/product/desk-portable-fans">Portable Fans &amp; Lights</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Mobile Accessories</h4>
          <a href="/product/mobile-adapters">Adapters</a>
          <a href="/product/mobile-cables">Cables &amp; Connectors</a>
          <a href="/product/mobile-powerbanks">Mobile Power Banks</a>
          <a href="/product/mobile-stands">Mobile Stands</a>
          <a href="/product/mobile-speakers">Speakers</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Gadgets</h4>
          <a href="/product/gadgets-audio-music">Audio &amp; Music Accessories</a>
          <a href="/product/gadgets-charging-devices">Charging Devices</a>
          <a href="/product/gadgets-portable-fans">Portable Fans</a>
          <a href="/product/gadgets-portable-lights">Portable Lights</a>
          <a href="/product/gadgets-powerbanks">Power Banks</a>
          <a href="/product/gadgets-tools">Tools</a>
        </div>
      </div>
    </li>

    <!-- APPAREL -->
    <li class="mega-menu-item<?= nav_active('apparel') ?>">
      <a href="/category-page.php?cat=apparel">APPAREL <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
      <div class="mega-dropdown">
        <div class="mega-dropdown-col">
          <h4>Caps</h4>
          <a href="/product/cap-baseball/">Baseball Cap</a>
          <a href="/product/cap-bucket-hat/">Bucket Hat</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Jackets</h4>
          <a href="/product/jacket-bomber/">Bomber Jackets</a>
          <a href="/product/jackets-corporate/">Corporate Jackets</a>
          <a href="/product/jacket-hoodies/">Hoodies</a>
          <a href="/product/jacket-sports/">Sports &amp; Windbreaker Jackets</a>
          <a href="/product/jacket-varsity-jackets/">Varsity Jackets</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Shirts</h4>
          <a href="/product/shirt-collared/">Collared Shirt</a>
          <a href="/product/shirt-dri-fit/">Dri Fit Shirt</a>
          <a href="/product/shirt-golf/">Golf Shirt</a>
          <a href="/product/shirt-honeycomb/">Honeycomb Shirt</a>
          <a href="/product/shirt-round-neck/">Round Neck Shirt</a>
        </div>
      </div>
    </li>

    <!-- BAGS & POUCHES -->
    <li class="mega-menu-item<?= nav_active('bags-pouches') ?>">
      <a href="/category-page.php?cat=bags-pouches">BAGS &amp; POUCHES <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
      <div class="mega-dropdown">
        <div class="mega-dropdown-col">
          <h4>Backpacks</h4>
          <a href="/product/backpacks-computer/">Computer Backpacks</a>
          <a href="/product/backpacks-sports/">Sports Backpacks</a>
          <a href="/product/backpacks-travel/">Travel Backpacks</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Eco Bags</h4>
          <a href="/product/ecobags-shopping-tote/">Foldable Shopping Totes</a>
          <a href="/product/ecobags-non-woven/">Non Woven Shopping Bags</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Laptop Bags</h4>
          <a href="/product/laptop-backpack/">Laptop Backpacks</a>
          <a href="/product/laptop-shoulder-bag/">Laptop Shoulder Bags</a>
          <a href="/product/laptop-hand-carry-bag/">Laptop Hand Carry Bags</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Tote Bags</h4>
          <a href="/product/tote-bag-classic-zip/">Classic Zip Totes</a>
          <a href="/product/tote-bag-foldable-zip/">Foldable Totes</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Travel Bags</h4>
          <a href="/product/travel-bag-backpack/">Back Pack Travel Bags</a>
          <a href="/product/travel-bag-duffel/">Duffel Travel Bags</a>
          <a href="/product/travel-bag-tote/">Tote Travel Bag</a>
          <a href="/product/travel-bag-weekender/">Weekender Travel Bag</a>
        </div>
      </div>
    </li>

    <!-- DRINKWARE -->
    <li class="mega-menu-item<?= nav_active('drinkware') ?>">
      <a href="/category-page.php?cat=drinkware">DRINKWARE <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
      <div class="mega-dropdown">
        <div class="mega-dropdown-col">
          <h4>Tumbler &amp; Flasks</h4>
          <a href="/product/tumbler-bamboo/">Bamboo Tumblers</a>
          <a href="/product/tumbler-insulated/">Insulated &amp; Double-Walled</a>
          <a href="/product/tumbler-tumblers/">Tumblers</a>
          <a href="/product/tumbler-stainless/">Stainless Steel Tumblers</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Mugs &amp; Cups</h4>
          <a href="/product/mugs-ceramic/">Ceramic Mugs</a>
          <a href="/product/mugs-coffee/">Coffee Mugs</a>
          <a href="/product/mugs-stainless/">Stainless Steel Mugs</a>
          <a href="/product/mugs-travel-mugs/">Travel Mugs with Lids</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Water Bottles</h4>
          <a href="/product/water-bottle-bpa/">BPA-Free Bottles</a>
          <a href="/product/water-bottle-sports/">Sports Bottles</a>
        </div>
      </div>
    </li>

    <!-- GIFT SET -->
    <li class="mega-menu-item<?= nav_active('giftset') ?>">
      <a href="/category-page.php?cat=giftset">GIFT SET <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
      <div class="mega-dropdown">
        <div class="mega-dropdown-col">
          <a href="/product/gift-set-active-outdoors/">Active Outdoors Gift Sets</a>
          <a href="/product/gift-set-desk-essentials/">Desk Essentials Gift Sets</a>
          <a href="/product/gift-set-employee-onboarding/">Employee Onboarding Sets</a>
          <a href="/product/gift-set-gadget/">Gadget Gift Sets</a>
          <a href="/product/gift-set-health-wellness/">Health &amp; Wellness Gift Sets</a>
          <a href="/product/gift-set-sustainable/">Sustainable Gift Sets</a>
          <a href="/product/gift-set-work-from-home/">Work From Home Gift Sets</a>
          <a href="/product/gift-set-travel-executive/">Travel Executive Gift Sets</a>
        </div>
      </div>
    </li>

    <!-- HOME LIVING -->
    <li class="mega-menu-item<?= nav_active('home-living') ?>">
      <a href="/category-page.php?cat=home-living">HOME LIVING <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
      <div class="mega-dropdown">
        <div class="mega-dropdown-col">
          <a href="/product/home-living-cleaning-essentials/">Cleaning Essentials</a>
          <a href="/product/home-living-cutlery-sets/">Cutlery Sets</a>
          <a href="/product/home-living-hand-towels/">Hand Towels</a>
          <a href="/product/home-living-outdoor-chairs/">Outdoor Chairs</a>
          <a href="/product/home-living-personal-care/">Personal Care</a>
          <a href="/product/home-living-pillows/">Pillows</a>
          <a href="/product/home-living-portable-fans/">Portable Fans</a>
        </div>
      </div>
    </li>

    <!-- PEN & PAPER -->
    <li class="mega-menu-item<?= nav_active('pen-paper') ?>">
      <a href="/category-page.php?cat=pen-paper">PEN &amp; PAPER <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
      <div class="mega-dropdown">
        <div class="mega-dropdown-col">
          <h4>Pen</h4>
          <a href="/product/pen-bamboo/">Bamboo Pens</a>
          <a href="/product/pen-metal/">Metal Pens</a>
          <a href="/product/pen-plastic/">Plastic Pens</a>
          <a href="/product/pen-gift-set/">Pen Gift Sets</a>
        </div>
        <div class="mega-dropdown-col">
          <h4>Paper</h4>
          <a href="/product/paper-boxed-notes/">Boxed Notes</a>
          <a href="/product/paper-desk-calendars/">Custom Desk Calendars</a>
          <a href="/product/paper-notebooks-journals">Custom Notebooks &amp; Journals</a>
          <a href="/product/paper-memo-solutions/">Memo Solutions</a>
          <a href="/product/paper-stationery-sets/">Stationery Sets</a>
          <a href="/product/paper-padfolios">Padfolios</a>
        </div>
      </div>
    </li>

    <!-- PU LEATHER -->
    <li class="mega-menu-item<?= nav_active('pu-leather') ?>">
      <a href="/category-page.php?cat=pu-leather">PU LEATHER <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
      <div class="mega-dropdown">
        <div class="mega-dropdown-col">
          <a href="/product/pu-leather-card-cases/">Card Cases</a>
          <a href="/product/pu-leather-desk-mats/">Desk Mats</a>
          <a href="/product/pu-leather-pouches/">Leather Pouches</a>
          <a href="/product/pu-leather-keychains/">Keychains</a>
          <a href="/product/pu-leather-luggage-tags/">Luggage Tags</a>
          <a href="/product/pu-leather-organizers/">Organizers</a>
          <a href="/product/pu-leather-wallets/">Wallets</a>
        </div>
      </div>
    </li>

  </ul>
</div>

<!-- ============================================================
     MOBILE SIDENAV
     ============================================================ -->
<div class="sidenav-overlay" aria-hidden="true"></div>

<aside class="sidenav" id="sidenav" role="complementary" aria-label="Mobile navigation">
  <div class="sidenav-header">
    <span class="logo-text">CreativeKit3A</span>
    <button class="sidenav-close" aria-label="Close menu">&times;</button>
  </div>
  <div class="sidenav-body">
    <p class="sidenav-section-title">Categories</p>
    <a href="/category-page.php?cat=accessories-gadgets">ACCESSORIES &amp; GADGETS</a>
    <a href="/category-page.php?cat=apparel">APPAREL</a>
    <a href="/category-page.php?cat=bags-pouches">BAGS &amp; POUCHES</a>
    <a href="/category-page.php?cat=drinkware">DRINKWARE</a>
    <a href="/category-page.php?cat=giftset">GIFT SET</a>
    <a href="/category-page.php?cat=home-living">HOME LIVING</a>
    <a href="/category-page.php?cat=pen-paper">PEN &amp; PAPER</a>
    <a href="/category-page.php?cat=pu-leather">PU LEATHER</a>
    <p class="sidenav-section-title">Help</p>
    <a href="/index.php#corporate-heading">About</a>
    <a href="/category-page.php?cat=accessories-gadgets">All Categories</a>
    <a href="/index.php#contact-heading">Contact Us</a>
    <p class="sidenav-section-title">Contact</p>
    <a href="tel:+639177142774"><i class="fas fa-mobile-alt"></i> +63 917 714 2774</a>
    <a href="tel:+639339927410"><i class="fas fa-phone"></i> +63 933 992 7410</a>
    <a href="mailto:salesandmarketing@creativekit3a.com"><i class="fas fa-envelope"></i> salesandmarketing@creativekit3a.com</a>
  </div>
</aside>
