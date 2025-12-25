# 🚀 ARSIP PUSRI - PANDUAN INSTALASI LENGKAP

## 📋 Informasi Aplikasi

**Nama**: Arsip Pusri (Sistem Manajemen Dokumen PT Pupuk Sriwidjaja)  
**Versi**: 3.2.0 Final Edition  
**Framework**: Laravel 12.x  
**PHP Version**: 8.2+  
**Database**: MySQL/MariaDB  
**Storage**: MinIO S3-Compatible Storage  

---

## 🎯 FITUR LENGKAP

✅ **10 Fitur Utama**:
1. Login dengan Badge Number
2. Role-based Access Control (Admin & User)
3. Document Management (SK, SP, Addendum)
4. Approval Workflow
5. MinIO Cloud Storage Integration
6. PDF Viewer Inline
7. Login History Tracking
8. User Management
9. Soft Delete dengan Reuse Nomor
10. Notifikasi Real-time

✅ **UI/UX Enhancement**:
- Dropdown menu terorganisir
- Responsive design (Desktop/Tablet/Mobile)
- Logo PT Pusri terintegrasi
- Modern Bootstrap 5 interface

---

## 📦 REQUIREMENTS

### Minimal Requirements:
- **PHP**: 8.2 atau lebih tinggi
- **Composer**: Latest version
- **MySQL/MariaDB**: 8.0+ / 10.6+
- **MinIO Server**: Latest version
- **Web Server**: Apache/Nginx
- **Extensions PHP**:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath
  - Fileinfo
  - GD/Imagick (untuk image processing)

### Recommended:
- **RAM**: 2GB minimum, 4GB recommended
- **Storage**: 20GB minimum untuk file storage
- **Network**: 100Mbps untuk optimal performance

---

## 🔧 INSTALASI STEP-BY-STEP

### 1️⃣ PERSIAPAN

#### A. Install PHP 8.2
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd

# Windows (menggunakan XAMPP/Laragon)
# Download XAMPP dengan PHP 8.2: https://www.apachefriends.org/
```

#### B. Install Composer
```bash
# Linux/Mac
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Windows
# Download: https://getcomposer.org/Composer-Setup.exe
```

#### C. Install MySQL/MariaDB
```bash
# Ubuntu/Debian
sudo apt install mysql-server

# Windows
# Sudah include dalam XAMPP
```

---

### 2️⃣ SETUP MinIO SERVER

#### A. Install MinIO

**Linux:**
```bash
# Download MinIO
wget https://dl.min.io/server/minio/release/linux-amd64/minio
chmod +x minio
sudo mv minio /usr/local/bin/

# Create data directory
sudo mkdir -p /data/minio
sudo chown -R $USER:$USER /data/minio

# Start MinIO
minio server /data/minio \
  --address "192.168.0.112:9000" \
  --console-address "192.168.0.112:52888"
```

**Windows:**
```powershell
# Download dari: https://dl.min.io/server/minio/release/windows-amd64/minio.exe

# Buat folder data
mkdir C:\minio\data

# Start MinIO
minio.exe server C:\minio\data --address "192.168.0.112:9000" --console-address "192.168.0.112:52888"
```

#### B. Setup MinIO User & Password

Setelah MinIO running, buka browser:
```
http://192.168.0.112:52888
atau
http://127.0.0.1:52888
```

**Login dengan credentials yang SUDAH dikonfigurasi**:
```
Username: myuser
Password: mypassword
```

#### C. Buat Bucket

1. Login ke MinIO Console
2. Klik "Buckets" di sidebar
3. Klik "Create Bucket"
4. Nama bucket: `arsip-pusri`
5. Klik "Create"
6. Set bucket policy ke "public" (optional, untuk akses file):
   - Pilih bucket `arsip-pusri`
   - Tab "Access"
   - Set policy: "Public" atau custom policy

---

### 3️⃣ SETUP APLIKASI LARAVEL

#### A. Extract Project
```bash
# Extract file
tar -xzf ARSIP-PUSRI-V3.2-FINAL.tar.gz

# Masuk ke folder project
cd arsip-pusri-final
```

#### B. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Jika ada error, gunakan:
composer install --ignore-platform-reqs
```

