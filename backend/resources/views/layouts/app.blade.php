<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PET CUTE CLOTHES - Administrar Productos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-rosa: #FFB6C1;
            --color-celeste: #ADD8E6;
            --color-amarillo: #FFFACD;
            --color-verde: #98FF98;
            --color-blanco: #FFFFFF;
            --color-gris: #333333;
            --color-fondo: #F8F9FA;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--color-fondo);
            padding-bottom: 2rem;
        }
        
        .navbar {
            background-color: var(--color-rosa);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            color: var(--color-blanco) !important;
            font-size: 1.5rem;
        }
        
        .navbar-nav .nav-link {
            color: var(--color-blanco) !important;
            font-weight: 500;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--color-amarillo) !important;
        }
        
        /* Asegurar alineación vertical perfecta en navbar */
        .navbar-nav {
            display: flex;
            align-items: center;
        }
        
        .navbar-nav .nav-item {
            display: flex;
            align-items: center;
        }
        
        /* Corregir botón de logout para que se alinee perfectamente */
        .logout-btn {
            border: none !important;
            background: none !important;
            padding: 0.5rem 1rem !important;
            cursor: pointer;
            color: var(--color-blanco) !important;
            text-decoration: none !important;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            line-height: 1.5;
            display: inline-block;
            vertical-align: middle;
        }
        
        .logout-btn:hover {
            color: var(--color-amarillo) !important;
        }
        
        .btn-primary {
            background-color: var(--color-rosa);
            border-color: var(--color-rosa);
            color: var(--color-blanco);
        }
        
        .btn-primary:hover {
            background-color: #FF9EC7;
            border-color: #FF9EC7;
        }
        
        .btn-secondary {
            background-color: transparent;
            border-color: var(--color-blanco);
            color: var(--color-blanco);
        }
        
        .btn-secondary:hover {
            background-color: var(--color-fondo);
            color: var(--color-gris);
        }
        
        .btn-danger {
            background-color: #DC3545;
            border-color: #DC3545;
            color: var(--color-blanco);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0,0,0.08);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-3px);
        }
        
        .card-title {
            color: var(--color-gris);
            font-weight: 600;
        }
        
        .badge {
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.5em 0.8em;
            border-radius: 20px;
        }
        
        .badge-casual {
            background-color: var(--color-rosa);
            color: var(--color-blanco);
        }
        
        .badge-elegante {
            background-color: var(--color-celeste);
            color: var(--color-blanco);
        }
        
        .badge-cumpleanos {
            background-color: var(--color-amarillo);
            color: var(--color-blanco);
        }
        
        .badge-success {
            background-color: var(--color-verde);
            color: var(--color-blanco);
        }
        
        .badge-low {
            background-color: #FFA07A;
            color: var(--color-blanco);
        }
        
        .stock-badge {
            font-size: 0.8rem;
            background-color: var(--color-gris);
            color: var(--color-blanco);
            padding: 0.3rem 0.6rem;
            border-radius: 10px;
        }
        
        .stock-available {
            background-color: var(--color-verde);
            color: var(--color-blanco);
        }
        
        .stock-low {
            background-color: #FFA07A;
            color: var(--color-blanco);
        }
        
        .talla-badge {
            font-size: 0.75rem;
            background-color: var(--color-celeste);
            color: var(--color-blanco);
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
        }
        
        .price {
            color: var(--color-rosa);
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .table {
            width: 100%;
            margin-bottom: 0;
        }
        
        .table th {
            background-color: var(--color-gris);
            color: var(--color-blanco);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0,0,0.02);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        
        .page-item.active .page-link {
            pointer-events: none;
            cursor: default;
            color: var(--color-rosa);
            font-weight: 500;
        }
        
        .page-link {
            pointer-events: none;
            color: var(--color-gris);
            font-weight: 500;
        }
        
        .page-link:not(.disabled) {
            cursor: pointer;
            color: var(--color-gris);
        }
        
        .page-link:hover {
            color: var(--color-rosa);
        }
        
        .breadcrumb {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            list-style: none;
            padding: 1rem;
            background: var(--color-fondo);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0,0,0.05);
        }
        
        .breadcrumb-item {
            margin-bottom: 0;
            color: var(--color-gris);
        }
        
        .breadcrumb-item a {
            text-decoration: none;
            color: var(--color-gris);
            font-weight: 500;
        }
        
        .breadcrumb-item.active {
            color: var(--color-rosa);
            font-weight: 600;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--color-rosa) 0%, var(--color-celeste) 100%);
            color: var(--color-blanco);
            padding: 4rem 2rem 2rem;
            text-align: center;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        
        .hero-title {
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 0;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bbebf7;
        }
        
        .d-flex {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .d-flex.justify-content-between {
            justify-content: space-between;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">
                PET CUTE CLOTHES
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products">Productos</a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register">Registro</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/products">Admin</a>
                        </li>
                        <li class="nav-item">
                            <form action="/logout" method="POST" style="display: inline; margin: 0;">
                                @csrf
                                <button type="submit" class="logout-btn">
                                    Logout ({{ auth()->user()->name }})
                                </button>
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Contenido principal -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="footer text-center">
        <div class="container">
            <p class="mb-0">PET CUTE CLOTHES</p>
            <p class="mb-2">Ropa adorable para tus mascotas</p>
            <p class="mb-0">
                <small>&copy; 2026 PET CUTE CLOTHES. Todos los derechos reservados.</small>
            </p>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>