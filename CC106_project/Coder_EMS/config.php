<?php
declare(strict_types=1);
session_start();
$host = 'localhost';
$db = 'ems_db';
$user = 'root';
$pass = '';
try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  exit('Database connection failed. Start MySQL in Laragon and import your existing database.sql into ems_db.');
}
function e($v): string
{
  return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
function is_admin(): bool
{
  return ($_SESSION['role'] ?? '') === 'Admin';
}
function require_login(): void
{
  if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
  }
}
function require_admin(): void
{
  require_login();
  if (!is_admin()) {
    http_response_code(403);
    exit('Access denied: administrator permission required.');
  }
}
function csrf_token(): string
{
  if (empty($_SESSION['csrf']))
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  return $_SESSION['csrf'];
}
function csrf_check(): void
{
  if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    http_response_code(400);
    exit('Invalid request token. Refresh the page and try again.');
  }
}
function flash(string $message, string $type = 'success'): void
{
  $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}
function show_flash(): void
{
  if (!empty($_SESSION['flash'])) {
    $f = $_SESSION['flash'];
    echo '<div class="alert ' . e($f['type'] ?? 'success') . '">' . e($f['message'] ?? '') . '</div>';
    unset($_SESSION['flash']);
  }
}
function active_page(string $page): string
{
  return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}
