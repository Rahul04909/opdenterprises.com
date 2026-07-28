<?php
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['confirm'])) {
    $_SESSION['flash_message'] = 'Invalid request.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if (!$id) {
    $_SESSION['flash_message'] = 'Product ID is required.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php');
    exit;
}

$pdo = getDBConnection();

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['flash_message'] = 'Product not found.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php');
    exit;
}

$base = __DIR__ . '/../../';

if ($product['featured_image'] && file_exists($base . $product['featured_image'])) {
    @unlink($base . $product['featured_image']);
}

if ($product['brochure'] && file_exists($base . $product['brochure'])) {
    @unlink($base . $product['brochure']);
}

if ($product['og_image'] && $product['og_image'] !== $product['featured_image'] && file_exists($base . $product['og_image'])) {
    @unlink($base . $product['og_image']);
}

$galStmt = $pdo->prepare("SELECT image_path FROM product_gallery WHERE product_id = ?");
$galStmt->execute([$id]);
foreach ($galStmt->fetchAll() as $img) {
    if ($img['image_path'] && file_exists($base . $img['image_path'])) {
        @unlink($base . $img['image_path']);
    }
}

$pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);

$_SESSION['flash_message'] = 'Product deleted successfully.';
$_SESSION['flash_type'] = 'success';
header('Location: index.php');
exit;
