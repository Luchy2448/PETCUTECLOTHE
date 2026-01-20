@extends('layouts.app')

@section('title', 'PET CUTE CLOTHES - Ropa para Mascotas')

@section('content')
<div class="container py-5">
    <div class="alert alert-info mb-4">
        <h4>Bienvenida a PET CUTE CLOTHES!</h4>
        <p>Descubre nuestra colección de ropa adorable para tu mascota.</p>
    </div>

    <!-- Productos -->
    <div class="row">
        @foreach($productos as $producto)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="{{ $producto->image_url ?? 'https://picsum.photos/seed/petcute' . $producto->id . '/400/200.jpg' }}" alt="{{ $producto->name }}" class="card-img-top" style="height: 200px; object-fit: cover;" onerror="this.src='https://picsum.photos/seed/petcute{{ $producto->id }}/400/200.jpg';">
                    <div class="card-body">
                        <h5 class="card-title">{{ $producto->name }}</h5>
                        <p class="card-text">{{ \Illuminate\Support\Str::limit($producto->description, 80) }}</p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            @if($producto->category)
                                <span class="badge {{ $producto->category->name == 'Casual' ? 'badge-casual' : ($producto->category->name == 'Elegante' ? 'badge-elegante' : 'badge-cumpleanos') }}">
                                    {{ $producto->category->name }}
                                </span>
                            @endif
                            <span class="stock-badge {{ $producto->stock > 0 ? 'stock-available' : 'stock-low' }}">
                                {{ $producto->stock > 0 ? 'En stock ('.$producto->stock.')' : 'Agotado ('.$producto->stock.')' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="talla-badge">Talla {{ ['XS','S','M','L','XL'][$producto->size - 1] }}</span>
                            <span class="price">ARS {{ number_format($producto->price, 0, ',', '.') }}</span>
                        </div>
                        @auth
                            @if($producto->stock > 0)
                                <button onclick="addToCart({{ $producto->id }})" class="btn btn-primary w-100 mt-2">
                                    🛒 Añadir al Carrito
                                </button>
                            @else
                                <button class="btn btn-secondary w-100 mt-2" disabled>
                                    📦 Producto Agotado
                                </button>
                            @endif
                        @endauth
                        <a href="{{ route('products.show', $producto->id) }}" class="btn btn-outline-primary w-100 mt-2">Ver Detalles</a>
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

    <!-- Paginación -->
    @if($productos->hasPages())
        <div class="row justify-content-center mt-4">
            <div class="col-auto">
                {{ $productos->links() }}
            </div>
        </div>
    @endif

    <!-- Enlace a ver todos los productos -->
    @if($productos->hasPages() || $productos->count() >= 12)
        <div class="text-center mt-4">
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                📦 Ver Todos los Productos ({{ $productos->total() }} disponibles)
            </a>
        </div>
    @endif
</div>
@endsection