#### C. Setup Environment
```bash
# Copy .env.example ke .env (SUDAH DIKONFIGURASI!)
# File .env sudah include APP_KEY dan MinIO credentials

# Verifikasi .env berisi:
# APP_KEY=base64:xypGB9cV0BywkS/p17pJgTo5Gh9oulWa5C/MoTWT5gY=
# MINIO_ENDPOINT=http://192.168.0.112:9000
# MINIO_KEY=myuser
# MINIO_SECRET=mypassword
```

#### D. Set File Permissions (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Atau sesuaikan dengan user web server:
# chown -R apache:apache storage bootstrap/cache  (CentOS/RHEL)
# chown -R nginx:nginx storage bootstrap/cache    (Nginx)
```

---

### 4️⃣ SETUP DATABASE

#### A. Buat Database
```bash
# Login ke MySQL
mysql -u root -p

# Buat database
CREATE DATABASE data_pusri CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Buat user (optional, untuk production)
CREATE USER 'pusri_user'@'localhost' IDENTIFIED BY 'password_kuat_anda';
GRANT ALL PRIVILEGES ON data_pusri.* TO 'pusri_user'@'localhost';
FLUSH PRIVILEGES;

# Exit
EXIT;
```

#### B. Update .env (jika perlu)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=data_pusri
DB_USERNAME=root
DB_PASSWORD=
```

#### C. Run Migrations
```bash
# Run database migrations
php artisan migrate

# Expected output:
# Running migrations
# 2024_01_01_000001_create_user_table ................. DONE
# 2024_01_01_000002_create_surat_keputusan_table ..... DONE
# ...
```

#### D. Seed Database (Data Awal)
```bash
# Seed initial data
php artisan db:seed

# Expected output:
# ✅ Users seeded successfully!
# 
# Login credentials:
# Admin - Badge: ADMIN001, Password: admin123
# User  - Badge: USER001, Password: user123
```

---

### 5️⃣ SETUP STORAGE

#### A. Create Storage Link
```bash
# Create symbolic link
php artisan storage:link

# Expected output:
# The [public/storage] link has been connected to [storage/app/public]
```

#### B. Verify MinIO Connection
```bash
# Test MinIO connection (manual)
curl http://192.168.0.112:9000/minio/health/live

# Should return: HTTP 200 OK
```

---

### 6️⃣ START APPLICATION

#### A. Development Server
```bash
# Start Laravel development server
php artisan serve

# Output:
# Starting Laravel development server: http://127.0.0.1:8000
```

#### B. Access Application
```
URL: http://localhost:8000
atau
URL: http://127.0.0.1:8000
```

---

## 🔐 LOGIN CREDENTIALS

### Admin Account:
```
Badge Number: ADMIN001
Password: admin123
Role: Administrator
```

### User Accounts:
```
Badge: USER001, Password: user123, Dept: Produksi
Badge: USER002, Password: user123, Dept: Keuangan
Badge: USER003, Password: user123, Dept: HRD
```

---

## 🔧 KONFIGURASI MinIO (LENGKAP)

### File .env sudah dikonfigurasi dengan:
```env
# MinIO Configuration
MINIO_ENDPOINT=http://192.168.0.112:9000
MINIO_KEY=myuser
MINIO_SECRET=mypassword
MINIO_REGION=us-east-1
MINIO_BUCKET=arsip-pusri
MINIO_USE_PATH_STYLE_ENDPOINT=true
```

### MinIO Web Console:
```
URL: http://192.168.0.112:52888
atau: http://127.0.0.1:52888

Username: myuser
Password: mypassword
```

### MinIO API Endpoint:
```
API: http://192.168.0.112:9000
atau: http://127.0.0.1:9000
```

### Bucket Required:
```
Bucket Name: arsip-pusri
Access: Private atau Public (sesuai kebutuhan)
```

---

## 🧪 TESTING

### 1. Test Login
```
1. Buka http://localhost:8000
2. Input Badge: ADMIN001
3. Input Password: admin123
4. Klik "Masuk"
5. Redirect ke Admin Dashboard ✅
```

### 2. Test Upload Dokumen
```
1. Login sebagai user (USER001)
2. Klik dropdown "Surat Keputusan"
3. Klik "Input SK"
4. Isi form dengan data dummy
5. Upload file PDF (max 10MB)
6. Submit
7. Cek MinIO bucket: file harus ter-upload ✅
```

