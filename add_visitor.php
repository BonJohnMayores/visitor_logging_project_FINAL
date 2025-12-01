<?php
require_once 'functions.php';
require_login();

$err = ''; $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'visitor_name' => trim($_POST['visitor_name'] ?? ''),
        'visit_date'   => $_POST['visit_date'] ?? date('Y-m-d'),
        'visit_time'   => $_POST['visit_time'] ?? date('H:i:s'),
        'address'      => trim($_POST['address'] ?? ''),
        'contact'      => trim($_POST['contact'] ?? ''),
        'school_office'=> trim($_POST['school_office'] ?? ''),
        'purpose'      => $_POST['purpose'] ?? 'Inquiry',
        'created_by'   => $_SESSION['user_id']
    ];
    if (!$data['visitor_name']) {
        $err = 'Visitor name is required.';
    } else {
        if (add_visitor($data)) {
            $ok = 'Visitor added successfully.';
        } else {
            $err = 'Failed to add visitor.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Visitor - Visitor Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    :root {
        --bg-light: #f8f9fa;
        --bg-dark: #212529;
        --card-light: #ffffff;
        --card-dark: #2d333b;
        --text-light: #212529;
        --text-dark: #e9ecef;
        --text-muted-light: #6c757d;
        --text-muted-dark: #adb5bd;
        --sidebar-light: #343a40;
        --sidebar-dark: #1a1d21;
        --border-light: rgba(0, 0, 0, 0.1);
        --border-dark: rgba(255, 255, 255, 0.1);
        --input-bg-dark: #343a40;
        --input-border-dark: #495057;
    }

    body {
        background: var(--bg-light);
        color: var(--text-light);
        transition: background 0.3s, color 0.3s;
    }

    .dark-mode {
        background: var(--bg-dark);
        color: var(--text-dark);
    }

    .sidebar {
        background: var(--sidebar-light);
        transition: background 0.3s;
    }

    .dark-mode .sidebar {
        background: var(--sidebar-dark);
    }

    .sidebar * {
        color: #ced4da !important;
    }

    .dark-mode .sidebar * {
        color: #e9ecef !important;
    }

    .card,
    .top-bar {
        background: var(--card-light);
        border: 1px solid var(--border-light);
        transition: all 0.3s;
    }

    .dark-mode .card,
    .dark-mode .top-bar {
        background: var(--card-dark);
        border-color: var(--border-dark);
        color: var(--text-dark);
    }

    .text-muted {
        color: var(--text-muted-light) !important;
    }

    .dark-mode .text-muted {
        color: var(--text-muted-dark) !important;
    }

    .form-control,
    .form-select {
        background: white;
        color: var(--text-light);
        border-color: #ced4da;
    }

    .dark-mode .form-control,
    .dark-mode .form-select {
        background: var(--input-bg-dark);
        color: var(--text-dark);
        border-color: var(--input-border-dark);
    }

    .dark-mode .form-control::placeholder {
        color: #adb5bd;
    }

    .btn-outline-secondary {
        color: var(--text-light);
        border-color: #ced4da;
    }

    .dark-mode .btn-outline-secondary {
        color: var(--text-dark);
        border-color: var(--input-border-dark);
    }

    .dark-mode .btn-outline-secondary:hover {
        background: #495057;
        color: white;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100vh;
        padding: 1.5rem 0;
        box-shadow: 2px 0 10px rgba(0, 0, 0, .1);
    }

    .sidebar-header h4 {
        padding: 0 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .user-profile {
        display: flex;
        align-items: center;
        padding: 0 1.5rem;
        margin: 1.5rem 0;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #0dcaf0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #fff;
    }

    .user-info h6 {
        margin: 0;
        font-size: .95rem;
    }

    .user-info small {
        opacity: .8;
    }

    .nav-link {
        color: #ced4da !important;
        padding: .75rem 1.5rem;
        margin: .25rem 1rem;
        border-radius: .375rem;
        display: flex;
        align-items: center;
        gap: .75rem;
        transition: all .2s;
    }

    .nav-link:hover,
    .nav-link.active {
        background: #495057;
        color: #fff !important;
    }

    .main-content {
        margin-left: 250px;
        padding: 2rem;
    }

    .top-bar {
        background: var(--card-light);
        padding: 1rem 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        border-radius: .75rem;
    }

    .top-bar h3 {
        margin: 0;
        font-weight: 600;
        color: var(--text-light);
    }

    .dark-mode .top-bar h3 {
        color: var(--text-dark);
    }

    .top-bar .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    @media(max-width:992px) {
        .sidebar {
            width: 80px;
            padding: 1rem 0;
        }

        .sidebar-header h4 span,
        .user-info,
        .nav-link span {
            display: none;
        }

        .nav-link {
            justify-content: center;
            margin: .5rem;
        }

        .main-content {
            margin-left: 80px;
        }
    }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="bi bi-journal-text"></i> <span>Visitor Log</span></h4>
        </div>
        <div class="user-profile">
            <div class="user-avatar">AD</div>
            <div class="user-info">
                <h6>Admin</h6>
                <small><?php echo htmlspecialchars($_SESSION['user_name']); ?></small>
            </div>
        </div>
        <nav>
            <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> <span>Dashboard</span></a>
            <a class="nav-link active" href="add_visitor.php"><i class="bi bi-person-plus"></i> <span>Add
                    Visitor</span></a>
            <a class="nav-link" href="export.php"><i class="bi bi-file-earmark-arrow-down"></i> <span>Export
                    CSV</span></a>
            <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h3>Visitor Log</h3>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <button id="theme-toggle" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="mb-4">Add Visitor</h5>
                <?php if ($err): ?><div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
                <?php endif; ?>
                <?php if ($ok):  ?><div class="alert alert-success"><?php echo htmlspecialchars($ok); ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Visitor Name <span class="text-danger">*</span></label>
                        <input name="visitor_name" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" name="visit_date" value="<?php echo date('Y-m-d'); ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time</label>
                            <input type="time" name="visit_time" value="<?php echo date('H:i'); ?>"
                                class="form-control">
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Contact #</label><input name="contact"
                            class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Address</label><input name="address"
                            class="form-control"></div>
                    <div class="mb-3"><label class="form-label">School / Office</label><input name="school_office"
                            class="form-control"></div>
                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <select name="purpose" class="form-select">
                            <option>Inquiry</option>
                            <option>Exam</option>
                            <option>Visit</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Save</button>
                        <a href="dashboard.php" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
    </script>
</body>

</html>