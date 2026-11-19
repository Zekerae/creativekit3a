<?php
/**
 * includes/footer.php — Shared Site Footer & Script Loader
 * Closes <body> and includes quote modal and per-page JavaScript.
 */

$extra_js = $extra_js ?? [];
?>

<!-- ============================================================
     GLOBAL SITE FOOTER
     ============================================================ -->
<footer class="site-footer" role="contentinfo">
  <div class="footer-top">
    <div class="container footer-grid">

      <!-- Brand & Company Address -->
      <div class="footer-brand">
        <h4>CreativeKit 3A</h4>
        <p>
          Your trusted partner for customized corporate giveaways, branded accessories, and premium promotional items in the Philippines.
        </p>
        <p style="font-size:0.8125rem; color:#9CA3AF;">
          <i class="fas fa-map-marker-alt" style="color:var(--orange-primary); margin-right:6px;"></i>
          2779B R. Fernandez St. cor. Benita St., Gagalangin, Tondo, Manila, 1013
        </p>
      </div>

      <!-- Quick Navigation -->
      <div class="footer-col">
        <h5>Quick Links</h5>
        <ul>
          <li><a href="/"><i class="fas fa-angle-right"></i> Home</a></li>
          <li><a href="/category-page.php"><i class="fas fa-angle-right"></i> Product Catalog</a></li>
          <li><a href="/#corporate-heading"><i class="fas fa-angle-right"></i> Corporate Profile</a></li>
          <li><a href="/#contact-heading"><i class="fas fa-angle-right"></i> Contact Us</a></li>
        </ul>
      </div>

      <!-- Staff & Management Portal -->
      <div class="footer-col">
        <h5>Staff Portal</h5>
        <ul>
          <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="/pages/dashboard.php"><i class="fas fa-angle-right"></i> Dashboard</a></li>
            <li><a href="/pages/products.php"><i class="fas fa-angle-right"></i> Manage Products</a></li>
            <li><a href="/pages/quotes.php"><i class="fas fa-angle-right"></i> Quote Inquiries</a></li>
            <li><a href="/pages/reports.php"><i class="fas fa-angle-right"></i> Summary Reports</a></li>
          <?php else: ?>
            <li><a href="/pages/login.php"><i class="fas fa-angle-right"></i> Staff / Admin Login</a></li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Direct Contact Channels -->
      <div class="footer-col">
        <h5>Connect With Us</h5>
        <ul>
          <li>
            <a href="mailto:salesandmarketing@creativekit3a.com">
              <i class="fas fa-envelope"></i> salesandmarketing@creativekit3a.com
            </a>
          </li>
          <li>
            <a href="tel:+639177142774">
              <i class="fas fa-mobile-alt"></i> +63 917 714 2774
            </a>
          </li>
          <li>
            <a href="tel:+639339927410">
              <i class="fas fa-phone"></i> +63 933 992 7410
            </a>
          </li>
          <li>
            <button class="btn btn-outline btn-sm" onclick="openModal('quote-modal')" style="margin-top:8px; border-color:#9CA3AF; color:#D1D5DB;">
              <i class="fas fa-paper-plane"></i> Quick Quote
            </button>
          </li>
        </ul>
      </div>

    </div>
  </div>

  <div class="container footer-bottom">
    <p>&copy; <?= date('Y') ?> CreativeKit 3A. All rights reserved. Built for production & academic defense.</p>
    <div style="display:flex; align-items:center; gap:16px;">
      <a href="https://www.facebook.com/profile.php?id=100063787431490" target="_blank" rel="noopener" aria-label="Facebook">
        <i class="fab fa-facebook-f"></i>
      </a>
      <span style="color:#4B5563;">•</span>
      <span style="font-size:0.8125rem; color:#9CA3AF;">VAT Registered Supplier</span>
    </div>
  </div>
</footer>

<!-- Reusable Request a Quote Modal Partial -->
<?php include __DIR__ . '/quote-modal.php'; ?>

<!-- Core Global JavaScript (Drawer, Modal Controller, Tooltip) -->
<script src="/assets/js/global.js"></script>
<script src="/assets/js/quote-modal.js"></script>

<!-- Dedicated Per-Page JavaScript Files -->
<?php foreach ($extra_js as $js): ?>
  <script src="<?= htmlspecialchars($js) ?>"></script>
<?php endforeach; ?>

</body>
</html>
