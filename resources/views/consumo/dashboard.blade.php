<x-app-layout>
    <x-slot name="header">Control de Recepción</x-slot>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card h-100 text-center py-4" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="bi bi-truck fs-3" style="color:#d97706;"></i>
                </div>
                <div class="stat-value mb-1" style="color:#92400e;">{{ $cajasEnTransito->count() }}</div>
                <div class="stat-label" style="color:#92400e;">En Tránsito</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card h-100 text-center py-4" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="bi bi-clipboard-check fs-3" style="color:#059669;"></i>
                </div>
                <div class="stat-value mb-1 text-success">{{ $cajasPendientes->count() }}</div>
                <div class="stat-label text-success">En Auditoría</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card h-100 text-center py-4" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="bi bi-arrow-left-right fs-3 text-primary"></i>
                </div>
                <div class="stat-value mb-1 text-primary">{{ $cajasEnTransito->count() + $cajasPendientes->count() }}</div>
                <div class="stat-label text-primary">Total en Proceso</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card card-elegante h-100">
                <div class="card-header d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                    <h5 class="mb-0" style="color:#92400e;"><i class="bi bi-truck me-2" style="color:#d97706;"></i>En Tránsito</h5>
                    <span class="badge" style="background:rgba(255,255,255,0.8);color:#92400e;font-weight:700;padding:0.5em 1em;border-radius:50rem;">
                        {{ $cajasEnTransito->count() }}
                    </span>
                </div>
                <div class="card-body p-0">
                    @forelse($cajasEnTransito as $caja)
                        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom" style="transition:all 0.15s ease;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#fef3c7,#fde68a);">
                                    <i class="bi bi-box-seam" style="color:#92400e;"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="color:#0f172a;">{{ $caja->nombre }}</div>
                                    <small class="text-uppercase" style="color:#94a3b8;letter-spacing:0.05em;">{{ $caja->codigo_interno }}</small>
                                </div>
                            </div>
                            <form action="{{ route('consumo.controlar', $caja) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-bio btn-sm" style="background:linear-gradient(135deg,#d97706,#b45309);color:#fff;">
                                    <i class="bi bi-arrow-down-circle me-1"></i> Recibir
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light mb-3" style="width:70px;height:70px;">
                                <i class="bi bi-inbox fs-3" style="color:#cbd5e1;"></i>
                            </div>
                            <p class="text-muted small mb-0">No hay cajas en tránsito.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-elegante h-100">
                <div class="card-header d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);">
                    <h5 class="mb-0" style="color:#065f46;"><i class="bi bi-clipboard-check me-2" style="color:#059669;"></i>Auditoría</h5>
                    <span class="badge" style="background:rgba(255,255,255,0.8);color:#065f46;font-weight:700;padding:0.5em 1em;border-radius:50rem;">
                        {{ $cajasPendientes->count() }}
                    </span>
                </div>
                <div class="card-body p-0">
                    @forelse($cajasPendientes as $caja)
                        <div class="p-4 border-bottom">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="d-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);">
                                    <i class="bi bi-search" style="color:#065f46;"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="color:#0f172a;">{{ $caja->nombre }}</div>
                                    <small class="text-uppercase" style="color:#94a3b8;letter-spacing:0.05em;">{{ $caja->codigo_interno }}</small>
                                </div>
                                <span class="ms-auto badge" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);color:#92400e;font-weight:700;border-radius:50rem;">
                                    <i class="bi bi-search me-1"></i> Auditando
                                </span>
                            </div>
                            <form action="{{ route('consumo.finalizar', $caja) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label form-label-bio">Resultado de auditoría</label>
                                    <select name="resultado" class="form-select form-control-bio" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="ok">✓ Integridad Confirmada — Enviar a Lavado</option>
                                        <option value="falla">✗ Falla Detectada — Enviar a Reparación</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label form-label-bio">Observaciones</label>
                                    <textarea name="observaciones" rows="2" class="form-control form-control-bio" placeholder="Detalle hallazgos relevantes..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-bio w-100">
                                    <i class="bi bi-check-all me-1"></i> Concluir Auditoría
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light mb-3" style="width:70px;height:70px;">
                                <i class="bi bi-clipboard fs-3" style="color:#cbd5e1;"></i>
                            </div>
                            <p class="text-muted small mb-0">No hay auditorías activas.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
