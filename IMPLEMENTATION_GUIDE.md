# IMPLEMENTASI LENGKAP SISTEM ARSIP PUSRI

## Ringkasan Perubahan

Sistem Arsip Pusri telah diperbaharui dengan fitur-fitur berikut:

### 1. SISTEM PENOMORAN OTOMATIS

#### Surat Keputusan (SK)
- **Format**: `SK/DIR/NOMOR/TAHUN` (contoh: `SK/DIR/001/2025`)
- **Karakteristik**:
  - Hanya bisa di-input untuk **tanggal hari ini**
  - Tidak bisa backdate
  - Nomor otomatis setiap hari dimulai dari 001
  - Admin ketika input langsung di-approve

#### Surat Perjanjian (SP)
- **Format**: `-NOMOR/SP/DIR/TAHUN` atau `-NOMOR/SP/TAHUN`
- **Contoh**: `-001/SP/DIR/2025` atau `-001/SP/2025`
- **Karakteristik**:
  - Boleh backdate ke tanggal sebelumnya
  - Nomor direset setiap tahun dimulai dari 001
  - Menggunakan suffix letter untuk backdate: A, B, C, ..., Z, AA, AB, ..., ZZ

#### Surat Addendum (ADD)
- **Format**: `-NOMOR/ADD-DIR/TAHUN` atau `-NOMOR/ADD/TAHUN`
- **Contoh**: `-001/ADD-DIR/2025` atau `-001/ADD/2025`
- **Karakteristik**:
  - Sama seperti SP (boleh backdate)
  - Nomor direset setiap tahun
  - Menggunakan suffix letter untuk backdate

---

## LOGIKA PENOMORAN DETAIL

### Contoh Scenario SP dengan Backdate

```
2025-01-20:
  - Input SP tanggal 2025-01-20 → nomor: -001/SP/2025

2025-01-21:
  - Input SP tanggal 2025-01-20 (backdate) → nomor: -001/SP/2025A
  - Input SP tanggal 2025-01-20 (backdate) → nomor: -001/SP/2025B
  - Input SP tanggal 2025-01-21 (hari ini) → nomor: -001/SP/2025
  - Input SP tanggal 2025-01-21 (hari ini) → nomor: -002/SP/2025

2025-01-22:
  - Input SP tanggal 2025-01-20 (backdate) → nomor: -001/SP/2025C
```

### Algoritma Penomoran SP/ADD dengan Backdate

```python
def generateBackdatableNumber(tanggal, jenis):
    tahun = tanggal.year
    isToday = tanggal.isToday()
    
    # 1. Cek soft delete dari tanggal yang sama
    deleted = Model.onlyTrashed()
        .whereDate('TANGGAL', tanggal)
        .first()
    
    if deleted:
        return deleted.NO  # Reuse soft delete
    
    # 2. Ambil semua data tahun ini
    allRecords = Model.withTrashed()
        .whereYear('TANGGAL', tahun)
        .orderByDesc('TANGGAL')
        .get()
    
    if allRecords.empty():
        return formatNomor(1)  # Nomor pertama tahun ini
    
    # 3. Cari anchor (nomor terakhir dengan tanggal <= tanggal input)
    anchor = findAnchor(allRecords, tanggal)
    
    if not anchor:
        # Semua data lebih baru dari tanggal input
        angka = 1
        suffix = 'A'
    else:
        angka, suffix = parseNomor(anchor.NO)
    
    # 4. Jika hari ini, cari suffix berikutnya
    if isToday:
        usedSuffixes = findUsedSuffixes(allRecords, angka, tanggal)
        nextSuffix = getNextSuffix(suffix, usedSuffixes)
        return formatNomor(angka, nextSuffix)
    else:
        # Backdate: gunakan suffix dari anchor
        return formatNomor(angka, suffix)
```

---

## SOFT DELETE DAN REUSE NOMOR

