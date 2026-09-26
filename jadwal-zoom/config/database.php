<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gunakan environment variables jika ada (untuk Railway/cloud), fallback ke localhost (XAMPP)
$host     = getenv('DB_HOST')     ?: 'localhost';
$username = getenv('DB_USER')     ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME')     ?: 'db_jadwal_zoom';
$port     = (int)(getenv('DB_PORT') ?: 3306);

$conn = new mysqli($host, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

function base_url($path = '') {
    $server_protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $dir      = str_replace('\\', '/', __DIR__);
    $project_root = str_replace('/config', '', $dir);
    $base_path = str_replace($doc_root, '', $project_root);
    return $base_path . '/' . ltrim($path, '/');
}
?>
