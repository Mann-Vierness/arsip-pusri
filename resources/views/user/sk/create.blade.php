@extends('layouts.user')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-white mb-0"><i class="bi bi-file-earmark-plus"></i> Tambah Surat Keputusan</h2>
                    <p class="text-white-50 mb-0">Buat SK baru (hanya bisa input hari ini)</p>
                </div>
                <a href="{{ route('sk.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="bi bi-pencil-square"></i> Form Surat Keputusan</h5></div>
        <div class="card-body">
            <form action="{{ route('sk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('TANGGAL') is-invalid @enderror" name="TANGGAL" value="{{ old('TANGGAL', date('Y-m-d')) }}" readonly required>
                            @error('TANGGAL')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">SK hanya bisa input tanggal hari ini</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Penandatangan <span class="text-danger">*</span></label>
                            <select class="form-select @error('PENANDATANGAN') is-invalid @enderror" name="PENANDATANGAN" required>
                                <option value="">-- Pilih --</option>
                                <option value="Direktur Utama">Direktur Utama</option>
                                <option value="Direktur Produksi">Direktur Produksi</option>
                                <option value="Direktur Keuangan">Direktur Keuangan</option>
                                <option value="Direktur SDM">Direktur SDM</option>
                            </select>
                            @error('PENANDATANGAN')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Perihal <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('PERIHAL') is-invalid @enderror" name="PERIHAL" rows="3" required>{{ old('PERIHAL') }}</textarea>
                    @error('PERIHAL')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('UNIT_KERJA') is-invalid @enderror" name="UNIT_KERJA" value="{{ old('UNIT_KERJA', Auth::user()->DEPARTEMEN ?? '') }}" readonly required>
                    @error('UNIT_KERJA')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Otomatis dari departemen Anda</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload PDF <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('pdf_file') is-invalid @enderror" name="pdf_file" accept=".pdf" required>
                    @error('pdf_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Max: 20MB</small>
                </div>
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i> <strong>Catatan:</strong>
                    <ul class="mb-0 mt-2">
                        <li>SK hanya bisa input tanggal hari ini</li>
                        <li>Nomor SK di-generate otomatis</li>
                        <li>Dokumen masuk status <strong>PENDING</strong> menunggu approval</li>
                        <li>Jika ada nomor yang sudah dihapus, nomor tersebut akan dipakai ulang</li>
                    </ul>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                    <a href="{{ route('sk.index') }}" class="btn btn-secondary"><i class="bi bi-x"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
