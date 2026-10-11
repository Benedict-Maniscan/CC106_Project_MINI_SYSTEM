<?php
require 'config.php'; 
if (!empty($_SESSION['user_id'])) { 
    header('Location: index.php'); 
    exit; 
}

$errors=[]; $name='';$username='';$email='';

if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    $name=trim($_POST['full_name']??'');
    $username=trim($_POST['username']??'');
    $email=trim($_POST['email']??'');
    $pw=$_POST['password']??'';
    $confirm=$_POST['confirm']??'';
    
if(!preg_match("/^[A-Za-z][A-Za-z .'-]{1,98}$/",$name))
    $errors[]='Enter a valid full name.';

if(!preg_match('/^[A-Za-z0-9_]{4,20}$/',
$username))$errors[]='Username must be 4–20 letters, numbers or underscores.';

if(!filter_var(
    $email,FILTER_VALIDATE_EMAIL))
    $errors[]='Enter a valid email.';
    
if(strlen($pw)<8||!preg_match('/[a-z]/',$pw)||!preg_match('/[A-Z]/',$pw)||!preg_match('/\d/',$pw))$errors[]='Password must be at least 8 characters with uppercase, lowercase, and a number.';
if($pw!==$confirm)$errors[]='Passwords do not match.';
if(!$errors){$q=$pdo->prepare('SELECT COUNT(*) FROM users WHERE username=? OR email=?');$q->execute([$username,$email]);if($q->fetchColumn())$errors[]='Username or email already exists.';}
if(!$errors){$q=$pdo->prepare("INSERT INTO users(username,full_name,email,password_hash,role) VALUES(?,?,?,?, 'User')");$q->execute([$username,$name,$email,password_hash($pw,PASSWORD_DEFAULT)]);header('Location: login.php?registered=1');exit;}}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Sign up — Coders EMS</title>
        <link rel="stylesheet" href="assets/style.css">
    </head>
    <body class="auth-page">
        <section class="auth-card">
            <h1>Create Account</h1>
            <p class="muted">Coders EMS</p>
            <?php if($errors): ?>
                <div class="alert error">
                    <ul>
                        <?php foreach($errors as $er): ?>
                            <li><?=e($er)?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
                <label>Full name</label>
                <input name="full_name" value="<?=e($name)?>" required>
                <label>Username</label>
                <input name="username" value="<?=e($username)?>" required>
                <label>Email</label>
                <input type="email" name="email" value="<?=e($email)?>" required>
                <label>Password</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" required>
                    <button type="button" class="toggle-password" data-target="password" aria-label="Show password">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 5c5.1 0 9.4 3.3 11 7-1.6 3.7-5.9 7-11 7S2.6 15.7 1 12c1.6-3.7 5.9-7 11-7zm0 2.2A8.1 8.1 0 0 0 4.8 12 8.1 8.1 0 0 0 12 16.8 8.1 8.1 0 0 0 19.2 12 8.1 8.1 0 0 0 12 7.2zm0 2.1a2.7 2.7 0 1 1 0 5.4 2.7 2.7 0 0 1 0-5.4z"/>
                        </svg>
                    </button>
                </div>
                <label>Confirm password</label>
                <div class="password-field">
                    <input type="password" id="confirm" name="confirm" required>
                    <button type="button" class="toggle-password" data-target="confirm" aria-label="Show password">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 5c5.1 0 9.4 3.3 11 7-1.6 3.7-5.9 7-11 7S2.6 15.7 1 12c1.6-3.7 5.9-7 11-7zm0 2.2A8.1 8.1 0 0 0 4.8 12 8.1 8.1 0 0 0 12 16.8 8.1 8.1 0 0 0 19.2 12 8.1 8.1 0 0 0 12 7.2zm0 2.1a2.7 2.7 0 1 1 0 5.4 2.7 2.7 0 0 1 0-5.4z"/>
                        </svg>
                    </button>
                </div>
                <button class="btn gold full">Sign up</button>
            </form>
            <p class="muted">Already registered? <a href="login.php">Sign in</a></p>
        </section>
        <script>
            document.querySelectorAll('.toggle-password').forEach(function (button) {
                button.addEventListener('click', function () {
                    const target = this.closest('.password-field')?.querySelector('input');
                    if (!target) return;

                    const isPassword = target.type === 'password';
                    target.type = isPassword ? 'text' : 'password';
                    this.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                    this.setAttribute('aria-pressed', String(isPassword));
                    this.classList.toggle('is-visible', isPassword);
                });
            });
        </script>
    </body>
</html>
