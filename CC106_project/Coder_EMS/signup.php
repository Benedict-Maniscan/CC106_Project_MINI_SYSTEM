<?php
require 'config.php';
if (!empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$error = ''; $success = '';
function pending_file(): string { return __DIR__ . '/data/pending_users.json'; }
function read_pending_locked($fp): array { rewind($fp); $raw = stream_get_contents($fp); $data = json_decode($raw ?: '[]', true); return is_array($data) ? $data : []; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username)) $error = 'Username must be 3–50 characters and may contain letters, numbers, dots, underscores, and hyphens.';
    elseif (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 100) $error = 'Enter your full name (2–100 characters).';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) $error = 'Enter a valid email address.';
    elseif (strlen($password) < 8) $error = 'Password must be at least 8 characters.';
    elseif ($password !== $confirm) $error = 'Passwords do not match.';
    else {
        $check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
        $check->execute([$username, $email]);
        if ((int)$check->fetchColumn() > 0) $error = 'That username or email is already registered.';
        else {
            $fp = @fopen(pending_file(), 'c+');
            if (!$fp) $error = 'Registration is temporarily unavailable. Please contact the administrator.';
            else {
                flock($fp, LOCK_EX);
                $pending = read_pending_locked($fp);
                $duplicate = false;
                foreach ($pending as $item) {
                    if (strcasecmp($item['username'] ?? '', $username) === 0 || strcasecmp($item['email'] ?? '', $email) === 0) { $duplicate = true; break; }
                }
                if ($duplicate) $error = 'That username or email already has a pending request.';
                else {
                    $pending[] = ['request_id' => bin2hex(random_bytes(12)), 'username' => $username, 'full_name' => $fullName, 'email' => $email, 'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'requested_at' => date('Y-m-d H:i:s')];
                    rewind($fp); ftruncate($fp, 0); fwrite($fp, json_encode($pending, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); fflush($fp);
                    $success = 'Your account request was submitted. You can sign in after an administrator approves it.';
                }
                flock($fp, LOCK_UN); fclose($fp);
            }
        }
    }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sign up · Coder EMS</title><link rel="stylesheet" href="assets/style.css"></head><body class="login-body"><main class="login-card"><div class="brand-mark">C</div><p class="eyebrow">JOIN CODER EMS</p><h1>Create your account</h1><p class="muted">Submit your details. An administrator must approve your request before you can sign in.</p><?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?><?php if($success): ?><div class="alert"><?=e($success)?></div><?php else: ?><form method="post" class="stack"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>Full name<input name="full_name" maxlength="100" autocomplete="name" required value="<?=e($_POST['full_name']??'')?>"></label><label>Username<input name="username" minlength="3" maxlength="50" pattern="[A-Za-z0-9_.-]{3,50}" autocomplete="username" required value="<?=e($_POST['username']??'')?>"></label><label>Email address<input name="email" type="email" maxlength="120" autocomplete="email" required value="<?=e($_POST['email']??'')?>"></label><label>Password<input name="password" type="password" minlength="8" autocomplete="new-password" required></label><label>Confirm password<input name="confirm_password" type="password" minlength="8" autocomplete="new-password" required></label><button class="btn primary wide">Submit registration</button></form><?php endif; ?><p class="small muted"><a href="login.php">← Back to sign in</a></p></main></body></html>
