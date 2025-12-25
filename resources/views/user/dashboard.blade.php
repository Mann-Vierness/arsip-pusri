@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header dengan salam -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">
                        <i class="bi bi-house-door-fill"></i> Selamat Datang, {{ strtoupper($user->badge) }}!
                    </h2>
                    <p class="card-text">Sistem Manajemen Dokumen - Dashboard User</p>
                    <p class="text-muted">Terakhir login: {{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center home-card">
                <div class="card-body">
                    <h3><i class="bi bi-file-text text-primary"></i></h3>
                    <h4>{{ $skCount }}</h4>
                    <p class="card-text">Surat Keputusan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center home-card">
                <div class="card-body">
                    <h3><i class="bi bi-file-earmark-text text-success"></i></h3>
                    <h4>{{ $spCount }}</h4>
                    <p class="card-text">Surat Perjanjian</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center home-card">
                <div class="card-body">
                    <h3><i class="bi bi-file-plus text-info"></i></h3>
                    <h4>{{ $addendumCount }}</h4>
                    <p class="card-text">Addendum</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center home-card">
                <div class="card-body">
                    <h3><i class="bi bi-files text-warning"></i></h3>
                    <h4>{{ $skCount + $spCount + $addendumCount }}</h4>
                    <p class="card-text">Total Dokumen</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Approval dan Notifikasi -->
    <div class="row mb-4">
        <!-- Status Approval -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-check-circle"></i> Status Approval Dokumen</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-2">
                                <span class="badge bg-warning fs-6 p-2">{{ $pendingCount }}</span>
                            </div>
                            <small>Pending</small>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <span class="badge bg-success fs-6 p-2">{{ $approvedCount }}</span>
                            </div>
                            <small>Approved</small>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <span class="badge bg-danger fs-6 p-2">{{ $rejectedCount }}</span>
                            </div>
                            <small>Rejected</small>
                        </div>
                    </div>
                    
                    @php
                        $totalDocs = $pendingCount + $approvedCount + $rejectedCount;
                    @endphp
                    
                    @if($totalDocs > 0)
                        @php
                            $pendingPercent = ($pendingCount / $totalDocs) * 100;
                            $approvedPercent = ($approvedCount / $totalDocs) * 100;
                            $rejectedPercent = ($rejectedCount / $totalDocs) * 100;
                        @endphp
                        <div class="progress mt-3" style="height: 20px;">
                            <div class="progress-bar bg-warning" style="width: {{ $pendingPercent }}%" 
                                 title="Pending: {{ round($pendingPercent, 1) }}%"></div>
                            <div class="progress-bar bg-success" style="width: {{ $approvedPercent }}%" 
                                 title="Approved: {{ round($approvedPercent, 1) }}%"></div>
                            <div class="progress-bar bg-danger" style="width: {{ $rejectedPercent }}%" 
                                 title="Rejected: {{ round($rejectedPercent, 1) }}%"></div>
                        </div>
                        <div class="mt-2 small text-muted text-center">
                            Status approval semua dokumen Anda
                        </div>
                    @else
                        <div class="text-center text-muted mt-3">
                            <p>Belum ada dokumen</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notifikasi Terbaru -->
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="bi bi-bell"></i> Notifikasi Terbaru</h5>
                    @if($unreadCount > 0)
                        <span class="badge bg-danger">{{ $unreadCount }} baru</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($recentNotifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentNotifications as $notification)
                                <div class="list-group-item d-flex justify-content-between align-items-start p-2 {{ !$notification->is_read ? 'unread' : '' }}">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold small">{{ $notification->title }}</div>
                                        {{ $notification->message }}
                                    </div>
                                    <span class="badge bg-light text-dark rounded-pill small">
                                        {{ $notification->timeAgo() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-2">
                            <a href="{{ route('user.notifications') }}" class="btn btn-sm btn-outline-light">
                                Lihat Semua Notifikasi
                            </a>
                        </div>
                    @else
                        <p class="text-center text-muted">Tidak ada notifikasi</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dokumen Terbaru -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-clock-history"></i> Dokumen Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($recentDocs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Jenis</th>
                                        <th>Nomor</th>
                                        <th>Tanggal</th>
                                        <th>Perihal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentDocs as $doc)
                                        <tr>
                                            <td>{{ $doc['jenis_text'] }}</td>
                                            <td>{{ $doc['nomor'] }}</td>
                                            <td>{{ \Carbon\Carbon::parse($doc['tanggal'])->format('d/m/Y') }}</td>
                                            <td>{{ \Str::limit($doc['perihal'], 50) }}</td>
                                            <td>
                                                <span class="badge {{ $doc['status_class'] }}">
                                                    {{ ucfirst($doc['approval_status']) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('user.' . $doc['jenis'] . '.index') }}" 
                                                   class="btn btn-sm btn-outline-primary">Lihat</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Belum ada dokumen</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('user.sk.create') }}" class="btn btn-primary me-md-2">
                            <i class="bi bi-plus-circle"></i> Tambah SK
                        </a>
                        <a href="{{ route('user.sp.create') }}" class="btn btn-success me-md-2">
                            <i class="bi bi-plus-circle"></i> Tambah SP
                        </a>
                        <a href="{{ route('user.addendum.create') }}" class="btn btn-info me-md-2">
                            <i class="bi bi-plus-circle"></i> Tambah Addendum
                        </a>
                        <a href="{{ route('user.notifications') }}" class="btn btn-warning">
                            <i class="bi bi-bell"></i> Lihat Notifikasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
