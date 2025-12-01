<?php
require_once 'functions.php';
require_login();

$msg = $msg_type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    if (delete_visitor($id)) {
        $msg = 'Visitor deleted successfully.';
        $msg_type = 'success';
    } else {
        $msg = 'Failed to delete visitor.';
        $msg_type = 'danger';
    }
}

/* ==================== FETCH FILTERS ==================== */
$filters = [];
if (!empty($_GET['from'])) $filters['from'] = $_GET['from'];
if (!empty($_GET['to']))   $filters['to']   = $_GET['to'];
if (!empty($_GET['q']))    $filters['q']    = $_GET['q'];
if (!empty($_GET['limit'])) $filters['limit'] = (int)$_GET['limit'];

/* ==================== DATA ==================== */
$visitors = fetch_visitors($filters);

// ---- STATS: BASED ON VISIBLE ROWS (ROBUST) ----
$stats = [
    'total'         => count($visitors),
    'exam_count'    => 0,
    'visit_count'   => 0,
    'inquiry_count' => 0,
    'other_count'   => 0,
    'other_total'   => 0
];

foreach ($visitors as $v) {
    $purpose = trim(strtoupper($v['purpose'] ?? ''));
    if ($purpose === 'EXAM') {
        $stats['exam_count']++;
    } elseif ($purpose === 'VISIT') {
        $stats['visit_count']++;
    } elseif ($purpose === 'INQUIRY') {
        $stats['inquiry_count']++;
    } else {
        $stats['other_count']++;
    }
}
$stats['other_total'] = $stats['visit_count'] + $stats['inquiry_count'] + $stats['other_count'];

/* ==================== FILTER SUMMARY TEXT ==================== */
$filter_summary = '';

// Search term
if (!empty($filters['q'])) {
    $filter_summary .= htmlspecialchars($filters['q']);
}

// Date range
if (!empty($filters['from']) || !empty($filters['to'])) {
    if (!empty($filters['q'])) $filter_summary .= ' • ';
    if (!empty($filters['from'])) {
        $filter_summary .= 'From: ' . date('d M Y', strtotime($filters['from']));
    }
    if (!empty($filters['to'])) {
        if (!empty($filters['from'])) $filter_summary .= ' • ';
        $filter_summary .= 'To: ' . date('d M Y', strtotime($filters['to']));
    }
}

// Entries
if (!empty($filters['limit'])) {
    if (!empty($filter_summary)) $filter_summary .= ' • ';
    $filter_summary .= $stats['total'] . ' entries';
}

// If no filters, show nothing
if (empty($filter_summary)) {
    $filter_summary = '';
} else {
    $filter_summary = '<small class="d-block mt-2 opacity-75 text-white">' . $filter_summary . '</small>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Visitor Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- External CSS File -->
    <link rel="stylesheet" href="assets/dashboard.css" />
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
            <a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
            <a class="nav-link" href="add_visitor.php"><i class="bi bi-person-plus"></i><span>Add Visitor</span></a>
            <a class="nav-link" href="export.php"><i class="bi bi-file-earmark-arrow-down"></i><span>Export
                    CSV</span></a>
            <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <?php if ($msg): ?>
        <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show alert-floating" role="alert">
            <i class="bi <?php echo $msg_type === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?> me-2"></i>
            <?php echo htmlspecialchars($msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="top-bar">
            <h3>Visitor Log</h3>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <button id="theme-toggle" class="btn btn-outline-secondary btn-sm"><i
                        class="bi bi-moon-stars-fill"></i></button>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>

        <!-- ==================== STAT CARDS ==================== -->
        <div class="stat-cards">

            <!-- TOTAL VISIBLE VISITORS -->
            <div class="stat-card" style="background:linear-gradient(135deg,#17a2b8,#0d6efd);">
                <i class="bi bi-people-fill icon-bg"></i>
                <i class="bi bi-people-fill icon"></i>
                <h3><?php echo $stats['total']; ?></h3>
                <p>Total Visitors</p>
                <?php echo $filter_summary; ?>
            </div>

            <!-- EXAM -->
            <div class="stat-card" style="background:linear-gradient(135deg,#28a745,#20c997);">
                <i class="bi bi-journal-check icon-bg"></i>
                <i class="bi bi-journal-check icon"></i>
                <h3><?php echo $stats['exam_count']; ?></h3>
                <p>EXAM</p>
            </div>

            <!-- OTHER PURPOSES -->
            <div class="stat-card" style="background:linear-gradient(135deg,#ffc107,#fd7e14);">
                <i class="bi bi-chat-dots icon-bg"></i>
                <i class="bi bi-chat-dots icon"></i>
                <h3><?php echo $stats['other_total']; ?></h3>
                <p>Other Purposes</p>
                <small class="d-block mt-1 opacity-75">
                    Visit: <?php echo $stats['visit_count']; ?> •
                    Inquiry: <?php echo $stats['inquiry_count']; ?> •
                    Other: <?php echo $stats['other_count']; ?>
                </small>
            </div>

        </div>

        <!-- ==================== TABLE + FILTER ==================== -->
        <div class="table-card">
            <form class="filter-bar" method="get" id="filterForm">
                <div class="entries-select">
                    <label>Show</label>
                    <select class="form-select" name="limit" onchange="this.form.submit()">
                        <option value="5" <?php echo ($_GET['limit'] ?? '') == '5'  ? 'selected' : ''; ?>>5</option>
                        <option value="10" <?php echo ($_GET['limit'] ?? '10') == '10' ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?php echo ($_GET['limit'] ?? '') == '25' ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo ($_GET['limit'] ?? '') == '50' ? 'selected' : ''; ?>>50</option>
                    </select>
                    <span>Entries</span>
                </div>

                <div class="filter-group">
                    <label>From</label>
                    <input type="date" name="from" value="<?php echo htmlspecialchars($_GET['from'] ?? ''); ?>"
                        class="form-control" onchange="this.form.submit()">
                    <label>To</label>
                    <input type="date" name="to" value="<?php echo htmlspecialchars($_GET['to'] ?? ''); ?>"
                        class="form-control" onchange="this.form.submit()">
                    <input type="text" name="q" placeholder="Search..."
                        value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" class="form-control">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="dashboard.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>School/Office</th>
                            <th>Purpose</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($visitors as $v): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($v['visit_date'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($v['visit_time'])); ?></td>
                            <td><?php echo htmlspecialchars($v['visitor_name']); ?></td>
                            <td><?php echo htmlspecialchars($v['contact']); ?></td>
                            <td><?php echo htmlspecialchars($v['school_office']); ?></td>
                            <td><?php echo htmlspecialchars($v['purpose']); ?></td>
                            <td>
                                <a href="update_visitor.php?id=<?php echo $v['id']; ?>"
                                    class="btn btn-update btn-sm me-1">Update</a>
                                <form method="post" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                    <input type="hidden" name="delete_id" value="<?php echo $v['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($visitors)): ?>
                        <tr>
                            <td colspan="7" class="text-muted text-center py-3">No records found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const toggle = document.getElementById('theme-toggle');
    const body = document.body;
    const icon = toggle.querySelector('i');

    if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia(
            '(prefers-color-scheme: dark)').matches)) {
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

    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => new bootstrap.Alert(alert).close(), 4000);
    });
    </script>
</body>

</html>