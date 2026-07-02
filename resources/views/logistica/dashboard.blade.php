<x-app-layout>
    <x-slot name="header">Logística — Retiro del Servicio</x-slot>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stat-card h-100 text-center py-4" style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="bi bi-box-seam fs-3" style="color:#7c3aed;"></i>
                </div>
                <div class="stat-value mb-1" style="color:#5b21b6;">{{ $cajasPendientesRetiro->count() }}</div>
                <div class="stat-label" style="color:#5b21b6;">Pendientes de Retiro</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card h-100 text-center py-4" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="bi bi-truck fs-3" style="color:#d97706;"></i>
                </div>
                <div class="stat-value mb-1" style="color:#92400e;">{{ $cajasEnTransito->count() }}</div>
                <div class="stat-label" style="color:#92400e;">En Tránsito Vuelta</div>
            </div>
        </div>
    </div>

    <div class="card card-elegante">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);">
            <h5 class="mb-0" style="color:#5b21b6;"><i class="bi bi-box-seam me-2" style="color:#7c3aed;"></i>Cajas Pendientes de Retiro</h5>
            <span class="badge" style="background:rgba(255,255,255,0.8);color:#5b21b6;font-weight:700;padding:0.5em 1em;border-radius:50rem;">
                {{ $cajasPendientesRetiro->count() }}
            </span>
        </div>
        <div class="card-body p-0">
            @forelse($cajasPendientesRetiro as $caja)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);">
                            <i class="bi bi-box-seam" style="color:#5b21b6;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="color:#0f172a;">{{ $caja->nombre }}</div>
                            <small style="color:#94a3b8;">{{ $caja->codigo_interno }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-retirar-{{ $caja->id }}">
                        <i class="bi bi-check-lg me-1"></i> Retirar del Servicio
                    </button>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-check-circle fs-1" style="color:#cbd5e1;"></i>
                    <p class="text-muted mb-0 mt-2 small">No hay cajas pendientes de retiro.</p>
                </div>
            @endforelse
        </div>
    </div>

    @foreach($cajasPendientesRetiro as $caja)
        <div class="modal fade" id="modal-retirar-{{ $caja->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);">
                        <h5 class="mb-0 fw-bold" style="color:#065f46;"><i class="bi bi-check-circle me-2" style="color:#10b981;"></i>Retirar Caja del Servicio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('logistica.retirar', $caja) }}" method="POST">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <p class="mb-0">Confirmar retiro de <strong>{{ $caja->nombre }}</strong> ({{ $caja->codigo_interno }}) del servicio. La caja pasará a estado <strong>EN TRANSITO VUELTA</strong>.</p>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                            <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-bio"><i class="bi bi-check-lg me-1"></i> Confirmar Retiro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @if($cajasEnTransito->isNotEmpty())
        <div class="card card-elegante mt-4">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                <h5 class="mb-0" style="color:#92400e;"><i class="bi bi-truck me-2" style="color:#d97706;"></i>En Tránsito Vuelta</h5>
                <span class="badge" style="background:rgba(255,255,255,0.8);color:#92400e;font-weight:700;padding:0.5em 1em;border-radius:50rem;">
                    {{ $cajasEnTransito->count() }}
                </span>
            </div>
            <div class="card-body p-0">
                @foreach($cajasEnTransito as $caja)
                    <div class="d-flex align-items-center px-4 py-3 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#fef3c7,#fde68a);">
                                <i class="bi bi-truck" style="color:#92400e;"></i>
                            </div>
                            <div>
                                <div class="fw-semibold" style="color:#0f172a;">{{ $caja->nombre }}</div>
                                <small style="color:#94a3b8;">{{ $caja->codigo_interno }} — Retirada</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
        });
    </script>
</x-app-layout>
