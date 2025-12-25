@extends('layouts.admin')

@section('title', 'Input Surat Perjanjian')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5>Input Surat Perjanjian</h5>
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
                    <label for="PIHAK_PERTAMA" class="form-label">Pihak Pertama</label>
                    <input type="text" class="form-control @error('PIHAK_PERTAMA') is-invalid @enderror" name="PIHAK_PERTAMA" value="{{ old('PIHAK_PERTAMA') }}" required>
                    @error('PIHAK_PERTAMA') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="PIHAK_LAIN" class="form-label">Pihak Lain</label>
                    <input type="text" class="form-control @error('PIHAK_LAIN') is-invalid @enderror" name="PIHAK_LAIN" value="{{ old('PIHAK_LAIN') }}" required>
                    @error('PIHAK_LAIN') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="PERIHAL" class="form-label">Perihal</label>
                    <textarea class="form-control @error('PERIHAL') is-invalid @enderror" name="PERIHAL" rows="3" required>{{ old('PERIHAL') }}</textarea>
                    @error('PERIHAL') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="PENANDATANGAN" class="form-label">Penandatangan</label>
                    <input type="text" class="form-control @error('PENANDATANGAN') is-invalid @enderror" name="PENANDATANGAN" value="{{ old('PENANDATANGAN') }}" required>
                    @error('PENANDATANGAN') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="UNIT_KERJA" class="form-label">Unit Kerja</label>
                    <input type="text" class="form-control @error('UNIT_KERJA') is-invalid @enderror" name="UNIT_KERJA" value="{{ old('UNIT_KERJA') }}" required>
                    @error('UNIT_KERJA') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="NAMA" class="form-label">Nama Dokumen</label>
                    <input type="text" class="form-control @error('NAMA') is-invalid @enderror" name="NAMA" value="{{ old('NAMA') }}" required>
                    @error('NAMA') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="pdf_file" class="form-label">File PDF</label>
                    <input type="file" class="form-control @error('pdf_file') is-invalid @enderror" name="pdf_file" accept="application/pdf" required>
                    @error('pdf_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan SP</button>
            </form>
        </div>
    </div>
</div>
@endsection
