@extends('layouts.user')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-white mb-0"><i class="bi bi-file-earmark-plus"></i> Tambah Surat Perjanjian</h2>
                    <p class="text-white-50 mb-0">Buat SP baru (bisa backdate)</p>
                </div>
                <a href="{{ route('sp.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Form Surat Perjanjian</h5></div>
        <div class="card-body">
            <form action="{{ route('sp.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="TANGGAL" value="{{ old('TANGGAL', date('Y-m-d')) }}" required>
                            <small class="text-muted">SP bisa backdate</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Format <span class="text-danger">*</span></label>
                            <select class="form-select" name="format" required>
                                <option value="DIR">DIR</option>
                                <option value="NON-DIR">NON-DIR</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Pihak Pertama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="PIHAK_PERTAMA" value="{{ old('PIHAK_PERTAMA', 'PT Pupuk Sriwidjaja - ' . (Auth::user()->Departemen ?? '')) }}" readonly required>
                            <small class="text-muted">Otomatis dari departemen Anda</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Pihak Lain <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="PIHAK_KEDUA" value="{{ old('PIHAK_KEDUA') }}" placeholder="Contoh: PT Indah Kiat Pulp & Paper" required>
                            <small class="text-muted">Bisa PT lain atau pihak eksternal</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Penandatangan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="PENANDATANGAN" value="{{ old('PENANDATANGAN') }}" placeholder="Contoh: Direktur Utama" required>
                            <small class="text-muted">Jabatan atau nama penandatangan</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="UNIT_KERJA" value="{{ old('UNIT_KERJA', Auth::user()->Departemen ?? '') }}" readonly required>
                            <small class="text-muted">Otomatis dari departemen Anda</small>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Perihal <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="PERIHAL" rows="3" placeholder="Jelaskan perihal surat perjanjian" required>{{ old('PERIHAL') }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="TANGGAL_MULAI" value="{{ old('TANGGAL_MULAI') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="TANGGAL_SELESAI" value="{{ old('TANGGAL_SELESAI') }}" required>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload PDF <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="pdf_file" accept=".pdf" required>
                    <small class="text-muted">Max: 20MB</small>
                </div>
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i> <strong>Catatan:</strong>
                    <ul class="mb-0 mt-2">
                        <li>SP bisa backdate</li>
                        <li>Jika tanggal sama, nomor dapat suffix (001A, 001B)</li>
                        <li>Dokumen masuk status <strong>PENDING</strong></li>
                        <li>Nomor yang dihapus di tanggal sama akan dipakai ulang</li>
                    </ul>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                    <a href="{{ route('sp.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
