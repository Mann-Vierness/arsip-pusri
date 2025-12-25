# 🚀 PANDUAN INSTALASI LENGKAP - Arsip Pusri Laravel

## 📋 PERSIAPAN (Requirements)

Pastikan sudah terinstall:
- ✅ PHP 8.2 atau lebih tinggi
- ✅ Composer 2.x
- ✅ MySQL 8.0+ atau MariaDB 10.3+
- ✅ MinIO Server (untuk storage PDF)
- ✅ Web Server (Apache/Nginx) - Optional untuk development

---

## 🔧 LANGKAH 1: DOWNLOAD & EXTRACT

### Download File
Download file: `ARSIP-PUSRI-LARAVEL-COMPLETE-v1.1.tar.gz`

### Extract File

**Linux/Mac:**
```bash
tar -xzf ARSIP-PUSRI-LARAVEL-COMPLETE-v1.1.tar.gz
cd arsip-pusri-full
```

**Windows:**
- Gunakan 7-Zip atau WinRAR untuk extract
- Buka folder `arsip-pusri-full`

---

## 🔧 LANGKAH 2: INSTALL DEPENDENCIES

Buka terminal/command prompt di folder project, kemudian jalankan:

```bash
composer install
```

**Tunggu hingga selesai** (proses download dependencies Laravel dan packages lainnya)

---

## 🔧 LANGKAH 3: SETUP ENVIRONMENT FILE

### Copy .env.example ke .env

**Linux/Mac:**
```bash
cp .env.example .env
```

**Windows:**
```bash
copy .env.example .env
```

### Generate Application Key

```bash
php artisan key:generate
```

---

## 🔧 LANGKAH 4: SETUP DATABASE

### A. Buat Database

Buka MySQL/phpMyAdmin dan buat database baru:

```sql
CREATE DATABASE data_pusri CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### B. Edit File .env

Buka file `.env` dengan text editor dan ubah bagian database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=data_pusri
DB_USERNAME=root
DB_PASSWORD=
```

**Sesuaikan:**
- `DB_USERNAME` = username MySQL Anda
- `DB_PASSWORD` = password MySQL Anda (kosongkan jika tidak ada password)

---

## 🔧 LANGKAH 5: SETUP MINIO (Storage PDF)

### A. Install MinIO

**Windows:**
1. Download MinIO: https://dl.min.io/server/minio/release/windows-amd64/minio.exe
2. Simpan di folder `C:\minio\`
3. Buat folder `C:\minio\data\` untuk storage

**Linux/Mac:**
```bash
wget https://dl.min.io/server/minio/release/linux-amd64/minio
chmod +x minio
sudo mv minio /usr/local/bin/
mkdir -p ~/minio/data
```

### B. Jalankan MinIO Server

**Windows:**
```bash
# Buka Command Prompt
cd C:\minio
minio.exe server data --console-address ":9001"
```

**Linux/Mac:**
```bash
minio server ~/minio/data --console-address ":9001"
```

**Output akan menampilkan:**
```
API: http://127.0.0.1:9000
Console: http://127.0.0.1:9001
RootUser: minioadmin
RootPass: minioadmin
```

### C. Setup MinIO Bucket

1. Buka browser: `http://localhost:9001`
2. Login:
   - Username: `minioadmin`
   - Password: `minioadmin`
3. Klik "Buckets" > "Create Bucket"
4. Nama bucket: `arsip-pusri`
5. Klik "Create Bucket"

### D. Edit File .env untuk MinIO

Buka file `.env` dan pastikan konfigurasi MinIO seperti ini:

```env
MINIO_ENDPOINT=http://localhost:9000
MINIO_KEY=minioadmin
MINIO_SECRET=minioadmin
MINIO_REGION=us-east-1
MINIO_BUCKET=arsip-pusri
MINIO_USE_PATH_STYLE_ENDPOINT=true

FILESYSTEM_DISK=minio
```

---

## 🔧 LANGKAH 6: MIGRASI DATABASE

Jalankan migrasi untuk membuat tabel-tabel:

```bash
php artisan migrate
```

**Output:**
```
Migration table created successfully.
Migrating: 2024_01_01_000001_create_user_table
Migrated:  2024_01_01_000001_create_user_table (45.23ms)
Migrating: 2024_01_01_000002_create_surat_keputusan_table
Migrated:  2024_01_01_000002_create_surat_keputusan_table (52.14ms)
...
```

