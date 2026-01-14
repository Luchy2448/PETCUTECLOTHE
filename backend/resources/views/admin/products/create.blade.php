@extends('layouts.app')

@section('title', 'Crear Producto - PET CUTE CLOTHES')

@section('content')
<div class="container py-5">
    <!-- Header de breadcrumb -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">🏠 Inicio</a></li>
                <li class="breadcrumb-item active">📦 Crear Producto</li>
            </ol>
        </nav>
    </div>
    
    <!-- Formulario de Creación -->
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center mb-4">📦 Crear Nuevo Producto</h2>
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-3">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">×</button>
                            <strong>¡Error!</strong> {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-3">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">×</button>
                            <strong>¡Éxito!</strong> {{ session('success') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">📝 Nombre del Producto</label>
                            <input type="text" class="form-control @error('name', 'error')" id="name" name="name" value="{{ old('name', '') }}" required placeholder="Ej: Suéter con corazones">
                            @error('name', 'error')
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">📖 Descripción</label>
                            <textarea class="form-control @error('description', 'error')" id="description" name="description" rows="4" required placeholder="Describe el producto...">{{ old('description', '') }}</textarea>
                            @error('description', 'error')
                                <span class="text-danger">{{ $errors->first('description') }}</span>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label for="price" class="form-label">💰 Precio (ARS)</label>
                                <input type="number" class="form-control @error('price', 'error')" id="price" name="price" value="{{ old('price', '') }}" step="100" min="0" required placeholder="Ej: 15000">
                                @error('price', 'error')
                                    <span class="text-danger">{{ $errors->first('price') }}</span>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="stock" class="form-label">📦 Stock</label>
                                <input type="number" class="form-control @error('stock', 'error')" id="stock" name="stock" value="{{ old('stock', '') }}" min="0" required placeholder="Ej: 10">
                                @error('stock', 'error')
                                    <span class="text-danger">{{ $errors->first('stock') }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label for="size" class="form-label">📏 Talla</label>
                                <select class="form-select @error('size', 'error')" name="size" required>
                                    <option value="">Selecciona talla...</option>
                                    <option value="1">1 - XS</option>
                                    <option value="2">2 - S</option>
                                    <option value="3">3 - M</option>
                                    <option value="4">4 - L</option>
                                    <option value="5">5 - XL</option>
                                </select>
                                @error('size', 'error')
                                    <span class="text-danger">{{ $errors->first('size') }}</span>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">📁 Categoría</label>
                                <select class="form-select @error('category_id', 'error')" name="category_id" required>
                                    <option value="">Selecciona categoría...</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id', 'error')
                                    <span class="text-danger">{{ $errors->first('category_id') }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image_url" class="form-label">� URL de la Imagen</label>
                            <input type="url" class="form-control @error('image_url', 'error')" id="image_url" name="image_url" value="{{ old('image_url', '') }}" placeholder="https://ejemplo.com/foto.jpg">
                            <div class="form-text text-muted small">
                                Ejemplo: https://ejemplo.com/foto-suet.jpg
                            </div>
                            @error('image_url', 'error')
                                <span class="text-danger">{{ $errors->first('image_url') }}</span>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                📋 Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                ➕ Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
