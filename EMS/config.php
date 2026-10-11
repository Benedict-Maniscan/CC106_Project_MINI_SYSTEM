<?php

session_start();

$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'ems_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed. Import database.sql and update the DB credentials in your hosting setup.');
}

function e($v): string {
    return htmlspecialchars((string)
    $v, ENT_QUOTES, 'UTF-8'); 
}

function is_admin(): bool { 
    return ($_SESSION['role'] ?? '') === 'Admin'; 
}

function require_login(): void { 
    if (empty($_SESSION['user_id'])) { 
        header('Location: login.php'); 
        exit; 
    } 
}

function require_admin(): void { 
    require_login(); if (!is_admin()) { 
        http_response_code(403); 
        exit('Access denied: Admin only.'); 
    }
}

function csrf_token(): string {
     if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); 
     return $_SESSION['csrf'];
}

function csrf_check(): void { 
    if (!hash_equals($_SESSION['csrf'] ?? '', 
    $_POST['csrf'] ?? '')) {
         http_response_code(400);
        exit('Invalid request token. Refresh and try again.');
    } 
}

function flash(string $message): void { 
    $_SESSION['flash'] = $message; 
}

function show_flash(): void {
     if (!empty($_SESSION['flash'])) {
         echo '<div class="alert">'.e($_SESSION['flash']).'</div>';
        unset($_SESSION['flash']); 
    } 
}
