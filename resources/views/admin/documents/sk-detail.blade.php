@extends('layouts.admin')

@section('title', 'Detail Surat Keputusan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-text"></i> Detail Surat Keputusan</h2>
        <a href="{{ route('admin.documents.sk') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Informasi Dokumen</h5>
                    <span class="badge {{ $sk->getStatusBadgeClass() }} fs-6">
                        {{ ucfirst($sk->approval_status) }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Nomor SK</th>
                            <td>: <strong>{{ $sk->NOMOR_SK }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>: {{ $sk->TANGGAL->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Perihal</th>
                            <td>: {{ $sk->PERIHAL }}</td>
                        </tr>
                        <tr>
                            <th>Penandatangan</th>
                            <td>: {{ $sk->PENANDATANGAN }}</td>
                        </tr>
                        <tr>
                            <th>Unit Kerja</th>
                            <td>: {{ $sk->UNIT_KERJA }}</td>
                        </tr>
                        <tr>
                            <th>Nama Pembuat</th>
                            <td>: {{ $sk->NAMA }}</td>
                        </tr>
                        <tr>
                            <th>User Badge</th>
                            <td>: {{ $sk->USER }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Tanggal</th>
                            <td>: {{ $sk->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($sk->approved_at)
                        <tr>
                            <th>Disetujui Tanggal</th>
                            <td>: {{ $sk->approved_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Disetujui Oleh</th>
                            <td>: {{ $sk->approved_by }}</td>
                        </tr>
                        @endif
                    </table>

                    @if($sk->isRejected() && $sk->rejection_reason)
                    <div class="alert alert-danger mt-3">
                        <h6><i class="bi bi-exclamation-triangle"></i> Alasan Penolakan:</h6>
                        <p class="mb-0">{{ $sk->rejection_reason }}</p>
                    </div>
                    @endif

                    @if($sk->isApproved())
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-check-circle"></i> Dokumen telah disetujui
                        @if($sk->approved_at)
                            pada {{ $sk->approved_at->format('d/m/Y H:i') }}
                        @endif
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
                    @if($sk->pdf_path)
                    <a href="{{ route('admin.approval.view-pdf', ['sk', $sk->id]) }}" class="btn btn-warning btn-sm w-100 mb-2" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> View PDF
                    </a>
                    <a href="{{ route('admin.documents.sk.download', $sk->id) }}" class="btn btn-success btn-sm w-100 mb-2">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    @endif

                    @if($sk->isPending())
                    <hr>
                    <a href="{{ route('admin.approval.show', ['sk', $sk->id]) }}" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-clipboard-check"></i> Halaman Approval
                    </a>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Status Approval</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        @if($sk->isPending())
                        <i class="bi bi-clock-history text-warning" style="font-size: 3rem;"></i>
                        <h6 class="mt-2">Menunggu Persetujuan</h6>
                        <p class="small text-muted">Dokumen sedang menunggu review</p>
                        @elseif($sk->isApproved())
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <h6 class="mt-2">Disetujui</h6>
                        @if($sk->approved_by)
                            <p class="small text-muted">Oleh: {{ $sk->approved_by }}</p>
                        @endif
                        @else
                        <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                        <h6 class="mt-2">Ditolak</h6>
                        @if($sk->approved_by)
                            <p class="small text-muted">Oleh: {{ $sk->approved_by }}</p>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection