<x-app-layout>
    <x-slot name="header">Gestión de Inventario</x-slot>

    <div class="card card-elegante mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><i class="bi bi-clipboard-data me-2" style="color:#3b82f6;"></i>Resumen de Cajas</h5>
            <button class="btn btn-sm btn-outline-secondary btn-bio" type="button" data-bs-toggle="collapse" data-bs-target="#collapseResumen" aria-expanded="true">
                <i class="bi bi-arrows-collapse"></i>
            </button>
        </div>
        <div class="collapse show" id="collapseResumen">
            <div class="card-body pb-2">
                <div class="row g-3">
                    <div class="col-3">
                        <div class="stat-card text-center py-3" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);">
                            <div class="stat-value text-success mb-1">{{ $cajas->where('estado', 'DISPONIBLE')->count() }}</div>
                            <div class="stat-label text-success">Disponibles</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-card text-center py-3" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
                            <div class="stat-value text-primary mb-1">{{ $cajas->where('estado', 'EN ESTERILIZADORA')->count() }}</div>
                            <div class="stat-label text-primary">Esterilización</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-card text-center py-3" style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);">
                            <div class="stat-value mb-1" style="color:#7c3aed;">{{ $cajas->where('estado', 'EN CX')->count() }}</div>
                            <div class="stat-label" style="color:#7c3aed;">En Cirugía</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-card text-center py-3" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                            <div class="stat-value text-warning mb-1">{{ $cajas->where('estado', 'EN TRANSITO')->count() }}</div>
                            <div class="stat-label text-warning">En Tránsito</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-card text-center py-3" style="background:linear-gradient(135deg,#fdf2f8,#fce7f3);">
                            <div class="stat-value mb-1" style="color:#db2777;">{{ $cajas->where('estado', 'PENDIENTE')->count() }}</div>
                            <div class="stat-label" style="color:#db2777;">Auditando</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-card text-center py-3" style="background:linear-gradient(135deg,#ecfeff,#cffafe);">
                            <div class="stat-value text-info mb-1">{{ $cajas->where('estado', 'ACONDICIONAMIENTO')->count() }}</div>
                            <div class="stat-label text-info">Lavado</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-card text-center py-3" style="background:linear-gradient(135deg,#fef2f2,#fee2e2);">
                            <div class="stat-value text-danger mb-1">{{ $cajas->where('estado', 'EN REPARACION')->count() }}</div>
                            <div class="stat-label text-danger">Reparación</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-card text-center py-3" style="background:linear-gradient(135deg,#f8fafc,#e2e8f0);">
                            <div class="stat-value mb-1" style="color:#64748b;">{{ $cajas->where('estado', 'BAJA')->count() }}</div>
                            <div class="stat-label" style="color:#64748b;">Baja</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-elegante mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="mb-0"><i class="bi bi-box-seam me-2" style="color:#3b82f6;"></i>Listado de Cajas</h5>
            <div class="d-flex align-items-center gap-3">
                <span id="contador" class="badge" style="background:linear-gradient(135deg,#f1f5f9,#e2e8f0);color:#64748b;font-weight:700;padding:0.5em 1em;border-radius:50rem;">
                    {{ $cajas->count() }} Cajas
                </span>
                <div class="input-group" style="width:280px;">
                    <span class="input-group-text border-0 bg-light">
                        <i class="bi bi-search" style="color:#94a3b8;"></i>
                    </span>
                    <input type="text" id="buscador" class="form-control border-0 bg-light" placeholder="Buscar nombre o código...">
                </div>
            </div>
        </div>

        <div class="card-header py-3 bg-white" style="border-top:1px solid #e2e8f0;">
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-primary btn-bio filtro-btn active px-3" data-filtro="todos" onclick="filtrarTabla('todos')">Todos</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-bio filtro-btn px-3" data-filtro="DISPONIBLE" onclick="filtrarTabla('DISPONIBLE')">Disponibles</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-bio filtro-btn px-3" data-filtro="EN ESTERILIZADORA" onclick="filtrarTabla('EN ESTERILIZADORA')">Esterilización</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-bio filtro-btn px-3" data-filtro="EN CX" onclick="filtrarTabla('EN CX')">Cirugía</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-bio filtro-btn px-3" data-filtro="EN TRANSITO" onclick="filtrarTabla('EN TRANSITO')">Tránsito</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-bio filtro-btn px-3" data-filtro="PENDIENTE" onclick="filtrarTabla('PENDIENTE')">Auditoría</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-bio filtro-btn px-3" data-filtro="ACONDICIONAMIENTO" onclick="filtrarTabla('ACONDICIONAMIENTO')">Lavado</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-bio filtro-btn px-3" data-filtro="EN REPARACION" onclick="filtrarTabla('EN REPARACION')">Reparación</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-bio filtro-btn px-3" data-filtro="BAJA" onclick="filtrarTabla('BAJA')">Baja</button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bio table-hover mb-0" id="tabla-cajas">
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
                            <tr data-estado="{{ $caja->estado }}">
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
                                    <code class="bg-light px-2 py-1 rounded-3" style="color:#475569;">{{ $caja->codigo_interno }}</code>
                                </td>
                                <td class="text-end">
                                    @if($caja->estado == 'DISPONIBLE')
                                        <button type="button" class="btn btn-primary btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-egreso-{{ $caja->id }}">
                                            <i class="bi bi-calendar-plus me-1"></i> Asignar
                                        </button>
                                    @endif
                                    @if(in_array($caja->estado, ['EN TRANSITO', 'PENDIENTE']))
                                        <button type="button" class="btn btn-warning btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-delegar-{{ $caja->id }}">
                                            <i class="bi bi-send me-1"></i> Delegar
                                        </button>
                                    @endif
                                    <a href="{{ route('cajas.show', $caja) }}" class="btn btn-outline-secondary btn-bio btn-sm">
                                        <i class="bi bi-eye me-1"></i> Detalles
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach($cajas->where('estado', 'DISPONIBLE') as $caja)
        <div class="modal fade" id="modal-egreso-{{ $caja->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-plus me-2" style="color:#3b82f6;"></i>Asignar a Cirugía — {{ $caja->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('cajas.egreso', $caja) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3" style="color:#0f172a;"><i class="bi bi-person me-1" style="color:#3b82f6;"></i> Datos de la Cirugía</h6>

                                    <div class="mb-3">
                                        <label class="form-label form-label-bio">Tipo de Técnico</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_tecnico" value="registrado" id="tipo-reg-{{ $caja->id }}" checked onchange="toggleTecnico('{{ $caja->id }}')">
                                                <label class="form-check-label small" for="tipo-reg-{{ $caja->id }}">Registrado</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_tecnico" value="externo" id="tipo-ext-{{ $caja->id }}" onchange="toggleTecnico('{{ $caja->id }}')">
                                                <label class="form-check-label small" for="tipo-ext-{{ $caja->id }}">Externo (sin login)</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="tecnico-registrado-{{ $caja->id }}">
                                        <div class="mb-3">
                                            <label class="form-label form-label-bio">Técnico</label>
                                            <select name="tecnico_id" class="form-select form-control-bio">
                                                <option value="">Seleccionar...</option>
                                                @foreach($tecnicos as $tecnico)
                                                    <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div id="tecnico-externo-{{ $caja->id }}" style="display:none;">
                                        <div class="mb-3">
                                            <label class="form-label form-label-bio">Nombre del Externo</label>
                                            <input type="text" name="tecnico_nombre" class="form-control form-control-bio" placeholder="Nombre completo del técnico externo">
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="form-label form-label-bio">Paciente</label>
                                            <input type="text" name="paciente" required class="form-control form-control-bio" placeholder="Nombre completo">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label form-label-bio">Médico</label>
                                            <input type="text" name="medico" required class="form-control form-control-bio" placeholder="Dr. / Dra.">
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label form-label-bio">ID Bioimplant <span class="text-muted fw-normal">(opcional)</span></label>
                                        <input type="text" name="bioimplant_id" class="form-control form-control-bio" placeholder="Número de proyecto">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3" style="color:#0f172a;"><i class="bi bi-paperclip me-1" style="color:#8b5cf6;"></i> Documentación</h6>
                                    @if($caja->pdf_path)
                                        <div class="alert alert-success py-2 px-3 mb-3 d-flex align-items-center gap-2" style="border-left:4px solid #10b981;">
                                            <i class="bi bi-file-earmark-pdf-fill" style="color:#dc2626;"></i>
                                            <span class="small">PDF cargado — subir uno nuevo lo reemplazará.</span>
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <label class="form-label form-label-bio">Nota de Consignación <span class="text-muted fw-normal">(PDF, opcional)</span></label>
                                        <input type="file" name="pdf" class="form-control form-control-bio" accept=".pdf">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label form-label-bio">Fotos del estado actual <span class="text-muted fw-normal">(opcional, múltiple)</span></label>
                                        <input type="file" name="imagenes[]" class="form-control form-control-bio" accept="image/*" multiple>
                                    </div>
                                    @if($caja->imagenes->isNotEmpty())
                                        <div>
                                            <label class="form-label form-label-bio small">Imágenes existentes:</label>
                                            <div class="d-flex gap-2 flex-wrap">
                                                @foreach($caja->imagenes->take(4) as $img)
                                                    <img src="{{ asset('storage/' . $img->ruta) }}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:2px solid #e2e8f0;">
                                                @endforeach
                                                @if($caja->imagenes->count() > 4)
                                                    <div class="d-flex align-items-center justify-content-center bg-light rounded-3 fw-bold" style="width:48px;height:48px;color:#94a3b8;font-size:0.75rem;">+{{ $caja->imagenes->count()-4 }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                            <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-bio px-4">
                                <i class="bi bi-check-lg me-1"></i> Confirmar Egreso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach($cajas->whereIn('estado', ['EN TRANSITO', 'PENDIENTE']) as $caja)
        <div class="modal fade" id="modal-delegar-{{ $caja->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-send me-2" style="color:#d97706;"></i>Delegar Recepción — {{ $caja->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('cajas.delegarRecepcion', $caja) }}" method="POST">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <div class="mb-3">
                                <label class="form-label form-label-bio">Nombre del Responsable Externo</label>
                                <input type="text" name="responsable_nombre" class="form-control form-control-bio" required placeholder="Ej: Juan Pérez (logística)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label form-label-bio">Acción a delegar</label>
                                @if($caja->estado === 'EN TRANSITO')
                                    <input type="hidden" name="accion" value="consumo.controlar">
                                    <div class="alert alert-info py-2 px-3 small mb-0">
                                        <i class="bi bi-arrow-down-circle me-1"></i> Recibir caja e iniciar control
                                    </div>
                                @else
                                    <select name="accion" class="form-select form-control-bio" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="consumo.finalizar" data-resultado="ok">Auditar — Integridad OK</option>
                                        <option value="consumo.finalizar" data-resultado="falla">Auditar — Reportar falla</option>
                                    </select>
                                @endif
                            </div>
                            @if($caja->estado !== 'EN TRANSITO')
                                <input type="hidden" name="resultado" id="resultado-delegar-{{ $caja->id }}" value="">
                            @endif
                        </div>
                        <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                            <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-bio"><i class="bi bi-link me-1"></i> Generar Link</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <div class="card card-elegante mb-4">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);">
            <h5 class="mb-0" style="color:#5b21b6;"><i class="bi bi-heart-pulse me-2" style="color:#7c3aed;"></i>Cirugías Activas</h5>
        </div>
        <div class="card-body p-0">
            @php
                $cirugiasActivas = \App\Models\Cirugia::whereIn('status', ['PENDIENTE', 'EN_CURSO'])->with('cajas', 'tecnico')->get();
            @endphp
            @forelse($cirugiasActivas as $cirugia)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#f5f3ff,#ede9fe);">
                            <i class="bi bi-heart-pulse" style="color:#7c3aed;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="color:#0f172a;">
                                {{ $cirugia->paciente }}
                                <span class="badge ms-2" style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);color:#5b21b6;font-size:0.6rem;vertical-align:middle;">
                                    {{ $cirugia->status }}
                                </span>
                            </div>
                            <small style="color:#94a3b8;">
                                Técnico: {{ $cirugia->tecnico?->name ?? $cirugia->tecnico_nombre ?? 'Sin asignar' }}
                                @if($cirugia->tecnico_original_id)
                                    <span class="text-muted">(original: {{ \App\Models\User::find($cirugia->tecnico_original_id)?->name ?? 'N/A' }})</span>
                                @endif
                                &middot; Cajas: {{ $cirugia->cajas->pluck('nombre')->implode(', ') }}
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-reasignar-{{ $cirugia->id }}">
                        <i class="bi bi-arrow-repeat me-1"></i> Reasignar
                    </button>
                </div>

                <div class="modal fade" id="modal-reasignar-{{ $cirugia->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4">
                            <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-arrow-repeat me-2" style="color:#7c3aed;"></i>Reasignar — {{ $cirugia->paciente }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('cirugias.reasignar', $cirugia) }}" method="POST">
                                @csrf
                                <div class="modal-body px-4 py-4">
                                    <div class="mb-3">
                                        <label class="form-label form-label-bio">Técnico Actual</label>
                                        <p class="form-control form-control-bio bg-light mb-0" style="cursor:default;">
                                            {{ $cirugia->tecnico?->name ?? $cirugia->tecnico_nombre ?? 'Sin asignar' }}
                                            @if($cirugia->tecnico_original_id)
                                                <small class="text-muted">(original: {{ \App\Models\User::find($cirugia->tecnico_original_id)?->name ?? 'N/A' }})</small>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex gap-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_reasignar" value="registrado" id="reasignar-reg-{{ $cirugia->id }}" checked onchange="toggleReasignar('{{ $cirugia->id }}')">
                                                <label class="form-check-label small" for="reasignar-reg-{{ $cirugia->id }}">Registrado</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_reasignar" value="externo" id="reasignar-ext-{{ $cirugia->id }}" onchange="toggleReasignar('{{ $cirugia->id }}')">
                                                <label class="form-check-label small" for="reasignar-ext-{{ $cirugia->id }}">Externo</label>
                                            </div>
                                        </div>
                                        <div id="reasignar-registro-{{ $cirugia->id }}">
                                            <label class="form-label form-label-bio">Nuevo Técnico</label>
                                            <select name="tecnico_id" class="form-select form-control-bio">
                                                <option value="">Seleccionar...</option>
                                                @foreach($tecnicos as $tecnico)
                                                    <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="reasignar-externo-{{ $cirugia->id }}" style="display:none;">
                                            <label class="form-label form-label-bio">Nombre del Reemplazo Externo</label>
                                            <input type="text" name="tecnico_nombre" class="form-control form-control-bio" placeholder="Nombre completo">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                                    <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary btn-bio"><i class="bi bi-check-lg me-1"></i> Reasignar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <p class="text-muted mb-0">No hay cirugías activas en este momento.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function toggleTecnico(id) {
            const reg = document.getElementById('tecnico-registrado-' + id);
            const ext = document.getElementById('tecnico-externo-' + id);
            const esExterno = document.getElementById('tipo-ext-' + id).checked;
            reg.style.display = esExterno ? 'none' : '';
            ext.style.display = esExterno ? '' : 'none';
            const select = reg.querySelector('select');
            const input = ext.querySelector('input');
            if (select) select.required = !esExterno;
            if (input) input.required = esExterno;
        }

        function toggleReasignar(id) {
            const reg = document.getElementById('reasignar-registro-' + id);
            const ext = document.getElementById('reasignar-externo-' + id);
            const esExterno = document.getElementById('reasignar-ext-' + id).checked;
            reg.style.display = esExterno ? 'none' : '';
            ext.style.display = esExterno ? '' : 'none';
        }

        document.addEventListener('change', function(e) {
            if (e.target.matches('select[name="accion"]')) {
                const opt = e.target.options[e.target.selectedIndex];
                const modal = e.target.closest('.modal');
                if (modal) {
                    const resultado = modal.querySelector('input[name="resultado"]');
                    if (resultado && opt && opt.dataset.resultado) {
                        resultado.value = opt.dataset.resultado;
                    }
                }
            }
        });

        let filtroActual = 'todos';
        function filtrarTabla(filtro) {
            filtroActual = filtro;
            document.querySelectorAll('.filtro-btn').forEach(b => { b.classList.remove('active','btn-primary'); b.classList.add('btn-outline-primary'); });
            document.querySelector(`[data-filtro="${filtro}"]`).classList.add('active','btn-primary');
            document.querySelector(`[data-filtro="${filtro}"]`).classList.remove('btn-outline-primary');
            let count = 0;
            document.querySelectorAll('#tabla-cajas tbody tr').forEach(f => {
                const ok = filtro === 'todos' || f.getAttribute('data-estado') === filtro;
                f.style.display = ok ? '' : 'none';
                if (ok) count++;
            });
            document.getElementById('contador').textContent = count + ' Cajas';
        }
        document.getElementById('buscador').addEventListener('input', function() {
            const t = this.value.toLowerCase();
            let count = 0;
            document.querySelectorAll('#tabla-cajas tbody tr').forEach(f => {
                if (filtroActual !== 'todos' && f.getAttribute('data-estado') !== filtroActual) { f.style.display = 'none'; return; }
                const n = f.querySelector('td:first-child .fw-bold').textContent.toLowerCase();
                const c = f.querySelector('code').textContent.toLowerCase();
                const m = n.includes(t) || c.includes(t);
                f.style.display = m ? '' : 'none';
                if (m) count++;
            });
            document.getElementById('contador').textContent = count + ' Cajas';
        });
    </script>
</x-app-layout>
