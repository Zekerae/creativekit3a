<?php
/**
 * actions/product-actions.php — Product CRUD Handler (POST)
 * Demonstrates:
 *   - $_POST handling
 *   - switch statement for action routing
 *   - if-else conditional validation
 *   - Arithmetic, comparison, and logical operators
 *   - Role-Based Access Control (Admin vs Staff)
 *   - Prepared PDO statements (INSERT, UPDATE, DELETE)
 *   - Server-side validation and sanitization
 *   - Activity logging
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../auth/guard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/products.php');
    exit;
}

$action = $_POST['action'] ?? '';

if (!$pdo) {
    set_flash('danger', 'Database connection not available.');
    header('Location: ' . BASE_URL . '/pages/products.php');
    exit;
}

// -------------------------------------------------------------
// Action Routing using switch statement (Required PHP Construct)
// -------------------------------------------------------------
switch ($action) {

    // =========================================================
    // 1. CREATE PRODUCT
    // =========================================================
    case 'create':
        $name        = trim($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $sku         = trim($_POST['sku'] ?? '');
        $price_min   = (float)($_POST['price_min'] ?? 0.0);
        $specs       = trim($_POST['specs'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $badge       = trim($_POST['badge'] ?? '');
        $status      = in_array($_POST['status'] ?? '', ['active', 'draft', 'archived']) ? $_POST['status'] : 'active';
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $image       = trim($_POST['image'] ?? '');
        if (empty($image)) {
            $image = '/assets/images/placeholder.png';
        }

        // Generate URL slug using regex
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        // Server-Side Validation (Dual Validation: Server Side)
        $errors = [];
        if (empty($name) || strlen($name) < 3) {
            $errors[] = 'Product name must be at least 3 characters.';
        }
        if ($category_id <= 0) {
            $errors[] = 'Please select a valid product category.';
        }
        if ($price_min <= 0.0) {
            $errors[] = 'Base price must be greater than zero.';
        }
        if (empty($sku)) {
            // Auto-generate SKU if omitted
            $sku = 'PRD-' . strtoupper(substr(md5(uniqid()), 0, 6));
        }

        // Check for duplicate SKU using prepared statement
        $check_sku = $pdo->prepare('SELECT id FROM products WHERE sku = :sku LIMIT 1');
        $check_sku->execute([':sku' => $sku]);
        if ($check_sku->fetch()) {
            $errors[] = "The SKU '{$sku}' is already in use by another product.";
        }

        // If validation fails, redirect back with form data preserved in session
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data']   = $_POST;
            header('Location: ' . BASE_URL . '/pages/product-form.php');
            exit;
        }

        // Insert new product using prepared statement (INSERT requirement)
        $insert_sql = 'INSERT INTO products 
            (category_id, name, slug, sku, description, specs, price_min, image, badge, status, is_featured, created_by)
            VALUES 
            (:category_id, :name, :slug, :sku, :description, :specs, :price_min, :image, :badge, :status, :is_featured, :created_by)';

        $stmt = $pdo->prepare($insert_sql);
        $stmt->execute([
            ':category_id' => $category_id,
            ':name'        => $name,
            ':slug'        => $slug,
            ':sku'         => $sku,
            ':description' => $description,
            ':specs'       => $specs,
            ':price_min'   => $price_min,
            ':image'       => $image,
            ':badge'       => $badge,
            ':status'      => $status,
            ':is_featured' => $is_featured,
            ':created_by'  => $current_user_id,
        ]);

        $new_id = $pdo->lastInsertId();

        // Audit log entry
        $log = $pdo->prepare('INSERT INTO activity_log (user_id, action, entity_type, entity_id, details, ip_address) VALUES (:uid, :act, :type, :eid, :details, :ip)');
        $log->execute([
            ':uid'     => $current_user_id,
            ':act'     => 'create_product',
            ':type'    => 'product',
            ':eid'     => $new_id,
            ':details' => "Created product '{$name}' (SKU: {$sku}) with price ₱" . number_format($price_min, 2),
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ]);

        set_flash('success', "Product '{$name}' was created successfully.");
        header('Location: ' . BASE_URL . '/pages/products.php');
        exit;

    // =========================================================
    // 2. UPDATE PRODUCT
    // =========================================================
    case 'update':
        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $sku         = trim($_POST['sku'] ?? '');
        $price_min   = (float)($_POST['price_min'] ?? 0.0);
        $specs       = trim($_POST['specs'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $badge       = trim($_POST['badge'] ?? '');
        $status      = in_array($_POST['status'] ?? '', ['active', 'draft', 'archived']) ? $_POST['status'] : 'active';
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $image       = trim($_POST['image'] ?? '');
        if (empty($image)) {
            $image = '/assets/images/placeholder.png';
        }

        if ($id <= 0) {
            set_flash('danger', 'Invalid product ID.');
            header('Location: ' . BASE_URL . '/pages/products.php');
            exit;
        }

        // Server-Side Validation
        $errors = [];
        if (empty($name) || strlen($name) < 3) {
            $errors[] = 'Product name must be at least 3 characters.';
        }
        if ($category_id <= 0) {
            $errors[] = 'Please select a valid product category.';
        }
        if ($price_min <= 0.0) {
            $errors[] = 'Base price must be greater than zero.';
        }

        // Check for duplicate SKU on OTHER products
        $check_sku = $pdo->prepare('SELECT id FROM products WHERE sku = :sku AND id != :id LIMIT 1');
        $check_sku->execute([':sku' => $sku, ':id' => $id]);
        if ($check_sku->fetch()) {
            $errors[] = "The SKU '{$sku}' is already in use by another product.";
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data']   = $_POST;
            header("Location: " . BASE_URL . "/pages/product-form.php?id={$id}");
            exit;
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        // Update product using prepared statement (UPDATE requirement)
        $update_sql = 'UPDATE products SET 
            category_id = :category_id,
            name        = :name,
            slug        = :slug,
            sku         = :sku,
            description = :description,
            specs       = :specs,
            price_min   = :price_min,
            image       = :image,
            badge       = :badge,
            status      = :status,
            is_featured = :is_featured
            WHERE id = :id';

        $stmt = $pdo->prepare($update_sql);
        $stmt->execute([
            ':category_id' => $category_id,
            ':name'        => $name,
            ':slug'        => $slug,
            ':sku'         => $sku,
            ':description' => $description,
            ':specs'       => $specs,
            ':price_min'   => $price_min,
            ':image'       => $image,
            ':badge'       => $badge,
            ':status'      => $status,
            ':is_featured' => $is_featured,
            ':id'          => $id,
        ]);

        // Audit log
        $log = $pdo->prepare('INSERT INTO activity_log (user_id, action, entity_type, entity_id, details, ip_address) VALUES (:uid, :act, :type, :eid, :details, :ip)');
        $log->execute([
            ':uid'     => $current_user_id,
            ':act'     => 'update_product',
            ':type'    => 'product',
            ':eid'     => $id,
            ':details' => "Updated product #{$id} ({$name})",
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ]);

        set_flash('success', "Product '{$name}' was updated successfully.");
        header('Location: ' . BASE_URL . '/pages/products.php');
        exit;

    // =========================================================
    // 3. DELETE PRODUCT (Role-Restricted: Admin Only)
    // =========================================================
    case 'delete':
        $id = (int)($_POST['id'] ?? 0);

        // RBAC: Only Admin can delete products
        if ($current_user_role !== 'admin') {
            set_flash('danger', 'Unauthorized action. Only administrators can delete records.');
            header('Location: ' . BASE_URL . '/pages/products.php');
            exit;
        }

        if ($id <= 0) {
            set_flash('danger', 'Invalid product ID.');
            header('Location: ' . BASE_URL . '/pages/products.php');
            exit;
        }

        // Fetch name for user feedback and logging
        $fetch = $pdo->prepare('SELECT name, sku FROM products WHERE id = :id');
        $fetch->execute([':id' => $id]);
        $prod = $fetch->fetch();

        if (!$prod) {
            set_flash('warning', 'Product was not found or already deleted.');
            header('Location: ' . BASE_URL . '/pages/products.php');
            exit;
        }

        // Delete using prepared statement (DELETE requirement)
        $del = $pdo->prepare('DELETE FROM products WHERE id = :id');
        $del->execute([':id' => $id]);

        // Audit log
        $log = $pdo->prepare('INSERT INTO activity_log (user_id, action, entity_type, entity_id, details, ip_address) VALUES (:uid, :act, :type, :eid, :details, :ip)');
        $log->execute([
            ':uid'     => $current_user_id,
            ':act'     => 'delete_product',
            ':type'    => 'product',
            ':eid'     => $id,
            ':details' => "Deleted product #{$id} ('{$prod['name']}', SKU: {$prod['sku']})",
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ]);

        set_flash('success', "Product '{$prod['name']}' was permanently deleted.");
        header('Location: ' . BASE_URL . '/pages/products.php');
        exit;

    // =========================================================
    // DEFAULT: UNKNOWN ACTION
    // =========================================================
    default:
        set_flash('danger', 'Invalid action requested.');
        header('Location: ' . BASE_URL . '/pages/products.php');
        exit;
}
