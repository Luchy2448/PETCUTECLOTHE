@extends('layouts.app')

@section('title', 'Registro - PET CUTE CLOTHES')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center mb-4">📝 Crear Cuenta</h2>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">👤 Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="Tu nombre">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="lastname" class="form-label">👤 Apellido</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" value="{{ old('lastname') }}" placeholder="Tu apellido (opcional)">
                            @error('lastname')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="dni" class="form-label">🪪 DNI</label>
                            <input type="text" class="form-control" id="dni" name="dni" value="{{ old('dni') }}" placeholder="Tu DNI (opcional)">
                            @error('dni')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">📧 Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="tu@email.com">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">🔑 Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Mínimo 6 caracteres">
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">🔑 Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Repite tu contraseña">
                            @error('password_confirmation')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                📝 Crear Cuenta
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a></p>
                        <p><a href="{{ route('home') }}">← Volver al inicio</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
