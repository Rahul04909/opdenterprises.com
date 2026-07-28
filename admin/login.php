<?php
require_once __DIR__ . '/../includes/config.php';
session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :uname OR email = :email LIMIT 1");
            $stmt->execute(['uname' => $username, 'email' => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id']    = $admin['id'];
                $_SESSION['admin_name']  = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_mobile']   = $admin['mobile'];
                $_SESSION['admin_profile_pic'] = $admin['profile_pic'];

                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            error_log('LOGIN ERROR: ' . $e->getMessage());
            $error = ($_ENV['APP_DEBUG'] === 'true')
                ? 'DB Error: ' . $e->getMessage()
                : 'System error. Please contact administrator.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
        }
        .login-card .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-card .logo h3 {
            font-weight: 700;
            color: #333;
        }
        .login-card .logo p {
            color: #777;
            font-size: 0.9rem;
        }
        .form-control {
            border-radius: 8px;
            padding: 0.7rem 1rem;
        }
        .btn-login {
            border-radius: 8px;
            padding: 0.7rem;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: #fff;
            width: 100%;
        }
        .btn-login:hover {
            opacity: 0.9;
        }
        .error-msg {
            background: #f8d7da;
            color: #842029;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <img src="../assets/images/logo.png" alt="O.P Defence Enterprises" height="60">
            <h3 class="mt-2">Admin Login</h3>
            <p>O.P Defence Enterprises</p>
        </div>

        <?php if ($error): ?>
            <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Username or Email</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username or email" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <p class="text-center mt-3 mb-0" style="font-size:0.85rem;color:#999;">
            <a href="../index.php" class="text-decoration-none"><i class="fas fa-arrow-left"></i> Back to Website</a>
        </p>
    </div>
</body>
</html>
