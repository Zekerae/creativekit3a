<?php
/**
 * includes/navbar.php — Shared Main Navbar & Responsive Mobile Drawer
 * Role-aware: Adjusts menu links dynamically based on session status (Guest, Staff, Admin).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role    = $_SESSION['user_role'] ?? '';
$user_name    = $_SESSION['user_name'] ?? '';
$active_nav   = $active_nav ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- ============================================================
     MAIN NAVBAR (Desktop & Tablet)
     ============================================================ -->
<nav class="navbar" role="navigation" aria-label="Main navigation">
  <div class="navbar-inner">

    <!-- Brand Logo -->
    <a href="/" class="navbar-logo" aria-label="CreativeKit3A home">
      <img src="/assets/images/logo.png" alt="CreativeKit3A Logo" class="logo-img">
      <span class="logo-text">CreativeKit3A</span>
    </a>

    <!-- Primary Navigation Links -->
    <ul class="navbar-nav" role="menubar">
      <li role="none">
        <a href="/" class="<?= ($current_page === 'index.php') ? 'active' : '' ?>" role="menuitem">
          <i class="fas fa-home"></i> Home
        </a>
      </li>
      <li role="none">
        <a href="/category-page.php" class="<?= ($current_page === 'category-page.php') ? 'active' : '' ?>" role="menuitem">
          <i class="fas fa-th-large"></i> Catalog
        </a>
      </li>

      <?php if ($is_logged_in): ?>
        <!-- Role-Aware Links for Staff & Admin -->
        <li role="none">
          <a href="/pages/dashboard.php" class="<?= ($current_page === 'dashboard.php') ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-chart-line"></i> Dashboard
          </a>
        </li>
        <li role="none">
          <a href="/pages/products.php" class="<?= (in_array($current_page, ['products.php', 'product-form.php'])) ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-boxes"></i> Products
          </a>
        </li>
        <li role="none">
          <a href="/pages/quotes.php" class="<?= ($current_page === 'quotes.php') ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-file-invoice-dollar"></i> Quotes
          </a>
        </li>
        <li role="none">
          <a href="/pages/reports.php" class="<?= ($current_page === 'reports.php') ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-chart-bar"></i> Reports
          </a>
        </li>
      <?php else: ?>
        <li role="none">
          <a href="/#corporate-heading" role="menuitem">About Us</a>
        </li>
        <li role="none">
          <a href="/#contact-heading" role="menuitem">Contact</a>
        </li>
      <?php endif; ?>
    </ul>

    <!-- Navbar Right: Action Buttons & Mobile Hamburger -->
    <div class="navbar-right">
      <button class="btn btn-primary btn-sm" onclick="openModal('quote-modal')" aria-label="Request a Quote" data-tooltip="Submit a custom quotation inquiry">
        <i class="fas fa-paper-plane"></i> Request a Quote
      </button>

      <?php if ($is_logged_in): ?>
        <a href="/auth/logout.php" class="btn btn-secondary btn-sm" data-tooltip="Sign out of <?= htmlspecialchars($user_name) ?>">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      <?php else: ?>
        <a href="/pages/login.php" class="btn btn-outline btn-sm" data-tooltip="Staff & Admin Portal">
          <i class="fas fa-lock"></i> Portal Login
        </a>
      <?php endif; ?>

      <!-- Hamburger Button (Mobile & Tablet) -->
      <button class="hamburger" aria-label="Toggle navigation menu" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

  </div>
</nav>

<!-- ============================================================
     RESPONSIVE MOBILE DRAWER
     ============================================================ -->
<div class="mobile-drawer-overlay" aria-hidden="true"></div>

<aside class="mobile-drawer" role="dialog" aria-label="Mobile navigation">
  <div class="mobile-drawer-header">
    <div class="navbar-logo">
      <img src="/assets/images/logo.png" alt="Logo" style="height:30px;">
      <span class="logo-text">CreativeKit3A</span>
    </div>
    <button class="mobile-drawer-close modal-close" aria-label="Close menu">&times;</button>
  </div>

  <div class="mobile-drawer-body">
    <?php if ($is_logged_in): ?>
      <div style="background:var(--orange-bg); padding:12px; border-radius:var(--radius-md); margin-bottom:8px;">
        <p style="font-size:0.75rem; color:var(--grey-dark); text-transform:uppercase; font-weight:600;">Signed In As</p>
        <p style="font-weight:700; color:var(--black-deep); font-size:0.9375rem;"><?= htmlspecialchars($user_name) ?></p>
        <span class="badge badge-<?= htmlspecialchars($user_role) ?>"><?= ucfirst(htmlspecialchars($user_role)) ?></span>
      </div>

      <a href="/pages/dashboard.php" class="<?= ($current_page === 'dashboard.php') ? 'active' : '' ?>">
        <i class="fas fa-chart-line" style="width:20px; color:var(--orange-primary);"></i> Dashboard
      </a>
      <a href="/pages/products.php" class="<?= (in_array($current_page, ['products.php', 'product-form.php'])) ? 'active' : '' ?>">
        <i class="fas fa-boxes" style="width:20px; color:var(--orange-primary);"></i> Products (CRUD)
      </a>
      <a href="/pages/quotes.php" class="<?= ($current_page === 'quotes.php') ? 'active' : '' ?>">
        <i class="fas fa-file-invoice-dollar" style="width:20px; color:var(--orange-primary);"></i> Quotes
      </a>
      <a href="/pages/reports.php" class="<?= ($current_page === 'reports.php') ? 'active' : '' ?>">
        <i class="fas fa-chart-bar" style="width:20px; color:var(--orange-primary);"></i> Reports
      </a>
      <hr style="border:none; border-top:1px solid var(--grey-border); margin:6px 0;">
    <?php endif; ?>

    <a href="/" class="<?= ($current_page === 'index.php') ? 'active' : '' ?>">
      <i class="fas fa-home" style="width:20px;"></i> Home
    </a>
    <a href="/category-page.php" class="<?= ($current_page === 'category-page.php') ? 'active' : '' ?>">
      <i class="fas fa-th-large" style="width:20px;"></i> Product Catalog
    </a>
    <a href="/#corporate-heading">
      <i class="fas fa-info-circle" style="width:20px;"></i> About Us
    </a>
    <a href="/#contact-heading">
      <i class="fas fa-envelope" style="width:20px;"></i> Contact Us
    </a>

    <div style="margin-top:auto; padding-top:20px; display:flex; flex-direction:column; gap:10px;">
      <button class="btn btn-primary" onclick="closeDrawer(); openModal('quote-modal');" style="width:100%;">
        <i class="fas fa-paper-plane"></i> Request a Quote
      </button>
      <?php if ($is_logged_in): ?>
        <a href="/auth/logout.php" class="btn btn-secondary" style="width:100%; justify-content:center;">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      <?php else: ?>
        <a href="/pages/login.php" class="btn btn-outline" style="width:100%; justify-content:center;">
          <i class="fas fa-lock"></i> Portal Login
        </a>
      <?php endif; ?>
    </div>
  </div>
</aside>
