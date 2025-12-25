@extends('layouts.user')

@section('title', 'Surat Perjanjian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-text"></i> Surat Perjanjian</h2>
        <div>
            <a href="{{ route('user.sp.export') }}" class="btn btn-success me-2" target="_blank">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <a href="{{ route('user.sp.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah SP
            </a>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label">Cari Nomor SP atau Perihal</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Masukkan nomor SP atau perihal..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Urutkan Nomor</label>
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('user.sp.index', array_merge(request()->query(), ['sort' => 'asc'])) }}" 
                           class="btn btn-outline-secondary {{ request('sort') === 'asc' ? 'active' : '' }}">
                            <i class="bi bi-arrow-up"></i> ASC
                        </a>
                        <a href="{{ route('user.sp.index', array_merge(request()->query(), ['sort' => 'desc'])) }}" 
                           class="btn btn-outline-secondary {{ request('sort') === 'desc' || !request('sort') ? 'active' : '' }}">
                            <i class="bi bi-arrow-down"></i> DESC
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('user.sp.index') }}" class="btn btn-warning w-100">
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
                                <th width="120">Status</th>
                                <th width="200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $index => $doc)
                            <tr>
                                <td>{{ $documents->firstItem() + $index }}</td>
                                <td><strong>{{ $doc->NO }}</strong></td>
                                <td>{{ $doc->TANGGAL->format('d/m/Y') }}</td>
                                <td>{{ \Str::limit($doc->PERIHAL, 50) }}</td>
                                <td>
                                    <span class="badge {{ $doc->getStatusBadgeClass() }}">
                                        {{ ucfirst($doc->approval_status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('user.sp.show', $doc->id) }}" class="btn btn-info" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($doc->pdf_path)
                                        <a href="{{ route('user.sp.download', $doc->id) }}" class="btn btn-success" title="Download PDF">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @endif
                                        @if($doc->isPending())
                                        <a href="{{ route('user.sp.edit', $doc->id) }}" class="btn btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('user.sp.destroy', $doc->id) }}" method="POST" 
                                              class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-file-text" style="font-size: 4rem; opacity: 0.3;"></i>
                <h4 class="mt-3">Belum Ada Surat Perjanjian</h4>
                <p class="text-muted">Klik tombol "Tambah SK" untuk membuat surat keputusan baru</p>
                <a href="{{ route('user.sp.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle"></i> Tambah SK
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
