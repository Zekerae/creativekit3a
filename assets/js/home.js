/**
 * ============================================================
 * assets/js/home.js — Dedicated JavaScript for the Homepage
 * Paired with: /index.php
 *   - Hero carousel: prev/next arrows, indicator dots, 7s autoplay,
 *     pause on hover, touch swipe (ported from the original main.js)
 *   - Product card click → product page (data-url)
 * ============================================================
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  // ============================================================
  // 1. Hero Carousel
  // ============================================================
  const AUTOPLAY_DELAY = 7000;
  const hero    = document.querySelector('.hero-section');
  const slides  = [...document.querySelectorAll('.hero-slide')];
  const dots    = [...document.querySelectorAll('.carousel-dot')];
  const prevBtn = document.querySelector('.carousel-btn.prev');
  const nextBtn = document.querySelector('.carousel-btn.next');
  let current = 0;
  let timer = null;

  function goTo(index) {
    if (!slides.length) return;
    slides[current].classList.remove('active');
    dots[current]?.classList.remove('active');
    dots[current]?.setAttribute('aria-selected', 'false');
    current = (index + slides.length) % slides.length;
    slides[current].classList.add('active');
    dots[current]?.classList.add('active');
    dots[current]?.setAttribute('aria-selected', 'true');
  }
  const next = () => goTo(current + 1);
  const prev = () => goTo(current - 1);
  const stop = () => { if (timer) clearInterval(timer); timer = null; };
  const start = () => { stop(); if (slides.length > 1) timer = setInterval(next, AUTOPLAY_DELAY); };

  if (slides.length) {
    if (slides.length < 2) {           // nothing to slide through
      prevBtn?.setAttribute('hidden', '');
      nextBtn?.setAttribute('hidden', '');
    }
    nextBtn?.addEventListener('click', () => { next(); start(); });
    prevBtn?.addEventListener('click', () => { prev(); start(); });
    dots.forEach((dot, i) => dot.addEventListener('click', () => { goTo(i); start(); }));

    hero?.addEventListener('mouseenter', stop);
    hero?.addEventListener('mouseleave', start);

    let touchX = 0;
    hero?.addEventListener('touchstart', (e) => { touchX = e.touches[0].clientX; }, { passive: true });
    hero?.addEventListener('touchend', (e) => {
      const diff = touchX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 40) { diff > 0 ? next() : prev(); start(); }
    }, { passive: true });

    start();
  }

  // ============================================================
  // 2. Product card click → product / category page
  // ============================================================
  document.querySelectorAll('.product-grid .product-card[data-url]').forEach((card) => {
    card.style.cursor = 'pointer';
    card.addEventListener('click', () => { window.location.href = card.dataset.url; });
  });
});
