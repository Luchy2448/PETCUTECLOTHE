@extends('layouts.app')

@section('title', 'Pedido Confirmado - PET CUTE CLOTHES')

@section('content')
<script>
    // Limpiar datos guardados del checkout
    localStorage.removeItem('checkout_form_data');
</script>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header de éxito -->
            <div class="text-center mb-5">
                <div style="font-size: 4rem;">✅</div>
                <h1 class="text-success mt-3">¡Pedido Confirmado!</h1>
                <p class="lead">Un paso más, <strong>{{ $order->user->name }}</strong>.</p>
                <p>Tu orden #{{ $order->id }} fue procesada correctamente.</p>
            </div>

            <!-- Información del pedido -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📋 Información de tu compra</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Método de envío:</strong>
                            <p class="mb-0">
                                @if(strpos($order->notes, 'Retiro en sucursal') !== false)
                                    🏪 Retiro en sucursal
                                @else
                                    📦 Envío a domicilio
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong>Estado del envío:</strong>
                            <p class="mb-0">⏳ Pendiente</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Método de pago:</strong>
                            <p class="mb-0">
                                @switch($order->payment_method)
                                    @case('cash')
                                        💵 Efectivo (5% OFF)
                                        @break
                                    @case('transfer')
                                        🏦 Transferencia bancaria
                                        @break
                                    @case('whatsapp')
                                        📱 Acordar por WhatsApp
                                        @break
                                    @default
                                        No especificado
                                @endswitch
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong>Total:</strong>
                            <p class="mb-0 text-primary h5">
                                ${{ number_format($order->payment_method === 'cash' ? $order->total * 0.95 : $order->total, 0, ',', '.') }}
                                @if($order->payment_method === 'cash')
                                    <small class="text-success">(5% OFF aplicado)</small>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos para transferencia -->
            @if($order->payment_method === 'transfer')
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">🏦 Datos para Transferencia</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Importante:</strong> Consultar los datos bancarios antes de abonar.
                        <hr>
                        <p class="mb-0">Debe solicitar los datos para el pago por WhatsApp:</p>
                        <a href="https://wa.me/543815152840" class="btn btn-success mt-2">
                            <i class="fab fa-whatsapp"></i> Natalia Cabral 3814459268
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- WhatsApp para acordar -->
            @if($order->payment_method === 'whatsapp')
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">📱 Acuerdo de Pago</h5>
                </div>
                <div class="card-body text-center">
                    <p>Para coordinar el pago y envío, comunicate con nosotros:</p>
                    <a href="https://wa.me/543815152840" class="btn btn-success btn-lg">
                        <i class="fab fa-whatsapp"></i> Escribir por WhatsApp
                    </a>
                </div>
            </div>
            @endif

            <!-- Resumen de productos -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">🛒 Productos</h5>
                </div>
                <div class="card-body">
                    @php
                        $tallas = ['XS','S','M','L','XL'];
                    @endphp
                    
                    @foreach($order->items as $item)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <img src="{{ $item->product->image_url ?? 'https://picsum.photos/seed/petcute' . $item->product_id . '/50/50.jpg' }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="rounded" 
                                 style="width: 50px; height: 50px; object-fit: cover;">
                            <div class="ms-3 flex-grow-1">
                                <h6 class="mb-0">{{ $item->product->name }}</h6>
                                <small class="text-muted">
                                    Cantidad: {{ $item->quantity }}
                                    @if(isset($tallas[$item->product->size - 1]))
                                        | Talla: {{ $tallas[$item->product->size - 1] }}
                                    @endif
                                </small>
                            </div>
                            <div class="text-end">
                                <strong>${{ number_format($item->price * $item->quantity, 0, ',</strong>
                            ', '.') }}</div>
                        </div>
                    @endforeach
                    
                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span>${{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                    @if($order->payment_method === 'cash')
                    <div class="d-flex justify-content-between text-success">
                        <span>Descuento (5% OFF):</span>
                        <span>-${{ number_format($order->total * 0.05, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong class="text-primary">
                            ${{ number_format($order->payment_method === 'cash' ? $order->total * 0.95 : $order->total, 0, ',', '.') }}
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Información de retiro -->
            @if(strpos($order->notes, 'Retiro en sucursal') !== false)
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">🏪 Información para el Retiro</h5>
                </div>
                <div class="card-body">
                    <p><strong>Dirección:</strong> Av. Sarmiento 1234, San Miguel de Tucumán</p>
                    <p><strong>Horario:</strong> Lunes a Viernes de 9:00 a 18:00 hs</p>
                    <p><strong>Persona que retira:</strong> {{ $order->shipping_name ?? $order->user->name }}</p>
                    <p><strong>DNI:</strong> {{ $order->shipping_dni ?? 'No especificado' }}</p>
                </div>
            </div>
            @endif

            <!-- Botones de acción -->
            <div class="text-center">
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg">
                    🛍️ Seguir Comprando
                </a>
                <a href="https://wa.me/543815152840" class="btn btn-success btn-lg">
                    <i class="fab fa-whatsapp"></i> Contactar
                </a>
            </div>

            <!-- Nota sobre email -->
            <div class="text-center mt-4">
                <p class="text-muted">
                    <i class="fas fa-envelope"></i>
                    Te enviamos un mail a <strong>{{ $order->user->email }}</strong> con los detalles de tu pedido.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection