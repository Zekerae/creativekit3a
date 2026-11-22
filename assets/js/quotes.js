/**
 * ============================================================
 * assets/js/quotes.js — Dedicated JavaScript for Quote Management
 * Paired with: /pages/quotes.php
 * Demonstrates:
 *   - 'click' events: View Details modal & Delete confirmation modal
 *   - 'input' event: Real-time search filter for quote inquiries
 *   - 'change' event: Inline status changer
 *   - 'mouseover' / 'mouseout': Row micro-interactions
 * ============================================================
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  const searchInput    = document.getElementById('quotes-search');
  const clearSearchBtn = document.getElementById('clear-quotes-search');
  const quoteRows      = document.querySelectorAll('.quote-row');
  const quotesCountEl  = document.getElementById('quotes-count');
  const noMatchNotice  = document.getElementById('quotes-no-match');
  const quotesTable    = document.getElementById('quotes-table');

  // ============================================================
  // 1. View Quote Details Modal (Event 1: 'click')
  // ============================================================
  const viewModal       = document.getElementById('quote-detail-modal');
  const closeViewBtn    = document.getElementById('btn-close-view-modal');
  const closeViewFooter = document.getElementById('btn-close-view-footer');

  document.querySelectorAll('.btn-view-quote').forEach((btn) => {
    btn.addEventListener('click', () => {
      const name     = btn.getAttribute('data-name');
      const company  = btn.getAttribute('data-company');
      const email    = btn.getAttribute('data-email');
      const phone    = btn.getAttribute('data-phone');
      const product  = btn.getAttribute('data-product');
      const qty      = btn.getAttribute('data-qty');
      const budget   = btn.getAttribute('data-budget');
      const deadline = btn.getAttribute('data-deadline');
      const details  = btn.getAttribute('data-details');
      const status   = btn.getAttribute('data-status');

      document.getElementById('detail-customer-name').textContent = name || 'Customer';
      document.getElementById('detail-company').textContent = company || 'Individual';
      document.getElementById('detail-email').textContent = email || '—';
      document.getElementById('detail-phone').textContent = phone || '—';
      document.getElementById('detail-product').textContent = product || '—';
      document.getElementById('detail-qty').textContent = qty ? `${Number(qty).toLocaleString()} pcs` : '—';
      document.getElementById('detail-budget').textContent = budget || 'Flexible';
      document.getElementById('detail-deadline').textContent = deadline || 'Flexible';
      document.getElementById('detail-notes').textContent = details || 'No additional specifications provided.';

      const badge = document.getElementById('detail-status-badge');
      if (badge) {
        badge.className = `badge badge-${status}`;
        badge.textContent = status ? status.toUpperCase() : 'NEW';
      }

      if (viewModal) {
        viewModal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    });
  });

  function closeDetailModal() {
    if (viewModal) {
      viewModal.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  if (closeViewBtn) closeViewBtn.addEventListener('click', closeDetailModal);
  if (closeViewFooter) closeViewFooter.addEventListener('click', closeDetailModal);
  if (viewModal) {
    viewModal.addEventListener('click', (e) => {
      if (e.target === viewModal) closeDetailModal();
    });
  }

  // ============================================================
  // 2. Delete Quote Confirmation Modal (Event 1: 'click')
  //    Canceling prevents deletion.
  // ============================================================
  const deleteModal      = document.getElementById('delete-quote-modal');
  const deleteModalName  = document.getElementById('modal-del-quote-name');
  const deleteModalId    = document.getElementById('modal-del-quote-id');
  const cancelDelBtn     = document.getElementById('btn-cancel-del-quote');
  const closeDelBtn      = document.getElementById('btn-close-del-quote');

  document.querySelectorAll('.btn-open-delete-quote').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const quoteId = btn.getAttribute('data-id');
      const label   = btn.getAttribute('data-name');

      if (deleteModalId) deleteModalId.value = quoteId;
      if (deleteModalName) deleteModalName.textContent = label || `Quote #${quoteId}`;

      if (deleteModal) {
        deleteModal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    });
  });

  function closeDeleteModal() {
    if (deleteModal) {
      deleteModal.classList.remove('active');
      document.body.style.overflow = '';
      if (deleteModalId) deleteModalId.value = '';
    }
  }

  if (cancelDelBtn) cancelDelBtn.addEventListener('click', closeDeleteModal);
  if (closeDelBtn) closeDelBtn.addEventListener('click', closeDeleteModal);
  if (deleteModal) {
    deleteModal.addEventListener('click', (e) => {
      if (e.target === deleteModal) closeDeleteModal();
    });
  }

  // ============================================================
  // 3. Real-Time Search Filter (Event 4: 'input')
  // ============================================================
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const term = this.value.trim().toLowerCase();

      if (clearSearchBtn) {
        clearSearchBtn.style.display = term ? 'block' : 'none';
      }

      let visibleCount = 0;

      quoteRows.forEach((row) => {
        const customer = row.getAttribute('data-customer') || '';
        const company  = row.getAttribute('data-company') || '';
        const email    = row.getAttribute('data-email') || '';
        const product  = row.getAttribute('data-product') || '';

        const match = (!term || customer.includes(term) || company.includes(term) || email.includes(term) || product.includes(term));

        if (match) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });

      if (quotesCountEl) {
        quotesCountEl.textContent = visibleCount;
      }

      if (noMatchNotice && quotesTable) {
        if (visibleCount === 0 && quoteRows.length > 0) {
          noMatchNotice.style.display = 'block';
          quotesTable.style.display = 'none';
        } else {
          noMatchNotice.style.display = 'none';
          quotesTable.style.display = '';
        }
      }
    });

    if (clearSearchBtn) {
      clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
      });
    }
  }

  // ============================================================
  // 4. Row Hover Micro-interactions (mouseover & mouseout)
  // ============================================================
  quoteRows.forEach((row) => {
    row.addEventListener('mouseover', () => {
      row.style.boxShadow = 'inset 3px 0 0 var(--orange-primary)';
    });
    row.addEventListener('mouseout', () => {
      row.style.boxShadow = '';
    });
  });

});
