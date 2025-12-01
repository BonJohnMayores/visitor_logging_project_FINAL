<?php
require_once 'functions.php';
require_login();

$id = $_GET['id'] ?? 0;
$visitor = get_visitor_by_id($id);

if (!$visitor) {
    header('Location: dashboard.php');
    exit;
}

$err = ''; $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id' => $id,
        'visitor_name' => trim($_POST['visitor_name'] ?? ''),
        'visit_date'   => $_POST['visit_date'] ?? '',
        'visit_time'   => $_POST['visit_time'] ?? '',
        'contact'      => trim($_POST['contact'] ?? ''),
        'address'      => trim($_POST['address'] ?? ''),
        'school_office'=> trim($_POST['school_office'] ?? ''),
        'purpose'      => $_POST['purpose'] ?? 'Inquiry'
    ];

    if (!$data['visitor_name']) {
        $err = 'Name is required.';
    } else {
        if (update_visitor($data)) {
            $ok = 'Visitor updated successfully.';
            $visitor = get_visitor_by_id($id);
        } else {
            $err = 'Update failed.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Visitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- External CSS File -->
    <link rel="stylesheet" href="assets/css/update_visitor.css">
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="bi bi-journal-text"></i> <span>Visitor Log</span></h4>
        </div>
        <div class="user-profile">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="user-info">
                <h6>Welcome!</h6>
                <small><?php echo htmlspecialchars($_SESSION['user_name']); ?></small>
            </div>
        </div>
        <nav>
            <a class="nav-link" href="dashboard.php">
                <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
            </a>
            <a class="nav-link" href="add_visitor.php">
                <i class="bi bi-person-plus"></i> <span>Add Visitor</span>
            </a>
            <a class="nav-link" href="export.php">
                <i class="bi bi-file-earmark-arrow-down"></i> <span>Export CSV</span>
            </a>
            <a class="nav-link active" href="#">
                <i class="bi bi-pencil-square"></i> <span>Update</span>
            </a>
            <a class="nav-link" href="logout.php">
                <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h3>Update Visitor</h3>
            <div class="user-info">
                <button id="theme-toggle" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5>Edit Visitor #<?php echo $id; ?></h5>
                <?php if ($err): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
                <?php endif; ?>
                <?php if ($ok): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($ok); ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Visitor Name <span class="text-danger">*</span></label>
                        <input name="visitor_name" class="form-control"
                            value="<?php echo htmlspecialchars($visitor['visitor_name']); ?>" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" name="visit_date" value="<?php echo $visitor['visit_date']; ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time</label>
                            <input type="time" name="visit_time"
                                value="<?php echo date('H:i', strtotime($visitor['visit_time'])); ?>"
                                class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact #</label>
                        <input name="contact" value="<?php echo htmlspecialchars($visitor['contact']); ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input name="address" value="<?php echo htmlspecialchars($visitor['address']); ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">School / Office</label>
                        <input name="school_office" value="<?php echo htmlspecialchars($visitor['school_office']); ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <select name="purpose" class="form-select">
                            <option <?php echo $visitor['purpose']=='Inquiry'?'selected':''; ?>>Inquiry</option>
                            <option <?php echo $visitor['purpose']=='Exam'?'selected':''; ?>>Exam</option>
                            <option <?php echo $visitor['purpose']=='Visit'?'selected':''; ?>>Visit</option>
                            <option <?php echo $visitor['purpose']=='Other'?'selected':''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Save Changes</button>
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