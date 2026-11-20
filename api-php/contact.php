<?php
/**
 * api/contact.php — Contact Form Endpoint
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$name    = trim($input['name'] ?? $input['full_name'] ?? '');
$email   = trim($input['email'] ?? '');
$message = trim($input['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please complete all required fields.']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Thank you for reaching out! We will get back to you shortly.'
]);
