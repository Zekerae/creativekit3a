/**
 * Vercel Serverless Function: POST /api/quote
 */
module.exports = async (req, res) => {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ success: false, message: 'Method Not Allowed' });
  }

  try {
    const body = typeof req.body === 'string' ? JSON.parse(req.body) : (req.body || {});
    const { name, email, phone, product_type, quantity } = body;

    if (!name || !email || !phone || !product_type || !quantity) {
      return res.status(400).json({
        success: false,
        message: 'Please fill in all required fields (name, email, phone, category, quantity).'
      });
    }

    // In production with a cloud DB / CRM, store the quote record here
    return res.status(200).json({
      success: true,
      message: 'Quote request submitted successfully.',
      quote_ref: 'CK3A-' + Math.floor(100000 + Math.random() * 900000)
    });
  } catch (err) {
    return res.status(500).json({
      success: false,
      message: 'Server error processing quote request: ' + err.message
    });
  }
};
