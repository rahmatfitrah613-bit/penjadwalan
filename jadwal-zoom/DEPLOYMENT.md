# Jadwal Zoom — PHP + MySQL App

## Deploy ke Railway (Gratis, Mudah)

### Langkah 1: Buat akun Railway
Buka https://railway.app → Sign up dengan GitHub

### Langkah 2: Deploy MySQL Database
1. Di Railway dashboard → **+ New Project**
2. Pilih **Provision MySQL**
3. Setelah selesai, klik service MySQL → tab **Variables**
4. Catat nilai: `MYSQL_HOST`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQLPASSWORD`, `MYSQL_DATABASE`, `MYSQL_PORT`

### Langkah 3: Import Database
1. Di Railway MySQL service → tab **Data** → buka **Query**
2. Copy-paste isi `database.sql` dan jalankan
3. Copy-paste isi `migration_railway.sql` dan jalankan

### Langkah 4: Deploy PHP App
1. Di Railway dashboard → **+ New Service** → **GitHub Repo**
2. Pilih repository ini
3. Set **Root Directory** → kosongkan (atau `/jadwal-zoom` jika ada subfolder)
4. Railway otomatis detect PHP via `composer.json`

### Langkah 5: Set Environment Variables di PHP Service
Di PHP service → tab **Variables** → tambahkan:
```
DB_HOST       = <nilai MYSQL_HOST dari MySQL service>
DB_USER       = <nilai MYSQL_USER>
DB_PASSWORD   = <nilai MYSQL_PASSWORD>
DB_NAME       = <nilai MYSQL_DATABASE>
DB_PORT       = <nilai MYSQL_PORT>
```

### Langkah 6: Akses Aplikasi
Railway akan generate URL seperti: `https://jadwal-zoom-production.up.railway.app`

---

## Alternatif: Deploy Manual ke InfinityFree (Hosting PHP Gratis)

1. Daftar di https://infinityfree.net
2. Upload semua file via FileZilla FTP
3. Buat database MySQL di control panel
4. Import `database.sql` via phpMyAdmin
5. Edit `config/database.php` dengan kredensial dari InfinityFree

---

## Catatan Penting tentang File Upload
File upload (foto, video, materi) tidak akan persisten di Railway karena filesystem-nya ephemeral.
Untuk solusi permanen, pertimbangkan integrasi Cloudinary atau Uploadthing.
Untuk sementara, fitur upload tetap berfungsi tapi file hilang saat restart.
