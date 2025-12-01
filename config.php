<?php
// config.php - update DB credentials if necessary
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '1234';
$DB_NAME = 'visitor_db';

function db_connect(){
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($mysqli->connect_errno) {
        die('DB connect error: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}
?>