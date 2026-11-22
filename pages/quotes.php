<?php
/**
 * pages/quotes.php — Quotation Request Lead Management
 * Protected route: requires authentication.
 * Dedicated Assets:
 *   - CSS: /assets/css/quotes.css
 *   - JS:  /assets/js/quotes.js
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../auth/guard.php';

$page_title = 'Quote Inquiries | CreativeKit 3A';
$extra_css  = ['/assets/css/admin.css', '/assets/css/quotes.css'];
$admin_heading = 'Quote Requests';
$extra_js   = ['/assets/js/quotes.js'];

$flash = get_flash();

// -------------------------------------------------------------
// Parameters & Filtering ($_GET)
// -------------------------------------------------------------
$filter_status = trim($_GET['status'] ?? '');
$filter_assigned = trim($_GET['assigned'] ?? '');
$search_query  = trim($_GET['q'] ?? '');

// -------------------------------------------------------------
// Fetch All Active Staff for Assignment Dropdowns (Admin Only)
// -------------------------------------------------------------
$staff_members = [];
if ($pdo) {
    $stmt = $pdo->query("SELECT id, CONCAT(first_name, ' ', last_name) AS full_name, role 
                         FROM users 
                         WHERE is_active = 1 
                         ORDER BY role ASC, first_name ASC");
    $staff_members = $stmt->fetchAll();
}

// -------------------------------------------------------------
// Fetch Quotes using LEFT JOIN, WHERE, LIKE, ORDER BY
// -------------------------------------------------------------
$quotes = [];
$status_counts = [
    'all'       => 0,
    'new'       => 0,
    'reviewing' => 0,
    'quoted'    => 0,
    'accepted'  => 0,
    'declined'  => 0,
];

if ($pdo) {
    // Count per status
    $counts_stmt = $pdo->query('SELECT status, COUNT(*) AS cnt FROM quote_requests GROUP BY status');
    while ($row = $counts_stmt->fetch()) {
        $status_counts[$row['status']] = (int)$row['cnt'];
        $status_counts['all'] += (int)$row['cnt'];
    }

    $sql = "SELECT 
                q.id,
                q.customer_name,
                q.company,
                q.email,
                q.phone,
                q.product_type,
                q.quantity,
                q.budget,
                q.deadline,
                q.details,
                q.status,
                q.assigned_to,
                q.created_at,
                p.id AS product_id,
                p.name AS product_name,
                p.sku AS product_sku,
                CONCAT(u.first_name, ' ', u.last_name) AS staff_name,
                u.email AS staff_email
            FROM quote_requests AS q
            LEFT JOIN products AS p ON q.product_id = p.id
            LEFT JOIN users AS u ON q.assigned_to = u.id
            WHERE 1=1";

    $params = [];

    // Filter by status
    if (!empty($filter_status) && in_array($filter_status, ['new', 'reviewing', 'quoted', 'accepted', 'declined'], true)) {
        $sql .= ' AND q.status = :status';
        $params[':status'] = $filter_status;
    }

    // Filter by assigned staff (e.g., 'me')
    if ($filter_assigned === 'me') {
        $sql .= ' AND q.assigned_to = :assigned_to';
        $params[':assigned_to'] = $current_user_id;
    }

    // Search query using LIKE
    if (!empty($search_query)) {
        // Native prepares (EMULATE_PREPARES=false) forbid reusing a named placeholder,
        // so each LIKE gets its own parameter.
        $sql .= ' AND (q.customer_name LIKE :s1 OR q.company LIKE :s2 OR q.email LIKE :s3 OR q.product_type LIKE :s4)';
        $like = '%' . $search_query . '%';
        $params += [':s1' => $like, ':s2' => $like, ':s3' => $like, ':s4' => $like];
    }

    $sql .= ' ORDER BY q.created_at DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $quotes = $stmt->fetchAll();
}

include __DIR__ . '/../includes/head.php';
?>
<body class="admin-body">
<?php include __DIR__ . '/../includes/admin-layout.php'; ?>

<main class="quotes-wrapper">
  <div class="container">

    <!-- Flash message -->
    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" role="alert">
        <span><i class="fas fa-info-circle"></i> <?= htmlspecialchars($flash['message']) ?></span>
        <button type="button" class="alert-close" aria-label="Close">&times;</button>
      </div>
    <?php endif; ?>

    <!-- Breadcrumb & Header -->
    <header class="quotes-header">
      <div>
        <nav class="breadcrumb-nav" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a>
          <span>/</span>
          <span class="current">Quote Requests</span>
        </nav>
        <h1>Custom Quote Inquiries</h1>
        <p>Review customer quotation submissions, update lead statuses, and assign staff coordinators.</p>
      </div>

      <div class="header-actions">
        <button class="btn btn-primary" onclick="openModal('quote-modal')">
          <i class="fas fa-plus"></i> Submit New Inquiry
        </button>
      </div>
    </header>

    <!-- Status Filter Tab Pills (Event 3: 'click' or 'change') -->
    <div class="status-tabs-bar" role="tablist">
      <a href="<?= BASE_URL ?>/pages/quotes.php" class="status-tab <?= empty($filter_status) ? 'active' : '' ?>">
        All Inquiries <span class="tab-count"><?= $status_counts['all'] ?></span>
      </a>
      <a href="<?= BASE_URL ?>/pages/quotes.php?status=new" class="status-tab <?= ($filter_status === 'new') ? 'active' : '' ?>">
        <i class="fas fa-envelope-open-text" style="color:#2563EB;"></i> New <span class="tab-count"><?= $status_counts['new'] ?></span>
      </a>
      <a href="<?= BASE_URL ?>/pages/quotes.php?status=reviewing" class="status-tab <?= ($filter_status === 'reviewing') ? 'active' : '' ?>">
        <i class="fas fa-search" style="color:#D97706;"></i> Reviewing <span class="tab-count"><?= $status_counts['reviewing'] ?></span>
      </a>
      <a href="<?= BASE_URL ?>/pages/quotes.php?status=quoted" class="status-tab <?= ($filter_status === 'quoted') ? 'active' : '' ?>">
        <i class="fas fa-paper-plane" style="color:#4338CA;"></i> Quoted <span class="tab-count"><?= $status_counts['quoted'] ?></span>
      </a>
      <a href="<?= BASE_URL ?>/pages/quotes.php?status=accepted" class="status-tab <?= ($filter_status === 'accepted') ? 'active' : '' ?>">
        <i class="fas fa-check-circle" style="color:#059669;"></i> Accepted <span class="tab-count"><?= $status_counts['accepted'] ?></span>
      </a>
      <a href="<?= BASE_URL ?>/pages/quotes.php?status=declined" class="status-tab <?= ($filter_status === 'declined') ? 'active' : '' ?>">
        <i class="fas fa-times-circle" style="color:#DC2626;"></i> Declined <span class="tab-count"><?= $status_counts['declined'] ?></span>
      </a>
    </div>

    <!-- Real-Time Search & Quick Filter Bar -->
    <div class="quotes-filter-card">
      <div class="quotes-search-wrap">
        <i class="fas fa-search search-icon"></i>
        <input 
          type="text" 
          id="quotes-search" 
          class="form-control" 
          placeholder="Filter by customer name, company, or email in real time..." 
          value="<?= htmlspecialchars($search_query) ?>"
          autocomplete="off"
        >
        <span class="clear-search-btn" id="clear-quotes-search" style="<?= empty($search_query) ? 'display:none;' : '' ?>">&times;</span>
      </div>

      <div class="quotes-filter-right">
        <span class="results-info">
          Found <strong id="quotes-count"><?= count($quotes) ?></strong> requests
        </span>
      </div>
    </div>

    <!-- Quotes Responsive Table & Cards -->
    <div class="table-card">
      <div class="table-responsive">
        <table class="quotes-table" id="quotes-table">
          <thead>
            <tr>
              <th style="width:60px;">ID</th>
              <th>Customer & Company</th>
              <th>Requested Product</th>
              <th>Quantity & Budget</th>
              <th>Deadline</th>
              <th>Status</th>
              <th>Assigned Staff</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody id="quotes-tbody">
            <?php if (empty($quotes)): ?>
              <tr>
                <td colspan="8" class="table-empty">
                  <i class="fas fa-folder-open" style="font-size:2rem; display:block; margin-bottom:10px; color:var(--grey-mid);"></i>
                  No quote inquiries found matching this view.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($quotes as $q): ?>
                <tr class="quote-row"
                    data-id="<?= $q['id'] ?>"
                    data-customer="<?= htmlspecialchars(strtolower($q['customer_name'])) ?>"
                    data-company="<?= htmlspecialchars(strtolower($q['company'] ?? '')) ?>"
                    data-email="<?= htmlspecialchars(strtolower($q['email'])) ?>"
                    data-product="<?= htmlspecialchars(strtolower($q['product_name'] ?? $q['product_type'] ?? '')) ?>"
                    data-status="<?= htmlspecialchars($q['status']) ?>"
                >
                  <!-- Ref ID -->
                  <td>
                    <span class="quote-ref-badge">#<?= $q['id'] ?></span>
                  </td>

                  <!-- Customer Name & Details -->
                  <td>
                    <strong class="customer-name"><?= htmlspecialchars($q['customer_name']) ?></strong>
                    <div class="company-sub"><?= htmlspecialchars($q['company'] ?: 'Individual Buyer') ?></div>
                    <div class="contact-sub">
                      <a href="mailto:<?= htmlspecialchars($q['email']) ?>" data-tooltip="Email customer">
                        <i class="fas fa-envelope"></i> <?= htmlspecialchars($q['email']) ?>
                      </a>
                    </div>
                  </td>

                  <!-- Product Requested -->
                  <td>
                    <strong><?= htmlspecialchars($q['product_name'] ?? $q['product_type'] ?? 'Custom Items') ?></strong>
                    <?php if (!empty($q['product_sku'])): ?>
                      <div class="table-sub"><i class="fas fa-barcode"></i> <?= htmlspecialchars($q['product_sku']) ?></div>
                    <?php endif; ?>
                  </td>

                  <!-- Quantity & Budget -->
                  <td>
                    <div><strong><?= number_format($q['quantity'] ?? 0) ?></strong> pcs</div>
                    <div class="table-sub" style="color:var(--orange-primary); font-weight:600;">
                      <?= htmlspecialchars($q['budget'] ?: 'Not specified') ?>
                    </div>
                  </td>

                  <!-- Deadline -->
                  <td>
                    <?php if (!empty($q['deadline'])): ?>
                      <span class="deadline-tag" data-tooltip="Target Delivery Date">
                        <i class="far fa-calendar-check"></i> <?= date('M j, Y', strtotime($q['deadline'])) ?>
                      </span>
                    <?php else: ?>
                      <span style="color:var(--grey-mid);">Flexible</span>
                    <?php endif; ?>
                  </td>

                  <!-- Status Dropdown (Event: 'change') -->
                  <td>
                    <form action="<?= BASE_URL ?>/actions/quote-actions.php" method="POST" class="status-quick-form" style="margin:0;">
                      <input type="hidden" name="action" value="update_status">
                      <input type="hidden" name="id" value="<?= $q['id'] ?>">
                      <select name="status" class="status-select-badge badge-<?= htmlspecialchars($q['status']) ?>" onchange="this.form.submit()">
                        <option value="new"       <?= ($q['status'] === 'new')       ? 'selected' : '' ?>>New</option>
                        <option value="reviewing" <?= ($q['status'] === 'reviewing') ? 'selected' : '' ?>>Reviewing</option>
                        <option value="quoted"    <?= ($q['status'] === 'quoted')    ? 'selected' : '' ?>>Quoted</option>
                        <option value="accepted"  <?= ($q['status'] === 'accepted')  ? 'selected' : '' ?>>Accepted</option>
                        <option value="declined"  <?= ($q['status'] === 'declined')  ? 'selected' : '' ?>>Declined</option>
                      </select>
                    </form>
                  </td>

                  <!-- Assigned Coordinator -->
                  <td>
                    <?php if ($is_admin): ?>
                      <form action="<?= BASE_URL ?>/actions/quote-actions.php" method="POST" class="assign-form" style="margin:0;">
                        <input type="hidden" name="action" value="assign">
                        <input type="hidden" name="id" value="<?= $q['id'] ?>">
                        <select name="assigned_to" class="form-control form-control-sm" style="font-size:0.75rem; padding:4px 8px; width:auto;" onchange="this.form.submit()">
                          <option value="">Unassigned</option>
                          <?php foreach ($staff_members as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= ($q['assigned_to'] == $s['id']) ? 'selected' : '' ?>>
                              <?= htmlspecialchars($s['full_name']) ?> (<?= ucfirst($s['role']) ?>)
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </form>
                    <?php else: ?>
                      <span style="font-size:0.8125rem;">
                        <i class="fas fa-user-circle" style="color:var(--orange-primary);"></i>
                        <?= htmlspecialchars($q['staff_name'] ?: 'Unassigned') ?>
                      </span>
                    <?php endif; ?>
                  </td>

                  <!-- Actions -->
                  <td style="text-align:right;">
                    <div class="action-buttons">
                      <!-- View Details Modal Trigger -->
                      <button 
                        type="button" 
                        class="btn-action btn-view-quote" 
                        data-id="<?= $q['id'] ?>"
                        data-name="<?= htmlspecialchars($q['customer_name']) ?>"
                        data-company="<?= htmlspecialchars($q['company'] ?? 'Individual') ?>"
                        data-email="<?= htmlspecialchars($q['email']) ?>"
                        data-phone="<?= htmlspecialchars($q['phone'] ?? 'N/A') ?>"
                        data-product="<?= htmlspecialchars($q['product_name'] ?? $q['product_type'] ?? 'Custom') ?>"
                        data-qty="<?= htmlspecialchars($q['quantity']) ?>"
                        data-budget="<?= htmlspecialchars($q['budget'] ?? 'N/A') ?>"
                        data-deadline="<?= htmlspecialchars($q['deadline'] ?? 'Flexible') ?>"
                        data-details="<?= htmlspecialchars($q['details'] ?? 'No additional notes provided.') ?>"
                        data-status="<?= htmlspecialchars($q['status']) ?>"
                        data-tooltip="View complete quotation inquiry"
                      >
                        <i class="fas fa-eye"></i>
                      </button>

                      <!-- Delete Quote (Role-Restricted: Admin Only) -->
                      <?php if ($is_admin): ?>
                        <button 
                          type="button" 
                          class="btn-action btn-action-delete btn-open-delete-quote"
                          data-id="<?= $q['id'] ?>"
                          data-name="Quote #<?= $q['id'] ?> from <?= htmlspecialchars($q['customer_name']) ?>"
                          data-tooltip="Delete quote inquiry permanently"
                        >
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div id="quotes-no-match" style="display:none; text-align:center; padding:40px; color:var(--grey-dark);">
        <i class="fas fa-search" style="font-size:2rem; color:var(--orange-primary); display:block; margin-bottom:12px;"></i>
        <h4>No matching quote inquiries</h4>
        <p style="font-size:0.875rem; color:var(--grey-mid);">Adjust your search query or select another status filter tab.</p>
      </div>
    </div>

  </div>
</main>

<!-- ============================================================
     VIEW QUOTE DETAILS MODAL
     ============================================================ -->
<div class="modal-overlay" id="quote-detail-modal" role="dialog" aria-modal="true" aria-labelledby="view-modal-title">
  <div class="modal-card" style="max-width:580px;">
    <div class="modal-header">
      <h3 id="view-modal-title">
        <i class="fas fa-file-invoice" style="color:var(--orange-primary);"></i> Quote Request Details
      </h3>
      <button type="button" class="modal-close" id="btn-close-view-modal">&times;</button>
    </div>
    <div class="modal-body" id="quote-detail-body">
      <div class="detail-header-block">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <h4 id="detail-customer-name" style="font-size:1.125rem; font-weight:700;">Customer</h4>
          <span class="badge" id="detail-status-badge">Status</span>
        </div>
        <p id="detail-company" style="font-size:0.875rem; color:var(--grey-dark);">Company</p>
      </div>

      <div class="detail-grid">
        <div>
          <span class="detail-label">Email Address</span>
          <p id="detail-email" class="detail-value">—</p>
        </div>
        <div>
          <span class="detail-label">Contact Phone</span>
          <p id="detail-phone" class="detail-value">—</p>
        </div>
        <div>
          <span class="detail-label">Target Product</span>
          <p id="detail-product" class="detail-value">—</p>
        </div>
        <div>
          <span class="detail-label">Requested Quantity</span>
          <p id="detail-qty" class="detail-value">—</p>
        </div>
        <div>
          <span class="detail-label">Budget Range</span>
          <p id="detail-budget" class="detail-value">—</p>
        </div>
        <div>
          <span class="detail-label">Target Deadline</span>
          <p id="detail-deadline" class="detail-value">—</p>
        </div>
      </div>

      <div style="margin-top:16px;">
        <span class="detail-label">Client Notes & Personalization Requirements</span>
        <div id="detail-notes" class="detail-notes-box">
          —
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="btn-close-view-footer">Close</button>
    </div>
  </div>
</div>

<!-- ============================================================
     DELETE QUOTE CONFIRMATION MODAL (Event 1: 'click')
     ============================================================ -->
<div class="modal-overlay" id="delete-quote-modal" role="dialog" aria-modal="true" aria-labelledby="del-quote-title">
  <div class="modal-card">
    <div class="modal-header" style="background:#FEF2F2; border-bottom-color:#FEE2E2;">
      <h3 id="del-quote-title" style="color:#991B1B;">
        <i class="fas fa-exclamation-triangle" style="color:var(--danger);"></i> Confirm Quote Deletion
      </h3>
      <button type="button" class="modal-close" id="btn-close-del-quote">&times;</button>
    </div>
    <div class="modal-body">
      <p style="font-size:0.9375rem; color:var(--black); margin-bottom:12px;">
        Are you sure you want to permanently delete this quote request?
      </p>
      <div style="background:var(--grey-light); padding:12px; border-radius:var(--radius-md); border-left:4px solid var(--danger); margin-bottom:14px;">
        <strong id="modal-del-quote-name" style="color:var(--black-deep);">Quote #</strong>
      </div>
      <p style="font-size:0.8125rem; color:var(--danger);">
        This inquiry will be permanently deleted from the database.
      </p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="btn-cancel-del-quote">Cancel</button>
      <form action="<?= BASE_URL ?>/actions/quote-actions.php" method="POST" style="margin:0;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="modal-del-quote-id" value="">
        <button type="submit" class="btn btn-danger">
          <i class="fas fa-trash-alt"></i> Yes, Delete Quote
        </button>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
