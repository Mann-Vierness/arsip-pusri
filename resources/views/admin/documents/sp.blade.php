@extends('layouts.admin')

@section('title', 'Semua Surat Perjanjian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-text"></i> Semua Surat Perjanjian</h2>
        <div>
            <a href="{{ route('admin.documents.sp.export', request()->query()) }}" class="btn btn-success me-2" target="_blank">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <a href="{{ route('admin.documents.sp.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah SP
            </a>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label">Cari Nomor SP, Perihal, atau User</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Masukkan nomor, perihal, atau nama user..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Urutkan Nomor</label>
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('admin.documents.sp', array_merge(request()->query(), ['sort' => 'asc'])) }}" 
                           class="btn btn-outline-secondary {{ request('sort') === 'asc' ? 'active' : '' }}">
                            <i class="bi bi-arrow-up"></i> ASC
                        </a>
                        <a href="{{ route('admin.documents.sp', array_merge(request()->query(), ['sort' => 'desc'])) }}" 
                           class="btn btn-outline-secondary {{ request('sort') === 'desc' || !request('sort') ? 'active' : '' }}">
                            <i class="bi bi-arrow-down"></i> DESC
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.documents.sp') }}" class="btn btn-warning w-100">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($documents->count() > 0)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Surat Perjanjian</h5>
                <span class="badge bg-primary">Total: {{ $documents->total() }} dokumen</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Nomor SP</th>
                                <th>Tanggal</th>
                                <th>Perihal</th>
                                <th>User Pembuat</th>
                                <th width="120">Status</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $index => $doc)
                            <tr>
                                <td>{{ $documents->firstItem() + $index }}</td>
                                <td><strong>{{ $doc->NO }}</strong></td>
                                <td>{{ $doc->TANGGAL->format('d/m/Y') }}</td>
                                <td>{{ \Str::limit($doc->PERIHAL, 40) }}</td>
                                <td>{{ $doc->NAMA }} ({{ $doc->USER }})</td>
                                <td>
                                    <span class="badge {{ $doc->getStatusBadgeClass() }}">
                                        {{ ucfirst($doc->approval_status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.documents.sp.show', $doc->id) }}" class="btn btn-info" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($doc->pdf_path)
                                        <a href="{{ route('admin.approval.view-pdf', ['sp', $doc->id]) }}" class="btn btn-warning" title="View PDF" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                        <a href="{{ route('admin.documents.sp.download', $doc->id) }}" class="btn btn-success" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        {{ $documents->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </ul>
                </nav>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-file-text" style="font-size: 4rem; opacity: 0.3;"></i>
                <h4 class="mt-3">Tidak Ada Data</h4>
                <p class="text-muted">Tidak ada dokumen yang sesuai dengan pencarian Anda</p>
            </div>
        </div>
    @endif
</div>
@endsection
