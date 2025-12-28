@extends('layouts.admin')

@section('title', 'Tambah Surat Keputusan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-earmark-plus"></i> Tambah Surat Keputusan</h2>
        <a href="{{ route('admin.documents.sk') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Terjadi Kesalahan:</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Form Surat Keputusan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.documents.sk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <!-- Tanggal - Otomatis Hari Ini -->
                    <div class="col-md-6 mb-3">
                        <label for="TANGGAL" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('TANGGAL') is-invalid @enderror" 
                               id="TANGGAL" name="TANGGAL" value="{{ date('Y-m-d') }}" readonly required>
                        @error('TANGGAL')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Tanggal otomatis hari ini</small>
                    </div>

                    <!-- Hidden DIR Field - Otomatis "DIR" -->
                    <input type="hidden" name="DIR" value="DIR">

                    <!-- Penandatangan -->
                    <div class="col-md-6 mb-3">
                        <label for="PENANDATANGAN" class="form-label">Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('PENANDATANGAN') is-invalid @enderror" 
                               id="PENANDATANGAN" name="PENANDATANGAN" value="{{ old('PENANDATANGAN') }}" required>
                        @error('PENANDATANGAN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Perihal -->
                <div class="mb-3">
                    <label for="PERIHAL" class="form-label">Perihal <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('PERIHAL') is-invalid @enderror" 
                              id="PERIHAL" name="PERIHAL" rows="3" required>{{ old('PERIHAL') }}</textarea>
                    @error('PERIHAL')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <!-- Unit Kerja -->
                    <div class="col-md-6 mb-3">
                        <label for="UNIT_KERJA" class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('UNIT_KERJA') is-invalid @enderror" 
                               id="UNIT_KERJA" name="UNIT_KERJA" value="{{ old('UNIT_KERJA') }}" required>
                        @error('UNIT_KERJA')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nama -->
                    <div class="col-md-6 mb-3">
                        <label for="NAMA" class="form-label">Nama Pembuat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('NAMA') is-invalid @enderror" 
                               id="NAMA" name="NAMA" value="{{ old('NAMA', auth()->user()->Nama) }}" required>
                        @error('NAMA')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Upload PDF -->
                <div class="mb-3">
                    <label for="pdf_file" class="form-label">Upload File PDF <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('pdf_file') is-invalid @enderror" 
                           id="pdf_file" name="pdf_file" accept=".pdf" required>
                    @error('pdf_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Maksimal 20MB, format PDF</small>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Informasi:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Tanggal SK otomatis hari ini ({{ date('d/m/Y') }})</li>
                        <li>Jenis SK otomatis "DIR"</li>
                        <li>Nomor SK akan digenerate otomatis dengan format: <strong>SK/DIR/XXX/{{ date('Y') }}</strong></li>
                        <li>Dokumen yang dibuat admin akan otomatis ter-approve</li>
                    </ul>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('admin.documents.sk') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Preview nama file yang dipilih
document.getElementById('pdf_file').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
        const label = e.target.nextElementSibling;
        if (label && label.classList.contains('custom-file-label')) {
            label.textContent = fileName;
        }
    }
});
</script>
@endsection