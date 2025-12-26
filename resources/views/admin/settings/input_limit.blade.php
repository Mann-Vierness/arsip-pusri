@extends('layouts.admin')

@section('title', 'Pengaturan Batas Input User')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Pengaturan Batas Maksimal Input Pending User</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.settings.input-limit.update') }}">
                @csrf
                <div class="mb-3">
                    <label for="max_pending" class="form-label">Batas Maksimal Input Pending</label>
                    <input type="number" min="1" max="100" class="form-control @error('max_pending') is-invalid @enderror" id="max_pending" name="max_pending" value="{{ old('max_pending', $maxPending) }}" required>
                    @error('max_pending')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