---

## 🔧 LANGKAH 7: SEED DATA (Data Awal)

Jalankan seeder untuk membuat user admin dan user sample:

```bash
php artisan db:seed
```

**Output:**
```
Seeding: Database\Seeders\DatabaseSeeder
Seeded:  Database\Seeders\DatabaseSeeder (123.45ms)
```

**User yang dibuat:**
- Admin: Badge `ADMIN001`, Password `admin123`
- User 1: Badge `USER001`, Password `user123`
- User 2: Badge `USER002`, Password `user123`
- User 3: Badge `USER003`, Password `user123`

---

## 🔧 LANGKAH 8: JALANKAN APLIKASI

### Development Server (Laravel Built-in)

```bash
php artisan serve
```

**Output:**
```
Starting Laravel development server: http://127.0.0.1:8000
```

Buka browser dan akses: **http://localhost:8000**

---

## 🎉 LANGKAH 9: LOGIN PERTAMA KALI

### Login sebagai Admin:
- URL: `http://localhost:8000`
- Badge: `ADMIN001`
- Password: `admin123`

### Login sebagai User:
- URL: `http://localhost:8000`
- Badge: `USER001`
- Password: `user123`

---

## ✅ VERIFIKASI INSTALASI

### Test Checklist:

1. **✅ Login Page Muncul**
   - Warna navy/blue sesuai original
   - Form login ada

2. **✅ Bisa Login Admin**
   - Dashboard admin muncul
   - Menu sidebar lengkap

3. **✅ Bisa Login User**
   - Dashboard user muncul
   - Menu sidebar lengkap

4. **✅ Test Create Surat Keputusan**
   - User > Surat Keputusan > Tambah
   - Upload PDF (max 20MB)
   - Submit berhasil

5. **✅ Test Approval**
   - Admin > Approval
   - Lihat dokumen pending
   - Approve/Reject berhasil

6. **✅ Test MinIO**
   - Upload PDF berhasil
   - Download PDF berhasil

---

## 🔧 TROUBLESHOOTING

### Problem 1: "Class 'League\Flysystem\AwsS3V3\AwsS3V3Adapter' not found"

**Solusi:**
```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### Problem 2: "SQLSTATE[HY000] [1045] Access denied"

**Solusi:**
- Cek username dan password di `.env`
- Pastikan MySQL service running

### Problem 3: "No application encryption key"

**Solusi:**
```bash
php artisan key:generate
```

### Problem 4: MinIO tidak bisa connect

**Solusi:**
- Pastikan MinIO server running
- Cek URL MinIO di `.env`: `http://localhost:9000`
- Cek bucket `arsip-pusri` sudah dibuat

### Problem 5: "419 Page Expired" saat login

**Solusi:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Problem 6: PDF tidak bisa upload

**Solusi:**
- Cek `php.ini`:
  ```ini
  upload_max_filesize = 20M
  post_max_size = 20M
  ```
- Restart web server

---

## 📱 UNTUK PRODUCTION (Deploy ke Server)

### 1. Upload ke Server

Upload semua file ke server via FTP/SFTP

### 2. Install Dependencies di Server

```bash
composer install --optimize-autoloader --no-dev
```

### 3. Set Permission

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Setup .env untuk Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### 5. Optimize untuk Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Setup Web Server

**Apache (.htaccess sudah ada):**
- Document root: `/public`
- Enable mod_rewrite

**Nginx:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/arsip-pusri-full/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 7. Setup SSL (Optional tapi Recommended)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

---

## 📞 SUPPORT

Jika ada masalah saat instalasi, cek dokumentasi lengkap di:
- `README.md` - Overview
- `DEVELOPER_GUIDE.md` - Technical details
- `STRUCTURE.md` - Database schema

---

## ✅ SELESAI!

Instalasi berhasil! Aplikasi siap digunakan! 🎉

**Default Login:**
- Admin: `ADMIN001` / `admin123`
- User: `USER001` / `user123`

**Jangan lupa ubah password default setelah login pertama kali!**

---

**Developed with ❤️ for PT Pupuk Sriwidjaja Palembang**
