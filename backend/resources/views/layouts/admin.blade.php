<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - PET CUTE CLOTHES')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #FFB6C1;
            --primary-dark: #FF69B4;
        }

        body {
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-brand {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
        }

        .sidebar-brand h4 {
            color: #fff;
            margin: 0;
            font-size: 1.1rem;
        }

        .sidebar-brand span {
            color: var(--primary-color);
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-menu .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-left-color: var(--primary-color);
        }

        .sidebar-menu .nav-link i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        .sidebar-section-title {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1rem 1.5rem 0.5rem;
            margin: 0;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: #f8f9fa;
        }

        .top-bar {
            background: #fff;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar .breadcrumb {
            margin: 0;
            font-size: 0.9rem;
        }

        .top-bar .breadcrumb .breadcrumb-item a {
            color: var(--primary-dark);
            text-decoration: none;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-menu .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: none;
            border: none;
            cursor: pointer;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 2px 2px 12px rgba(0, 0, 0, 0.08);
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #333;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            color: #fff;
        }

        .stat-card {
            border-left: 4px solid var(--primary-color);
        }

        .stat-card.pending {
            border-left-color: #FFD700;
        }

        .stat-card.processing {
            border-left-color: #17a2b8;
        }

        .stat-card.shipped {
            border-left-color: #007bff;
        }

        .stat-card.delivered {
            border-left-color: #28a745;
        }

        .stat-card.cancelled {
            border-left-color: #dc3545;
        }
    </style>
    @yield('extra_css')
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <h4><span>🐾</span> PET CUTE</h4>
            <small style="color: rgba(255,255,255,0.5);">Panel de Admin</small>
        </div>

        <nav class="sidebar-menu">
            <p class="sidebar-section-title">Principal</p>
            <a href="{{ route('admin.dashboard') }}"
                class="nav-link {{ Request::is('admin/dashboard') || Request::get('view') == 'dashboard' || Request::is('admin') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <p class="sidebar-section-title">Gestión</p>
            <a href="{{ route('admin.productos.list') }}"
                class="nav-link {{ Request::is('admin/productos*') || Request::is('admin.products.index') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Productos
            </a>
            <a href="{{ route('admin.categories.index') }}"
                class="nav-link {{ Request::is('admin/categories*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Categorías
            </a>
            <a href="{{ route('admin.orders.index') }}"
                class="nav-link {{ Request::is('admin/orders*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Pedidos
            </a>
            <a href="{{ route('admin.users.list') }}"
                class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Usuarios
            </a>

            <p class="sidebar-section-title">Tienda</p>
            <a href="{{ route('home') }}" class="nav-link" target="_blank">
                <i class="bi bi-shop"></i> Ver Tienda
            </a>
            <a href="{{ route('logout') }}" class="nav-link"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
            </a>
        </nav>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.index', ['view' => 'dashboard']) }}">Admin</a>
                    </li>
                    <li class="breadcrumb-item active">@yield('breadcrumb', 'Dashboard')</li>
                </ol>
            </nav>

            <div class="user-menu">
                <span class="text-muted">{{ auth()->user()->name }}</span>
                <div class="dropdown">
                    <button class="dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-4"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('home') }}" target="_blank">Ver Tienda</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('extra_js')
</body>

</html>
