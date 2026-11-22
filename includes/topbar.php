<?php
/**
 * includes/topbar.php — Shared dark top bar (CreativeKit3A Main UI)
 * Left:  social link + Sign In / Register (guests) or
 *        account / Dashboard / Logout pills (signed-in users).
 * Right: contact info.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_name    = $_SESSION['user_name'] ?? '';
$user_role    = $_SESSION['user_role'] ?? '';
$is_staff     = in_array($user_role, ['admin', 'staff'], true);
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
        <?php if ($is_logged_in): ?>
          <?php if ($is_staff): ?>
            <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/pages/dashboard.php" title="Signed in as <?= htmlspecialchars(ucfirst($user_role)) ?>">
              <i class="fas fa-user-circle"></i> <?= htmlspecialchars($user_name) ?> · Dashboard
            </a>
          <?php else: ?>
            <span class="btn btn-outline btn-sm" title="Signed in">
              <i class="fas fa-user-circle"></i> <?= htmlspecialchars($user_name) ?>
            </span>
          <?php endif; ?>
          <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
        <?php else: ?>
          <button class="btn btn-outline btn-sm" onclick="openModal('login-modal')">Sign In</button>
          <button class="btn btn-primary btn-sm" onclick="openModal('register-modal')">Register</button>
        <?php endif; ?>
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
