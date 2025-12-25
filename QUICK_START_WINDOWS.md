# 🚀 Quick Start - Windows 11 (5 Menit)

## ⚡ Instalasi Cepat

### 1️⃣ Download & Install Prerequisites
```
✓ XAMPP (PHP 8.2): https://www.apachefriends.org/download.html
✓ Composer: https://getcomposer.org/download/
✓ MinIO: https://dl.min.io/server/minio/release/windows-amd64/minio.exe
```

### 2️⃣ Setup MinIO
```batch
# Buat folder
mkdir C:\minio\data

# Buat file start-minio.bat dengan isi:
set MINIO_ROOT_USER=minioadmin
set MINIO_ROOT_PASSWORD=minioadmin
C:\minio\minio.exe server C:\minio\data --console-address ":9001"

# Jalankan MinIO
Double-click start-minio.bat

# Buat bucket di http://localhost:9001
Bucket name: arsip-pusri
```

### 3️⃣ Extract Project
```
Extract → Copy ke C:\xampp\htdocs\arsip-pusri
```

### 4️⃣ Setup Database
```
http://localhost/phpmyadmin
Create database: data_pusri
```

### 5️⃣ Install Project
```cmd
cd C:\xampp\htdocs\arsip-pusri
copy .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

### 6️⃣ Akses
```
http://localhost:8000
Badge: ADMIN001
Password: admin123
```

---

## 📋 Checklist Harian

**Start:**
```
1. ✓ XAMPP → Start MySQL
2. ✓ Double-click: C:\minio\start-minio.bat
3. ✓ CMD: php artisan serve
4. ✓ Browser: http://localhost:8000
```

**Stop:**
```
1. ✓ Close browser
2. ✓ Ctrl+C pada CMD Laravel
3. ✓ Ctrl+C pada CMD MinIO
4. ✓ XAMPP → Stop MySQL
```

---

## 🔧 Troubleshooting Cepat

| Problem | Solution |
|---------|----------|
| MySQL error | XAMPP → Start MySQL |
| MinIO error | Jalankan start-minio.bat |
| 404 Not Found | php artisan serve |
| Upload gagal | Cek MinIO running |
| Permission error | Folder storage → Properties → Full Control |

---

## 📝 File .env Penting

```env
DB_DATABASE=data_pusri
DB_USERNAME=root
DB_PASSWORD=

MINIO_ENDPOINT=http://localhost:9000
MINIO_KEY=minioadmin
MINIO_SECRET=minioadmin
MINIO_BUCKET=arsip-pusri
```

---

**Untuk panduan lengkap, baca: INSTALL_WINDOWS_11.md**
