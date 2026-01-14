@extends('layouts.app')

@section('title', 'Editar Producto - PET CUTE CLOTHES')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">🏠 Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">📦 Productos</a></li>
                <li class="breadcrumb-item active">✏️ Editar Producto</li>
            </ol>
        </nav>
    </div>

    <!-- Formulario de Edición -->
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center mb-4">✏️ Editar Producto</h2>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
                            <strong>¡Error!</strong> {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.products.update', $producto->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">📝 Nombre del Producto</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $producto->name) }}" required placeholder="Ej: Suéter con corazones">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">📖 Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Describe el producto...">{{ old('description', $producto->description) }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">💰 Precio (ARS)</label>
                                    <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $producto->price) }}" step="100" min="0" required placeholder="Ej: 15000">
                                    @error('price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock" class="form-label">📦 Stock</label>
                                    <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', $producto->stock) }}" min="0" required placeholder="Ej: 10">
                                    @error('stock')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="size" class="form-label">📏 Talla</label>
                                    <select class="form-select" name="size" required>
                                        <option value="">Selecciona talla...</option>
                                        <option value="1" {{ $producto->size == 1 ? 'selected' : '' }}>1 - XS</option>
                                        <option value="2" {{ $producto->size == 2 ? 'selected' : '' }}>2 - S</option>
                                        <option value="3" {{ $producto->size == 3 ? 'selected' : '' }}>3 - M</option>
                                        <option value="4" {{ $producto->size == 4 ? 'selected' : '' }}>4 - L</option>
                                        <option value="5" {{ $producto->size == 5 ? 'selected' : '' }}>5 - XL</option>
                                    </select>
                                    @error('size')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">📁 Categoría</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">Selecciona categoría...</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}" {{ $producto->category_id == $categoria->id ? 'selected' : '' }}>{{ $categoria->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image_url" class="form-label">🔗 URL de la Imagen</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" value="{{ old('image_url', $producto->image_url) }}" placeholder="https://ejemplo.com/foto.jpg">
                            <div class="form-text text-muted small">
                                Ejemplo: https://ejemplo.com/foto-suet.jpg
                            </div>
                            @error('image_url')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                📋 Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                💾 Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
