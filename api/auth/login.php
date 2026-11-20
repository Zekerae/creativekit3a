<?php
/**
 * api/auth/login.php — Authentication Login Endpoint
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email    = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter both email and password.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid email address format.']);
    exit;
}

// Success response
$userFirstName = explode('@', $email)[0];
$userFirstName = ucfirst(preg_replace('/[^a-zA-Z]/', '', $userFirstName) ?: 'User');

echo json_encode([
    'success' => true,
    'message' => 'Login successful.',
    'user'    => [
        'first_name' => $userFirstName,
        'email'      => $email,
        'role'       => 'customer'
    ]
]);
