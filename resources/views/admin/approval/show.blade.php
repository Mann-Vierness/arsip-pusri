@extends('layouts.admin')
@section('title', 'Review Dokumen')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-check"></i> Review Dokumen</h2>
        <a href="{{ route('admin.approval.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Detail Dokumen</h5>
                    <span class="badge bg-{{ $document->approval_status === 'pending' ? 'warning' : ($document->approval_status === 'approved' ? 'success' : 'danger') }} fs-6">
                        {{ ucfirst($document->approval_status) }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Jenis Dokumen</th>
                            <td>: {{ $type === 'sk' ? 'Surat Keputusan' : ($type === 'sp' ? 'Surat Perjanjian' : 'Addendum') }}</td>
                        </tr>
                        <tr>
                            <th>Nomor</th>
                            <td>: <strong>{{ $type === 'sk' ? $document->NOMOR_SK : $document->NO }}</strong></td>
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
                            <th>Dibuat Oleh</th>
                            <td>: {{ $document->user->Nama }} ({{ $document->USER }})</td>
                        </tr>
                        <tr>
                            <th>Dibuat Tanggal</th>
                            <td>: {{ $document->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($document->isRejected())
                    <div class="alert alert-danger mt-3">
                        <h6><i class="bi bi-exclamation-triangle"></i> Alasan Penolakan:</h6>
                        <p class="mb-0">{{ $document->rejection_reason }}</p>
                        <small>Ditolak oleh: {{ $document->approved_by }} pada {{ $document->approved_at->format('d/m/Y H:i') }}</small>
                    </div>
                    @endif

                    @if($document->isApproved())
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-check-circle"></i> Dokumen telah disetujui oleh {{ $document->approved_by }} pada {{ $document->approved_at->format('d/m/Y H:i') }}
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
                    <a href="{{ route('admin.approval.download', [$type, $document->id]) }}" class="btn btn-success btn-sm w-100 mb-2">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    @endif

                    @if($document->isPending())
                    <form action="{{ route('admin.approval.approve', [$type, $document->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menyetujui dokumen ini?')">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm w-100 mb-2">
                            <i class="bi bi-check-circle"></i> Approve
                        </button>
                    </form>

                    <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($document->isPending())
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #003366;">
            <form action="{{ route('admin.approval.reject', [$type, $document->id]) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Alasan Penolakan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" name="rejection_reason" rows="4" placeholder="Masukkan alasan penolakan..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Reject Dokumen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
