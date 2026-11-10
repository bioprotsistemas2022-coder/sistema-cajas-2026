<x-app-layout>
    <x-slot name="header">Consignaciones</x-slot>

    <div class="card card-elegante mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="mb-0"><i class="bi bi-box-seam me-2" style="color:#3b82f6;"></i>Cajas Sin Consignar</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="badge" style="background:linear-gradient(135deg,#f1f5f9,#e2e8f0);color:#64748b;font-weight:700;padding:0.5em 1em;border-radius:50rem;">
                    {{ $disponibles->total() }} Cajas @if(request('q'))<small class="fw-normal">(filtradas)</small>@endif
                </span>
                <form method="GET" action="{{ route('consignaciones.index') }}" class="input-group" style="width:280px;">
                    <span class="input-group-text border-0 bg-light">
                        <i class="bi bi-search" style="color:#94a3b8;"></i>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control border-0 bg-light" placeholder="Buscar nombre o código...">
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            @if($disponibles->isEmpty())
                <div class="p-4 text-center text-muted small">No hay cajas Sin Consignar que coincidan.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bio table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Identificación</th>
                                <th>Código</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($disponibles as $caja)
                                <tr>
                                    <td class="fw-bold" style="color:#0f172a;">{{ $caja->nombre }}</td>
                                    <td><code class="bg-light px-2 py-1 rounded-3" style="color:#475569;">{{ $caja->codigo_interno }}</code></td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-primary btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-consignar-{{ $caja->id }}">
                                            <i class="bi bi-box-arrow-up me-1"></i> Consignar
                                        </button>
                                        <a href="{{ route('cajas.show', $caja) }}" class="btn btn-outline-secondary btn-bio btn-sm">
                                            <i class="bi bi-eye me-1"></i> Detalles
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($disponibles->hasPages())
                    <div class="card-footer bg-white d-flex justify-content-center py-3 border-top">
                        {{ $disponibles->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <div class="card card-elegante mb-4">
        <div class="card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg,#eef2ff,#e0e7ff);">
            <h5 class="mb-0" style="color:#4338ca;"><i class="bi bi-link-45deg me-2" style="color:#4338ca;"></i>Cajas Consignadas</h5>
            <span class="badge" style="background:#fff;color:#4338ca;font-weight:700;padding:0.5em 1em;border-radius:50rem;">
                {{ $consignadas->total() }} Consignada{{ $consignadas->total() === 1 ? '' : 's' }}
            </span>
        </div>
        <div class="card-body p-0">
            <p class="small text-muted px-4 pt-3 mb-0">Para vincular una caja consignada a una cirugía (CX), usá la grilla <strong>Depósito → "Cajas Consignadas — Vincular a CX"</strong>.</p>
            @if($consignadas->isEmpty())
                <div class="p-4 text-center text-muted small">No hay cajas Consignadas todavía.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bio table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Caja</th>
                                <th>Código</th>
                                <th>Consignatario</th>
                                <th>Fecha consignación</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($consignadas as $caja)
                                <tr>
                                    <td class="fw-bold" style="color:#0f172a;">{{ $caja->nombre }}</td>
                                    <td><code class="bg-light px-2 py-1 rounded-3" style="color:#475569;">{{ $caja->codigo_interno }}</code></td>
                                    <td>{{ $caja->consignatario_nombre ?: '—' }}</td>
                                    <td>{{ $caja->fecha_consignacion ? \Illuminate\Support\Carbon::parse($caja->fecha_consignacion)->format('d/m/Y') : '—' }}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-primary btn-bio btn-sm" onclick="verInstrumental({{ $caja->id }}, '{{ addslashes($caja->nombre) }}')">
                                            <i class="bi bi-list-check me-1"></i> Ver instrumental
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-devolver-{{ $caja->id }}">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Devolver a Disponible
                                        </button>
                                        <a href="{{ route('cajas.show', $caja) }}" class="btn btn-outline-secondary btn-bio btn-sm">
                                            <i class="bi bi-eye me-1"></i> Detalles
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($consignadas->hasPages())
                    <div class="card-footer bg-white d-flex justify-content-center py-3 border-top">
                        {{ $consignadas->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Modales Consignar --}}
    @foreach($disponibles as $caja)
        <div class="modal fade" id="modal-consignar-{{ $caja->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-box-arrow-up me-2" style="color:#3b82f6;"></i>Consignar — {{ $caja->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('cajas.consignar.extraer', $caja) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <div class="mb-3">
                                <label class="form-label form-label-bio">Consignatario</label>
                                <input type="text" name="consignatario_nombre" required class="form-control form-control-bio" placeholder="Institución / cliente">
                            </div>
                            <div class="mb-3">
                                <label class="form-label form-label-bio">Fecha de consignación</label>
                                <input type="date" name="fecha_consignacion" class="form-control form-control-bio" value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label form-label-bio">Observaciones</label>
                                <textarea name="observaciones" rows="2" class="form-control form-control-bio" placeholder="Notas (opcional)"></textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label form-label-bio">Nota de Consignación <span class="text-muted fw-normal">(PDF, opcional)</span></label>
                                <input type="file" name="pdf" class="form-control form-control-bio" accept=".pdf">
                                <div class="form-text">Si adjuntás el remito en PDF, se lee su contenido y en el siguiente paso vas a poder revisar/editar el instrumental detectado antes de confirmar.</div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                            <button type="button" class="btn btn-outline-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-bio">
                                <i class="bi bi-arrow-right me-1"></i> Continuar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modales Devolver --}}
    @foreach($consignadas as $caja)
        <div class="modal fade" id="modal-devolver-{{ $caja->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-arrow-counterclockwise me-2" style="color:#64748b;"></i>Devolver a Disponible — {{ $caja->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('cajas.devolver', $caja) }}" method="POST">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <p class="small text-muted">La caja pasará de <strong>Consignada</strong> a <strong>Sin Consignar</strong> y se limpiarán el consignatario y la fecha de consignación.</p>
                            <label class="form-label form-label-bio">Observaciones</label>
                            <textarea name="observaciones" rows="2" class="form-control form-control-bio" placeholder="Motivo (opcional)"></textarea>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                            <button type="button" class="btn btn-outline-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning btn-bio">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Confirmar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal Ver instrumental --}}
    <button type="button" id="vi-trigger" data-bs-toggle="modal" data-bs-target="#modal-ver-instrumental" style="display:none"></button>
    <div class="modal fade" id="modal-ver-instrumental" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-list-check me-2" style="color:#4338ca;"></i>Instrumental — <span id="vi-caja-nombre"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-4" id="vi-body" style="max-height:70vh;overflow-y:auto;">
                    <div class="text-muted small">Cargando...</div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                    <button type="button" class="btn btn-outline-secondary btn-bio" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function verInstrumental(cajaId, nombre) {
        document.getElementById('vi-caja-nombre').textContent = nombre;
        var body = document.getElementById('vi-body');
        body.innerHTML = '<div class="text-muted small">Cargando...</div>';
        document.getElementById('vi-trigger').click();

        fetch('{{ url('/cajas') }}/' + cajaId + '/consignaciones')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data || data.length === 0) {
                    body.innerHTML = '<div class="text-muted small">Esta caja todavía no tiene instrumental cargado en ninguna consignación.</div>';
                    return;
                }
                var html = '';
                data.forEach(function (c) {
                    html += '<div class="mb-4">';
                    html += '<div class="d-flex justify-content-between align-items-center mb-2">';
                    html += '<strong>' + formatFecha(c.fecha_consignacion) + ' — ' + escHtml(c.consignatario_nombre) + '</strong>';
                    if (c.pdf_path) {
                        html += '<a href="{{ asset('storage') }}/' + c.pdf_path + '" target="_blank" class="btn btn-outline-secondary btn-bio btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>';
                    }
                    html += '</div>';
                    if (!c.items || c.items.length === 0) {
                        html += '<div class="text-muted small">Sin ítems cargados.</div>';
                    } else {
                        html += '<table class="table table-bio table-sm mb-0"><thead><tr><th>Cant.</th><th>Descripción</th><th>Sección</th><th>Incl.</th></tr></thead><tbody>';
                        c.items.forEach(function (it) {
                            html += '<tr><td>' + it.cantidad + '</td><td>' + escHtml(it.descripcion) + '</td><td class="text-muted small">' + escHtml(it.seccion || '—') + '</td><td>' + (it.incluido ? '<i class="bi bi-check-lg text-success"></i>' : '') + '</td></tr>';
                        });
                        html += '</tbody></table>';
                    }
                    html += '</div>';
                });
                body.innerHTML = html;
            })
            .catch(function () {
                body.innerHTML = '<div class="text-danger small">Error al cargar el instrumental.</div>';
            });
    }
    function escHtml(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function formatFecha(iso) {
        if (!iso) return '—';
        var d = new Date(iso);
        if (isNaN(d.getTime())) return iso;
        var dd = String(d.getUTCDate()).padStart(2, '0');
        var mm = String(d.getUTCMonth() + 1).padStart(2, '0');
        return dd + '/' + mm + '/' + d.getUTCFullYear();
    }
    </script>
</x-app-layout>
