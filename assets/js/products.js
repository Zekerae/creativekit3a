/**
 * ============================================================
 * assets/js/products.js — Dedicated JavaScript for Product Catalog
 * Paired with: /pages/products.php
 * Demonstrates:
 *   - 'click' event: Delete confirmation modal with cancel prevention
 *   - 'input' event: Real-time instant client-side table filter
 *   - 'change' event: Category and status dropdown filtering
 *   - 'mouseover' / 'mouseout': Interactive row & spec preview
 * ============================================================
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  const searchInput    = document.getElementById('search-input');
  const clearSearchBtn = document.getElementById('clear-search-btn');
  const catSelect      = document.getElementById('category-select');
  const statusSelect   = document.getElementById('status-select');
  const productRows    = document.querySelectorAll('.product-row');
  const productCountEl = document.getElementById('product-count');
  const liveFilterTag  = document.getElementById('live-filter-tag');
  const noMatchesBox   = document.getElementById('dynamic-no-matches');
  const productsTable  = document.getElementById('products-table');

  // ============================================================
  // 1. DELETE CONFIRMATION MODAL (Event 1: 'click')
  //    Intercepts click, opens custom confirmation dialog.
  //    Canceling prevents deletion. Confirming proceeds.
  // ============================================================
  const deleteModal    = document.getElementById('delete-confirm-modal');
  const modalProdName  = document.getElementById('modal-del-prod-name');
  const modalProdSku   = document.getElementById('modal-del-prod-sku');
  const modalProdId    = document.getElementById('modal-del-prod-id');
  const cancelDelBtn   = document.getElementById('btn-cancel-del');
  const closeDelBtn    = document.getElementById('btn-close-del-modal');

  // Open modal on delete button click
  document.querySelectorAll('.btn-open-delete-modal').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();

      const prodId  = btn.getAttribute('data-id');
      const prodName = btn.getAttribute('data-name');
      const prodSku  = btn.getAttribute('data-sku');

      if (modalProdId)   modalProdId.value = prodId;
      if (modalProdName) modalProdName.textContent = prodName || 'Unnamed Product';
      if (modalProdSku)  modalProdSku.textContent = 'SKU: ' + (prodSku || 'N/A');

      if (deleteModal) {
        deleteModal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    });
  });

  // Cancel deletion (Prevents deletion)
  function closeDeleteModal() {
    if (deleteModal) {
      deleteModal.classList.remove('active');
      document.body.style.overflow = '';
      if (modalProdId) modalProdId.value = '';
    }
  }

  if (cancelDelBtn) {
    cancelDelBtn.addEventListener('click', closeDeleteModal);
  }
  if (closeDelBtn) {
    closeDelBtn.addEventListener('click', closeDeleteModal);
  }
  if (deleteModal) {
    deleteModal.addEventListener('click', (e) => {
      if (e.target === deleteModal) closeDeleteModal();
    });
  }

  // Close modal with Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && deleteModal && deleteModal.classList.contains('active')) {
      closeDeleteModal();
    }
  });

  // ============================================================
  // 2. Real-Time Table Search (Event 4: 'input')
  //    Filters table rows dynamically as the user types.
  // ============================================================
  function performClientFilter() {
    const term = searchInput ? searchInput.value.trim().toLowerCase() : '';
    const selectedCat = catSelect ? catSelect.value : '0';
    const selectedStatus = statusSelect ? statusSelect.value.toLowerCase() : '';

    if (clearSearchBtn) {
      clearSearchBtn.style.display = term ? 'block' : 'none';
    }

    const isFiltering = (term !== '' || selectedCat !== '0' || selectedStatus !== '');
    if (liveFilterTag) {
      liveFilterTag.style.display = isFiltering ? 'inline-flex' : 'none';
    }

    let visibleCount = 0;

    productRows.forEach((row) => {
      const name = row.getAttribute('data-name') || '';
      const sku = row.getAttribute('data-sku') || '';
      const cat = row.getAttribute('data-cat') || '0';
      const status = row.getAttribute('data-status') || '';

      const matchText = (!term || name.includes(term) || sku.includes(term));
      const matchCat = (selectedCat === '0' || cat === selectedCat);
      const matchStatus = (!selectedStatus || status === selectedStatus);

      if (matchText && matchCat && matchStatus) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    if (productCountEl) {
      productCountEl.textContent = visibleCount;
    }

    if (noMatchesBox && productsTable) {
      if (visibleCount === 0 && productRows.length > 0) {
        noMatchesBox.style.display = 'block';
        productsTable.style.display = 'none';
      } else {
        noMatchesBox.style.display = 'none';
        productsTable.style.display = '';
      }
    }
  }

  if (searchInput) {
    searchInput.addEventListener('input', performClientFilter);
  }

  if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', () => {
      searchInput.value = '';
      performClientFilter();
      searchInput.focus();
    });
  }

  // ============================================================
  // 3. Category & Status Dynamic Filter (Event 3: 'change')
  // ============================================================
  if (catSelect) {
    catSelect.addEventListener('change', performClientFilter);
  }
  if (statusSelect) {
    statusSelect.addEventListener('change', performClientFilter);
  }

  // ============================================================
  // 4. Interactive Row Micro-interactions (mouseover / mouseout)
  // ============================================================
  productRows.forEach((row) => {
    row.addEventListener('mouseover', () => {
      row.style.boxShadow = 'inset 3px 0 0 var(--orange-primary)';
    });

    row.addEventListener('mouseout', () => {
      row.style.boxShadow = '';
    });
  });

});
