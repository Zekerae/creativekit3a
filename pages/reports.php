<?php
/**
 * pages/reports.php — Executive Records Summary & System Reports
 * Demonstrates:
 *   - INNER JOIN: Active products joined with categories
 *   - LEFT JOIN: Categories with product counts (including empty categories)
 *   - RIGHT JOIN: Staff users joined with quote assignments (including staff with 0 quotes)
 *   - Column and table aliases using AS
 *   - String formatting using CONCAT()
 *   - WHERE, LIKE, and ORDER BY clauses
 *   - Arithmetic operators for pipeline valuation and metrics
 *   - switch statement for report mode handling
 * Dedicated Assets:
 *   - CSS: /assets/css/reports.css
 *   - JS:  /assets/js/reports.js
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../auth/guard.php';

$page_title = 'System Reports & Analytics | CreativeKit 3A';
$extra_css  = ['/assets/css/reports.css'];
$extra_js   = ['/assets/js/reports.js'];

$report_mode = $_GET['tab'] ?? 'summary';

// -------------------------------------------------------------
// Report Data Queries (SQL Joins & Formatting Requirements)
// -------------------------------------------------------------
$category_summary = [];
$staff_performance= [];
$active_catalog   = [];
$status_pipeline  = [];
$total_catalog_items = 0;
$total_estimated_pipeline = 0;

if ($pdo) {

    // =========================================================
    // 1. LEFT JOIN: All categories with product aggregates
    //    Demonstrates: LEFT JOIN, GROUP BY, ORDER BY, AS aliases
    //    Includes categories that currently have 0 products!
    // =========================================================
    $left_join_sql = "SELECT 
                          c.id AS category_id,
                          c.name AS category_name,
                          c.icon AS category_icon,
                          COUNT(p.id) AS product_count,
                          COALESCE(AVG(p.price_min), 0.0) AS avg_price,
                          COALESCE(MIN(p.price_min), 0.0) AS min_price,
                          COALESCE(MAX(p.price_min), 0.0) AS max_price
                      FROM categories AS c
                      LEFT JOIN categories AS sc ON (sc.parent_id = c.id OR sc.id = c.id)
                      LEFT JOIN products AS p ON p.category_id = sc.id
                      WHERE c.parent_id IS NULL
                      GROUP BY c.id, c.name, c.icon
                      ORDER BY product_count DESC, c.name ASC";
    $category_summary = $pdo->query($left_join_sql)->fetchAll();

    // =========================================================
    // 2. RIGHT JOIN: All staff members with quote assignments
    //    Demonstrates: RIGHT JOIN, CONCAT(), AS aliases, GROUP BY
    //    Includes staff members who currently have 0 assigned quotes!
    // =========================================================
    $right_join_sql = "SELECT 
                           u.id AS user_id,
                           CONCAT(u.first_name, ' ', u.last_name) AS staff_name,
                           u.email AS staff_email,
                           u.role AS staff_role,
                           COUNT(q.id) AS assigned_quotes,
                           SUM(CASE WHEN q.status = 'accepted' THEN 1 ELSE 0 END) AS accepted_quotes,
                           SUM(CASE WHEN q.status = 'quoted' THEN 1 ELSE 0 END) AS quoted_quotes
                       FROM quote_requests AS q
                       RIGHT JOIN users AS u ON q.assigned_to = u.id
                       WHERE u.is_active = 1
                       GROUP BY u.id, staff_name, u.email, u.role
                       ORDER BY assigned_quotes DESC, staff_name ASC";
    $staff_performance = $pdo->query($right_join_sql)->fetchAll();

    // =========================================================
    // 3. INNER JOIN: Active products joined with category names
    //    Demonstrates: INNER JOIN, CONCAT(), WHERE, ORDER BY, AS
    // =========================================================
    $inner_join_sql = "SELECT 
                           p.id AS product_id,
                           p.name AS product_name,
                           CONCAT(p.name, ' (SKU: ', p.sku, ')') AS product_label,
                           p.sku,
                           p.price_min,
                           p.status,
                           c.name AS category_name
                       FROM products AS p
                       INNER JOIN categories AS c ON p.category_id = c.id
                       WHERE p.status = 'active'
                       ORDER BY p.name ASC";
    $active_catalog = $pdo->query($inner_join_sql)->fetchAll();
    $total_catalog_items = count($active_catalog);

    // =========================================================
    // 4. Quote Pipeline Breakdown
    // =========================================================
    $pipeline_sql = "SELECT 
                         status, 
                         COUNT(*) AS quote_count,
                         COALESCE(SUM(quantity), 0) AS total_units
                     FROM quote_requests 
                     GROUP BY status";
    $status_pipeline = $pdo->query($pipeline_sql)->fetchAll();

    // Calculate pipeline metric arithmetic
    foreach ($status_pipeline as $pipe) {
        if ($pipe['status'] === 'accepted' || $pipe['status'] === 'quoted') {
            $total_estimated_pipeline += (int)$pipe['total_units'] * 125.0; // average merchandise price estimate
        }
    }
}

include __DIR__ . '/../includes/head.php';
?>
<body>
<?php
include __DIR__ . '/../includes/topbar.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="reports-wrapper">
  <div class="container">

    <!-- Page Header & Print/Export Actions -->
    <header class="reports-header">
      <div>
        <nav class="breadcrumb-nav" aria-label="Breadcrumb">
          <a href="/pages/dashboard.php">Dashboard</a>
          <span>/</span>
          <span class="current">Executive Reports</span>
        </nav>
        <h1>System Reports & Records Summary</h1>
        <p>Comprehensive aggregation of merchandise inventory, customer quote requests, and staff performance.</p>
      </div>

      <div class="header-actions no-print">
        <button type="button" class="btn btn-secondary" id="btn-print-report" data-tooltip="Print or save this report as PDF">
          <i class="fas fa-print"></i> Print Report
        </button>
        <button type="button" class="btn btn-primary" id="btn-export-csv" data-tooltip="Download summary records as CSV">
          <i class="fas fa-file-csv"></i> Export CSV
        </button>
      </div>
    </header>

    <!-- Report Mode Navigation Tabs -->
    <div class="report-tabs no-print">
      <a href="/pages/reports.php?tab=summary" class="report-tab <?= ($report_mode === 'summary') ? 'active' : '' ?>">
        <i class="fas fa-chart-pie"></i> Complete Summary
      </a>
      <a href="/pages/reports.php?tab=categories" class="report-tab <?= ($report_mode === 'categories') ? 'active' : '' ?>">
        <i class="fas fa-layer-group"></i> Category Breakdown (LEFT JOIN)
      </a>
      <a href="/pages/reports.php?tab=staff" class="report-tab <?= ($report_mode === 'staff') ? 'active' : '' ?>">
        <i class="fas fa-users"></i> Staff Assignments (RIGHT JOIN)
      </a>
      <a href="/pages/reports.php?tab=catalog" class="report-tab <?= ($report_mode === 'catalog') ? 'active' : '' ?>">
        <i class="fas fa-boxes"></i> Active Catalog (INNER JOIN)
      </a>
    </div>

    <!-- Summary KPI Highlights (Arithmetic Operators Demonstration) -->
    <section class="report-kpi-grid">
      <div class="report-kpi-card">
        <div class="kpi-tag"><i class="fas fa-check-circle" style="color:var(--success);"></i> Active Merchandise</div>
        <h3><?= number_format($total_catalog_items) ?> Items</h3>
        <p>Active items joined across <?= count($category_summary) ?> categories</p>
      </div>

      <div class="report-kpi-card">
        <div class="kpi-tag"><i class="fas fa-money-bill-wave" style="color:var(--orange-primary);"></i> Pipeline Volume</div>
        <h3>₱<?= number_format($total_estimated_pipeline, 2) ?></h3>
        <p>Estimated valuation based on active quote requests</p>
      </div>

      <div class="report-kpi-card">
        <div class="kpi-tag"><i class="fas fa-user-shield" style="color:#7C3AED;"></i> Staff Coverage</div>
        <h3><?= count($staff_performance) ?> Coordinators</h3>
        <p>Assigned via RIGHT JOIN across all registered personnel</p>
      </div>
    </section>

    <!-- =========================================================
         SECTION 1: CATEGORY BREAKDOWN (Demonstrates LEFT JOIN)
         ========================================================= -->
    <?php if ($report_mode === 'summary' || $report_mode === 'categories'): ?>
      <section class="report-section" id="section-categories">
        <div class="section-title-wrap">
          <div>
            <h2><i class="fas fa-layer-group" style="color:var(--orange-primary);"></i> Category Inventory Aggregation</h2>
            <p class="sql-badge">
              <i class="fas fa-database"></i> Executed with: <code>categories LEFT JOIN categories (subcategories) LEFT JOIN products</code>
            </p>
          </div>
          <span class="section-count"><?= count($category_summary) ?> Main Categories</span>
        </div>

        <div class="table-responsive">
          <table class="report-table" id="categories-report-table">
            <thead>
              <tr>
                <th>Category Name</th>
                <th>Total Products</th>
                <th>Product Share</th>
                <th>Avg. Base Price</th>
                <th>Price Range</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($category_summary as $cat): ?>
                <?php 
                  // Calculate percentage share arithmetic
                  $pct = ($total_catalog_items > 0) ? round(($cat['product_count'] / $total_catalog_items) * 100, 1) : 0;
                ?>
                <tr class="report-row">
                  <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                      <div class="cat-icon-badge">
                        <i class="fas <?= htmlspecialchars($cat['category_icon'] ?: 'fa-folder') ?>"></i>
                      </div>
                      <strong><?= htmlspecialchars($cat['category_name']) ?></strong>
                    </div>
                  </td>
                  <td>
                    <strong style="font-size:1rem;"><?= number_format($cat['product_count']) ?></strong> items
                  </td>
                  <td style="width:200px;">
                    <div class="progress-bar-wrap" data-tooltip="<?= $pct ?>% of total active catalog">
                      <div class="progress-bar-fill" style="width: <?= min(100, $pct * 2.5) ?>%;"></div>
                    </div>
                    <span style="font-size:0.75rem; color:var(--grey-mid);"><?= $pct ?>% share</span>
                  </td>
                  <td>
                    <strong>₱<?= number_format($cat['avg_price'], 2) ?></strong>
                  </td>
                  <td>
                    <?php if ($cat['product_count'] > 0): ?>
                      ₱<?= number_format($cat['min_price'], 2) ?> – ₱<?= number_format($cat['max_price'], 2) ?>
                    <?php else: ?>
                      <span style="color:var(--grey-mid);">No products yet</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>

    <!-- =========================================================
         SECTION 2: STAFF ASSIGNMENTS (Demonstrates RIGHT JOIN)
         ========================================================= -->
    <?php if ($report_mode === 'summary' || $report_mode === 'staff'): ?>
      <section class="report-section" id="section-staff">
        <div class="section-title-wrap">
          <div>
            <h2><i class="fas fa-user-check" style="color:var(--orange-primary);"></i> Staff Assignment Performance</h2>
            <p class="sql-badge">
              <i class="fas fa-database"></i> Executed with: <code>quote_requests RIGHT JOIN users ON quote_requests.assigned_to = users.id</code>
            </p>
          </div>
          <span class="section-count"><?= count($staff_performance) ?> Staff Profiles</span>
        </div>

        <div class="table-responsive">
          <table class="report-table" id="staff-report-table">
            <thead>
              <tr>
                <th>Staff Coordinator (CONCAT)</th>
                <th>Role</th>
                <th>Assigned Quotes</th>
                <th>Accepted Conversions</th>
                <th>Quoted In-Progress</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($staff_performance as $st): ?>
                <tr class="report-row">
                  <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                      <div class="staff-avatar">
                        <?= strtoupper(substr($st['staff_name'], 0, 1)) ?>
                      </div>
                      <div>
                        <strong><?= htmlspecialchars($st['staff_name']) ?></strong>
                        <div class="table-sub"><?= htmlspecialchars($st['staff_email']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge badge-<?= htmlspecialchars($st['staff_role']) ?>">
                      <?= ucfirst(htmlspecialchars($st['staff_role'])) ?>
                    </span>
                  </td>
                  <td>
                    <strong style="font-size:1rem; color:var(--orange-primary);"><?= (int)$st['assigned_quotes'] ?></strong> quotes
                  </td>
                  <td>
                    <span class="badge badge-accepted">
                      <i class="fas fa-check"></i> <?= (int)$st['accepted_quotes'] ?> Accepted
                    </span>
                  </td>
                  <td>
                    <span class="badge badge-quoted">
                      <?= (int)$st['quoted_quotes'] ?> Quoted
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>

    <!-- =========================================================
         SECTION 3: ACTIVE CATALOG (Demonstrates INNER JOIN)
         ========================================================= -->
    <?php if ($report_mode === 'summary' || $report_mode === 'catalog'): ?>
      <section class="report-section" id="section-catalog">
        <div class="section-title-wrap">
          <div>
            <h2><i class="fas fa-boxes" style="color:var(--orange-primary);"></i> Active Catalog Inventory</h2>
            <p class="sql-badge">
              <i class="fas fa-database"></i> Executed with: <code>products INNER JOIN categories ON products.category_id = categories.id</code>
            </p>
          </div>
          <span class="section-count"><?= $total_catalog_items ?> Active Products</span>
        </div>

        <div class="table-responsive">
          <table class="report-table" id="catalog-report-table">
            <thead>
              <tr>
                <th>Formatted Product Label (CONCAT)</th>
                <th>Category</th>
                <th>Base Price</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($active_catalog as $item): ?>
                <tr class="report-row">
                  <td>
                    <strong><?= htmlspecialchars($item['product_label']) ?></strong>
                  </td>
                  <td>
                    <span class="cat-pill">
                      <i class="fas fa-tag"></i> <?= htmlspecialchars($item['category_name']) ?>
                    </span>
                  </td>
                  <td>
                    <strong style="color:var(--orange-primary);">₱<?= number_format($item['price_min'], 2) ?></strong>
                  </td>
                  <td>
                    <span class="badge badge-active">Active</span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>

  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
