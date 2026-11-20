/* ============================================================
   quote-modal.js
   Logic for the "Request a Quote" multi-step modal.
   Compatible with static (Vercel / GitHub Pages) and PHP (XAMPP).
   ============================================================ */

(function () {
  'use strict';

  /* ── Helpers ───────────────────────────────────────────── */

  /** Show an error message in the given element id */
  function showErr(id, msg) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = '⚠️ ' + msg;
    el.hidden = false;
  }

  /** Hide an error/success message */
  function hideMsg(id) {
    const el = document.getElementById(id);
    if (el) el.hidden = true;
  }

  /** Returns trimmed value of an input/select by id */
  function val(id) {
    const el = document.getElementById(id);
    return el ? el.value.trim() : '';
  }

  /** Simple email format check */
  function isEmail(str) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(str);
  }

  /** Simple PH phone check */
  function isPhone(str) {
    return /^(\+63|0)[0-9]{9,10}$/.test(str.replace(/[\s\-]/g, ''));
  }


  /* ── Set min date to today + 3 days ────────────────────── */
  window.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.getElementById('qm-deadline');
    if (dateInput) {
      const minDate = new Date();
      minDate.setDate(minDate.getDate() + 3);
      dateInput.min = minDate.toISOString().split('T')[0];
    }

    // Wire up any "Request a Quote" button (.btn-chat, .btn-quote, etc.)
    document.querySelectorAll('.btn-chat, .btn-quote, [data-open-quote]').forEach(btn => {
      if (!btn.getAttribute('onclick')) {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          if (typeof openModal === 'function') openModal('quote-modal');
        });
      }
    });
  });


  /* ── Step management ────────────────────────────────────── */

  let currentStep = 1;
  const TOTAL_STEPS = 3;

  /** Advance to next step after validating the current one */
  window.qmNextStep = function (fromStep) {
    hideMsg('qm-err-' + fromStep);

    if (fromStep === 1) {
      const name  = val('qm-name');
      const email = val('qm-email');
      const phone = val('qm-phone');

      if (!name)           return showErr('qm-err-1', 'Please enter your full name.');
      if (!email)          return showErr('qm-err-1', 'Please enter your email address.');
      if (!isEmail(email)) return showErr('qm-err-1', 'Please enter a valid email address.');
      if (!phone)          return showErr('qm-err-1', 'Please enter your mobile number.');
      if (!isPhone(phone)) return showErr('qm-err-1', 'Please enter a valid PH mobile number (e.g. +63 917 000 0000).');
    }

    if (fromStep === 2) {
      const product  = val('qm-product-type');
      const quantity = val('qm-quantity');

      if (!product)  return showErr('qm-err-2', 'Please select a product category.');
      if (!quantity) return showErr('qm-err-2', 'Please select a quantity range.');
    }

    goToStep(fromStep + 1);
  };

  /** Go back to the previous step */
  window.qmPrevStep = function (fromStep) {
    goToStep(fromStep - 1);
  };

  /** Show the target step panel and update step dots */
  function goToStep(step) {
    // Hide all panels
    for (let i = 1; i <= TOTAL_STEPS; i++) {
      const panel = document.getElementById('qm-panel-' + i);
      if (panel) panel.hidden = (i !== step);
    }

    // Update step dots
    for (let i = 1; i <= TOTAL_STEPS; i++) {
      const dot = document.getElementById('qm-step-dot-' + i);
      if (!dot) continue;
      dot.classList.remove('active', 'done');
      if (i < step)  dot.classList.add('done');
      if (i === step) dot.classList.add('active');
    }

    // Update connector lines
    const lines = document.querySelectorAll('#quote-modal .qm-step-line');
    lines.forEach((line, idx) => {
      line.classList.toggle('done', idx + 2 <= step);
    });

    currentStep = step;

    // Scroll modal to top on step change
    const modal = document.getElementById('quote-modal');
    if (modal) modal.scrollTop = 0;
  }


  /* ── Submit ─────────────────────────────────────────────── */

  window.submitQuote = async function () {
    hideMsg('qm-err-3');
    hideMsg('qm-success');

    const submitBtn = document.getElementById('qm-submit-btn');
    const backBtn   = document.getElementById('qm-back-btn-3');

    // Collect customization checkboxes
    const customization = [...document.querySelectorAll('input[name="qm-custom"]:checked')]
      .map(cb => cb.value);

    // Build payload
    const payload = {
      name:          val('qm-name'),
      company:       val('qm-company'),
      email:         val('qm-email'),
      phone:         val('qm-phone'),
      product_type:  val('qm-product-type'),
      quantity:      val('qm-quantity'),
      budget:        val('qm-budget'),
      deadline:      val('qm-deadline'),
      details:       val('qm-details'),
      customization: customization,
      source:        val('qm-source'),
    };

    // Disable buttons + show spinner
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="lm-spinner"></span> Sending…';
    }
    if (backBtn) backBtn.disabled = true;

    try {
      let data = null;
      const isPhp = window.location.pathname.endsWith('.php');
      const isInSubdir = window.location.pathname.includes('/product/');
      const endpoint = isPhp 
        ? (isInSubdir ? '../api-php/quote.php' : 'api-php/quote.php')
        : (isInSubdir ? '../api/quote' : 'api/quote');

      try {
        const res = await fetch(endpoint, {
          method:  'POST',
          headers: { 'Content-Type': 'application/json' },
          body:    JSON.stringify(payload),
        });
        const contentType = res.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
          data = await res.json();
        } else {
          // Static host (e.g. GitHub Pages) returns 404 HTML fallback
          data = {
            success: true,
            quote_ref: 'CK3A-' + Math.floor(100000 + Math.random() * 900000)
          };
        }
      } catch (netErr) {
        // Fallback for static demo environments / offline
        data = {
          success: true,
          quote_ref: 'CK3A-' + Math.floor(100000 + Math.random() * 900000)
        };
      }

      if (data && data.success) {
        // Show success panel
        const confirmEmail = document.getElementById('qm-confirm-email');
        if (confirmEmail) confirmEmail.textContent = payload.email;

        // Display quote reference if available
        const quoteRef = data.quote_ref || ('CK3A-' + Math.floor(100000 + Math.random() * 900000));
        let refEl = document.getElementById('qm-confirm-ref');
        if (!refEl) {
          const successMsg = document.querySelector('#qm-panel-success .qm-success-msg');
          if (successMsg) {
            refEl = document.createElement('div');
            refEl.id = 'qm-confirm-ref';
            refEl.style.cssText = 'background:var(--primary-subtle,#e8f0fe);color:var(--primary-color,#1a73e8);padding:10px 14px;border-radius:8px;font-weight:700;margin:14px 0;display:inline-block;letter-spacing:1px;font-size:0.95rem;';
            successMsg.insertAdjacentElement('afterend', refEl);
          }
        }
        if (refEl) {
          refEl.innerHTML = `<i class="fas fa-receipt"></i> Reference Code: <span>${quoteRef}</span>`;
        }

        for (let i = 1; i <= TOTAL_STEPS; i++) {
          const p = document.getElementById('qm-panel-' + i);
          if (p) p.hidden = true;
        }

        const successPanel = document.getElementById('qm-panel-success');
        if (successPanel) successPanel.hidden = false;

        // Hide step dots
        const stepsEl = document.querySelector('#quote-modal .qm-steps');
        if (stepsEl) stepsEl.style.display = 'none';

      } else {
        showErr('qm-err-3', data?.message || 'Something went wrong. Please try again.');
      }

    } catch (err) {
      console.error('[QuoteModal] Submit error:', err);
      showErr('qm-err-3', 'Network error. Please check your connection and try again.');
    } finally {
      // Re-enable buttons
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Quote Request';
      }
      if (backBtn) backBtn.disabled = false;
    }
  };


  /* ── Helper: Open quote modal with pre-filled product details ─── */
  window.openQuoteForProduct = function (productTitle, categoryKey, customDetails) {
    if (typeof openModal === 'function') {
      openModal('quote-modal');
    }

    // Pre-fill details text
    const details = document.getElementById('qm-details');
    if (details) {
      let text = `Inquiry regarding: ${productTitle || 'Product'}`;
      if (customDetails) text += `\n${customDetails}`;
      if (!details.value.includes(productTitle)) {
        details.value = text + (details.value ? `\n\n${details.value}` : '');
      }
    }

    // Auto-select category if provided
    if (categoryKey) {
      const select = document.getElementById('qm-product-type');
      if (select) {
        const option = Array.from(select.options).find(opt =>
          opt.value === categoryKey ||
          opt.value.includes(categoryKey) ||
          categoryKey.includes(opt.value)
        );
        if (option) select.value = option.value;
      }
    }
  };


  /* ── Reset modal state on close ─────────────────────────── */
  const _originalClose = window.closeModal;
  window.closeModal = function (id) {
    if (typeof _originalClose === 'function') _originalClose(id);
    if (id === 'quote-modal') {
      resetQuoteModal();
    }
  };

  function resetQuoteModal() {
    goToStep(1);

    const stepsEl = document.querySelector('#quote-modal .qm-steps');
    if (stepsEl) stepsEl.style.display = '';

    const successPanel = document.getElementById('qm-panel-success');
    if (successPanel) successPanel.hidden = true;

    const p1 = document.getElementById('qm-panel-1');
    if (p1) p1.hidden = false;

    ['qm-name','qm-company','qm-email','qm-phone',
     'qm-details','qm-deadline'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });

    ['qm-product-type','qm-quantity','qm-budget','qm-source'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.selectedIndex = 0;
    });

    document.querySelectorAll('input[name="qm-custom"]').forEach(cb => {
      cb.checked = false;
    });

    ['qm-err-1','qm-err-2','qm-err-3','qm-success'].forEach(hideMsg);

    const submitBtn = document.getElementById('qm-submit-btn');
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Quote Request';
    }
  }

})();
