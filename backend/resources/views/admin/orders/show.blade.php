@extends('layouts.admin')

@section('title', "Pedido #{$order->id} - PET CUTE CLOTHES")
@section('breadcrumb', 'Detalle Pedido')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-0">📋 Pedido #{{ $order->id }}</h1>
                        <p class="text-muted mb-0">Detalles del pedido</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                            ← Volver a Pedidos
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">👤 Cliente</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Nombre:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Teléfono:</strong> {{ $order->phone ?? 'No especificado' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">🚚 Envío</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Dirección:</strong> {{ $order->shipping_address ?? 'No especificada' }}
                        </p>
                        <p class="mb-1"><strong>Nombre:</strong> {{ $order->shipping_name ?? 'N/A' }}
                            {{ $order->shipping_lastname ?? '' }}</p>
                        <p class="mb-1"><strong>DNI:</strong> {{ $order->shipping_dni ?? 'No especificado' }}</p>
                        @if ($order->tracking_number)
                            <p class="mb-0"><strong>Tracking:</strong> {{ $order->tracking_number }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">💳 Pago</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Método:</strong>
                            @switch($order->payment_method)
                                @case('cash')
                                    💵 Efectivo (5% OFF)
                                @break

                                @case('transfer')
                                    🏦 Transferencia
                                @break

                                @case('whatsapp')
                                    📱 WhatsApp
                                @break

                                @default
                                    {{ $order->payment_method }}
                            @endswitch
                        </p>
                        <p class="mb-1"><strong>Total:</strong> ${{ number_format($order->total, 0, ',', '.') }}</p>
                        <p class="mb-0"><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📦 Estado del Pedido</h5>
                        @php
                            $badges = [
                                'pending' => 'bg-warning',
                                'processing' => 'bg-info',
                                'shipped' => 'bg-primary',
                                'delivered' => 'bg-success',
                                'cancelled' => 'bg-danger',
                            ];
                            $badgeClass = $badges[$order->status] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $badgeClass }} fs-6">{{ ucfirst($order->status) }}</span>
                    </div>
                    <div class="card-body">
                        @if ($order->status !== 'cancelled' && $order->status !== 'delivered')
                            <hr>
                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}"
                                class="row g-3 align-items-end">
                                @csrf
                                @method('PATCH')

                                <div class="col-md-4">
                                    <label class="form-label">Cambiar Estado</label>
                                    <select name="status" class="form-select" required>
                                        <option value="">Seleccionar...</option>
                                        @if ($order->status == 'pending')
                                            <option value="processing">🔄 Marcar como En Proceso</option>
                                            <option value="cancelled">❌ Cancelar Pedido</option>
                                        @elseif($order->status == 'processing')
                                            <option value="shipped">🚚 Marcar como Enviado</option>
                                            <option value="cancelled">❌ Cancelar Pedido</option>
                                        @elseif($order->status == 'shipped')
                                            <option value="delivered">✅ Marcar como Entregado</option>
                                        @endif
                                    </select>
                                </div>

                                @if ($order->status == 'processing' || $order->status == 'shipped')
                                    <div class="col-md-4">
                                        <label class="form-label">Número de Tracking</label>
                                        <input type="text" name="tracking_number" class="form-control"
                                            value="{{ $order->tracking_number }}" placeholder="Opcional">
                                    </div>
                                @endif

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Actualizar</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">🛍️ Productos</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Talla</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $tallas = ['XS', 'S', 'M', 'L', 'XL']; @endphp
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->product->name ?? 'Producto #' . $item->product_id }}</strong>
                                            </td>
                                            <td>{{ isset($tallas[$item->size - 1]) ? $tallas[$item->size - 1] : $item->size }}
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>${{ number_format($item->price ?? 0, 0, ',', '.') }}</td>
                                            <td><strong>${{ number_format(($item->price ?? 0) * $item->quantity, 0, ',', '.') }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
