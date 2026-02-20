@extends('layouts.admin')

@section('title', 'Categorías - PET CUTE CLOTHES Admin')
@section('breadcrumb', 'Categorías')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-0">📂 Categorías</h1>
                <p class="text-muted">Gestiona las categorías de productos</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-circle"></i> Nueva Categoría
            </button>
        </div>
    </div>

    <!-- Tabla de Categorías -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th>Productos</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categorias as $categoria)
                        <tr>
                            <td>{{ $categoria->id }}</td>
                            <td>
                                <strong>{{ $categoria->name }}</strong>
                            </td>
                            <td><code>{{ $categoria->slug }}</code></td>
                            <td>
                                <span class="badge bg-primary">{{ $categoria->products_count }} productos</span>
                            </td>
                            <td>{{ $categoria->created_at->format('d/m/Y') }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $categoria->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.categories.destroy', $categoria->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta categoría?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        
                        <!-- Edit Modal -->
                        <div class="modal fade" id="editCategoryModal{{ $categoria->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Categoría</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.categories.update', $categoria->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nombre</label>
                                                <input type="text" name="name" class="form-control" value="{{ $categoria->name }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Guardar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay categorías creadas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej: Ropa para Perros" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
