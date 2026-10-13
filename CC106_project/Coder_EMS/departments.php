<?php
require 'config.php';
require_login();
$page_title = 'Departments';
$rows = $pdo->query('SELECT d.department_id,d.department_name,d.location,COUNT(e.employee_id) headcount FROM departments d LEFT JOIN employees e ON e.department_id=d.department_id GROUP BY d.department_id,d.department_name,d.location ORDER BY d.department_name')->fetchAll();
include 'includes/header.php'; ?>
<section class="hero">
    <div>
        <p class="eyebrow">ORGANIZATION STRUCTURE</p>
        <h1>Departments</h1>
        <p class="muted">View department locations and current headcount.</p>
    </div><button class="btn outline print-trigger" onclick="window.print()">Print directory</button>
</section>
<section class="panel">
    <div class="panel-head">
        <h2>Department directory</h2><span class="muted"><?= count($rows) ?> department(s)</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Location</th>
                    <th>Employees</th>
                </tr>
            </thead>
            <tbody><?php foreach ($rows as $r): ?>
                    <tr>
                        <td><b><?= e($r['department_name']) ?></b></td>
                        <td><?= e($r['location']) ?></td>
                        <td><?= number_format($r['headcount']) ?></td>
                    </tr><?php endforeach;
            if (!$rows): ?>
                    <tr>
                        <td colspan="3" class="empty">No departments found.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <p class="muted" style="font-size:12px">Department creation and location editing are not included because the
        existing database setup does not provide a dedicated department-management screen.</p>
</section><?php include 'includes/footer.php'; ?>