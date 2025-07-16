<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin') - Sistem Presensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            width: 16.666667%;
            overflow-y: auto;
            max-height: 100vh;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
            margin-left: 16.666667%;
            width: 83.333333%;
        }
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn {
            border-radius: 8px;
            padding: 8px 16px;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-tepat-waktu {
            background-color: #d4edda;
            color: #155724;
        }
        .status-terlambat {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-absen {
            background-color: #fff3cd;
            color: #856404;
        }
        .admin-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 15px 15px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <div class="text-center mb-4">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:56px; height:56px; object-fit:contain; border-radius:12px; margin-bottom:10px; background:rgba(255,255,255,0.7); border:2px solid #fff; box-shadow:0 2px 8px rgba(102,126,234,0.10);">
            <h5 class="text-white mb-0">
            
            </h5>
            <small class="text-white-50">{{ auth()->user()->name }}</small>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}" href="{{ route('admin.siswa.index') }}">
                <i class="fas fa-users"></i>
                Data Siswa
            </a>
            <a class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}" href="{{ route('admin.guru.index') }}">
                <i class="fas fa-chalkboard-teacher"></i>
              Data Guru
            </a>
            <a class="nav-link {{ request()->routeIs('admin.presensi.*') ? 'active' : '' }}" href="{{ route('admin.presensi.index') }}">
                <i class="fas fa-clipboard-list"></i>
              Presensi
            </a>
            <a class="nav-link {{ request()->routeIs('admin.libur.*') ? 'active' : '' }}" href="{{ route('admin.libur.index') }}">
                <i class="fas fa-calendar-times"></i>
              Hari Libur
            </a>
            <a class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}" href="{{ route('admin.laporan.index') }}">
                <i class="fas fa-chart-bar"></i>
               Laporan Kehadiran
            </a>
            <hr class="text-white-50">
            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
       
        <!-- Content -->
        <div class="p-4">
            <!-- Flash Messages -->
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

            @yield('content')
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script>
document.querySelectorAll('a[href="{{ route('logout') }}"]').forEach(function(el) {
    el.addEventListener('click', function(e) {
        if(!confirm('Yakin ingin logout?')) {
            e.preventDefault();
        }
    });
});
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html> 