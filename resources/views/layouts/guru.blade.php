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
        
        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            width: 280px;
            overflow-y: auto;
            max-height: 100vh;
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 0;
            transition: all 0.3s;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            width: calc(100% - 280px);
            transition: margin-left 0.3s ease-in-out;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        
        /* Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .sidebar-overlay.show {
            opacity: 1;
        }
        
        /* Mobile Header */
        .mobile-header {
            display: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 1001;
        }
        
        .navbar-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            padding: 0.5rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .navbar-toggle {
                display: block;
            }
            
            .mobile-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            .sidebar .nav-link {
                padding: 15px 20px;
                font-size: 1rem;
            }
            
            .sidebar .nav-link i {
                width: 24px;
                margin-right: 12px;
                font-size: 1.1rem;
            }
        }
        
        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
            }
            
            .main-content {
                padding: 1rem !important;
            }
            
            .mobile-header {
                padding: 0.75rem;
            }
            
            .mobile-header h6 {
                font-size: 0.9rem;
            }
            
            .mobile-header small {
                font-size: 0.75rem;
            }
        }
        
        /* Tablet adjustments */
        @media (min-width: 769px) and (max-width: 1024px) {
            .sidebar {
                width: 250px;
            }
            
            .main-content {
                margin-left: 250px;
                width: calc(100% - 250px);
            }
        }
        
        /* Large screen adjustments */
        @media (min-width: 1025px) {
            .sidebar {
                width: 280px;
            }
            
            .main-content {
                margin-left: 280px;
                width: calc(100% - 280px);
            }
        }
        
        /* Card and UI improvements */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        @media (max-width: 768px) {
            .card {
                border-radius: 10px;
                margin-bottom: 1rem;
            }
            
            .table-responsive {
                border-radius: 10px;
                overflow: hidden;
            }
            
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            
            .alert {
                border-radius: 10px;
                margin-bottom: 1rem;
            }
            
            .alert-dismissible .btn-close {
                padding: 0.75rem;
            }
        }
        
        /* Navbar improvements */
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
        }
        
        .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="d-flex align-items-center">
            <button class="navbar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="ms-3">
                <h6 class="mb-0">Guru Dashboard</h6>
                <small>{{ auth()->user()->name }}</small>
            </div>
        </div>
        <div>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-white">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="sidebar p-3" id="sidebar">
        <div class="text-center mb-4">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:56px; height:56px; object-fit:contain; border-radius:12px; margin-bottom:10px; background:rgba(255,255,255,0.7); border:2px solid #fff; box-shadow:0 2px 8px rgba(102,126,234,0.10);">
            <h5 class="text-white mb-0">Guru Dashboard</h5>
            <small class="text-white-50">{{ auth()->user()->name }}</small>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" href="{{ route('guru.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('guru.presensi.*') ? 'active' : '' }}" href="{{ route('guru.presensi.index') }}">
                <i class="fas fa-clipboard-list"></i>
                Presensi
            </a>
            <a class="nav-link {{ request()->routeIs('guru.pemindaiqr') ? 'active' : '' }}" href="{{ route('guru.pemindaiqr') }}">
                <i class="fas fa-qrcode"></i>
                Pemindai QR
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
        <div class="p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div>
                <h4 class="mb-1">@yield('title', 'Dashboard')</h4>
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
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    
    <script>
    // Sidebar Toggle for Mobile
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        function toggleSidebar() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        }
        
        function closeSidebar() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        // Toggle sidebar when hamburger button is clicked
        sidebarToggle.addEventListener('click', toggleSidebar);
        
        // Close sidebar when overlay is clicked
        sidebarOverlay.addEventListener('click', closeSidebar);
        
        // Close sidebar when a nav link is clicked (mobile)
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
        
        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                closeSidebar();
            }
        });
        
        // Logout confirmation
        document.querySelectorAll('a[href="{{ route('logout') }}"]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                if(!confirm('Yakin ingin logout?')) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>
</body>
</html>
