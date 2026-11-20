<?php
/**
 * admin/index.php — Management Information, Inventory & Purchasing Portal
 * CreativeKit 3A Enterprise Management Portal
 * Supports both standalone browser execution and live MySQL database integration.
 */

require_once __DIR__ . '/../includes/db.php';
$pdo = get_db_connection();
$db_connected = ($pdo !== null);

// Fetch quick stats from MySQL if available
$db_metrics = null;
if ($db_connected) {
    try {
        $stmt = $pdo->query("SELECT * FROM v_mis_executive_kpi");
        $db_metrics = $stmt->fetch();
    } catch (\Throwable $e) {
        $db_metrics = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CreativeKit 3A — Management Portal (MIS, Purchasing & Inventory)</title>

  <!-- Font Awesome -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">

  <!-- Admin System Stylesheet -->
  <link rel="stylesheet" href="../assets/css/admin-system.css">
</head>
<body class="admin-body">

  <!-- Top Navigation Bar -->
  <header class="admin-navbar">
    <a href="index.php" class="admin-brand">
      <img src="../assets/images/logo.png" alt="CreativeKit 3A Logo" onerror="this.style.display='none'">
      <span class="admin-brand-title">CreativeKit 3A</span>
      <span class="admin-brand-badge">Enterprise Portal</span>
    </a>

    <div class="admin-nav-actions">
      <!-- Database status pill -->
      <div style="font-size:0.8rem;padding:4px 10px;border-radius:20px;font-weight:600;display:flex;align-items:center;gap:6px;background:<?php echo $db_connected ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)'; ?>;color:<?php echo $db_connected ? '#10b981' : '#f59e0b'; ?>;">
        <i class="fas <?php echo $db_connected ? 'fa-database' : 'fa-server'; ?>"></i>
        <span><?php echo $db_connected ? 'MySQL Connected' : 'Local Data Engine'; ?></span>
      </div>

      <div class="admin-clock">
        <i class="far fa-clock"></i> <span id="admin-clock-time">--:--:--</span>
      </div>
      <a href="../index.php" class="btn-storefront">
        <i class="fas fa-store"></i> View Storefront
      </a>
    </div>
  </header>

  <!-- Sub-navigation Tabs (The 3 Core Systems) -->
  <nav class="admin-tabs-bar" aria-label="Systems Navigation">
    <div class="admin-tabs">
      <button class="admin-tab active" data-tab="information">
        <i class="fas fa-chart-pie"></i> Information System (MIS)
      </button>
      <button class="admin-tab" data-tab="inventory">
        <i class="fas fa-boxes-stacked"></i> Inventory System
        <span class="badge" id="tab-badge-inventory">0</span>
      </button>
      <button class="admin-tab" data-tab="purchasing">
        <i class="fas fa-cart-flatbed"></i> Purchasing System
        <span class="badge" id="tab-badge-purchasing">0</span>
      </button>
    </div>

    <div>
      <button class="btn-admin btn-admin-secondary btn-admin-sm" onclick="AdminApp.exportReportCSV()">
        <i class="fas fa-file-csv"></i> Export Inventory Report
      </button>
    </div>
  </nav>

  <!-- Main Container -->
  <main class="admin-container">

    <!-- ============================================================
         1. INFORMATION SYSTEM (MIS & EXECUTIVE DASHBOARD)
         ============================================================ -->
    <section class="tab-content-panel" id="panel-information">
      
      <!-- Executive KPI Cards -->
      <div class="kpi-grid">
        <div class="kpi-card">
          <div class="kpi-icon primary"><i class="fas fa-coins"></i></div>
          <div class="kpi-info">
            <h4>Inventory Valuation</h4>
            <div class="kpi-value" id="kpi-inventory-val">₱0.00</div>
            <div class="kpi-subtext">Current asset value in warehouses</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon success"><i class="fas fa-cubes"></i></div>
          <div class="kpi-info">
            <h4>Total Units On Hand</h4>
            <div class="kpi-value" id="kpi-inventory-units">0</div>
            <div class="kpi-subtext">Across 3 active storage hubs</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon danger"><i class="fas fa-triangle-exclamation"></i></div>
          <div class="kpi-info">
            <h4>Low Stock Alerts</h4>
            <div class="kpi-value" id="kpi-low-stock" style="color:var(--admin-danger);">0</div>
            <div class="kpi-subtext">Items below reorder threshold</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon warning"><i class="fas fa-truck-ramp-box"></i></div>
          <div class="kpi-info">
            <h4>Procurement Spend</h4>
            <div class="kpi-value" id="kpi-procurement-spend">₱0.00</div>
            <div class="kpi-subtext">Total received purchase orders</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon purple"><i class="fas fa-file-invoice-dollar"></i></div>
          <div class="kpi-info">
            <h4>Active PO Orders</h4>
            <div class="kpi-value" id="kpi-active-pos">0</div>
            <div class="kpi-subtext">Pending / in-transit deliveries</div>
          </div>
        </div>
      </div>

      <!-- MIS Visual Breakdown Grid -->
      <div class="grid-2col">
        <div class="admin-card">
          <div class="admin-card-header">
            <h3><i class="fas fa-chart-simple"></i> Merchandising Category Distribution</h3>
          </div>
          <div style="padding:24px;">
            <div style="margin-bottom:14px;">
              <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:0.85rem;font-weight:600;">
                <span>Drinkware (Tumblers, Flasks, Mugs)</span>
                <span>35% (2,410 units)</span>
              </div>
              <div style="background:#e2e8f0;height:10px;border-radius:5px;overflow:hidden;">
                <div style="background:#1a73e8;width:35%;height:100%;"></div>
              </div>
            </div>

            <div style="margin-bottom:14px;">
              <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:0.85rem;font-weight:600;">
                <span>Pen &amp; Paper (Bamboo Pens, Journals)</span>
                <span>45% (4,180 units)</span>
              </div>
              <div style="background:#e2e8f0;height:10px;border-radius:5px;overflow:hidden;">
                <div style="background:#10b981;width:45%;height:100%;"></div>
              </div>
            </div>

            <div style="margin-bottom:14px;">
              <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:0.85rem;font-weight:600;">
                <span>Bags &amp; Pouches (Totes, Backpacks)</span>
                <span>12% (1,390 units)</span>
              </div>
              <div style="background:#e2e8f0;height:10px;border-radius:5px;overflow:hidden;">
                <div style="background:#f59e0b;width:12%;height:100%;"></div>
              </div>
            </div>

            <div>
              <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:0.85rem;font-weight:600;">
                <span>Apparel &amp; Tech Accessories (Jackets, Chargers)</span>
                <span>8% (830 units)</span>
              </div>
              <div style="background:#e2e8f0;height:10px;border-radius:5px;overflow:hidden;">
                <div style="background:#8b5cf6;width:8%;height:100%;"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="admin-card">
          <div class="admin-card-header">
            <h3><i class="fas fa-warehouse"></i> Warehouse Utilization Snapshot</h3>
          </div>
          <div style="padding:24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:10px;">
              <div>
                <strong style="color:#0f172a;">Main Central Warehouse (WH-QC)</strong>
                <div style="font-size:0.75rem;color:#64748b;">Diliman, Quezon City</div>
              </div>
              <span class="badge-status badge-in-stock">Primary Facility (78% Cap.)</span>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:10px;">
              <div>
                <strong style="color:#0f172a;">Metro Manila Distribution Hub (WH-MM)</strong>
                <div style="font-size:0.75rem;color:#64748b;">FTI Complex, Taguig City</div>
              </div>
              <span class="badge-status badge-sent">Distribution Depot (42% Cap.)</span>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px;border:1px solid #e2e8f0;border-radius:8px;">
              <div>
                <strong style="color:#0f172a;">Visayas Regional Depot (WH-CEB)</strong>
                <div style="font-size:0.75rem;color:#64748b;">Mandaue City, Cebu</div>
              </div>
              <span class="badge-status badge-draft">Regional Satellite (25% Cap.)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Management Audit Trail & System Logs -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h3><i class="fas fa-clipboard-list"></i> Enterprise Activity Log &amp; Audit Trail</h3>
        </div>
        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Timestamp</th>
                <th>Module</th>
                <th>Action</th>
                <th>Operation Details</th>
                <th>Operator</th>
              </tr>
            </thead>
            <tbody id="audit-logs-table-body">
              <!-- Rendered via admin-system.js -->
            </tbody>
          </table>
        </div>
      </div>

    </section>

    <!-- ============================================================
         2. INVENTORY SYSTEM
         ============================================================ -->
    <section class="tab-content-panel" id="panel-inventory" style="display:none;">
      
      <div class="admin-card">
        <div class="admin-card-header">
          <h3><i class="fas fa-boxes-stacked"></i> Real-Time Stock &amp; Warehouse Matrix</h3>
          
          <div class="admin-card-tools">
            <input type="text" id="inv-search-input" class="admin-search-input" placeholder="Search SKU or item...">
            
            <select id="inv-category-select" class="admin-select">
              <option value="all">All Categories</option>
              <option value="Accessories & Gadgets">Accessories &amp; Gadgets</option>
              <option value="Apparel">Apparel</option>
              <option value="Bags & Pouches">Bags &amp; Pouches</option>
              <option value="Drinkware">Drinkware</option>
              <option value="Pen & Paper">Pen &amp; Paper</option>
              <option value="PU Leather">PU Leather</option>
            </select>

            <select id="inv-status-select" class="admin-select">
              <option value="all">All Statuses</option>
              <option value="in-stock">In Stock</option>
              <option value="low-stock">Low Stock (Alerts)</option>
              <option value="out-of-stock">Out of Stock</option>
            </select>
          </div>
        </div>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>SKU</th>
                <th>Product Description</th>
                <th>Location</th>
                <th>On Hand</th>
                <th>Reserved</th>
                <th>Available</th>
                <th>Reorder Point</th>
                <th>Status</th>
                <th>Asset Valuation</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody id="inventory-table-body">
              <!-- Rendered via admin-system.js -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Stock Movement History Ledger -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h3><i class="fas fa-history"></i> Stock Transaction &amp; Movement Ledger</h3>
        </div>
        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Movement Ref</th>
                <th>Type</th>
                <th>Source / Ref</th>
                <th>Product Name</th>
                <th>Qty Change</th>
                <th>Warehouse</th>
                <th>Logged Date</th>
              </tr>
            </thead>
            <tbody id="movements-table-body">
              <!-- Rendered via admin-system.js -->
            </tbody>
          </table>
        </div>
      </div>

    </section>

    <!-- ============================================================
         3. PURCHASING SYSTEM (PROCUREMENT & SUPPLIERS)
         ============================================================ -->
    <section class="tab-content-panel" id="panel-purchasing" style="display:none;">

      <div class="admin-card">
        <div class="admin-card-header">
          <h3><i class="fas fa-file-invoice-dollar"></i> Purchase Orders &amp; Procurement Orders</h3>
          
          <button class="btn-admin btn-admin-primary" onclick="AdminApp.openCreatePOModal()">
            <i class="fas fa-plus-circle"></i> Create Purchase Order
          </button>
        </div>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>PO Number</th>
                <th>Supplier / Vendor</th>
                <th>Order Date</th>
                <th>Expected Delivery</th>
                <th>PO Status</th>
                <th>Payment</th>
                <th>Total Amount</th>
                <th style="text-align:right;">Goods Receipt</th>
              </tr>
            </thead>
            <tbody id="purchasing-table-body">
              <!-- Rendered via admin-system.js -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Supplier Directory -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h3><i class="fas fa-building"></i> Verified Suppliers &amp; Vendors Directory</h3>
        </div>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Supplier Code</th>
                <th>Company Name</th>
                <th>Contact Representative</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Terms</th>
                <th>Vendor Rating</th>
              </tr>
            </thead>
            <tbody id="suppliers-table-body">
              <!-- Rendered via admin-system.js -->
            </tbody>
          </table>
        </div>
      </div>

    </section>

  </main>

  <!-- ============================================================
       MODAL 1: STOCK ADJUSTMENT
       ============================================================ -->
  <div class="admin-modal-overlay" id="modal-stock-adjust">
    <div class="admin-modal">
      <div class="admin-modal-header">
        <h3><i class="fas fa-sliders-h"></i> Inventory Stock Adjustment</h3>
        <button class="cart-drawer-close" onclick="AdminApp.closeStockAdjustModal()">&times;</button>
      </div>
      <div class="admin-modal-body">
        <input type="hidden" id="adj-item-id">
        
        <div style="background:#f8fafc;padding:12px;border-radius:8px;border:1px solid #e2e8f0;">
          <div style="font-weight:700;font-size:1rem;" id="adj-item-name">Product Name</div>
          <div style="font-size:0.85rem;color:#64748b;margin-top:4px;">
            Current On-Hand Stock: <strong id="adj-current-qty" style="color:#0f172a;">0</strong> units
          </div>
        </div>

        <div class="form-group">
          <label for="adj-type">Adjustment Action</label>
          <select id="adj-type" class="admin-select">
            <option value="in">Stock In (Add pieces received)</option>
            <option value="out">Stock Out (Deduct pieces dispatched)</option>
            <option value="set">Set Exact Count (Physical audit overwrite)</option>
          </select>
        </div>

        <div class="form-group">
          <label for="adj-qty">Quantity</label>
          <input type="number" id="adj-qty" placeholder="e.g. 50" min="1">
        </div>

        <div class="form-group">
          <label for="adj-remarks">Audit Reason / Remarks</label>
          <textarea id="adj-remarks" rows="2" placeholder="e.g. Physical inventory reconciliation or damaged batch write-off"></textarea>
        </div>
      </div>
      <div class="admin-modal-footer">
        <button class="btn-admin btn-admin-secondary" onclick="AdminApp.closeStockAdjustModal()">Cancel</button>
        <button class="btn-admin btn-admin-primary" onclick="AdminApp.submitStockAdjust()">
          <i class="fas fa-save"></i> Save Adjustment
        </button>
      </div>
    </div>
  </div>

  <!-- ============================================================
       MODAL 2: CREATE PURCHASE ORDER
       ============================================================ -->
  <div class="admin-modal-overlay" id="modal-create-po">
    <div class="admin-modal">
      <div class="admin-modal-header">
        <h3><i class="fas fa-cart-plus"></i> Create Purchase Order</h3>
        <button class="cart-drawer-close" onclick="AdminApp.closeCreatePOModal()">&times;</button>
      </div>
      <div class="admin-modal-body">
        <div class="form-group">
          <label for="po-supplier-select">Select Supplier / Manufacturer</label>
          <select id="po-supplier-select" class="admin-select">
            <!-- Populated via JS -->
          </select>
        </div>

        <div class="form-group">
          <label for="po-warehouse-select">Receiving Destination Warehouse</label>
          <select id="po-warehouse-select" class="admin-select">
            <option value="WH-QC">Main Central Warehouse (WH-QC)</option>
            <option value="WH-MM">Metro Manila Distribution Hub (WH-MM)</option>
            <option value="WH-CEB">Visayas Regional Depot (WH-CEB)</option>
          </select>
        </div>

        <div class="form-group">
          <label for="po-item-name">Item Description</label>
          <input type="text" id="po-item-name" placeholder="e.g. 450ml Bamboo Tumbler Blanks">
        </div>

        <div class="grid-2col">
          <div class="form-group">
            <label for="po-item-qty">Order Quantity</label>
            <input type="number" id="po-item-qty" placeholder="e.g. 250" min="1">
          </div>
          <div class="form-group">
            <label for="po-item-cost">Unit Cost (₱)</label>
            <input type="number" id="po-item-cost" placeholder="e.g. 175.00" step="0.5" min="1">
          </div>
        </div>

        <div class="form-group">
          <label for="po-exp-date">Expected Delivery Date</label>
          <input type="date" id="po-exp-date">
        </div>
      </div>
      <div class="admin-modal-footer">
        <button class="btn-admin btn-admin-secondary" onclick="AdminApp.closeCreatePOModal()">Cancel</button>
        <button class="btn-admin btn-admin-primary" onclick="AdminApp.submitCreatePO()">
          <i class="fas fa-paper-plane"></i> Issue Purchase Order
        </button>
      </div>
    </div>
  </div>

  <!-- Admin System JavaScript -->
  <script src="../assets/js/admin-system.js"></script>
</body>
</html>
