<?php
// ================================================================
// CONFIG - database connection, session setup and application settings
// Pattern follows the reference Library Management System.
// ================================================================
/* ================= Database settings ================= */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'student_management');
/* ================= Application settings ================= */
define('APP_NAME', 'EduManage');
define('SESSION_TIMEOUT', 1800);
// 30 minutes
/* ================= Hardened session ================= */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}
/* ================= MySQL connection ================= */
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die(
    'Database connection failed. Did you import database.sql? Details: '
    . mysqli_connect_error()
    );
}
mysqli_set_charset($conn, 'utf8mb4');
// Existing model/controller API.
function db()
{
    global $conn;
    return $conn;
}
