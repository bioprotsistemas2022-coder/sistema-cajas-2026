<x-guest-layout>
    <div class="text-center" style="padding:4rem 2rem;">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width:80px;height:80px;background:linear-gradient(135deg,#ecfdf5,#d1fae5);">
            <i class="bi bi-check-circle-fill fs-2 text-success"></i>
        </div>
        <h3 class="fw-bold mb-2" style="color:#065f46;">¡Recepción Confirmada!</h3>
        <p class="text-muted mb-0">
            La caja <strong>{{ $caja->nombre }}</strong> ({{ $caja->codigo_interno }}) ha sido registrada correctamente.
            El proceso sigue su curso.
        </p>
    </div>
</x-guest-layout>
