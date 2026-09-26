<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$host = "localhost";
$username = "root";
$password = "";
$database = "db_jadwal_zoom";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

function base_url($path = '') {
    $base_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    // If the script is in a subdirectory (like jadwal, auth), we need to get the root of the project
    // A simple hack is to rely on a fixed folder name if needed, or compute it.
    // Let's use a simpler approach: get the directory of the current executed script and find where the 'config' folder would be relative to it, but it's easier to just use the HTTP_HOST and compute the base path.
    $server_protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    
    // Instead of guessing, we can use the requested URI and strip the script name, but let's just make it relative to the document root:
    $doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $dir = str_replace('\\', '/', __DIR__);
    // $dir is like C:/xampp/htdocs/Penjadwalan/jadwal-zoom/config
    $project_root = str_replace('/config', '', $dir);
    $base_path = str_replace($doc_root, '', $project_root);
    
    return $base_path . '/' . ltrim($path, '/');
}
?>
