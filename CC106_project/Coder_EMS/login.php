<?php
require 'config.php';
if (!empty($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  $st = $pdo->prepare('SELECT user_id,username,full_name,password_hash,role FROM users WHERE username=? LIMIT 1');
  $st->execute([$username]);
  $u = $st->fetch();
  if ($u && password_verify($password, $u['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $u['user_id'];
    $_SESSION['username'] = $u['username'];
    $_SESSION['full_name'] = $u['full_name'];
    $_SESSION['role'] = $u['role'];
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
    header('Location: index.php');
    exit;
  }
  $error = 'Incorrect username or password. If you recently signed up, wait for administrator approval.';
}
?><!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sign in · Coder EMS</title>
  <link rel="stylesheet" href="assets/style.css">
</head>

<body class="login-body">
  <main class="login-card">
    <div class="brand-mark">C</div>
    <p class="eyebrow">PEOPLE · PERFORMANCE · PROGRESS</p>
    <h1>Welcome back</h1>
    <p class="muted">Sign in to Coder Employee Management System.</p><?php if ($error): ?>
      <div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="stack"><input type="hidden" name="csrf"
        value="<?= e(csrf_token()) ?>"><label>Username<input name="username" autocomplete="username" required
          autofocus></label><label>Password<input type="password" name="password" autocomplete="current-password"
          required></label><button class="btn primary wide">Sign in</button></form>
    <p class="small muted">New to Coder EMS? <a href="signup.php"><b>Create an account</b></a></p>
  </main>
</body>

</html>