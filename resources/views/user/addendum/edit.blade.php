@extends('layouts.user')
@section('title', 'Edit Addendum')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-pencil"></i> Edit Addendum</h2>
        <a href="{{ route('user.addendum.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> Nomor Addendum: <strong>{{ $document->NO }}</strong> (tidak dapat diubah)
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terdapat kesalahan:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('user.addendum.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="text" class="form-control" value="{{ $document->TANGGAL->format('d/m/Y') }}" readonly disabled>
                        <small class="text-muted">Tanggal tidak dapat diubah</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis</label>
                        <input type="text" class="form-control" value="{{ strpos($document->NO, 'DIR') !== false ? 'DIR' : 'NON DIR' }}" readonly disabled>
                        <small class="text-muted">Jenis tidak dapat diubah</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pihak Pertama <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('PIHAK_PERTAMA') is-invalid @enderror" 
                           name="PIHAK_PERTAMA" value="{{ old('PIHAK_PERTAMA', $document->PIHAK_PERTAMA) }}" required>
                    @error('PIHAK_PERTAMA')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Pihak Lain <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('PIHAK_LAIN') is-invalid @enderror" 
                           name="PIHAK_LAIN" value="{{ old('PIHAK_LAIN', $document->PIHAK_LAIN) }}" required>
                    @error('PIHAK_LAIN')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Perihal <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('PERIHAL') is-invalid @enderror" 
                              name="PERIHAL" rows="4" required>{{ old('PERIHAL', $document->PERIHAL) }}</textarea>
                    @error('PERIHAL')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Penandatangan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('PENANDATANGAN') is-invalid @enderror" 
                           name="PENANDATANGAN" value="{{ old('PENANDATANGAN', $document->PENANDATANGAN) }}" required>
                    @error('PENANDATANGAN')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('UNIT_KERJA') is-invalid @enderror" 
                               name="UNIT_KERJA" value="{{ old('UNIT_KERJA', $document->UNIT_KERJA) }}" required readonly>
                        @error('UNIT_KERJA')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('NAMA') is-invalid @enderror" 
                               name="NAMA" value="{{ old('NAMA', $document->NAMA) }}" required readonly>
                        @error('NAMA')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ganti PDF (opsional)</label>
                    @if($document->pdf_path)
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="bi bi-file-pdf"></i> File saat ini: {{ basename($document->pdf_path) }}
                        </small>
                    </div>
                    @endif
                    <input type="file" class="form-control @error('pdf_file') is-invalid @enderror" 
                           name="pdf_file" accept="application/pdf">
                    @error('pdf_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah file PDF</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('user.addendum.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection