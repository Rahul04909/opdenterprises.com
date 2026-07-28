<?php
require_once __DIR__ . '/../includes/config.php';

echo "=== Admin Table Setup ===\n\n";

try {
    $pdo = getDBConnection();

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
        echo "     Password: admin@123\n";
    }

} catch (PDOException $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Done ===\n";
