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
                    @if($producto->image_url)
                        <img src="{{ $producto->image_url }}" alt="{{ $producto->name }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top" style="height: 200px; background: linear-gradient(135deg, var(--color-rosa) 0%, var(--color-celeste) 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                            🐾
                        </div>
                    @endif
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
                        <a href="{{ url('products.show', $producto->id) }}" class="btn btn-primary w-100 mt-3">Ver Detalles</a>
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
