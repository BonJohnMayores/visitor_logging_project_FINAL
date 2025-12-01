<?php
require_once 'config.php';
session_start();

/* ==============================================================
   AUTHENTICATION
   ============================================================== */
function is_logged_in(){
    return isset($_SESSION['user_id']);
}

function require_login(){
    if (!is_logged_in()){
        header('Location: index.php');
        exit;
    }
}

function get_user_by_email($email){
    $db = db_connect();
    $stmt = $db->prepare('SELECT id, fullname, email, password_hash FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();
    return $user;
}

function register_user($fullname, $email, $password){
    // 1. Check if user already exists
    if (get_user_by_email($email)) {
        // You might return false, throw an exception, or return a specific error code
        return false; // User already exists, don't proceed
    }
    
    // 2. Proceed with registration if email is unique
    $db = db_connect();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    // Original INSERT query
    $stmt = $db->prepare('INSERT INTO users (fullname, email, password_hash) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $fullname, $email, $hash);
    
    // Line 35 is where the execute happens (this is where the error occurred before the fix)
    $ok = $stmt->execute(); 
    $stmt->close();
    return $ok;
}

function login_user($email, $password){
    $user = get_user_by_email($email);
    if ($user && password_verify($password, $user['password_hash'])){
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['fullname'];
        return true;
    }
    return false;
}

/* ==============================================================
   VISITORS – CRUD
   ============================================================== */
function add_visitor($data){
    $db = db_connect();
    $stmt = $db->prepare('INSERT INTO visitors (visitor_name, visit_date, visit_time, address, contact, school_office, purpose, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssssssi',
        $data['visitor_name'],
        $data['visit_date'],
        $data['visit_time'],
        $data['address'],
        $data['contact'],
        $data['school_office'],
        $data['purpose'],
        $data['created_by']
    );
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function delete_visitor($id){
    $db = db_connect();
    $stmt = $db->prepare('DELETE FROM visitors WHERE id = ?');
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function get_visitor_by_id($id){
    $db = db_connect();
    $stmt = $db->prepare('SELECT * FROM visitors WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $visitor = $res->fetch_assoc();
    $stmt->close();
    return $visitor;
}

function update_visitor($data){
    $db = db_connect();
    $stmt = $db->prepare('UPDATE visitors SET 
        visitor_name = ?, visit_date = ?, visit_time = ?, address = ?, contact = ?, school_office = ?, purpose = ?
        WHERE id = ?');
    $stmt->bind_param('sssssssi',
        $data['visitor_name'],
        $data['visit_date'],
        $data['visit_time'],
        $data['address'],
        $data['contact'],
        $data['school_office'],
        $data['purpose'],
        $data['id']
    );
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

/* ==============================================================
   FETCH VISITORS (with filters + limit)
   ============================================================== */
function fetch_visitors($filters = []){
    $db = db_connect();
    $sql = 'SELECT v.*, u.fullname as created_by_name FROM visitors v LEFT JOIN users u ON v.created_by = u.id';
    $conds = [];
    $params = [];
    $types = '';

    if (!empty($filters['from'])) {
        $conds[] = 'v.visit_date >= ?';
        $params[] = $filters['from'];
        $types .= 's';
    }
    if (!empty($filters['to'])) {
        $conds[] = 'v.visit_date <= ?';
        $params[] = $filters['to'];
        $types .= 's';
    }

    if (!empty($filters['q'])) {
        $like = '%' . $filters['q'] . '%';
        $conds[] = '(v.visitor_name LIKE ? OR v.contact LIKE ? OR v.school_office LIKE ? OR v.purpose LIKE ?)';
        $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
        $types .= 'ssss';
    }

    if ($conds) {
        $sql .= ' WHERE ' . implode(' AND ', $conds);
    }

    $sql .= ' ORDER BY v.visit_date DESC, v.visit_time DESC';

    if (!empty($filters['limit'])) {
        $sql .= ' LIMIT ?';
        $params[] = (int)$filters['limit'];
        $types .= 'i';
    }

    $stmt = $db->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}
?>