/**
 * ============================================================
 * assets/js/dashboard.js — Dedicated JavaScript for Dashboard
 * Paired with: /pages/dashboard.php
 * Demonstrates:
 *   - 'input' event: Real-time search/filtering of activity items
 *   - 'click' event: Clear filter button
 *   - 'mouseover' / 'mouseout' events: KPI card elevation micro-interactions
 * ============================================================
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  const activityFilter = document.getElementById('activity-filter');
  const clearFilterBtn = document.getElementById('clear-activity-filter');
  const activityItems  = document.querySelectorAll('.activity-item');
  const noMatchMsg     = document.getElementById('activity-no-match');

  // ============================================================
  // 1. Real-Time Filter for Activity Log (input event)
  //    Filters items instantaneously as user types.
  // ============================================================
  if (activityFilter) {
    activityFilter.addEventListener('input', function () {
      const term = this.value.trim().toLowerCase();

      // Show/hide clear 'x' icon
      if (clearFilterBtn) {
        clearFilterBtn.style.display = term ? 'block' : 'none';
      }

      let matchCount = 0;

      activityItems.forEach((item) => {
        const searchData = item.getAttribute('data-search') || item.textContent.toLowerCase();

        if (!term || searchData.includes(term)) {
          item.style.display = 'flex';
          matchCount++;
        } else {
          item.style.display = 'none';
        }
      });

      if (noMatchMsg) {
        noMatchMsg.style.display = (matchCount === 0 && term) ? 'block' : 'none';
      }
    });

    // Clear filter on button click
    if (clearFilterBtn) {
      clearFilterBtn.addEventListener('click', () => {
        activityFilter.value = '';
        activityFilter.dispatchEvent(new Event('input'));
        activityFilter.focus();
      });
    }
  }

  // ============================================================
  // 2. Micro-interactions on KPI Cards (mouseover & mouseout)
  // ============================================================
  const kpiCards = document.querySelectorAll('.kpi-card');
  kpiCards.forEach((card) => {
    card.addEventListener('mouseover', () => {
      const icon = card.querySelector('.kpi-icon');
      if (icon) {
        icon.style.transition = 'transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1)';
        icon.style.transform = 'scale(1.15) rotate(3deg)';
      }
    });

    card.addEventListener('mouseout', () => {
      const icon = card.querySelector('.kpi-icon');
      if (icon) {
        icon.style.transform = 'scale(1) rotate(0deg)';
      }
    });
  });

});
