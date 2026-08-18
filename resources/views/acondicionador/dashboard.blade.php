<x-app-layout>
    <x-slot name="header">Lavado y Acondicionamiento</x-slot>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card h-100 text-center py-4" style="background:linear-gradient(135deg,#ecfeff,#cffafe);">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="bi bi-droplet fs-3" style="color:#0891b2;"></i>
                </div>
                <div class="stat-value mb-1 text-info">{{ $cajas->count() }}</div>
                <div class="stat-label text-info">Cajas en Lavado</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card h-100 text-center py-4" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="bi bi-check-circle fs-3 text-success"></i>
                </div>
                <div class="stat-value mb-1 text-success">0</div>
                <div class="stat-label text-success">Completadas Hoy</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card h-100 text-center py-4" style="background:linear-gradient(135deg,#f8fafc,#e2e8f0);">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="bi bi-clock fs-3" style="color:#475569;"></i>
                </div>
                <div class="stat-value mb-1" style="color:#475569;">{{ $cajas->count() > 0 ? now()->format('H:i') : '--:--' }}</div>
                <div class="stat-label" style="color:#475569;">Hora Actual</div>
            </div>
        </div>
    </div>

    <div class="card card-elegante mb-4">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#ecfeff,#cffafe);">
            <h5 class="mb-0" style="color:#155e75;"><i class="bi bi-droplet me-2" style="color:#0891b2;"></i>Pendientes de Lavado</h5>
            <span class="badge" style="background:rgba(255,255,255,0.8);color:#0891b2;font-weight:700;padding:0.5em 1em;border-radius:50rem;">
                {{ $cajas->count() }}
            </span>
        </div>
        <div class="card-body p-0">
            @forelse($cajas as $caja)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom" style="transition:all 0.15s ease;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#a5f3fc,#67e8f9);">
                            <i class="bi bi-box" style="color:#155e75;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="color:#0f172a;">{{ $caja->nombre }}</div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <small class="text-uppercase" style="color:#94a3b8;letter-spacing:0.05em;">{{ $caja->codigo_interno }}</small>
                                <span class="badge badge-estado badge-info px-2">{{ $caja->estadoLabel() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        @if($caja->eventos->isNotEmpty())
                            <div class="text-end d-none d-md-block">
                                <small class="d-block fw-bold" style="color:#64748b;font-size:0.7rem;">Último evento</small>
                                <small style="color:#94a3b8;font-size:0.7rem;">{{ $caja->eventos->last()->created_at->format('d/m H:i') }}</small>
                            </div>
                        @endif
                        <div style="width:10px;height:10px;background:linear-gradient(135deg,#06b6d4,#0891b2);border-radius:50%;animation:pulse 2s infinite;"></div>
                        <form action="{{ route('acondicionador.disponibilizar', $caja) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-bio btn-sm" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;">
                                <i class="bi bi-check-all me-1"></i> Disponibilizar
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light mb-3" style="width:70px;height:70px;">
                        <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                    </div>
                    <h5 class="fw-bold" style="color:#10b981;">Área Libre</h5>
                    <p class="text-muted small mb-0">No hay cajas pendientes de lavado.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="card card-elegante">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Guía de Proceso</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                        <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0 fw-bold text-white" style="width:44px;height:44px;background:linear-gradient(135deg,#f59e0b,#d97706);">1</div>
                        <div>
                            <h6 class="fw-bold mb-1" style="color:#92400e;">Recepción</h6>
                            <p class="small mb-0" style="color:#78716c;">Recibir la caja de auditoría y verificar el instrumental.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background:linear-gradient(135deg,#ecfeff,#cffafe);">
                        <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0 fw-bold text-white" style="width:44px;height:44px;background:linear-gradient(135deg,#06b6d4,#0891b2);">2</div>
                        <div>
                            <h6 class="fw-bold mb-1" style="color:#155e75;">Lavado</h6>
                            <p class="small mb-0" style="color:#57534e;">Limpieza, desinfección y secado completo.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);">
                        <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0 fw-bold text-white" style="width:44px;height:44px;background:linear-gradient(135deg,#10b981,#059669);">3</div>
                        <div>
                            <h6 class="fw-bold mb-1" style="color:#065f46;">Disponibilizar</h6>
                            <p class="small mb-0" style="color:#78716c;">Marcar como limpia para nueva cirugía.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
