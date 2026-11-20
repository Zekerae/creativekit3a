<?php
/**
 * api/quote.php — Request a Quote Endpoint
 * Receives quote submission payload and returns JSON response.
 * Stores into MySQL `quote_requests` table if database is accessible.
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
$company      = trim($input['company'] ?? '');
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
    echo json_encode(['success' => false, 'message' => 'Please provide all required fields (Name, Email, Phone, Product Type, Quantity).']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

// Generate Quote Reference number
$quote_ref = 'CK3A-' . random_int(100000, 999999);

// Attempt database persistence if MySQL connection is available
require_once __DIR__ . '/../includes/db.php';
$pdo = get_db_connection();

if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO quote_requests (
                quote_ref, full_name, company_name, email, phone,
                product_type, quantity_range, target_budget, target_deadline,
                project_details, customization_options, source_url, status
            ) VALUES (
                :quote_ref, :name, :company, :email, :phone,
                :product_type, :quantity, :budget, :deadline,
                :details, :custom, :source, 'new'
            )
        ");

        $deadline_val = !empty($deadline) ? $deadline : null;
        $custom_json  = !empty($custom) ? json_encode($custom) : null;

        $stmt->execute([
            ':quote_ref'    => $quote_ref,
            ':name'         => $name,
            ':company'      => $company ?: null,
            ':email'        => $email,
            ':phone'        => $phone,
            ':product_type' => $product_type,
            ':quantity'     => $quantity,
            ':budget'       => $budget ?: null,
            ':deadline'     => $deadline_val,
            ':details'      => $details ?: null,
            ':custom'       => $custom_json,
            ':source'       => $source ?: null,
        ]);
    } catch (\Throwable $e) {
        // Silently log DB error so user still receives quote confirmation
        error_log('DB insert error in quote.php: ' . $e->getMessage());
    }
}

// Return clean success response
echo json_encode([
    'success'   => true,
    'message'   => 'Quote request received successfully. Our sales team will contact you within 24 hours.',
    'quote_ref' => $quote_ref,
    'data'      => [
        'name'         => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
        'email'        => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
        'product_type' => htmlspecialchars($product_type, ENT_QUOTES, 'UTF-8'),
        'quantity'     => htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8'),
    ]
]);
