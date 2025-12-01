<?php
require_once 'functions.php';
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (login_user($email, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $err = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Visitor Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- External CSS File -->
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>

    <button id="theme-toggle" class="btn btn-sm toggle-btn">
        <i class="bi bi-moon-stars-fill"></i>
    </button>

    <!-- MAIN LOGIN CARD -->
    <div class="login-container">
        <div class="logo">
            <div class="logo-circle">
                <i class="bi bi-person-fill"></i>
            </div>
            <h1>LOGIN</h1>
        </div>
        <p class="subtitle">Access The Visitor Inquiry Logging System</p>
        <div class="underline"></div>

        <?php if ($err): ?>
        <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($err); ?></div>
        <?php endif; ?>

        <form method="post" id="loginForm" novalidate class="mt-3">
            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input name="email" type="email" class="form-control" placeholder="Enter Email" required>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3 position-relative">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input name="password" type="password" id="password" class="form-control" placeholder="•••••"
                        required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="options">
                <label class="remember-me">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <span id="btnText">Login</span>
                <div class="spinner d-none" id="spinner"></div>
                <i class="bi bi-arrow-right-circle" id="btnIcon"></i>
            </button>

            <a href="register.php" class="btn-register">
                <i class="bi bi-person-plus"></i> Register
            </a>
        </form>

        <p class="text-muted small text-center mt-3">
            Design & Coded By: <strong>BonJohn Mayores</strong>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Theme Toggle
    const toggle = document.getElementById('theme-toggle');
    const body = document.body;
    const icon = toggle.querySelector('i');

    if (localStorage.getItem('theme') === 'dark' ||
        (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        body.classList.add('dark-mode');
        icon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
    }

    toggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        if (body.classList.contains('dark-mode')) {
            icon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
            localStorage.setItem('theme', 'dark');
        } else {
            icon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
            localStorage.setItem('theme', 'light');
        }
    });

    // Password Toggle
    function togglePassword() {
        const input = document.getElementById('password');
        const btn = document.querySelector('.password-toggle i');
        if (input.type === 'password') {
            input.type = 'text';
            btn.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            btn.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    // Spinner
    const form = document.getElementById('loginForm');
    const btn = document.getElementById('loginBtn');
    const btnText = document.getElementById('btnText');
    const spinner = document.getElementById('spinner');
    const btnIcon = document.getElementById('btnIcon');

    form.addEventListener('submit', function() {
        btn.disabled = true;
        btnText.textContent = 'Logging in';
        spinner.classList.remove('d-none');
        btnIcon.classList.add('d-none');
    });
    </script>
</body>

</html>