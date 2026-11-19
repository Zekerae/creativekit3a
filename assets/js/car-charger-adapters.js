/* ============================================================
   car-charger-adapters.js — Product Items / Variants Page renderer

   HOW IT WORKS:
   1. product/car-charger-adapters.php reads ?item=PRODUCT_SLUG,
      queries products + product_variants + categories from the
      single creativekit3a_db database (via config/db.php) and prints
      window.ITEMS_DATA and window.SIBLING_DATA.
   2. This file only renders: breadcrumb, hero banner, variant
      filter tabs, item cards, and "You May Also Like".
   To add/edit items, use the database — there is no hardcoded data here.
   ============================================================ */

(function () {
  'use strict';

  /* ============================================================
     ITEMS DATA — from MySQL (see product/car-charger-adapters.php)
       ITEMS_DATA[categorySlug][itemSlug] = {
         name, eyebrow, catName, price, heroImg, desc,
         variantTypes: [ { key, label } ],
         items: [ { id, name, specs, price, priceValue, img, badge, variantType } ]
       }
  ============================================================ */
  const ITEMS_DATA   = window.ITEMS_DATA   || {};
  const SIBLING_DATA = window.SIBLING_DATA || {};

  /** Escape DB text before inserting it into innerHTML */
  function esc(str) {
    return String(str ?? "").replace(/[&<>"']/g, ch => ({ "&":"&amp;", "<":"&lt;", ">":"&gt;", "\"":"&quot;", "'":"&#39;" }[ch]));
  }


  /* ============================================================
     HELPER: Resolve the current product
     The PHP page already validated ?item=SLUG against the database
     and emits exactly one product, so read its keys from ITEMS_DATA.
  ============================================================ */
  function getParams() {
    const cat  = Object.keys(ITEMS_DATA)[0] || '';
    const item = cat ? (Object.keys(ITEMS_DATA[cat])[0] || '') : '';
    return { cat, item };
  }

  /** Open the shared "Request a Quote" modal (includes/quote-modal.php) */
  function openQuote() {
    const params = getParams();
    const data   = params.cat ? ITEMS_DATA[params.cat][params.item] : null;
    if (data && typeof window.openQuoteFor === 'function') window.openQuoteFor(data.id);
    else if (typeof window.openModal === 'function') window.openModal('quote-modal');
  }


  /* ============================================================
     RENDER BREADCRUMB
     Updates: Home > Category > Subcategory > Item Name
  ============================================================ */
  function renderBreadcrumb(params, itemData) {
    const bcCatLink    = document.getElementById('bc-category-link');
    const bcSubcatLink = document.getElementById('bc-subcat-link');
    const bcItemName   = document.getElementById('bc-item-name');
    const catUrl       = (window.BASE_URL || '') + '/category-page.php?cat=' + encodeURIComponent(params.cat);

    if (bcCatLink) {
      bcCatLink.textContent = itemData.catName;
      bcCatLink.href = catUrl;
    }
    if (bcSubcatLink) {
      bcSubcatLink.textContent = itemData.eyebrow;
      bcSubcatLink.href = catUrl;
    }
    if (bcItemName) bcItemName.textContent = itemData.name;

    document.title = itemData.name + ' | CreativeKit3A';
  }


  /* ============================================================
     RENDER HERO BANNER
     Populates the item hero section at the top of the page.
     CHANGED: Added onerror on heroImg so a missing image shows
              a styled placeholder instead of a broken icon.
  ============================================================ */
  function renderHero(itemData) {
    const title   = document.getElementById('item-hero-title');
    const eyebrow = document.getElementById('item-eyebrow');
    const desc    = document.getElementById('item-hero-desc');
    const price   = document.getElementById('item-hero-price');
    const img     = document.getElementById('item-hero-img');

    if (title)   title.textContent   = itemData.name;
    if (eyebrow) eyebrow.textContent = itemData.eyebrow;
    if (desc)    desc.textContent    = itemData.desc;
    if (price)   price.textContent   = itemData.price;
    if (img) {
      img.src = itemData.heroImg;
      img.alt = itemData.name;
      // CHANGED: if the heroImg path is wrong/missing, swap in a visible placeholder
      img.onerror = function () {
        this.style.display = 'none';
        const ph = document.createElement('div');
        ph.className = 'item-hero-img-placeholder';
        ph.innerHTML = '<i class="fas fa-image"></i><span>No image set</span>';
        this.parentNode.appendChild(ph);
      };
    }
  }


  /* ============================================================
     RENDER VARIANT FILTER TABS
     If the item has variantTypes, renders filter tabs.
     "All" tab always shown first.
  ============================================================ */
  let activeVariantType = ''; // '' means show all

  function renderVariantTabs(itemData) {
    const container = document.getElementById('item-variants-tabs-container');
    if (!container) return;
    container.innerHTML = '';

    // Hide the tab bar if no variant types defined
    const wrapper = document.getElementById('item-variants-tabs-wrapper');
    if (!itemData.variantTypes || !itemData.variantTypes.length) {
      if (wrapper) wrapper.style.display = 'none';
      return;
    }
    if (wrapper) wrapper.style.display = '';

    // "All" tab
    const allBtn = document.createElement('button');
    allBtn.className   = 'item-variant-tab' + (!activeVariantType ? ' active' : '');
    allBtn.dataset.key = '';
    allBtn.innerHTML   = 'All <span class="vtab-count">' + itemData.items.length + '</span>';
    allBtn.addEventListener('click', () => {
      activeVariantType = '';
      renderVariantTabs(itemData);
      renderItemsGrid(itemData, getSortValue());
    });
    container.appendChild(allBtn);

    // One tab per variant type
    itemData.variantTypes.forEach(vt => {
      const count = itemData.items.filter(i => i.variantType === vt.key).length;
      const btn = document.createElement('button');
      btn.className   = 'item-variant-tab' + (activeVariantType === vt.key ? ' active' : '');
      btn.dataset.key = vt.key;
      btn.innerHTML   = esc(vt.label) + ' <span class="vtab-count">' + count + '</span>';
      btn.addEventListener('click', () => {
        activeVariantType = vt.key;
        renderVariantTabs(itemData);
        renderItemsGrid(itemData, getSortValue());
      });
      container.appendChild(btn);
    });
  }


  /* ============================================================
     BUILD INDIVIDUAL ITEM CARD
     Renders a single item variant card.
     CHANGED: Added onerror fallback on <img> so that if the image
              file is missing, a grey placeholder is shown instead
              of a broken image icon.
  ============================================================ */
  function buildItemCard(item) {
    const badge = item.badge
      ? '<span class="item-card-badge">' + esc(item.badge) + '</span>'
      : '';

    // onerror hides the broken img and shows the .item-card-img-placeholder div instead
    return `
      <div class="item-card" data-variant-id="${esc(item.id)}">
        <div class="item-card-img">
          <img src="${esc(item.img)}" alt="${esc(item.name)}" loading="lazy"
               onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <div class="item-card-img-placeholder" style="display:none;">
            <i class="fas fa-image"></i>
            <span>No image yet</span>
          </div>
          ${badge}
        </div>
        <div class="item-card-body">
          <div class="item-card-title">${esc(item.name)}</div>
          <div class="item-card-specs">${esc(item.specs)}</div>
          <div class="item-card-price">${esc(item.price)}</div>
        </div>
        <div class="item-card-footer">
          <span class="customize-btn"><i class="fas fa-chevron-right"></i> CUSTOMIZE</span>
        </div>
      </div>`;
  }

  /** CUSTOMIZE / card click → open the quote request modal */
  function bindItemClicks(grid) {
    grid.querySelectorAll('.item-card').forEach(card => {
      card.style.cursor = 'pointer';
      card.addEventListener('click', openQuote);
    });
  }


  /* ============================================================
     RENDER ITEMS GRID
     Populates the main grid with item cards.
     Groups by variantType when showing "All".
  ============================================================ */
  function renderItemsGrid(itemData, sortVal) {
    const grid = document.getElementById('items-grid');
    if (!grid) return;
    grid.innerHTML = '';

    let items = activeVariantType
      ? itemData.items.filter(i => i.variantType === activeVariantType)
      : itemData.items;

    // Apply sort
    if (sortVal === 'name-asc')  items = [...items].sort((a,b) => a.name.localeCompare(b.name));
    if (sortVal === 'name-desc') items = [...items].sort((a,b) => b.name.localeCompare(a.name));
    if (sortVal === 'price-asc')  items = [...items].sort((a,b) => a.priceValue - b.priceValue);
    if (sortVal === 'price-desc') items = [...items].sort((a,b) => b.priceValue - a.priceValue);

    // Update count label
    const countEl = document.getElementById('items-showing-count');
    const nameEl  = document.getElementById('items-showing-name');
    if (countEl) countEl.textContent = items.length;
    if (nameEl)  nameEl.textContent  = itemData.name;

    if (!items.length) {
      grid.innerHTML = '<div class="items-no-results"><i class="fas fa-box-open"></i><p>No variants found.</p></div>';
      return;
    }

    // Group by variantType when showing all, if variantTypes exist
    if (!activeVariantType && itemData.variantTypes && itemData.variantTypes.length) {
      itemData.variantTypes.forEach(vt => {
        const group = items.filter(i => i.variantType === vt.key);
        if (!group.length) return;

        // Section heading
        const heading = document.createElement('div');
        heading.className = 'items-type-heading';
        heading.innerHTML = `<h3>${esc(vt.label)}</h3><span class="type-count">${group.length}</span>`;
        grid.appendChild(heading);

        group.forEach(item => {
          grid.insertAdjacentHTML('beforeend', buildItemCard(item));
        });
      });
    } else {
      // Just render flat
      items.forEach(item => {
        grid.insertAdjacentHTML('beforeend', buildItemCard(item));
      });
    }

    bindItemClicks(grid);
  }


  /* ============================================================
     RENDER RELATED PRODUCTS
     Shows sibling sub-category cards from the same category
     so the user can easily browse nearby products.
     SIBLING_DATA comes from the PHP page (same main category,
     current product excluded by the SQL query).
  ============================================================ */

  function renderRelated(params, currentItemSlug) {
    const relatedGrid = document.getElementById('related-grid');
    if (!relatedGrid) return;
    relatedGrid.innerHTML = '';

    const siblings = (SIBLING_DATA[params.cat] || [])
      .filter(s => s.slug !== currentItemSlug);

    if (!siblings.length) {
      document.getElementById('related-section').style.display = 'none';
      return;
    }

    siblings.forEach(sib => {
      const card = document.createElement('div');
      card.className = 'product-card'; // reuse existing style from style.css
      card.innerHTML = `
        <div class="product-card-img">
          <img src="${esc(sib.img)}" alt="${esc(sib.name)}" loading="lazy">
        </div>
        <div class="product-card-body">
          <div class="product-card-title">${esc(sib.name)}</div>
          <div class="product-card-price">${esc(sib.price)}</div>
        </div>
        <div class="product-card-footer">
          <span class="customize-btn"><i class="fas fa-chevron-right"></i> VIEW</span>
        </div>`;
      card.style.cursor = 'pointer';
      card.addEventListener('click', () => { window.location.href = sib.url; });
      relatedGrid.appendChild(card);
    });
  }


  /* ============================================================
     HELPER: Get current sort dropdown value
  ============================================================ */
  function getSortValue() {
    const sel = document.getElementById('items-sort-select');
    return sel ? sel.value : 'default';
  }


  /* ============================================================
     SEARCH: Live filter on items grid
  ============================================================ */
  function initSearch(itemData) {
    const input = document.getElementById('items-search-input');
    if (!input) return;

    input.addEventListener('input', function () {
      const q = this.value.trim().toLowerCase();
      if (!q) {
        renderItemsGrid(itemData, getSortValue());
        return;
      }

      const grid = document.getElementById('items-grid');
      if (!grid) return;
      grid.innerHTML = '';

      const results = itemData.items.filter(
        i => i.name.toLowerCase().includes(q) || (i.specs || '').toLowerCase().includes(q)
      );

      if (!results.length) {
        grid.innerHTML = '<div class="items-no-results"><i class="fas fa-search"></i><p>No items match "<strong>' + esc(q) + '</strong>"</p></div>';
        return;
      }

      results.forEach(item => grid.insertAdjacentHTML('beforeend', buildItemCard(item)));
      bindItemClicks(grid);
    });
  }


  /* ============================================================
     INIT — Runs on page load
  ============================================================ */
  (function init() {
    const params   = getParams();
    const itemData = params.cat ? ITEMS_DATA[params.cat][params.item] : null;

    // Guard: no product data from the database
    if (!itemData) {
      console.warn('[car-charger-adapters.js] No product data received from the database.');
      return;
    }

    // Hero "Request a Quote" button
    const heroCta = document.getElementById('item-hero-cta');
    if (heroCta) heroCta.addEventListener('click', openQuote);

    // Render all sections
    renderBreadcrumb(params, itemData);
    renderHero(itemData);
    renderVariantTabs(itemData);
    renderItemsGrid(itemData, 'default');
    renderRelated(params, params.item);
    initSearch(itemData);

    // Sort dropdown listener
    const sortSel = document.getElementById('items-sort-select');
    if (sortSel) {
      sortSel.addEventListener('change', function () {
        renderItemsGrid(itemData, this.value);
      });
    }
  })();

})();

