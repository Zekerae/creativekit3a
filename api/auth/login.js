/**
 * Vercel Serverless Function: POST /api/auth/login
 * CreativeKit 3A — Authentication & Role-Based Access Control
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
    const { email, password } = body;

    if (!email || !password) {
      return res.status(400).json({ success: false, message: 'Email and password are required.' });
    }

    const normalizedEmail = email.toLowerCase().trim();
    const isAdmin = (normalizedEmail === 'admin@creativekit3a.com' || normalizedEmail === 'admin@ck3a.com');

    // Administrator Authentication
    if (isAdmin) {
      if (password === 'Admin@2026!') {
        return res.status(200).json({
          success: true,
          message: 'Admin authentication successful.',
          user: {
            id: 1,
            email: normalizedEmail,
            first_name: 'Admin',
            last_name: 'Director',
            role: 'admin'
          }
        });
      } else {
        return res.status(401).json({
          success: false,
          message: 'Invalid password for administrator account.'
        });
      }
    }

    // Standard Customer Authentication Demo
    return res.status(200).json({
      success: true,
      message: 'Login successful.',
      user: {
        id: 2,
        email: email,
        first_name: email.split('@')[0] || 'Customer',
        role: 'customer'
      }
    });

  } catch (err) {
    return res.status(500).json({
      success: false,
      message: 'Authentication error: ' + err.message
    });
  }
};
