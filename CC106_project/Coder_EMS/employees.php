<?php
require 'config.php';
require_login();
$page_title = 'Employees';
$q = trim($_GET['q'] ?? '');
$dept = (int) ($_GET['dept'] ?? 0);
$status = $_GET['status'] ?? '';
$allowed = ['Active', 'On Leave', 'Resigned'];
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(e.emp_no LIKE ? OR e.first_name LIKE ? OR e.last_name LIKE ? OR e.email LIKE ? OR e.phone LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%", "%$q%");
}
if ($dept) {
    $where[] = 'e.department_id=?';
    $params[] = $dept;
}
if (in_array($status, $allowed, true)) {
    $where[] = 'e.status=?';
    $params[] = $status;
}
$w = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$c = $pdo->prepare("SELECT COUNT(*) FROM employees e $w");
$c->execute($params);
$total = (int) $c->fetchColumn();
$page = max(1, (int) ($_GET['page'] ?? 1));
$per = 10;
$pages = max(1, (int) ceil($total / $per));
$page = min($page, $pages);
$offset = ($page - 1) * $per;
$st = $pdo->prepare("SELECT e.*,d.department_name,p.position_title,(SELECT COUNT(*) FROM employee_projects ep WHERE ep.employee_id=e.employee_id) project_count FROM employees e JOIN departments d ON d.department_id=e.department_id JOIN positions p ON p.position_id=e.position_id $w ORDER BY e.last_name,e.first_name LIMIT $per OFFSET $offset");
$st->execute($params);
$rows = $st->fetchAll();
$depts = $pdo->query('SELECT department_id,department_name FROM departments ORDER BY department_name')->fetchAll();
include 'includes/header.php'; ?>
<section class="hero">
    <div>
        <p class="eyebrow">PEOPLE DIRECTORY</p>
        <h1>Employees</h1>
        <p class="muted">Search, filter, and review employee information in one place.</p>
    </div><?php if (is_admin()): ?><a class="btn primary" href="employee_form.php">＋ Add employee</a><?php endif; ?>
</section>
<section class="panel">
    <div class="panel-head">
        <h2>Employee directory <span class="muted">(<?= number_format($total) ?>)</span></h2>
        <div class="actions"><a class="btn outline" href="export.php?<?= e(http_build_query($_GET)) ?>">Export
                CSV</a><button class="btn outline print-trigger" onclick="window.print()">Print directory</button></div>
    </div>
    <form class="filters" method="get"><input name="q" placeholder="Search name, ID, email, phone…"
            value="<?= e($q) ?>"><select name="dept">
            <option value="0">All departments</option><?php foreach ($depts as $d): ?>
                <option value="<?= $d['department_id'] ?>" <?= $dept === $d['department_id'] ? 'selected' : '' ?>>
                    <?= e($d['department_name']) ?></option><?php endforeach; ?>
        </select><select name="status">
            <option value="">All statuses</option><?php foreach ($allowed as $s): ?>
                <option <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?>
        </select><button class="btn primary">Apply filters</button><a class="btn outline" href="employees.php">Reset</a>
    </form>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Hire date</th><?php if (is_admin()): ?>
                        <th>Monthly salary</th><?php endif; ?>
                    <th>Projects</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody><?php if (!$rows): ?>
                    <tr>
                        <td colspan="<?= is_admin() ? 7 : 6 ?>" class="empty">No employee records match your search.</td>
                    </tr><?php endif;
            foreach ($rows as $r): ?>
                    <tr>
                        <td><b><?= e($r['first_name'] . ' ' . $r['last_name']) ?></b><small><?= e($r['emp_no']) ?> ·
                                <?= e($r['email']) ?></small></td>
                        <td><?= e($r['department_name']) ?></td>
                        <td><?= e($r['position_title']) ?></td>
                        <td><?= e($r['hire_date']) ?></td><?php if (is_admin()): ?>
                            <td>₱<?= number_format((float) $r['salary'], 2) ?></td><?php endif; ?>
                        <td><?= $r['project_count'] ?></td>
                        <td><span
                                class="badge <?= strtolower(str_replace(' ', '', $r['status'])) ?>"><?= e($r['status']) ?></span>
                        </td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="pager"><span><?= number_format($total) ?> record(s) · Page <?= $page ?> of <?= $pages ?></span>
        <div><?php for ($i = 1; $i <= $pages; $i++):
            $pp = $_GET;
            $pp['page'] = $i; ?><a class="<?= $i === $page ? 'current' : '' ?>"
                    href="?<?= e(http_build_query($pp)) ?>"><?= $i ?></a><?php endfor; ?></div>
    </div>
</section><?php include 'includes/footer.php'; ?>