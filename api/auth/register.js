/**
 * Vercel Serverless Function: POST /api/auth/register
 */
module.exports = async (req, res) => {
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
    const { first_name, last_name, email, password } = body;

    if (!first_name || !last_name || !email || !password) {
      return res.status(400).json({
        success: false,
        message: 'All required registration fields must be completed.'
      });
    }

    return res.status(201).json({
      success: true,
      message: 'Account registered successfully.'
    });
  } catch (err) {
    return res.status(500).json({
      success: false,
      message: 'Registration error: ' + err.message
    });
  }
};
