<?php
/**
 * pages/login.php — Staff & Administrator Authentication Portal
 * Dedicated Assets:
 *   - CSS: /assets/css/login.css
 *   - JS:  /assets/js/login.js
 */

require_once __DIR__ . '/../config/db.php';

// If already logged in, redirect straight to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: /pages/dashboard.php');
    exit;
}

$page_title = 'Staff & Admin Login | CreativeKit 3A';
$extra_css  = ['/assets/css/login.css'];
$extra_js   = ['/assets/js/login.js'];

// Retrieve and clear flash errors from session
$login_errors = $_SESSION['login_errors'] ?? [];
$login_email  = $_SESSION['login_email'] ?? '';
unset($_SESSION['login_errors'], $_SESSION['login_email']);

$flash = get_flash();

include __DIR__ . '/../includes/head.php';
?>
<body>
<?php
include __DIR__ . '/../includes/topbar.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="login-wrapper">
  <div class="container login-container">

    <!-- Flash message if redirected with status -->
    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" role="alert">
        <span><i class="fas fa-info-circle"></i> <?= htmlspecialchars($flash['message']) ?></span>
        <button type="button" class="alert-close" aria-label="Close">&times;</button>
      </div>
    <?php endif; ?>

    <div class="login-grid">

      <!-- Left Column: Login Card -->
      <div class="login-card">
        <div class="login-header">
          <div class="login-badge">
            <i class="fas fa-shield-alt"></i> Secure Staff Portal
          </div>
          <h1>Welcome Back</h1>
          <p>Sign in to manage products, quote requests, and summary reports.</p>
        </div>

        <!-- Server-side validation errors -->
        <?php if (!empty($login_errors)): ?>
          <div class="alert alert-danger" id="server-error-box" role="alert">
            <div>
              <i class="fas fa-exclamation-triangle"></i>
              <strong>Please check your credentials:</strong>
              <ul style="margin-top:6px; padding-left:18px;">
                <?php foreach ($login_errors as $err): ?>
                  <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <button type="button" class="alert-close" aria-label="Close">&times;</button>
          </div>
        <?php endif; ?>

        <!-- Client-side dynamic error box (populated via JS submit event) -->
        <div class="alert alert-danger" id="client-error-box" style="display:none;" role="alert">
          <div id="client-error-msg"></div>
        </div>

        <!-- Login Form -->
        <form action="/auth/login.php" method="POST" id="login-form" novalidate>
          <div class="form-group">
            <label for="email">
              <i class="fas fa-envelope"></i> Work Email Address
            </label>
            <input 
              type="email" 
              name="email" 
              id="email" 
              class="form-control" 
              placeholder="e.g. admin@creativekit3a.com"
              value="<?= htmlspecialchars($login_email) ?>" 
              required 
              autocomplete="email"
              autofocus
            >
            <span class="field-error" id="email-error"></span>
          </div>

          <div class="form-group">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <label for="password">
                <i class="fas fa-lock"></i> Password
              </label>
              <button type="button" class="btn-toggle-pw" id="toggle-password" aria-label="Toggle password visibility">
                <i class="fas fa-eye" id="toggle-pw-icon"></i> <span id="toggle-pw-text">Show</span>
              </button>
            </div>
            <input 
              type="password" 
              name="password" 
              id="password" 
              class="form-control" 
              placeholder="Enter your password"
              required 
              autocomplete="current-password"
            >
            <span class="field-error" id="password-error"></span>
          </div>

          <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-submit-login">
            <i class="fas fa-sign-in-alt"></i> Sign In to Portal
          </button>
        </form>

        <div class="login-footer">
          <a href="/index.php"><i class="fas fa-arrow-left"></i> Return to CreativeKit 3A Storefront</a>
        </div>
      </div>

      <!-- Right Column: Demo Accounts & Role Preview Guide -->
      <div class="login-info-card">
        <h3><i class="fas fa-key" style="color:var(--orange-primary);"></i> Quick Demo Credentials</h3>
        <p style="font-size:0.875rem; color:var(--grey-dark); margin-bottom:18px;">
          For evaluation and project defense, you can click either role below to automatically fill in the credentials:
        </p>

        <!-- Role 1: Administrator Card -->
        <div class="demo-role-box demo-role-admin" id="autofill-admin" role="button" tabindex="0" data-tooltip="Click to autofill Admin credentials">
          <div class="demo-role-header">
            <span class="badge badge-admin"><i class="fas fa-user-shield"></i> Administrator</span>
            <span class="demo-click-hint">1-Click Fill <i class="fas fa-arrow-right"></i></span>
          </div>
          <div class="demo-role-body">
            <p><strong>Email:</strong> <code>admin@creativekit3a.com</code></p>
            <p><strong>Password:</strong> <code>admin123</code></p>
            <p class="demo-perm-text"><i class="fas fa-check-circle"></i> Full CRUD, Delete Products, Assign Quotes, User Audit</p>
          </div>
        </div>

        <!-- Role 2: Staff Member Card -->
        <div class="demo-role-box demo-role-staff" id="autofill-staff" role="button" tabindex="0" data-tooltip="Click to autofill Staff credentials">
          <div class="demo-role-header">
            <span class="badge badge-staff"><i class="fas fa-user-tie"></i> Staff Member</span>
            <span class="demo-click-hint">1-Click Fill <i class="fas fa-arrow-right"></i></span>
          </div>
          <div class="demo-role-body">
            <p><strong>Email:</strong> <code>maria@creativekit3a.com</code></p>
            <p><strong>Password:</strong> <code>staff123</code></p>
            <p class="demo-perm-text"><i class="fas fa-check-circle"></i> Product Catalog, Edit Products, Update Quote Workflow</p>
          </div>
        </div>

        <div class="system-specs-box">
          <h4><i class="fas fa-shield-virus"></i> Security Implementations</h4>
          <ul>
            <li><strong>Password Hashing:</strong> PHP <code>password_hash()</code> & <code>password_verify()</code> (Bcrypt)</li>
            <li><strong>Injection Prevention:</strong> PDO Prepared Statements on all queries</li>
            <li><strong>Route Protection:</strong> Server-side <code>auth/guard.php</code> session check</li>
            <li><strong>Audit Logging:</strong> All login, create, update, delete events recorded</li>
          </ul>
        </div>
      </div>

    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
