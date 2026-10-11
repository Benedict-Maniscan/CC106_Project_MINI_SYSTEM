<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>EMS</title> 
    <link rel="stylesheet" href="assets/style.css">

</head>
<body>
<nav class="topbar">
    <a class="brand" href="index.php">
        <span class="crest">M</span>
         Maison 
         <small>EMS</small>
        </a>
        
        <div class="nav-right">
            <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="index.php">Dashboard</a>
                <?php if (is_admin()): ?>
                    <a href="employee_form.php">Add Employee</a>
                    <a href="users.php">Users</a>
                    <?php endif; ?>
                    <span><?=e($_SESSION['username'])?> · <?=e($_SESSION['role'])?>
                
                </span>
                <a class="btn outline" href="logout.php">Sign out</a>
                <?php endif; ?>
        </div>
    </nav>
    
    <main class="container">
<?php show_flash(); ?>