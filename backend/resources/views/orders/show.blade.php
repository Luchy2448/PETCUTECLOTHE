@extends('layouts.app')

@section('title', 'Detalles del Pedido #' . $order->id . ' - PET CUTE CLOTHES')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">🏠 Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">📦 Productos</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">📦 Mis Pedidos</a></li>
                <li class="breadcrumb-item active">Pedido #{{ $order->id }}</li>
            </ol>
        </nav>
    </div>

    <!-- Información del pedido -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        📦 Detalles del Pedido #{{ $order->id }}
                    </h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-light text-dark me-2">
                                {{ $order->status_text }}
                            </span>
                            <small class="text-white">Estado actual</small>
                        </div>
                        <div class="text-end">
                            <small class="text-white">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Resumen del pedido -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary">ARS {{ number_format($order->total, 2, ',', '.') }}</h6>
                            <p class="text-muted mb-0">Total del pedido</p>
                        </div>
                        <div class="col-md-6 text-end">
                            @if($order->isPaid())
                                <a href="#" onclick="window.print()" class="btn btn-outline-primary btn-sm">
                                    🖨️ Imprimir Factura
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Items del pedido -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mb-3">📦 Productos Comprados</h6>
                            @foreach($order->items as $item)
                                <div class="d-flex align-items-center mb-3 p-3 border rounded">
                                    <img src="{{ $item->product->image_url ?? 'https://picsum.photos/seed/petcute' . $item->product_id . '/100/100.jpg' }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="rounded me-3" 
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                    <div class="ms-4">
                                        <h6 class="mb-1">{{ $item->product->name }}</h6>
                                        <div class="text-muted mb-2">
                                            <small class="d-block">Talla: {{ ['XS','S','M','L','XL'][$item->size - 1] }}</small>
                                            <small class="d-block">Cantidad: {{ $item->quantity }}</small>
                                            <small class="d-block">Precio unitario: ARS {{ number_format($item->price_at_purchase, 2, ',', '.') }}</small>
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-success">ARS {{ number_format($item->price_at_purchase * $item->quantity, 2, ',', '.') }}</strong><br>
                                            <small class="text-muted">Subtotal</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Información de envío -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="mb-3">📦 Información de Envío</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Dirección de Envío:</strong><br>
                                            <p>{{ $order->shipping_address ?? 'No especificada' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Teléfono de Contacto:</strong><br>
                                            <p>{{ $order->phone_number ?? 'No especificado' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Métodos de pago -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="mb-3">💳 Información de Pago</h6>
                            @if($order->payments->isNotEmpty())
                                @foreach($order->payments as $payment)
                                    <div class="card mb-3">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $payment->payment_method ?? 'Mercado Pago' }}</strong>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge {{ $payment->status_badge }}">
                                                    {{ $payment->status_text }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @if($payment->mercado_pago_payment_id)
                                                <div class="mb-2">
                                                    <small class="text-muted">ID de Transacción:</small><br>
                                                    <code>{{ $payment->mercado_pago_payment_id }}</code>
                                                </div>
                                            @endif
                                            <div class="mb-2">
                                                <small class="text-muted">Fecha del Pago:</small><br>
                                                {{ $payment->created_at->format('d/m/Y H:i') }}
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">Tipo de Pago:</small><br>
                                                {{ $payment->payment_type ?? 'No especificado' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <h6>💳 Información de Pago</h6>
                                    <p>No hay información de pago disponible para este pedido.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Acciones -->
                    @if($order->canBeCancelled())
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de cancelar este pedido? Esta acción no se puede deshacer.')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-lg">
                                        ❌ Cancelar Pedido
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-primary me-2">
                ← Volver a Mis Pedidos
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                🛒 Seguir Comprando
            </a>
        </div>
    </div>
</div>

<!-- Estilos específicos para detalles -->
<style>
.order-summary {
    background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.payment-info .card {
    border-left: 4px solid #28a745;
    margin-bottom: 1rem;
}

.item-card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
    border-radius: 10px;
}

.item-card:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0.15);
}

.status-badge {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    border-radius: 25px;
}

@media (max-width: 768px) {
    .order-summary {
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .item-card {
        margin-bottom: 0.5rem;
    }
}
</style>

<!-- Script para imprimir -->
<script>
function printOrder() {
    window.print();
}
</script>
@endsection