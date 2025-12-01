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
    <style>
    :root {
        --bg-light: #f8f9ff;
        --bg-dark: #1a1a2e;
        --card-light: #ffffff;
        --card-dark: #16213e;
        --text-light: #333;
        --text-dark: #e0e0ff;
        --primary: #4e54c8;
        --primary-dark: #8b5cf6;
        --input-bg-light: #f0f4ff;
        --input-bg-dark: #1e2a44;
        --input-border-light: #d0d7ff;
        --input-border-dark: #3a4a6e;
        --label-light: #555;
        --label-dark: #b0b8ff;
        --border-color-light: #e0e7ff;
        --border-color-dark: #2a3b5e;
        --input-height: 46px;
        --text-input-light: #212529;
        --text-input-dark: #e9ecef;
    }

    body {
        background: var(--bg-light);
        color: var(--text-light);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 2.5rem 1rem;
        gap: 1.5rem;
        transition: background 0.3s;
    }

    .dark-mode {
        background: var(--bg-dark);
        color: var(--text-dark);
    }

    .register-container {
        width: 100%;
        max-width: 380px;
        padding: 2rem;
        background: var(--card-light);
        border: 1px solid var(--border-color-light);
        border-radius: 14px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease-out forwards;
        z-index: 1;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dark-mode .register-container {
        background: var(--card-dark);
        border-color: var(--border-color-dark);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .toggle-btn {
        position: fixed;
        top: 1.5rem;
        right: 1.5rem;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        padding: 0.45rem 0.7rem;
        font-size: 0.9rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .dark-mode .toggle-btn {
        background: rgba(0, 0, 0, 0.4);
        border-color: rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    /* LOGO */
    .logo {
        text-align: center;
        margin-bottom: 1.2rem;
    }

    .logo .logo-circle {
        width: 70px;
        height: 70px;
        background: var(--primary);
        border-radius: 50%;
        margin: 0 auto 0.4rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8rem;
        font-weight: bold;
    }

    .dark-mode .logo .logo-circle {
        background: var(--primary-dark);
    }

    .logo h1 {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--primary);
        margin: 0;
    }

    .dark-mode .logo h1 {
        color: var(--primary-dark);
    }

    .subtitle {
        text-align: center;
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 1.2rem;
    }

    .dark-mode .subtitle {
        color: #aaa;
    }

    .underline {
        width: 45px;
        height: 3px;
        background: var(--primary);
        margin: 0.8rem auto;
        border-radius: 2px;
    }

    .dark-mode .underline {
        background: var(--primary-dark);
    }

    .form-label {
        font-weight: 600;
        color: var(--label-light);
        margin-bottom: 0.4rem;
        font-size: 0.9rem;
    }

    .dark-mode .form-label {
        color: var(--label-dark);
    }

    .input-group {
        height: var(--input-height);
        margin-bottom: 0.9rem;
        position: relative;
    }

    .input-group .input-group-text {
        background: transparent;
        border: 1px solid var(--input-border-light);
        border-right: none;
        border-radius: 10px 0 0 10px;
        padding: 0 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dark-mode .input-group .input-group-text {
        border-color: var(--input-border-dark);
        color: #adb5bd;
    }

    .input-group input {
        height: 100%;
        border: 1px solid var(--input-border-light);
        border-left: none;
        border-radius: 0 10px 10px 0;
        background: var(--input-bg-light);
        padding: 0 0.9rem;
        font-size: 0.95rem;
        color: var(--text-input-light) !important;
        transition: all 0.3s;
    }

    .dark-mode .input-group input {
        background: var(--input-bg-dark);
        border-color: var(--input-border-dark);
        color: var(--text-input-dark) !important;
    }

    .input-group input::placeholder {
        color: #aaa !important;
        opacity: 0.7;
    }

    .dark-mode .input-group input::placeholder {
        color: #777 !important;
    }

    .input-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2.5px rgba(78, 84, 200, 0.2);
        z-index: 10;
    }

    .dark-mode .input-group input:focus {
        border-color: var(--primary-dark);
        box-shadow: 0 0 0 2.5px rgba(139, 92, 246, 0.3);
    }

    .input-group .input-group-text i {
        font-size: 1rem;
        color: #777;
    }

    .dark-mode .input-group .input-group-text i {
        color: #999;
    }

    /* PASSWORD TOGGLE */
    .password-toggle {
        position: absolute;
        right: 0.7rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #777;
        font-size: 1rem;
        cursor: pointer;
        z-index: 11;
    }

    .dark-mode .password-toggle {
        color: #bbb;
    }

    .btn-register {
        width: 100%;
        padding: 0.85rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        transition: all 0.3s;
        margin-bottom: 0.6rem;
        position: relative;
    }

    .btn-register:hover {
        background: #3b41b3;
        transform: translateY(-1px);
    }

    .dark-mode .btn-register {
        background: var(--primary-dark);
    }

    .dark-mode .btn-register:hover {
        background: #7c4dff;
    }

    .btn-register:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .btn-login {
        width: 100%;
        padding: 0.85rem;
        background: transparent;
        color: var(--primary);
        border: 2px solid var(--primary);
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        transition: all 0.3s;
    }

    .btn-login:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-1px);
    }

    .dark-mode .btn-login {
        color: var(--primary-dark);
        border-color: var(--primary-dark);
    }

    .dark-mode .btn-login:hover {
        background: var(--primary-dark);
        color: white;
    }

    .text-muted {
        color: #6c757d !important;
        font-size: 0.8rem;
    }

    .dark-mode .text-muted {
        color: #adb5bd !important;
    }

    /* SPINNER */
    .spinner {
        width: 1.1rem;
        height: 1.1rem;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-left: 0.4rem;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* RESPONSIVE */
    @media (max-width: 480px) {
        body {
            padding: 2rem 0.8rem;
            gap: 1rem;
        }

        .register-container {
            padding: 1.8rem;
        }

        .logo .logo-circle {
            width: 60px;
            height: 60px;
            font-size: 1.6rem;
        }

        .logo h1 {
            font-size: 1.5rem;
        }

        .toggle-btn {
            top: 1rem;
            right: 1rem;
        }
    }

    @media (max-height: 600px) {
        body {
            padding: 1.5rem 0.8rem;
        }
    }
    </style>
</head>

<body>

    <!-- Dark/Light Toggle -->
    <button id="theme-toggle" class="btn btn-sm toggle-btn">
        <i class="bi bi-moon-stars-fill"></i>
    </button>

    <!-- Register Card -->
    <div class="register-container">
        <div class="logo">
            <div class="logo-circle">U</div>
            <h1>User's Register</h1>
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