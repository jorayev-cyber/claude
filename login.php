<?php
require_once __DIR__ . '/config/config.php';

// Agar kirgan bo'lsa - admin panelga yo'naltirish
if (isLoggedIn()) {
    redirect(ADMIN_URL);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email va parolni kiriting';
    } elseif (login($email, $password)) {
        // Role ga qarab yo'naltirish
        switch ($_SESSION['user_role']) {
            case 'super_admin':
                redirect(ADMIN_URL . 'super/');
                break;
            case 'manager':
                redirect(ADMIN_URL . 'manager/');
                break;
            case 'teacher':
                redirect(ADMIN_URL . 'teacher/');
                break;
            default:
                redirect(ADMIN_URL);
        }
    } else {
        $error = 'Email yoki parol noto\'g\'ri';
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirish - EduCRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-gradient);
            font-family: 'Segoe UI', system-ui, sans-serif;
            overflow: hidden;
        }

        /* Animated Background */
        .bg-bubbles {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .bg-bubbles li {
            position: absolute;
            list-style: none;
            display: block;
            width: 40px; height: 40px;
            background: rgba(255,255,255,0.1);
            bottom: -160px;
            border-radius: 50%;
            animation: floatUp 25s infinite;
        }

        .bg-bubbles li:nth-child(1) { left: 10%; width: 80px; height: 80px; animation-delay: 0s; }
        .bg-bubbles li:nth-child(2) { left: 20%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
        .bg-bubbles li:nth-child(3) { left: 35%; animation-delay: 4s; }
        .bg-bubbles li:nth-child(4) { left: 50%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 18s; }
        .bg-bubbles li:nth-child(5) { left: 65%; animation-delay: 3s; }
        .bg-bubbles li:nth-child(6) { left: 75%; width: 110px; height: 110px; animation-delay: 5s; }
        .bg-bubbles li:nth-child(7) { left: 85%; width: 30px; height: 30px; animation-delay: 7s; }
        .bg-bubbles li:nth-child(8) { left: 45%; width: 25px; height: 25px; animation-delay: 15s; animation-duration: 45s; }
        .bg-bubbles li:nth-child(9) { left: 5%; width: 15px; height: 15px; animation-delay: 2s; animation-duration: 35s; }
        .bg-bubbles li:nth-child(10) { left: 90%; width: 90px; height: 90px; animation-delay: 10s; }

        @keyframes floatUp {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; border-radius: 50%; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; border-radius: 50%; }
        }

        /* Login Card */
        .login-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-logo .icon {
            width: 70px; height: 70px;
            background: var(--bg-gradient);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .login-logo .icon i {
            font-size: 32px;
            color: white;
        }

        .login-logo h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .login-logo p {
            color: #64748b;
            font-size: 14px;
        }

        .form-floating {
            margin-bottom: 16px;
        }

        .form-floating .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            height: 56px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-floating .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.1);
        }

        .btn-login {
            width: 100%;
            height: 52px;
            background: var(--bg-gradient);
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99,102,241,0.3);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 12px;
            border: none;
            font-size: 14px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <ul class="bg-bubbles">
        <li></li><li></li><li></li><li></li><li></li>
        <li></li><li></li><li></li><li></li><li></li>
    </ul>

    <!-- Login Form -->
    <div class="login-card animate__animated animate__fadeIn">
        <div class="login-logo">
            <div class="icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1>EduCRM</h1>
            <p>O'quv markaz boshqaruv tizimi</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="Email" value="<?= clean($_POST['email'] ?? '') ?>" required>
                <label for="email"><i class="fas fa-envelope me-2"></i>Email manzil</label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Parol" required>
                <label for="password"><i class="fas fa-lock me-2"></i>Parol</label>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Kirish
            </button>
        </form>

        <div class="back-link">
            <a href="<?= BASE_URL ?>"><i class="fas fa-arrow-left me-1"></i> Bosh sahifaga qaytish</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
