# 📺 VISUAL INSTALLATION GUIDE - Step by Step

## 🎯 OVERVIEW

```
┌─────────────────────────────────────────────────────────────┐
│                    INSTALLATION FLOW                        │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. EXTRACT         →  2. COMPOSER      →  3. ENV          │
│     [ZIP File]          [Install]           [Configure]     │
│                                                             │
│  4. DATABASE        →  5. MINIO         →  6. MIGRATE      │
│     [Create DB]         [Setup]             [Tables]        │
│                                                             │
│  7. SEED            →  8. RUN           →  9. LOGIN        │
│     [Sample Data]       [Server]            [Test]         │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 📦 STEP 1: EXTRACT PROJECT

```
┌──────────────────────────────────────────────┐
│  ARSIP-PUSRI-LARAVEL-COMPLETE-v1.1.tar.gz   │
│                    ↓                         │
│              [EXTRACT]                       │
│                    ↓                         │
│           arsip-pusri-full/                  │
│           ├── app/                           │
│           ├── database/                      │
│           ├── resources/                     │
│           ├── routes/                        │
│           ├── .env.example                   │
│           └── composer.json                  │
└──────────────────────────────────────────────┘
```

**Command:**
```bash
tar -xzf ARSIP-PUSRI-LARAVEL-COMPLETE-v1.1.tar.gz
cd arsip-pusri-full
```

---

## 📚 STEP 2: INSTALL COMPOSER DEPENDENCIES

```
┌──────────────────────────────────────────────┐
│  composer.json                               │
│      ↓                                       │
│  composer install                            │
│      ↓                                       │
│  Downloading packages...                     │
│  ✓ laravel/framework                         │
│  ✓ league/flysystem-aws-s3-v3                │
│  ✓ ... (50+ packages)                        │
│      ↓                                       │
│  vendor/                                     │
│  └── [All dependencies installed]            │
└──────────────────────────────────────────────┘
```

**Command:**
```bash
composer install
```

**Output:**
```
Installing dependencies from lock file
...
Package operations: 93 installs, 0 updates, 0 removals
  - Installing symfony/polyfill-ctype (v1.x)
  - Installing laravel/framework (v12.x)
