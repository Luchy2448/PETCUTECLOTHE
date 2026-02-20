@extends('layouts.app')

@section('title', 'Checkout - PET CUTE CLOTHES')

@section('content')
    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-center">
                    🛒 Finalizar Compra
                </h2>
                <div class="text-center mt-2">
                    <span class="badge bg-secondary">1. Envío</span>
                    <span class="mx-2">></span>
                    <span class="badge bg-primary">2. Pago</span>
                    <span class="mx-2">></span>
                    <span class="badge bg-secondary">3. Confirmación</span>
                </div>
            </div>
        </div>

        <!-- Mensajes de sesión -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
                <strong>¡Éxito!</strong> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
                <strong>¡Error!</strong> {{ session('error') }}
            </div>
        @endif

        @if ($cartItems->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ddd;"></i>
                <h3 class="mt-3">Tu carrito está vacío</h3>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg mt-3">Ver Productos</a>
            </div>
        @else
            <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                @csrf

                <div class="row">
                    <!-- Columna Izquierda: Formulario -->
                    <div class="col-lg-7">

                        <!-- 1. Información de Contacto -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">📧 Información de Contacto</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $user->email ?? '') }}" placeholder="tu@email.com" required>
                                    @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">¿Ya tenés una cuenta?</small>
                                    <a href="{{ route('login') }}" class="small">Iniciar sesión</a>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Método de Envío -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">🚚 Método de Envío</h5>
                            </div>
                            <div class="card-body">
                                <!-- Opción 1: Envío a domicilio -->
                                <div class="form-check mb-3 shipping-option" onclick="selectShippingMethod('delivery')">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="delivery"
                                        value="delivery" {{ old('shipping_method') == 'delivery' ? 'checked' : '' }}
                                        required>
                                    <label class="form-check-label w-100" for="delivery">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>📦 Envío a domicilio</strong>
                                                <p class="text-muted small mb-0">Entrega en tu dirección por mensajería o
                                                    Uber</p>
                                            </div>
                                            <span class="badge bg-success">Recomendado</span>
                                        </div>
                                    </label>
                                </div>

                                <!-- Campos para entrega a domicilio -->
                                <div id="delivery-fields"
                                    class="delivery-fields ps-4 {{ old('shipping_method') == 'delivery' ? '' : 'd-none' }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="delivery_name" class="form-label">Nombre *</label>
                                            <input type="text" class="form-control" id="delivery_name"
                                                name="delivery_name" value="{{ old('delivery_name', $user->name ?? '') }}"
                                                required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="delivery_lastname" class="form-label">Apellido *</label>
                                            <input type="text" class="form-control" id="delivery_lastname"
                                                name="delivery_lastname" value="{{ old('delivery_lastname') }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="delivery_dni" class="form-label">DNI *</label>
                                            <input type="text" class="form-control" id="delivery_dni" name="delivery_dni"
                                                value="{{ old('delivery_dni') }}" placeholder="12345678" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="delivery_phone" class="form-label">Teléfono *</label>
                                            <input type="text" class="form-control" id="delivery_phone"
                                                name="delivery_phone" value="{{ old('delivery_phone') }}"
                                                placeholder="3815123456" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="delivery_address" class="form-label">Dirección *</label>
                                        <input type="text" class="form-control" id="delivery_address"
                                            name="delivery_address" value="{{ old('delivery_address') }}"
                                            placeholder="Calle y número" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="delivery_city" class="form-label">Ciudad *</label>
                                            <input type="text" class="form-control" id="delivery_city"
                                                name="delivery_city" value="{{ old('delivery_city') }}"
                                                placeholder="San Miguel de Tucumán" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="delivery_zipcode" class="form-label">Código Postal *</label>
                                            <input type="text" class="form-control" id="delivery_zipcode"
                                                name="delivery_zipcode" value="{{ old('delivery_zipcode') }}"
                                                placeholder="4000" required>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Opción 2: Retiro en sucursal -->
                                <div class="form-check mb-3 shipping-option" onclick="selectShippingMethod('pickup')">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="pickup"
                                        value="pickup" {{ old('shipping_method') == 'pickup' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="pickup">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>🏪 Retiro en sucursal</strong>
                                                <p class="text-muted small mb-0">Retira en nuestro local</p>
                                            </div>
                                            <span class="badge bg-info">Gratis</span>
                                        </div>
                                    </label>
                                </div>

                                <!-- Campos para retiro en sucursal -->
                                <div id="pickup-fields"
                                    class="pickup-fields ps-4 {{ old('shipping_method') == 'pickup' ? '' : 'd-none' }}">
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Dirección de retiro:</strong><br>
                                        Av. Sarmiento 1234, San Miguel de Tucumán<br>
                                        Lunes a Viernes de 9:00 a 18:00 hs
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="pickup_name" class="form-label">Nombre *</label>
                                            <input type="text" class="form-control" id="pickup_name"
                                                name="pickup_name" value="{{ old('pickup_name', $user->name ?? '') }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="pickup_lastname" class="form-label">Apellido *</label>
                                            <input type="text" class="form-control" id="pickup_lastname"
                                                name="pickup_lastname" value="{{ old('pickup_lastname') }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="pickup_dni" class="form-label">DNI *</label>
                                            <input type="text" class="form-control" id="pickup_dni" name="pickup_dni"
                                                value="{{ old('pickup_dni') }}" placeholder="12345678">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="pickup_phone" class="form-label">Teléfono *</label>
                                            <input type="text" class="form-control" id="pickup_phone"
                                                name="pickup_phone" value="{{ old('pickup_phone') }}"
                                                placeholder="3815123456">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Método de Pago -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">💳 Método de Pago</h5>
                            </div>
                            <div class="card-body">
                                <!-- Opción 1: Efectivo -->
                                <div class="form-check mb-3 payment-option" onclick="selectPaymentMethod('cash')">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cash"
                                        value="cash" {{ old('payment_method') == 'cash' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100" for="cash">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>💵 Efectivo</strong>
                                                <p class="text-muted small mb-0">5% OFF - Pagá en el local o cuando retire
                                                </p>
                                            </div>
                                            <span class="badge bg-success">5% OFF</span>
                                        </div>
                                    </label>
                                </div>

                                <hr>

                                <!-- Opción 2: Transferencia -->
                                <div class="form-check mb-3 payment-option" onclick="selectPaymentMethod('transfer')">
                                    <input class="form-check-input" type="radio" name="payment_method" id="transfer"
                                        value="transfer" {{ old('payment_method') == 'transfer' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="transfer">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>🏦 Transferencia bancaria</strong>
                                                <p class="text-muted small mb-0">Transferí a nuestra cuenta</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <hr>

                                <!-- Opción 3: Acordar por WhatsApp -->
                                <div class="form-check mb-3 payment-option" onclick="selectPaymentMethod('whatsapp')">
                                    <input class="form-check-input" type="radio" name="payment_method" id="whatsapp"
                                        value="whatsapp" {{ old('payment_method') == 'whatsapp' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="whatsapp">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>📱 Acordar por WhatsApp</strong>
                                                <p class="text-muted small mb-0">Comunicate con nosotros para acordar el
                                                    envío</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Información de contacto -->
                                <div class="alert alert-info mt-3 mb-0">
                                    <i class="fab fa-whatsapp"></i>
                                    <strong> WhatsApp:</strong> 3815152840 (Luciana Morales)
                                </div>
                            </div>
                        </div>

                        <!-- 4. Observaciones -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">📝 Observaciones</h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" name="notes" rows="3"
                                    placeholder="Alguna observación adicional para tu pedido...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- Botón Volver -->
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary mb-4">
                            ← Volver al Carrito
                        </a>
                    </div>

                    <!-- Columna Derecha: Resumen del Pedido -->
                    <div class="col-lg-5">
                        <div class="card position-sticky" style="top: 20px;">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">📋 Resumen del Pedido</h5>
                            </div>
                            <div class="card-body">
                                <!-- Items del carrito -->
                                @php
                                    $tallas = ['XS', 'S', 'M', 'L', 'XL'];
                                    $subtotalVista = 0;
                                @endphp

                                @foreach ($cartItems as $item)
                                    @php
                                        $tallaTexto = isset($tallas[$item->product->size - 1])
                                            ? $tallas[$item->product->size - 1]
                                            : $item->product->size;
                                        $itemSubtotal = $item->product->price * $item->quantity;
                                        $subtotalVista += $itemSubtotal;
                                    @endphp
                                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                        <div class="position-relative">
                                            <img src="{{ $item->product->image_url ?? 'https://picsum.photos/seed/petcute' . $item->product_id . '/60/60.jpg' }}"
                                                alt="{{ $item->product->name }}" class="rounded"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                                {{ $item->quantity }}
                                            </span>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <h6 class="mb-0 small">{{ $item->product->name }}</h6>
                                            <small class="text-muted">Talla: {{ $tallaTexto }}</small>
                                        </div>
                                        <div class="text-end">
                                            <strong>${{ number_format($itemSubtotal, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Totales -->
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal ({{ $cartItems->sum('quantity') }} productos):</span>
                                    <span>${{ number_format($subtotalVista, 0, ',', '.') }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Envío:</span>
                                    <span id="shipping-cost">
                                        @if ($subtotalVista > 50000)
                                            <span class="text-success">Gratis</span>
                                        @else
                                            ${{ number_format(3000, 0, ',', '.') }}
                                        @endif
                                    </span>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong class="text-primary" id="total-amount">
                                        ${{ number_format($subtotalVista + ($subtotalVista > 50000 ? 0 : 3000), 0, ',', '.') }}
                                    </strong>
                                </div>

                                <!-- Botón de pago -->
                                <button type="submit" class="btn btn-primary w-100 py-3" id="submitBtn">
                                    💳 Finalizar Compra
                                </button>

                                <p class="text-center text-muted small mt-2 mb-0">
                                    <i class="fas fa-lock"></i> Pago seguro
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>

    <script>
        const STORAGE_KEY = 'checkout_form_data';

        function saveFormData() {
            const form = document.getElementById('checkoutForm');
            const formData = new FormData(form);
            const data = {};

            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }

            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        }

        function loadFormData() {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (!saved) return;

            try {
                const data = JSON.parse(saved);
                const form = document.getElementById('checkoutForm');

                for (let key in data) {
                    const field = form.elements[key];
                    if (field) {
                        if (field.type === 'radio' || field.type === 'checkbox') {
                            field.checked = (field.value === data[key]);
                        } else {
                            field.value = data[key];
                        }
                    }
                }

                if (data.shipping_method === 'delivery') {
                    selectShippingMethod('delivery');
                } else if (data.shipping_method === 'pickup') {
                    selectShippingMethod('pickup');
                }
            } catch (e) {
                console.error('Error loading saved form data:', e);
            }
        }

        function clearFormData() {
            localStorage.removeItem(STORAGE_KEY);
        }

        function selectShippingMethod(method) {
            // Actualizar radios
            document.getElementById('delivery').checked = (method === 'delivery');
            document.getElementById('pickup').checked = (method === 'pickup');

            // Mostrar/ocultar campos
            const deliveryFields = document.getElementById('delivery-fields');
            const pickupFields = document.getElementById('pickup-fields');

            if (method === 'delivery') {
                deliveryFields.classList.remove('d-none');
                pickupFields.classList.add('d-none');

                // Hacer campos requeridos
                toggleRequired('delivery', true);
                toggleRequired('pickup', false);
            } else {
                deliveryFields.classList.add('d-none');
                pickupFields.classList.remove('d-none');

                // Hacer campos requeridos
                toggleRequired('delivery', false);
                toggleRequired('pickup', true);
            }
        }

        function selectPaymentMethod(method) {
            // Actualizar radios de pago
            document.getElementById('cash').checked = (method === 'cash');
            document.getElementById('transfer').checked = (method === 'transfer');
            document.getElementById('whatsapp').checked = (method === 'whatsapp');

            // Guardar en localStorage
            saveFormData();
        }

        function toggleRequired(method, required) {
            const fields = method === 'delivery' ? ['delivery_name', 'delivery_lastname', 'delivery_dni', 'delivery_phone',
                'delivery_address',
                'delivery_city', 'delivery_zipcode'
            ] : ['pickup_name', 'pickup_lastname', 'pickup_dni', 'pickup_phone'];

            fields.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.required = required;
                }
            });
        }

        // Inicializar al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            loadFormData();

            const deliveryChecked = document.getElementById('delivery').checked;
            const pickupChecked = document.getElementById('pickup').checked;

            if (deliveryChecked) {
                selectShippingMethod('delivery');
            } else if (pickupChecked) {
                selectShippingMethod('pickup');
            } else {
                // Por defecto, seleccionar delivery
                selectShippingMethod('delivery');
            }
        });

        // Validar formulario antes de enviar
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '🔄 Procesando...';

            saveFormData();
        });

        // Guardar datos cuando cambian los campos
        document.getElementById('checkoutForm').addEventListener('input', function() {
            saveFormData();
        });

        // Guardar datos cuando cambian los radio buttons
        document.getElementById('checkoutForm').addEventListener('change', function(e) {
            if (e.target.type === 'radio') {
                saveFormData();
            }
        });
    </script>

    <style>
        .shipping-option {
            cursor: pointer;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .shipping-option:hover {
            border-color: #FFB6C1;
            background-color: #fff5f7;
        }

        .shipping-option input:checked+label {
            color: inherit;
        }

        .shipping-option:has(input:checked) {
            border-color: #FFB6C1;
            background-color: #fff5f7;
        }

        .delivery-fields,
        .pickup-fields {
            border-left: 3px solid #FFB6C1;
            padding-left: 15px;
            margin-top: 15px;
        }
    </style>
@endsection
