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
        --border-light: #dee2e6;
        --border-dark: #495057;
        --input-bg-dark: #343a40;
        --input-border-dark: #495057;
        --table-header-light: #f8f9fa;
        --table-header-dark: #343a40;
        --table-divider-light: #dee2e6;
        --table-divider-dark: #495057;
        --table-stripe-light: rgba(0, 0, 0, 0.03);
        --table-stripe-dark: rgba(255, 255, 255, 0.05);
        --table-hover-light: rgba(0, 0, 0, 0.075);
        --table-hover-dark: rgba(255, 255, 255, 0.1);
    }

    body {
        background: var(--bg-light);
        color: var(--text-light);
        transition: all .3s;
        font-family: system-ui, -apple-system, sans-serif;
    }

    .dark-mode {
        background: var(--bg-dark);
        color: var(--text-dark);
    }

    .sidebar {
        background: var(--sidebar-light);
        transition: background .3s;
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
    .table-card,
    .top-bar {
        background: var(--card-light);
        border: 1px solid var(--border-light);
        transition: all .3s;
    }

    .dark-mode .card,
    .dark-mode .table-card,
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
        color: #fff;
    }

    .table {
        --bs-table-bg: transparent;
        --bs-table-striped-bg: var(--table-stripe-light);
        --bs-table-hover-bg: var(--table-hover-light);
        color: var(--text-light) !important;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }

    .dark-mode .table {
        --bs-table-striped-bg: var(--table-stripe-dark);
        --bs-table-hover-bg: var(--table-hover-dark);
        color: var(--text-dark) !important;
    }

    .table thead th {
        background: var(--table-header-light);
        color: #495057 !important;
        font-weight: 600;
        font-size: .85rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        padding: .75rem;
        border-bottom: 2px solid var(--table-divider-light);
        border-right: 1px solid var(--table-divider-light);
    }

    .dark-mode .table thead th {
        background: var(--table-header-dark);
        color: #ced4da !important;
        border-bottom-color: var(--table-divider-dark);
        border-right-color: var(--table-divider-dark);
    }

    .table thead th:last-child {
        border-right: none;
    }

    .table tbody td {
        padding: .75rem;
        vertical-align: middle;
        border-top: 1px solid var(--table-divider-light);
        border-right: 1px solid var(--table-divider-light);
        font-size: .925rem;
        color: inherit !important;
    }

    .dark-mode .table tbody td {
        border-top-color: var(--table-divider-dark);
        border-right-color: var(--table-divider-dark);
        color: var(--text-dark) !important;
    }

    .table tbody td:last-child,
    .table tbody tr:last-child td {
        border-right: none;
    }

    .table tbody tr:last-child td {
        border-bottom: 1px solid var(--table-divider-light);
    }

    .dark-mode .table tbody tr:last-child td {
        border-bottom-color: var(--table-divider-dark);
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: var(--bs-table-striped-bg);
    }

    .table-hover tbody tr:hover {
        background-color: var(--bs-table-hover-bg);
        transition: background-color .2s;
    }

    .table-sm th,
    .table-sm td {
        padding: .5rem .75rem;
        font-size: .875rem;
    }

    .btn-update {
        background: #ffc107;
        color: #212529;
        border: none;
        font-size: .8125rem;
        padding: .35rem .65rem;
        border-radius: .375rem;
    }

    .btn-update:hover {
        background: #e0a800;
        color: #fff;
    }

    .dark-mode .btn-update {
        background: #ffb300;
        color: #000;
    }

    .dark-mode .btn-update:hover {
        background: #ff8c00;
        color: #fff;
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
        margin: 0;
        font-size: 1.25rem;
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
        font-size: 1rem;
    }

    .user-info h6 {
        margin: 0;
        font-size: .95rem;
    }

    .user-info small {
        opacity: .8;
        font-size: .8rem;
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
        text-decoration: none;
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

    .dark-mode .top-bar {
        background: var(--card-dark);
        border-color: var(--border-dark);
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

    .stat-cards {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .stat-card {
        flex: 1;
        min-width: 200px;
        padding: 1.5rem;
        border-radius: .75rem;
        color: #fff;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
    }

    .stat-card h3 {
        font-size: 2.5rem;
        margin: .5rem 0;
        font-weight: 700;
    }

    .stat-card p {
        margin: 0;
        font-size: 1rem;
        opacity: .9;
    }

    .stat-card .icon {
        font-size: 2rem;
        margin-bottom: .5rem;
        opacity: .9;
    }

    .table-card {
        background: var(--card-light);
        border-radius: .75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
    }

    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        align-items: center;
        margin-bottom: 1.25rem;
        justify-content: space-between;
    }

    .filter-group {
        display: flex;
        gap: .5rem;
        align-items: center;
    }

    .filter-group label {
        font-weight: 500;
        white-space: nowrap;
        margin: 0;
    }

    .filter-group input[type=date],
    .filter-group input[type=text],
    .filter-group .form-select {
        width: auto;
        min-width: 130px;
    }

    .entries-select {
        display: flex;
        align-items: center;
        gap: .5rem;
        white-space: nowrap;
    }

    .entries-select select {
        width: 80px;
    }

    .alert-floating {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .15);
        border: none;
        border-radius: .75rem;
        font-weight: 500;
    }

    @media (max-width:992px) {
        .sidebar {
            width: 80px;
            padding: 1rem 0;
        }

        .sidebar-header h4 span,
        .user-info,
        .nav-link span {
            display: none;
        }

        .main-content {
            margin-left: 80px;
        }

        .filter-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            width: 100%;
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
                <h6>Admin</h6><small><?php echo htmlspecialchars($_SESSION['user_name']); ?></small>
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

        <!-- ==================== STAT CARDS (WITH FILTER SUMMARY) ==================== -->
        <div class="stat-cards">

            <!-- TOTAL VISIBLE VISITORS + FILTER SUMMARY -->
            <div class="stat-card" style="background:linear-gradient(135deg,#17a2b8,#0d6efd);">
                <i class="bi bi-people-fill icon"></i>
                <h3><?php echo $stats['total']; ?></h3>
                <p>Total Visitors</p>
                <?php echo $filter_summary; ?>
            </div>

            <!-- EXAM -->
            <div class="stat-card" style="background:linear-gradient(135deg,#28a745,#20c997);">
                <i class="bi bi-journal-check icon"></i>
                <h3><?php echo $stats['exam_count']; ?></h3>
                <p>EXAM</p>
                <!--   <small class="d-block mt-1 opacity-75"><?php echo $stats['exam_count']; ?> visible</small>  -->
            </div>

            <!-- OTHER PURPOSES -->
            <div class="stat-card" style="background:linear-gradient(135deg,#ffc107,#fd7e14);">
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