# 📋 CHANGELOG - UI UPDATE

## Version 3.1.0 - UI Enhancement (22 Des 2025)

### 🎨 Added
- ✅ **Dropdown menu** untuk setiap jenis surat (SK/SP/Addendum)
- ✅ **Submenu** dengan 2 opsi: Input dan Lihat
- ✅ **Auto-expand** dropdown saat route aktif
- ✅ **Active state** untuk parent dan child menu
- ✅ **Icons** untuk setiap menu item
- ✅ **Indentation styling** untuk submenu

### 🔄 Changed
- 📱 **User Menu**: Flat menu → Dropdown dengan Input & Lihat
- 👨‍💼 **Admin Menu**: Separate "Input Dokumen" → Grouped per document type
- 🎨 **CSS**: Tambah styling untuk dropdown & submenu

### ⚠️ Breaking Changes
**NONE** - Pure UI update, no backend changes

### 📝 Files Modified
1. `resources/views/layouts/user.blade.php`
2. `resources/views/layouts/admin.blade.php`

### ✅ Testing Status
- [x] Blade syntax valid
- [x] Routes verified exist
- [x] Bootstrap 5.3 compatible
- [x] CSS valid
- [x] HTML5 compliant
- [x] Responsive design maintained
- [x] No errors detected

### 🚀 Deployment
No special steps needed. Just replace layout files.

---

## Version 3.0.0 - Complete Edition (21 Des 2025)

### Features
- Login with Badge Number
- Role-based access (User/Admin)
- Document management (SK/SP/Addendum)
- Approval workflow
- MinIO storage integration
- PDF viewer
- Login history tracking
- User management
- Soft delete
- Notifications

---

**Current Version**: 3.1.0  
**Status**: ✅ Ready for deployment
