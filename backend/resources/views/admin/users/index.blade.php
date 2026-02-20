@extends('layouts.admin')

@section('title', 'Usuarios - PET CUTE CLOTHES Admin')
@section('breadcrumb', 'Usuarios')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-0">👥 Usuarios</h1>
            <p class="text-muted">Gestiona los usuarios del sistema</p>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Pedidos</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->is_admin)
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-secondary">Usuario</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $user->orders_count ?? 0 }}</span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if(!$user->is_admin)
                                <form action="{{ route('admin.users.makeAdmin', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Hacer admin a este usuario?')">
                                        <i class="bi bi-shield-check"></i>
                                    </button>
                                </form>
                                @else
                                <span class="badge bg-light text-muted">Admin</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay usuarios registrados
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
        {{ $users->links() }}
    </div>
</div>
@endsection
