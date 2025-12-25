# Arsip Pusri - Sistem Manajemen Dokumen

## Dokumentasi Sistem Penomoran dan Approval

### Ringkasan Fitur Utama

Sistem telah diperbaharui dengan fitur-fitur berikut:

1. **Sistem Penomoran Otomatis** untuk Surat Keputusan, Surat Perjanjian, dan Addendum Perjanjian
2. **Sistem Approval** dengan status pending/approved/rejected
3. **Integrasi MinIO** untuk penyimpanan PDF
4. **Soft Delete** dengan reuse nomor yang sudah dihapus
5. **Backdate** untuk dokumen tertentu dengan logika suffix letter (A-Z, AA-ZZ)

---

## 1. Format Nomor Surat

### Surat Keputusan (SK)
Format: `SK/DIR/NOMOR/TAHUN`
Contoh: `SK/DIR/001/2025`

**Karakteristik:**
- Hanya bisa di-input untuk tanggal hari ini
- Tidak boleh backdate
- Nomor otomatis setiap hari dimulai dari 001
- Admin ketika input langsung di-approved

### Surat Perjanjian (SP)
Format (dengan DIR): `-NOMOR/SP/DIR/TAHUN`
Format (tanpa DIR): `-NOMOR/SP/TAHUN`
Contoh: `-001/SP/DIR/2025` atau `-001/SP/2025`

**Karakteristik:**
- Boleh backdate ke tanggal sebelumnya
- Nomor direset setiap tahun dimulai dari 001
- Jika sudah ada nomor 001 kemudian dinaikkan menjadi 002, maka jika input backdate akan diambil nomor 001 dengan suffix letter
- Suffix letter: A, B, C, ..., Z, AA, AB, ..., AZ, BA, ..., ZZ

### Surat Addendum Perjanjian (ADD)
Format (dengan DIR): `-NOMOR/ADD-DIR/TAHUN`
Format (tanpa DIR): `-NOMOR/ADD/TAHUN`
Contoh: `-001/ADD-DIR/2025` atau `-001/ADD/2025`

**Karakteristik:**
- Sama seperti Surat Perjanjian (boleh backdate)
- Nomor direset setiap tahun
- Menggunakan suffix letter untuk backdate

---

## 2. Logika Penomoran Detail

### Untuk Dokumen Hari Ini (SK, SP hari ini)

1. Cek soft delete dari hari ini
   - Jika ada, gunakan nomor dari soft delete dan hapus record
   - Jika tidak ada, lanjut ke langkah 2

2. Ambil nomor terakhir dari hari ini
   - Jika tidak ada: gunakan nomor 001
   - Jika ada: naikkan angka (001 → 002) tanpa suffix

### Untuk Dokumen Backdate (SP, ADD tanggal sebelumnya)

1. Cek soft delete dari tanggal yang sama
   - Jika ada, gunakan nomor dari soft delete
   - Jika tidak ada, lanjut ke langkah 2

2. Cari anchor (nomor terakhir dengan tanggal ≤ tanggal input)
   - Jika tidak ada: gunakan nomor 001 dengan suffix A
   - Jika ada: ambil nomor dan suffix dari anchor

3. Jika tanggal input adalah hari ini:
   - Cari semua suffix yang sudah dipakai untuk nomor itu di hari ini
   - Gunakan suffix berikutnya (jika tidak ada = '', jika ada = A, B, C, dst)
   - Jika semua suffix sudah terpakai sampai ZZ, error

---

## 3. Soft Delete dan Reuse Nomor

**Skenario:**
```
2025-01-15:
- User input 5 dokumen → nomor: 001, 002, 003, 004, 005
- User hapus 3 dokumen (001, 002, 003 menjadi soft delete)
- Sekarang: 004, 005 active, 001, 002, 003 soft delete

2025-01-15 (kemudian):
- User input lagi → sistem mengambil dari soft delete: 001
- Status: 001 (active), 004, 005 active, 002, 003 soft delete

- User input lagi → sistem mengambil dari soft delete: 002
- Status: 001, 002 (active), 004, 005 active, 003 soft delete
```

**Implementasi:**
- Model menggunakan `SoftDeletes` trait
- Setiap create, cek `onlyTrashed()` dulu
- Jika ada, gunakan nomor tersebut dan `forceDelete()` record soft delete

---

## 4. Sistem Approval

### Status Approval
- `pending`: Menunggu persetujuan admin
- `approved`: Sudah disetujui admin
- `rejected`: Ditolak oleh admin dengan alasan

### Workflow Approval

