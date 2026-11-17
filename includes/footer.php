<?php
/**
 * includes/footer.php
 * ─────────────────────────────────────────────────────────────
 * Shared site footer, auth modals, and closing scripts.
 * Always include this as the LAST thing inside <body>.
 *
 * Optional variable:
 *   $extra_js  (array) — additional script srcs loaded after main.js
 *                        e.g. ['/product/car-charger-adapters.js']
 * ─────────────────────────────────────────────────────────────
 */

$extra_js = $extra_js ?? [];
?>

<!-- ============================================================
     SITE FOOTER
     ============================================================ -->
<footer class="site-footer" role="contentinfo">
  <div class="footer-main">
    <div class="container">
      <div class="footer-grid">

        <!-- Brand -->
        <div class="footer-brand">
          <a href="/" aria-label="CreativeKit3A home">
            <span class="logo-text">CreativeKit3A</span>
          </a>
          <p class="tagline">
            VAT registered company<br>
            2779B R. FERNANDEZ ST. CORNER BENITA ST. GAGALANGIN, TONDO, MANILA, Philippines, 1013
          </p>
          <div class="footer-social" aria-label="Social media links">
            <a href="https://www.facebook.com/profile.php?id=100063787431490"
               target="_blank" rel="noopener" aria-label="Facebook">
              <i class="fab fa-facebook-f"></i>
            </a>
          </div>
        </div>

        <!-- Contact Info -->
        <div class="footer-col">
          <h5>Contact Information</h5>
          <div class="footer-contact-item">
            <i class="fas fa-envelope icon" aria-hidden="true"></i>
            <a href="mailto:salesandmarketing@creativekit3a.com">salesandmarketing@creativekit3a.com</a>
          </div>
          <div class="footer-contact-item">
            <i class="fas fa-mobile-alt icon" aria-hidden="true"></i>
            <a href="tel:+639177142774">+63 917 714 2774</a>
          </div>
          <div class="footer-contact-item">
            <i class="fas fa-phone icon" aria-hidden="true"></i>
            <a href="tel:+639339927410">+63 933 992 7410</a>
          </div>
        </div>

        <!-- Customer Help -->
        <div class="footer-col">
          <h5>Customer Help</h5>
          <ul>
            <li><a href="/faq/">About</a></li>
            <li><a href="/product-category/accessories-gadgets">All Categories</a></li>
            <li><a href="/contact-us/">Contact Us</a></li>
          </ul>
        </div>

      </div>
    </div>
  </div>

  <hr class="footer-divider">

  <div class="container">
    <div class="footer-bottom">
      <p>&copy; Copyright <?= date('Y') ?>. CreativeKit3A.shop All Rights Reserved.</p>
      <div class="footer-payments" aria-label="Accepted payment methods">
        <img src="https://pinoyballers.com/wp-content/uploads/2024/04/mastercard.png" alt="Mastercard">
        <img src="https://pinoyballers.com/wp-content/uploads/2024/04/visa.png" alt="Visa">
        <img src="https://pinoyballers.com/wp-content/uploads/2024/04/paypal-new.png" alt="PayPal">
        <img src="https://pinoyballers.com/wp-content/uploads/2024/04/BPI.png" alt="BPI">
        <img src="https://pinoyballers.com/wp-content/uploads/2024/04/paymongo_gcash.png" alt="GCash">
        <img src="https://pinoyballers.com/wp-content/uploads/2024/04/paymongo_paymaya.png" alt="Maya">
      </div>
    </div>
  </div>
</footer>

<!-- ============================================================
     AUTH MODALS — Login & Register
     Opened via:  openModal('login-modal') / openModal('register-modal')
     Tab switch:  switchTab('login') / switchTab('register')
     Backend API: POST /api/auth/login  |  POST /api/auth/register
     ============================================================ -->

<!-- Overlay — clicking outside closes the open modal -->
<div class="lm-overlay" id="lm-overlay" onclick="closeAllModals()" aria-hidden="true"></div>

