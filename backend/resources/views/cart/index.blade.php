@extends('layouts.app')

@section('title', 'Mi Carrito - PET CUTE CLOTHES')

@section('content')
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1>🛒 Mi Carrito</h1>
                <p class="text-muted">Gestiona tus productos seleccionados para mascotas</p>
                <span class="cart-item-count" style="display: none;">{{ $itemCount }}</span>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <strong>¡Éxito!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <strong>¡Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($cartItems->isEmpty())
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ddd;"></i>
                </div>
                <h3>Tu carrito está vacío</h3>
                <p class="text-muted">No hay productos en tu carrito. ¡Ve nuestras <a
                        href="{{ route('products.index') }}">colecciones</a>!</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">🛒 Ir a Comprar</a>
            </div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">🛒 Mi Carrito ({{ $itemCount }} productos)</h5>
                            <form action="{{ route('cart.clear') }}" method="POST"
                                onsubmit="return confirm('¿Vaciar todo el carrito?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm">🗑️ Vaciar Carrito</button>
                            </form>
                        </div>

                        <div class="card-body p-0">
                            @foreach ($cartItems as $item)
                                <div class="cart-item d-flex align-items-center p-3 border-bottom">
                                    <div class="flex-shrink-0 me-3">
                                        <img src="{{ $item->product->image_url ?? 'https://picsum.photos/seed/' . $item->product_id . '/80/80.jpg' }}"
                                            class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>

                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('products.show', $item->product->id) }}"
                                                class="text-decoration-none text-dark fw-bold">
                                                {{ $item->product->name }}
                                            </a>
                                        </h6>
                                        <p class="small text-muted mb-1">{{ Str::limit($item->product->description, 60) }}
                                        </p>
                                        @if ($item->product->category)
                                            <span
                                                class="badge bg-info text-dark small">{{ $item->product->category->name }}</span>
                                        @endif
                                    </div>

                                    <div class="me-4 text-center">
                                        <div class="input-group input-group-sm" style="width: 120px;">
                                            {{-- Extraer variables para evitar problemas --}}
                                            @php
                                                $itemId = $item->id;
                                                $currentQty = $item->quantity;
                                            @endphp
                                            
                                            {{-- Botón de decrementar --}}
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="decrementQuantity('{{ $itemId }}', '{{ $currentQty }}')">➖</button>
                                            
                                            {{-- Input de cantidad --}}
                                            <input type="text" class="form-control text-center"
                                                value="{{ $currentQty }}" readonly>
                                            
                                            {{-- Botón de incrementar --}}
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="incrementQuantity('{{ $itemId }}', '{{ $currentQty }}')">➕</button>
                                        </div>
                                    </div>

                                    <div class="text-end" style="min-width: 150px;">
                                        <div class="fw-bold text-primary">ARS
                                            {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                                        <small class="text-muted d-block">u:
                                            ${{ number_format($item->product->price, 0, ',', '.') }}</small>
                                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST"
                                            class="mt-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-link btn-sm text-danger p-0 text-decoration-none">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="card-footer bg-light p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted">Total a pagar:</span>
                                    <h3 class="text-primary mb-0">$ {{ number_format($total, 2, ',', '.') }}</h3>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Seguir
                                        Comprando</a>
                                    <a href="{{ route('checkout.index') }}" class="btn btn-primary px-4">Proceder al Pago
                                        💳</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function incrementQuantity(itemId, currentQuantity) {
            const newQuantity = Math.min(10, currentQuantity + 1);
            updateQuantity(itemId, newQuantity);
        }

        function decrementQuantity(itemId, currentQuantity) {
            const newQuantity = Math.max(1, currentQuantity - 1);
            updateQuantity(itemId, newQuantity);
        }

        function updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1) return;
            if (newQuantity > 10) {
                alert('Máximo 10 unidades por producto');
                return;
            }

            // Usamos un formulario oculto para actualizar (redirección completa)
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/cart/${itemId}`;

            // Agregar CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }

            // Agregar método PUT
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            // Agregar quantity
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'quantity';
            quantityInput.value = newQuantity;
            form.appendChild(quantityInput);

            // Enviar formulario
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection
