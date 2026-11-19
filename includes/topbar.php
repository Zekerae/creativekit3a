<?php
/**
 * includes/topbar.php — Shared Topbar
 * Displays social links, contact numbers, and role-aware auth status.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_name    = $_SESSION['user_name'] ?? '';
$user_role    = $_SESSION['user_role'] ?? '';
?>
<div class="top-bar">
  <div class="container top-bar-inner">
    <ul class="top-bar-social">
      <li>
        <a href="https://www.facebook.com/profile.php?id=100063787431490"
           target="_blank" rel="noopener" aria-label="Facebook">
          <i class="fab fa-facebook-f"></i>
        </a>
      </li>
      <li>
        <span style="color:#6B7280;">|</span>
      </li>
      <?php if ($is_logged_in): ?>
        <li style="color:#D1D5DB; font-size:0.8125rem;">
          <i class="fas fa-user-circle" style="color:var(--orange-primary);"></i>
          <span>Hello, <strong><?= htmlspecialchars($user_name) ?></strong></span>
          <span class="badge badge-<?= htmlspecialchars($user_role) ?>" style="margin-left:4px; font-size:0.7rem; padding:2px 8px;">
            <?= ucfirst(htmlspecialchars($user_role)) ?>
          </span>
        </li>
      <?php else: ?>
        <li class="top-bar-auth">
          <a href="/pages/login.php" class="btn btn-outline btn-sm" style="padding:4px 12px; font-size:0.75rem;">Sign In</a>
        </li>
      <?php endif; ?>
    </ul>

    <div class="top-bar-contact">
      <a href="mailto:salesandmarketing@creativekit3a.com">
        <i class="fas fa-envelope"></i> salesandmarketing@creativekit3a.com
      </a>
      <a href="tel:+639177142774">
        <i class="fas fa-mobile-alt"></i> +63 917 714 2774
      </a>
      <a href="tel:+639339927410">
        <i class="fas fa-phone"></i> +63 933 992 7410
      </a>
      <?php if ($is_logged_in): ?>
        <a href="/auth/logout.php" style="color:#F87171; font-weight:600; margin-left:6px;" data-tooltip="Sign out of current session">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>
