<x-app-layout>
    <x-slot name="header">Gestión de Técnicos</x-slot>

    <div class="row g-4 mb-4">
        <div class="col-4">
            <div class="stat-card h-100 text-center py-4" style="background:linear-gradient(135deg,#f8fafc,#e2e8f0);">
                <div class="stat-value mb-1" style="color:#0f172a;">{{ $tecnicos->count() }}</div>
                <div class="stat-label" style="color:#64748b;">Registrados</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card h-100 text-center py-4" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
                <div class="stat-value text-primary mb-1">{{ $tecnicos->filter(fn($t) => $t->cirugias->contains('status', 'EN_CURSO'))->count() }}</div>
                <div class="stat-label text-primary">En Cirugía</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card h-100 text-center py-4" style="background:linear-gradient(135deg,#1e293b,#0f172a);">
                <div class="stat-value text-white mb-1">{{ $tecnicos->sum(fn($t) => $t->cirugias->count()) }}</div>
                <div class="stat-label text-white-50">Procedimientos</div>
            </div>
        </div>
    </div>

    <div class="card card-elegante mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0"><i class="bi bi-people me-2" style="color:#3b82f6;"></i>Técnicos</h5>
                <div class="input-group" style="width:260px;">
                    <span class="input-group-text border-0 bg-light">
                        <i class="bi bi-search" style="color:#94a3b8;"></i>
                    </span>
                    <input type="text" id="buscadorTecnico" class="form-control border-0 bg-light" placeholder="Buscar nombre o email...">
                </div>
            </div>
            <button class="btn btn-primary btn-bio btn-sm px-4" data-bs-toggle="modal" data-bs-target="#modal-crear">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Usuario
            </button>
        </div>

        <div class="card-body p-0">
            @forelse($tecnicos as $tecnico)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom" data-tecnico="{{ $tecnico->name }} {{ $tecnico->email }}" style="transition:all 0.15s ease;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center text-white fw-bold rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);font-size:1rem;">
                            {{ substr($tecnico->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="fw-semibold" style="color:#0f172a;">{{ $tecnico->name }}</div>
                            <small style="color:#94a3b8;">{{ $tecnico->email }}</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="text-end">
                            <div class="small fw-bold" style="color:#3b82f6;">{{ $tecnico->cirugias->count() }}</div>
                            <small style="color:#94a3b8;font-size:0.65rem;text-transform:uppercase;">cirugías</small>
                        </div>
                        @if($tecnico->cirugias->contains('status', 'EN_CURSO'))
                            <span class="badge badge-primary p-2" style="animation:pulse 2s infinite;">
                                <i class="bi bi-heart-pulse me-1"></i> En CX
                            </span>
                        @endif
                        <button class="btn btn-outline-primary btn-bio btn-sm" onclick='editarTecnico(@json($tecnico))'>
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.tecnicos.destroy', $tecnico) }}" class="d-inline" onsubmit="return confirm('¿Eliminar este técnico? Se perderán sus datos.')">
                            @csrf @method('delete')
                            <button type="submit" class="btn btn-outline-danger btn-bio btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light mb-3" style="width:64px;height:64px;">
                        <i class="bi bi-people fs-3" style="color:#cbd5e1;"></i>
                    </div>
                    <p class="text-muted mb-0">No hay técnicos registrados.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" id="modal-crear" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2" style="color:#3b82f6;"></i>Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.tecnicos.store') }}">
                    @csrf
                    <div class="modal-body px-4 py-4">
                        <div class="mb-3">
                            <label class="form-label form-label-bio">Rol</label>
                            <select name="role" class="form-select form-control-bio" required>
                                <option value="tecnico">Técnico</option>
                                <option value="logistica">Logística</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label form-label-bio">Nombre Completo</label>
                            <input type="text" name="name" required class="form-control form-control-bio" placeholder="Nombre y apellido">
                        </div>
                        <div class="mb-3">
                            <label class="form-label form-label-bio">Email</label>
                            <input type="email" name="email" required class="form-control form-control-bio" placeholder="tecnico@bioimplant.com">
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label form-label-bio">Contraseña <span class="text-muted fw-normal">(mín. 8)</span></label>
                                <input type="password" name="password" required minlength="8" class="form-control form-control-bio">
                            </div>
                            <div class="col-6">
                                <label class="form-label form-label-bio">Confirmar</label>
                                <input type="password" name="password_confirmation" required class="form-control form-control-bio">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                        <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-bio"><i class="bi bi-check-lg me-1"></i> Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-editar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil me-2" style="color:#8b5cf6;"></i>Editar Técnico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="form-editar" action="">
                    @csrf @method('put')
                    <div class="modal-body px-4 py-4">
                        <div class="mb-3">
                            <label class="form-label form-label-bio">Nombre Completo</label>
                            <input type="text" name="name" id="edit-name" required class="form-control form-control-bio">
                        </div>
                        <div class="mb-3">
                            <label class="form-label form-label-bio">Email</label>
                            <input type="email" name="email" id="edit-email" required class="form-control form-control-bio">
                        </div>
                        <div class="alert bg-light py-2 px-3 rounded-3 mb-3" style="font-size:0.85rem;color:#64748b;">
                            <i class="bi bi-info-circle me-1"></i> Dejar contraseña en blanco para mantener la actual.
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label form-label-bio">Nueva Contraseña</label>
                                <input type="password" name="password" class="form-control form-control-bio" minlength="8">
                            </div>
                            <div class="col-6">
                                <label class="form-label form-label-bio">Confirmar</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-bio">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                        <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-bio"><i class="bi bi-check-lg me-1"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editarTecnico(tecnico) {
            document.getElementById('edit-name').value = tecnico.name;
            document.getElementById('edit-email').value = tecnico.email;
            document.getElementById('form-editar').action = '/admin/tecnicos/' + tecnico.id;
            new bootstrap.Modal(document.getElementById('modal-editar')).show();
        }
        document.getElementById('buscadorTecnico')?.addEventListener('input', function() {
            const t = this.value.toLowerCase();
            document.querySelectorAll('[data-tecnico]').forEach(el => {
                el.style.display = el.getAttribute('data-tecnico').toLowerCase().includes(t) ? '' : 'none';
            });
        });
    </script>
</x-app-layout>
