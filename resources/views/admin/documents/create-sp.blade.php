@extends('layouts.admin')

@section('title', 'Tambah Surat Perjanjian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-earmark-plus"></i> Tambah Surat Perjanjian</h2>
        <a href="{{ route('admin.documents.sp') }}" class="btn btn-secondary">
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
            <h5 class="mb-0">Form Surat Perjanjian</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.documents.sp.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <!-- Tanggal -->
                    <div class="col-md-6 mb-3">
                        <label for="TANGGAL" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('TANGGAL') is-invalid @enderror" 
                               id="TANGGAL" name="TANGGAL" value="{{ old('TANGGAL') }}" required>
                        @error('TANGGAL')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- DIR -->
                    <div class="col-md-6 mb-3">
                        <label for="DIR" class="form-label">DIR <span class="text-danger">*</span></label>
                        <select class="form-select @error('DIR') is-invalid @enderror" id="DIR" name="DIR" required>
                            <option value="">-- Pilih DIR --</option>
                            <option value="PKS" {{ old('DIR') == 'PKS' ? 'selected' : '' }}>PKS</option>
                            <option value="SP" {{ old('DIR') == 'SP' ? 'selected' : '' }}>SP</option>
                        </select>
                        @error('DIR')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Digunakan untuk generate nomor SP</small>
                    </div>

                    <!-- Pihak Pertama -->
                    <div class="col-md-6 mb-3">
                        <label for="PIHAK_PERTAMA" class="form-label">Pihak Pertama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('PIHAK_PERTAMA') is-invalid @enderror" 
                               id="PIHAK_PERTAMA" name="PIHAK_PERTAMA" value="{{ old('PIHAK_PERTAMA', 'PT Pupuk Sriwidjaja Palembang') }}" required>
                        @error('PIHAK_PERTAMA')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pihak Lain -->
                    <div class="col-md-6 mb-3">
                        <label for="PIHAK_LAIN" class="form-label">Pihak Lain <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('PIHAK_LAIN') is-invalid @enderror" 
                               id="PIHAK_LAIN" name="PIHAK_LAIN" value="{{ old('PIHAK_LAIN') }}" required>
                        @error('PIHAK_LAIN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Perihal -->
                    <div class="col-md-12 mb-3">
                        <label for="PERIHAL" class="form-label">Perihal <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('PERIHAL') is-invalid @enderror" 
                                  id="PERIHAL" name="PERIHAL" rows="3" required>{{ old('PERIHAL') }}</textarea>
                        @error('PERIHAL')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Penandatangan -->
                    <div class="col-md-6 mb-3">
                        <label for="PENANDATANGAN" class="form-label">Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('PENANDATANGAN') is-invalid @enderror" 
                               id="PENANDATANGAN" name="PENANDATANGAN" value="{{ old('PENANDATANGAN') }}" required>
                        @error('PENANDATANGAN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

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

                    <!-- Upload PDF -->
                    <div class="col-md-6 mb-3">
                        <label for="pdf_file" class="form-label">Upload File PDF <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('pdf_file') is-invalid @enderror" 
                               id="pdf_file" name="pdf_file" accept=".pdf" required>
                        @error('pdf_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Maksimal 10MB, format PDF</small>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Informasi:</strong> Dokumen yang dibuat oleh admin akan otomatis ter-approve dan nomor SP akan digenerate otomatis berdasarkan DIR dan tanggal.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('admin.documents.sp') }}" class="btn btn-secondary">
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