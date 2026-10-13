<?php
require 'config.php';
require_login();
$q = trim($_GET['q'] ?? '');
$dept = (int) ($_GET['dept'] ?? 0);
$status = $_GET['status'] ?? '';
$where = [];
$p = [];
if ($q !== '') {
    $where[] = '(e.emp_no LIKE ? OR e.first_name LIKE ? OR e.last_name LIKE ? OR e.email LIKE ? OR e.phone LIKE ?)';
    array_push($p, "%$q%", "%$q%", "%$q%", "%$q%", "%$q%");
}
if ($dept) {
    $where[] = 'e.department_id=?';
    $p[] = $dept;
}
if (in_array($status, ['Active', 'On Leave', 'Resigned'], true)) {
    $where[] = 'e.status=?';
    $p[] = $status;
}
$w = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$st = $pdo->prepare("SELECT e.*,d.department_name,p.position_title FROM employees e JOIN departments d ON d.department_id=e.department_id JOIN positions p ON p.position_id=e.position_id $w ORDER BY e.last_name");
$st->execute($p);
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=\"employees_' . date('Ymd_His') . '.csv\"');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");
$head = ['Employee ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Department', 'Position', 'Hire Date', 'Status'];
if (is_admin())
    $head[] = 'Monthly Salary';
fputcsv($out, $head);
while ($r = $st->fetch()) {
    $row = [$r['emp_no'], $r['first_name'], $r['last_name'], $r['email'], $r['phone'], $r['department_name'], $r['position_title'], $r['hire_date'], $r['status']];
    if (is_admin())
        $row[] = $r['salary'];
    fputcsv($out, $row);
}
fclose($out);
exit;
