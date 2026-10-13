<?php require_login(); $current=basename($_SERVER['PHP_SELF']); ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="color-scheme" content="light"><title><?=e($page_title??'Dashboard')?> · Coder EMS</title><link rel="stylesheet" href="assets/style.css"></head><body>
<div class="app-shell">
<aside class="sidebar" id="sidebar"><a class="brand" href="index.php"><span class="brand-mark small-mark">C</span><span><b>CODER</b><small>EMPLOYEE MANAGEMENT</small></span></a><div class="nav-label">WORKSPACE</div><nav>
<a class="<?=active_page('index.php')?>" href="index.php"><span>▦</span> Dashboard</a>
<a class="<?=active_page('employees.php')||active_page('employee_form.php')?'active':''?>" href="employees.php"><span>♙</span> Employees</a>
<a class="<?=active_page('projects.php')?>" href="projects.php"><span>▤</span> Projects</a>
<a class="<?=active_page('departments.php')?>" href="departments.php"><span>⌂</span> Departments</a>
<a class="<?=active_page('positions.php')?>" href="positions.php"><span>◇</span> Positions</a>
<a class="<?=active_page('reports.php')?>" href="reports.php"><span>▥</span> Reports & analytics</a>
<?php if(is_admin()): ?><div class="nav-label">ADMINISTRATION</div><a class="<?=active_page('users.php')?>" href="users.php"><span>♧</span> User accounts & approvals</a><?php endif; ?>
</nav><div class="sidebar-bottom"><div class="user-chip"><div class="avatar"><?=e(strtoupper(substr($_SESSION['full_name']??'U',0,1)))?></div><div class="user-meta"><b><?=e($_SESSION['full_name']??'User')?></b><small><?=e($_SESSION['role']??'User')?></small></div></div><a class="logout-link" href="logout.php">↪ Sign out</a></div></aside>
<div class="main-area"><header class="topbar"><button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation">☰</button><div><span class="top-kicker">CODER EMS</span><b><?=e($page_title??'Dashboard')?></b></div><div class="top-user"><span class="status-dot"></span><?=e($_SESSION['full_name']??'User')?></div></header><main class="content"><div class="print-masthead"><div class="print-brand">CODER <span>EMPLOYEE MANAGEMENT SYSTEM</span></div><div class="print-document-title"><?=e($page_title??'Report')?></div><div class="print-meta">Generated on <?=e(date('F j, Y \a\t g:i A'))?></div></div><?php show_flash(); ?>