**User Input Dokumen:**
1. User input data dokumen
2. Upload file PDF → tersimpan ke MinIO
3. Approval status: `pending`
4. Notifikasi ke semua admin

**Admin Review:**
1. Admin lihat dokumen di halaman Approval
2. Admin bisa:
   - **Approve**: Status berubah ke `approved`, notifikasi ke user
   - **Reject**: Status berubah ke `rejected`, notifikasi ke user dengan alasan

### Admin Input (Auto-Approved)
Ketika admin input dokumen:
1. Sistem langsung generate nomor
2. PDF diupload ke MinIO
3. Status: `approved` (langsung)
4. `approved_by`: Badge admin yang input
5. `approved_at`: Timestamp sekarang

---

## 5. Integrasi MinIO

### Konfigurasi
File: `.env`
```dotenv
MINIO_ENDPOINT=http://192.168.0.112:9000
MINIO_KEY=myuser
MINIO_SECRET=mypassword
MINIO_REGION=us-east-1
MINIO_BUCKET=arsip-pusri
MINIO_USE_PATH_STYLE_ENDPOINT=true
```

### Path Penyimpanan
- Surat Keputusan: `surat-keputusan/SK_*.pdf`
- Surat Perjanjian: `surat-perjanjian/SP_*.pdf`
- Surat Addendum: `surat-addendum/ADD_*.pdf`

### Operasi
- **Upload**: `Storage::disk('minio')->putFileAs()`
- **Download**: `Storage::disk('minio')->download()`
- **Delete**: `Storage::disk('minio')->delete()`

---

## 6. Database Schema

### Tabel: surat_keputusan
```sql
CREATE TABLE surat_keputusan (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    NOMOR_SK VARCHAR(100) UNIQUE,
    TANGGAL DATE,
    PERIHAL TEXT,
    PENANDATANGAN VARCHAR(100),
    UNIT_KERJA VARCHAR(100),
    NAMA VARCHAR(100),
    USER VARCHAR(50) FOREIGN KEY,
    pdf_path VARCHAR(255),
    approval_status ENUM('pending', 'approved', 'rejected'),
    approved_by VARCHAR(50),
    approved_at TIMESTAMP,
    rejection_reason TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP (soft delete)
);
```

### Tabel: surat_perjanjian
```sql
CREATE TABLE surat_perjanjian (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    NO VARCHAR(100) UNIQUE,
    TANGGAL DATE,
    PIHAK_PERTAMA VARCHAR(200),
    PIHAK_LAIN VARCHAR(200),
    PERIHAL TEXT,
    PENANDATANGAN VARCHAR(100),
    UNIT_KERJA VARCHAR(100),
    NAMA VARCHAR(100),
    USER VARCHAR(50) FOREIGN KEY,
    pdf_path VARCHAR(255),
    approval_status ENUM('pending', 'approved', 'rejected'),
    approved_by VARCHAR(50),
    approved_at TIMESTAMP,
    rejection_reason TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP (soft delete)
);
```

### Tabel: surat_addendum
```sql
CREATE TABLE surat_addendum (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    NO VARCHAR(100) UNIQUE,
    NOMOR_PERJANJIAN_ASAL VARCHAR(100),
    TANGGAL DATE,
    PIHAK_PERTAMA VARCHAR(200),
    PIHAK_LAIN VARCHAR(200),
    PERIHAL TEXT,
    PERUBAHAN TEXT,
    PENANDATANGAN VARCHAR(100),
    UNIT_KERJA VARCHAR(100),
    NAMA VARCHAR(100),
    USER VARCHAR(50) FOREIGN KEY,
    pdf_path VARCHAR(255),
    approval_status ENUM('pending', 'approved', 'rejected'),
    approved_by VARCHAR(50),
    approved_at TIMESTAMP,
    rejection_reason TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP (soft delete)
);
```

---

## 7. File-File yang Diubah/Dibuat

### Services
- **`app/Services/DocumentNumberService.php`**: Rewrite complete dengan logika penomoran baru

### Controllers - User
- **`app/Http/Controllers/User/SuratKeputusanController.php`**: Update untuk validasi tanggal hari ini
- **`app/Http/Controllers/User/SuratPerjanjianController.php`**: Update untuk support backdate dan DIR
- **`app/Http/Controllers/User/SuratAddendumController.php`**: Update untuk support backdate dan DIR

### Controllers - Admin
- **`app/Http/Controllers/Admin/AdminDocumentController.php`**: Update untuk support DIR parameter
- **`app/Http/Controllers/Admin/ApprovalController.php`**: Update untuk field mapping yang benar

