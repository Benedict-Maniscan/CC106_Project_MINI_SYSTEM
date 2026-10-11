<?php
require 'config.php';
$accounts = [
 ['admin','System Administrator','admin@example.com','admin123','Admin'],
 ['user','Regular User','user@example.com','user123','User']
];
$sql = "INSERT IGNORE INTO users (username,full_name,email,password_hash,role) VALUES (?,?,?,?,?)";
$st = $pdo->prepare($sql);
foreach ($accounts as $a) $st->execute([$a[0],$a[1],$a[2],password_hash($a[3],PASSWORD_DEFAULT),$a[4]]);
echo 'Initial accounts created if they did not already exist. Admin: admin / admin123. User: user / user123. Delete install.php now and change these passwords.';
