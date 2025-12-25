@extends('layouts.admin')

@section('title', 'Approval Management')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-check-circle"></i> Approval Management</h2>

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filter Status</h5>
                <div class="btn-group">
                    <a href="{{ route('admin.approval.index', ['status' => 'pending']) }}" 
                       class="btn btn-sm btn-{{ $status === 'pending' ? 'warning' : 'outline-warning' }}">
                        Pending
                    </a>
                    <a href="{{ route('admin.approval.index', ['status' => 'approved']) }}" 
                       class="btn btn-sm btn-{{ $status === 'approved' ? 'success' : 'outline-success' }}">
                        Approved
                    </a>
                    <a href="{{ route('admin.approval.index', ['status' => 'rejected']) }}" 
                       class="btn btn-sm btn-{{ $status === 'rejected' ? 'danger' : 'outline-danger' }}">
                        Rejected
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Daftar Dokumen - {{ ucfirst($status) }}</h5>
        </div>
        <div class="card-body">
            @if($documents->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>Nomor</th>
                            <th>Tanggal</th>
                            <th>Perihal</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $index => $doc)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $doc['type_text'] }}</td>
                            <td>{{ $doc['nomor'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($doc['tanggal'])->format('d/m/Y') }}</td>
                            <td>{{ \Str::limit($doc['perihal'], 50) }}</td>
                            <td>{{ $doc['user'] }} ({{ $doc['user_badge'] }})</td>
                            <td>
                                <span class="badge bg-{{ $doc['status'] === 'pending' ? 'warning' : ($doc['status'] === 'approved' ? 'success' : 'danger') }}">
                                    {{ ucfirst($doc['status']) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.approval.show', [$doc['type'], $doc['id']]) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; opacity: 0.3;"></i>
                <h5 class="mt-3">Tidak Ada Dokumen</h5>
                <p class="text-muted">Tidak ada dokumen dengan status {{ $status }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