### Scenario
```
2025-01-15:
  1. Input 5 dokumen → nomor: 001, 002, 003, 004, 005
  2. User delete dokumen 001, 002, 003 (soft delete)
  
  Status:
  - Active: 004, 005
  - Soft Deleted: 001, 002, 003
  
  3. User input dokumen lagi
     → Ambil dari soft delete: 001 (force delete record lama)
  
  4. User input dokumen lagi
     → Ambil dari soft delete: 002
  
  Status akhir:
  - Active: 001, 002, 004, 005
  - Soft Deleted: 003
```

### Implementasi
```php
// 1. Cek soft delete dulu
$deleted = Model::onlyTrashed()
    ->whereDate('TANGGAL', $tanggal)
    ->first();

if ($deleted) {
    $num = $deleted->NO;
    $deleted->forceDelete();  // Hapus permanent
    return $num;  // Reuse nomor
}

// 2. Jika tidak ada soft delete, generate nomor baru
// ... lanjut dengan logika penomoran normal
```

---

## SISTEM APPROVAL

### Status Approval
- **pending**: Menunggu persetujuan admin
- **approved**: Sudah disetujui admin
- **rejected**: Ditolak oleh admin dengan alasan

### Workflow

#### User Input Dokumen
1. User fill form + upload PDF
2. Sistem generate nomor otomatis
3. PDF diupload ke MinIO
4. Approval status: `pending`
5. Notifikasi ke semua admin

#### Admin Review (di halaman Approval)
1. Admin lihat dokumen pending
2. Admin bisa:
   - **Approve**: Status → `approved`, notifikasi ke user
   - **Reject**: Status → `rejected`, notifikasi ke user + alasan

#### Admin Input Dokumen (Auto-Approved)
1. Admin fill form + upload PDF
2. Sistem generate nomor otomatis
3. PDF diupload ke MinIO
4. Approval status: `approved` (langsung, tidak perlu review)
5. `approved_by`: Badge admin yang input
6. `approved_at`: Timestamp sekarang

---

## INTEGRASI MINIO

### Konfigurasi (.env)
```
MINIO_ENDPOINT=http://192.168.0.112:9000
MINIO_KEY=myuser
MINIO_SECRET=mypassword
MINIO_REGION=us-east-1
MINIO_BUCKET=arsip-pusri
MINIO_USE_PATH_STYLE_ENDPOINT=true
```

### Path Penyimpanan
```
surat-keputusan/SK_NOMOR_WAKTU.pdf
surat-perjanjian/SP_NOMOR_WAKTU.pdf
surat-addendum/ADD_NOMOR_WAKTU.pdf
```

### Operasi
```php
// Upload
$file = $request->file('pdf_file');
$path = Storage::disk('minio')->putFileAs(
    'surat-keputusan',
    $file,
    'SK_001_2025_123456.pdf'
);

// Download
return Storage::disk('minio')->download($path, 'Surat_Keputusan.pdf');

// Delete
Storage::disk('minio')->delete($path);
```

---

## FILE-FILE YANG DIUBAH

### 1. Service Layer
- `app/Services/DocumentNumberService.php`
  - Complete rewrite dengan logika penomoran baru
  - Method: `generateSKNumber()`, `generateSPNumber()`, `generateAddendumNumber()`

### 2. Controllers - User
- `app/Http/Controllers/User/SuratKeputusanController.php`
  - Validasi tanggal harus hari ini
  - Upload PDF ke MinIO
  - Soft delete handling

- `app/Http/Controllers/User/SuratPerjanjianController.php`
  - Support backdate
  - Support DIR parameter
  - Upload PDF ke MinIO

- `app/Http/Controllers/User/SuratAddendumController.php`
  - Support backdate
  - Support DIR parameter
  - Upload PDF ke MinIO

### 3. Controllers - Admin
- `app/Http/Controllers/Admin/AdminDocumentController.php`
  - Input dokumen dengan auto-approval
  - Support DIR parameter
  - Upload PDF ke MinIO

- `app/Http/Controllers/Admin/ApprovalController.php`
  - Review pending dokumen
  - Approve/reject workflow
  - Notifikasi ke user

### 4. Models
- `app/Models/SuratKeputusan.php` (sudah updated)
- `app/Models/SuratPerjanjian.php` (sudah updated)
- `app/Models/SuratAddendum.php` (sudah updated)

### 5. Routes
- `routes/web.php` (sudah lengkap)

---

## DATABASE SCHEMA

### Kolom Penting di Setiap Tabel

```sql
-- Shared columns
id                    BIGINT PRIMARY KEY
[NOMOR_SK|NO]        VARCHAR(100) UNIQUE
TANGGAL              DATE
PERIHAL              TEXT
PENANDATANGAN        VARCHAR(100)
UNIT_KERJA           VARCHAR(100)
NAMA                 VARCHAR(100)
USER                 VARCHAR(50) FOREIGN KEY → user.BADGE
pdf_path             VARCHAR(255)
approval_status      ENUM('pending', 'approved', 'rejected')
approved_by          VARCHAR(50)
approved_at          TIMESTAMP
rejection_reason     TEXT
created_at           TIMESTAMP
updated_at           TIMESTAMP
deleted_at           TIMESTAMP (SOFT DELETE)
```

---

## VALIDASI & ATURAN BISNIS

### Surat Keputusan (SK)
- ✅ Tanggal harus hari ini (date_equals:today)
- ✅ Tidak bisa edit/delete jika sudah approved
- ✅ PDF wajib upload (max 20MB)
- ✅ Nomor auto-generate
- ✅ User hanya bisa lihat dokumen sendiri

### Surat Perjanjian (SP)
- ✅ Tanggal bisa backdate
- ✅ Tidak bisa edit/delete jika sudah approved
- ✅ PDF wajib upload (max 20MB)
- ✅ Nomor auto-generate dengan suffix letter untuk backdate
- ✅ DIR optional (untuk format dengan DIR)
- ✅ Direset setiap tahun
- ✅ User hanya bisa lihat dokumen sendiri

### Surat Addendum (ADD)
- ✅ Tanggal bisa backdate
- ✅ Tidak bisa edit/delete jika sudah approved
- ✅ PDF wajib upload (max 20MB)
- ✅ Nomor auto-generate dengan suffix letter untuk backdate
- ✅ DIR optional
- ✅ Direset setiap tahun
- ✅ User hanya bisa lihat dokumen sendiri

---

## TESTING CHECKLIST

### Test Penomoran
- [ ] SK hari ini naik dengan benar (001, 002, 003)
- [ ] SK error jika input tanggal kemarin
- [ ] SP backdate kemarin dapat suffix A, B, C
- [ ] SP hari ini tetap nomor tanpa suffix
- [ ] ADD dengan DIR format benar
- [ ] ADD tanpa DIR format benar
- [ ] Soft delete dan reuse nomor bekerja
- [ ] Yearly reset bekerja saat tahun baru

### Test Approval
- [ ] User input → status pending
- [ ] Admin input → status approved (otomatis)
- [ ] Admin bisa approve dokumen pending
- [ ] Admin bisa reject dengan alasan
- [ ] User dapat notifikasi approval
- [ ] User dapat notifikasi rejection

### Test MinIO
- [ ] PDF upload berhasil
- [ ] PDF download berhasil
- [ ] File tersimpan di path yang benar
- [ ] Delete dokumen juga delete PDF (soft delete)

### Test Soft Delete
- [ ] Delete dokumen membuat soft delete
- [ ] Reuse nomor dari soft delete
- [ ] forceDelete() bekerja saat reuse

---

## MIGRATION & SETUP

### Database Migration
Semua kolom sudah ada di migration:
- `approval_status` (enum)
- `approved_by` (nullable varchar)
- `approved_at` (nullable timestamp)
- `rejection_reason` (nullable text)
- `deleted_at` (nullable timestamp - soft delete)

Jika perlu add kolom:
```bash
php artisan make:migration add_approval_fields_to_surat_keputusan_table
```

### Running Migration
```bash
php artisan migrate
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## API ENDPOINTS

### User Routes
```
GET    /user/sk              - List SK
GET    /user/sk/create       - Form create SK
POST   /user/sk              - Store SK
GET    /user/sk/{id}         - View SK
GET    /user/sk/{id}/edit    - Form edit SK
PUT    /user/sk/{id}         - Update SK
DELETE /user/sk/{id}         - Delete SK (soft)
GET    /user/sk/{id}/download - Download PDF

(Sama untuk /sp dan /addendum)
```

### Admin Routes
```
GET    /admin/approval              - List pending docs
GET    /admin/approval/{type}/{id}  - View dokumen
POST   /admin/approval/{type}/{id}/approve - Approve
POST   /admin/approval/{type}/{id}/reject  - Reject
GET    /admin/approval/{type}/{id}/download - Download PDF

GET    /admin/documents/sk/create   - Form input SK
POST   /admin/documents/sk/store    - Store SK (auto-approved)
(Sama untuk /sp dan /addendum)
```

---

## TROUBLESHOOTING

### SK Error "Hanya bisa hari ini"
**Masalah**: User input SK tanggal kemarin
**Solusi**: Validasi sudah di `date_equals:today`, pastikan tanggal sistem server benar

### Nomor Tidak Naik
**Masalah**: Nomor SP/ADD masih 001
**Solusi**: Cek apakah data benar-benar tersimpan dengan:
```php
SuratPerjanjian::whereYear('TANGGAL', 2025)->get();
```

### PDF Tidak Bisa Upload
**Masalah**: Error saat upload PDF
**Solusi**: 
1. Pastikan MinIO running: `http://192.168.0.112:9000`
2. Pastikan credentials benar di .env
3. Pastikan bucket `arsip-pusri` sudah ada
4. Check disk config: `config/filesystems.php`

### Approval Tidak Keluar
**Masalah**: Dokumen tidak muncul di halaman Approval
**Solusi**:
1. Cek approval_status di database
2. Query test:
```php
SuratKeputusan::where('approval_status', 'pending')->get();
```

### Soft Delete Tidak Bekerja
**Masalah**: Dokumen yang didelete masih terlihat
**Solusi**: Cek model menggunakan trait SoftDeletes:
```php
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratKeputusan extends Model {
    use SoftDeletes;
}
```

---

## PERFORMA & OPTIMASI

### Database Indexes
Sudah ada di migration:
```sql
INDEX pada (TANGGAL, deleted_at)
INDEX pada (USER)
```

### Query Optimization
- Gunakan `select()` untuk hanya ambil kolom yang perlu
- Gunakan `with()` untuk eager loading relations
- Gunakan pagination untuk list besar

---

## SECURITY

### Authorization
- User hanya bisa lihat/edit dokumen sendiri (check `$document->USER !== Auth::user()->BADGE`)
- Admin check di middleware: `'role:admin'`
- User check di middleware: `'role:user'`

### File Upload
- Validasi format: `mimes:pdf`
- Validasi ukuran: `max:20480` (20MB)
- File disimpan di MinIO (bukan public folder)

### SQL Injection
- Gunakan model methods dan parameterized queries
- Hindari raw SQL

---

## DEPLOYMENT CHECKLIST

- [ ] Pull kode terbaru
- [ ] Run `composer install`
- [ ] Run `php artisan migrate`
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Set environment: APP_ENV=production
- [ ] Configure MinIO credentials
- [ ] Test upload/download PDF
- [ ] Test penomoran
- [ ] Test approval workflow
- [ ] Monitor logs: `tail -f storage/logs/laravel.log`

---

Dokumen ini adalah referensi lengkap untuk implementasi sistem baru. Jika ada yang kurang jelas, silakan tanyakan!
