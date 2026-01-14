@extends('layouts.app')

@section('title', $producto->name . ' - Admin - PET CUTE CLOTHES')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">🏠 Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">📦 Productos</a></li>
                <li class="breadcrumb-item active">👁️ Detalles</li>
            </ol>
        </nav>
    </div>

    <!-- Detalle de Producto -->
    <div class="row">
        <div class="col-md-6 mb-4">
            @if($producto->image_url)
                <img src="{{ $producto->image_url }}" alt="{{ $producto->name }}" class="img-fluid rounded shadow" style="width: 100%; height: 400px; object-fit: cover;">
            @else
                <div class="rounded shadow d-flex align-items-center justify-content-center" style="width: 100%; height: 400px; background: linear-gradient(135deg, var(--color-rosa) 0%, var(--color-celeste) 100%); color: white; font-size: 5rem;">
                    🐾
                </div>
            @endif
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-secondary">ID: {{ $producto->id }}</span>
                        @if($producto->category)
                            <span class="badge {{ $producto->category->name == 'Casual' ? 'badge-casual' : ($producto->category->name == 'Elegante' ? 'badge-elegante' : 'badge-cumpleanos') }}">
                                {{ $producto->category->name }}
                            </span>
                        @endif
                    </div>

                    <h1 class="card-title mb-3">{{ $producto->name }}</h1>
                    <p class="card-text">{{ $producto->description }}</p>

                    <hr>

                    <div class="mb-3">
                        <strong>💰 Precio:</strong>
                        <span class="price">ARS {{ number_format($producto->price, 0, ',', '.') }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>📏 Talla:</strong>
                        <span class="talla-badge">{{ ['XS','S','M','L','XL'][$producto->size - 1] }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>📦 Stock:</strong>
                        <span class="stock-badge {{ $producto->stock > 0 ? 'stock-available' : 'stock-low' }}">
                            {{ $producto->stock > 0 ? 'Disponible ('.$producto->stock.' unidades)' : 'Agotado' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>🔗 URL de Imagen:</strong>
                        <br>
                        <a href="{{ $producto->image_url }}" target="_blank" class="text-break">{{ $producto->image_url }}</a>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            ← Volver a Productos
                        </a>
                        <a href="{{ route('admin.products.edit', $producto->id) }}" class="btn btn-success">
                            ✏️ Editar Producto
                        </a>
                        <button onclick="eliminarProducto({{ $producto->id }})" class="btn btn-danger">
                            🗑️ Eliminar Producto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function eliminarProducto(id) {
        if(confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/products/' + id;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection
