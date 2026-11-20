<?php
/**
 * api/auth/register.php — Account Registration Endpoint
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$first_name = trim($input['first_name'] ?? '');
$last_name  = trim($input['last_name'] ?? '');
$email      = trim($input['email'] ?? '');
$phone      = trim($input['phone'] ?? '');
$password   = trim($input['password'] ?? '');

if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Account created successfully.',
    'user'    => [
        'first_name' => htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8'),
        'last_name'  => htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8'),
        'email'      => htmlspecialchars($email, ENT_QUOTES, 'UTF-8')
    ]
]);