<!-- ── LOGIN MODAL ──────────────────────────────────────────── -->
<div class="lm-modal" id="login-modal" role="dialog" aria-modal="true" aria-labelledby="login-title">
  <div class="lm-card">
    <button class="lm-close" onclick="closeModal('login-modal')" aria-label="Close">&times;</button>

    <div class="lm-header">
      <span class="lm-brand">CreativeKit3A</span>
      <p class="lm-tagline">Sign in to your account</p>
    </div>

    <div class="lm-tabs" role="tablist">
      <button class="lm-tab active" id="tab-login"    role="tab" aria-selected="true"
              onclick="switchTab('login')">Sign In</button>
      <button class="lm-tab"        id="tab-register" role="tab" aria-selected="false"
              onclick="switchTab('register')">Register</button>
    </div>

    <!-- Sign In Panel -->
    <div class="lm-panel" id="panel-login" role="tabpanel">
      <div class="lm-field">
        <label for="lm-login-email"><i class="fas fa-envelope"></i> Email Address</label>
        <input type="email" id="lm-login-email" placeholder="you@email.com"
               autocomplete="email" required>
      </div>
      <div class="lm-field">
        <label for="lm-login-password"><i class="fas fa-lock"></i> Password</label>
        <div class="lm-pw-wrap">
          <input type="password" id="lm-login-password" placeholder="••••••••"
                 autocomplete="current-password" required>
          <button type="button" class="lm-pw-toggle"
                  onclick="togglePw('lm-login-password', this)" aria-label="Show password">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>
      <div class="lm-extras">
        <label class="lm-remember"><input type="checkbox" id="lm-remember"> Remember me</label>
        <a href="/forgot-password/" class="lm-forgot">Forgot password?</a>
      </div>
      <div class="lm-msg lm-msg--error" id="lm-login-error" hidden></div>
      <button class="btn btn-primary lm-submit" id="lm-login-btn" onclick="submitLogin()">
        <i class="fas fa-sign-in-alt"></i> Sign In
      </button>
      <p class="lm-switch">
        Don't have an account?
        <a href="#" onclick="closeModal('login-modal'); openModal('register-modal'); return false;">Register here</a>
      </p>
    </div>
  </div>
</div>

<!-- ── REGISTER MODAL ───────────────────────────────────────── -->
<div class="lm-modal" id="register-modal" role="dialog" aria-modal="true" aria-labelledby="register-title">
  <div class="lm-card">
    <button class="lm-close" onclick="closeModal('register-modal')" aria-label="Close">&times;</button>

    <div class="lm-header">
      <span class="lm-brand">CreativeKit3A</span>
      <p class="lm-tagline">Create your account</p>
    </div>

    <div class="lm-panel" id="panel-register" role="tabpanel">
      <div class="lm-field-row">
        <div class="lm-field">
          <label for="lm-reg-firstname"><i class="fas fa-user"></i> First Name</label>
          <input type="text" id="lm-reg-firstname" placeholder="Juan"
                 autocomplete="given-name" required>
        </div>
        <div class="lm-field">
          <label for="lm-reg-lastname"><i class="fas fa-user"></i> Last Name</label>
          <input type="text" id="lm-reg-lastname" placeholder="Dela Cruz"
                 autocomplete="family-name" required>
        </div>
      </div>
      <div class="lm-field">
        <label for="lm-reg-email"><i class="fas fa-envelope"></i> Email Address</label>
        <input type="email" id="lm-reg-email" placeholder="you@email.com"
               autocomplete="email" required>
      </div>
      <div class="lm-field">
        <label for="lm-reg-phone">
          <i class="fas fa-mobile-alt"></i> Mobile
          <span class="lm-optional">(optional)</span>
        </label>
        <input type="tel" id="lm-reg-phone" placeholder="+63 917 000 0000" autocomplete="tel">
      </div>
      <div class="lm-field">
        <label for="lm-reg-password"><i class="fas fa-lock"></i> Password</label>
        <div class="lm-pw-wrap">
          <input type="password" id="lm-reg-password" placeholder="At least 8 characters"
                 autocomplete="new-password" required>
          <button type="button" class="lm-pw-toggle"
                  onclick="togglePw('lm-reg-password', this)" aria-label="Show password">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <div class="lm-strength-bar"><div class="lm-strength-fill" id="lm-strength-fill"></div></div>
        <span class="lm-strength-label" id="lm-strength-label"></span>
      </div>
      <div class="lm-field">
        <label for="lm-reg-confirm"><i class="fas fa-lock"></i> Confirm Password</label>
        <div class="lm-pw-wrap">
          <input type="password" id="lm-reg-confirm" placeholder="Repeat your password"
                 autocomplete="new-password" required>
          <button type="button" class="lm-pw-toggle"
                  onclick="togglePw('lm-reg-confirm', this)" aria-label="Show password">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>
      <div class="lm-terms">
        <label>
          <input type="checkbox" id="lm-reg-terms" required>
          I agree to the <a href="/terms-conditions/" target="_blank">Terms &amp; Conditions</a>
        </label>
      </div>
      <div class="lm-msg lm-msg--error"  id="lm-reg-error"   hidden></div>
      <div class="lm-msg lm-msg--success" id="lm-reg-success" hidden></div>
      <button class="btn btn-primary lm-submit" id="lm-reg-btn" onclick="submitRegister()">
        <i class="fas fa-user-plus"></i> Create Account
      </button>
      <p class="lm-switch">
        Already have an account?
        <a href="#" onclick="closeModal('register-modal'); openModal('login-modal'); return false;">Sign in here</a>
      </p>
    </div>
  </div>
</div>

<?php
// Include Request a Quote modal
include __DIR__ . '/quote-modal.php';
?>

<!-- ============================================================
     SCRIPTS — load main.js & quote-modal.js, then page-specific scripts
     ============================================================ -->
<script src="/assets/js/main.js"></script>
<script src="/assets/js/quote-modal.js"></script>

<?php foreach ($extra_js as $js): ?>
  <script src="<?= htmlspecialchars($js) ?>"></script>
<?php endforeach; ?>

</body>
</html>
