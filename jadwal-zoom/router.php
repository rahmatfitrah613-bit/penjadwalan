<?php
// Router untuk PHP built-in server (Railway deployment)
// Menggantikan fungsi .htaccess pada Apache

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Jika file static (css, js, gambar, dll) — serve langsung
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Default: serve index.php (redirect ke login)
require_once __DIR__ . '/index.php';
