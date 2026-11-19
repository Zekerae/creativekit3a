<?php
/**
 * pages/dashboard.php — Role-Based Administrative Dashboard
 * Protected route: requires authentication.
 * Dedicated Assets:
 *   - CSS: /assets/css/dashboard.css
 *   - JS:  /assets/js/dashboard.js
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../auth/guard.php';

$page_title = 'System Dashboard | CreativeKit 3A';
$extra_css  = ['/assets/css/dashboard.css'];
$extra_js   = ['/assets/js/dashboard.js'];

$flash = get_flash();

// -------------------------------------------------------------
// Live Database Queries for Dashboard Metrics
// -------------------------------------------------------------
$total_products    = 0;
$active_products   = 0;
$total_quotes      = 0;
$new_quotes        = 0;
$total_categories  = 0;
$total_staff       = 0;
$assigned_to_me    = 0;
$recent_quotes     = [];
$recent_activities = [];

if ($pdo) {
    // 1. Total products count
    $stmt = $pdo->query('SELECT COUNT(*) FROM products');
    $total_products = (int)$stmt->fetchColumn();

    // 2. Active products count
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'");
    $active_products = (int)$stmt->fetchColumn();

    // 3. Total quotes count
    $stmt = $pdo->query('SELECT COUNT(*) FROM quote_requests');
    $total_quotes = (int)$stmt->fetchColumn();

    // 4. New / unreviewed quotes count
    $stmt = $pdo->query("SELECT COUNT(*) FROM quote_requests WHERE status = 'new'");
    $new_quotes = (int)$stmt->fetchColumn();

    // 5. Total top-level categories
    $stmt = $pdo->query('SELECT COUNT(*) FROM categories WHERE parent_id IS NULL');
    $total_categories = (int)$stmt->fetchColumn();

    // 6. Total active staff members
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'staff' AND is_active = 1");
    $total_staff = (int)$stmt->fetchColumn();

    // 7. Quotes assigned specifically to current staff member
    if (!$is_admin) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM quote_requests WHERE assigned_to = :uid');
        $stmt->execute([':uid' => $current_user_id]);
        $assigned_to_me = (int)$stmt->fetchColumn();
    }

    // 8. Recent Quote Requests (with product name join)
    $stmt = $pdo->query('SELECT q.*, p.name AS product_name 
                         FROM quote_requests AS q 
                         LEFT JOIN products AS p ON q.product_id = p.id 
                         ORDER BY q.created_at DESC 
                         LIMIT 5');
    $recent_quotes = $stmt->fetchAll();

    // 9. Recent Activity Log (demonstrating CONCAT and LEFT JOIN)
    $stmt = $pdo->query("SELECT a.*, 
                                CONCAT(u.first_name, ' ', u.last_name) AS user_full_name,
                                u.role AS user_role
                         FROM activity_log AS a 
                         LEFT JOIN users AS u ON a.user_id = u.id 
                         ORDER BY a.created_at DESC 
                         LIMIT 8");
    $recent_activities = $stmt->fetchAll();
}

include __DIR__ . '/../includes/head.php';
?>
<body>
<?php
include __DIR__ . '/../includes/topbar.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="dashboard-wrapper">
  <div class="container">

    <!-- Flash message display -->
    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" role="alert">
        <span><i class="fas fa-check-circle"></i> <?= htmlspecialchars($flash['message']) ?></span>
        <button type="button" class="alert-close" aria-label="Close">&times;</button>
      </div>
    <?php endif; ?>

    <!-- Dashboard Top Bar: Role Greeting & Actions -->
    <header class="dashboard-header">
      <div class="header-titles">
        <div class="role-pill-wrap">
          <span class="badge badge-<?= htmlspecialchars($current_user_role) ?>">
            <i class="fas <?= $is_admin ? 'fa-user-shield' : 'fa-user-tie' ?>"></i>
            <?= ucfirst(htmlspecialchars($current_user_role)) ?> Mode
          </span>
          <span class="header-date"><i class="far fa-calendar-alt"></i> <?= date('F j, Y') ?></span>
        </div>
        <h1>Overview Dashboard</h1>
        <p>
          Welcome, <strong><?= htmlspecialchars($current_user_name) ?></strong>!
          <?= $is_admin ? 'You have full administrative privileges across products, quotes, and reports.' : 'You can manage product listings and process assigned quote inquiries.' ?>
        </p>
      </div>

      <div class="header-actions">
        <a href="/pages/product-form.php" class="btn btn-primary" data-tooltip="Create a new promotional product">
          <i class="fas fa-plus"></i> Add Product
        </a>
        <a href="/pages/quotes.php" class="btn btn-outline" data-tooltip="Manage incoming quotation leads">
          <i class="fas fa-file-invoice-dollar"></i> View Quotes
        </a>
        <a href="/pages/reports.php" class="btn btn-secondary" data-tooltip="Open summary analytics">
          <i class="fas fa-chart-bar"></i> Reports
        </a>
      </div>
    </header>

    <!-- KPI Metric Cards Grid -->
    <section class="kpi-grid" aria-label="Key Performance Indicators">

      <!-- Card 1: Total Products -->
      <article class="kpi-card" data-tooltip="Click to view all products">
        <a href="/pages/products.php" class="kpi-card-link">
          <div class="kpi-icon icon-orange"><i class="fas fa-box-open"></i></div>
          <div class="kpi-content">
            <span class="kpi-label">Total Products</span>
            <h3 class="kpi-value"><?= number_format($total_products) ?></h3>
            <span class="kpi-subtext"><i class="fas fa-check"></i> <?= $active_products ?> Active Catalog Items</span>
          </div>
        </a>
      </article>

      <!-- Card 2: Quote Inquiries -->
      <article class="kpi-card" data-tooltip="Click to view quote inquiries">
        <a href="/pages/quotes.php" class="kpi-card-link">
          <div class="kpi-icon icon-blue"><i class="fas fa-file-invoice"></i></div>
          <div class="kpi-content">
            <span class="kpi-label">Quote Requests</span>
            <h3 class="kpi-value"><?= number_format($total_quotes) ?></h3>
            <span class="kpi-subtext" style="color:var(--orange-primary); font-weight:600;">
              <i class="fas fa-bell"></i> <?= $new_quotes ?> Pending Action
            </span>
          </div>
        </a>
      </article>

      <!-- Card 3: Categories -->
      <article class="kpi-card">
        <div class="kpi-icon icon-purple"><i class="fas fa-tags"></i></div>
        <div class="kpi-content">
          <span class="kpi-label">Main Categories</span>
          <h3 class="kpi-value"><?= number_format($total_categories) ?></h3>
          <span class="kpi-subtext"><i class="fas fa-layer-group"></i> 8 Standard Mega-Menu Roots</span>
        </div>
      </article>

      <!-- Card 4: Role-Specific Stat (Staff count for Admin, Assigned Quotes for Staff) -->
      <?php if ($is_admin): ?>
        <article class="kpi-card">
          <div class="kpi-icon icon-green"><i class="fas fa-users-cog"></i></div>
          <div class="kpi-content">
            <span class="kpi-label">Active Staff</span>
            <h3 class="kpi-value"><?= number_format($total_staff) ?></h3>
            <span class="kpi-subtext"><i class="fas fa-user-check"></i> Staff Sales Accounts</span>
          </div>
        </article>
      <?php else: ?>
        <article class="kpi-card" data-tooltip="Inquiries assigned to you">
          <a href="/pages/quotes.php?assigned=me" class="kpi-card-link">
            <div class="kpi-icon icon-green"><i class="fas fa-tasks"></i></div>
            <div class="kpi-content">
              <span class="kpi-label">Assigned To Me</span>
              <h3 class="kpi-value"><?= number_format($assigned_to_me) ?></h3>
              <span class="kpi-subtext"><i class="fas fa-clock"></i> My Active Inquiries</span>
            </div>
          </a>
        </article>
      <?php endif; ?>

    </section>

    <!-- Main Content Two-Column Grid -->
    <div class="dashboard-columns">

      <!-- Left Column: Recent Quotes Table -->
      <section class="dash-panel">
        <div class="panel-header">
          <div>
            <h2><i class="fas fa-file-signature" style="color:var(--orange-primary);"></i> Recent Quote Requests</h2>
            <p>Latest inquiries submitted via website storefront</p>
          </div>
          <a href="/pages/quotes.php" class="btn btn-outline btn-sm">View All (<?= $total_quotes ?>)</a>
        </div>

        <div class="table-responsive">
          <table class="dash-table" id="recent-quotes-table">
            <thead>
              <tr>
                <th>Customer / Company</th>
                <th>Product Type</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Date</th>
                <th style="text-align:right;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recent_quotes)): ?>
                <tr>
                  <td colspan="6" class="table-empty">
                    <i class="fas fa-inbox"></i> No quote requests recorded yet.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($recent_quotes as $q): ?>
                  <tr>
                    <td>
                      <strong><?= htmlspecialchars($q['customer_name']) ?></strong>
                      <div class="table-sub"><?= htmlspecialchars($q['company'] ?: 'Individual') ?></div>
                    </td>
                    <td>
                      <?= htmlspecialchars($q['product_name'] ?? $q['product_type'] ?? 'Custom Merch') ?>
                    </td>
                    <td><?= number_format($q['quantity'] ?? 0) ?></td>
                    <td>
                      <span class="badge badge-<?= htmlspecialchars($q['status']) ?>">
                        <?= ucfirst(htmlspecialchars($q['status'])) ?>
                      </span>
                    </td>
                    <td style="font-size:0.8125rem; color:var(--grey-dark);">
                      <?= date('M j, Y', strtotime($q['created_at'])) ?>
                    </td>
                    <td style="text-align:right;">
                      <a href="/pages/quotes.php?id=<?= $q['id'] ?>" class="btn btn-secondary btn-sm" data-tooltip="Review Quote #<?= $q['id'] ?>">
                        <i class="fas fa-eye"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Right Column: Audit Activity Log with Live Real-Time Filter -->
      <section class="dash-panel">
        <div class="panel-header">
          <div>
            <h2><i class="fas fa-history" style="color:var(--orange-primary);"></i> System Activity Log</h2>
            <p>Live audit trail of user and system events</p>
          </div>
        </div>

        <!-- Real-Time Filter Input (Demonstrates 'input' event in dashboard.js) -->
        <div class="activity-search-wrap">
          <i class="fas fa-search search-icon"></i>
          <input 
            type="text" 
            id="activity-filter" 
            class="form-control form-control-sm" 
            placeholder="Type to filter activities in real time..." 
            aria-label="Filter activity log"
          >
          <span class="clear-search" id="clear-activity-filter" style="display:none;">&times;</span>
        </div>

        <ul class="activity-list" id="activity-log-list">
          <?php if (empty($recent_activities)): ?>
            <li class="activity-empty">No activity records found.</li>
          <?php else: ?>
            <?php foreach ($recent_activities as $act): ?>
              <li class="activity-item" data-search="<?= htmlspecialchars(strtolower($act['details'] . ' ' . ($act['user_full_name'] ?? 'System') . ' ' . $act['action'])) ?>">
                <div class="activity-bullet">
                  <?php 
                    $icon = 'fa-info';
                    if (str_contains($act['action'], 'login')) $icon = 'fa-sign-in-alt';
                    elseif (str_contains($act['action'], 'create')) $icon = 'fa-plus';
                    elseif (str_contains($act['action'], 'update')) $icon = 'fa-edit';
                    elseif (str_contains($act['action'], 'delete')) $icon = 'fa-trash-alt';
                  ?>
                  <i class="fas <?= $icon ?>"></i>
                </div>
                <div class="activity-body">
                  <div class="activity-text"><?= htmlspecialchars($act['details']) ?></div>
                  <div class="activity-meta">
                    <span>
                      <i class="fas fa-user"></i> <?= htmlspecialchars($act['user_full_name'] ?? 'System') ?>
                    </span>
                    <span>•</span>
                    <span>
                      <i class="far fa-clock"></i> <?= date('M j, g:i a', strtotime($act['created_at'])) ?>
                    </span>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
        <div id="activity-no-match" style="display:none; text-align:center; padding:20px; color:var(--grey-mid); font-size:0.875rem;">
          <i class="fas fa-search"></i> No matching activity records found.
        </div>
      </section>

    </div>

  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
