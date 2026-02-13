@extends('layouts.app')

@section('title', 'Mis Pedidos - PET CUTE CLOTHES')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-center">
                📦 Mis Pedidos
            </h1>
            <p class="text-center text-muted">
                Gestiona tus compras y pedidos realizados
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

    <!-- Lista de pedidos -->
    @if($orders->isEmpty())
        <div class="text-center py-5">
            <div class="empty-orders text-center">
                <div class="mb-4">
                    <i class="fas fa-receipt" style="font-size: 4rem; color: #ddd;"></i>
                </div>
                <h3>No tienes pedidos aún</h3>
                <p class="text-muted">
                    ¿Aún no has hecho ninguna compra? <a href="{{ route('products.index') }}" class="link-primary">Ver nuestros productos</a> y añade algunos a tu carrito.
                </p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                    🛒 Ir a Comprar
                </a>
            </div>
        </div>
    @else
        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">🔍 Filtrar por Estado</h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('orders.index') }}">
                            <div class="mb-3">
                                <label class="form-label">Estado:</label>
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pagado</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Procesando</option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Enviado</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregado</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-outline-primary btn-sm">Filtrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de pedidos -->
        <div class="row">
            @foreach($orders as $order)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Pedido #{{ $order->id }}</h6>
                                <span class="badge {{ $order->status_badge }}">
                                    {{ $order->status_text }}
                                </span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <h6 class="text-primary">ARS {{ number_format($order->total, 2, ',', '.') }}</h6>
                                    <small class="text-muted">Total del pedido</small>
                                </div>
                                <div class="col-md-4 text-end">
                                    @if($order->canBeCancelled())
                                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de cancelar este pedido?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                ❌ Cancelar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Items del pedido -->
                            <h6 class="mb-3">📦 Productos del Pedido</h6>
                            @foreach($order->items as $item)
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $item->product->image_url ?? 'https://picsum.photos/seed/petcute' . $item->product_id . '/80/80.jpg' }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="rounded me-2" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <div class="ms-3">
                                            <h6 class="mb-1">{{ $item->product->name }}</h6>
                                            <small class="text-muted">Talla: {{ ['XS','S','M','L','XL'][$item->size - 1] }}</small><br>
                                            <small class="text-muted">Cantidad: {{ $item->quantity }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-1">
                                            <small class="text-muted">Precio unit.</small><br>
                                            <strong class="text-primary">ARS {{ number_format($item->price_at_purchase, 2, ',', '.') }}</strong>
                                        </div>
                                        <div>
                                            <small class="text-muted">Subtotal</small><br>
                                            <strong class="text-success">ARS {{ number_format($item->price_at_purchase * $item->quantity, 2, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Información de envío -->
                            <div class="row mt-3 pt-3 border-top">
                                <div class="col-12">
                                    <h6 class="mb-2">📦 Información de Envío</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Dirección:</strong><br>
                                            <p class="mb-2">{{ $order->shipping_address ?? 'No especificada' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Teléfono:</strong><br>
                                            <p class="mb-0">{{ $order->phone_number ?? 'No especificado' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Métodos de pago -->
                            <div class="row mt-3 pt-3 border-top">
                                <div class="col-12">
                                    <h6 class="mb-2">💳 Información de Pago</h6>
                                    @if($order->payments->isNotEmpty())
                                        @foreach($order->payments as $payment)
                                            <div class="mb-2 p-2 border rounded">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>Método:</strong> {{ $payment->payment_method ?? 'No especificado' }}
                                                    </div>
                                                    <div class="text-end">
                                                        <small class="text-muted">Estado:</small><br>
                                                        <span class="badge {{ $payment->status_badge }}">
                                                            {{ $payment->status_text }}
                                                        </span>
                                                    </div>
                                                </div>
                                                @if($payment->mercado_pago_payment_id)
                                                    <div class="mt-1">
                                                        <small class="text-muted">ID Transacción:</small><br>
                                                        <code>{{ $payment->mercado_pago_payment_id }}</code>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No hay información de pago disponible</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="row mt-3 pt-3 border-top">
                                <div class="col-12 text-center">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary me-2">
                                        📋 Ver Detalles
                                    </a>
                                    <button onclick="window.print()" class="btn btn-outline-secondary">
                                        🖨️ Imprimir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Paginación -->
    @if($orders->hasPages())
        <div class="row justify-content-center mt-4">
            <div class="col-auto">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Estilos adicionales para esta página -->
<style>
.empty-orders {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 3rem;
    margin: 2rem 0;
}

.order-card .card-body {
    padding: 1.5rem;
}

.order-item {
    transition: all 0.3s ease;
}

.order-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
}

.badge {
    font-size: 0.8rem;
    padding: 0.5rem 0.8rem;
    border-radius: 20px;
}

@media (max-width: 768px) {
    .badge {
        font-size: 0.7rem;
    }
    
    .order-card {
        margin-bottom: 1rem;
    }
}
</style>
@endsection