...
Generating optimized autoload files
```

---

## ⚙️ STEP 3: CONFIGURE ENVIRONMENT

```
┌──────────────────────────────────────────────┐
│  .env.example                                │
│      ↓ COPY                                  │
│  .env                                        │
│      ↓ EDIT                                  │
│  ┌────────────────────────────┐              │
│  │ DB_DATABASE=data_pusri     │              │
│  │ DB_USERNAME=root           │              │
│  │ DB_PASSWORD=               │              │
│  │                            │              │
│  │ MINIO_ENDPOINT=...         │              │
│  │ MINIO_BUCKET=arsip-pusri   │              │
│  └────────────────────────────┘              │
│      ↓                                       │
│  php artisan key:generate                    │
│      ↓                                       │
│  APP_KEY=base64:xxxxx                        │
└──────────────────────────────────────────────┘
```

**Commands:**
```bash
cp .env.example .env
php artisan key:generate
```

**Edit .env:**
```
1. Open .env with text editor
2. Find DB_* settings
3. Change DB_DATABASE to "data_pusri"
4. Set DB_USERNAME (default: root)
5. Set DB_PASSWORD (blank if no password)
6. Save file
```

---

## 🗄️ STEP 4: CREATE DATABASE

```
┌──────────────────────────────────────────────┐
│  MySQL / phpMyAdmin                          │
│      ↓                                       │
│  CREATE DATABASE data_pusri;                 │
│      ↓                                       │
│  ┌────────────────────────────┐              │
│  │  Database: data_pusri      │              │
│  │  Status: ✓ Created         │              │
│  │  Collation: utf8mb4        │              │
│  └────────────────────────────┘              │
└──────────────────────────────────────────────┘
```

**MySQL Command:**
```sql
CREATE DATABASE data_pusri 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
```

**Or via phpMyAdmin:**
```
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click "New" or "Databases"
3. Database name: data_pusri
4. Collation: utf8mb4_unicode_ci
5. Click "Create"
```

---

## 💾 STEP 5: SETUP MINIO

```
┌──────────────────────────────────────────────┐
│  Download MinIO                              │
│      ↓                                       │
│  Start MinIO Server                          │
│      ↓                                       │
│  ┌────────────────────────────┐              │
│  │  API: :9000                │              │
│  │  Console: :9001            │              │
│  │  User: minioadmin          │              │
│  └────────────────────────────┘              │
│      ↓                                       │
│  Open http://localhost:9001                  │
│      ↓                                       │
│  Login: minioadmin / minioadmin              │
│      ↓                                       │
│  Create Bucket: "arsip-pusri"                │
│      ↓                                       │
│  ✓ Bucket Ready                              │
└──────────────────────────────────────────────┘
```

**Commands:**

**Windows:**
```bash
# Download minio.exe
# In folder containing minio.exe:
minio.exe server data --console-address ":9001"
```

**Linux/Mac:**
```bash
wget https://dl.min.io/server/minio/release/linux-amd64/minio
chmod +x minio
./minio server data --console-address ":9001"
```

**Browser:**
```
1. Open: http://localhost:9001
2. Login: minioadmin / minioadmin
3. Click "Buckets" → "Create Bucket"
4. Name: arsip-pusri
5. Click "Create Bucket"
6. ✓ Done
```

---

## 🔨 STEP 6: RUN MIGRATIONS

```
┌──────────────────────────────────────────────┐
│  php artisan migrate                         │
│      ↓                                       │
│  Creating tables...                          │
│      ↓                                       │
│  ✓ user                                      │
│  ✓ surat_keputusan                           │
│  ✓ surat_perjanjian                          │
│  ✓ surat_addendum                            │
│  ✓ user_notifications                        │
│  ✓ user_logs                                 │
│      ↓                                       │
│  Database Structure Ready!                   │
└──────────────────────────────────────────────┘
```

**Command:**
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

## 🌱 STEP 7: SEED DATA

```
┌──────────────────────────────────────────────┐
│  php artisan db:seed                         │
│      ↓                                       │
│  Creating users...                           │
│      ↓                                       │
│  ✓ ADMIN001 (admin) - admin123               │
│  ✓ USER001 (user) - user123                  │
│  ✓ USER002 (user) - user123                  │
│  ✓ USER003 (user) - user123                  │
│      ↓                                       │
│  Sample Data Ready!                          │
└──────────────────────────────────────────────┘
```

**Command:**
```bash
php artisan db:seed
```

**Output:**
```
Seeding: Database\Seeders\DatabaseSeeder
Seeded:  Database\Seeders\DatabaseSeeder (123.45ms)
Database seeding completed successfully.
```

---

## 🚀 STEP 8: START SERVER

```
┌──────────────────────────────────────────────┐
│  php artisan serve                           │
│      ↓                                       │
│  Starting Laravel development server...      │
│      ↓                                       │
│  ┌────────────────────────────┐              │
│  │  Server started!           │              │
│  │  http://127.0.0.1:8000     │              │
│  │                            │              │
│  │  Press Ctrl-C to stop      │              │
│  └────────────────────────────┘              │
└──────────────────────────────────────────────┘
```

**Command:**
```bash
php artisan serve
```

**Output:**
```
   INFO  Server running on [http://127.0.0.1:8000].

  Press Ctrl+C to stop the server
```

---

## 🔐 STEP 9: LOGIN & TEST

```
┌──────────────────────────────────────────────┐
│  Open Browser                                │
│      ↓                                       │
│  http://localhost:8000                       │
│      ↓                                       │
│  ┌────────────────────────────┐              │
│  │   LOGIN PAGE               │              │
│  │                            │              │
│  │   Badge: [ADMIN001]        │              │
│  │   Password: [admin123]     │              │
│  │                            │              │
│  │   [Login Button]           │              │
│  └────────────────────────────┘              │
│      ↓                                       │
│  ✓ Dashboard Admin Muncul                    │
│      ↓                                       │
│  Test Features:                              │
│  ✓ View Documents                            │
│  ✓ Approval System                           │
│  ✓ User Management                           │
└──────────────────────────────────────────────┘
```

**Test Login:**

**As Admin:**
- URL: http://localhost:8000
- Badge: `ADMIN001`
- Password: `admin123`
- Should see: Admin Dashboard

**As User:**
- Badge: `USER001`
- Password: `user123`
- Should see: User Dashboard

---

## ✅ VERIFICATION CHECKLIST

```
┌─────────────────────────────────────────────┐
│  INSTALLATION VERIFICATION                  │
├─────────────────────────────────────────────┤
│                                             │
│  [ ] Extract project                        │
│  [ ] Composer install completed             │
│  [ ] .env file configured                   │
│  [ ] Database created                       │
│  [ ] MinIO running & bucket created         │
│  [ ] Migration completed                    │
│  [ ] Seeder completed                       │
│  [ ] Server running                         │
│  [ ] Login page accessible                  │
│  [ ] Admin login works                      │
│  [ ] User login works                       │
│  [ ] Can create document                    │
│  [ ] Can upload PDF                         │
│  [ ] Can approve/reject (admin)             │
│                                             │
│  All checked? ✓ INSTALLATION SUCCESS!      │
└─────────────────────────────────────────────┘
```

---

## 🎯 QUICK REFERENCE

| Step | Command | Time |
|------|---------|------|
| 1. Extract | `tar -xzf ...` | 10s |
| 2. Composer | `composer install` | 2-5 min |
| 3. Env | `cp .env.example .env` | 30s |
| 4. Key | `php artisan key:generate` | 5s |
| 5. Database | Create via MySQL/phpMyAdmin | 1 min |
| 6. MinIO | Download & run | 2 min |
| 7. Migrate | `php artisan migrate` | 30s |
| 8. Seed | `php artisan db:seed` | 10s |
| 9. Serve | `php artisan serve` | 5s |

**Total: ~15 minutes**

---

## 🆘 COMMON ERRORS & SOLUTIONS

```
ERROR: "Class not found"
└─→ SOLUTION: composer require league/flysystem-aws-s3-v3

ERROR: "Connection refused [database]"
└─→ SOLUTION: Check DB credentials in .env

ERROR: "419 Page Expired"
└─→ SOLUTION: php artisan cache:clear

ERROR: "MinIO connection failed"
└─→ SOLUTION: Check MinIO is running on port 9000

ERROR: "Permission denied"
└─→ SOLUTION: chmod -R 755 storage bootstrap/cache
```

---

**🎉 INSTALLATION COMPLETE!**

Untuk detail lengkap, lihat: **INSTALL_GUIDE.md**
