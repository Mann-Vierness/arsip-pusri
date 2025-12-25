@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Admin</h2>

    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center home-card">
                <div class="card-body">
                    <h3><i class="bi bi-people text-primary"></i></h3>
                    <h4>{{ $totalUsers }}</h4>
                    <p class="card-text">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center home-card">
                <div class="card-body">
                    <h3><i class="bi bi-files text-success"></i></h3>
                    <h4>{{ $totalDocuments }}</h4>
                    <p class="card-text">Total Dokumen</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center home-card">
                <div class="card-body">
                    <h3><i class="bi bi-clock-history text-warning"></i></h3>
                    <h4>{{ $totalPending }}</h4>
                    <p class="card-text">Pending Approval</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center home-card">
                <div class="card-body">
                    <h3><i class="bi bi-check-circle text-info"></i></h3>
                    <h4>{{ $approvedCount }}</h4>
                    <p class="card-text">Approved</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-clock-history"></i> Dokumen Pending Approval</h5>
                </div>
                <div class="card-body">
                    @if($pendingDocuments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Nomor</th>
                                    <th>Tanggal</th>
                                    <th>Perihal</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingDocuments as $doc)
                                <tr>
                                    <td>{{ $doc['type_text'] }}</td>
                                    <td>{{ $doc['nomor'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($doc['tanggal'])->format('d/m/Y') }}</td>
                                    <td>{{ \Str::limit($doc['perihal'], 40) }}</td>
                                    <td>{{ $doc['user'] }}</td>
                                    <td>
                                        <a href="{{ route('admin.approval.show', [$doc['type'], $doc['id']]) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Review
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-center text-muted">Tidak ada dokumen pending</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
