<?php
require_once 'functions.php';
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}
$err = ''; $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$fullname || !$email || !$password) {
        $err = 'All fields are required';
    } else {
        if (register_user($fullname, $email, $password)) {
            $ok = 'Registration successful. You may login now.';
        } else {
            $err = 'Registration failed. Email may already exist.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Register - Visitor Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- External CSS File -->
    <link rel="stylesheet" href="assets/css/register.css">
</head>

<body>

    <!-- Dark/Light Toggle -->
    <button id="theme-toggle" class="btn btn-sm toggle-btn">
        <i class="bi bi-moon-stars-fill"></i>
    </button>

    <!-- Register Card -->
    <div class="register-container">
        <div class="logo">
            <div class="logo-circle">
                <i class="bi bi-person-plus"></i>
            </div>
            <h1>Register</h1>
        </div>
        <p class="subtitle">Create your account for Visitor Inquiry Logging System</p>
        <div class="underline"></div>

        <!-- Messages -->
        <?php if ($err): ?>
        <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($err); ?></div>
        <?php endif; ?>
        <?php if ($ok): ?>
        <div class="alert alert-success mt-3"><?php echo htmlspecialchars($ok); ?></div>
        <?php endif; ?>

        <!-- Register Form -->
        <form method="post" id="registerForm" novalidate class="mt-3">

            <!-- Full Name -->
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <input name="fullname" type="text" class="form-control" placeholder="Enter full name" required>
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input name="email" type="email" class="form-control" placeholder="Enter email" required>
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

            <!-- Register Button with Spinner -->
            <button type="submit" class="btn-register" id="registerBtn">
                <span id="btnText">Register</span>
                <div class="spinner d-none" id="spinner"></div>
                <i class="bi bi-person-plus" id="btnIcon"></i>
            </button>

            <!-- Back to Login -->
            <a href="index.php" class="btn-login">
                <i class="bi bi-box-arrow-in-left"></i>
                Back to Login
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

    // Spinner on Submit
    const form = document.getElementById('registerForm');
    const btn = document.getElementById('registerBtn');
    const btnText = document.getElementById('btnText');
    const spinner = document.getElementById('spinner');
    const btnIcon = document.getElementById('btnIcon');

    form.addEventListener('submit', function() {
        btn.disabled = true;
        btnText.textContent = 'Creating Account';
        spinner.classList.remove('d-none');
        btnIcon.classList.add('d-none');
    });
    </script>
</body>

</html>