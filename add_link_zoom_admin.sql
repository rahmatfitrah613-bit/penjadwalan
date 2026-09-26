-- ============================================================
-- MIGRATION: Tambah kolom yang belum ada di tabel jadwal_zoom
-- Jalankan di phpMyAdmin > db_jadwal_zoom > SQL
-- ============================================================
USE db_jadwal_zoom;

ALTER TABLE jadwal_zoom
    ADD COLUMN token_zoom VARCHAR(100) NULL AFTER link_zoom_admin,
    ADD COLUMN file_video VARCHAR(255) NULL AFTER dokumentasi,
    ADD COLUMN file_materi VARCHAR(255) NULL AFTER file_video;
