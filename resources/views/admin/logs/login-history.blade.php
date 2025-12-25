@extends('layouts.admin')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-white mb-0">
                <i class="bi bi-clock-history"></i> Histori Login
            </h2>
            <p class="text-white-50 mb-0">Riwayat login semua user</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Total Login</h6>
                    <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Login Hari Ini</h6>
                    <h3 class="mb-0">{{ number_format($stats['today']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Sedang Online</h6>
                    <h3 class="mb-0 text-success">{{ number_format($stats['online']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Admin / User</h6>
                    <h3 class="mb-0">{{ number_format($stats['admin_logins']) }} / {{ number_format($stats['user_logins']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-filter"></i> Filter</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.logs.login') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role">
                            <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cari Nama/Badge</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Nama atau Badge...">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-list"></i> Riwayat Login ({{ $logs->total() }} entries)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Badge</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Login</th>
                            <th>Logout</th>
                            <th>Durasi</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $index => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $index }}</td>
                            <td><strong>{{ $log->user_badge }}</strong></td>
                            <td>{{ $log->user_name }}</td>
                            <td>
                                @if($log->role == 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-primary">User</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $log->login_at->format('d/m/Y') }}</small><br>
                                <strong>{{ $log->login_at->format('H:i:s') }}</strong>
                            </td>
                            <td>
                                @if($log->logout_at)
                                    <small class="text-muted">{{ $log->logout_at->format('d/m/Y') }}</small><br>
                                    <strong>{{ $log->logout_at->format('H:i:s') }}</strong>
                                @else
                                    <span class="badge bg-success">Online</span>
                                @endif
                            </td>
                            <td>
                                @if($log->logout_at)
                                    {{ $log->login_at->diff($log->logout_at)->format('%H:%I:%S') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $log->ip_address ?? '-' }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mb-0">Belum ada riwayat login</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
