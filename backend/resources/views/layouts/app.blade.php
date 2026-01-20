<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        
        /* Paginación personalizada PET CUTE CLOTHES */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.25rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .page-item {
            margin: 0 0.125rem;
        }
        
        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            background-color: var(--color-blanco);
            color: var(--color-gris);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .page-link:hover {
            background-color: var(--color-rosa);
            border-color: var(--color-rosa);
            color: var(--color-blanco) !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(255, 182, 193, 0.3);
        }
        
        .page-item.active .page-link {
            background-color: var(--color-rosa);
            border-color: var(--color-rosa);
            color: var(--color-blanco) !important;
            font-weight: 600;
            z-index: 1;
        }
        
        .page-item.disabled .page-link {
            background-color: #e9ecef;
            border-color: #dee2e6;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.65;
            transform: none;
        }
        
        .page-item.disabled .page-link:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
            color: #6c757d;
            transform: none;
            box-shadow: none;
        }
        
        /* Responsive: ocultar texto en móvil */
        @media (max-width: 768px) {
            .pagination {
                gap: 0.125rem;
            }
            
            .page-link {
                min-width: 2.25rem;
                height: 2.25rem;
                padding: 0.375rem 0.5rem;
                font-size: 0.875rem;
            }
            
            .page-item {
                margin: 0 0.0625rem;
            }
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
                            <a class="nav-link position-relative" href="/cart">
                                🛒 Carrito
                                <span class="cart-badge badge bg-danger text-white rounded-circle" style="position: absolute; top: -5px; right: -10px; font-size: 0.7rem; padding: 2px 5px; min-width: 18px; text-align: center; display: none;">0</span>
                            </a>
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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Cart Functionality -->
    <script>
        // 🛒 Función para agregar producto al carrito
        function addToCart(productId) {
            // Deshabilitar el botón temporalmente
            const button = event.target;
            button.disabled = true;
            button.innerHTML = '🔄 Agregando...';
            
            // Realizar la petición a la RUTA WEB (usando formulario con CSRF)
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/cart';
            
            // Agregar CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }
            
            // Agregar product_id
            const productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = 'product_id';
            productIdInput.value = productId;
            form.appendChild(productIdInput);
            
            // Agregar quantity
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'quantity';
            quantityInput.value = 1;
            form.appendChild(quantityInput);
            
            // Enviar formulario
            document.body.appendChild(form);
            form.submit();
        }
        
        // 🔔 Función para mostrar notificaciones
        function showNotification(title, message, type) {
            // Crear elemento de notificación
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'alert-success' : 'alert-danger'} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            notification.style.zIndex = '9999';
            notification.style.minWidth = '300px';
            notification.innerHTML = `
                <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
                <strong>${title}</strong> ${message}
            `;
            
            // Agregar al DOM
            document.body.appendChild(notification);
            
            // Auto-eliminar después de 3 segundos
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // 📊 Función para actualizar el contador del carrito
        function updateCartCount() {
            fetch('/cart', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => {
                if (response.redirected) {
                    // Si redirige a login, el usuario no está autenticado
                    return null;
                }
                return response.text();
            })
            .then(html => {
                if (!html) return;
                
                // Crear un parser temporal para extraer datos
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Buscar el contador de items en el carrito
                const itemCountElement = doc.querySelector('.cart-item-count');
                const cartBadge = document.querySelector('.cart-badge');
                
                if (itemCountElement) {
                    const itemCount = itemCountElement.textContent || 0;
                    // Actualizar badge del carrito en el navbar si existe
                    if (cartBadge) {
                        cartBadge.textContent = itemCount;
                        cartBadge.style.display = itemCount > 0 ? 'inline-block' : 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error al obtener el carrito:', error);
            });
        }
        
        // 🔄 Inicializar el contador del carrito al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
    </script>
</body>
</html>