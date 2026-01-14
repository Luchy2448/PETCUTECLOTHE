@extends('layouts.app')

@section('title', 'Gestión de Productos - PET CUTE CLOTHES')

@section('content')
<div class="container-fluid py-4">
    <!-- Header del Panel de Administración -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">🛍️ Gestión de Productos</h1>
                    <p class="text-muted mb-0">Administra el inventario de tu tienda</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Catálogo
                    </a>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Nuevo Producto
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Productos Mejorada -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">📦 Lista de Productos</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" placeholder="Buscar productos..." id="searchInput">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-end">ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Talla</th>
                            <th>Categoría</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                            <tr>
                                <td class="border-end">
                                    <span class="badge bg-secondary">{{ $producto->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $producto->image_url }}" alt="{{ $producto->name }}" 
                                             class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <strong class="d-block">{{ $producto->name }}</strong>
                                            <small class="text-muted">{{ Str::limit($producto->description, 30) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        ${{ number_format($producto->price, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($producto->stock > 0)
                                            <span class="badge bg-success text-white">En Stock</span>
                                            <span class="badge bg-light text-dark">{{ $producto->stock }}</span>
                                        @else
                                            <span class="badge bg-danger text-white">Agotado</span>
                                            <span class="badge bg-light text-dark">0</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @switch($producto->size)
                                        @case(1)
                                            <span class="badge bg-purple text-white">XS</span>
                                            @break
                                        @case(2)
                                            <span class="badge bg-blue text-white">S</span>
                                            @break
                                        @case(3)
                                            <span class="badge bg-orange text-white">M</span>
                                            @break
                                        @case(4)
                                            <span class="badge bg-green text-white">L</span>
                                            @break
                                        @default
                                            <span class="badge bg-indigo text-white">XL</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @switch($producto->category->name)
                                        @case('Elegante')
                                            <span class="badge bg-primary text-white">Elegante</span>
                                            @break
                                        @case('Cumpleaños')
                                            <span class="badge bg-warning text-white">Cumpleaños</span>
                                            @break
                                        @default
                                            <span class="badge bg-info text-white">{{ $producto->category->name }}</span>
                                    @endswitch
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.products.edit', $producto->id) }}" 
                                           class="btn btn-sm btn-outline-success" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="eliminarProducto({{ $producto->id }})" 
                                                class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay productos registrados</p>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Crear Primer Producto
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="row align-items-center">
                <div class="col">
<small class="text-muted">
                        Mostrando <span id="visible-count">{{ $productos->count() }}</span> 
                        de <span id="total-count">{{ $productos->total() }}</span> productos
                    </small>
                </div>
                <div class="col-auto">
                    {{ $productos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="eliminarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">⚠️ Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este producto?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Sistema de Notificaciones por Toast -->
@if(session()->has('success'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div class="toast show" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Éxito</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast">&times;</button>
            </div>
            <div class="toast-body">
                {{ session()->get('success') }}
            </div>
        </div>
    </div>
@endif

@if(session()->has('error'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div class="toast show" role="alert">
            <div class="toast-header bg-danger text-white">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast">&times;</button>
            </div>
            <div class="toast-body">
                {{ session()->get('error') }}
            </div>
        </div>
    </div>
@endif

@if(session()->has('error'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
        <div class="toast show" role="alert">
            <div class="toast-header bg-danger text-white">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session()->get('error') }}
            </div>
        </div>
    </div>
@endif

@if(session()->has('error'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
        <div class="toast show" role="alert">
            <div class="toast-header bg-danger text-white">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session()->get('error') }}
            </div>
        </div>
    </div>
@endif

<!-- Scripts -->
<!-- Estilos para Admin con conflictos resueltos -->
<link rel="stylesheet" href="{{ asset('css/admin/products.css') }}">
<style>
/* SOBREESCRITURA - Estilos de Laravel que entran en conflicto */
.pagination {
    margin: 0;
    padding: 0;
}

.page-link {
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
    border-radius: 0.25rem;
}

.page-item.disabled {
    opacity: 0.5;
}

.page-item.active {
    background-color: #e9ecef;
}

/* ESTILOS PERSONALIZADOS - Prioridad sobre los de Laravel */
.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 1rem 0;
}

.pagination-info-text {
    background-color: #17a2b8;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    margin-bottom: 1rem;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/1a94b0c0c0.js" crossorigin="anonymous"></script>

<script>
let productoIdAEliminar = null;

function eliminarProducto(id) {
    productoIdAEliminar = id;
    const modal = new bootstrap.Modal(document.getElementById('eliminarModal'));
    modal.show();
}

document.getElementById('confirmarEliminar').addEventListener('click', function() {
    if (productoIdAEliminar) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/products/' + productoIdAEliminar;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
});

// Búsqueda en tiempo real
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Auto-cerrar toasts después de 5 segundos
setTimeout(() => {
    document.querySelectorAll('.toast').forEach(toast => {
        const bsToast = new bootstrap.Toast(toast);
        bsToast.hide();
    });
}, 5000);

// Actualizar contadores de paginación dinámicamente
function updatePaginationCounters() {
    const visibleRows = document.querySelectorAll('tbody tr:not([style*="display: none"])').length;
    const totalCount = {{ $productos->total() }};
    
    document.getElementById('visible-count').textContent = visibleRows;
    document.getElementById('total-count').textContent = totalCount;
}

// Actualizar contadores de paginación dinámicamente
function updatePaginationCounters() {
    const visibleRows = document.querySelectorAll('tbody tr:not([style*="display: none"])').length;
    const totalCount = {{ $productos->total() }};
    
    const visibleCountElement = document.getElementById('visible-count');
    const totalCountElement = document.getElementById('total-count');
    
    if (visibleCountElement) {
        visibleCountElement.textContent = visibleRows;
    }
    if (totalCountElement) {
        totalCountElement.textContent = totalCount;
    }
}

// Inicializar contadores cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    updatePaginationCounters();
});

// Actualizar también cuando se usa la búsqueda
document.addEventListener('input', function(e) {
    if (e.target && e.target.id === 'searchInput') {
        updatePaginationCounters();
    }
});
</script>

<style>
/* Estilos personalizados para badges de talla */
.badge.bg-purple { background-color: #6f42c1 !important; }
.badge.bg-blue { background-color: #0d6efd !important; }
.badge.bg-orange { background-color: #fd7e14 !important; }
.badge.bg-green { background-color: #198754 !important; }
.badge.bg-indigo { background-color: #6f42c1 !important; }

/* Mejoras de contraste */
.btn-outline-success {
    color: #198754;
    border-color: #198754;
}

.btn-outline-success:hover {
    color: #fff;
    background-color: #198754;
    border-color: #198754;
}

.btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-danger:hover {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
}

/* Alineación perfecta de elementos */
.table td {
    vertical-align: middle;
}

.d-flex.align-items-center {
    align-items: center;
}

/* Mejoras visuales */
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.05);
}
</style>
@endsection