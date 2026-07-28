<?php
/**
 * Run this script ONCE via CLI: php sql/insert-admin.php
 * Or access via browser: https://yourdomain.com/sql/insert-admin.php
 * DELETE this file after successful setup for security.
 */

$isCLI = (php_sapi_name() === 'cli');

if (!$isCLI) {
    echo '<pre>';
}

echo "=== Admin Table Setup ===\n\n";

// Attempt to connect without database first, then create it if needed
try {
    require_once __DIR__ . '/../includes/config.php';
    $pdo = getDBConnection();
} catch (PDOException $e) {
    // Try connecting without database to create it
    try {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $dbname = $_ENV['DB_DATABASE'] ?? '';
        $username = $_ENV['DB_USERNAME'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "[OK] Database '{$dbname}' created or already exists.\n";

        // Now connect with the database
        require_once __DIR__ . '/../includes/config.php';
        $pdo = getDBConnection();
    } catch (PDOException $e2) {
        echo "[ERROR] Cannot connect to MySQL: " . $e2->getMessage() . "\n";
        echo "\nPlease check your .env DB credentials and ensure MySQL is running.\n";
        if (!$isCLI) echo '</pre>';
        exit(1);
    }
}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        mobile VARCHAR(20) NOT NULL,
        username VARCHAR(50) NOT NULL UNIQUE,
        profile_pic VARCHAR(255) DEFAULT 'default.png',
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    echo "[OK] 'admins' table is ready.\n";

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = :username OR email = :email");
    $stmt->execute(['username' => 'admin', 'email' => 'admin@opdenterprises.com']);
    $exists = $stmt->fetchColumn();

    if ($exists > 0) {
        echo "[SKIP] Default admin already exists.\n";
    } else {
        $hashedPassword = password_hash('admin@123', PASSWORD_BCRYPT);

        $insert = $pdo->prepare("INSERT INTO admins (name, email, mobile, username, profile_pic, password) 
                                 VALUES (:name, :email, :mobile, :username, :profile_pic, :password)");
        $insert->execute([
            'name'       => 'Super Admin',
            'email'      => 'admin@opdenterprises.com',
            'mobile'     => '0000000000',
            'username'   => 'admin',
            'profile_pic'=> 'default.png',
            'password'   => $hashedPassword,
        ]);

        echo "[OK] Default admin created.\n";
        echo "     Username: admin\n";
        echo "     Password: admin@123\n\n";
        echo "⚠  IMPORTANT: Change the default password after first login!\n";
    }

} catch (PDOException $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    if (!$isCLI) echo '</pre>';
    exit(1);
}

echo "\n=== Done ===\n";

if (!$isCLI) {
    echo '</pre>';
    echo '<p style="color:red;font-weight:bold;">⚠ Delete this file (sql/insert-admin.php) after setup!</p>';
}
