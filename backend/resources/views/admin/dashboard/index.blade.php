@extends('layouts.admin')

@section('title', 'Dashboard - PET CUTE CLOTHES Admin')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-0">📊 Dashboard</h1>
            <p class="text-muted">Resumen de tu tienda</p>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row g-4 mb-4">
        <!-- Total Pedidos -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #FFB6C1 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Total Pedidos</p>
                            <h2 class="mb-0">{{ $stats['total'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">📦</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedidos Pendientes -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #FFD700 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Pendientes</p>
                            <h2 class="mb-0">{{ $stats['pending'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">⏳</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedidos Entregados -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #98FF98 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Entregados</p>
                            <h2 class="mb-0">{{ $stats['delivered'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">✅</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingresos Totales -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ADD8E6 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Ingresos Totales</p>
                            <h2 class="mb-0">${{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">💰</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila -->
    <div class="row g-4">
        <!-- Pedidos por Estado -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">📈 Pedidos por Estado</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><span class="badge bg-warning">⏳</span> Pendientes</span>
                            <strong>{{ $stats['pending'] ?? 0 }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><span class="badge bg-info">🔄</span> En Proceso</span>
                            <strong>{{ $stats['processing'] ?? 0 }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><span class="badge bg-primary">🚚</span> Enviados</span>
                            <strong>{{ $stats['shipped'] ?? 0 }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><span class="badge bg-success">✅</span> Entregados</span>
                            <strong>{{ $stats['delivered'] ?? 0 }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><span class="badge bg-danger">❌</span> Cancelados</span>
                            <strong>{{ $stats['cancelled'] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">⚡ Accesos Rápidos</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.productos.list') }}" class="btn btn-outline-primary btn-lg">
                            🛍️ Gestionar Productos
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-success btn-lg">
                            📋 Ver Pedidos
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg" target="_blank">
                            🏪 Ver Catálogo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🕐 Últimos Pedidos</h5>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">Ver todos</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders ?? [] as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>${{ number_format($order->total, 0, ',', '.') }}</td>
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
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No hay pedidos recientes
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
