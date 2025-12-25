<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'User') - Arsip Pusri</title>
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
            --dark-sidebar: #1a3a52;
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
            background: linear-gradient(180deg, #1a3a52 0%, #0d1f2d 100%);
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
            width: 140px;
            height: 70px;
            margin-bottom: 0.5rem;
            /* logo asli, tidak bulat */
        }

        .sidebar .text-center h4 {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

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

        main {
            margin-left: 16.6667%;
            padding: 2rem;
            min-height: 100vh;
        }

        .navbar-custom {
            background: linear-gradient(90deg, #1a3a52 0%, #0d1f2d 100%);
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
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 sidebar p-3">
                <!-- BRAND -->
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo-pusri.svg') }}" alt="Logo PUSRI">
                    <div class="mt-2">
                        <h4 class="text-white mb-1">ARSIP</h4>
                        <small class="text-white-50">PUSRI</small>
                    </div>
                </div>

                <!-- NAVIGATION -->
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    <!-- Surat Keputusan -->
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('sk.*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#skMenu">
                            <i class="bi bi-file-earmark"></i> Surat Keputusan
                        </a>
                        <div class="collapse {{ request()->routeIs('sk.*') ? 'show' : '' }}" id="skMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('user.sk.create') ? 'active' : '' }}" href="{{ route('user.sk.create') }}">
                                        <i class="bi bi-plus-circle"></i> Input SK
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('user.sk.index') || request()->routeIs('user.sk.show') || request()->routeIs('user.sk.edit') ? 'active' : '' }}" href="{{ route('user.sk.index') }}">
                                        <i class="bi bi-list-ul"></i> Lihat SK Saya
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Surat Perjanjian -->
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('sp.*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#spMenu">
                            <i class="bi bi-file-earmark-text"></i> Surat Perjanjian
                        </a>
                        <div class="collapse {{ request()->routeIs('sp.*') ? 'show' : '' }}" id="spMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('user.sp.create') ? 'active' : '' }}" href="{{ route('user.sp.create') }}">
                                        <i class="bi bi-plus-circle"></i> Input SP
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('user.sp.index') || request()->routeIs('user.sp.show') || request()->routeIs('user.sp.edit') ? 'active' : '' }}" href="{{ route('user.sp.index') }}">
                                        <i class="bi bi-list-ul"></i> Lihat SP Saya
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Surat Addendum -->
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('addendum.*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#addendumMenu">
                            <i class="bi bi-file-earmark-check"></i> Surat Addendum
                        </a>
                        <div class="collapse {{ request()->routeIs('addendum.*') ? 'show' : '' }}" id="addendumMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('user.addendum.create') ? 'active' : '' }}" href="{{ route('user.addendum.create') }}">
                                        <i class="bi bi-plus-circle"></i> Input Addendum
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('user.addendum.index') || request()->routeIs('user.addendum.show') || request()->routeIs('user.addendum.edit') ? 'active' : '' }}" href="{{ route('user.addendum.index') }}">
                                        <i class="bi bi-list-ul"></i> Lihat Addendum
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.notifications') ? 'active' : '' }}" href="{{ route('user.notifications') }}">
                            <i class="bi bi-bell"></i> Notifikasi
                        </a>
                    </li>

                    <!-- Logout -->
                    <li class="nav-item mt-4 pt-3" style="border-top: 1px solid rgba(255,255,255,0.1);">
                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </li>
                </ul>
            </nav>

            <!-- MAIN CONTENT -->
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
