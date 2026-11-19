/**
 * ============================================================
 * assets/js/global.js — Shared Reusable JavaScript Utilities
 * Includes:
 *   - Mobile Hamburger Menu Toggle (click event)
 *   - Modal Controller (openModal, closeModal, closeAllModals)
 *   - Global Tooltip System (mouseover & mouseout events)
 *   - Auto-dismiss Flash Alerts
 * ============================================================
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  // ============================================================
  // 1. Mobile Drawer Navigation Toggle (click event)
  // ============================================================
  const hamburgerBtn = document.querySelector('.hamburger');
  const mobileDrawer = document.querySelector('.mobile-drawer');
  const drawerOverlay = document.querySelector('.mobile-drawer-overlay');
  const drawerCloseBtn = document.querySelector('.mobile-drawer-close');

  function openDrawer() {
    if (mobileDrawer) mobileDrawer.classList.add('active');
    if (drawerOverlay) drawerOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    if (mobileDrawer) mobileDrawer.classList.remove('active');
    if (drawerOverlay) drawerOverlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  if (hamburgerBtn) {
    hamburgerBtn.addEventListener('click', openDrawer);
  }
  if (drawerCloseBtn) {
    drawerCloseBtn.addEventListener('click', closeDrawer);
  }
  if (drawerOverlay) {
    drawerOverlay.addEventListener('click', closeDrawer);
  }

  // ============================================================
  // 2. Global Tooltip System (mouseover & mouseout events)
  //    Demonstrates event-driven UI enhancement
  // ============================================================
  let tooltipEl = document.getElementById('app-tooltip');
  if (!tooltipEl) {
    tooltipEl = document.createElement('div');
    tooltipEl.id = 'app-tooltip';
    document.body.appendChild(tooltipEl);
  }

  // Delegate mouseover on document for any [data-tooltip] element
  document.addEventListener('mouseover', (e) => {
    const target = e.target.closest('[data-tooltip]');
    if (!target) return;

    const text = target.getAttribute('data-tooltip');
    if (!text) return;

    tooltipEl.textContent = text;
    tooltipEl.classList.add('visible');

    const rect = target.getBoundingClientRect();
    const tooltipRect = tooltipEl.getBoundingClientRect();

    // Position above target by default, or below if near top
    let top = rect.top - tooltipRect.height - 8;
    let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);

    if (top < 10) {
      top = rect.bottom + 8;
    }
    if (left < 10) {
      left = 10;
    } else if (left + tooltipRect.width > window.innerWidth - 10) {
      left = window.innerWidth - tooltipRect.width - 10;
    }

    tooltipEl.style.top = `${top}px`;
    tooltipEl.style.left = `${left}px`;
  });

  document.addEventListener('mouseout', (e) => {
    const target = e.target.closest('[data-tooltip]');
    if (!target) return;
    tooltipEl.classList.remove('visible');
  });

  // ============================================================
  // 3. Auto-dismiss Flash Alerts
  // ============================================================
  document.querySelectorAll('.alert').forEach((alert) => {
    const closeBtn = alert.querySelector('.alert-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-6px)';
        setTimeout(() => alert.remove(), 250);
      });
    }

    // Auto dismiss after 6 seconds if success or info
    if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
      setTimeout(() => {
        if (alert.parentNode) {
          alert.style.transition = 'all 0.4s ease';
          alert.style.opacity = '0';
          alert.style.transform = 'translateY(-6px)';
          setTimeout(() => alert.remove(), 400);
        }
      }, 6000);
    }
  });

});

// ============================================================
// 4. Global Modal Functions (accessible inline if needed)
// ============================================================
window.openModal = function (modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
};

window.closeModal = function (modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
};

window.closeAllModals = function () {
  document.querySelectorAll('.modal-overlay, .lm-modal').forEach((m) => {
    m.classList.remove('active');
  });
  document.body.style.overflow = '';
};
