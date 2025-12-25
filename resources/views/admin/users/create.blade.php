@extends('layouts.admin')
@section('title', 'Tambah User')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person-plus"></i> Tambah User</h2>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Badge <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('badge') is-invalid @enderror" name="badge" value="{{ old('badge') }}" required>
                        @error('badge')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('Nama') is-invalid @enderror" name="Nama" value="{{ old('Nama') }}" required>
                        @error('Nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Departemen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('departemen') is-invalid @enderror" name="departemen" value="{{ old('departemen') }}" required>
                        @error('departemen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
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
