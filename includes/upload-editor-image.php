<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    echo json_encode(['error' => 'No file uploaded.']);
    exit;
}

$file = $_FILES['file'];
$allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$maxSize = 2097152;
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'Upload error.']);
    exit;
}

if (!in_array($ext, $allowedTypes)) {
    echo json_encode(['error' => 'Invalid file type.']);
    exit;
}

if ($file['size'] > $maxSize) {
    echo json_encode(['error' => 'File too large (max 2MB).']);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/products/editor/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
$dest = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $dest)) {
    $relativePath = 'uploads/products/editor/' . $filename;
    echo json_encode(['location' => $relativePath]);
} else {
    echo json_encode(['error' => 'Failed to save file.']);
}
