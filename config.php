<?php
// Sesuaikan jika username/password MySQL kamu berbeda.
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_manajemen_gudang";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi database gagal: " . htmlspecialchars($conn->connect_error));
}
$conn->set_charset("utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
function csrf_check() {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $token = $_POST["csrf"] ?? "";
        if (!hash_equals($_SESSION["csrf"] ?? "", $token)) {
            http_response_code(403);
            exit("Permintaan tidak valid. Silakan muat ulang halaman.");
        }
    }
}
function redirect_to($page) {
    header("Location: " . $page);
    exit;
}
