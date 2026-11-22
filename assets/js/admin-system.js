/* ============================================================
   admin-system.js — CreativeKit 3A Management Portal
   Core logic for Purchasing, Inventory, and Information Systems
   ============================================================ */

(function () {
  'use strict';

  // Local storage keys for persistent demo & live state
  const KEYS = {
    INVENTORY: 'ck_admin_inventory',
    PURCHASING: 'ck_admin_purchasing',
    SUPPLIERS: 'ck_admin_suppliers',
    AUDIT_LOGS: 'ck_admin_audit_logs',
    MOVEMENTS: 'ck_admin_movements',
    USERS: 'ck_admin_users'
  };

  /* ── 1. DEFAULT SEED DATA ── */
  const DEFAULT_SUPPLIERS = [
    { id: 1, code: 'SUP-PH-001', name: 'Luzon Garments & Apparel Mfg', contact: 'Maricel Bautista', email: 'orders@luzongarments.ph', phone: '+63 2 8712 3456', location: 'Taytay, Rizal', terms: 'Net 30', rating: 4.9 },
    { id: 2, code: 'SUP-PH-002', name: 'Pacific Tech & Gadgets Import Corp', contact: 'Kenji Tanaka', email: 'supply@pacifictech.com.ph', phone: '+63 2 8923 8812', location: 'Binondo, Manila', terms: 'Net 15', rating: 4.8 },
    { id: 3, code: 'SUP-PH-003', name: 'EcoDrinkware Phils Corp', contact: 'Eduardo Ramos', email: 'sales@ecodrinkware.ph', phone: '+63 917 555 1290', location: 'Sta. Rosa, Laguna', terms: 'Net 30', rating: 4.95 },
    { id: 4, code: 'SUP-PH-004', name: 'Premium Leather & Packaging Solutions', contact: 'Sofia Tan', email: 'inquiry@premiumleather.ph', phone: '+63 2 8540 2211', location: 'Marikina City', terms: 'Net 30', rating: 4.7 }
  ];

  const DEFAULT_INVENTORY = [
    { id: 1, sku: 'ACC-CAR-001', name: 'Car Charger Adapters', category: 'Accessories & Gadgets', wh: 'WH-QC', qty: 480, reserved: 50, reorder: 60, cost: 45.00, price: 85.00 },
    { id: 2, sku: 'ACC-CAR-002', name: 'Car Organizers & Holders', category: 'Accessories & Gadgets', wh: 'WH-QC', qty: 310, reserved: 30, reorder: 50, cost: 70.00, price: 120.00 },
    { id: 3, sku: 'ACC-CAR-003', name: 'Dash Cams', category: 'Accessories & Gadgets', wh: 'WH-QC', qty: 18, reserved: 5, reorder: 25, cost: 280.00, price: 450.00 },
    { id: 4, sku: 'APP-JKT-001', name: 'Corporate Jackets', category: 'Apparel', wh: 'WH-QC', qty: 14, reserved: 10, reorder: 30, cost: 480.00, price: 720.00 },
    { id: 5, sku: 'APP-JKT-001', name: 'Corporate Jackets', category: 'Apparel', wh: 'WH-MM', qty: 22, reserved: 5, reorder: 20, cost: 480.00, price: 720.00 },
    { id: 6, sku: 'APP-JKT-002', name: 'Bomber Jackets', category: 'Apparel', wh: 'WH-QC', qty: 95, reserved: 15, reorder: 25, cost: 420.00, price: 650.00 },
    { id: 7, sku: 'BAG-LPT-001', name: 'Laptop Backpacks', category: 'Bags & Pouches', wh: 'WH-QC', qty: 140, reserved: 25, reorder: 30, cost: 320.00, price: 520.00 },
    { id: 8, sku: 'BAG-TOT-001', name: 'Classic Zip Totes', category: 'Bags & Pouches', wh: 'WH-QC', qty: 1250, reserved: 200, reorder: 150, cost: 65.00, price: 120.00 },
    { id: 9, sku: 'DRK-TMB-001', name: 'Bamboo Tumblers', category: 'Drinkware', wh: 'WH-QC', qty: 850, reserved: 120, reorder: 100, cost: 120.00, price: 220.00 },
    { id: 10, sku: 'DRK-TMB-002', name: 'Stainless Steel Tumblers', category: 'Drinkware', wh: 'WH-QC', qty: 620, reserved: 90, reorder: 80, cost: 180.00, price: 310.00 },
    { id: 11, sku: 'DRK-MUG-001', name: 'Ceramic Mugs', category: 'Drinkware', wh: 'WH-QC', qty: 940, reserved: 100, reorder: 150, cost: 48.00, price: 95.00 },
    { id: 12, sku: 'PEN-BAM-001', name: 'Bamboo Pens', category: 'Pen & Paper', wh: 'WH-QC', qty: 3400, reserved: 500, reorder: 300, cost: 16.00, price: 35.00 },
    { id: 13, sku: 'PPR-NBK-001', name: 'Custom Notebooks & Journals', category: 'Pen & Paper', wh: 'WH-QC', qty: 780, reserved: 150, reorder: 100, cost: 52.00, price: 95.00 },
    { id: 14, sku: 'LEA-WLT-001', name: 'PU Leather Wallets', category: 'PU Leather', wh: 'WH-QC', qty: 19, reserved: 4, reorder: 30, cost: 95.00, price: 195.00 }
  ];

  const DEFAULT_PURCHASE_ORDERS = [
    {
      id: 1,
      poNumber: 'PO-2026-001',
      supplier: 'EcoDrinkware Phils Corp',
      orderDate: '2026-10-15',
      expectedDate: '2026-10-25',
      receivedDate: '2026-10-24',
      status: 'received',
      paymentStatus: 'paid',
      totalAmount: 101060.00,
      wh: 'WH-QC',
      items: [{ name: 'Bamboo Tumbler Blanks 450ml', sku: 'DRK-TMB-001', qty: 500, unitCost: 176.00, total: 88000.00 }]
    },
    {
      id: 2,
      poNumber: 'PO-2026-002',
      supplier: 'Luzon Garments & Apparel Mfg',
      orderDate: '2026-11-05',
      expectedDate: '2026-11-18',
      receivedDate: '2026-11-17',
      status: 'received',
      paymentStatus: 'paid',
      totalAmount: 82440.00,
      wh: 'WH-QC',
      items: [{ name: 'Executive Corporate Windbreaker Blanks', sku: 'APP-JKT-001', qty: 150, unitCost: 480.00, total: 72000.00 }]
    },
    {
      id: 3,
      poNumber: 'PO-2026-003',
      supplier: 'Pacific Tech & Gadgets Import Corp',
      orderDate: '2026-11-18',
      expectedDate: '2026-11-28',
      receivedDate: null,
      status: 'sent',
      paymentStatus: 'unpaid',
      totalAmount: 51900.00,
      wh: 'WH-QC',
      items: [{ name: '1080p HD Wide-Angle Dash Cam Units', sku: 'ACC-CAR-003', qty: 150, unitCost: 300.00, total: 45000.00 }]
    },
    {
      id: 4,
      poNumber: 'PO-2026-004',
      supplier: 'Premium Leather & Packaging Solutions',
      orderDate: '2026-11-20',
      expectedDate: '2026-12-02',
      receivedDate: null,
      status: 'draft',
      paymentStatus: 'unpaid',
      totalAmount: 37040.00,
      wh: 'WH-QC',
      items: [{ name: 'RFID Bifold PU Leather Wallet Blanks', sku: 'LEA-WLT-001', qty: 250, unitCost: 128.00, total: 32000.00 }]
    }
  ];

  const DEFAULT_MOVEMENTS = [
    { id: 1, ref: 'MOV-261024-001', type: 'stock_in', source: 'PO-2026-001', item: 'Bamboo Tumblers', qty: 500, wh: 'WH-QC', user: 'Ramon Valdez', date: '2026-10-24 14:32' },
    { id: 2, ref: 'MOV-261117-002', type: 'stock_in', source: 'PO-2026-002', item: 'Corporate Jackets', qty: 150, wh: 'WH-QC', user: 'Ramon Valdez', date: '2026-11-17 10:15' },
    { id: 3, ref: 'MOV-261119-003', type: 'stock_out', source: 'ORD-2026-041', item: 'Classic Zip Totes', qty: -100, wh: 'WH-QC', user: 'Analyn Santos', date: '2026-11-19 16:45' }
  ];

  const DEFAULT_AUDIT_LOGS = [
    { id: 1, module: 'Inventory', action: 'Stock In', details: 'Received 150 units of Corporate Jackets from PO-2026-002.', user: 'Warehouse Lead', time: '2026-11-17 10:15' },
    { id: 2, module: 'Purchasing', action: 'Create PO', details: 'Created Purchase Order PO-2026-003 for Pacific Tech.', user: 'Procurement Officer', time: '2026-11-18 09:20' },
    { id: 3, module: 'Quotes', action: 'Approval', details: 'Quotation inquiry CK3A-849201 marked as Approved & In Production.', user: 'Sales Manager', time: '2026-11-20 11:45' }
  ];

  const DEFAULT_USERS = [
    {
      id: 1,
      name: 'Lead Administrator',
      firstName: 'Lead',
      lastName: 'Administrator',
      email: 'admin@creativekit3a.com',
      phone: '+63 917 111 2233',
      role: 'admin',
      status: 'active',
      createdAt: '2026-01-10',
      lastLogin: '2026-11-23 01:02'
    },
    {
      id: 2,
      name: 'System Admin',
      firstName: 'System',
      lastName: 'Admin',
      email: 'admin@ck3a.com',
      phone: '+63 918 222 3344',
      role: 'admin',
      status: 'active',
      createdAt: '2026-02-15',
      lastLogin: '2026-11-22 18:30'
    },
    {
      id: 3,
      name: 'Ramon Valdez',
      firstName: 'Ramon',
      lastName: 'Valdez',
      email: 'procurement@creativekit3a.com',
      phone: '+63 919 333 4455',
      role: 'staff',
      status: 'active',
      createdAt: '2026-03-01',
      lastLogin: '2026-11-21 14:22'
    },
    {
      id: 4,
      name: 'Analyn Santos',
      firstName: 'Analyn',
      lastName: 'Santos',
      email: 'sales.staff@creativekit3a.com',
      phone: '+63 920 444 5566',
      role: 'staff',
      status: 'active',
      createdAt: '2026-04-12',
      lastLogin: '2026-11-22 09:15'
    },
    {
      id: 5,
      name: 'Juan Dela Cruz',
      firstName: 'Juan',
      lastName: 'Dela Cruz',
      email: 'juan.delacruz@acmecorp.ph',
      phone: '+63 922 555 6677',
      role: 'customer',
      status: 'active',
      createdAt: '2026-05-18',
      lastLogin: '2026-11-20 16:40'
    },
    {
      id: 6,
      name: 'Maria Clara',
      firstName: 'Maria',
      lastName: 'Clara',
      email: 'maria.clara@sanmiguel.com.ph',
      phone: '+63 923 666 7788',
      role: 'customer',
      status: 'active',
      createdAt: '2026-06-25',
      lastLogin: '2026-11-19 11:05'
    },
    {
      id: 7,
      name: 'Roberto Garcia',
      firstName: 'Roberto',
      lastName: 'Garcia',
      email: 'rgarcia@inactivecorp.com',
      phone: '+63 925 888 9900',
      role: 'customer',
      status: 'inactive',
      createdAt: '2026-07-02',
      lastLogin: '2026-09-14 08:12'
    }
  ];

  /* ── 2. STATE STORE HELPERS ── */
  function getStore(key, defaults) {
    try {
      const data = localStorage.getItem(key);
      return data ? JSON.parse(data) : defaults;
    } catch {
      return defaults;
    }
  }

  function setStore(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
  }

  function showToast(msg, type = 'info') {
    if (typeof window.showToast === 'function') {
      window.showToast(msg, type);
      return;
    }
    let toast = document.getElementById('admin-mini-toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'admin-mini-toast';
      toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#0f172a;color:#fff;padding:12px 20px;border-radius:8px;box-shadow:0 10px 25px rgba(0,0,0,0.2);z-index:99999;font-size:0.875rem;font-weight:600;display:none;transition:opacity 0.2s ease;';
      document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.style.display = 'block';
    toast.style.opacity = '1';
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
      toast.style.opacity = '0';
      setTimeout(() => { toast.style.display = 'none'; }, 200);
    }, 3000);
  }

  /* ── 3. STATE INITIALIZATION ── */
  let inventory = getStore(KEYS.INVENTORY, DEFAULT_INVENTORY);
  let purchasing = getStore(KEYS.PURCHASING, DEFAULT_PURCHASE_ORDERS);
  let suppliers = getStore(KEYS.SUPPLIERS, DEFAULT_SUPPLIERS);
  let movements = getStore(KEYS.MOVEMENTS, DEFAULT_MOVEMENTS);
  let auditLogs = getStore(KEYS.AUDIT_LOGS, DEFAULT_AUDIT_LOGS);
  let users = getStore(KEYS.USERS, DEFAULT_USERS);

  // Sync session user from ck_user if present
  try {
    const sessionUser = JSON.parse(localStorage.getItem('ck_user') || 'null');
    if (sessionUser && sessionUser.email) {
      const emailLower = sessionUser.email.toLowerCase();
      const existing = users.find(u => u.email.toLowerCase() === emailLower);
      if (!existing) {
        users.unshift({
          id: Date.now(),
          name: sessionUser.name || sessionUser.email.split('@')[0],
          firstName: sessionUser.name ? sessionUser.name.split(' ')[0] : 'User',
          lastName: sessionUser.name ? sessionUser.name.split(' ').slice(1).join(' ') : '',
          email: sessionUser.email,
          phone: sessionUser.phone || '—',
          role: sessionUser.role || 'customer',
          status: 'active',
          createdAt: new Date().toISOString().split('T')[0],
          lastLogin: new Date().toLocaleString()
        });
        setStore(KEYS.USERS, users);
      }
    }
  } catch (_) {}

  function logAudit(module, action, details) {
    const entry = {
      id: Date.now(),
      module,
      action,
      details,
      user: 'Operations Admin',
      time: new Date().toLocaleString()
    };
    auditLogs.unshift(entry);
    setStore(KEYS.AUDIT_LOGS, auditLogs);
    renderAuditLogs();
  }

  /* ── 4. INFORMATION SYSTEM (MIS & Analytics) ── */
  function renderMISDashboard() {
    // KPI calculations
    let totalStockUnits = 0;
    let totalInventoryValuation = 0;
    let lowStockCount = 0;

    inventory.forEach(item => {
      totalStockUnits += item.qty;
      totalInventoryValuation += (item.qty * item.price);
      if (item.qty <= item.reorder) {
        lowStockCount++;
      }
    });

    let totalProcurementSpend = 0;
    let activePOCount = 0;

    purchasing.forEach(po => {
      if (po.status === 'received') {
        totalProcurementSpend += po.totalAmount;
      } else {
        activePOCount++;
      }
    });

    // Update KPI UI
    const elValuation = document.getElementById('kpi-inventory-val');
    const elUnits = document.getElementById('kpi-inventory-units');
    const elLowStock = document.getElementById('kpi-low-stock');
    const elProcurement = document.getElementById('kpi-procurement-spend');
    const elActivePO = document.getElementById('kpi-active-pos');

    if (elValuation) elValuation.textContent = '₱' + totalInventoryValuation.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    if (elUnits) elUnits.textContent = totalStockUnits.toLocaleString();
    if (elLowStock) elLowStock.textContent = lowStockCount;
    if (elProcurement) elProcurement.textContent = '₱' + totalProcurementSpend.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    if (elActivePO) elActivePO.textContent = activePOCount;

    // Update Tab Badges & Sidebar Badges
    const invBadge = document.getElementById('tab-badge-inventory');
    const poBadge = document.getElementById('tab-badge-purchasing');
    const userBadge = document.getElementById('tab-badge-users');
    const sideInvBadge = document.getElementById('side-badge-inventory');
    const sidePoBadge = document.getElementById('side-badge-purchasing');
    const sideUserBadge = document.getElementById('side-badge-users');

    const invText = lowStockCount > 0 ? `${lowStockCount} alerts` : inventory.length;
    if (invBadge) invBadge.textContent = invText;
    if (sideInvBadge) sideInvBadge.textContent = lowStockCount > 0 ? lowStockCount : inventory.length;

    if (poBadge) poBadge.textContent = purchasing.length;
    if (sidePoBadge) sidePoBadge.textContent = purchasing.length;

    if (userBadge) userBadge.textContent = users.length;
    if (sideUserBadge) sideUserBadge.textContent = users.length;

    renderAuditLogs();
  }

  function renderAuditLogs() {
    const container = document.getElementById('audit-logs-table-body');
    if (!container) return;

    if (!auditLogs.length) {
      container.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:24px;color:#94a3b8;">No system audit entries found.</td></tr>`;
      return;
    }

    container.innerHTML = auditLogs.slice(0, 10).map(log => `
      <tr>
        <td><strong>${log.time}</strong></td>
        <td><span class="badge-status badge-sent">${log.module}</span></td>
        <td><strong>${log.action}</strong></td>
        <td>${log.details}</td>
        <td><i class="fas fa-user-circle"></i> ${log.user}</td>
      </tr>
    `).join('');
  }

  /* ── 5. INVENTORY SYSTEM (Stock & Warehouses) ── */
  function renderInventoryTable(filterKeyword = '', filterCategory = 'all', filterStatus = 'all') {
    const tbody = document.getElementById('inventory-table-body');
    if (!tbody) return;

    let filtered = inventory.filter(item => {
      const matchKeyword = !filterKeyword ||
        item.name.toLowerCase().includes(filterKeyword.toLowerCase()) ||
        item.sku.toLowerCase().includes(filterKeyword.toLowerCase());
      const matchCategory = (filterCategory === 'all') || item.category === filterCategory;

      let status = 'in-stock';
      if (item.qty <= 0) status = 'out-of-stock';
      else if (item.qty <= item.reorder) status = 'low-stock';

      const matchStatus = (filterStatus === 'all') || (status === filterStatus);

      return matchKeyword && matchCategory && matchStatus;
    });

    if (!filtered.length) {
      tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:32px;color:#94a3b8;"><i class="fas fa-box-open" style="font-size:2rem;margin-bottom:8px;display:block;"></i>No inventory records found.</td></tr>`;
      return;
    }

    tbody.innerHTML = filtered.map(item => {
      let statusBadge = `<span class="badge-status badge-in-stock"><i class="fas fa-check-circle"></i> In Stock</span>`;
      if (item.qty <= 0) {
        statusBadge = `<span class="badge-status badge-out-of-stock"><i class="fas fa-times-circle"></i> Out of Stock</span>`;
      } else if (item.qty <= item.reorder) {
        statusBadge = `<span class="badge-status badge-low-stock"><i class="fas fa-exclamation-triangle"></i> Low Stock</span>`;
      }

      const available = item.qty - item.reserved;
      const totalVal = item.qty * item.price;

      return `
        <tr>
          <td><strong>${item.sku}</strong></td>
          <td>
            <div style="font-weight:700;">${item.name}</div>
            <div style="font-size:0.75rem;color:#64748b;">${item.category}</div>
          </td>
          <td><span class="badge-status badge-draft"><i class="fas fa-warehouse"></i> ${item.wh}</span></td>
          <td><strong style="font-size:1rem;">${item.qty}</strong></td>
          <td style="color:#64748b;">${item.reserved}</td>
          <td><strong style="color:${available <= item.reorder ? '#d97706' : '#059669'};">${available}</strong></td>
          <td><span style="color:#d97706;font-weight:600;"><i class="fas fa-bell"></i> ${item.reorder}</span></td>
          <td>${statusBadge}</td>
          <td><strong>₱${totalVal.toLocaleString('en-US', { minimumFractionDigits: 2 })}</strong></td>
          <td style="text-align:right;">
            <button class="btn-admin btn-admin-secondary btn-admin-sm" onclick="AdminApp.openStockAdjustModal(${item.id})">
              <i class="fas fa-sliders-h"></i> Adjust
            </button>
          </td>
        </tr>
      `;
    }).join('');
  }

  function renderMovementsTable() {
    const tbody = document.getElementById('movements-table-body');
    if (!tbody) return;

    if (!movements.length) {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">No stock movement records logged.</td></tr>`;
      return;
    }

    tbody.innerHTML = movements.slice(0, 15).map(m => `
      <tr>
        <td><strong>${m.ref}</strong></td>
        <td><span class="badge-status ${m.type === 'stock_in' ? 'badge-in-stock' : 'badge-out-of-stock'}">${m.type.replace('_', ' ').toUpperCase()}</span></td>
        <td>${m.source}</td>
        <td><strong>${m.item}</strong></td>
        <td><strong style="color:${m.qty > 0 ? '#059669' : '#dc2626'};">${m.qty > 0 ? '+' + m.qty : m.qty}</strong></td>
        <td>${m.wh}</td>
        <td>${m.date}</td>
      </tr>
    `).join('');
  }

  /* ── 6. PURCHASING SYSTEM (Procurement & Orders) ── */
  function renderPurchasingTable(filterStatus = 'all') {
    const tbody = document.getElementById('purchasing-table-body');
    if (!tbody) return;

    let filtered = purchasing.filter(po => (filterStatus === 'all') || po.status === filterStatus);

    if (!filtered.length) {
      tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:32px;color:#94a3b8;"><i class="fas fa-file-invoice" style="font-size:2rem;margin-bottom:8px;display:block;"></i>No purchase orders match criteria.</td></tr>`;
      return;
    }

    tbody.innerHTML = filtered.map(po => {
      let statusBadge = `<span class="badge-status badge-draft">Draft</span>`;
      if (po.status === 'sent') statusBadge = `<span class="badge-status badge-sent"><i class="fas fa-paper-plane"></i> Sent</span>`;
      if (po.status === 'received') statusBadge = `<span class="badge-status badge-received"><i class="fas fa-check-double"></i> Received</span>`;

      let payBadge = po.paymentStatus === 'paid'
        ? `<span class="badge-status badge-paid"><i class="fas fa-check"></i> Paid</span>`
        : `<span class="badge-status badge-unpaid"><i class="fas fa-clock"></i> Unpaid</span>`;

      const canReceive = po.status === 'sent' || po.status === 'draft';

      return `
        <tr>
          <td><strong>${po.poNumber}</strong></td>
          <td><strong>${po.supplier}</strong></td>
          <td>${po.orderDate}</td>
          <td>${po.expectedDate || '—'}</td>
          <td>${statusBadge}</td>
          <td>${payBadge}</td>
          <td><strong>₱${po.totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2 })}</strong></td>
          <td style="text-align:right;">
            ${canReceive ? `
              <button class="btn-admin btn-admin-success btn-admin-sm" onclick="AdminApp.receivePO(${po.id})">
                <i class="fas fa-dolly"></i> Receive Goods
              </button>
            ` : `
              <span style="font-size:0.8rem;color:#059669;font-weight:700;"><i class="fas fa-check"></i> Fulfilled</span>
            `}
          </td>
        </tr>
      `;
    }).join('');
  }

  function renderSuppliersTable() {
    const tbody = document.getElementById('suppliers-table-body');
    if (!tbody) return;

    tbody.innerHTML = suppliers.map(s => `
      <tr>
        <td><strong>${s.code}</strong></td>
        <td><strong>${s.name}</strong></td>
        <td>${s.contact}</td>
        <td><a href="mailto:${s.email}">${s.email}</a></td>
        <td>${s.phone}</td>
        <td><span class="badge-status badge-draft">${s.terms}</span></td>
        <td><span style="color:#f59e0b;font-weight:700;">★ ${s.rating.toFixed(1)}</span></td>
      </tr>
    `).join('');
  }

  /* ── 6.5. USER CONTROL PANEL (Identity & Access Control) ── */
  function renderUsersTable(filterKeyword = '', filterRole = 'all', filterStatus = 'all') {
    const tbody = document.getElementById('users-table-body');
    if (!tbody) return;

    // Update KPI counters
    const totalUsers = users.length;
    const adminCount = users.filter(u => u.role === 'admin').length;
    const staffCount = users.filter(u => u.role === 'staff').length;
    const customerCount = users.filter(u => u.role === 'customer').length;

    const elTotal = document.getElementById('kpi-total-users');
    const elAdmin = document.getElementById('kpi-admin-users');
    const elStaff = document.getElementById('kpi-staff-users');
    const elCustomer = document.getElementById('kpi-customer-users');
    const tabUserBadge = document.getElementById('tab-badge-users');

    if (elTotal) elTotal.textContent = totalUsers;
    if (elAdmin) elAdmin.textContent = adminCount;
    if (elStaff) elStaff.textContent = staffCount;
    if (elCustomer) elCustomer.textContent = customerCount;
    const sideUserBadge = document.getElementById('side-badge-users');
    if (tabUserBadge) tabUserBadge.textContent = totalUsers;
    if (sideUserBadge) sideUserBadge.textContent = totalUsers;

    const q = filterKeyword.toLowerCase().trim();
    const filtered = users.filter(u => {
      const matchKeyword = !q ||
        (u.name && u.name.toLowerCase().includes(q)) ||
        (u.email && u.email.toLowerCase().includes(q)) ||
        (u.phone && u.phone.toLowerCase().includes(q));
      const matchRole = filterRole === 'all' || u.role === filterRole;
      const matchStatus = filterStatus === 'all' || u.status === filterStatus;
      return matchKeyword && matchRole && matchStatus;
    });

    if (filtered.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--admin-text-muted);font-weight:600;"><i class="fas fa-user-slash" style="margin-right:6px;color:#94a3b8;"></i> No user accounts match the selected filters.</td></tr>`;
      return;
    }

    tbody.innerHTML = filtered.map(u => {
      const initials = ((u.firstName ? u.firstName[0] : (u.name ? u.name[0] : 'U')) + (u.lastName ? u.lastName[0] : '')).toUpperCase() || 'U';
      const roleBadgeClass = u.role === 'admin' ? 'badge-role-admin' : (u.role === 'staff' ? 'badge-role-staff' : 'badge-role-customer');
      const roleIcon = u.role === 'admin' ? 'fa-user-shield' : (u.role === 'staff' ? 'fa-user-tie' : 'fa-user');
      const roleLabel = u.role === 'admin' ? 'Administrator' : (u.role === 'staff' ? 'Staff Officer' : 'Customer');

      const statusBadge = u.status === 'active'
        ? `<span class="badge-status badge-active"><i class="fas fa-check-circle"></i> Active</span>`
        : `<span class="badge-status badge-inactive"><i class="fas fa-ban"></i> Inactive</span>`;

      const isProtected = (u.email && u.email.toLowerCase() === 'admin@creativekit3a.com') || u.id === 1;

      return `
        <tr>
          <td>
            <div class="user-cell">
              <div class="user-avatar-circle ${u.role}">${initials}</div>
              <div>
                <div class="user-name-title">${u.name} ${isProtected ? '<span style="font-size:0.7rem;color:#ff6b00;" title="Primary Root Administrator"><i class="fas fa-crown"></i></span>' : ''}</div>
                <div class="user-email-subtitle"><a href="mailto:${u.email}" style="color:inherit;text-decoration:none;">${u.email}</a></div>
              </div>
            </div>
          </td>
          <td><span style="font-size:0.83rem;color:var(--admin-text-dark);">${u.phone || '—'}</span></td>
          <td><span class="badge-status ${roleBadgeClass}"><i class="fas ${roleIcon}"></i> ${roleLabel}</span></td>
          <td>${statusBadge}</td>
          <td><span style="font-size:0.82rem;color:var(--admin-text-muted);">${u.createdAt || '2026-01-01'}</span></td>
          <td><span style="font-size:0.82rem;color:var(--admin-text-muted);">${u.lastLogin || 'Never'}</span></td>
          <td style="text-align:right;">
            <div style="display:inline-flex;gap:6px;align-items:center;">
              <button class="btn-admin btn-admin-secondary btn-admin-sm" onclick="AdminApp.openEditUserModal(${u.id})" title="Edit user details and role permissions">
                <i class="fas fa-edit"></i> Edit
              </button>
              ${isProtected ? '' : `
                <button class="btn-admin btn-admin-sm" style="${u.status === 'active' ? 'background:#fee2e2;color:#991b1b;border:1px solid #fecaca;' : 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;'}" onclick="AdminApp.toggleUserStatus(${u.id})" title="${u.status === 'active' ? 'Deactivate Account' : 'Activate Account'}">
                  <i class="fas ${u.status === 'active' ? 'fa-user-slash' : 'fa-user-check'}"></i> ${u.status === 'active' ? 'Deactivate' : 'Activate'}
                </button>
                <button class="btn-admin btn-admin-sm" style="background:#fee2e2;color:#dc2626;border:1px solid #fecaca;" onclick="AdminApp.deleteUser(${u.id})" title="Delete Account">
                  <i class="fas fa-trash-alt"></i>
                </button>
              `}
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  /* ── 7. INTERACTIVE ACTIONS ── */
  window.AdminApp = {
    switchTab(tabId) {
      document.querySelectorAll('.admin-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === tabId));
      document.querySelectorAll('.hover-sidebar-link[data-tab]').forEach(l => l.classList.toggle('active', l.dataset.tab === tabId));
      document.querySelectorAll('.tab-content-panel').forEach(p => p.style.display = 'none');

      const targetPanel = document.getElementById(`panel-${tabId}`);
      if (targetPanel) targetPanel.style.display = 'block';

      if (tabId === 'information') renderMISDashboard();
      if (tabId === 'inventory') { renderInventoryTable(); renderMovementsTable(); }
      if (tabId === 'purchasing') { renderPurchasingTable(); renderSuppliersTable(); }
      if (tabId === 'users') { AdminApp.filterUsers(); }
    },

    openStockAdjustModal(itemId) {
      const item = inventory.find(i => i.id === itemId);
      if (!item) return;

      document.getElementById('adj-item-id').value = item.id;
      document.getElementById('adj-item-name').textContent = `${item.name} (${item.sku})`;
      document.getElementById('adj-current-qty').textContent = item.qty;
      document.getElementById('adj-qty').value = '';
      document.getElementById('adj-remarks').value = '';

      document.getElementById('modal-stock-adjust').classList.add('open');
    },

    closeStockAdjustModal() {
      document.getElementById('modal-stock-adjust').classList.remove('open');
    },

    submitStockAdjust() {
      const id = parseInt(document.getElementById('adj-item-id').value, 10);
      const actionType = document.getElementById('adj-type').value;
      const qtyChange = parseInt(document.getElementById('adj-qty').value, 10);
      const remarks = document.getElementById('adj-remarks').value.trim();

      if (isNaN(qtyChange) || qtyChange <= 0) {
        alert('Please enter a valid positive quantity.');
        return;
      }

      const item = inventory.find(i => i.id === id);
      if (!item) return;

      let movementType = 'adjustment';
      let recordedQty = qtyChange;

      if (actionType === 'in') {
        item.qty += qtyChange;
        movementType = 'stock_in';
        recordedQty = qtyChange;
      } else if (actionType === 'out') {
        if (qtyChange > item.qty) {
          alert('Cannot remove more items than available on hand.');
          return;
        }
        item.qty -= qtyChange;
        movementType = 'stock_out';
        recordedQty = -qtyChange;
      } else {
        item.qty = qtyChange;
        movementType = 'adjustment';
        recordedQty = qtyChange;
      }

      setStore(KEYS.INVENTORY, inventory);

      // Record movement
      const movRef = `MOV-${Date.now().toString().slice(-6)}`;
      movements.unshift({
        id: Date.now(),
        ref: movRef,
        type: movementType,
        source: 'Manual Adjustment',
        item: item.name,
        qty: recordedQty,
        wh: item.wh,
        user: 'Operations Admin',
        date: new Date().toLocaleString()
      });
      setStore(KEYS.MOVEMENTS, movements);

      logAudit('Inventory', 'Stock Adjusted', `${item.name} (${item.sku}) adjusted by ${recordedQty} units. Note: ${remarks || 'None'}`);

      this.closeStockAdjustModal();
      renderInventoryTable();
      renderMovementsTable();
      renderMISDashboard();
    },

    openCreatePOModal() {
      const select = document.getElementById('po-supplier-select');
      if (select) {
        select.innerHTML = suppliers.map(s => `<option value="${s.name}">${s.name} (${s.code})</option>`).join('');
      }
      document.getElementById('modal-create-po').classList.add('open');
    },

    closeCreatePOModal() {
      document.getElementById('modal-create-po').classList.remove('open');
    },

    submitCreatePO() {
      const supplierName = document.getElementById('po-supplier-select').value;
      const itemName = document.getElementById('po-item-name').value.trim();
      const qty = parseInt(document.getElementById('po-item-qty').value, 10);
      const unitCost = parseFloat(document.getElementById('po-item-cost').value);
      const wh = document.getElementById('po-warehouse-select').value;
      const expDate = document.getElementById('po-exp-date').value;

      if (!itemName || isNaN(qty) || qty <= 0 || isNaN(unitCost) || unitCost <= 0) {
        alert('Please fill out all product details with valid quantities and costs.');
        return;
      }

      const total = qty * unitCost;
      const poNum = `PO-2026-${Math.floor(100 + Math.random() * 900)}`;

      const newPO = {
        id: Date.now(),
        poNumber: poNum,
        supplier: supplierName,
        orderDate: new Date().toISOString().split('T')[0],
        expectedDate: expDate || null,
        receivedDate: null,
        status: 'sent',
        paymentStatus: 'unpaid',
        totalAmount: total,
        wh: wh,
        items: [{ name: itemName, qty: qty, unitCost: unitCost, total: total }]
      };

      purchasing.unshift(newPO);
      setStore(KEYS.PURCHASING, purchasing);

      logAudit('Purchasing', 'PO Created', `Generated ${poNum} for ${supplierName} (Total: ₱${total.toLocaleString()}).`);

      this.closeCreatePOModal();
      renderPurchasingTable();
      renderMISDashboard();
    },

    receivePO(poId) {
      const po = purchasing.find(p => p.id === poId);
      if (!po) return;

      if (!confirm(`Confirm goods receipt for ${po.poNumber}? This will automatically increment stock in warehouse ${po.wh}.`)) {
        return;
      }

      po.status = 'received';
      po.receivedDate = new Date().toISOString().split('T')[0];
      setStore(KEYS.PURCHASING, purchasing);

      // Increment inventory stock
      po.items.forEach(poItem => {
        let invItem = inventory.find(i => i.name.toLowerCase() === poItem.name.toLowerCase() || (poItem.sku && i.sku === poItem.sku));
        if (invItem) {
          invItem.qty += poItem.qty;
        } else {
          inventory.push({
            id: Date.now(),
            sku: poItem.sku || `SKU-${Date.now().toString().slice(-4)}`,
            name: poItem.name,
            category: 'Procured Stock',
            wh: po.wh,
            qty: poItem.qty,
            reserved: 0,
            reorder: 20,
            cost: poItem.unitCost,
            price: Math.round(poItem.unitCost * 1.5)
          });
        }

        // Add movement ledger entry
        movements.unshift({
          id: Date.now() + Math.random(),
          ref: `MOV-${Date.now().toString().slice(-6)}`,
          type: 'stock_in',
          source: po.poNumber,
          item: poItem.name,
          qty: poItem.qty,
          wh: po.wh,
          user: 'Operations Admin',
          date: new Date().toLocaleString()
        });
      });

      setStore(KEYS.INVENTORY, inventory);
      setStore(KEYS.MOVEMENTS, movements);

      logAudit('Purchasing', 'Goods Received', `Received delivery for ${po.poNumber} at ${po.wh}. Stock updated.`);

      renderPurchasingTable();
      renderInventoryTable();
      renderMovementsTable();
      renderMISDashboard();
    },

    exportReportCSV() {
      let csvContent = 'data:text/csv;charset=utf-8,';
      csvContent += 'SKU,Product Name,Category,Warehouse,Stock On Hand,Reserved,Available,Reorder Level,Unit Price,Valuation\n';

      inventory.forEach(i => {
        const row = [
          i.sku,
          `"${i.name}"`,
          `"${i.category}"`,
          i.wh,
          i.qty,
          i.reserved,
          i.qty - i.reserved,
          i.reorder,
          i.price,
          i.qty * i.price
        ].join(',');
        csvContent += row + '\n';
      });

      const encodedUri = encodeURI(csvContent);
      const link = document.createElement('a');
      link.setAttribute('href', encodedUri);
      link.setAttribute('download', `CreativeKit3A_Inventory_Report_${new Date().toISOString().split('T')[0]}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      logAudit('System', 'Report Export', 'Exported executive inventory CSV report.');
    },

    /* ── User Control Panel Methods ── */
    filterUsers() {
      const q = document.getElementById('user-search-input')?.value || '';
      const role = document.getElementById('user-role-filter')?.value || 'all';
      const status = document.getElementById('user-status-filter')?.value || 'all';
      renderUsersTable(q, role, status);
    },

    resetUserFilters() {
      const searchEl = document.getElementById('user-search-input');
      const roleEl = document.getElementById('user-role-filter');
      const statusEl = document.getElementById('user-status-filter');
      if (searchEl) searchEl.value = '';
      if (roleEl) roleEl.value = 'all';
      if (statusEl) statusEl.value = 'all';
      renderUsersTable('', 'all', 'all');
    },

    openAddUserModal() {
      ['new-user-fname', 'new-user-lname', 'new-user-email', 'new-user-phone', 'new-user-pw'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
      });
      const roleEl = document.getElementById('new-user-role');
      if (roleEl) roleEl.value = 'customer';
      const statusEl = document.getElementById('new-user-status');
      if (statusEl) statusEl.value = 'active';
      const modal = document.getElementById('modal-add-user');
      if (modal) modal.classList.add('open');
    },

    closeAddUserModal() {
      const modal = document.getElementById('modal-add-user');
      if (modal) modal.classList.remove('open');
    },

    submitAddUser() {
      const fname = document.getElementById('new-user-fname')?.value.trim();
      const lname = document.getElementById('new-user-lname')?.value.trim();
      const email = document.getElementById('new-user-email')?.value.trim();
      const phone = document.getElementById('new-user-phone')?.value.trim();
      const role = document.getElementById('new-user-role')?.value || 'customer';
      const status = document.getElementById('new-user-status')?.value || 'active';
      const pw = document.getElementById('new-user-pw')?.value;

      if (!fname || !lname || !email) {
        alert('Please fill out first name, last name, and email address.');
        return;
      }

      if (!/^\S+@\S+\.\S+$/.test(email)) {
        alert('Please enter a valid email address.');
        return;
      }

      if (!pw || pw.length < 6) {
        alert('Initial password must be at least 6 characters.');
        return;
      }

      const emailLower = email.toLowerCase();
      if (users.some(u => u.email.toLowerCase() === emailLower)) {
        alert('A user with this email address already exists.');
        return;
      }

      const newUser = {
        id: Date.now(),
        name: `${fname} ${lname}`.trim(),
        firstName: fname,
        lastName: lname,
        email: emailLower,
        phone: phone || '—',
        role: role,
        status: status,
        createdAt: new Date().toISOString().split('T')[0],
        lastLogin: 'Never'
      };

      users.unshift(newUser);
      setStore(KEYS.USERS, users);

      logAudit('Identity & Access', 'User Provisioned', `Created new account for ${newUser.name} (${newUser.email}) with role '${role}'.`);

      this.closeAddUserModal();
      this.filterUsers();
      showToast(`User account for ${newUser.name} created successfully.`);
    },

    openEditUserModal(userId) {
      const user = users.find(u => u.id === userId);
      if (!user) return;

      const idEl = document.getElementById('edit-user-id');
      const fnameEl = document.getElementById('edit-user-fname');
      const lnameEl = document.getElementById('edit-user-lname');
      const emailEl = document.getElementById('edit-user-email');
      const phoneEl = document.getElementById('edit-user-phone');
      const roleEl = document.getElementById('edit-user-role');
      const statusEl = document.getElementById('edit-user-status');
      const pwEl = document.getElementById('edit-user-pw');

      if (idEl) idEl.value = user.id;
      if (fnameEl) fnameEl.value = user.firstName || (user.name ? user.name.split(' ')[0] : '');
      if (lnameEl) lnameEl.value = user.lastName || (user.name ? user.name.split(' ').slice(1).join(' ') : '');
      if (emailEl) emailEl.value = user.email;
      if (phoneEl) phoneEl.value = user.phone === '—' ? '' : (user.phone || '');
      if (roleEl) roleEl.value = user.role;
      if (statusEl) statusEl.value = user.status;
      if (pwEl) pwEl.value = '';

      const isProtected = user.email.toLowerCase() === 'admin@creativekit3a.com' || user.id === 1;
      if (roleEl) roleEl.disabled = isProtected;
      if (statusEl) statusEl.disabled = isProtected;

      const modal = document.getElementById('modal-edit-user');
      if (modal) modal.classList.add('open');
    },

    closeEditUserModal() {
      const modal = document.getElementById('modal-edit-user');
      if (modal) modal.classList.remove('open');
    },

    submitEditUser() {
      const id = parseInt(document.getElementById('edit-user-id')?.value, 10);
      const user = users.find(u => u.id === id);
      if (!user) return;

      const fname = document.getElementById('edit-user-fname')?.value.trim();
      const lname = document.getElementById('edit-user-lname')?.value.trim();
      const email = document.getElementById('edit-user-email')?.value.trim();
      const phone = document.getElementById('edit-user-phone')?.value.trim();
      const role = document.getElementById('edit-user-role')?.value || user.role;
      const status = document.getElementById('edit-user-status')?.value || user.status;
      const pw = document.getElementById('edit-user-pw')?.value;

      if (!fname || !lname || !email) {
        alert('First name, last name, and email are required.');
        return;
      }

      if (!/^\S+@\S+\.\S+$/.test(email)) {
        alert('Please enter a valid email address.');
        return;
      }

      const emailLower = email.toLowerCase();
      const emailConflict = users.some(u => u.id !== id && u.email.toLowerCase() === emailLower);
      if (emailConflict) {
        alert('Another user is already registered with this email address.');
        return;
      }

      const isProtected = user.email.toLowerCase() === 'admin@creativekit3a.com' || user.id === 1;

      user.firstName = fname;
      user.lastName = lname;
      user.name = `${fname} ${lname}`.trim();
      user.email = emailLower;
      user.phone = phone || '—';
      if (!isProtected) {
        user.role = role;
        user.status = status;
      }

      setStore(KEYS.USERS, users);

      let auditMsg = `Updated account profile for ${user.name} (${user.email}).`;
      if (pw && pw.length >= 6) {
        auditMsg += ' Security credentials (password) reset.';
      }

      logAudit('Identity & Access', 'User Updated', auditMsg);

      this.closeEditUserModal();
      this.filterUsers();
      showToast(`User profile for ${user.name} updated successfully.`);
    },

    toggleUserStatus(userId) {
      const user = users.find(u => u.id === userId);
      if (!user) return;

      if (user.email.toLowerCase() === 'admin@creativekit3a.com' || user.id === 1) {
        alert('Root administrator account cannot be deactivated.');
        return;
      }

      const nextStatus = user.status === 'active' ? 'inactive' : 'active';
      user.status = nextStatus;
      setStore(KEYS.USERS, users);

      logAudit('Identity & Access', 'Status Toggled', `User ${user.name} (${user.email}) marked as ${nextStatus}.`);

      this.filterUsers();
      showToast(`Account for ${user.name} is now ${nextStatus.toUpperCase()}.`);
    },

    deleteUser(userId) {
      const user = users.find(u => u.id === userId);
      if (!user) return;

      if (user.email.toLowerCase() === 'admin@creativekit3a.com' || user.id === 1) {
        alert('Root administrator account cannot be deleted.');
        return;
      }

      if (!confirm(`Are you sure you want to permanently delete user account: ${user.name} (${user.email})?\n\nThis action cannot be undone.`)) {
        return;
      }

      users = users.filter(u => u.id !== userId);
      setStore(KEYS.USERS, users);

      logAudit('Identity & Access', 'User Deleted', `Permanently deleted user account ${user.name} (${user.email}).`);

      this.filterUsers();
      showToast(`User account ${user.name} deleted.`);
    },

    exportUsersCSV() {
      let csvContent = 'data:text/csv;charset=utf-8,';
      csvContent += 'User ID,Full Name,First Name,Last Name,Email,Phone,Security Role,Account Status,Registered Date,Last Login\n';

      users.forEach(u => {
        const row = [
          u.id,
          `"${u.name || ''}"`,
          `"${u.firstName || ''}"`,
          `"${u.lastName || ''}"`,
          `"${u.email || ''}"`,
          `"${u.phone || ''}"`,
          u.role,
          u.status,
          u.createdAt || '',
          `"${u.lastLogin || ''}"`
        ].join(',');
        csvContent += row + '\n';
      });

      const encodedUri = encodeURI(csvContent);
      const link = document.createElement('a');
      link.setAttribute('href', encodedUri);
      link.setAttribute('download', `CreativeKit3A_Users_Export_${new Date().toISOString().split('T')[0]}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      logAudit('Identity & Access', 'Export Users CSV', 'Exported directory of system users to CSV.');
    },

    handleGateLogin: function (e) {
      if (e) e.preventDefault();
      const email = document.getElementById('gate-admin-email')?.value.trim();
      const pass  = document.getElementById('gate-admin-password')?.value;
      const errEl = document.getElementById('gate-error-msg');

      if (!email || !pass) {
        if (errEl) { errEl.textContent = 'Please enter both email and password.'; errEl.style.display = 'block'; }
        return;
      }

      const normEmail = email.toLowerCase();
      const isAdmin = (normEmail === 'admin@creativekit3a.com' || normEmail === 'admin@ck3a.com') && pass === 'Admin@2026!';

      if (isAdmin) {
        localStorage.setItem('ck_user', JSON.stringify({
          name: 'Admin',
          email: email,
          role: 'admin'
        }));
        if (errEl) errEl.style.display = 'none';
        checkAdminAuth();
        showToast('Admin access verified. Welcome to CreativeKit 3A Management Portal!');
      } else {
        if (errEl) {
          errEl.textContent = 'Invalid administrator credentials. Access denied.';
          errEl.style.display = 'block';
        }
      }
    },

    logout: function () {
      localStorage.removeItem('ck_user');
      checkAdminAuth();
      showToast('Signed out of Management Portal.');
      setTimeout(() => {
        const isPhp = window.location.pathname.endsWith('.php');
        window.location.href = isPhp ? '../index.php' : '../index.html';
      }, 500);
    }
  };

  function checkAdminAuth() {
    const userStr = localStorage.getItem('ck_user');
    let user = null;
    try {
      user = userStr ? JSON.parse(userStr) : null;
    } catch (_) {}

    const isAdmin = user && (user.role === 'admin' || user.email === 'admin@creativekit3a.com' || user.email === 'admin@ck3a.com');
    const authGate = document.getElementById('admin-auth-gate');
    const userDisplay = document.getElementById('admin-user-display');

    if (!isAdmin) {
      if (authGate) authGate.style.display = 'flex';
      return false;
    }

    if (authGate) authGate.style.display = 'none';

    if (userDisplay) {
      userDisplay.innerHTML = `
        <div style="display:inline-flex;align-items:center;gap:8px;font-size:0.82rem;font-weight:700;color:#0f172a;background:rgba(255,107,0,0.1);border:1px solid rgba(255,107,0,0.3);padding:4px 12px;border-radius:20px;">
          <i class="fas fa-user-shield" style="color:#ff6b00;"></i>
          <span>${user.name || 'Admin'}</span>
          <button onclick="AdminApp.logout()" style="background:none;border:none;color:#ef4444;cursor:pointer;font-weight:700;font-size:0.75rem;margin-left:4px;" title="Sign out of Admin">(Sign Out)</button>
        </div>`;
    }

    const sideUserName = document.getElementById('side-user-name');
    const sideUserRole = document.getElementById('side-user-role');
    const sideUserAvatar = document.getElementById('side-user-avatar');

    if (sideUserName) sideUserName.textContent = user.name || 'Admin';
    if (sideUserRole) sideUserRole.textContent = (user.role ? user.role.toUpperCase() : 'ADMIN') + ' ACCESS';
    if (sideUserAvatar) {
      const parts = (user.name || 'Admin').trim().split(/\s+/);
      const initials = parts.map(p => p[0]).join('').substring(0, 2).toUpperCase() || 'AD';
      sideUserAvatar.textContent = initials;
    }
    return true;
  }

  /* ── 8. BOOTSTRAP ── */
  document.addEventListener('DOMContentLoaded', () => {
    // 0. Check admin authentication
    checkAdminAuth();

    // Live Clock
    const clockEl = document.getElementById('admin-clock-time');
    if (clockEl) {
      const updateClock = () => {
        clockEl.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
      };
      updateClock();
      setInterval(updateClock, 1000);
    }

    // Tab bindings
    document.querySelectorAll('.admin-tab').forEach(tab => {
      tab.addEventListener('click', () => {
        AdminApp.switchTab(tab.dataset.tab);
      });
    });

    // Sidebar link bindings
    document.querySelectorAll('.hover-sidebar-link[data-tab]').forEach(link => {
      link.addEventListener('click', () => {
        AdminApp.switchTab(link.dataset.tab);
        const sidebar = document.getElementById('hover-sidebar');
        if (sidebar && !sidebar.classList.contains('pinned')) {
          sidebar.classList.remove('open');
        }
      });
    });

    // Pin button toggle
    const pinBtn = document.getElementById('sidebar-pin-btn');
    const sidebar = document.getElementById('hover-sidebar');
    if (pinBtn && sidebar) {
      const isPinned = localStorage.getItem('ck_sidebar_pinned') === 'true';
      if (isPinned) {
        sidebar.classList.add('pinned');
        document.body.classList.add('sidebar-pinned');
      }

      pinBtn.addEventListener('click', () => {
        const pinned = sidebar.classList.toggle('pinned');
        document.body.classList.toggle('sidebar-pinned', pinned);
        localStorage.setItem('ck_sidebar_pinned', pinned ? 'true' : 'false');
        showToast(pinned ? 'Navigation sidebar pinned to left.' : 'Navigation sidebar unpinned (auto-hides on hover).');
      });
    }

    // Hover sensor click for touch/mobile
    const hoverZone = document.getElementById('sidebar-hover-zone');
    if (hoverZone && sidebar) {
      hoverZone.addEventListener('click', () => {
        sidebar.classList.toggle('open');
      });
    }

    // Inventory search & filters
    const searchInput = document.getElementById('inv-search-input');
    const catSelect = document.getElementById('inv-category-select');
    const statusSelect = document.getElementById('inv-status-select');

    const handleFilterChange = () => {
      renderInventoryTable(
        searchInput ? searchInput.value.trim() : '',
        catSelect ? catSelect.value : 'all',
        statusSelect ? statusSelect.value : 'all'
      );
    };

    if (searchInput) searchInput.addEventListener('input', handleFilterChange);
    if (catSelect) catSelect.addEventListener('change', handleFilterChange);
    if (statusSelect) statusSelect.addEventListener('change', handleFilterChange);

    // User search & filters
    const userSearchInput = document.getElementById('user-search-input');
    const userRoleFilter = document.getElementById('user-role-filter');
    const userStatusFilter = document.getElementById('user-status-filter');

    if (userSearchInput) userSearchInput.addEventListener('input', () => AdminApp.filterUsers());
    if (userRoleFilter) userRoleFilter.addEventListener('change', () => AdminApp.filterUsers());
    if (userStatusFilter) userStatusFilter.addEventListener('change', () => AdminApp.filterUsers());

    // Modal overlay backdrop click to close
    document.querySelectorAll('.admin-modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay && overlay.id !== 'admin-auth-gate') {
          overlay.classList.remove('open');
        }
      });
    });

    // Initial render
    renderMISDashboard();
    renderInventoryTable();
    renderMovementsTable();
    renderPurchasingTable();
    renderSuppliersTable();
    renderUsersTable();
  });

})();
