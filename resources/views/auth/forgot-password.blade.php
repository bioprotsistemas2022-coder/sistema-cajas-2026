<x-guest-layout>
    <p class="text-muted small mb-4">¿Olvidaste tu contraseña? Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label form-label-bio">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="form-control form-control-lg form-control-bio @error('email') is-invalid @enderror"
                   placeholder="tu@bioimplant.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 mb-4" style="background:linear-gradient(135deg,#3b82f6,#8b5cf6);border:none;font-weight:700;">
            <i class="bi bi-envelope me-2"></i> Enviar enlace de recuperación
        </button>
    </form>

    <div class="text-center">
        <a href="{{ route('login') }}" class="small fw-semibold" style="color:#64748b;text-decoration:none;">
            <i class="bi bi-arrow-left me-1"></i> Volver al login
        </a>
    </div>
</x-guest-layout>
