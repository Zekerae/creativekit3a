  (function () {
    'use strict';

    /* ============================================================
       CATEGORY DATA — loaded from MySQL (creativekit3a_db)
       category-page.php queries categories + products through
       config/db.php and prints them into window.CATEGORY_DATA.
       Shape: { slug: { name, icon, desc, count, subcats[{key,label}],
                        products[{id,name,price,img,url,subcat}] } }
       ============================================================ */
    const CATEGORY_DATA = window.CATEGORY_DATA || {};

    /** Escape DB text before inserting it into innerHTML */
    function esc(str) {
      return String(str ?? "").replace(/[&<>"']/g, ch => ({ "&":"&amp;", "<":"&lt;", ">":"&gt;", "\"":"&quot;", "'":"&#39;" }[ch]));
    }

    /* ============================================================
       DETECT WHICH CATEGORY WE'RE ON
       category-page.php resolves ?cat=SLUG against the categories
       table and passes the validated slug in window.ACTIVE_CATEGORY.
       ============================================================ */
    function getCategorySlug() {
      if (window.ACTIVE_CATEGORY && CATEGORY_DATA[window.ACTIVE_CATEGORY]) return window.ACTIVE_CATEGORY;
      return Object.keys(CATEGORY_DATA)[0] || '';
    }

    /* ============================================================
       RENDER HERO BANNER
       ============================================================ */
    function renderHero(cat) {
      document.title = cat.name + ' | CreativeKit3A';
      document.getElementById('hero-title').textContent         = cat.name;
      document.getElementById('hero-breadcrumb-current').textContent = cat.name;
      document.getElementById('hero-desc').textContent          = cat.desc;
      document.getElementById('hero-product-count').textContent = cat.count;
      document.getElementById('hero-icon').innerHTML            = '<i class="' + cat.icon + '"></i>';
    }

    /* ============================================================
       RENDER SUBCATEGORY TABS
       ============================================================ */
    function renderTabs(cat, activeSubcat) {
      const container = document.getElementById('subcat-tabs-container');
      container.innerHTML = '';

      // "All" tab
      const allBtn = document.createElement('button');
      allBtn.className   = 'subcat-tab' + (!activeSubcat ? ' active' : '');
      allBtn.dataset.key = '';
      allBtn.innerHTML   = 'All <span class="tab-count">' + cat.products.length + '</span>';
      allBtn.addEventListener('click', () => { renderPage(slugKey, ''); });
      container.appendChild(allBtn);

      // One tab per subcategory
      cat.subcats.forEach(sc => {
        const count = cat.products.filter(p => p.subcat === sc.key).length;
        const btn = document.createElement('button');
        btn.className   = 'subcat-tab' + (activeSubcat === sc.key ? ' active' : '');
        btn.dataset.key = sc.key;
        btn.innerHTML   = esc(sc.label) + ' <span class="tab-count">' + count + '</span>';
        btn.addEventListener('click', () => { renderPage(slugKey, sc.key); });
        container.appendChild(btn);
      });
    }

    /* ============================================================
       BUILD A PRODUCT CARD (matches the style in index.html)
       ============================================================ */
    function buildCard(product) {
      // product.url is built by category-page.php: it points to the item
      // (variants) page when the product has rows in product_variants,
      // and is empty otherwise (card then opens the quote modal).
      return `
        <div class="product-card" data-product-id="${esc(product.id)}" data-url="${esc(product.url)}">
          <div class="product-card-img">
            <img src="${esc(product.img)}" alt="${esc(product.name)}" loading="lazy">
          </div>
          <div class="product-card-body">
            <div class="product-card-title">${esc(product.name)}</p>
            <div class="product-card-price">${esc(product.price)}</p>
          </div>
         
        </div>`;
    }

    /** Card click: go to the variants page, or open the quote modal */
    function bindCardClicks(grid) {
      grid.querySelectorAll('.product-card').forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', () => {
          if (card.dataset.url) {
            window.location.href = card.dataset.url;
          } else if (typeof window.openQuoteFor === 'function') {
            window.openQuoteFor(card.dataset.productId);
          }
        });
      });
    }

    /* ============================================================
       RENDER PRODUCT GRID
       ============================================================ */
    function renderGrid(cat, activeSubcat, sortVal) {
      const grid = document.getElementById('cat-product-grid');
      grid.innerHTML = '';

      let products = activeSubcat
        ? cat.products.filter(p => p.subcat === activeSubcat)
        : cat.products;

      // Sort
      if (sortVal === 'name-asc')   products = [...products].sort((a,b) => a.name.localeCompare(b.name));
      if (sortVal === 'name-desc')  products = [...products].sort((a,b) => b.name.localeCompare(a.name));

      // Update count label
      const subLabel = activeSubcat
        ? cat.subcats.find(s => s.key === activeSubcat)?.label || cat.name
        : cat.name;
      document.getElementById('showing-count').textContent  = products.length;
      document.getElementById('showing-subcat').textContent = subLabel;

      if (!products.length) {
        grid.innerHTML = '<div class="no-results"><i class="fas fa-box-open"></i><p>No products found in this category yet.</p></div>';
        return;
      }

      // If showing all, group by subcategory
      if (!activeSubcat) {
        cat.subcats.forEach(sc => {
          const group = products.filter(p => p.subcat === sc.key);
          if (!group.length) return;

          // Section heading
          const heading = document.createElement('div');
          heading.className = 'subcat-section-heading';
          heading.innerHTML = `<h3>${esc(sc.label)}</h3><span class="count-tag">${group.length}</span>`;
          grid.appendChild(heading);

          group.forEach(p => {
            grid.insertAdjacentHTML('beforeend', buildCard(p));
          });
        });
      } else {
        products.forEach(p => {
          grid.insertAdjacentHTML('beforeend', buildCard(p));
        });
      }

      bindCardClicks(grid);
    }

    /* ============================================================
       FULL PAGE RENDER  (hero + tabs + grid)
       ============================================================ */
    let currentSubcat = '';

    function renderPage(slug, subcatKey) {
      const cat = CATEGORY_DATA[slug];
      if (!cat) return;
      currentSubcat = subcatKey;
      const sortVal = document.getElementById('sort-select')?.value || 'default';
      renderHero(cat);
      renderTabs(cat, subcatKey);
      renderGrid(cat, subcatKey, sortVal);
    }

    /* ============================================================
       INIT
       ============================================================ */
    const slugKey = getCategorySlug();

    // Sort change listener
    document.getElementById('sort-select').addEventListener('change', function () {
      renderGrid(CATEGORY_DATA[slugKey], currentSubcat, this.value);
    });

    // Search input (live filter)
    const searchInput = document.getElementById('navbar-search-input') || document.getElementById('cat-search-input');
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        if (!q) {
          renderPage(slugKey, currentSubcat);
          return;
        }
        const cat     = CATEGORY_DATA[slugKey];
        const grid    = document.getElementById('cat-product-grid');
        const results = cat.products.filter(p => p.name.toLowerCase().includes(q));
        grid.innerHTML = '';
        if (!results.length) {
          grid.innerHTML = '<div class="no-results"><i class="fas fa-search"></i><p>No products match "<strong>' + esc(q) + '</strong>"</p></div>';
          return;
        }
        results.forEach(p => grid.insertAdjacentHTML('beforeend', buildCard(p)));
        bindCardClicks(grid);
      });
    }

    // Initial render
    if (!slugKey) {
      document.getElementById('cat-product-grid').innerHTML =
        '<div class="no-results"><i class="fas fa-database"></i><p>No catalog data available.</p></div>';
      return;
    }
    renderPage(slugKey, '');

  })();
