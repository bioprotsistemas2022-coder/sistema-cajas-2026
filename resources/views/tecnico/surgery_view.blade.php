<x-guest-layout>
    <div class="text-center mb-4">
        <div style="width:70px;height:70px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;color:#fff;font-size:1.5rem;">
            <i class="bi bi-heart-pulse"></i>
        </div>
        <span class="badge mb-3" style="background:linear-gradient(135deg,#f1f5f9,#e2e8f0);color:#475569;font-weight:700;border-radius:50rem;padding:0.5em 1em;">Surgery Protocol</span>
        <h2 class="fw-bold mb-1" style="font-size:2rem;letter-spacing:-0.03em;color:#0f172a;">{{ $cirugia->paciente }}</h2>
        <p class="small fw-semibold text-uppercase tracking-wide mb-3" style="color:#94a3b8;">Dr. {{ $cirugia->medico }}</p>
        <span class="badge badge-estado
            @if($cirugia->status == 'PENDIENTE') badge-warning
            @elseif($cirugia->status == 'EN_CURSO') badge-primary
            @else badge-dark @endif">
            {{ $cirugia->status }}
        </span>
    </div>

    <div class="surgery-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#1e293b,#0f172a);">
            <h5 class="text-white mb-0"><i class="bi bi-box-seam me-2"></i>Instrumental Asignado</h5>
            <span class="badge" style="background:rgba(255,255,255,0.2);color:#fff;font-weight:700;border-radius:50rem;">{{ $cirugia->cajas->count() }}</span>
        </div>
        <div class="card-body p-0">
            @foreach($cirugia->cajas as $caja)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold" style="color:#0f172a;">{{ $caja->nombre }}</div>
                            <small style="color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">{{ $caja->codigo_interno }}</small>
                        </div>
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,#10b981,#059669);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                    </div>

                    @if($caja->pdf_path || $caja->imagenes->isNotEmpty())
                        <div class="mt-3 pt-3" style="border-top:1px dashed #e2e8f0;">
                            <p class="small fw-bold mb-2" style="color:#64748b;">Documentación de la Caja:</p>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($caja->pdf_path)
                                    <a href="{{ Storage::url($caja->pdf_path) }}" target="_blank" class="btn btn-sm btn-bio" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;">
                                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Ver Nota Consignación
                                    </a>
                                @endif
                            </div>
                            @if($caja->imagenes->isNotEmpty())
                                <div class="d-flex gap-2 flex-wrap mt-2">
                                    @foreach($caja->imagenes->take(4) as $img)
                                        <div class="position-relative" style="width:80px;">
                                            <img src="{{ Storage::url($img->ruta) }}" class="img-fluid" style="height:70px;width:70px;object-fit:cover;border-radius:10px;border:2px solid #e2e8f0;">
                                            <button class="btn btn-sm position-absolute" style="background:rgba(0,0,0,0.6);color:#fff;border-radius:50%;width:22px;height:22px;top:-5px;right:-5px;font-size:0.6rem;" onclick="verImagen('{{ Storage::url($img->ruta) }}')">
                                                <i class="bi bi-zoom-in"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                    @if($caja->imagenes->count() > 4)
                                        <div class="d-flex align-items-center justify-content-center" style="width:70px;height:70px;background:#f1f5f9;border-radius:10px;font-size:0.75rem;color:#64748b;font-weight:600;">
                                            +{{ $caja->imagenes->count() - 4 }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @if($cirugia->observaciones)
        <div class="p-3 mb-4" style="background:#fffbeb;border:1px solid #fde68a;border-radius:16px;">
            <p class="small fw-bold mb-1" style="color:#92400e;"><i class="bi bi-info-circle me-1"></i>Observaciones</p>
            <p class="mb-0" style="color:#78350f;font-size:0.9rem;">{{ $cirugia->observaciones }}</p>
        </div>
    @endif

    @if($cirugia->status == 'PENDIENTE')
        <form action="{{ route('tecnico.surgery.llegado', $cirugia) }}" method="POST">
            @csrf
            @unless(auth()->check())
                <div class="mb-3">
                    <label class="form-label form-label-bio">Tu nombre (técnico)</label>
                    <input type="text" name="responsable_nombre" class="form-control form-control-bio" required placeholder="Nombre completo" value="{{ $cirugia->tecnico_nombre ?? '' }}">
                </div>
            @endunless
            <button type="submit" class="btn btn-lg w-100 py-3 btn-bio" style="background:linear-gradient(135deg,#1e293b,#0f172a);color:#fff;font-weight:700;font-size:0.95rem;">
                <i class="bi bi-geo-alt me-2"></i> Comenzar Cirugía
            </button>
        </form>
    @endif

    @if($cirugia->status == 'EN_CURSO')
        <div class="d-flex justify-content-between align-items-center p-3 mb-4" style="background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:16px;color:#fff;">
            <div class="d-flex align-items-center gap-2">
                <div style="width:10px;height:10px;background:#fff;border-radius:50%;animation:pulse 1.5s infinite;"></div>
                <span class="small fw-semibold" style="opacity:0.85;">Cronómetro activo</span>
            </div>
            <span class="fw-bold fs-5">{{ $cirugia->start_time->format('H:i') }} hs</span>
        </div>

        @if($cirugia->cajas->isEmpty())
            <div class="alert alert-warning py-2 px-3 mb-3 small">Esta cirugía no tiene cajas asociadas. No se puede reportar consumo.</div>
        @endif
        <form action="{{ route('tecnico.surgery.finalizar', $cirugia) }}" method="POST">
            @csrf
            <div class="surgery-card mb-3">
                <div class="card-header" style="background:linear-gradient(135deg,#1e293b,#0f172a);">
                    <h5 class="text-white mb-0"><i class="bi bi-card-text me-2"></i>Reporte de Consumos</h5>
                </div>
                <div class="card-body">
                    @foreach($cirugia->cajas as $caja)
                        <div class="mb-3">
                            <label class="form-label form-label-bio">{{ $caja->nombre }}</label>
                            <textarea name="consumos[{{ $caja->id }}]" rows="2" class="form-control form-control-bio" placeholder="Declare consumos (opcional)"></textarea>
                        </div>
                    @endforeach
                    <div class="mb-3">
                        <label class="form-label form-label-bio">Minuta Final</label>
                        <textarea name="observaciones" rows="4" class="form-control form-control-bio" placeholder="Comentarios del procedimiento..."></textarea>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-lg w-100 py-3 btn-bio" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-weight:700;font-size:0.95rem;">
                <i class="bi bi-check-all me-2"></i> Concluir Cirugía
            </button>
        </form>
    @endif

    @if($cirugia->status == 'COMPLETADA')
        <div class="text-center py-5" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);border-radius:20px;">
            <div style="width:80px;height:80px;background:linear-gradient(135deg,#10b981,#059669);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;color:#fff;font-size:2rem;box-shadow:0 10px 30px rgba(16,185,129,0.3);">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h4 class="fw-bold mb-2" style="color:#065f46;">Cirugía Finalizada</h4>
            <p class="small mb-0" style="color:#10b981;">El reporte ha sido procesado exitosamente</p>
            <a href="{{ route('dashboard') }}" class="btn btn-bio mt-4 px-5 py-2" style="background:linear-gradient(135deg,#1e293b,#0f172a);color:#fff;font-weight:600;border-radius:50rem;">
                <i class="bi bi-arrow-left me-2"></i> Volver al Dashboard
            </a>
        </div>
    @elseif($cirugia->status == 'CANCELADA' || $cirugia->status == 'POSTPUESTA')
        <div class="text-center py-5" style="background:linear-gradient(135deg,#fef2f2,#fee2e2);border-radius:20px;">
            <div style="width:80px;height:80px;background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;color:#fff;font-size:2rem;box-shadow:0 10px 30px rgba(239,68,68,0.3);">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            @if($cirugia->status == 'CANCELADA')
                <h4 class="fw-bold mb-2" style="color:#991b1b;">Cirugía Cancelada</h4>
                <p class="small mb-0" style="color:#dc2626;">Esta cirugía ha sido cancelada. Las cajas fueron retornadas a depósito.</p>
            @else
                <h4 class="fw-bold mb-2" style="color:#92400e;">Cirugía Postergada</h4>
                <p class="small mb-0" style="color:#d97706;">Esta cirugía ha sido postergada. Conservá este link por si se reactiva más tarde.</p>
            @endif
        </div>
    @endif

    <div class="modal fade" id="modal-imagen" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background:linear-gradient(135deg,#1e293b,#0f172a);">
                    <h5 class="modal-title text-white"><i class="bi bi-image me-2"></i>Imagen de Caja</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-0" style="background:#000;">
                    <img id="imagen-modal" src="" class="img-fluid" style="max-height:80vh;">
                </div>
            </div>
        </div>
    </div>

    <script>
    function verImagen(url) {
        document.getElementById('imagen-modal').src = url;
        var modal = new bootstrap.Modal(document.getElementById('modal-imagen'));
        modal.show();
    }
    </script>
</x-guest-layout>
