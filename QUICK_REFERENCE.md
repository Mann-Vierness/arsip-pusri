# QUICK REFERENCE - ARSIP PUSRI

## 🎯 Fitur yang Diimplementasikan

### 1. Format Nomor Surat
```
Surat Keputusan (SK)     : SK/DIR/001/2025
Surat Perjanjian (SP)    : -001/SP/DIR/2025 atau -001/SP/2025
Addendum Perjanjian (ADD): -001/ADD-DIR/2025 atau -001/ADD/2025
```

### 2. Aturan Input
```
SK  : Hanya hari ini (date_equals:today)
SP  : Boleh backdate (tanggal sebelumnya)
ADD : Boleh backdate (tanggal sebelumnya)
```

### 3. Nomor dengan Backdate
```
Hari 1: 001
Hari 2: 001A, 001B, 002
Hari 3: 001C, 002A, 003
Dst...
```

### 4. Soft Delete & Reuse
```
Delete nomor 001 → Soft delete
Input baru → Ambil nomor 001 (reuse)
```

### 5. Approval
```
User input → pending
Admin approve → approved
Admin reject → rejected + alasan
Admin input → approved (auto)
```

### 6. MinIO
```
Bucket: arsip-pusri
Path: surat-keputusan/, surat-perjanjian/, surat-addendum/
Format: SK_NOMOR_WAKTU.pdf
```

---

## 📂 File-File Penting

| File | Perubahan |
|------|-----------|
| `app/Services/DocumentNumberService.php` | Rewrite complete |
| `app/Http/Controllers/User/SuratKeputusanController.php` | Update |
| `app/Http/Controllers/User/SuratPerjanjianController.php` | Update |
| `app/Http/Controllers/User/SuratAddendumController.php` | Update |
| `app/Http/Controllers/Admin/AdminDocumentController.php` | Update |
| `app/Http/Controllers/Admin/ApprovalController.php` | Update |

---

## 🔑 Kode Penting

### Generate Nomor SK
```php
$nomor = DocumentNumberService::generateSKNumber($tanggal);
// Output: SK/DIR/001/2025
```

### Generate Nomor SP/ADD dengan Backdate
```php
$nomor_sp = DocumentNumberService::generateSPNumber($tanggal, $dir);
// Output: -001/SP/DIR/2025 atau -001/SP/2025A

$nomor_add = DocumentNumberService::generateAddendumNumber($tanggal, $dir);
// Output: -001/ADD-DIR/2025 atau -001/ADD/2025B
```

### Upload PDF ke MinIO
```php
$file = $request->file('pdf_file');
$path = Storage::disk('minio')->putFileAs('surat-keputusan', $file, $fileName);
```

### Approve Dokumen
```php
$document->update([
    'approval_status' => 'approved',
    'approved_by' => Auth::user()->BADGE,
    'approved_at' => now(),
]);
```

### Soft Delete Reuse
```php
// Cek soft delete
$deleted = SuratKeputusan::onlyTrashed()
    ->whereDate('TANGGAL', $tanggal)
    ->first();

if ($deleted) {
    $nomor = $deleted->NOMOR_SK;
    $deleted->forceDelete();
}
```

---

## 🧪 Testing Cepat

### Test SK
```bash
1. Login sebagai user
2. Input SK tanggal hari ini → ✅
3. Input SK tanggal kemarin → ❌
4. Delete → Soft delete
5. Input SK lagi hari ini → Reuse nomor
```

### Test SP Backdate
```bash
1. Input SP tanggal 2025-01-20
2. Input SP tanggal 2025-01-20 lagi (backdate) → Dapat suffix A
3. Input SP tanggal 2025-01-20 lagi (backdate) → Dapat suffix B
```

### Test Approval
```bash
1. User input → Status pending
2. Login admin → Lihat di Approval
3. Admin approve → Status approved
4. User dapat notifikasi
```

---

## 🐛 Debugging

### Lihat Soft Delete
```php
SuratKeputusan::onlyTrashed()->get()
```

### Lihat Pending Approval
```php
SuratKeputusan::where('approval_status', 'pending')->get()
```

### Lihat Nomor Terakhir
```php
SuratKeputusan::orderBy('NOMOR_SK', 'desc')->first()
```

### Check MinIO Files
```php
Storage::disk('minio')->files('surat-keputusan/')
```

---

## ⚡ Validasi

| Field | Validasi |
|-------|----------|
| SK Tanggal | `date_equals:today` |
| SP/ADD Tanggal | `date` |
| PDF | `mimes:pdf\|max:20480` |
| Approval Reason | `min:10\|max:500` |

---

## 📊 Database Fields

```sql
-- Nomor
NOMOR_SK (SK) / NO (SP, ADD)

-- Tanggal
TANGGAL

-- User
USER → BADGE (bukan id)

-- Approval
approval_status → enum('pending', 'approved', 'rejected')
approved_by → BADGE
approved_at → timestamp
rejection_reason → text

-- Soft Delete
deleted_at → timestamp
```

---

## 🚀 Deployment Cepat

```bash
# 1. Pull code
git pull

# 2. Install
composer install

# 3. Migrate
php artisan migrate

# 4. Clear cache
php artisan cache:clear

# 5. Test
php artisan tinker
>>> Auth::user()
>>> Storage::disk('minio')->files('/')
```

---

## 📞 Dokumentasi Referensi

- **SUMMARY_IMPLEMENTATION.md** ← Mulai dari sini!
- **SYSTEM_DOCUMENTATION.md** - Teknis lengkap
- **IMPLEMENTATION_GUIDE.md** - Panduan step-by-step
- **IMPLEMENTATION_CHECKLIST.md** - Verifikasi

---

## ✅ Checklist Startup

- [ ] Git pull latest code
- [ ] Composer install
- [ ] Database migration
- [ ] Clear cache
- [ ] Test login
- [ ] Test SK input (harus hari ini)
- [ ] Test SP input (backdate)
- [ ] Test PDF upload
- [ ] Test approval workflow
- [ ] Test MinIO access

---

## 🎯 User Paths

### User Flow
```
Login → Dashboard → Pilih (SK/SP/ADD)
     → Create form → Upload PDF → Submit
     → Status: Pending → Approval by Admin
     → Status: Approved/Rejected → Notifikasi
```

### Admin Flow
```
Login → Dashboard → Approval menu
     → Review dokumen → Approve/Reject
     → atau Input dokumen baru (auto-approved)
     → View all documents dari semua user
```

---

## 🔒 Security

- User hanya bisa lihat dokumen sendiri
- PDF hanya bisa diunggah dalam format PDF
- Size maksimal 20MB
- Admin role check di middleware
- User role check di middleware
- Authorization check di controller

---

Implementasi selesai! Sistem siap production. 🚀
