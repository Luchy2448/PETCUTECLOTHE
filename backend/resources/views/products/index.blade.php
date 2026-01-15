@extends('layouts.app')

@section('title', 'Productos - PET CUTE CLOTHES')

@section('content')
<div class="container py-5">
    <!-- Hero Section -->
    <div class="hero-section mb-5">
        <h1 class="hero-title">🐾 PET CUTE CLOTHES</h1>
        <p class="hero-subtitle">Nuestra Colección de Productos</p>
    </div>

    <!-- Mensajes de sesión -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
            <strong>¡Éxito!</strong> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
            <strong>¡Error!</strong> {{ session('error') }}
        </div>
    @endif

    <!-- Productos -->
    <div class="row">
        @foreach($productos as $producto)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $producto->image_url ?? 'https://picsum.photos/seed/petcute' . $producto->id . '/400/200.jpg' }}" alt="{{ $producto->name }}" class="card-img-top" style="height:200px; object-fit: cover;" onerror="this.src='https://picsum.photos/seed/petcute{{ $producto->id }}/400/200.jpg';">
                    <div class="card-body">
                        <h5 class="card-title">{{ $producto->name }}</h5>
                        <p class="card-text">{{ Str::limit($producto->description, 80) }}</p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            @if($producto->category)
                                <span class="badge {{ $producto->category->name == 'Casual' ? 'badge-casual' : ($producto->category->name == 'Elegante' ? 'badge-elegante' : 'badge-cumpleanos') }}">
                                    {{ $producto->category->name }}
                                </span>
                            @endif
                            <span class="stock-badge {{ $producto->stock > 0 ? 'stock-available' : 'stock-low' }}">
                                {{ $producto->stock > 0 ? 'En stock ('.$producto->stock.')' : 'Agotado' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="talla-badge">Talla {{ ['XS','S','M','L','XL'][$producto->size - 1] }}</span>
                            <span class="price">ARS {{ number_format($producto->price, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('products.show', $producto->id) }}" class="btn btn-primary w-100 mt-3">Ver Detalles</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($productos->count() == 0)
        <div class="alert alert-info text-center">
            <h4>No hay productos disponibles</h4>
            <p>Vuelve más tarde para ver nuevas colecciones</p>
        </div>
    @endif
</div>
@endsection
