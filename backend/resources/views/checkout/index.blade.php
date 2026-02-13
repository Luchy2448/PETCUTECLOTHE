@extends('layouts.app')

@section('title', 'Checkout - PET CUTE CLOTHES')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-center">
                💳 Checkout - Finalizar Compra
            </h1>
            <p class="text-center text-muted">
                Revisa tu pedido y completa el proceso de pago
            </p>
        </div>
    </div>

    <!-- Mensajes de sesión -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
            <strong>¡Éxito!</strong> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
            <strong>¡Error!</strong> {{ session('error') }}
        </div>
    @endif

    <!-- Formulario de Checkout -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        📦 Resumen del Pedido
                    </h5>
                </div>
                
                <div class="card-body">
                    <!-- Items del Carrito -->
                    <div class="mb-4">
                        <h6 class="mb-3">🛒 Productos en tu Carrito</h6>
                        @if($cartItems->isEmpty())
                            <div class="alert alert-warning">
                                <h6>⚠️ Carrito Vacío</h6>
                                <p>No hay productos en tu carrito. <a href="{{ route('products.index') }}" class="alert-link">Agrega productos</a> para continuar.</p>
                            </div>
                        @else
                            @php
                                $tallas = ['XS','S','M','L','XL'];
                                $subtotalVista = 0;
                            @endphp
                            
                            @foreach($cartItems as $item)
                                @php
                                    $tallaTexto = isset($tallas[$item->product->size - 1]) ? $tallas[$item->product->size - 1] : $item->product->size;
                                    $itemSubtotal = $item->product->price * $item->quantity;
                                    $subtotalVista += $itemSubtotal;
                                @endphp
                                
                                <div class="d-flex align-items-center mb-3 p-3 border rounded">
                                    <img src="{{ $item->product->image_url ?? 'https://picsum.photos/seed/petcute' . $item->product_id . '/80/80.jpg' }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="rounded me-3" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                    <div class="ms-3">
                                        <h6 class="mb-1">{{ $item->product->name }}</h6>
                                        <div class="text-muted mb-2">
                                            <small class="d-block">Talla: {{ $tallaTexto }}</small>
                                            <small class="d-block">Cantidad: {{ $item->quantity }}</small>
                                            <small class="d-block">Precio: ARS {{ number_format($item->product->price, 2, ',', '.') }}</small>
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-primary">ARS {{ number_format($itemSubtotal, 2, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Totales -->
                    @if(!$cartItems->isEmpty())
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h6>Subtotal:</h6>
                            </div>
                            <div class="col-md-4 text-end">
                                <h6>ARS {{ number_format($subtotalVista, 2, ',', '.') }}</h6>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h6>Envío:</h6>
                            </div>
                            <div class="col-md-4 text-end">
                                <h6>ARS 0.00</h6>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5 class="text-primary">Total:</h5>
                            </div>
                            <div class="col-md-4 text-end">
                                <h5 class="text-primary">ARS {{ number_format($subtotalVista, 2, ',', '.') }}</h5>
                            </div>
                        </div>
                    @endif

                    <!-- Formulario de Información -->
                    @if(!$cartItems->isEmpty())
                        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                            @csrf
                            
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">📦 Información de Envío</h6>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="shipping_address" class="form-label">Dirección de Envío *</label>
                                    <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" required placeholder="Calle 123, Ciudad, Provincia">{{ auth()->user()->name ?? '' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">Teléfono *</label>
                                    <input type="tel" name="phone_number" id="phone_number" class="form-control" required placeholder="381-123-4567" value="{{ auth()->user()->phone ?? '' }}">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3">💳 Método de Pago</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_mercadopago" value="mercadopago" checked>
                                        <label class="form-check-label" for="payment_mercadopago">
                                            💳 Mercado Pago (Tarjeta, Efectivo, Transferencia)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100">
                                        ← Volver al Carrito
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary w-100" id="createOrderBtn">
                                        🛒 Crear Pedido
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para validación -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    const createOrderBtn = document.getElementById('createOrderBtn');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Deshabilitar botón temporalmente para dobles envíos
            if (createOrderBtn) {
                if (createOrderBtn.disabled) {
                    e.preventDefault();
                    return;
                }
                createOrderBtn.disabled = true;
                createOrderBtn.innerHTML = '🔄 Creando Pedido...';
            }
        });
    }
});
</script>

<!-- Estilos adicionales -->
<style>
.checkout-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 2rem;
}

.cart-item {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
    border-radius: 10px;
}

.cart-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0.15);
}

.total-row {
    border-top: 2px solid #007bff;
    padding-top: 1rem;
    margin-top: 1rem;
}

.payment-methods {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
}

.form-check-label {
    cursor: pointer;
}

@media (max-width: 768px) {
    .checkout-container {
        padding: 1rem;
        margin: 1rem 0;
    }
    
    .cart-item {
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection