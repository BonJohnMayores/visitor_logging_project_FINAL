<?php
require_once 'functions.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = intval($_POST['id'] ?? 0);
    if ($id && delete_visitor($id)){
        header('Location: dashboard.php');
        exit;
    } else {
        echo 'Failed to delete.';
    }
} else {
    header('Location: dashboard.php');
    exit;
}
?>