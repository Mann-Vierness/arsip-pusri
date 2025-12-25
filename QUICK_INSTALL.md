# ⚡ QUICK INSTALL - Arsip Pusri Laravel

## 🚀 Instalasi Cepat (15 Menit)

### 1️⃣ Extract & Install Dependencies
```bash
tar -xzf ARSIP-PUSRI-LARAVEL-COMPLETE-v1.1.tar.gz
cd arsip-pusri-full
composer install
```

### 2️⃣ Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3️⃣ Edit .env - Database
```env
DB_DATABASE=data_pusri
DB_USERNAME=root
DB_PASSWORD=
```

### 4️⃣ Edit .env - MinIO
```env
MINIO_ENDPOINT=http://localhost:9000
MINIO_KEY=minioadmin
MINIO_SECRET=minioadmin
MINIO_BUCKET=arsip-pusri
```

### 5️⃣ Buat Database
```sql
CREATE DATABASE data_pusri;
```

### 6️⃣ Setup MinIO
**Windows:**
```bash
# Download dari https://min.io/download
minio.exe server data --console-address ":9001"
```

**Linux/Mac:**
```bash
wget https://dl.min.io/server/minio/release/linux-amd64/minio
chmod +x minio
./minio server data --console-address ":9001"
```

**Buka: http://localhost:9001**
- Login: minioadmin / minioadmin
- Create bucket: `arsip-pusri`

### 7️⃣ Migrasi & Seed
```bash
php artisan migrate
php artisan db:seed
```

### 8️⃣ Jalankan Server
```bash
php artisan serve
```

### 9️⃣ Buka Browser
```
http://localhost:8000
```

---

## 🔑 Default Login

**Admin:**
- Badge: `ADMIN001`
- Password: `admin123`

**User:**
- Badge: `USER001`
- Password: `user123`

---

## ⚠️ Troubleshooting Cepat

**Error: Class not found**
```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

**Error: 419 Page Expired**
```bash
php artisan cache:clear
php artisan config:clear
```

**Error: Cannot connect to MinIO**
- Pastikan MinIO running
- Cek bucket `arsip-pusri` sudah dibuat
- Cek `.env` MINIO_ENDPOINT

**Error: PDF upload failed**
Edit `php.ini`:
```ini
upload_max_filesize = 20M
post_max_size = 20M
```

---

## 📋 Checklist

- [ ] PHP 8.2+ installed
- [ ] Composer installed
- [ ] MySQL/MariaDB installed
- [ ] MinIO downloaded
- [ ] Database created
- [ ] Composer install done
- [ ] .env configured
- [ ] Migration done
- [ ] Seeder done
- [ ] MinIO bucket created
- [ ] Server running
- [ ] Login berhasil

---

## ✅ SELESAI!

Untuk panduan lengkap, lihat: **INSTALL_GUIDE.md**
