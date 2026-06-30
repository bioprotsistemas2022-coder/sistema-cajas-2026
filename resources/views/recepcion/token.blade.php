<x-guest-layout>
    <div class="text-center" style="padding:4rem 2rem;">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width:80px;height:80px;background:linear-gradient(135deg,#eff6ff,#dbeafe);">
            <i class="bi bi-box-seam fs-2 text-primary"></i>
        </div>
        <h3 class="fw-bold mb-2" style="color:#0f172a;">Acción de Recepción</h3>
        <p class="text-muted mb-4" style="max-width:400px;margin:0 auto;">
            Se ha solicitado tu confirmación para la caja <strong>{{ $t->caja->nombre }}</strong> ({{ $t->caja->codigo_interno }}).
        </p>

        @if($t->accion === 'consumo.controlar')
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i> Acción: Recibir caja e iniciar control de auditoría.
            </div>
        @elseif($t->accion === 'consumo.finalizar')
            <div class="alert" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                <i class="bi bi-clipboard-check me-2" style="color:#92400e;"></i>
                Acción: Finalizar control — resultado: {{ $t->params['resultado'] ?? 'ok' }}
            </div>
        @endif

        <form action="{{ route('recepcion.confirmar', $t->token) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-lg px-5 py-3" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:16px;font-weight:700;">
                <i class="bi bi-check-circle me-2"></i> Sí, confirmar recepción
            </button>
        </form>
        <p class="text-muted mt-3 small">Este enlace expira en 24 horas y es de un solo uso.</p>
    </div>
</x-guest-layout>
