/**
 * ============================================================
 * assets/js/product-form.js — Dedicated JavaScript for Product Form
 * Paired with: /pages/product-form.php
 * Demonstrates:
 *   - 'submit' event: Client-side validation blocking invalid submissions
 *   - 'change' event: Dependent category selection auto-suggesting SKU & specs
 *   - 'change' event: Real-time image preview switcher
 *   - 'click' event: SKU auto-generator
 *   - 'input' event: Real-time error clearing
 * ============================================================
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  const productForm     = document.getElementById('product-form');
  const nameInput       = document.getElementById('prod-name');
  const catSelect       = document.getElementById('prod-category');
  const skuInput        = document.getElementById('prod-sku');
  const priceInput      = document.getElementById('prod-price');
  const specsInput      = document.getElementById('prod-specs');
  const imageSelect     = document.getElementById('prod-image');
  const livePreviewImg  = document.getElementById('img-live-preview');
  const genSkuBtn       = document.getElementById('btn-gen-sku');
  const saveBtn         = document.getElementById('btn-save-product');

  const clientErrorBox  = document.getElementById('client-form-error');
  const clientErrorList = document.getElementById('client-error-list');

  // Category to SKU Prefix & Spec Defaults Mapping
  const categoryHelpers = {
    'car-accessories':      { prefix: 'ACC-CC', spec: '12V-24V Input • Smart Charging IC' },
    'computer-accessories': { prefix: 'ACC-PC', spec: 'Plug & Play • High-Speed USB 3.0' },
    'desk-accessories':     { prefix: 'ACC-DK', spec: 'Eco Material • Desktop Organizer' },
    'mobile-accessories':   { prefix: 'ACC-MB', spec: 'Fast Charging • Compact Portable' },
    'gadgets':              { prefix: 'ACC-GD', spec: 'Rechargeable Battery • Portable' },
    'accessories-gadgets':  { prefix: 'ACC-GEN',spec: 'Custom Logo Branding Included' },
    'apparel':              { prefix: 'APP-COR',spec: 'Cotton/Polyester Blend • Custom Embroidery' },
    'bags-pouches':         { prefix: 'BAG-TOT',spec: 'Eco-Friendly Canvas • Reinforced Handles' },
    'drinkware':            { prefix: 'DRK-TUM',spec: 'Double-Walled Insulated • BPA-Free' },
    'giftset':              { prefix: 'GFT-SET',spec: 'Curated 3-Piece Executive Gift Set' },
    'home-living':          { prefix: 'HOM-LIV',spec: 'Household Grade • Custom Box Packaging' },
    'pen-paper':            { prefix: 'PEN-BAM',spec: 'Bamboo Barrel • Blue/Black Ink Cartridge' },
    'pu-leather':           { prefix: 'LEA-CRD',spec: 'Premium PU Leather • Debossed Logo' },
  };

  // ============================================================
  // 1. Dependent Category Handler (Event 3: 'change')
  //    Suggests SKU prefix and spec details dynamically.
  // ============================================================
  if (catSelect) {
    catSelect.addEventListener('change', function () {
      const selectedOption = this.options[this.selectedIndex];
      const slug = selectedOption ? selectedOption.getAttribute('data-slug') : '';

      if (slug && categoryHelpers[slug]) {
        const helper = categoryHelpers[slug];

        // Suggest SKU if empty or default
        if (skuInput && (!skuInput.value || skuInput.value.startsWith('PRD-'))) {
          const rand = Math.floor(100 + Math.random() * 900);
          skuInput.value = `${helper.prefix}-${rand}`;
          skuInput.classList.remove('is-invalid');
        }

        // Suggest specs if empty
        if (specsInput && !specsInput.value.trim()) {
          specsInput.value = helper.spec;
        }
      }
    });
  }

  // ============================================================
  // 2. Real-Time Image Preview Switcher (Event 3: 'change')
  // ============================================================
  if (imageSelect && livePreviewImg) {
    imageSelect.addEventListener('change', function () {
      const selectedPath = this.value;
      if (selectedPath) {
        livePreviewImg.style.opacity = '0';
        setTimeout(() => {
          livePreviewImg.src = selectedPath;
          livePreviewImg.style.opacity = '1';
        }, 150);
      }
    });
  }

  // ============================================================
  // 3. Random SKU Generator (Event 1: 'click')
  // ============================================================
  if (genSkuBtn && skuInput) {
    genSkuBtn.addEventListener('click', () => {
      const selectedOption = catSelect ? catSelect.options[catSelect.selectedIndex] : null;
      const slug = selectedOption ? selectedOption.getAttribute('data-slug') : '';
      const prefix = (slug && categoryHelpers[slug]) ? categoryHelpers[slug].prefix : 'CK3A';
      const rand = Math.floor(1000 + Math.random() * 9000);

      skuInput.value = `${prefix}-${rand}`;
      skuInput.classList.remove('is-invalid');

      // Visual flash
      skuInput.style.borderColor = 'var(--orange-primary)';
      setTimeout(() => skuInput.style.borderColor = '', 400);
    });
  }

  // ============================================================
  // 4. Input Error Clearing (Event 4: 'input')
  // ============================================================
  [nameInput, catSelect, skuInput, priceInput].forEach((input) => {
    if (input) {
      input.addEventListener('input', () => {
        input.classList.remove('is-invalid');
        const errSpan = document.getElementById(input.id.replace('prod-', '') + '-error');
        if (errSpan) errSpan.textContent = '';
      });
    }
  });

  // ============================================================
  // 5. Client-Side Form Validation (Event 2: 'submit')
  //    REQUIRED: Blocks submission, validates inputs, shows feedback.
  // ============================================================
  if (productForm) {
    productForm.addEventListener('submit', (e) => {
      let isValid = true;
      const errorList = [];

      // Reset error styles
      document.querySelectorAll('.field-error').forEach(span => span.textContent = '');
      document.querySelectorAll('.form-control').forEach(ctrl => ctrl.classList.remove('is-invalid'));
      if (clientErrorBox) clientErrorBox.style.display = 'none';

      // 1. Name validation (>= 3 chars)
      const nameVal = nameInput ? nameInput.value.trim() : '';
      if (!nameVal || nameVal.length < 3) {
        isValid = false;
        if (nameInput) nameInput.classList.add('is-invalid');
        const err = document.getElementById('name-error');
        if (err) err.textContent = 'Product name must be at least 3 characters.';
        errorList.push('Product name is required (minimum 3 characters).');
      }

      // 2. Category validation
      const catVal = catSelect ? catSelect.value : '';
      if (!catVal || catVal === '0') {
        isValid = false;
        if (catSelect) catSelect.classList.add('is-invalid');
        const err = document.getElementById('category-error');
        if (err) err.textContent = 'Please choose a category.';
        errorList.push('A valid category must be selected.');
      }

      // 3. SKU validation
      const skuVal = skuInput ? skuInput.value.trim() : '';
      const skuRegex = /^[A-Za-z0-9-_]{3,30}$/;
      if (!skuVal) {
        isValid = false;
        if (skuInput) skuInput.classList.add('is-invalid');
        const err = document.getElementById('sku-error');
        if (err) err.textContent = 'Product SKU code is required.';
        errorList.push('SKU code cannot be blank.');
      } else if (!skuRegex.test(skuVal)) {
        isValid = false;
        if (skuInput) skuInput.classList.add('is-invalid');
        const err = document.getElementById('sku-error');
        if (err) err.textContent = 'SKU may only contain letters, numbers, and dashes.';
        errorList.push('SKU format is invalid (letters, numbers, dashes only).');
      }

      // 4. Price validation (numeric, > 0)
      const priceVal = priceInput ? parseFloat(priceInput.value) : 0;
      if (isNaN(priceVal) || priceVal <= 0) {
        isValid = false;
        if (priceInput) priceInput.classList.add('is-invalid');
        const err = document.getElementById('price-error');
        if (err) err.textContent = 'Price must be a valid number greater than ₱0.00.';
        errorList.push('Base unit price must be greater than zero.');
      }

      // If invalid, block form submission and show feedback
      if (!isValid) {
        e.preventDefault();

        if (clientErrorBox && clientErrorList) {
          clientErrorList.innerHTML = `
            <strong><i class="fas fa-exclamation-triangle"></i> Cannot Save Product:</strong>
            <ul style="margin-top:6px; padding-left:18px;">
              ${errorList.map(item => `<li>${item}</li>`).join('')}
            </ul>
          `;
          clientErrorBox.style.display = 'block';
          clientErrorBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        return false;
      }

      // If valid, show loading on submit button
      if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving to Database...';
      }
    });
  }

});
