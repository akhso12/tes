<?php
/**
 * Admin Login Page
 * SMK INFOKOM BOGOR - Modern Design
 */

require_once __DIR__ . '/../config/database.php';
session_start();

// Redirect jika sudah login
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Regenerate session untuk mencegah session fixation
                session_regenerate_id(true);

                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['nama_lengkap'];
                $_SESSION['admin_login_time'] = time();

                header("Location: index.php");
                exit;
            } else {
                $error = 'Username atau password salah.';
            }
        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | SMK INFOKOM BOGOR</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #1a3a5c;
            --primary-dark: #0f2a45;
            --primary-light: #2c5f8a;
            --accent: #c8a415;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --radius: 12px;
            --shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative elements */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(200,164,21,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(44,95,138,0.3) 0%, transparent 70%);
            border-radius: 50%;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: var(--white);
            border-radius: 20px;
            padding: 48px 40px;
            box-shadow: var(--shadow);
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .login-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--white);
            font-size: 2rem;
            box-shadow: 0 8px 24px rgba(26,58,92,0.3);
        }

        .login-header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 6px;
        }

        .login-header p {
            color: var(--gray-500);
            font-size: 0.92rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.88rem;
            color: var(--gray-700);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1rem;
            transition: color 0.3s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 16px 14px 46px;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            color: var(--gray-700);
            transition: all 0.3s;
            background: var(--gray-50);
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(26,58,92,0.08);
        }

        .input-wrapper input:focus + i,
        .input-wrapper input:focus ~ i {
            color: var(--primary);
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            font-size: 1rem;
            padding: 4px;
        }

        .toggle-password:hover { color: var(--gray-600); }

        .alert-error {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            border-radius: var(--radius);
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 16px rgba(26,58,92,0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(26,58,92,0.4);
        }

        .btn-login:active { transform: translateY(0); }

        .login-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--gray-100);
        }

        .login-footer a {
            color: var(--primary);
            font-size: 0.88rem;
            font-weight: 500;
        }

        .login-footer a:hover { color: var(--accent); }

        .copyright {
            text-align: center;
            margin-top: 24px;
            color: rgba(255,255,255,0.6);
            font-size: 0.82rem;
        }

        @media (max-width: 480px) {
            .login-card { padding: 36px 24px; }
            .login-header h1 { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1>Admin Panel</h1>
                <p>SMK INFOKOM BOGOR</p>
            </div>

            <?php if ($error): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" required autofocus placeholder="Masukkan username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required placeholder="Masukkan password">
                        <i class="fas fa-lock"></i>
                        <button type="button" class="toggle-password" onclick="togglePass()" aria-label="Toggle password visibility">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Masuk ke Dashboard
                </button>
            </form>

            <div class="login-footer">
                <a href="../index.php"><i class="fas fa-arrow-left"></i> Kembali ke Website</a>
            </div>
        </div>

        <p class="copyright">&copy; <?= date('Y') ?> SMK INFOKOM BOGOR. All rights reserved.</p>
    </div>

    <script>
        function togglePass() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>