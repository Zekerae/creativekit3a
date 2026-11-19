/**
 * ============================================================
 * assets/js/home.js — Dedicated JavaScript for Homepage
 * Paired with: /index.php
 * Demonstrates:
 *   - 'click' events: Carousel dots and product card navigation
 *   - 'mouseover' / 'mouseout': Card micro-interactions
 * ============================================================
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  // ============================================================
  // 1. Hero Carousel Controller
  // ============================================================
  const slides = document.querySelectorAll('.hero-slide');
  const dots   = document.querySelectorAll('.carousel-dot');
  let currentSlide = 0;
  let carouselTimer = null;

  function showSlide(index) {
    if (!slides.length) return;

    slides.forEach((s) => s.classList.remove('active'));
    dots.forEach((d) => d.classList.remove('active'));

    currentSlide = (index + slides.length) % slides.length;
    slides[currentSlide].classList.add('active');
    if (dots[currentSlide]) dots[currentSlide].classList.add('active');
  }

  function nextSlide() {
    showSlide(currentSlide + 1);
  }

  function startAutoPlay() {
    stopAutoPlay();
    carouselTimer = setInterval(nextSlide, 5000);
  }

  function stopAutoPlay() {
    if (carouselTimer) clearInterval(carouselTimer);
  }

  dots.forEach((dot, idx) => {
    dot.addEventListener('click', () => {
      showSlide(idx);
      startAutoPlay();
    });
  });

  const carouselEl = document.querySelector('.hero-carousel');
  if (carouselEl) {
    carouselEl.addEventListener('mouseenter', stopAutoPlay);
    carouselEl.addEventListener('mouseleave', startAutoPlay);
  }

  startAutoPlay();

  // ============================================================
  // 2. Product Card Click Navigation (Event: 'click')
  // ============================================================
  document.querySelectorAll('.product-card').forEach((card) => {
    card.addEventListener('click', () => {
      const targetUrl = card.getAttribute('data-url');
      if (targetUrl) {
        window.location.href = targetUrl;
      }
    });
  });

  // ============================================================
  // 3. Card Elevation Micro-interactions (mouseover / mouseout)
  // ============================================================
  document.querySelectorAll('.product-card').forEach((card) => {
    card.addEventListener('mouseover', () => {
      const btn = card.querySelector('.customize-btn');
      if (btn) {
        btn.style.color = 'var(--orange-mid)';
        btn.style.transform = 'translateX(4px)';
        btn.style.transition = 'all 0.2s ease';
      }
    });

    card.addEventListener('mouseout', () => {
      const btn = card.querySelector('.customize-btn');
      if (btn) {
        btn.style.color = '';
        btn.style.transform = '';
      }
    });
  });

});
