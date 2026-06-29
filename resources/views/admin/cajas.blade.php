<x-app-layout>
    <x-slot name="header">Gestión de Cajas</x-slot>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card h-100" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);">
                <div class="stat-icon" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-value text-success">{{ $cajas->count() }}</div>
                <div class="stat-label text-success">Total Cajas</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card h-100" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
                <div class="stat-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;">
                    <i class="bi bi-file-earmark-pdf"></i>
                </div>
                <div class="stat-value text-primary">{{ $cajas->whereNotNull('pdf_path')->count() }}</div>
                <div class="stat-label text-primary">Con PDF</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card h-100" style="background:linear-gradient(135deg,#ecfeff,#cffafe);">
                <div class="stat-icon" style="background:linear-gradient(135deg,#06b6d4,#0891b2);color:#fff;">
                    <i class="bi bi-image"></i>
                </div>
                <div class="stat-value text-info">{{ $cajas->sum(fn($c) => $c->imagenes->count()) }}</div>
                <div class="stat-label text-info">Imágenes Totales</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card h-100" style="background:linear-gradient(135deg,#f8fafc,#e2e8f0);">
                <div class="stat-icon" style="background:linear-gradient(135deg,#64748b,#475569);color:#fff;">
                    <i class="bi bi-archive"></i>
                </div>
                <div class="stat-value" style="color:#64748b;">{{ $cajas->where('estado','BAJA')->count() }}</div>
                <div class="stat-label" style="color:#64748b;">De Baja</div>
            </div>
        </div>
    </div>

    <div class="card card-elegante mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="bi bi-box-seam me-2"></i>Listado de Cajas</h5>
            <button class="btn btn-primary btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-crear">
                <i class="bi bi-plus-lg me-1"></i> Nueva Caja
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bio table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Estado</th>
                            <th>PDF</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cajas as $caja)
                            <tr>
                                <td><span class="fw-semibold">{{ $caja->nombre }}</span></td>
                                <td><code>{{ $caja->codigo_interno }}</code></td>
                                <td>
                                    @php
                                        $estilos = [
                                            'DISPONIBLE' => 'badge-success','EN ESTERILIZADORA' => 'badge-primary',
                                            'EN CX' => 'badge-dark','EN TRANSITO' => 'badge-warning',
                                            'PENDIENTE' => 'badge-warning','ACONDICIONAMIENTO' => 'badge-info',
                                            'EN REPARACION' => 'badge-danger','BAJA' => 'badge-secondary',
                                        ];
                                    @endphp
                                    <span class="badge badge-estado {{ $estilos[$caja->estado] ?? 'badge-secondary' }}">{{ $caja->estado }}</span>
                                </td>
                                <td>
                                    @if($caja->pdf_path)
                                        <a href="{{ Storage::url($caja->pdf_path) }}" target="_blank" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border-radius:8px;">
                                            <i class="bi bi-file-earmark-pdf-fill"></i> Ver PDF
                                        </a>
                                    @else
                                        <span class="badge badge-secondary">Sin PDF</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('cajas.show', $caja) }}" class="btn btn-outline-primary btn-bio btn-sm">
                                        <i class="bi bi-pencil me-1"></i> Gestionar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4"><p class="text-muted mb-0">No hay cajas registradas.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-crear" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5><i class="bi bi-plus-circle me-2"></i>Nueva Caja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.cajas.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label form-label-bio">Nombre</label>
                            <input type="text" name="nombre" class="form-control form-control-bio" required placeholder="Ej: Caja Traumatología">
                        </div>
                        <div class="mb-3">
                            <label class="form-label form-label-bio">Código Interno</label>
                            <input type="text" name="codigo_interno" class="form-control form-control-bio" required placeholder="Ej: CX-001">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-bio"><i class="bi bi-check-lg me-1"></i> Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
