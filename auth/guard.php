<?php
/**
 * auth/guard.php — Route Protection & Role-Based Access Control (RBAC)
 * Include at the top of any restricted administrative page.
 *
 * Usage:
 *   require_once __DIR__ . '/../config/db.php';
 *   require_once __DIR__ . '/../auth/guard.php';
 *
 * Restricting to a specific role:
 *   $required_role = 'admin';
 *   require_once __DIR__ . '/../auth/guard.php';
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    if (function_exists('set_flash')) {
        set_flash('warning', 'Please sign in to access this page.');
    }
    header('Location: /pages/login.php?msg=please_login');
    exit;
}

// Optional role restriction
if (isset($required_role)) {
    $user_role = $_SESSION['user_role'] ?? 'staff';
    
    // Admin has superuser access to all roles
    $has_access = ($user_role === 'admin') || ($user_role === $required_role);
    
    if (!$has_access) {
        if (function_exists('set_flash')) {
            set_flash('danger', 'Access denied. You do not have permission to view that resource.');
        }
        header('Location: /pages/dashboard.php?msg=access_denied');
        exit;
    }
}

// Convenience global variables for pages
$current_user_id    = $_SESSION['user_id'];
$current_user_role  = $_SESSION['user_role'] ?? 'staff';
$current_user_name  = $_SESSION['user_name'] ?? 'User';
$current_user_email = $_SESSION['user_email'] ?? '';
$is_admin           = ($current_user_role === 'admin');
