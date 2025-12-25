# CHANGELOG - Arsip Pusri Laravel

## [1.1.0] - 2024-12-20

### Changed
- ✅ **Increased PDF upload limit from 10MB to 20MB**
  - Updated validation rules in all controllers (SuratKeputusanController, SuratPerjanjianController, SuratAddendumController)
  - Changed validation from `max:10240` to `max:20480` (in KB)
  - Updated all view forms to show "Maksimal 20MB"
  - Updated documentation (README, INSTALLATION, DEVELOPER_GUIDE)

### Files Modified
**Controllers (3 files):**
- `app/Http/Controllers/User/SuratKeputusanController.php`
- `app/Http/Controllers/User/SuratPerjanjianController.php`
- `app/Http/Controllers/User/SuratAddendumController.php`

**Views (3 files):**
- `resources/views/user/sk/create.blade.php`
- `resources/views/user/sp/create.blade.php`
- `resources/views/user/addendum/create.blade.php`

**Documentation (4 files):**
- `README.md`
- `INSTALLATION.md`
- `DEVELOPER_GUIDE.md`
- `QUICK_START.md`

---

## [1.0.0] - 2024-12-20

### Added
- ✅ Initial release with full features
- ✅ Automatic document numbering (SK, SP, Addendum)
- ✅ Soft delete with number reuse
- ✅ Approval workflow system
- ✅ MinIO integration for PDF storage
- ✅ Role-based access control (Admin/User)
- ✅ Real-time notifications
- ✅ Activity logging
- ✅ Complete CRUD operations
- ✅ Dashboard with statistics
- ✅ Backdate support for SP and Addendum
- ✅ Original UI/UX preserved (Navy theme)
- ✅ Bootstrap 5.3.2 + Bootstrap Icons
- ✅ 70+ files with complete code
- ✅ Full documentation

---

**For detailed information, see README.md**
