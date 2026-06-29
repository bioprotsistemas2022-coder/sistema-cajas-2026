<x-guest-layout>
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @enderror

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="form-label form-label-bio">Email corporativo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="form-control form-control-lg form-control-bio @error('email') is-invalid @enderror"
                   placeholder="nombre@bioimplant.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="form-label form-label-bio">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="form-control form-control-lg form-control-bio @error('password') is-invalid @enderror"
                   placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label for="remember_me" class="form-check-label small" style="color:#64748b;">Recordarme</label>
            </div>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small fw-semibold" style="color:#3b82f6;">¿Olvidaste tu contraseña?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 mb-3" style="background:linear-gradient(135deg,#3b82f6,#8b5cf6);border:none;font-weight:700;font-size:0.95rem;">
            <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión
        </button>
    </form>

    <p class="text-center small mb-0" style="color:#94a3b8;">
        &copy; {{ date('Y') }} Bioimplant SRL
    </p>
</x-guest-layout>
