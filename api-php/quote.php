<?php
/**
 * api/quote.php — Request a Quote Endpoint
 * Receives quote submission payload and returns JSON response.
 */

header('Content-Type: application/json; charset=utf-8');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
    exit;
}

$name         = trim($input['name'] ?? '');
$email        = trim($input['email'] ?? '');
$phone        = trim($input['phone'] ?? '');
$product_type = trim($input['product_type'] ?? '');
$quantity     = trim($input['quantity'] ?? '');
$budget       = trim($input['budget'] ?? '');
$deadline     = trim($input['deadline'] ?? '');
$details      = trim($input['details'] ?? '');
$custom       = $input['customization'] ?? [];
$source       = trim($input['source'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($phone) || empty($product_type) || empty($quantity)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please provide all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

// Return clean success response
echo json_encode([
    'success' => true,
    'message' => 'Quote request received successfully. Our team will contact you within 24 hours.',
    'data'    => [
        'name'         => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
        'email'        => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
        'product_type' => htmlspecialchars($product_type, ENT_QUOTES, 'UTF-8'),
        'quantity'     => htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8'),
    ]
]);
