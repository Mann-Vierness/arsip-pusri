@extends('layouts.admin')
@section('title', 'Edit User')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-pencil"></i> Edit User</h2>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->BADGE) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Badge</label>
                        <input type="text" class="form-control" value="{{ $user->BADGE }}" readonly disabled>
                        <small class="text-muted">Badge tidak dapat diubah</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('Nama') is-invalid @enderror" 
                               name="Nama" value="{{ old('Nama', $user->Nama) }}" required>
                        @error('Nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" placeholder="******">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Minimal 6 karakter jika diisi</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Departemen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('departemen') is-invalid @enderror" 
                               name="departemen" value="{{ old('departemen', $user->Departemen) }}" required>
                        @error('departemen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- FITUR BARU: Pilihan Role -->
                <div class="mb-3">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" name="role" required>
                        <option value="user" {{ old('role', $user->ROLE) == 'user' ? 'selected' : '' }}>User (Regular)</option>
                        <option value="admin" {{ old('role', $user->ROLE) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        <strong>User:</strong> Dapat membuat & mengelola dokumen sendiri. 
                        <strong>Admin:</strong> Dapat approve/reject dokumen & kelola semua user.
                    </small>
                </div>

                @if($user->ROLE == 'admin')
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> 
                    <strong>Perhatian:</strong> User ini saat ini adalah Admin. Pastikan ada admin lain sebelum mengubah role ke User.
                </div>
                @endif

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection