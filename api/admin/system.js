/**
 * Vercel Serverless Function: /api/admin/system
 * CreativeKit 3A — Management Information, Inventory & Purchasing Backend
 */

module.exports = async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  // Initial enterprise snapshot data
  const defaultSuppliers = [
    { id: 1, code: 'SUP-001', name: 'Manila Eco-Craft Supplies Corp.', contact: 'Eduardo Santos', email: 'orders@manilaecocraft.ph', phone: '+63 917 234 5678', rating: 4.8, terms: 'Net 30' },
    { id: 2, code: 'SUP-002', name: 'Pacific Tech & Print Importers', contact: 'Grace Tan', email: 'sales@pacifictechprint.com', phone: '+63 928 345 6789', rating: 4.5, terms: 'Net 15' },
    { id: 3, code: 'SUP-003', name: 'Luzon Textile & Garments Factory', contact: 'Ramon Dela Cruz', email: 'rdc@luzontextile.com.ph', phone: '+63 919 456 7890', rating: 4.9, terms: 'Net 30' },
    { id: 4, code: 'SUP-004', name: 'Visayas Leathercraft Co-op', contact: 'Maria Theresa Lim', email: 'mtheresa@visayasleather.org', phone: '+63 932 567 8901', rating: 4.7, terms: 'COD / 50% Adv' }
  ];

  const defaultWarehouses = [
    { code: 'WH-QC', name: 'Main Central Warehouse', location: 'Diliman, Quezon City', capacity: '78%' },
    { code: 'WH-MM', name: 'Metro Manila Distribution Hub', location: 'FTI Complex, Taguig City', capacity: '42%' },
    { code: 'WH-CEB', name: 'Visayas Regional Depot', location: 'Mandaue City, Cebu', capacity: '25%' }
  ];

  if (req.method === 'GET') {
    return res.status(200).json({
      success: true,
      timestamp: new Date().toISOString(),
      system: {
        name: 'CreativeKit 3A Enterprise Management Portal',
        subsystems: ['Information System (MIS)', 'Inventory System', 'Purchasing System']
      },
      warehouses: defaultWarehouses,
      suppliers: defaultSuppliers,
      kpis: {
        total_valuation: 856940.00,
        total_units_on_hand: 8810,
        low_stock_count: 2,
        procurement_spend: 345500.00,
        active_pos_count: 2
      }
    });
  }

  if (req.method === 'POST') {
    try {
      const body = typeof req.body === 'string' ? JSON.parse(req.body) : (req.body || {});
      const action = body.action || '';

      if (action === 'adjust_stock') {
        const { itemId, adjustmentType, quantity, remarks } = body;
        return res.status(200).json({
          success: true,
          message: 'Stock adjustment logged successfully.',
          movement_ref: 'MOV-' + Math.floor(100000 + Math.random() * 900000),
          data: { itemId, adjustmentType, quantity, remarks, timestamp: new Date().toISOString() }
        });
      }

      if (action === 'create_po') {
        const { supplierId, warehouseCode, itemName, quantity, unitCost, expDate } = body;
        const total = (parseFloat(quantity) || 0) * (parseFloat(unitCost) || 0);
        return res.status(201).json({
          success: true,
          message: 'Purchase order issued successfully.',
          po_number: 'PO-' + new Date().getFullYear() + '-' + Math.floor(1000 + Math.random() * 9000),
          data: { supplierId, warehouseCode, itemName, quantity, unitCost, total, expDate, status: 'sent' }
        });
      }

      if (action === 'receive_po') {
        const { poNumber } = body;
        return res.status(200).json({
          success: true,
          message: `Purchase Order ${poNumber} received and inventory incremented successfully.`,
          goods_receipt_number: 'GRN-' + Math.floor(100000 + Math.random() * 900000),
          status: 'received'
        });
      }

      return res.status(400).json({
        success: false,
        message: 'Unknown action specified.'
      });

    } catch (err) {
      return res.status(500).json({
        success: false,
        message: 'Server error processing admin request: ' + err.message
      });
    }
  }

  return res.status(405).json({ success: false, message: 'Method Not Allowed' });
};
