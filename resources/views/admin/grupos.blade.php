<x-app-layout>
    <x-slot name="header">Gestión de Grupos de Cajas</x-slot>

    <div class="card card-elegante mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-boxes me-2" style="color:#3b82f6;"></i>Grupos de Cajas</h5>
            <button class="btn btn-primary btn-bio" data-bs-toggle="modal" data-bs-target="#modal-crear-grupo">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Grupo
            </button>
        </div>
        <div class="card-body p-0">
            @if($grupos->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-boxes fs-1" style="color:#cbd5e1;"></i>
                    <p class="text-muted mt-2 mb-0 small">Sin grupos creados. Creá el primer grupo.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bio table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Cajas</th>
                                <th style="width:160px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grupos as $grupo)
                                <tr>
                                    <td class="fw-semibold" style="color:#0f172a;">{{ $grupo->nombre }}</td>
                                    <td><small style="color:#64748b;">{{ $grupo->descripcion ?: '—' }}</small></td>
                                    <td>
                                        <span class="badge" style="background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border-radius:50rem;">
                                            {{ $grupo->cajas_count }}
                                        </span>
                                        @if($grupo->cajas->isNotEmpty())
                                            <div class="mt-1">
                                                @foreach($grupo->cajas as $c)
                                                    <small class="d-block" style="color:#94a3b8;font-size:0.7rem;">
                                                        {{ $c->nombre }} <code>{{ $c->codigo_interno }}</code>
                                                        <span class="badge badge-estado badge-{{ $c->estado == 'CONSIGNADA' ? 'info' : ($c->estado == 'DISPONIBLE' ? 'success' : 'secondary') }}" style="font-size:0.6rem;">{{ $c->estadoLabel() }}</span>
                                                    </small>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-editar-{{ $grupo->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-eliminar-{{ $grupo->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal Editar --}}
                                <div class="modal fade" id="modal-editar-{{ $grupo->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                                                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil me-2" style="color:#3b82f6;"></i>Editar Grupo</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.grupos.update', $grupo) }}" method="POST">
                                                @csrf @method('put')
                                                <div class="modal-body px-4 py-4">
                                                    <div class="mb-3">
                                                        <label class="form-label form-label-bio">Nombre del Grupo</label>
                                                        <input type="text" name="nombre" class="form-control form-control-bio" required value="{{ $grupo->nombre }}" placeholder="Ej: Kit Cadera Cementada">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label form-label-bio">Descripción</label>
                                                        <textarea name="descripcion" rows="2" class="form-control form-control-bio" placeholder="Opcional">{{ $grupo->descripcion }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label form-label-bio">Cajas del Grupo</label>
                                                        <div class="table-responsive" style="max-height:300px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:12px;">
                                                            <table class="table table-sm table-bio mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:40px;"></th>
                                                                        <th>Nombre</th>
                                                                        <th>Código</th>
                                                                        <th>Estado</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($cajasDisponibles as $caja)
                                                                        <tr>
                                                                            <td>
                                                                                <input type="checkbox" name="cajas[]" value="{{ $caja->id }}" class="form-check-input"
                                                                                    {{ $grupo->cajas->contains($caja->id) ? 'checked' : '' }}>
                                                                            </td>
                                                                            <td><small>{{ $caja->nombre }}</small></td>
                                                                            <td><code>{{ $caja->codigo_interno }}</code></td>
                                                                            <td><span class="badge badge-estado badge-{{ $caja->estado == 'CONSIGNADA' ? 'info' : ($caja->estado == 'DISPONIBLE' ? 'success' : 'secondary') }}">{{ $caja->estadoLabel() }}</span></td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <small class="text-muted mt-1 d-block">Solo se muestran cajas en estado DISPONIBLE.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                                                    <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary btn-bio px-4"><i class="bi bi-check-lg me-1"></i> Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Eliminar --}}
                                <div class="modal fade" id="modal-eliminar-{{ $grupo->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4" style="background:linear-gradient(135deg,#fef2f2,#fee2e2);">
                                                <h5 class="mb-0 fw-bold" style="color:#991b1b;"><i class="bi bi-exclamation-triangle me-2" style="color:#dc2626;"></i>Eliminar Grupo</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.grupos.destroy', $grupo) }}" method="POST">
                                                @csrf @method('delete')
                                                <div class="modal-body px-4 py-4">
                                                    <p class="mb-0">¿Eliminar el grupo <strong>{{ $grupo->nombre }}</strong>?</p>
                                                    <p class="small text-muted mt-2 mb-0">Las cajas no se eliminan, solo se desvinculan del grupo.</p>
                                                </div>
                                                <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                                                    <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger btn-bio px-4"><i class="bi bi-trash me-1"></i> Eliminar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Crear --}}
    <div class="modal fade" id="modal-crear-grupo" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2" style="color:#3b82f6;"></i>Nuevo Grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.grupos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body px-4 py-4">
                        <div class="mb-3">
                            <label class="form-label form-label-bio">Nombre del Grupo</label>
                            <input type="text" name="nombre" class="form-control form-control-bio" required placeholder="Ej: Kit Cadera Cementada">
                        </div>
                        <div class="mb-3">
                            <label class="form-label form-label-bio">Descripción</label>
                            <textarea name="descripcion" rows="2" class="form-control form-control-bio" placeholder="Opcional"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label form-label-bio">Cajas del Grupo</label>
                            <div class="mb-2">
                                <input type="text" id="buscador-cajas" class="form-control form-control-bio" placeholder="Buscar caja...">
                            </div>
                            <div class="table-responsive" style="max-height:300px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:12px;">
                                <table class="table table-sm table-bio mb-0" id="tabla-cajas">
                                    <thead>
                                        <tr>
                                            <th style="width:40px;"></th>
                                            <th>Nombre</th>
                                            <th>Código</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cajasDisponibles as $caja)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="cajas[]" value="{{ $caja->id }}" class="form-check-input">
                                                </td>
                                                <td><small>{{ $caja->nombre }}</small></td>
                                                <td><code>{{ $caja->codigo_interno }}</code></td>
                                                <td><span class="badge badge-estado badge-{{ $caja->estado == 'CONSIGNADA' ? 'info' : ($caja->estado == 'DISPONIBLE' ? 'success' : 'secondary') }}">{{ $caja->estadoLabel() }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted mt-1 d-block">Solo se muestran cajas en estado DISPONIBLE.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                        <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-bio px-4"><i class="bi bi-check-lg me-1"></i> Crear Grupo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('buscador-cajas')?.addEventListener('input', function() {
            const t = this.value.toLowerCase();
            document.querySelectorAll('#tabla-cajas tbody tr').forEach(function(f) {
                const txt = f.textContent.toLowerCase();
                f.style.display = txt.includes(t) ? '' : 'none';
            });
        });
    </script>
</x-app-layout>
