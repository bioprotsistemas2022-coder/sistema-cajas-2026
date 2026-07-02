<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Bioimplant — Smart Surgery Management</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-bio sticky-top px-4">
            <div class="container-fluid d-flex align-items-center">
                <a class="navbar-brand d-flex align-items-center py-0 me-4" href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Bioimplant" style="width:200px;height:150px;object-fit:contain;">
                </a>

                @php $role = Auth::user()->role; @endphp

                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-0 gap-1">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                        @if(in_array($role, ['admin', 'deposito']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('deposito.*') || request()->routeIs('cajas.*') || request()->routeIs('admin.cajas.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-box-seam me-1"></i> Inventario
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item {{ request()->routeIs('deposito.*') ? 'active' : '' }}" href="{{ route('deposito.dashboard') }}">
                                        <i class="bi bi-building me-2"></i> Depósito
                                    </a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('cajas.*') ? 'active' : '' }}" href="{{ route('cajas.index') }}">
                                        <i class="bi bi-grid me-2"></i> Listado Cajas
                                    </a></li>
                                    @if($role === 'admin')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('admin.cajas.*') ? 'active' : '' }}" href="{{ route('admin.cajas.index') }}">
                                        <i class="bi bi-gear me-2"></i> Configuración
                                    </a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if(in_array($role, ['admin', 'deposito', 'consumo']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('consumo.*') ? 'active' : '' }}" href="{{ route('consumo.dashboard') }}">
                                    <i class="bi bi-clipboard-check me-1"></i> Recepción
                                </a>
                            </li>
                        @endif
                        @if(in_array($role, ['admin', 'logistica']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('logistica.*') ? 'active' : '' }}" href="{{ route('logistica.dashboard') }}">
                                    <i class="bi bi-truck me-1"></i> Logística
                                </a>
                            </li>
                        @endif
                        @if(in_array($role, ['admin', 'acondicionador']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('acondicionador.*') ? 'active' : '' }}" href="{{ route('acondicionador.dashboard') }}">
                                    <i class="bi bi-droplet me-1"></i> Lavado
                                </a>
                            </li>
                        @endif
                        @if(in_array($role, ['admin', 'tecnico']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('tecnico.dashboard') ? 'active' : '' }}" href="{{ route('tecnico.dashboard') }}">
                                    <i class="bi bi-heart-pulse me-1"></i> Cirugías
                                </a>
                            </li>
                        @endif
                        @if($role === 'admin')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.tecnicos') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear me-1"></i> Admin
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item {{ request()->routeIs('admin.tecnicos') ? 'active' : '' }}" href="{{ route('admin.tecnicos') }}">
                                        <i class="bi bi-people me-2"></i> Técnicos
                                    </a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('admin.cajas.*') ? 'active' : '' }}" href="{{ route('admin.cajas.index') }}">
                                        <i class="bi bi-box-seam me-2"></i> Cajas
                                    </a></li>
                                </ul>
                            </li>
                        @endif
                    </ul>

                    <ul class="navbar-nav align-items-center ms-3">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center px-3" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex align-items-center justify-content-center text-white fw-bold rounded-3" style="width:38px;height:38px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);font-size:0.85rem;">
                                        {{ substr(Auth::user()->name,0,1) }}
                                    </div>
                                    <div class="d-none d-md-block text-start lh-1">
                                        <div class="fw-semibold" style="color:rgba(255,255,255,0.95);font-size:0.8rem;">{{ Auth::user()->name }}</div>
                                        <div style="color:rgba(255,255,255,0.5);font-size:0.65rem;text-transform:uppercase;letter-spacing:0.05em;">{{ Auth::user()->role }}</div>
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2" style="min-width:220px;">
                                <li><a class="dropdown-item rounded-3" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i> Mi Perfil
                                </a></li>
                                <li><hr class="dropdown-divider my-2"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item rounded-3 text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <button class="navbar-toggler border-0 p-2 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <i class="bi bi-list fs-4" style="color:rgba(255,255,255,0.85);"></i>
                </button>
            </div>
        </nav>

        <div class="main-wrapper">
            @isset($header)
                <div class="page-header">
                    <h1><i class="bi bi-dot me-1" style="color:#3b82f6;"></i>{{ $header }}</h1>
                    <div class="header-line"></div>
                </div>
            @endisset

            @if(session('success'))
                <div class="alert alert-success alert-bio alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2 flex-shrink-0"></i>
                    <span>{!! session('success') !!}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{ $slot }}
        </div>

        <footer class="footer-bio mt-5">
            <div class="container-fluid px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Bioimplant" height="36" style="opacity:0.6;filter:grayscale(100%);">
                        <span class="fw-bold" style="color:rgba(255,255,255,0.5);font-size:0.85rem;">Bioimplant</span>
                    </div>
                    <p class="mb-0 small" style="color:rgba(255,255,255,0.35);">&copy; {{ date('Y') }} Bioimplant SRL. Surgical Traceability System.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
