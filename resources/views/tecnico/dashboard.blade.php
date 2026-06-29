<x-app-layout>
    <x-slot name="header">Mis Cirugías Asignadas</x-slot>

    <div class="card card-elegante">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
            <div>
                <h5 class="mb-0"><i class="bi bi-heart-pulse me-2" style="color:#3b82f6;"></i>Historial y Pendientes</h5>
                <small style="color:#94a3b8;">Listado de cirugías vinculadas a tu usuario</small>
            </div>
        </div>

        @if($cirugias->isEmpty())
            <div class="card-body text-center py-5">
                <div style="width:90px;height:90px;background:linear-gradient(135deg,#f1f5f9,#e2e8f0);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                    <i class="bi bi-calendar-x fs-1" style="color:#cbd5e1;"></i>
                </div>
                <h5 style="color:#64748b;font-weight:600;">No tienes cirugías asignadas</h5>
                <p class="small mb-0" style="color:#94a3b8;">Las cirugías aparecerán aquí cuando sean asignadas.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bio table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="bi bi-person me-1"></i>Paciente / Médico</th>
                            <th><i class="bi bi-calendar me-1"></i>Fecha</th>
                            <th><i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>Estado</th>
                            <th class="text-end"><i class="bi bi-link me-1"></i>Acceso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cirugias as $cirugia)
                            <tr>
                                <td>
                                    <div class="fw-bold" style="color:#0f172a;">{{ $cirugia->paciente }}</div>
                                    <small style="color:#64748b;">Dr. {{ $cirugia->medico }}</small>
                                </td>
                                <td>
                                    <span style="font-size:0.875rem;color:#475569;font-weight:500;">{{ $cirugia->fecha_cx->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-estado
                                        @if($cirugia->status == 'PENDIENTE') badge-warning
                                        @elseif($cirugia->status == 'EN_CURSO') badge-success
                                        @else badge-secondary @endif">
                                        {{ $cirugia->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('tecnico.surgery.view', ['cirugia' => $cirugia, 'token' => $cirugia->access_token]) }}"
                                       class="btn btn-primary btn-bio btn-sm">
                                        <i class="bi bi-card-text me-1"></i> Ver Control
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
