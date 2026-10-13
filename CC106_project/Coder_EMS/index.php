<?php
require 'config.php';
require_login();
$page_title = 'Dashboard';
$stats = $pdo->query("SELECT COUNT(*) total, COALESCE(SUM(status='Active'),0) active, COALESCE(SUM(status='On Leave'),0) onleave, COALESCE(SUM(status='Resigned'),0) resigned, COALESCE(SUM(salary),0) payroll FROM employees")->fetch();
$dept = $pdo->query("SELECT d.department_name,COUNT(e.employee_id) headcount FROM departments d LEFT JOIN employees e ON e.department_id=d.department_id GROUP BY d.department_id,d.department_name ORDER BY headcount DESC")->fetchAll();
$recent = $pdo->query("SELECT e.employee_id,e.emp_no,e.first_name,e.last_name,e.email,e.hire_date,e.status,d.department_name,p.position_title FROM employees e JOIN departments d ON d.department_id=e.department_id JOIN positions p ON p.position_id=e.position_id ORDER BY e.created_at DESC,e.employee_id DESC LIMIT 6")->fetchAll();
$projects = (int) $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn();
include 'includes/header.php'; ?>
<section class="hero">
    <div>
        <p class="eyebrow">PEOPLE · PERFORMANCE · PROGRESS</p>
        <h1>Good <?= date('H') < 12 ? 'morning' : (date('H') < 18 ? 'afternoon' : 'evening') ?>,
            <?= e(explode(' ', $_SESSION['full_name'] ?? 'there')[0]) ?>.</h1>
        <p class="muted">Here is what's happening across your workforce today.</p>
    </div><?php if (is_admin()): ?><a class="btn primary" href="employee_form.php">＋ Add employee</a><?php endif; ?>
</section>
<div class="stats">
    <article class="stat">
        <div class="stat-top"><span>Total employees</span><span class="stat-icon">♙</span></div>
        <strong><?= number_format($stats['total']) ?></strong><small>Employee records</small>
    </article>
    <article class="stat">
        <div class="stat-top"><span>Active employees</span><span class="stat-icon">✓</span></div>
        <strong><?= number_format($stats['active']) ?></strong><small>Currently active</small>
    </article>
    <article class="stat">
        <div class="stat-top"><span>On leave</span><span class="stat-icon">◷</span></div>
        <strong><?= number_format($stats['onleave']) ?></strong><small>Temporarily away</small>
    </article>
    <article class="stat">
        <div class="stat-top"><span>Projects</span><span class="stat-icon">▤</span></div>
        <strong><?= number_format($projects) ?></strong><small>Registered projects</small>
    </article>
</div>
<div class="report-grid">
    <section class="panel">
        <div class="panel-head">
            <h2>Employees by department</h2><a href="departments.php">View all →</a>
        </div>
        <div class="chart-bars"><?php $max = max(1, ...array_column($dept, 'headcount'));
        foreach ($dept as $d): ?>
                <div class="bar-row"><span><?= e($d['department_name']) ?></span>
                    <div class="bar-track">
                        <div class="bar-fill" style="width:<?= round($d['headcount'] / $max * 100) ?>%"></div>
                    </div><b><?= $d['headcount'] ?></b>
                </div><?php endforeach;
        if (!$dept): ?>
                <p class="muted">No departments available.</p><?php endif; ?>
        </div>
    </section>
    <section class="panel">
        <div class="panel-head">
            <h2>Quick actions</h2>
        </div>
        <div class="stack"><a class="btn outline" href="employees.php">♙ &nbsp; Browse employee directory</a><a
                class="btn outline" href="projects.php">▤ &nbsp; Manage projects</a><a class="btn outline"
                href="reports.php">▥ &nbsp; View workforce reports</a><?php if (is_admin()): ?><a class="btn outline"
                    href="users.php">♧ &nbsp; Manage user accounts</a><?php endif; ?></div>
        <p class="muted" style="font-size:12px;margin:18px 0 0">Resigned employees:
            <b><?= number_format($stats['resigned']) ?></b><?php if (is_admin()): ?> · Monthly salary total:
                <b>₱<?= number_format((float) $stats['payroll'], 2) ?></b><?php endif; ?></p>
    </section>
</div>
<section class="panel">
    <div class="panel-head">
        <h2>Recently added employees</h2><a href="employees.php">Open directory →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Hire date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody><?php if (!$recent): ?>
                    <tr>
                        <td colspan="5" class="empty">No employees found. <?php if (is_admin()): ?><a
                                    href="employee_form.php">Add your first employee</a><?php endif; ?></td>
                    </tr><?php endif;
            foreach ($recent as $r): ?>
                    <tr>
                        <td><b><?= e($r['first_name'] . ' ' . $r['last_name']) ?></b><small><?= e($r['emp_no']) ?> ·
                                <?= e($r['email']) ?></small></td>
                        <td><?= e($r['department_name']) ?></td>
                        <td><?= e($r['position_title']) ?></td>
                        <td><?= e($r['hire_date']) ?></td>
                        <td><span
                                class="badge <?= strtolower(str_replace(' ', '', $r['status'])) ?>"><?= e($r['status']) ?></span>
                        </td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include 'includes/footer.php'; ?>