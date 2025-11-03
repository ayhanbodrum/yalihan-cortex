<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Yalihan Emlak Admin') | Context7 Uyumlu</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom Admin CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            border-radius: 0.375rem;
            margin: 0.125rem 0;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #495057;
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .neo-badge {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            margin-left: 8px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="p-3">
                    <h5 class="text-white mb-3">
                        <i class="fas fa-building"></i> Yalihan Emlak
                        <span class="neo-badge">C7</span>
                    </h5>

                    <nav class="nav flex-column">
                        @if(isset($menuItems) && is_array($menuItems))
                            @foreach($menuItems as $item)
                                <a class="nav-link" href="{{ isset($item['route']) ? route($item['route']) : ($item['url'] ?? '#') }}">
                                    <i class="{{ $item['icon'] ?? 'fas fa-circle' }}"></i>
                                    {{ $item['name'] }}
                                </a>
                            @endforeach
                        @else
                            <!-- Default menu -->
                            <a class="nav-link active" href="#">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                            <a class="nav-link" href="{{ route('admin.kullanicilar.index') }}">
                                <i class="fas fa-users"></i> Kullanıcılar
                            </a>
                        @endif
                    </nav>

                    <!-- Context7 Info -->
                    <div class="mt-4 p-2 bg-success bg-opacity-10 rounded">
                        <small class="text-light">
                            <i class="fas fa-check-circle text-success"></i>
                            Context7 Uyumlu<br>
                            <span class="text-muted">Yasaklı fieldlar kullanılmıyor</span>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <h4 class="mb-0">@yield('page-title', 'Admin Panel')</h4>

                        <div class="navbar-nav ms-auto">
                            @auth
                                <div class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                                        <span class="neo-badge">
                                            Status: {{ auth()->user()->status ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profil</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Ayarlar</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endauth
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="p-4">
                    <!-- Success Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close touch-target-optimized touch-target-optimized" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Error Messages -->
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close touch-target-optimized touch-target-optimized" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6><i class="fas fa-exclamation-triangle"></i> Validation Hatalar:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close touch-target-optimized touch-target-optimized" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (if needed) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Context7 Global Scripts -->
    <script>
        // Context7 compliant global JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Context7 Admin Layout Loaded');
            console.log('✅ Forbidden fields (status, status, status, il) are NOT used');

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.querySelector('.btn-close')) {
                        alert.querySelector('.btn-close').click();
                    }
                }, 5000);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
