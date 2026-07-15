<x-app-layout>
    <x-slot name="header">Detalle de Caja</x-slot>

    <div class="d-flex align-items-center mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-bio btn-sm me-3">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
        <div>
            <h2 class="mb-0 fw-bold" style="color:#0f172a;">{{ $caja->nombre }}</h2>
            <small style="color:#94a3b8;">Código: {{ $caja->codigo_interno }}</small>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-3 col-lg-4">
            <div class="card card-elegante h-100">
                <div class="card-header">
                    <h5><i class="bi bi-info-circle me-2" style="color:#3b82f6;"></i>Información</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label form-label-bio">Estado</label>
                        @php
                            $estilos = [
                                'DISPONIBLE' => 'badge-success','EN ESTERILIZADORA' => 'badge-primary',
                                'EN CX' => 'badge-dark','CX FINALIZADA' => 'badge-dark','EN TRANSITO VUELTA' => 'badge-warning',
                                'PENDIENTE' => 'badge-warning','ACONDICIONAMIENTO' => 'badge-info',
                                'EN REPARACION' => 'badge-danger','BAJA' => 'badge-secondary',
                            ];
                        @endphp
                        <span class="badge badge-estado {{ $estilos[$caja->estado] ?? 'badge-secondary' }}">{{ $caja->estado }}</span>
                    </div>
                    <div class="mb-4">
                        <label class="form-label form-label-bio">ID</label>
                        <p class="fw-semibold mb-0">#{{ $caja->id }}</p>
                    </div>
                    <div class="mb-4">
                        <label class="form-label form-label-bio">Código</label>
                        <code style="background:#f8fafc;padding:0.4em 0.8em;border-radius:8px;font-size:0.85rem;">{{ $caja->codigo_interno }}</code>
                    </div>
                    <div class="mb-4">
                        <label class="form-label form-label-bio">Cirugías</label>
                        <p class="mb-0">{{ $caja->cirugias->count() }}</p>
                    </div>
                    @if($caja->relationLoaded('grupos') && $caja->grupos->isNotEmpty())
                    <div class="mb-4">
                        <label class="form-label form-label-bio">Grupos</label>
                        <div>
                            @foreach($caja->grupos as $grp)
                                <span class="badge" style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;border-radius:50rem;font-size:0.75rem;margin-bottom:2px;">
                                    <i class="bi bi-boxes me-1"></i>{{ $grp->nombre }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    <div class="mb-4">
                        <label class="form-label form-label-bio">Imágenes</label>
                        <p class="mb-0">{{ $caja->imagenes->count() }}</p>
                    </div>
                    <div class="d-grid gap-2">
                        @if($caja->pdf_path)
                            <a href="{{ Storage::url($caja->pdf_path) }}" target="_blank" class="btn btn-bio" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;">
                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Ver Nota de Consignación
                            </a>
                            <form method="POST" action="{{ route('cajas.deletePdf', $caja) }}" onsubmit="return confirm('¿Eliminar el PDF?')">
                                @csrf @method('delete')
                                <button type="submit" class="btn btn-outline-danger btn-bio btn-sm w-100">
                                    <i class="bi bi-trash me-1"></i> Eliminar PDF
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('cajas.pdf', $caja) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label form-label-bio small">Subir Nota de Consignación (PDF)</label>
                                    <input type="file" name="pdf" class="form-control form-control-bio" accept=".pdf" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-bio btn-sm w-100">
                                    <i class="bi bi-upload me-1"></i> Cargar PDF
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-8">
            <div class="card card-elegante mb-4">
                <div class="card-header">
                    <h5><i class="bi bi-images me-2" style="color:#8b5cf6;"></i>Imágenes de la Caja</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cajas.imagen', $caja) }}" enctype="multipart/form-data" class="mb-4 p-3" style="background:#f8fafc;border-radius:16px;">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label form-label-bio">Nueva Imagen</label>
                                <input type="file" name="imagen" class="form-control form-control-bio" accept="image/*" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-bio">Descripción (opcional)</label>
                                <input type="text" name="descripcion" class="form-control form-control-bio" placeholder="Ej: Antes de CX #15">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-bio w-100">
                                    <i class="bi bi-upload me-1"></i> Subir
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($caja->imagenes->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-image fs-1" style="color:#cbd5e1;"></i>
                            <p class="small mb-0 mt-2" style="color:#94a3b8;">Sin imágenes. Subí la primera.</p>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($caja->imagenes as $img)
                                <div class="col-md-3 col-lg-2 col-4">
                                    <div class="position-relative" style="border-radius:16px;overflow:hidden;border:2px solid #e2e8f0;">
                                        <img src="{{ Storage::url($img->ruta) }}" class="img-fluid w-100" style="height:150px;object-fit:cover;">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <button class="btn btn-sm" style="background:rgba(239,68,68,0.9);color:#fff;border-radius:8px;padding:0.25rem 0.5rem;" onclick="if(confirm('¿Eliminar esta imagen?')){document.getElementById('delete-img-{{ $img->id }}').submit();}">
                                                <i class="bi bi-trash small"></i>
                                            </button>
                                        </div>
                                        <form id="delete-img-{{ $img->id }}" method="POST" action="{{ route('cajas.deleteImagen', $img) }}">@csrf @method('delete')</form>
                                        @if($img->descripcion)
                                            <div class="p-2" style="background:#f8fafc;font-size:0.75rem;color:#64748b;">{{ $img->descripcion }}</div>
                                        @endif
                                        <div class="p-2" style="background:#f8fafc;font-size:0.7rem;color:#94a3b8;">{{ $img->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card card-elegante mb-4">
                <div class="card-header">
                    <h5><i class="bi bi-heart-pulse me-2" style="color:#ef4444;"></i>Cirugías Realizadas</h5>
                </div>
                <div class="card-body p-0">
                    @php $hasCirugias = $caja->cirugias->isNotEmpty(); @endphp
                    @if(!$hasCirugias)
                        <div class="text-center py-4"><p class="text-muted mb-0 small">Sin cirugías registradas</p></div>
                    @else
                        @foreach($caja->cirugias->sortByDesc('created_at') as $cirugia)
                            @php $consumo = $cirugia->consumos->where('caja_id', $caja->id)->first(); @endphp
                            <div class="p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-semibold" style="color:#0f172a;">{{ $cirugia->paciente }}</div>
                                        <small style="color:#94a3b8;">Dr. {{ $cirugia->medico }} · {{ $cirugia->fecha_cx?->format('d/m/Y') }}</small>
                                    </div>
                                    <span class="badge badge-estado
                                        @if($cirugia->status == 'COMPLETADA') badge-success
                                        @elseif($cirugia->status == 'EN_CURSO') badge-primary
                                        @elseif($cirugia->status == 'CANCELADA') badge-secondary
                                        @elseif($cirugia->status == 'POSTPUESTA') badge-warning
                                        @else badge-dark @endif">
                                        {{ $cirugia->status }}
                                    </span>
                                </div>
                                @if($consumo && $cirugia->status == 'COMPLETADA')
                                    @if($consumo->items)
                                        <div class="mb-1">
                                            <small class="fw-bold" style="color:#475569;">Consumos reportados:</small>
                                            <p class="mb-0 small" style="color:#64748b;">{{ $consumo->items }}</p>
                                        </div>
                                    @endif
                                    @if($consumo->observaciones)
                                        <div>
                                            <small class="fw-bold" style="color:#475569;">Minuta Final:</small>
                                            <p class="mb-0 small" style="color:#64748b;">{{ $consumo->observaciones }}</p>
                                        </div>
                                    @endif
                                @elseif($cirugia->observaciones && $cirugia->status != 'COMPLETADA')
                                    <div>
                                        <small class="fw-bold" style="color:#475569;">Observaciones:</small>
                                        <p class="mb-0 small" style="color:#64748b;">{{ $cirugia->observaciones }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card card-elegante">
                <div class="card-header">
                    <h5><i class="bi bi-clock-history me-2" style="color:#8b5cf6;"></i>Historial</h5>
                </div>
                <div class="card-body p-0">
                    @if($caja->eventos->isEmpty())
                        <div class="text-center py-4"><p class="text-muted mb-0 small">Sin historial</p></div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bio table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Estado Anterior</th>
                                        <th>Estado Nuevo</th>
                                        <th>Usuario</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($caja->eventos->sortByDesc('created_at') as $evento)
                                        @php
                                            $obs = $evento->observaciones ?? '';
                                            $obsHtml = e($obs);
                                            $obsHtml = preg_replace(
                                                '/\[PlcCod:(\d+)\]/',
                                                '<a href="http://bioprot.ddns.net/procedure/api/planillacx.php?id=$1" target="_blank" class="btn btn-sm px-2 py-0 ms-1" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border-radius:50rem;font-size:0.7rem;"><i class="bi bi-box-arrow-up-right me-1"></i>PlcCod #$1</a>',
                                                $obsHtml
                                            );
                                        @endphp
                                        <tr>
                                            <td><small>{{ $evento->created_at->format('d/m/Y H:i') }}</small></td>
                                            <td><span class="badge badge-estado badge-secondary">{{ $evento->estado_anterior ?? '—' }}</span></td>
                                            <td><span class="badge badge-estado {{ $estilos[$evento->estado_nuevo] ?? 'badge-secondary' }}">{{ $evento->estado_nuevo }}</span></td>
                                            <td><small>{{ $evento->user->name ?? 'Sistema' }}</small></td>
                                            <td><small>{!! $obsHtml ?: '—' !!}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
