@extends('layouts.app')

@section('title', 'Checkout - PET CUTE CLOTHES')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fas fa-credit-card" style="font-size: 4rem; color: #FFB6C1;"></i>
                    </div>
                    <h2 class="card-title mb-3">💳 Checkout Próximamente</h2>
                    <p class="card-text text-muted mb-4">
                        El proceso de pago estará disponible en la ETAPA 3.<br>
                        Por ahora puedes seguir agregando productos a tu carrito.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary">
                            🛒 Volver al Carrito
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            📦 Seguir Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection