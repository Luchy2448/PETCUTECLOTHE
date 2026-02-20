@extends('layouts.admin')

@section('title', 'Gestión de Pedidos - PET CUTE CLOTHES')
@section('breadcrumb', 'Pedidos')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">📋 Gestión de Pedidos</h1>
                    <p class="text-muted mb-0">Administra los pedidos de tu tienda</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        ← Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center h-100" style="border-left: 3px solid #FFD700 !important;">
                <div class="card-body">
                    <h3 class="mb-0 text-warning">{{ $stats['pending'] }}</h3>
                    <small class="text-muted">Pendientes</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center h-100" style="border-left: 3px solid #0dcaf0 !important;">
                <div class="card-body">
                    <h3 class="mb-0 text-info">{{ $stats['processing'] }}</h3>
                    <small class="text-muted">Proceso</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center h-100" style="border-left: 3px solid #0d6efd !important;">
                <div class="card-body">
                    <h3 class="mb-0 text-primary">{{ $stats['shipped'] }}</h3>
                    <small class="text-muted">Enviados</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center h-100" style="border-left: 3px solid #198754 !important;">
                <div class="card-body">
                    <h3 class="mb-0 text-success">{{ $stats['delivered'] }}</h3>
                    <small class="text-muted">Entregados</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center h-100" style="border-left: 3px solid #dc3545 !important;">
                <div class="card-body">
                    <h3 class="mb-0 text-danger">{{ $stats['cancelled'] }}</h3>
                    <small class="text-muted">Cancelados</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En proceso</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Enviado</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregado</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="ID, cliente..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Pedidos -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Método de Pago</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>
                                <div>{{ $order->user->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $order->user->email ?? '' }}</small>
                            </td>
                            <td>
                                @php
                                    $itemCount = $order->items->sum('quantity');
                                @endphp
                                {{ $itemCount }} producto(s)
                            </td>
                            <td><strong>${{ number_format($order->total, 0, ',', '.') }}</strong></td>
                            <td>
                                @switch($order->status)
                                    @case('pending')
                                        <span class="badge bg-warning">⏳ Pendiente</span>
                                        @break
                                    @case('processing')
                                        <span class="badge bg-info">🔄 En proceso</span>
                                        @break
                                    @case('shipped')
                                        <span class="badge bg-primary">🚚 Enviado</span>
                                        @break
                                    @case('delivered')
                                        <span class="badge bg-success">✅ Entregado</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">❌ Cancelado</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $order->status }}</span>
                                @endswitch
                            </td>
                            <td>
                                @switch($order->payment_method)
                                    @case('cash')
                                        <span>💵 Efectivo</span>
                                        @break
                                    @case('transfer')
                                        <span>🏦 Transferencia</span>
                                        @break
                                    @case('whatsapp')
                                        <span>📱 WhatsApp</span>
                                        @break
                                    @default
                                        <span>{{ $order->payment_method }}</span>
                                @endswitch
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                    Ver Detalle
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No se encontraron pedidos
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection
