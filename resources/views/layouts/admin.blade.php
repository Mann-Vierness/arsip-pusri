<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Arsip Pusri</title>

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #0066cc;
            --sidebar-bg: #0d1f2d;
            --sidebar-hover: rgba(255,255,255,0.15);
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg,#f5f7fa,#e9ecef);
            min-height:100vh;
            color:#333;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            background: var(--sidebar-bg);
            position: fixed;
            top:0;
            left:0;
            height: 100vh;
            width: 220px;
            padding: 1.5rem 1rem;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 60px;
        }

        .sidebar .brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .sidebar .brand img {
            width:140px;
            height:70px;
            margin-bottom:0.5rem;
        }

        .sidebar .brand .title {
            color:#fff;
            font-weight:700;
            font-size:1.1rem;
        }

        .sidebar .brand .subtitle {
            color: rgba(255,255,255,0.6);
            font-size:0.85rem;
        }

        .sidebar .nav-link {
            color:#e0e0e0;
            padding:10px 15px;
            border-radius:8px;
            margin-bottom:5px;
            display:flex;
            align-items:center;
            gap:10px;
            transition: all 0.3s;
        }

        .sidebar .nav-link i {
            font-size:1.2rem;
            width:24px;
            text-align:center;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: var(--sidebar-hover);
            color:#fff;
        }

        .sidebar .nav .collapse .nav-link {
            padding-left:35px;
            font-size:0.9rem;
            color:#bbb;
        }

        .sidebar .nav .collapse .nav-link:hover {
            color:#fff;
        }

        /* ===== MAIN ===== */
        main {
            margin-left:220px;
            padding:2rem;
            transition: margin-left 0.3s;
        }

        .sidebar.collapsed + main {
            margin-left:60px;
        }

        /* ===== NAVBAR ===== */
        .navbar-custom {
            background: linear-gradient(90deg,#1a2f42,#0d1f2d);
            padding:10px 20px;
            border-radius:10px;
            margin-bottom:2rem;
            color:#fff;
        }

        /* ===== RESPONSIVE ===== */
        @media(max-width:768px){
            .sidebar{
                left:-100%;
                width:220px;
            }
            .sidebar.show{
                left:0;
            }
            main{
                margin-left:0;
            }
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <nav class="sidebar" id="sidebar">
        <div class="brand">
            <img src="{{ asset('images/logo-pusri.svg') }}" alt="Logo PUSRI">
            <div class="title">ARSIP</div>
            <div class="subtitle">ADMIN</div>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> <span class="link-text">Dashboard</span>
                </a>
            </li>

            <!-- Pengaturan Batas Input User -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.settings.input-limit') ? 'active' : '' }}" href="{{ route('admin.settings.input-limit') }}">
                    <i class="bi bi-sliders"></i> <span class="link-text">Pengaturan Batas Input</span>
                </a>
            </li>

            <!-- Surat Keputusan -->
            <li class="nav-item">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.documents.sk*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#skMenu">
                    <i class="bi bi-file-earmark-text"></i> <span class="link-text">Surat Keputusan</span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.documents.sk*') ? 'show' : '' }}" id="skMenu">
                    <ul class="nav flex-column">
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

            <!-- Surat Perjanjian -->
            <li class="nav-item">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.documents.sp*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#spMenu">
                    <i class="bi bi-file-earmark-text"></i> <span class="link-text">Surat Perjanjian</span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.documents.sp*') ? 'show' : '' }}" id="spMenu">
                    <ul class="nav flex-column">
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

            <!-- Surat Addendum -->
            <li class="nav-item">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.documents.addendum*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#addendumMenu">
                    <i class="bi bi-file-earmark-text"></i> <span class="link-text">Surat Addendum</span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.documents.addendum*') ? 'show' : '' }}" id="addendumMenu">
                    <ul class="nav flex-column">
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

            <li class="nav-item mt-3">
                <a class="nav-link {{ request()->routeIs('admin.approval.*') ? 'active' : '' }}" href="{{ route('admin.approval.index') }}">
                    <i class="bi bi-check-circle"></i> <span class="link-text">Approval</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i> <span class="link-text">Kelola User</span>
                </a>
            </li>
            <li class="nav-item">
             <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.documents') }}">
                 <i class="bi bi-graph-up"></i> <span class="link-text">Laporan Dokumen</span>
            </a>
        </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}" href="{{ route('admin.logs.login') }}">
                    <i class="bi bi-clock-history"></i> <span class="link-text">Histori Login</span>
                </a>
            </li>

            <li class="nav-item mt-4 pt-3" style="border-top:1px solid rgba(255,255,255,0.1)">
                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i> <span class="link-text">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
        </ul>
    </nav>

    <!-- MAIN CONTENT -->
    <main>
        <nav class="navbar navbar-custom">
            <span class="navbar-text"><i class="bi bi-person-circle"></i> {{ Auth::user()->Nama }} ({{ Auth::user()->BADGE }})</span>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
