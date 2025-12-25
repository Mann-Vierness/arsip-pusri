# 🪟 Panduan Instalasi Arsip Pusri di Windows 11

## 📋 Daftar Isi
1. [Prerequisites](#prerequisites)
2. [Instalasi Software Pendukung](#instalasi-software-pendukung)
3. [Extract Project](#extract-project)
4. [Setup Database](#setup-database)
5. [Setup MinIO](#setup-minio)
6. [Konfigurasi Project](#konfigurasi-project)
7. [Menjalankan Aplikasi](#menjalankan-aplikasi)
8. [Testing](#testing)
9. [Troubleshooting](#troubleshooting)

---

## 📦 Prerequisites

Software yang dibutuhkan:
- ✅ Windows 11 (64-bit)
- ✅ XAMPP atau Laragon (untuk PHP & MySQL)
- ✅ Composer
- ✅ MinIO Server
- ✅ Browser (Chrome/Firefox/Edge)
- ✅ Text Editor (VSCode/Notepad++)

---

## 🔧 Instalasi Software Pendukung

### 1️⃣ Install XAMPP (Pilihan 1 - Recommended untuk Pemula)

**Download & Install:**
```
1. Download XAMPP dari: https://www.apachefriends.org/download.html
2. Pilih versi PHP 8.2.x
3. Download file installer (sekitar 150MB)
4. Jalankan installer
5. Install di: C:\xampp (default)
6. Pilih komponen:
   ✓ Apache
   ✓ MySQL
   ✓ PHP
   ✓ phpMyAdmin
7. Klik Next sampai selesai
```

**Menjalankan XAMPP:**
```
1. Buka XAMPP Control Panel
2. Start Apache
3. Start MySQL
4. Tunggu sampai keduanya berwarna hijau
```

### 1️⃣ Install Laragon (Pilihan 2 - Recommended untuk Advanced)

**Download & Install:**
```
1. Download Laragon Full dari: https://laragon.org/download/
2. Pilih versi Full (dengan MySQL)
3. Download file installer
4. Jalankan installer
5. Install di: C:\laragon (default)
6. Klik Next sampai selesai
```

**Menjalankan Laragon:**
```
1. Buka Laragon
2. Klik "Start All"
3. Tunggu sampai Apache dan MySQL running
```

---

### 2️⃣ Install Composer

**Download & Install:**
```
1. Download Composer dari: https://getcomposer.org/download/
2. Klik "Composer-Setup.exe"
3. Jalankan installer
4. Pilih PHP path:
   - XAMPP: C:\xampp\php\php.exe
   - Laragon: C:\laragon\bin\php\php-8.2.x\php.exe
5. Klik Next sampai selesai
```

**Verifikasi Instalasi:**
```cmd
# Buka Command Prompt (CMD)
composer --version

# Output seharusnya:
# Composer version 2.x.x
```

---

### 3️⃣ Install MinIO Server

**Download:**
```
1. Download MinIO untuk Windows:
   https://dl.min.io/server/minio/release/windows-amd64/minio.exe

2. Buat folder untuk MinIO:
   C:\minio

3. Copy file minio.exe ke folder C:\minio
```

**Setup MinIO:**

**Buat folder untuk data:**
```cmd
# Buka Command Prompt
mkdir C:\minio\data
```

**Buat file start-minio.bat:**
```cmd
# Buka Notepad
# Copy paste script berikut:
```

```batch
@echo off
set MINIO_ROOT_USER=minioadmin
set MINIO_ROOT_PASSWORD=minioadmin
C:\minio\minio.exe server C:\minio\data --console-address ":9001"
pause
```

```cmd
# Save As:
# - File name: C:\minio\start-minio.bat
# - Save as type: All Files
```

**Jalankan MinIO:**
```
1. Double-click file: C:\minio\start-minio.bat
2. Tunggu sampai muncul pesan:
   "API: http://192.168.x.x:9000"
   "Console: http://192.168.x.x:9001"
3. JANGAN TUTUP jendela CMD ini!
```

**Akses MinIO Console:**
```
1. Buka browser
2. Akses: http://localhost:9001
3. Login:
   - Username: minioadmin
   - Password: minioadmin
```

**Buat Bucket:**
```
1. Di MinIO Console, klik "Buckets"
2. Klik "Create Bucket"
3. Bucket Name: arsip-pusri
4. Klik "Create Bucket"
```

---

## 📂 Extract Project

**Langkah-langkah:**

### Jika menggunakan XAMPP:
```
1. Extract file: ARSIP-PUSRI-LARAVEL-COMPLETE-v1.1.tar.gz
2. Copy folder "arsip-pusri-full" ke:
   C:\xampp\htdocs\

3. Rename folder menjadi "arsip-pusri":
   C:\xampp\htdocs\arsip-pusri
```

### Jika menggunakan Laragon:
```
1. Extract file: ARSIP-PUSRI-LARAVEL-COMPLETE-v1.1.tar.gz
2. Copy folder "arsip-pusri-full" ke:
   C:\laragon\www\

3. Rename folder menjadi "arsip-pusri":
   C:\laragon\www\arsip-pusri
```

---

## 🗄️ Setup Database

### 1️⃣ Buat Database

**Via phpMyAdmin:**
```
1. Buka browser
2. Akses: http://localhost/phpmyadmin
3. Klik tab "Databases"
4. Database name: data_pusri
5. Collation: utf8mb4_unicode_ci
6. Klik "Create"
```

**Via Command Line (Alternatif):**
```cmd
# Buka Command Prompt

# XAMPP:
cd C:\xampp\mysql\bin
mysql -u root -p

# Laragon:
cd C:\laragon\bin\mysql\mysql-8.x.x\bin
mysql -u root -p

# Di MySQL prompt:
CREATE DATABASE data_pusri CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

---

## ⚙️ Konfigurasi Project

### 1️⃣ Setup .env File

**Buka Command Prompt:**
```cmd
# XAMPP:
cd C:\xampp\htdocs\arsip-pusri

# Laragon:
cd C:\laragon\www\arsip-pusri

# Copy .env.example ke .env
copy .env.example .env
```

### 2️⃣ Edit .env File

**Buka file .env dengan Notepad++/VSCode:**

```env
APP_NAME="Arsip Pusri"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost:8000

# DATABASE CONFIGURATION
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=data_pusri
DB_USERNAME=root
DB_PASSWORD=

# MINIO CONFIGURATION
MINIO_ENDPOINT=http://localhost:9000
MINIO_KEY=minioadmin
MINIO_SECRET=minioadmin
MINIO_REGION=us-east-1
MINIO_BUCKET=arsip-pusri
MINIO_USE_PATH_STYLE_ENDPOINT=true

FILESYSTEM_DISK=minio

# SESSION & CACHE
BROADCAST_CONNECTION=log
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

MAIL_MAILER=log
```

**💡 Catatan Penting untuk Database Password:**
```
Jika MySQL Anda memiliki password:
DB_PASSWORD=password_mysql_anda

Jika tidak ada password (default XAMPP):
DB_PASSWORD=
```

---

### 3️⃣ Install Dependencies

**Buka Command Prompt di folder project:**
```cmd
# XAMPP:
cd C:\xampp\htdocs\arsip-pusri

# Laragon:
cd C:\laragon\www\arsip-pusri

# Install dependencies (akan download package dari internet)
composer install

# Tunggu proses selesai (5-10 menit tergantung internet)
```

**💡 Jika ada error "composer not found":**
```cmd
# Tutup CMD dan buka lagi
# Atau gunakan full path:
C:\ProgramData\ComposerSetup\bin\composer.exe install
```

---

### 4️⃣ Generate Application Key

```cmd
php artisan key:generate

# Output:
# Application key set successfully.
```

---

### 5️⃣ Run Migration & Seeder

**Jalankan Migration:**
```cmd
php artisan migrate

# Output akan menampilkan:
# Migration table created successfully.
# Migrating: 2024_01_01_000001_create_user_table
# Migrated:  2024_01_01_000001_create_user_table
# ... (6 migrations total)
```

**Jalankan Seeder:**
```cmd
php artisan db:seed

# Output:
# Database seeding completed successfully.
```

**✅ Seeder akan membuat:**
- 1 Admin: Badge `ADMIN001`, Password `admin123`
- 3 User: Badge `USER001/002/003`, Password `user123`

---

## 🚀 Menjalankan Aplikasi

### 1️⃣ Start Laravel Development Server

**Buka Command Prompt:**
```cmd
# XAMPP:
cd C:\xampp\htdocs\arsip-pusri

# Laragon:
cd C:\laragon\www\arsip-pusri

# Start server
php artisan serve

# Output:
# Starting Laravel development server: http://127.0.0.1:8000
# Press Ctrl+C to stop the server
```

**💡 Jika port 8000 sudah digunakan:**
```cmd
php artisan serve --port=8080
```

### 2️⃣ Pastikan MinIO Running

**Cek MinIO masih berjalan:**
```
1. Cek jendela CMD MinIO masih terbuka
2. Jika sudah tertutup, jalankan lagi:
   Double-click: C:\minio\start-minio.bat
```

### 3️⃣ Akses Aplikasi

**Buka Browser:**
```
URL: http://localhost:8000

Atau jika menggunakan port 8080:
URL: http://localhost:8080
```

---

## 🧪 Testing

### 1️⃣ Login sebagai Admin

```
URL: http://localhost:8000
Badge: ADMIN001
Password: admin123
```

**Test Admin Features:**
- ✅ Dashboard admin
- ✅ View all documents
- ✅ Approval system
- ✅ User management

### 2️⃣ Login sebagai User

```
Logout dari admin, lalu login:
Badge: USER001
Password: user123
```

**Test User Features:**
- ✅ Create SK (hari ini only)
- ✅ Create SP (bisa backdate)
- ✅ Create Addendum (bisa backdate)
- ✅ Upload PDF (max 20MB)
- ✅ View notifications
- ✅ Edit/delete pending documents

### 3️⃣ Test Approval Workflow

**Sebagai User:**
```
1. Login sebagai USER001
2. Create Surat Keputusan baru
3. Upload PDF
4. Submit
5. Status: Pending
6. Logout
```

**Sebagai Admin:**
```
1. Login sebagai ADMIN001
2. Lihat notifikasi badge (ada 1 pending)
3. Klik menu "Approval"
4. Klik "Detail" pada dokumen pending
5. Download PDF untuk review
6. Klik "Approve" atau "Reject"
7. Logout
```

**Cek Notifikasi User:**
```
1. Login kembali sebagai USER001
2. Lihat notifikasi badge (ada notifikasi baru)
3. Klik notifikasi
4. Lihat status approval (approved/rejected)
```

---

## 🔧 Troubleshooting

### ❌ Error: "composer: command not found"

**Solusi:**
```cmd
# Restart Command Prompt
# Atau gunakan full path:
C:\ProgramData\ComposerSetup\bin\composer.exe install
```

---

### ❌ Error: "SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'"

**Penyebab:** Password MySQL salah

**Solusi:**
```
1. Cek password MySQL Anda
2. Edit file .env:
   DB_PASSWORD=password_yang_benar
```

**Untuk XAMPP (default tanpa password):**
```env
DB_PASSWORD=
```

**Untuk Laragon (default tanpa password):**
```env
DB_PASSWORD=
```

---

### ❌ Error: "SQLSTATE[HY000] [2002] No connection could be made"

**Penyebab:** MySQL tidak running

**Solusi XAMPP:**
```
1. Buka XAMPP Control Panel
2. Start MySQL
3. Tunggu sampai hijau
```

**Solusi Laragon:**
```
1. Buka Laragon
2. Klik "Start All"
```

---

### ❌ Error: MinIO Connection Refused

**Penyebab:** MinIO tidak running

**Solusi:**
```
1. Double-click: C:\minio\start-minio.bat
2. Tunggu sampai running
3. Test akses: http://localhost:9001
```

---

### ❌ Error: "Failed to open stream: Permission denied"

**Penyebab:** Folder storage tidak writable

**Solusi:**
```
1. Buka folder project
2. Klik kanan folder "storage"
3. Properties → Security → Edit
4. Pilih user Anda → Full Control → OK
5. Apply to all subfolder
```

---

### ❌ Error: Upload PDF gagal

**Solusi:**

**1. Cek ukuran file:**
```
Maksimal 20MB
```

**2. Cek MinIO running:**
```
http://localhost:9001
```

**3. Cek bucket exists:**
```
Bucket name: arsip-pusri
```

**4. Edit php.ini (jika perlu):**
```
XAMPP: C:\xampp\php\php.ini
Laragon: C:\laragon\bin\php\php-8.2.x\php.ini

Ubah:
upload_max_filesize = 20M
post_max_size = 20M

Restart Apache
```

---

### ❌ Page tidak load / 404 Not Found

**Solusi:**
```cmd
# Pastikan artisan serve masih running
# Jika tidak, jalankan lagi:
php artisan serve

# Akses:
http://localhost:8000
```

---

### ❌ CSS/Design tidak muncul

**Penyebab:** Bootstrap CDN tidak terload

**Solusi:**
```
1. Cek koneksi internet
2. Atau edit file view untuk gunakan local Bootstrap
```

---

## 📝 Cara Menjalankan Setiap Hari

### ✅ Startup Checklist:

**1. Start MySQL:**
```
XAMPP: Buka XAMPP Control Panel → Start MySQL
Laragon: Buka Laragon → Start All
```

**2. Start MinIO:**
```
Double-click: C:\minio\start-minio.bat
JANGAN TUTUP jendela CMD ini!
```

**3. Start Laravel:**
```cmd
cd C:\xampp\htdocs\arsip-pusri
# atau
cd C:\laragon\www\arsip-pusri

php artisan serve
JANGAN TUTUP jendela CMD ini!
```

**4. Akses Aplikasi:**
```
Browser: http://localhost:8000
```

### ✅ Shutdown Checklist:

```
1. Close browser
2. Tekan Ctrl+C di CMD Laravel server
3. Tekan Ctrl+C di CMD MinIO server
4. Stop MySQL di XAMPP/Laragon
```

---

## 🔐 Default Login Credentials

### Admin:
```
URL: http://localhost:8000
Badge: ADMIN001
Password: admin123
```

### User 1:
```
Badge: USER001
Password: user123
```

### User 2:
```
Badge: USER002
Password: user123
```

### User 3:
```
Badge: USER003
Password: user123
```

---

## 📞 Bantuan Lebih Lanjut

### Dokumentasi Lengkap:
```
📄 README.md - Overview
📄 INSTALLATION.md - Instalasi detail
📄 DEVELOPER_GUIDE.md - Technical docs
📄 QUICK_START.md - Quick setup
📄 CHANGELOG.md - Version history
```

### Cek Log Error:
```
File: storage/logs/laravel.log
```

---

## ✅ Summary Instalasi

**Total waktu instalasi:** ±30-45 menit

**Step by step:**
1. ✅ Install XAMPP/Laragon (10 menit)
2. ✅ Install Composer (5 menit)
3. ✅ Install & setup MinIO (10 menit)
4. ✅ Extract project (2 menit)
5. ✅ Create database (2 menit)
6. ✅ Configure .env (3 menit)
7. ✅ Composer install (5-10 menit)
8. ✅ Migration & seed (2 menit)
9. ✅ Start server & test (5 menit)

**SELESAI! Aplikasi siap digunakan!** 🎉

---

**© 2024 PT Pupuk Sriwidjaja - Arsip Pusri Laravel**
