@extends('layouts.admin')
@section('title', 'Kelola User')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people"></i> Kelola User</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah User
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Badge</th>
                            <th>Nama</th>
                            <th>Departemen</th>
                            <th>Role</th> <!-- KOLOM BARU -->
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->BADGE }}</td>
                            <td>{{ $user->Nama }}</td>
                            <td>{{ $user->Departemen }}</td>
                            <td>
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
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.users.show', $user->BADGE) }}" class="btn btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user->BADGE) }}" class="btn btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->BADGE) }}" method="POST" 
                                          class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $users->links() }}
            </div>
            @else
            <p class="text-center text-muted py-5">Belum ada user</p>
            @endif
        </div>
    </div>
</div>
@endsection