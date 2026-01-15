@extends('layouts.app')

@section('title', $producto->name . ' - PET CUTE CLOTHES')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">🏠 Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">📦 Productos</a></li>
                <li class="breadcrumb-item active">{{ $producto->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Detalle de Producto -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <img src="{{ $producto->image_url ?? 'https://picsum.photos/seed/petcute' . $producto->id . '/600/400.jpg' }}" alt="{{ $producto->name }}" class="img-fluid rounded shadow" style="width: 100%; height: 400px; object-fit: cover;" onerror="this.src='https://picsum.photos/seed/petcute{{ $producto->id }}/600/400.jpg';">
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    @if($producto->category)
                        <span class="badge {{ $producto->category->name == 'Casual' ? 'badge-casual' : ($producto->category->name == 'Elegante' ? 'badge-elegante' : 'badge-cumpleanos') }} mb-2">
                            {{ $producto->category->name }}
                        </span>
                    @endif
                    <h1 class="card-title mb-3">{{ $producto->name }}</h1>
                    <p class="card-text">{{ $producto->description }}</p>

                    <hr>

                    <div class="mb-3">
                        <strong>💰 Precio:</strong>
                        <span class="price">ARS {{ number_format($producto->price, 0, ',', '.') }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>📏 Talla:</strong>
                        <span class="talla-badge">{{ ['XS','S','M','L','XL'][$producto->size - 1] }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>📦 Stock:</strong>
                        <span class="stock-badge {{ $producto->stock > 0 ? 'stock-available' : 'stock-low' }}">
                            {{ $producto->stock > 0 ? 'Disponible ('.$producto->stock.' unidades)' : 'Agotado' }}
                        </span>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            ← Volver a Productos
                        </a>
                        @if($producto->stock > 0)
                            <button class="btn btn-primary" disabled>
                                🛒 Añadir al Carrito (Próximamente)
                            </button>
                        @else
                            <button class="btn btn-secondary" disabled>
                                📦 Producto Agotado
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
