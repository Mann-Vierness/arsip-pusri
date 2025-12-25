@extends('layouts.user')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0" style="color:#222"><i class="bi bi-file-earmark-plus"></i> Tambah Surat Perjanjian</h2>
                    <p class="text-muted mb-0">Buat SP baru (bisa backdate)</p>
                </div>
                <a href="{{ route('user.sp.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Form Surat Perjanjian</h5></div>
        <div class="card-body">
            <form action="{{ route('user.sp.store') }}" method="POST" enctype="multipart/form-data" style="font-size:0.92rem;">
                @csrf
                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="TANGGAL" class="form-label">Tanggal Surat Perjanjian</label>
                        <input type="date" class="form-control @error('TANGGAL') is-invalid @enderror" name="TANGGAL" value="{{ old('TANGGAL', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                        @error('TANGGAL') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="DIR" class="form-label">Jenis Surat Perjanjian</label>
                        <select class="form-select @error('DIR') is-invalid @enderror" name="DIR" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="DIR" {{ old('DIR') == 'DIR' ? 'selected' : '' }}>DIR</option>
                            <option value="NON DIR" {{ old('DIR') == 'NON DIR' ? 'selected' : '' }}>Non DIR</option>
                        </select>
                        @error('DIR') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="PIHAK_PERTAMA" class="form-label" style="font-size:0.92rem;">Pihak Pertama</label>
                    <input type="text" class="form-control @error('PIHAK_PERTAMA') is-invalid @enderror" name="PIHAK_PERTAMA" value="{{ old('PIHAK_PERTAMA') }}" required style="font-size:0.92rem;">
                    @error('PIHAK_PERTAMA') <div class="invalid-feedback" style="font-size:0.92rem;">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="PIHAK_LAIN" class="form-label" style="font-size:0.92rem;">Pihak Lain</label>
                    <input type="text" class="form-control @error('PIHAK_LAIN') is-invalid @enderror" name="PIHAK_LAIN" value="{{ old('PIHAK_LAIN') }}" required style="font-size:0.92rem;">
                    @error('PIHAK_LAIN') <div class="invalid-feedback" style="font-size:0.92rem;">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="PERIHAL" class="form-label" style="font-size:0.92rem;">Perihal</label>
                    <textarea class="form-control @error('PERIHAL') is-invalid @enderror" name="PERIHAL" rows="3" required style="font-size:0.92rem;">{{ old('PERIHAL') }}</textarea>
                    @error('PERIHAL') <div class="invalid-feedback" style="font-size:0.92rem;">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="PENANDATANGAN" class="form-label" style="font-size:0.92rem;">Penandatangan</label>
                    <input type="text" class="form-control @error('PENANDATANGAN') is-invalid @enderror" name="PENANDATANGAN" value="{{ old('PENANDATANGAN') }}" required style="font-size:0.92rem;">
                    @error('PENANDATANGAN') <div class="invalid-feedback" style="font-size:0.92rem;">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="UNIT_KERJA" class="form-label" style="font-size:0.92rem;">Unit Kerja</label>
                    <input type="text" class="form-control @error('UNIT_KERJA') is-invalid @enderror" name="UNIT_KERJA" value="{{ old('UNIT_KERJA', Auth::user()->Departemen ?? Auth::user()->DEPARTEMEN ?? '') }}" readonly required style="font-size:0.92rem;">
                    @error('UNIT_KERJA') <div class="invalid-feedback" style="font-size:0.92rem;">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="NAMA" class="form-label">Nama Penginput</label>
                    <input type="text" class="form-control" name="NAMA" value="{{ Auth::user()->Nama }}" required>
                </div>
                <div class="mb-3">
                    <label for="pdf_file" class="form-label" style="font-size:0.92rem;">File PDF</label>
                    <input type="file" class="form-control @error('pdf_file') is-invalid @enderror" name="pdf_file" accept="application/pdf" required style="font-size:0.92rem;">
                    @error('pdf_file') <div class="invalid-feedback" style="font-size:0.92rem;">{{ $message }}</div> @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" style="font-size:0.92rem;"><i class="bi bi-save"></i> Simpan</button>
                    <a href="{{ route('user.sp.index') }}" class="btn btn-secondary" style="font-size:0.92rem;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
