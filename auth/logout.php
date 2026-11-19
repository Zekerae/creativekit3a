<?php
/**
 * auth/logout.php — Logout Handler
 * Destroys session, logs audit trail, and redirects.
 */

require_once __DIR__ . '/../config/db.php';

// Staff return to the portal login; storefront customers return to the shop
// (read the role now — the session is cleared below)
$was_staff = in_array($_SESSION['user_role'] ?? '', ['admin', 'staff'], true);

// Log the logout activity before destroying session
if (isset($_SESSION['user_id']) && $pdo) {
    try {
        $log = $pdo->prepare('INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (:uid, :action, :details, :ip)');
        $log->execute([
            ':uid'     => $_SESSION['user_id'],
            ':action'  => 'logout',
            ':details' => 'User logged out: ' . ($_SESSION['user_name'] ?? 'Unknown'),
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ]);
    } catch (Exception $e) {
        // Continue logout even if logging fails
    }
}

// Clear session data
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

// Start a fresh session just to hold the goodbye message
session_start();
set_flash('info', 'You have been signed out successfully.');
header('Location: ' . BASE_URL . ($was_staff ? '/pages/login.php?msg=logged_out' : '/'));
exit;
