<x-app-layout>
    <x-slot name="header">Inventario de Cajas</x-slot>

    <div class="card card-elegante mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="bi bi-box-seam me-2"></i>Listado Completo</h5>
            <span class="badge" style="background:linear-gradient(135deg,#f1f5f9,#e2e8f0);color:#64748b;font-weight:700;padding:0.5em 1em;border-radius:50rem;">
                {{ $cajas->count() }} Cajas
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bio table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Identificación</th>
                            <th>Estado</th>
                            <th>Código</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cajas as $caja)
                            <tr>
                                <td>
                                    <div class="fw-bold" style="color:#0f172a;">{{ $caja->nombre }}</div>
                                    <small style="color:#94a3b8;">Box ID: {{ $caja->id }}</small>
                                </td>
                                <td>
                                    @php
                                        $estilos = [
                                            'DISPONIBLE' => 'badge-success',
                                            'EN ESTERILIZADORA' => 'badge-primary',
                                            'EN CX' => 'badge-dark',
                                            'EN TRANSITO' => 'badge-warning',
                                            'PENDIENTE' => 'badge-warning',
                                            'ACONDICIONAMIENTO' => 'badge-info',
                                            'EN REPARACION' => 'badge-danger',
                                            'BAJA' => 'badge-secondary',
                                        ];
                                    @endphp
                                    <span class="badge badge-estado {{ $estilos[$caja->estado] ?? 'badge-secondary' }}">
                                        {{ $caja->estado }}
                                    </span>
                                </td>
                                <td>
                                    <code style="background:#f8fafc;padding:0.3em 0.7em;border-radius:8px;font-size:0.8rem;color:#475569;">{{ $caja->codigo_interno }}</code>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('cajas.show', $caja) }}" class="btn btn-primary btn-bio btn-sm">
                                        <i class="bi bi-eye me-1"></i> Ver Detalle
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
