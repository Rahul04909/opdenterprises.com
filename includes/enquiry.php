<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$quantity = trim($_POST['quantity'] ?? '');
$message = trim($_POST['message'] ?? '');
$product = trim($_POST['product'] ?? '');

if (!$name || !$phone) {
    echo json_encode(['success' => false, 'error' => 'Name and phone are required']);
    exit;
}

$to = $_ENV['MAIL_TO'] ?? 'info@opdenterprises.com';
$subject = 'Product Enquiry: ' . $product;
$body = "Product: $product\n\nName: $name\nPhone: $phone\nEmail: $email\nQuantity: $quantity\n\nMessage:\n$message";
$headers = 'From: ' . ($email ?: 'noreply@opdenterprises.com');

mail($to, $subject, $body, $headers);

try {
    $pdo = getDBConnection();
    $pdo->exec("CREATE TABLE IF NOT EXISTS enquiries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product VARCHAR(255) NOT NULL DEFAULT '',
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        email VARCHAR(255) DEFAULT '',
        quantity VARCHAR(50) DEFAULT '',
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $stmt = $pdo->prepare("INSERT INTO enquiries (product, name, phone, email, quantity, message) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$product, $name, $phone, $email, $quantity, $message]);
} catch (Exception $e) {
    // Non-critical - email was sent
}

echo json_encode(['success' => true]);
