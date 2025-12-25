# Checklist Implementasi Arsip Pusri

## ✅ Konfigurasi
- [x] Database: `data_pusri` (di .env)
- [x] MinIO Bucket: `arsip-pusri` (di .env)
- [x] Flysystem S3 v3 dependency (di composer.json)

## ✅ Sistem Penomoran
- [x] DocumentNumberService.php - Complete rewrite
- [x] generateSKNumber() - SK tanpa backdate
- [x] generateSPNumber() - SP dengan backdate
- [x] generateAddendumNumber() - ADD dengan backdate
- [x] incrementLetter() - Suffix letter A-Z, AA-ZZ
- [x] Soft delete reuse logic
- [x] Yearly reset logic

## ✅ Database Models
- [x] SuratKeputusan - dengan soft delete & approval
- [x] SuratPerjanjian - dengan soft delete & approval
- [x] SuratAddendum - dengan soft delete & approval
- [x] Semua model memiliki relationships dengan User

## ✅ Controllers - User
- [x] SuratKeputusanController - SK validation (today only)
- [x] SuratPerjanjianController - SP backdate support
- [x] SuratAddendumController - ADD backdate support
- [x] BADGE field mapping (bukan badge)
- [x] MinIO PDF upload
- [x] Soft delete (delete method)
- [x] Download PDF

## ✅ Controllers - Admin
- [x] AdminDocumentController - auto-approved for admin input
- [x] ApprovalController - approve/reject logic
- [x] PDF download functionality
- [x] BADGE field mapping

## ✅ Routes
- [x] User routes untuk SK/SP/ADD
- [x] Admin approval routes
- [x] Admin document input routes
- [x] PDF download routes

## ✅ Validasi
- [x] SK hanya hari ini: `date_equals:today`
- [x] SP & ADD: bisa backdate
- [x] PDF wajib upload
- [x] Approval rejection reason validation

## ✅ Soft Delete & Reuse
- [x] Model menggunakan SoftDeletes trait
- [x] onlyTrashed() query untuk reuse
- [x] forceDelete() saat nomor digunakan
- [x] withTrashed() untuk get all dengan soft delete

## ✅ Approval System
- [x] Status enum: pending, approved, rejected
- [x] User input → pending
- [x] Admin input → approved (auto)
- [x] Admin review workflow
- [x] Rejection reason storage

## ✅ MinIO Integration
- [x] Disk configuration di config/filesystems.php
- [x] Path: surat-keputusan/, surat-perjanjian/, surat-addendum/
- [x] Upload menggunakan putFileAs()
- [x] Download menggunakan download()
- [x] Delete menggunakan delete()

## ⚠️ Catatan Penting
- Pastikan MinIO running dan accessible
- Database migration sudah dijalankan
- File PDF harus di-upload (validation required)
- Admin auto-approval hanya untuk admin input
- User input selalu pending hingga di-approve

## 📋 Testing yang Perlu Dilakukan
- [ ] Test SK input hari ini
- [ ] Test SK error backdate (harus hari ini)
- [ ] Test SP backdate kemarin
- [ ] Test ADD dengan DIR parameter
- [ ] Test soft delete & reuse nomor
- [ ] Test approval workflow
- [ ] Test PDF upload ke MinIO
- [ ] Test PDF download
- [ ] Test yearly reset (tahun baru)
- [ ] Test suffix letter (A, B, ..., Z, AA, AB)
- [ ] Test admin auto-approved

## 🔍 Debugging Tips
1. Check soft delete: `SuratKeputusan::onlyTrashed()->get()`
2. Check approval: `SuratKeputusan::where('approval_status', 'pending')->get()`
3. Check MinIO connection: Buka dashboard 192.168.0.112:9000
4. Check nomor terakhir: `SuratKeputusan::orderBy('NOMOR_SK', 'desc')->first()`
5. Check user role: `Auth::user()->ROLE` (bukan role())

## 📝 Migrasi dari Sistem Lama (jika ada)
1. Backup database lama
2. Jalankan migration baru
3. Update data old surat dengan approval_status = 'approved'
4. Update pdf_path jika ada dari storage ke MinIO
5. Verify semua nomor surat

## 🚀 Deployment
1. Pull code terbaru
2. Run: `php artisan migrate`
3. Run: `composer install`
4. Clear cache: `php artisan cache:clear`
5. Test functionality
6. Monitor logs: `tail -f storage/logs/laravel.log`