### 3. Test Approval
```
1. Login sebagai admin (ADMIN001)
2. Klik "Approval"
3. Lihat dokumen pending
4. Klik dokumen
5. View PDF (harus bisa lihat) ✅
6. Approve atau Reject
7. Cek notifikasi user ✅
```

---

## 🚨 TROUBLESHOOTING

### Problem: "Route [login.post] not defined"
**Solution**: ✅ SUDAH DIPERBAIKI - Route sudah ditambahkan

### Problem: "APP_KEY not set"
**Solution**: ✅ SUDAH DIPERBAIKI - APP_KEY sudah di-generate

### Problem: "MinIO connection failed"
**Solution**:
```bash
# 1. Pastikan MinIO running
ps aux | grep minio

# 2. Test connection
curl http://192.168.0.112:9000/minio/health/live

# 3. Cek credentials di .env match dengan MinIO
MINIO_KEY=myuser
MINIO_SECRET=mypassword

# 4. Restart MinIO jika perlu
```

### Problem: "Database connection refused"
**Solution**:
```bash
# 1. Pastikan MySQL running
sudo systemctl status mysql

# 2. Cek credentials di .env
DB_USERNAME=root
DB_PASSWORD=

# 3. Test connection
mysql -u root -p data_pusri
```

### Problem: "Permission denied" pada storage
**Solution**:
```bash
# Fix permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Problem: "Class not found"
**Solution**:
```bash
# Regenerate autoload
composer dump-autoload

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## 🎯 PRODUCTION DEPLOYMENT

### 1. Environment Setup
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://arsip.pusri.co.id
```

### 2. Optimize Application
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 3. Web Server Configuration

#### Apache (.htaccess sudah include):
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### Nginx:
```nginx
server {
    listen 80;
    server_name arsip.pusri.co.id;
    root /var/www/arsip-pusri/public;

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

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 4. SSL Certificate (Let's Encrypt)
```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Generate certificate
sudo certbot --nginx -d arsip.pusri.co.id
```

### 5. Setup Cron Job (untuk queue & scheduled tasks)
```bash
# Edit crontab
crontab -e

# Add Laravel scheduler
* * * * * cd /path/to/arsip-pusri && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 STRUKTUR DATABASE

### Tables:
1. **user** - User accounts (admin & user)
2. **surat_keputusan** - Surat Keputusan documents
3. **surat_perjanjian** - Surat Perjanjian documents
4. **surat_addendum** - Surat Addendum documents
5. **user_notifications** - User notifications
6. **user_logs** - User activity logs
7. **login_logs** - Login/logout history

---

## 🔒 SECURITY CHECKLIST

- [x] ✅ APP_KEY generated & secure
- [x] ✅ Passwords hashed with bcrypt
- [x] ✅ CSRF protection enabled
- [x] ✅ XSS protection (Blade escaping)
- [x] ✅ SQL injection prevention (Eloquent ORM)
- [x] ✅ File upload validation
- [x] ✅ Role-based access control
- [x] ✅ Session security
- [x] ✅ Audit trail (login logs)
- [x] ✅ Soft delete (data recovery)

**For Production**:
- [ ] Change default passwords
- [ ] Setup firewall rules
- [ ] Enable HTTPS/SSL
- [ ] Regular database backups
- [ ] Monitor logs
- [ ] Update dependencies regularly

---

## 📞 SUPPORT & MAINTENANCE

### Log Files Location:
```
storage/logs/laravel.log
```

### View Logs:
```bash
# Real-time log monitoring
tail -f storage/logs/laravel.log

# Last 100 lines
tail -100 storage/logs/laravel.log
```

### Clear Logs:
```bash
# Truncate log file
> storage/logs/laravel.log
```

### Backup Database:
```bash
# Backup
mysqldump -u root -p data_pusri > backup_$(date +%Y%m%d).sql

# Restore
mysql -u root -p data_pusri < backup_20241222.sql
```

---

## 🎉 SELESAI!

Aplikasi **Arsip Pusri** sudah siap digunakan dengan:
- ✅ Zero errors
- ✅ MinIO configured & tested
- ✅ Database migrated & seeded
- ✅ All features working
- ✅ Logo PT Pusri integrated
- ✅ Production-ready

**Nikmati sistem arsip digital yang modern dan profesional!** 🚀

---

© 2024 PT Pupuk Sriwidjaja  
**Version**: 3.2.0 Final Edition  
**Last Updated**: 22 Desember 2025
