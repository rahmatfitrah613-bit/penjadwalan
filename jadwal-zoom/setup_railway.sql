-- ============================================================
-- FULL SETUP SQL untuk Railway / Cloud MySQL
-- Import file ini sekali saat setup awal di Railway
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_jadwal_zoom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_jadwal_zoom;

-- ============================================================
-- Tabel users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Tabel jurusan
-- ============================================================
CREATE TABLE IF NOT EXISTS jurusan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_jurusan VARCHAR(20) NOT NULL UNIQUE,
    nama_jurusan VARCHAR(150) NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Tabel prodi
-- ============================================================
CREATE TABLE IF NOT EXISTS prodi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_prodi VARCHAR(30) NOT NULL UNIQUE,
    nama_prodi VARCHAR(150) NOT NULL,
    jurusan_id INT NOT NULL,
    jenjang ENUM('D3','D4','S1','S2','S3','Profesi') DEFAULT 'S1',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE CASCADE
);

-- ============================================================
-- Tabel jadwal_zoom (lengkap dengan semua kolom)
-- ============================================================
CREATE TABLE IF NOT EXISTS jadwal_zoom (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal_rapat DATE NOT NULL,
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NOT NULL,
    nama_rapat VARCHAR(200) NOT NULL,
    jenis_rapat VARCHAR(100),
    jurusan VARCHAR(100),
    program_magister VARCHAR(100),
    link_zoom TEXT NOT NULL,
    link_zoom_admin TEXT NULL,
    token_zoom VARCHAR(100) NULL,
    password_zoom VARCHAR(100),
    dokumentasi VARCHAR(255),
    file_video VARCHAR(255) NULL,
    file_materi VARCHAR(255) NULL,
    hasil_rapat TEXT,
    keterangan TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================================
-- Default admin user
-- Password: admin123
-- ============================================================
INSERT IGNORE INTO users (nama, username, password, role)
VALUES ('Administrator', 'admin', '$2y$10$HRMkCJcz6rF4t.T4pvAJbeb.cWpTw7ZZ9sRPk4RV1V3cviqKbAGlW', 'admin');
