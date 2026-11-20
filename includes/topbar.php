<?php
/**
 * includes/topbar.php
 * ─────────────────────────────────────────────────────────────
 * Shared top bar: social links, sign-in / register buttons,
 * and contact info.
 *
 * No variables required. Just include it.
 * ─────────────────────────────────────────────────────────────
 */
?>
<div class="top-bar">
  <div class="container">
    <ul class="top-bar-social">
      <li>
        <a href="https://www.facebook.com/profile.php?id=100063787431490"
           target="_blank" rel="noopener" aria-label="Facebook">
          <i class="fab fa-facebook-f"></i>
        </a>
      </li>
      <li class="top-bar-auth">
        <a href="/admin/index.php" class="btn btn-outline btn-sm" style="text-decoration:none;"><i class="fas fa-chart-line"></i> Management</a>
        <button class="btn btn-outline btn-sm" onclick="openModal('login-modal')">Sign In</button>
        <button class="btn btn-primary btn-sm" onclick="openModal('register-modal')">Register</button>
      </li>
    </ul>

    <div class="top-bar-contact">
      <a href="mailto:salesandmarketing@creativekit3a.com">
        <i class="fas fa-envelope icon"></i> salesandmarketing@creativekit3a.com
      </a>
      <a href="tel:+639177142774">
        <i class="fas fa-mobile-alt icon"></i> +63 917 714 2774
      </a>
      <a href="tel:+639339927410">
        <i class="fas fa-phone icon"></i> +63 933 992 7410
      </a>
    </div>
  </div>
</div>
