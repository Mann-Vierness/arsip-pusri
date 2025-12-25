@extends('layouts.user')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-bell"></i> Notifikasi</h2>
        @if($unreadCount > 0)
        <form action="{{ route('user.notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-check-all"></i> Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <div class="list-group-item {{ !$notification->is_read ? 'unread' : '' }}">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <i class="bi bi-{{ $notification->type === 'success' ? 'check-circle text-success' : ($notification->type === 'danger' ? 'x-circle text-danger' : 'info-circle text-info') }}"></i>
                                        {{ $notification->title }}
                                    </h6>
                                    <p class="mb-1">{{ $notification->message }}</p>
                                    <small class="text-muted">{{ $notification->timeAgo() }}</small>
                                </div>
                                @if(!$notification->is_read)
                                <form action="{{ route('user.notifications.read', $notification->id) }}" method="POST" class="ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h5 class="mt-3">Tidak Ada Notifikasi</h5>
                    <p class="text-muted">Anda belum memiliki notifikasi</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
