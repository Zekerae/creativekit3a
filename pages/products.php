<?php
/**
 * pages/products.php — Product Catalog Management (Full CRUD)
 * Protected route: requires authentication (Admin has delete, Staff has edit/view).
 * Dedicated Assets:
 *   - CSS: /assets/css/products.css
 *   - JS:  /assets/js/products.js
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../auth/guard.php';

$page_title = 'Manage Products | CreativeKit 3A';
$extra_css  = ['/assets/css/admin.css', '/assets/css/products.css'];
$admin_heading = 'Products';
$extra_js   = ['/assets/js/products.js'];

$flash = get_flash();

// -------------------------------------------------------------
// Request Parameters & Filtering ($_GET Handling)
// -------------------------------------------------------------
$search_query = trim($_GET['q'] ?? '');
$filter_cat   = (int)($_GET['cat'] ?? 0);
$filter_status= trim($_GET['status'] ?? '');
$sort_by      = trim($_GET['sort'] ?? 'name_asc');

// Map sorting parameter safely to SQL ORDER BY
switch ($sort_by) {
    case 'name_desc':
        $order_clause = 'p.name DESC';
        break;
    case 'price_asc':
        $order_clause = 'p.price_min ASC';
        break;
    case 'price_desc':
        $order_clause = 'p.price_min DESC';
        break;
    case 'date_desc':
        $order_clause = 'p.created_at DESC';
        break;
    case 'name_asc':
    default:
        $order_clause = 'p.name ASC';
        break;
}

// -------------------------------------------------------------
// Fetch Categories for Dropdown Filter
// -------------------------------------------------------------
$categories = [];
if ($pdo) {
    // Main categories with their subcategories grouped underneath (self LEFT JOIN)
    $cat_stmt = $pdo->query("SELECT c.id,
                                    CONCAT(IF(c.parent_id IS NULL, '', '↳ '), c.name) AS name
                             FROM categories AS c
                             LEFT JOIN categories AS pc ON c.parent_id = pc.id
                             ORDER BY COALESCE(pc.sort_order, c.sort_order), c.parent_id IS NOT NULL, c.sort_order, c.name");
    $categories = $cat_stmt->fetchAll();
}

// -------------------------------------------------------------
// Fetch Products using INNER JOIN, WHERE, LIKE, and ORDER BY
// Demonstrates: INNER JOIN, LIKE, WHERE, ORDER BY, AS aliases
// -------------------------------------------------------------
$products = [];
$total_matching = 0;

if ($pdo) {
    $sql = 'SELECT 
                p.id,
                p.name AS product_name,
                p.slug,
                p.sku,
                p.specs,
                p.price_min,
                p.image,
                p.badge,
                p.status,
                p.is_featured,
                p.created_at,
                c.id AS category_id,
                c.name AS category_name,
                CONCAT(u.first_name, " ", u.last_name) AS creator_name
            FROM products AS p
            INNER JOIN categories AS c ON p.category_id = c.id
            LEFT JOIN users AS u ON p.created_by = u.id
            WHERE 1=1';

    $params = [];

    // Filter by category
    if ($filter_cat > 0) {
        // Matches the category itself or any of its subcategories.
        // (Native prepares forbid reusing a named placeholder, hence :cat_id2.)
        $sql .= ' AND (p.category_id = :cat_id OR c.parent_id = :cat_id2)';
        $params[':cat_id']  = $filter_cat;
        $params[':cat_id2'] = $filter_cat;
    }

    // Filter by status
    if (!empty($filter_status) && in_array($filter_status, ['active', 'draft', 'archived'])) {
        $sql .= ' AND p.status = :status';
        $params[':status'] = $filter_status;
    }

    // Search query using LIKE
    if (!empty($search_query)) {
        $sql .= ' AND (p.name LIKE :s1 OR p.sku LIKE :s2 OR p.description LIKE :s3)';
        $like = '%' . $search_query . '%';
        $params += [':s1' => $like, ':s2' => $like, ':s3' => $like];
    }

    $sql .= ' ORDER BY ' . $order_clause;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    $total_matching = count($products);
}

include __DIR__ . '/../includes/head.php';
?>
<body class="admin-body">
<?php include __DIR__ . '/../includes/admin-layout.php'; ?>

<main class="products-wrapper">
  <div class="container">

    <!-- Flash message -->
    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" role="alert">
        <span><i class="fas fa-info-circle"></i> <?= htmlspecialchars($flash['message']) ?></span>
        <button type="button" class="alert-close" aria-label="Close">&times;</button>
      </div>
    <?php endif; ?>

    <!-- Page Header & Action Bar -->
    <header class="page-header">
      <div>
        <nav class="breadcrumb-nav" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a>
          <span>/</span>
          <span class="current">Products</span>
        </nav>
        <h1>Promotional Products Management</h1>
        <p>Browse, search, add, edit, and organize corporate merchandise.</p>
      </div>

      <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/pages/product-form.php" class="btn btn-primary">
          <i class="fas fa-plus"></i> Add New Product
        </a>
      </div>
    </header>

    <!-- Filter & Real-Time Search Control Bar -->
    <section class="filter-card" aria-label="Filter products">
      <form action="<?= BASE_URL ?>/pages/products.php" method="GET" id="filter-form" class="filter-form">

        <!-- Real-Time Search Input (input event in products.js) -->
        <div class="filter-col filter-search">
          <label for="search-input"><i class="fas fa-search"></i> Search Product</label>
          <div class="input-with-icon">
            <input 
              type="text" 
              name="q" 
              id="search-input" 
              class="form-control" 
              placeholder="Search name, SKU, or specs..." 
              value="<?= htmlspecialchars($search_query) ?>"
              autocomplete="off"
            >
            <span class="clear-input-btn" id="clear-search-btn" style="<?= empty($search_query) ? 'display:none;' : '' ?>">&times;</span>
          </div>
        </div>

        <!-- Category Dropdown Filter (change event in products.js) -->
        <div class="filter-col">
          <label for="category-select"><i class="fas fa-tags"></i> Category</label>
          <select name="cat" id="category-select" class="form-control">
            <option value="0">All Categories</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= ($filter_cat === (int)$cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Status Filter -->
        <div class="filter-col">
          <label for="status-select"><i class="fas fa-toggle-on"></i> Status</label>
          <select name="status" id="status-select" class="form-control">
            <option value="">All Statuses</option>
            <option value="active"   <?= ($filter_status === 'active')   ? 'selected' : '' ?>>Active</option>
            <option value="draft"    <?= ($filter_status === 'draft')    ? 'selected' : '' ?>>Draft</option>
            <option value="archived" <?= ($filter_status === 'archived') ? 'selected' : '' ?>>Archived</option>
          </select>
        </div>

        <!-- Sorting Options -->
        <div class="filter-col">
          <label for="sort-select"><i class="fas fa-sort-amount-down"></i> Sort By</label>
          <select name="sort" id="sort-select" class="form-control">
            <option value="name_asc"   <?= ($sort_by === 'name_asc')   ? 'selected' : '' ?>>Name (A to Z)</option>
            <option value="name_desc"  <?= ($sort_by === 'name_desc')  ? 'selected' : '' ?>>Name (Z to A)</option>
            <option value="price_asc"  <?= ($sort_by === 'price_asc')  ? 'selected' : '' ?>>Price (Low to High)</option>
            <option value="price_desc" <?= ($sort_by === 'price_desc') ? 'selected' : '' ?>>Price (High to Low)</option>
            <option value="date_desc"  <?= ($sort_by === 'date_desc')  ? 'selected' : '' ?>>Newest Added</option>
          </select>
        </div>

        <!-- Filter Submit & Reset -->
        <div class="filter-col filter-actions">
          <label>&nbsp;</label>
          <div style="display:flex; gap:8px;">
            <button type="submit" class="btn btn-primary btn-sm" id="btn-apply-filters">
              <i class="fas fa-filter"></i> Filter
            </button>
            <a href="<?= BASE_URL ?>/pages/products.php" class="btn btn-secondary btn-sm" data-tooltip="Reset all filters">
              <i class="fas fa-undo"></i> Reset
            </a>
          </div>
        </div>

      </form>
    </section>

    <!-- Results Count Bar -->
    <div class="results-bar">
      <span>Showing <strong id="product-count"><?= $total_matching ?></strong> promotional products</span>
      <span class="live-filter-indicator" id="live-filter-tag" style="display:none;">
        <i class="fas fa-bolt"></i> Live Filter Active
      </span>
    </div>

    <!-- Products Data Table (Desktop/Tablet) & Responsive Cards (Mobile) -->
    <div class="table-card">
      <div class="table-responsive">
        <table class="products-table" id="products-table">
          <thead>
            <tr>
              <th style="width:70px;">Image</th>
              <th>Product Name & SKU</th>
              <th>Category</th>
              <th>Base Price</th>
              <th>Status</th>
              <th>Featured</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody id="products-tbody">
            <?php if (empty($products)): ?>
              <tr id="no-products-row">
                <td colspan="7" class="table-empty">
                  <i class="fas fa-box-open" style="font-size:2rem; margin-bottom:10px; display:block; color:var(--grey-mid);"></i>
                  No promotional products found matching your search criteria.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($products as $p): ?>
                <tr class="product-row" 
                    data-id="<?= $p['id'] ?>"
                    data-name="<?= htmlspecialchars(strtolower($p['product_name'])) ?>"
                    data-sku="<?= htmlspecialchars(strtolower($p['sku'] ?? '')) ?>"
                    data-cat="<?= $p['category_id'] ?>"
                    data-status="<?= htmlspecialchars($p['status']) ?>"
                    data-price="<?= $p['price_min'] ?>"
                >
                  <!-- Product Image with Tooltip -->
                  <td class="col-img">
                    <div class="prod-thumb-wrap" data-tooltip="Preview: <?= htmlspecialchars($p['product_name']) ?>">
                      <img src="<?= htmlspecialchars(url($p['image'] ?: '/assets/images/placeholder.png')) ?>" 
                           alt="<?= htmlspecialchars($p['product_name']) ?>" 
                           class="prod-thumb" 
                           loading="lazy">
                    </div>
                  </td>

                  <!-- Product Name, SKU & Specs -->
                  <td class="col-name">
                    <div class="prod-title-wrap">
                      <strong class="prod-name"><?= htmlspecialchars($p['product_name']) ?></strong>
                      <?php if (!empty($p['badge'])): ?>
                        <span class="prod-inline-badge"><?= htmlspecialchars($p['badge']) ?></span>
                      <?php endif; ?>
                    </div>
                    <div class="prod-meta">
                      <span class="sku-tag" data-tooltip="Stock Keeping Unit (SKU)">
                        <i class="fas fa-barcode"></i> <?= htmlspecialchars($p['sku'] ?? 'N/A') ?>
                      </span>
                      <?php if (!empty($p['specs'])): ?>
                        <span class="specs-preview" data-tooltip="Specifications: <?= htmlspecialchars($p['specs']) ?>">
                          <i class="fas fa-info-circle"></i> <?= htmlspecialchars(substr($p['specs'], 0, 36)) ?><?= strlen($p['specs']) > 36 ? '...' : '' ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  </td>

                  <!-- Category -->
                  <td>
                    <span class="cat-pill">
                      <i class="fas fa-folder"></i> <?= htmlspecialchars($p['category_name']) ?>
                    </span>
                  </td>

                  <!-- Base Price -->
                  <td>
                    <span class="prod-price">₱<?= number_format($p['price_min'], 2) ?></span>
                  </td>

                  <!-- Status Badge -->
                  <td>
                    <span class="badge badge-<?= htmlspecialchars($p['status']) ?>">
                      <?= ucfirst(htmlspecialchars($p['status'])) ?>
                    </span>
                  </td>

                  <!-- Featured Badge -->
                  <td>
                    <?php if ($p['is_featured']): ?>
                      <span class="featured-star" data-tooltip="Featured on Homepage Carousel/Grid">
                        <i class="fas fa-star" style="color:#F59E0B;"></i> Yes
                      </span>
                    <?php else: ?>
                      <span style="color:var(--grey-mid); font-size:0.8125rem;">—</span>
                    <?php endif; ?>
                  </td>

                  <!-- Actions (Edit for all, Delete role-restricted to Admin) -->
                  <td style="text-align:right;" class="col-actions">
                    <div class="action-buttons">
                      <!-- Edit Product -->
                      <a href="<?= BASE_URL ?>/pages/product-form.php?id=<?= $p['id'] ?>" class="btn-action btn-action-edit" data-tooltip="Edit product details">
                        <i class="fas fa-edit"></i>
                      </a>

                      <!-- Delete Product (Role-Restricted: Admin Only) -->
                      <?php if ($is_admin): ?>
                        <button 
                          type="button" 
                          class="btn-action btn-action-delete btn-open-delete-modal"
                          data-id="<?= $p['id'] ?>"
                          data-name="<?= htmlspecialchars($p['product_name']) ?>"
                          data-sku="<?= htmlspecialchars($p['sku'] ?? '') ?>"
                          data-tooltip="Delete product permanently"
                        >
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      <?php else: ?>
                        <span class="btn-action btn-action-disabled" data-tooltip="Staff cannot delete. Admin permission required.">
                          <i class="fas fa-lock"></i>
                        </span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Real-Time No Matches Dynamic Notice -->
      <div id="dynamic-no-matches" style="display:none; text-align:center; padding:40px; color:var(--grey-dark);">
        <i class="fas fa-search" style="font-size:2rem; margin-bottom:12px; color:var(--orange-primary); display:block;"></i>
        <h4>No matching products found</h4>
        <p style="font-size:0.875rem; color:var(--grey-mid);">Try adjusting your search terms or filters.</p>
      </div>
    </div>

  </div>
</main>

<!-- ============================================================
     DELETE CONFIRMATION MODAL (Event 1: 'click')
     Canceling prevents deletion. Confirming sends POST to actions/product-actions.php
     ============================================================ -->
<div class="modal-overlay" id="delete-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="modal-del-title">
  <div class="modal-card">
    <div class="modal-header" style="border-bottom-color:#FEE2E2; background:#FEF2F2;">
      <h3 id="modal-del-title" style="color:#991B1B;">
        <i class="fas fa-exclamation-triangle" style="color:var(--danger);"></i> Confirm Product Deletion
      </h3>
      <button type="button" class="modal-close" id="btn-close-del-modal" aria-label="Close modal">&times;</button>
    </div>
    <div class="modal-body">
      <p style="margin-bottom:12px; font-size:0.9375rem; color:var(--black);">
        Are you sure you want to permanently delete the following product?
      </p>
      <div style="background:var(--grey-light); padding:12px 16px; border-radius:var(--radius-md); border-left:4px solid var(--danger); margin-bottom:16px;">
        <p style="font-weight:700; color:var(--black-deep); font-size:1rem;" id="modal-del-prod-name">—</p>
        <p style="font-size:0.8125rem; color:var(--grey-dark);" id="modal-del-prod-sku">SKU: —</p>
      </div>
      <p style="font-size:0.8125rem; color:var(--danger); line-height:1.5;">
        <i class="fas fa-radiation"></i> <strong>Warning:</strong> This action cannot be undone. Associated quote attachments may be unlinked.
      </p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="btn-cancel-del">
        <i class="fas fa-times"></i> Cancel
      </button>
      <form action="<?= BASE_URL ?>/actions/product-actions.php" method="POST" id="form-confirm-delete" style="margin:0;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="modal-del-prod-id" value="">
        <button type="submit" class="btn btn-danger" id="btn-confirm-delete">
          <i class="fas fa-trash-alt"></i> Yes, Delete Product
        </button>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
