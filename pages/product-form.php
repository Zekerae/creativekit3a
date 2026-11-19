<?php
/**
 * pages/product-form.php — Product Create & Edit Form
 * Dual Validation: Client-side JS + Server-side PHP with prepared statements.
 * Dedicated Assets:
 *   - CSS: /assets/css/product-form.css
 *   - JS:  /assets/js/product-form.js
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../auth/guard.php';

$page_id = (int)($_GET['id'] ?? 0);
$is_edit = ($page_id > 0);

$page_title = $is_edit ? 'Edit Product | CreativeKit 3A' : 'Add New Product | CreativeKit 3A';
$extra_css  = ['/assets/css/product-form.css'];
$extra_js   = ['/assets/js/product-form.js'];

$flash = get_flash();
$form_errors = $_SESSION['form_errors'] ?? [];
$form_data   = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);

// Product defaults
$product = [
    'id'          => 0,
    'category_id' => 1,
    'name'        => '',
    'sku'         => '',
    'price_min'   => '0.00',
    'specs'       => '',
    'description' => '',
    'image'       => '/assets/images/1.png',
    'badge'       => '',
    'status'      => 'active',
    'is_featured' => 0,
];

// If edit mode, load existing product from database
if ($is_edit && $pdo) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $page_id]);
    $existing = $stmt->fetch();
    if ($existing) {
        $product = $existing;
    } else {
        set_flash('danger', 'Product not found.');
        header('Location: /pages/products.php');
        exit;
    }
}

// Override with session flash data if validation failed
if (!empty($form_data)) {
    $product = array_merge($product, $form_data);
}

// Fetch all categories
$categories = [];
if ($pdo) {
    // Main categories with their subcategories grouped underneath (self LEFT JOIN)
    $stmt = $pdo->query('SELECT c.id, c.parent_id, c.name, c.slug, pc.slug AS parent_slug
                         FROM categories AS c
                         LEFT JOIN categories AS pc ON c.parent_id = pc.id
                         ORDER BY COALESCE(pc.sort_order, c.sort_order), c.parent_id IS NOT NULL, c.sort_order, c.name');
    $categories = $stmt->fetchAll();
}

// Available product images in assets/images/
$sample_images = [
    '/assets/images/1.png'                   => 'Single USB Car Charger',
    '/assets/images/2.png'                   => 'Fast Dual Adapter',
    '/assets/images/dual-usb-adapter.png'    => 'Dual USB Adapter',
    '/assets/images/usb-c-adapter.png'       => 'USB-C Adapter',
    '/assets/images/car-charger-adapters.png'=> 'Car Charger Bundle',
    '/assets/images/car-organizer.png'       => 'Car Organizer',
    '/assets/images/dash-cam.png'            => 'Dash Cam',
    '/assets/images/mouse-pad.png'           => 'Mouse Pad',
    '/assets/images/usb-hub.png'             => '4-Port USB Hub',
    '/assets/images/audio-accessories.png'   => 'Audio Earbuds',
    '/assets/images/wooven-clock.png'        => 'Woven Bamboo Clock',
    '/assets/images/desk-organizer.png'      => 'Desk Organizer',
    '/assets/images/mobile-powerbank.png'    => 'Slim Power Bank',
    '/assets/images/mobile-stand.png'        => 'Mobile Phone Stand',
    '/assets/images/portable-fan.png'        => 'Portable Mini Fan',
    '/assets/images/power-banks.png'         => 'High-Capacity Power Bank',
    '/assets/images/corporate-jacket.jpg'    => 'Corporate Jacket',
    '/assets/images/laptop-bag.png'          => 'Laptop Backpack',
    '/assets/images/bamboo-tumbler.png'      => 'Bamboo Tumbler',
    '/assets/images/handheld-vacuum.png'     => 'Handheld Vacuum',
];

include __DIR__ . '/../includes/head.php';
?>
<body>
<?php
include __DIR__ . '/../includes/topbar.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="form-page-wrapper">
  <div class="container form-container">

    <!-- Flash message -->
    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" role="alert">
        <span><i class="fas fa-info-circle"></i> <?= htmlspecialchars($flash['message']) ?></span>
        <button type="button" class="alert-close" aria-label="Close">&times;</button>
      </div>
    <?php endif; ?>

    <!-- Breadcrumbs -->
    <nav class="breadcrumb-nav" aria-label="Breadcrumb">
      <a href="/pages/dashboard.php">Dashboard</a>
      <span>/</span>
      <a href="/pages/products.php">Products</a>
      <span>/</span>
      <span class="current"><?= $is_edit ? 'Edit Product' : 'Add New Product' ?></span>
    </nav>

    <!-- Header -->
    <div class="form-header">
      <div>
        <h1><?= $is_edit ? 'Edit Promotional Product' : 'Create New Promotional Product' ?></h1>
        <p><?= $is_edit ? 'Update merchandise specifications, pricing, and catalog visibility.' : 'Fill in the required information to add an item to the corporate catalog.' ?></p>
      </div>
      <a href="/pages/products.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Products
      </a>
    </div>

    <!-- Server Validation Error Box -->
    <?php if (!empty($form_errors)): ?>
      <div class="alert alert-danger" role="alert">
        <div>
          <strong><i class="fas fa-exclamation-triangle"></i> Validation Failed:</strong>
          <ul style="margin-top:6px; padding-left:18px;">
            <?php foreach ($form_errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <button type="button" class="alert-close" aria-label="Close">&times;</button>
      </div>
    <?php endif; ?>

    <!-- Client-side Error Box (triggered via JS submit event) -->
    <div class="alert alert-danger" id="client-form-error" style="display:none;" role="alert">
      <div id="client-error-list"></div>
    </div>

    <!-- The Product CRUD Form -->
    <form action="/actions/product-actions.php" method="POST" id="product-form" class="product-form-card" novalidate>
      <input type="hidden" name="action" value="<?= $is_edit ? 'update' : 'create' ?>">
      <?php if ($is_edit): ?>
        <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
      <?php endif; ?>

      <div class="form-sections-grid">

        <!-- Left Column: Core Fields -->
        <div class="form-section-main">

          <!-- Product Name -->
          <div class="form-group">
            <label for="prod-name">
              Product Name <span class="required">*</span>
            </label>
            <input 
              type="text" 
              name="name" 
              id="prod-name" 
              class="form-control" 
              placeholder="e.g. Bamboo Fiber Insulated Tumbler 500ml"
              value="<?= htmlspecialchars($product['name']) ?>" 
              required
            >
            <span class="field-error" id="name-error"></span>
          </div>

          <!-- Category Selection (Event 3: 'change' triggers dynamic SKU & spec suggestions) -->
          <div class="form-row-2">
            <div class="form-group">
              <label for="prod-category">
                Category <span class="required">*</span>
                <span class="info-tag" data-tooltip="Changing category suggests prefix and specs"><i class="fas fa-magic"></i> Auto-Helper</span>
              </label>
              <select name="category_id" id="prod-category" class="form-control" required>
                <option value="">Select a Category</option>
                <?php foreach ($categories as $cat): ?>
                  <option 
                    value="<?= $cat['id'] ?>" 
                    data-slug="<?= htmlspecialchars($cat['slug']) ?>"
                    data-parent-slug="<?= htmlspecialchars($cat['parent_slug'] ?? '') ?>"
                    <?= ((int)$product['category_id'] === (int)$cat['id']) ? 'selected' : '' ?>
                  >
                    <?= $cat['parent_id'] ? '&nbsp;&nbsp;↳ ' : '' ?><?= htmlspecialchars($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <span class="field-error" id="category-error"></span>
            </div>

            <!-- SKU Code -->
            <div class="form-group">
              <label for="prod-sku">
                SKU / Product Code <span class="required">*</span>
              </label>
              <div class="sku-input-wrap">
                <input 
                  type="text" 
                  name="sku" 
                  id="prod-sku" 
                  class="form-control" 
                  placeholder="e.g. DRK-BT-001" 
                  value="<?= htmlspecialchars($product['sku']) ?>"
                  required
                >
                <button type="button" class="btn btn-secondary btn-sm" id="btn-gen-sku" data-tooltip="Generate random unique SKU">
                  <i class="fas fa-sync-alt"></i>
                </button>
              </div>
              <span class="field-error" id="sku-error"></span>
            </div>
          </div>

          <!-- Base Price & Badge -->
          <div class="form-row-2">
            <div class="form-group">
              <label for="prod-price">
                Base Unit Price (PHP) <span class="required">*</span>
              </label>
              <div class="price-input-wrap">
                <span class="currency-prefix">₱</span>
                <input 
                  type="number" 
                  step="0.01" 
                  min="0.01" 
                  name="price_min" 
                  id="prod-price" 
                  class="form-control" 
                  placeholder="0.00" 
                  value="<?= htmlspecialchars($product['price_min']) ?>"
                  required
                >
              </div>
              <span class="field-error" id="price-error"></span>
            </div>

            <div class="form-group">
              <label for="prod-badge">
                Promotional Badge
              </label>
              <select name="badge" id="prod-badge" class="form-control">
                <option value="" <?= empty($product['badge']) ? 'selected' : '' ?>>None</option>
                <option value="Best Seller" <?= ($product['badge'] === 'Best Seller') ? 'selected' : '' ?>>Best Seller</option>
                <option value="New" <?= ($product['badge'] === 'New') ? 'selected' : '' ?>>New</option>
                <option value="Eco-Friendly" <?= ($product['badge'] === 'Eco-Friendly') ? 'selected' : '' ?>>Eco-Friendly</option>
                <option value="Premium" <?= ($product['badge'] === 'Premium') ? 'selected' : '' ?>>Premium</option>
              </select>
            </div>
          </div>

          <!-- Specifications Summary -->
          <div class="form-group">
            <label for="prod-specs">
              Key Specifications / Dimensions
              <span class="helper-text">(Short highlights shown on catalog cards)</span>
            </label>
            <input 
              type="text" 
              name="specs" 
              id="prod-specs" 
              class="form-control" 
              placeholder="e.g. 500ml • Stainless Interior • Leak-Proof Lid"
              value="<?= htmlspecialchars($product['specs']) ?>"
            >
          </div>

          <!-- Detailed Description -->
          <div class="form-group">
            <label for="prod-desc">Detailed Description</label>
            <textarea 
              name="description" 
              id="prod-desc" 
              rows="4" 
              class="form-control" 
              placeholder="Provide a comprehensive marketing description, material details, and personalization options..."
            ><?= htmlspecialchars($product['description']) ?></textarea>
          </div>

        </div>

        <!-- Right Column: Settings & Image Selector -->
        <div class="form-section-side">

          <!-- Status & Visibility Card -->
          <div class="side-panel-card">
            <h4><i class="fas fa-sliders-h"></i> Publishing Settings</h4>

            <div class="form-group" style="margin-top:12px;">
              <label for="prod-status">Catalog Status</label>
              <select name="status" id="prod-status" class="form-control">
                <option value="active"   <?= ($product['status'] === 'active')   ? 'selected' : '' ?>>Active (Visible in Catalog)</option>
                <option value="draft"    <?= ($product['status'] === 'draft')    ? 'selected' : '' ?>>Draft (Hidden)</option>
                <option value="archived" <?= ($product['status'] === 'archived') ? 'selected' : '' ?>>Archived</option>
              </select>
            </div>

            <div class="form-group checkbox-group">
              <label class="custom-checkbox">
                <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?>>
                <span class="checkbox-mark"></span>
                <span>Feature on Homepage Showcase</span>
              </label>
            </div>
          </div>

          <!-- Product Image Selector Card -->
          <div class="side-panel-card">
            <h4><i class="fas fa-image"></i> Product Image</h4>
            
            <div class="image-preview-wrap">
              <img src="<?= htmlspecialchars($product['image'] ?: '/assets/images/1.png') ?>" alt="Preview" id="img-live-preview">
            </div>

            <div class="form-group" style="margin-top:14px;">
              <label for="prod-image">Select Existing Photo</label>
              <select name="image" id="prod-image" class="form-control">
                <?php foreach ($sample_images as $path => $name): ?>
                  <option value="<?= $path ?>" <?= ($product['image'] === $path) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($name) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

        </div>

      </div>

      <!-- Form Action Buttons -->
      <div class="form-footer-actions">
        <a href="/pages/products.php" class="btn btn-secondary">
          Cancel
        </a>
        <button type="submit" class="btn btn-primary btn-lg" id="btn-save-product">
          <i class="fas fa-save"></i> <?= $is_edit ? 'Update Product' : 'Create Product' ?>
        </button>
      </div>

    </form>

  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
