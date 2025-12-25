<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Arsip Pusri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-bg: #f5f7fa;
            --dark-sidebar: #0d1f2d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            background: linear-gradient(180deg, #1a2f42 0%, #0d1f2d 100%);
            min-height: 100vh;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.15);
            position: fixed;
            width: 16.6667%;
            left: 0;
            top: 0;
            z-index: 1000;
            padding: 1.5rem 0;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: #e0e0e0;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border-left-color: var(--primary-color);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: rgba(0, 102, 204, 0.3);
            color: #fff;
            border-left-color: var(--primary-color);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* ===== BRAND AREA ===== */
        .sidebar .text-center {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .sidebar .text-center img {
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .sidebar .text-center h4 {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* ===== COLLAPSE MENU ===== */
        .collapse .nav-link {
            font-size: 0.9rem;
            padding: 8px 20px;
            padding-left: 30px;
            color: #bbb;
        }

        .collapse .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .collapse .nav-link i {
            font-size: 0.85rem;
        }

        /* ===== MAIN CONTENT ===== */
        main {
            margin-left: 16.6667%;
            padding: 2rem;
            min-height: 100vh;
        }

        /* ===== NAVBAR ===== */
        .navbar-custom {
            background: linear-gradient(90deg, #1a2f42 0%, #0d1f2d 100%);
            padding: 15px 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .navbar-custom .navbar-text {
            color: #fff;
            font-weight: 500;
            font-size: 1rem;
        }

        /* ===== CARDS ===== */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(90deg, #dc3545 0%, #c82333 100%);
            color: #fff;
            border-radius: 12px 12px 0 0 !important;
            padding: 18px 20px;
            font-weight: 600;
            border: none;
        }

        .card-body {
            padding: 25px;
        }

        .card-title {
            font-weight: 700;
            color: #1a2f42;
            margin-bottom: 1rem;
        }

        /* ===== HOME CARDS ===== */
        .home-card {
            border: none;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-top: 4px solid var(--primary-color);
        }

        .home-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .home-card h3 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .home-card h4 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .home-card p {
            font-weight: 500;
            color: #666;
        }

        /* ===== BUTTONS ===== */
        .btn {
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(90deg, #0066cc 0%, #0052a3 100%);
            color: #fff;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #0052a3 0%, #003d7a 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 102, 204, 0.3);
        }

        .btn-success {
            background: #28a745;
            color: #fff;
        }

        .btn-success:hover {
            background: #218838;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-warning {
            background: #ffc107;
            color: #000;
        }

        .btn-warning:hover {
            background: #e0a800;
            color: #000;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #dc3545;
            color: #fff;
        }

        .btn-danger:hover {
            background: #c82333;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(220, 53, 69, 0.3);
        }

        .btn-info {
            background: #17a2b8;
            color: #fff;
        }

        .btn-info:hover {
            background: #138496;
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-group-sm .btn {
            padding: 6px 10px;
            font-size: 0.85rem;
        }

        /* ===== TABLES ===== */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: #f5f7fa;
            color: #1a2f42;
            font-weight: 700;
            border: none;
            padding: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border: none;
            border-bottom: 1px solid #e9ecef;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: #f5f7fa;
        }

        /* ===== BADGES ===== */
        .badge {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge.bg-warning {
            background: #ffc107 !important;
            color: #000;
        }

        .badge.bg-success {
            background: #28a745 !important;
            color: #fff;
        }

        .badge.bg-danger {
            background: #dc3545 !important;
            color: #fff;
        }

        /* ===== ALERTS ===== */
        .alert {
            border: none;
            border-radius: 10px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left-color: #28a745;
        }

        .alert-danger {
            background: #fdf2f2;
            color: #991b1b;
            border-left-color: #dc3545;
        }

        /* ===== PAGINATION ===== */
        .pagination {
            gap: 5px;
        }

        .page-link {
            border: 1px solid #e9ecef;
            color: var(--primary-color);
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: var(--primary-color);
            color: #fff;
            border-color: var(--primary-color);
        }

        .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* ===== FORMS ===== */
        .form-label {
            font-weight: 600;
            color: #1a2f42;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -100%;
                width: 70%;
                transition: left 0.3s ease;
                z-index: 1001;
            }

            .sidebar.show {
                left: 0;
            }

            main {
                margin-left: 0;
                padding: 1rem;
            }

            .navbar-custom {
                margin-bottom: 1rem;
            }

            .card-body {
                padding: 15px;
            }

            .btn {
                padding: 8px 15px;
                font-size: 0.9rem;
            }

            .table {
                font-size: 0.9rem;
            }

            .table thead th, .table tbody td {
                padding: 10px;
            }
        }

        @media (max-width: 576px) {
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }

            .btn-group {
                flex-wrap: wrap;
                gap: 5px;
            }

            h2 {
                font-size: 1.5rem;
            }
        }

        /* ===== UTILITY ===== */
        .text-white-50 {
            color: rgba(255, 255, 255, 0.5);
        }

        .me-2 {
            margin-right: 0.5rem;
        }

        .mb-4 {
            margin-bottom: 2rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .py-5 {
            padding: 3rem 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 sidebar p-3">
                <div class="text-center mb-4">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <img src="{{ asset('images/logo-pusri.svg') }}" alt="Logo PUSRI" style="height:60px;width:60px">
                        <div class="text-start">
                            <h4 class="text-white mb-0">ARSIP</h4>
                            <small class="text-white-50">ADMIN</small>
                        </div>
                    </div>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    
                    <!-- Surat Keputusan Dropdown -->
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.documents.sk*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#skMenu">
                            <i class="bi bi-file-earmark-text"></i> Surat Keputusan
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.documents.sk*') ? 'show' : '' }}" id="skMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.documents.sk.create') ? 'active' : '' }}" href="{{ route('admin.documents.sk.create') }}">
                                        <i class="bi bi-plus-circle"></i> Input SK
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.documents.sk') && !request()->routeIs('admin.documents.sk.create') && !request()->routeIs('admin.documents.sk.store') ? 'active' : '' }}" href="{{ route('admin.documents.sk') }}">
                                        <i class="bi bi-list-ul"></i> Lihat Semua SK
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    
                    <!-- Surat Perjanjian Dropdown -->
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.documents.sp*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#spMenu">
                            <i class="bi bi-file-earmark-text"></i> Surat Perjanjian
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.documents.sp*') ? 'show' : '' }}" id="spMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.documents.sp.create') ? 'active' : '' }}" href="{{ route('admin.documents.sp.create') }}">
                                        <i class="bi bi-plus-circle"></i> Input SP
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.documents.sp') && !request()->routeIs('admin.documents.sp.create') && !request()->routeIs('admin.documents.sp.store') ? 'active' : '' }}" href="{{ route('admin.documents.sp') }}">
                                        <i class="bi bi-list-ul"></i> Lihat Semua SP
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    
                    <!-- Surat Addendum Dropdown -->
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.documents.addendum*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#addendumMenu">
                            <i class="bi bi-file-earmark-text"></i> Surat Addendum
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.documents.addendum*') ? 'show' : '' }}" id="addendumMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.documents.addendum.create') ? 'active' : '' }}" href="{{ route('admin.documents.addendum.create') }}">
                                        <i class="bi bi-plus-circle"></i> Input Addendum
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.documents.addendum') && !request()->routeIs('admin.documents.addendum.create') && !request()->routeIs('admin.documents.addendum.store') ? 'active' : '' }}" href="{{ route('admin.documents.addendum') }}">
                                        <i class="bi bi-list-ul"></i> Lihat Semua Addendum
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.approval.*') ? 'active' : '' }}" href="{{ route('admin.approval.index') }}">
                            <i class="bi bi-check-circle"></i> Approval
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people"></i> Kelola User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}" href="{{ route('admin.logs.login') }}">
                            <i class="bi bi-clock-history"></i> Histori Login
                        </a>
                    </li>

                    <li class="nav-item mt-4 pt-3" style="border-top: 1px solid rgba(255,255,255,0.1);">
                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </li>
                </ul>
            </nav>
            <main class="col-md-10">
                <nav class="navbar navbar-custom mb-4">
                    <div class="container-fluid">
                        <span class="navbar-text"><i class="bi bi-person-circle"></i> {{ Auth::user()->Nama }} ({{ Auth::user()->BADGE }})</span>
                    </div>
                </nav>
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
