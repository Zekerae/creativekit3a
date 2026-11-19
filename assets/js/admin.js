/**
 * ============================================================
 * assets/js/admin.js — Staff portal shell (all pages in pages/)
 *   - Off-canvas sidebar toggle on tablets/phones
 *   - Closes on overlay click, Escape, or when a nav link is chosen
 * ============================================================
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {
  const shell   = document.getElementById('admin-shell');
  const menuBtn = document.getElementById('admin-menu-btn');
  const overlay = document.getElementById('admin-sidebar-overlay');
  if (!shell || !menuBtn) return;

  const setOpen = (open) => {
    shell.classList.toggle('sidebar-open', open);
    menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.body.style.overflow = open ? 'hidden' : '';
  };

  menuBtn.addEventListener('click', () => setOpen(!shell.classList.contains('sidebar-open')));
  overlay?.addEventListener('click', () => setOpen(false));
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setOpen(false); });
  shell.querySelectorAll('.admin-nav-link').forEach((link) => link.addEventListener('click', () => setOpen(false)));
});
