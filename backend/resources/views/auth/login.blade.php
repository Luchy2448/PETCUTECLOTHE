@extends('layouts.app')

@section('title', 'Login - PET CUTE CLOTHES')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center mb-4">🔐 Iniciar Sesión</h2>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">📧 Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="tu@email.com">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">🔑 Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                🔐 Iniciar Sesión
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p>¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
                        <p><a href="{{ route('home') }}">← Volver al inicio</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
