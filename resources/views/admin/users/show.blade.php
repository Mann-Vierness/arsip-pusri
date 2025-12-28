@extends('layouts.admin')
@section('title', 'Detail User')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person"></i> Detail User</h2>
        <div class="btn-group">
            <a href="{{ route('admin.users.edit', $user->BADGE) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Informasi User</h5></div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="120">Badge</th>
                            <td>: {{ $user->BADGE }}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>: {{ $user->Nama }}</td>
                        </tr>
                        <tr>
                            <th>Departemen</th>
                            <td>: {{ $user->Departemen }}</td>
                        </tr>
                         <tr>
                            <th>Role</th>
                            <td>: 
                                @if($user->ROLE == 'admin')
                                    <span class="badge bg-danger">
                                        <i class="bi bi-shield-check"></i> Admin
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        <i class="bi bi-person"></i> User
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>: {{ $user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Statistik Dokumen</h5></div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h3 class="text-primary">{{ $skCount }}</h3>
                                <p class="mb-0">Surat Keputusan</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h3 class="text-success">{{ $spCount }}</h3>
                                <p class="mb-0">Surat Perjanjian</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h3 class="text-info">{{ $addendumCount }}</h3>
                                <p class="mb-0">Surat Addendum</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><h5 class="mb-0">Dokumen Terbaru</h5></div>
                <div class="card-body">
                    @if($recentDocuments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Nomor</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentDocuments as $doc)
                                <tr>
                                    <td>{{ class_basename($doc) }}</td>
                                    <td>{{ $doc->NOMOR_SK ?? $doc->NO }}</td>
                                    <td>{{ $doc->TANGGAL->format('d/m/Y') }}</td>
                                    <td><span class="badge {{ $doc->getStatusBadgeClass() }}">{{ strtoupper($doc->approval_status) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-center text-muted py-3">Belum ada dokumen</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
