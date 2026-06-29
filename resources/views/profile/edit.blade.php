<x-app-layout>
    <x-slot name="header">Mi Perfil</x-slot>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card card-elegante h-100">
                <div class="card-header">
                    <h5><i class="bi bi-person me-2" style="color:#3b82f6;"></i>Información del Perfil</h5>
                </div>
                <div class="card-body">
                    @if(session('status') === 'profile-updated')
                        <div class="alert alert-success alert-bio alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>Datos actualizados correctamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')
                        <div class="mb-3">
                            <label for="name" class="form-label form-label-bio">Nombre</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
                                   class="form-control form-control-bio @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label form-label-bio">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                                   class="form-control form-control-bio @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-bio">
                                <i class="bi bi-check-lg me-1"></i> Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-elegante h-100">
                <div class="card-header">
                    <h5><i class="bi bi-shield-lock me-2" style="color:#8b5cf6;"></i>Cambiar Contraseña</h5>
                </div>
                <div class="card-body">
                    @if(session('status') === 'password-updated')
                        <div class="alert alert-success alert-bio alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>Contraseña actualizada.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')
                        <div class="mb-3">
                            <label for="current_password" class="form-label form-label-bio">Contraseña Actual</label>
                            <input id="current_password" type="password" name="current_password" autocomplete="current-password"
                                   class="form-control form-control-bio @error('current_password') is-invalid @enderror">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label form-label-bio">Nueva Contraseña</label>
                            <input id="password" type="password" name="password" autocomplete="new-password"
                                   class="form-control form-control-bio @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label form-label-bio">Confirmar Nueva Contraseña</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                                   class="form-control form-control-bio @error('password_confirmation') is-invalid @enderror">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-bio" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                                <i class="bi bi-check-lg me-1"></i> Actualizar contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card card-elegante" style="border:2px solid #fecaca;">
                <div class="card-header" style="background:linear-gradient(135deg,#fef2f2,#fee2e2);">
                    <h5 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Zona de Peligro</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Una vez eliminada tu cuenta, todos tus datos serán eliminados permanentemente. Esta acción no se puede deshacer.</p>
                    <button class="btn btn-outline-danger btn-bio" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash me-1"></i> Eliminar Cuenta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:linear-gradient(135deg,#fef2f2,#fee2e2);">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="modal-body">
                        <p class="text-muted">Para eliminar tu cuenta de forma permanente, ingresa tu contraseña.</p>
                        <div class="mb-3">
                            <label for="password" class="form-label form-label-bio">Contraseña</label>
                            <input id="password" type="password" name="password" required
                                   class="form-control form-control-bio @error('password', 'userDeletion') is-invalid @enderror"
                                   placeholder="Tu contraseña actual">
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger btn-bio">
                            <i class="bi bi-trash me-1"></i> Eliminar cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
