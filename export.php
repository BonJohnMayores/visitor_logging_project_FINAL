<?php
require_once 'functions.php';
require_login();
$today = date('Y-m-d');
$rows = fetch_visitors(['date' => $today]);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=visitors_' . $today . '.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID','Name','Date','Time','Contact','Address','School/Office','Purpose','Created By','Created At']);
foreach($rows as $r){
    fputcsv($out, [
        $r['id'],
        $r['visitor_name'],
        $r['visit_date'],
        $r['visit_time'],
        $r['contact'],
        $r['address'],
        $r['school_office'],
        $r['purpose'],
        $r['created_by_name'] ?? '',
        $r['created_at']
    ]);
}
fclose($out);
exit;
?>