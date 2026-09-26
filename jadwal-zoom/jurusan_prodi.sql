-- Tambahkan tabel jurusan dan prodi
-- Jalankan perintah ini di phpMyAdmin atau MySQL console

USE db_jadwal_zoom;

CREATE TABLE IF NOT EXISTS jurusan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_jurusan VARCHAR(20) NOT NULL UNIQUE,
    nama_jurusan VARCHAR(150) NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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

-- Contoh data awal (opsional, hapus jika tidak diperlukan)
-- INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES ('TI', 'Teknik Informatika');
-- INSERT INTO prodi (kode_prodi, nama_prodi, jurusan_id, jenjang) VALUES ('TI-S1', 'Teknik Informatika', 1, 'S1');
