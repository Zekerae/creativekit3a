/**
 * ============================================================
 * assets/js/login.js — Dedicated JavaScript for Login Portal
 * Paired with: /pages/login.php
 * Demonstrates:
 *   - 'submit' event for client-side form validation
 *   - 'click' events for password toggle & demo autofills
 *   - 'input' events for real-time error clearing
 * ============================================================
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  const loginForm      = document.getElementById('login-form');
  const emailInput     = document.getElementById('email');
  const passwordInput  = document.getElementById('password');
  const emailError     = document.getElementById('email-error');
  const passwordError  = document.getElementById('password-error');
  const clientErrorBox = document.getElementById('client-error-box');
  const clientErrorMsg = document.getElementById('client-error-msg');
  const submitBtn      = document.getElementById('btn-submit-login');

  const togglePwBtn    = document.getElementById('toggle-password');
  const togglePwIcon   = document.getElementById('toggle-pw-icon');
  const togglePwText   = document.getElementById('toggle-pw-text');

  const autofillAdmin  = document.getElementById('autofill-admin');
  const autofillStaff  = document.getElementById('autofill-staff');

  // ============================================================
  // 1. Password Visibility Toggle (click event)
  // ============================================================
  if (togglePwBtn && passwordInput) {
    togglePwBtn.addEventListener('click', () => {
      const isPassword = passwordInput.type === 'password';
      passwordInput.type = isPassword ? 'text' : 'password';

      if (togglePwIcon) {
        togglePwIcon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
      }
      if (togglePwText) {
        togglePwText.textContent = isPassword ? 'Hide' : 'Show';
      }
    });
  }

  // ============================================================
  // 2. Demo Credentials 1-Click Autofill (click events)
  // ============================================================
  function fillCredentials(email, pass, label) {
    if (emailInput && passwordInput) {
      emailInput.value = email;
      passwordInput.value = pass;

      // Clear any prior errors
      clearErrors();

      // Visual feedback: pulse the form fields
      emailInput.style.transition = 'all 0.3s ease';
      passwordInput.style.transition = 'all 0.3s ease';
      emailInput.style.borderColor = 'var(--orange-primary)';
      passwordInput.style.borderColor = 'var(--orange-primary)';

      setTimeout(() => {
        emailInput.style.borderColor = '';
        passwordInput.style.borderColor = '';
      }, 600);

      emailInput.focus();
    }
  }

  if (autofillAdmin) {
    autofillAdmin.addEventListener('click', () => {
      fillCredentials('admin@creativekit3a.com', 'admin123', 'Administrator');
    });
    autofillAdmin.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        autofillAdmin.click();
      }
    });
  }

  if (autofillStaff) {
    autofillStaff.addEventListener('click', () => {
      fillCredentials('maria@creativekit3a.com', 'staff123', 'Staff Member');
    });
    autofillStaff.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        autofillStaff.click();
      }
    });
  }

  // ============================================================
  // 3. Real-time Input Clearing (input event)
  // ============================================================
  function clearErrors() {
    if (emailInput) emailInput.classList.remove('is-invalid');
    if (passwordInput) passwordInput.classList.remove('is-invalid');
    if (emailError) emailError.textContent = '';
    if (passwordError) passwordError.textContent = '';
    if (clientErrorBox) clientErrorBox.style.display = 'none';
  }

  if (emailInput) {
    emailInput.addEventListener('input', () => {
      emailInput.classList.remove('is-invalid');
      if (emailError) emailError.textContent = '';
    });
  }

  if (passwordInput) {
    passwordInput.addEventListener('input', () => {
      passwordInput.classList.remove('is-invalid');
      if (passwordError) passwordError.textContent = '';
    });
  }

  // ============================================================
  // 4. Client-Side Form Validation (submit event)
  //    REQUIRED EVENT: Validates fields before sending to PHP.
  //    Blocks submission and displays clear error feedback.
  // ============================================================
  if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
      let isValid = true;
      const errors = [];

      clearErrors();

      const emailVal = emailInput.value.trim();
      const passVal  = passwordInput.value;

      // Email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailVal) {
        isValid = false;
        emailInput.classList.add('is-invalid');
        emailError.textContent = 'Email address is required.';
        errors.push('Please enter your work email.');
      } else if (!emailRegex.test(emailVal)) {
        isValid = false;
        emailInput.classList.add('is-invalid');
        emailError.textContent = 'Please enter a valid email format (e.g., name@domain.com).';
        errors.push('The email address format is invalid.');
      }

      // Password validation
      if (!passVal) {
        isValid = false;
        passwordInput.classList.add('is-invalid');
        passwordError.textContent = 'Password is required.';
        errors.push('Please enter your account password.');
      } else if (passVal.length < 6) {
        isValid = false;
        passwordInput.classList.add('is-invalid');
        passwordError.textContent = 'Password must be at least 6 characters.';
        errors.push('Password must be at least 6 characters long.');
      }

      // If invalid, block form submission
      if (!isValid) {
        e.preventDefault();

        if (clientErrorBox && clientErrorMsg) {
          clientErrorMsg.innerHTML = `
            <strong><i class="fas fa-exclamation-triangle"></i> Form Validation Error:</strong>
            <ul style="margin-top:4px; padding-left:18px;">
              ${errors.map(err => `<li>${err}</li>`).join('')}
            </ul>
          `;
          clientErrorBox.style.display = 'flex';
          clientErrorBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        return false;
      }

      // Valid: Show loading state on button
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authenticating...';
      }
    });
  }

});
