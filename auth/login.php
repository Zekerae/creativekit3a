<?php
/**
 * auth/login.php — Login Handler (POST)
 * Processes login form submissions using password_hash / password_verify.
 * Demonstrates: $_POST, if-else, prepared statements, $_SESSION, password_verify.
 */

require_once __DIR__ . '/../config/db.php';

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/login.php');
    exit;
}

// Retrieve and sanitize inputs ($_POST)
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Server-side validation
$errors = [];
if (empty($email)) {
    $errors[] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (empty($password)) {
    $errors[] = 'Password is required.';
}

if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_email']  = $email;
    header('Location: /pages/login.php');
    exit;
}

// Verify DB connection
if (!$pdo) {
    $_SESSION['login_errors'] = [
        'Could not connect to the database. Please ensure MySQL is running in XAMPP and creativekit3a_db is imported.'
    ];
    $_SESSION['login_email'] = $email;
    header('Location: /pages/login.php');
    exit;
}

// Look up user with prepared statement (prevents SQL injection)
$stmt = $pdo->prepare('SELECT id, first_name, last_name, email, password_hash, role, is_active FROM users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

// Authentication logic using if-else statements
if (!$user) {
    // User not found
    $_SESSION['login_errors'] = ['No account found with that email address.'];
    $_SESSION['login_email']  = $email;
    header('Location: /pages/login.php');
    exit;
} elseif (!$user['is_active']) {
    // Account deactivated
    $_SESSION['login_errors'] = ['This account has been deactivated. Please contact an administrator.'];
    $_SESSION['login_email']  = $email;
    header('Location: /pages/login.php');
    exit;
} elseif (!password_verify($password, $user['password_hash'])) {
    // Wrong password
    $_SESSION['login_errors'] = ['Incorrect password. Please try again.'];
    $_SESSION['login_email']  = $email;
    header('Location: /pages/login.php');
    exit;
} else {
    // Successful login — set session variables
    $_SESSION['user_id']    = (int)$user['id'];
    $_SESSION['user_role']  = $user['role'];
    $_SESSION['user_name']  = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_email'] = $user['email'];

    // Update last_login_at
    $update = $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
    $update->execute([':id' => $user['id']]);

    // Log activity
    $log = $pdo->prepare('INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (:uid, :action, :details, :ip)');
    $log->execute([
        ':uid'     => $user['id'],
        ':action'  => 'login',
        ':details' => 'User logged in: ' . $user['first_name'] . ' ' . $user['last_name'] . ' (' . ucfirst($user['role']) . ')',
        ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
    ]);

    set_flash('success', 'Welcome back, ' . htmlspecialchars($user['first_name']) . '! You are logged in as ' . ucfirst($user['role']) . '.');
    header('Location: /pages/dashboard.php');
    exit;
}