### Models
- `app/Models/SuratKeputusan.php`: Sudah memiliki soft delete dan approval fields
- `app/Models/SuratPerjanjian.php`: Sudah memiliki soft delete dan approval fields
- `app/Models/SuratAddendum.php`: Sudah memiliki soft delete dan approval fields

### Routes
- **`routes/web.php`**: Sudah memiliki semua route yang diperlukan

---

## 8. Testing Penomoran

### Test Case 1: SK Hari Ini
```
1. Input SK tanggal 2025-01-20 → nomor: SK/DIR/001/2025
2. Input SK tanggal 2025-01-20 → nomor: SK/DIR/002/2025
3. Delete yang pertama (soft delete)
4. Input SK tanggal 2025-01-20 → nomor: SK/DIR/001/2025 (reuse)
```

### Test Case 2: SP dengan Backdate
```
2025-01-20:
1. Input SP tanggal 2025-01-20 → nomor: -001/SP/2025

2025-01-21:
2. Input SP tanggal 2025-01-20 → nomor: -001/SP/2025A (backdate)
3. Input SP tanggal 2025-01-20 → nomor: -001/SP/2025B (backdate)
4. Input SP tanggal 2025-01-21 → nomor: -001/SP/2025 (hari ini)
```

### Test Case 3: ADD dengan DIR
```
1. Input ADD tanggal 2025-01-20, DIR="Y" → nomor: -001/ADD-DIR/2025
2. Input ADD tanggal 2025-01-20, DIR=null → nomor: -001/ADD/2025
```

---

## 9. User Interface

### User Dashboard
- **Surat Keputusan**: Menampilkan semua SK milik user (sesuai USER field)
- **Surat Perjanjian**: Menampilkan semua SP milik user
- **Surat Addendum**: Menampilkan semua ADD milik user

Setiap tabel menampilkan:
- Nomor Surat
- Tanggal
- Status Approval
- Aksi (View, Edit, Delete, Download)

### Admin Dashboard
- **Approval Inbox**: Menampilkan semua dokumen pending
- **All Surat Keputusan**: Menampilkan semua SK (dari semua user)
- **All Surat Perjanjian**: Menampilkan semua SP
- **All Surat Addendum**: Menampilkan semua ADD

Setiap dokumen bisa di-approve atau di-reject dengan notifikasi ke user.

---

## 10. Notifikasi

Sistem menggunakan `NotificationService`:
- User notifikasi ketika dokumennya di-approve
- User notifikasi ketika dokumennya di-reject dengan alasan
- Admin notifikasi ketika ada dokumen pending

---

## 11. Catatan Penting

1. **Database**: Sudah menggunakan `data_pusri` (sesuai .env)
2. **MinIO Bucket**: Gunakan `arsip-pusri` (sesuai .env)
3. **Soft Delete**: Semua model sudah menggunakan `SoftDeletes` trait
4. **Approval Status**: Default semua user input adalah `pending`
5. **Admin Input**: Langsung di-set ke `approved`
6. **Yearly Reset**: Nomor direset otomatis saat tahun berubah (via `whereYear()`)
7. **PDF Upload**: Wajib, file disimpan ke MinIO dengan naming konvensi

---

## 12. Cara Menggunakan Sistem

### User Input Dokumen
1. Masuk ke menu dokumen (SK/SP/ADD)
2. Klik "Tambah Dokumen Baru"
3. Isi form dengan data dokumen
4. Upload file PDF (wajib)
5. Klik "Simpan"
6. Dokumen masuk status `pending`, menunggu approval admin

### Admin Review Dokumen
1. Masuk ke menu "Approval"
2. Lihat dokumen-dokumen pending
3. Klik dokumen untuk melihat detail
4. Klik "Approve" untuk menyetujui
   - atau "Reject" dengan alasan penolakan
5. User akan menerima notifikasi

### Admin Input Dokumen
1. Masuk ke menu "Input Dokumen" (ada di admin)
2. Pilih jenis dokumen (SK/SP/ADD)
3. Isi form dengan data
4. Upload PDF
5. Klik "Simpan"
6. Dokumen langsung `approved` (tidak perlu review)

---

## 13. API Response

### Success Create Dokumen
```json
{
    "success": true,
    "message": "Surat Keputusan berhasil dibuat dengan nomor: SK/DIR/001/2025",
    "nomor": "SK/DIR/001/2025"
}
```

### Error Backdate SK
```json
{
    "success": false,
    "message": "SK hanya bisa diinput untuk tanggal hari ini"
}
```

### Success Approval
```json
{
    "success": true,
    "message": "Dokumen berhasil disetujui"
}
```

---

Dokumen ini akan terus diperbarui seiring dengan perkembangan sistem.
