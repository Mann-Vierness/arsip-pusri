@extends('layouts.user')

@section('title', 'Detail Surat Perjanjian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-text"></i> Detail Surat Perjanjian</h2>
        <a href="{{ route('user.sp.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Informasi Dokumen</h5>
                    <span class="badge {{ $document->getStatusBadgeClass() }} fs-6">
                        {{ ucfirst($document->approval_status) }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Nomor SK</th>
                            <td>: <strong>{{ $document->NO }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>: {{ $document->TANGGAL->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Perihal</th>
                            <td>: {{ $document->PERIHAL }}</td>
                        </tr>
                        <tr>
                            <th>Penandatangan</th>
                            <td>: {{ $document->PENANDATANGAN }}</td>
                        </tr>
                        <tr>
                            <th>Unit Kerja</th>
                            <td>: {{ $document->UNIT_KERJA }}</td>
                        </tr>
                        <tr>
                            <th>Nama Pembuat</th>
                            <td>: {{ $document->NAMA }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Tanggal</th>
                            <td>: {{ $document->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($document->isRejected() && $document->rejection_reason)
                    <div class="alert alert-danger mt-3">
                        <h6><i class="bi bi-exclamation-triangle"></i> Alasan Penolakan:</h6>
                        <p class="mb-0">{{ $document->rejection_reason }}</p>
                    </div>
                    @endif

                    @if($document->isApproved())
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-check-circle"></i> Dokumen telah disetujui oleh admin
                        pada {{ $document->approved_at->format('d/m/Y H:i') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Aksi</h5>
                </div>
                <div class="card-body">
                    @if($document->pdf_path)
                    <a href="{{ route('user.sp.download', $document->id) }}" class="btn btn-success btn-sm w-100 mb-2">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    <button class="btn btn-info btn-sm w-100 mb-2" type="button" onclick="togglePdfViewer('pdf-viewer-sp')">
                        <i class="bi bi-eye"></i> Lihat PDF
                    </button>
                    <div id="pdf-viewer-sp" style="display:none;">
                        @php
                            $pdfUrl = route('user.sp.download', $document->id) . '?view=1';
                        @endphp
                        @include('user.partials.pdf-viewer', ['pdfUrl' => $pdfUrl])
                    </div>
                    <script>
                    function togglePdfViewer(id) {
                        var el = document.getElementById(id);
                        if (el.style.display === 'none') {
                            el.style.display = 'block';
                        } else {
                            el.style.display = 'none';
                        }
                    }
                    </script>
                    @endif

                    @if($document->isPending())
                    <a href="{{ route('user.sp.edit', $document->id) }}" class="btn btn-warning btn-sm w-100 mb-2">
                        <i class="bi bi-pencil"></i> Edit
                    </a>

                    <form action="{{ route('user.sp.destroy', $document->id) }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </form>
                    @else
                    <div class="alert alert-info small">
                        Dokumen yang sudah {{ $document->approval_status }} tidak dapat diedit atau dihapus.
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Status Approval</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        @if($document->isPending())
                        <i class="bi bi-clock-history text-warning" style="font-size: 3rem;"></i>
                        <h6 class="mt-2">Menunggu Persetujuan</h6>
                        <p class="small text-muted">Dokumen sedang menunggu review dari admin</p>
                        @elseif($document->isApproved())
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <h6 class="mt-2">Disetujui</h6>
                        <p class="small text-muted">Oleh: {{ $document->approved_by }}</p>
                        @else
                        <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                        <h6 class="mt-2">Ditolak</h6>
                        <p class="small text-muted">Oleh: {{ $document->approved_by }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
