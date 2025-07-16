<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Guru') - Sistem Presensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')

    <style>
        body {
            overflow-x: hidden;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        @media (min-width: 768px) {
            #sidebarOffcanvas {
                position: fixed !important;
                top: 56px;
                left: 0;
                width: 240px;
                height: calc(100% - 56px);
                transform: none !important;
                visibility: visible !important;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                z-index: 1040;
            }
            .main-content {
                margin-left: 240px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar Responsive -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('guru.dashboard') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:40px; height:40px; object-fit:contain; border-radius:8px; margin-right:10px; box-shadow:0 2px 8px rgba(102,126,234,0.15);">
                <span>Guru Dashboard</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarGuru" aria-controls="navbarGuru" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarGuru">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" href="{{ route('guru.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guru.presensi.*') ? 'active' : '' }}" href="{{ route('guru.presensi.index') }}">
                            <i class="fas fa-clipboard-list"></i> Presensi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guru.pemindaiqr') ? 'active' : '' }}" href="{{ route('guru.pemindaiqr') }}">
                            <i class="fas fa-qrcode"></i> Pemindai QR
                        </a>
                    </li>
                </ul>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-light btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>
    </nav>
    <!-- Main Content -->
    <div class="container-fluid py-4" style="min-height: 100vh; background-color: #f8f9fa;">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
                <h4 class="mb-1">@yield('title', 'Dashboard')</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Guru</a></li>
                        <li class="breadcrumb-item active">@yield('title', 'Dashboard')</li>
                    </ol>
                </nav>
            </div>
            <div class="text-end mt-2 mt-lg-0">
                <small class="text-muted">{{ now()->format('d F Y, H:i') }}</small>
            </div>
        </div>
        <!-- Flash Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <!-- Content Section -->
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
document.querySelectorAll('form[action="{{ route('logout') }}"]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        if(!confirm('Yakin ingin logout?')) {
            e.preventDefault();
        }
    });
});
</script>
</body>
</html>
