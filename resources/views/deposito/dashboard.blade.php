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
                            <div class="stat-label text-success">Sin Consignar</div>
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
                        <div class="stat-card text-center py-3" style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);">
                            <div class="stat-value mb-1" style="color:#5b21b6;">{{ $cajas->where('estado', 'CX FINALIZADA')->count() }}</div>
                            <div class="stat-label" style="color:#5b21b6;">CX Finalizada</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-card text-center py-3" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                            <div class="stat-value text-warning mb-1">{{ $cajas->where('estado', 'EN TRANSITO VUELTA')->count() }}</div>
                            <div class="stat-label text-warning">Tránsito Vuelta</div>
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
                <button type="button" class="btn btn-info btn-bio" data-bs-toggle="modal" data-bs-target="#modal-egreso-grupo">
                    <i class="bi bi-boxes me-1"></i> Egreso por Grupo
                </button>
            </div>
        </div>

        <div class="card-header py-3 bg-white" style="border-top:1px solid #e2e8f0;">
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-primary btn-bio filtro-btn active px-3" data-filtro="todos" onclick="filtrarTabla('todos')">Todos</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-bio filtro-btn px-3" data-filtro="DISPONIBLE" onclick="filtrarTabla('DISPONIBLE')">Sin Consignar</button>
                <button type="button" class="btn btn-sm btn-outline-info btn-bio filtro-btn px-3" data-filtro="CONSIGNADA" onclick="filtrarTabla('CONSIGNADA')">Consignadas</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-bio filtro-btn px-3" data-filtro="EN ESTERILIZADORA" onclick="filtrarTabla('EN ESTERILIZADORA')">Esterilización</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-bio filtro-btn px-3" data-filtro="EN CX" onclick="filtrarTabla('EN CX')">Cirugía</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-bio filtro-btn px-3" data-filtro="CX FINALIZADA" onclick="filtrarTabla('CX FINALIZADA')">CX Finalizada</button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-bio filtro-btn px-3" data-filtro="EN TRANSITO VUELTA" onclick="filtrarTabla('EN TRANSITO VUELTA')">Tránsito Vuelta</button>
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
                                            'CONSIGNADA' => 'badge-info',
                                            'EN ESTERILIZADORA' => 'badge-primary',
            'EN CX' => 'badge-dark',
            'CX FINALIZADA' => 'badge-dark',
            'EN TRANSITO VUELTA' => 'badge-warning',
                                            'PENDIENTE' => 'badge-warning',
                                            'ACONDICIONAMIENTO' => 'badge-info',
                                            'EN REPARACION' => 'badge-danger',
                                            'BAJA' => 'badge-secondary',
                                        ];
                                    @endphp
                                    <span class="badge badge-estado {{ $estilos[$caja->estado] ?? 'badge-secondary' }}">
                                        {{ $caja->estadoLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded-3" style="color:#475569;">{{ $caja->codigo_interno }}</code>
                                </td>
                                <td class="text-end">
                                    @if($caja->estado == 'CONSIGNADA')
                                        <button type="button" class="btn btn-primary btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-egreso-{{ $caja->id }}">
                                            <i class="bi bi-calendar-plus me-1"></i> Asignar
                                        </button>
                                    @endif
                                    @if($caja->estado == 'PENDIENTE')
                                        <button type="button" class="btn btn-warning btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-delegar-{{ $caja->id }}">
                                            <i class="bi bi-send me-1"></i> Delegar
                                        </button>
                                    @endif
                                    @if(isset($tokensActivos[$caja->id]))
                                        <button type="button" class="btn btn-bio btn-sm" style="background:linear-gradient(135deg,#1e293b,#0f172a);color:#fff;" onclick="copiarLink('{{ route('recepcion.token', $tokensActivos[$caja->id]->token) }}')">
                                            <i class="bi bi-clipboard me-1"></i> Link
                                        </button>
                                    @endif
                                    @if(in_array($caja->estado, ['DISPONIBLE', 'EN ESTERILIZADORA', 'EN CX', 'PENDIENTE']))
                                        <button type="button" class="btn btn-warning btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-reparar-{{ $caja->id }}">
                                            <i class="bi bi-tools me-1"></i> Reparar
                                        </button>
                                    @endif
                                    @if($caja->estado == 'EN REPARACION')
                                        <button type="button" class="btn btn-success btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-disponible-{{ $caja->id }}">
                                            <i class="bi bi-check-lg me-1"></i> Disponible
                                        </button>
                                    @endif
                                    @if(in_array($caja->estado, ['DISPONIBLE', 'EN REPARACION']))
                                        <button type="button" class="btn btn-danger btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-baja-{{ $caja->id }}">
                                            <i class="bi bi-trash me-1"></i> Baja
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

    @foreach($cajas->where('estado', 'CONSIGNADA') as $caja)
        <div class="modal fade" id="modal-egreso-{{ $caja->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-plus me-2" style="color:#3b82f6;"></i>Asignar a Cirugía — {{ $caja->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('cajas.egreso', $caja) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body px-4 py-4" style="position:relative;min-height:380px;">

                            {{-- Overlay de búsqueda externa --}}
                            <div id="search-overlay-{{ $caja->id }}" style="display:none;position:absolute;top:0;left:0;right:0;bottom:0;background:#fff;z-index:10;padding:1.5rem;overflow-y:auto;border-radius:0.375rem;">
                                <button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="cerrarBusqueda('{{ $caja->id }}')">
                                    <i class="bi bi-arrow-left me-1"></i> Volver a datos de cirugía
                                </button>
                                <div class="row g-2 mb-3">
                                    <div class="col-3">
                                        <label class="form-label form-label-bio small">Fecha Desde</label>
                                        <input type="date" id="bfecha_desde-{{ $caja->id }}" class="form-control form-control-bio">
                                    </div>
                                    <div class="col-3">
                                        <label class="form-label form-label-bio small">Fecha Hasta</label>
                                        <input type="date" id="bfecha_hasta-{{ $caja->id }}" class="form-control form-control-bio">
                                    </div>
                                    <div class="col-3">
                                        <label class="form-label form-label-bio small">Paciente</label>
                                        <input type="text" id="bpaciente-{{ $caja->id }}" class="form-control form-control-bio" placeholder="Nombre">
                                    </div>
                                    <div class="col-3">
                                        <label class="form-label form-label-bio small">Médico</label>
                                        <input type="text" id="bmedico-{{ $caja->id }}" class="form-control form-control-bio" placeholder="Dr.">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary btn-bio mb-3" onclick="buscarProcedimientos('{{ $caja->id }}')">
                                    <i class="bi bi-search me-1"></i> Buscar
                                </button>
                                <div id="resultados-{{ $caja->id }}" style="display:none;">
                                    <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
                                        <table class="table table-bio mb-0">
                                            <thead>
                                                <tr>
                                                    <th>PlcCod</th>
                                                    <th>Fecha</th>
                                                    <th>Paciente</th>
                                                    <th>Médico</th>
                                                    <th>Hospital</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabla-resultados-{{ $caja->id }}"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div id="form-content-{{ $caja->id }}">
                                <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3" style="color:#0f172a;"><i class="bi bi-person me-1" style="color:#3b82f6;"></i> Datos de la Cirugía</h6>

                                    {{-- Buscador en API externa --}}
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-outline-info btn-bio btn-sm w-100" onclick="abrirBusqueda('{{ $caja->id }}')">
                                            <i class="bi bi-search me-1"></i> Buscar en sistema externo
                                        </button>
                                    </div>

                                    {{-- Badge de CX seleccionada --}}
                                    <div id="cx-seleccionada-{{ $caja->id }}" class="alert alert-success py-1 px-3 mb-3 small d-none" style="border-left:4px solid #10b981;">
                                        <i class="bi bi-check-circle-fill me-1" style="color:#10b981;"></i>
                                        CX <span id="codigo-cx-{{ $caja->id }}"></span> seleccionada
                                    </div>

                                    <input type="hidden" name="plc_cod" id="plc_cod-{{ $caja->id }}" value="">

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
                                            <select name="tecnico_id" class="form-select form-control-bio" required>
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
                                            <input type="text" name="paciente" id="input-paciente-{{ $caja->id }}" required class="form-control form-control-bio" placeholder="Nombre completo">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label form-label-bio">Médico</label>
                                            <input type="text" name="medico" id="input-medico-{{ $caja->id }}" required class="form-control form-control-bio" placeholder="Dr. / Dra.">
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label form-label-bio">Observaciones</label>
                                        <textarea name="observaciones" rows="3" class="form-control form-control-bio" placeholder="Notas para el técnico u otros grupos..."></textarea>
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

    {{-- Modal Egreso por Grupo --}}
    <div class="modal fade" id="modal-egreso-grupo" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-boxes me-2" style="color:#3b82f6;"></i>Egreso por Grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-egreso-grupo" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body px-4 py-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3" style="color:#0f172a;"><i class="bi bi-boxes me-1" style="color:#3b82f6;"></i> Grupo</h6>
                                <div class="mb-3">
                                    <label class="form-label form-label-bio">Seleccionar Grupo</label>
                                    <select name="grupo_id" id="select-grupo" class="form-select form-control-bio" required onchange="cargarCajasGrupo()">
                                        <option value="">Seleccionar...</option>
                                        @foreach($grupos as $grupo)
                                            <option value="{{ $grupo->id }}" data-cajas='@json($grupo->cajas->pluck('id'))'>{{ $grupo->nombre }} ({{ $grupo->cajas->count() }} cajas)</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="info-grupo" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label form-label-bio">Cajas del Grupo</label>
                                        <div id="lista-cajas-grupo" class="table-responsive" style="max-height:250px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:12px;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3" style="color:#0f172a;"><i class="bi bi-person me-1" style="color:#3b82f6;"></i> Datos de la Cirugía</h6>
                                <div class="mb-3">
                                    <label class="form-label form-label-bio">Paciente</label>
                                    <input type="text" name="paciente" id="input-paciente-grupo" required class="form-control form-control-bio" placeholder="Nombre completo">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label form-label-bio">Médico</label>
                                    <input type="text" name="medico" id="input-medico-grupo" required class="form-control form-control-bio" placeholder="Dr. / Dra.">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label form-label-bio">Tipo de Técnico</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipo_tecnico_grupo" value="registrado" id="tipo-reg-grupo" checked onchange="toggleTecnicoGrupo()">
                                            <label class="form-check-label small" for="tipo-reg-grupo">Registrado</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipo_tecnico_grupo" value="externo" id="tipo-ext-grupo" onchange="toggleTecnicoGrupo()">
                                            <label class="form-check-label small" for="tipo-ext-grupo">Externo (sin login)</label>
                                        </div>
                                    </div>
                                </div>
                                <div id="tecnico-registrado-grupo">
                                    <div class="mb-3">
                                        <label class="form-label form-label-bio">Técnico</label>
                                        <select name="tecnico_id" class="form-select form-control-bio" required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($tecnicos as $tecnico)
                                                <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="tecnico-externo-grupo" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label form-label-bio">Nombre del Externo</label>
                                        <input type="text" name="tecnico_nombre" class="form-control form-control-bio" placeholder="Nombre completo del técnico externo">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label form-label-bio">Observaciones</label>
                                    <textarea name="observaciones" rows="3" class="form-control form-control-bio" placeholder="Notas para el técnico u otros grupos..."></textarea>
                                </div>
                                <input type="hidden" name="plc_cod" id="plc_cod-grupo" value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                        <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-bio px-4" id="btn-egreso-grupo" disabled>
                            <i class="bi bi-check-lg me-1"></i> Confirmar Egreso del Grupo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Reparar --}}
    @foreach($cajas->whereIn('estado', ['DISPONIBLE', 'EN ESTERILIZADORA', 'EN CX', 'PENDIENTE']) as $caja)
        <div class="modal fade" id="modal-reparar-{{ $caja->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-tools me-2" style="color:#d97706;"></i>Enviar a Reparación — {{ $caja->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('cajas.reparacion', $caja) }}" method="POST">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <p class="small text-muted mb-3">La caja pasará a estado <strong>EN REPARACION</strong>.</p>
                            <div class="mb-3">
                                <label class="form-label form-label-bio">Motivo / Observaciones</label>
                                <textarea name="observaciones" rows="3" class="form-control form-control-bio" required placeholder="Describa el problema..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                            <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning btn-bio"><i class="bi bi-tools me-1"></i> Enviar a Reparación</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal Volver a Disponible (desde Reparación) --}}
    @foreach($cajas->where('estado', 'EN REPARACION') as $caja)
        <div class="modal fade" id="modal-disponible-{{ $caja->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);">
                        <h5 class="mb-0 fw-bold" style="color:#065f46;"><i class="bi bi-check-circle me-2" style="color:#10b981;"></i>Volver a Disponible — {{ $caja->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('cajas.disponibilizar', $caja) }}" method="POST">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <p class="mb-0">La caja pasará a estado <strong>DISPONIBLE</strong>.</p>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                            <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success btn-bio"><i class="bi bi-check-lg me-1"></i> Volver a Disponible</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal Baja --}}
    @foreach($cajas->whereIn('estado', ['DISPONIBLE', 'EN REPARACION']) as $caja)
        <div class="modal fade" id="modal-baja-{{ $caja->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4" style="background:linear-gradient(135deg,#fef2f2,#fee2e2);">
                        <h5 class="mb-0 fw-bold" style="color:#991b1b;"><i class="bi bi-trash me-2" style="color:#dc2626;"></i>Dar de Baja — {{ $caja->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('cajas.baja', $caja) }}" method="POST">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <div class="alert alert-danger py-2 px-3 small">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Esta acción es irreversible. La caja quedará fuera de circulación.
                            </div>
                            <div class="mb-3">
                                <label class="form-label form-label-bio">Motivo / Observaciones</label>
                                <textarea name="observaciones" rows="3" class="form-control form-control-bio" required placeholder="Ej: Daño estructural irreparable..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                            <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger btn-bio"><i class="bi bi-check-lg me-1"></i> Confirmar Baja</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach($cajas->where('estado', 'PENDIENTE') as $caja)
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
                                @if($caja->estado === 'EN TRANSITO VUELTA')
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
                            @if($caja->estado !== 'EN TRANSITO VUELTA')
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

    @php
        $cirugiasActivas = \App\Models\Cirugia::whereIn('status', ['PENDIENTE', 'EN_CURSO'])->with('cajas', 'tecnico')->get();
    @endphp

    <div class="card card-elegante mb-4">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);">
            <h5 class="mb-0" style="color:#5b21b6;"><i class="bi bi-heart-pulse me-2" style="color:#7c3aed;"></i>Cirugías Activas</h5>
        </div>
        <div class="card-body p-0">
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
                    <div class="d-flex gap-2">
                        @if(!$cirugia->tecnico_id && $cirugia->tecnico_nombre)
                            <button type="button" class="btn btn-bio btn-sm" style="background:linear-gradient(135deg,#1e293b,#0f172a);color:#fff;" onclick="copiarLink('{{ route('tecnico.surgery.view', [$cirugia, $cirugia->access_token]) }}')">
                                <i class="bi bi-clipboard me-1"></i> Copiar Link
                            </button>
                        @endif
                        <button type="button" class="btn btn-outline-primary btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-reasignar-{{ $cirugia->id }}">
                            <i class="bi bi-arrow-repeat me-1"></i> Reasignar
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-bio btn-sm" data-bs-toggle="modal" data-bs-target="#modal-cancelar-{{ $cirugia->id }}">
                            <i class="bi bi-x-circle me-1"></i> Cancelar
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <p class="text-muted mb-0">No hay cirugías activas en este momento.</p>
                </div>
            @endforelse
        </div>
    </div>

    @foreach($cirugiasActivas as $cirugia)
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
    @endforeach

    @foreach($cirugiasActivas as $cirugia)
        <div class="modal fade" id="modal-cancelar-{{ $cirugia->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom bg-light px-4 py-3 rounded-top-4" style="background:linear-gradient(135deg,#fef2f2,#fee2e2);">
                        <h5 class="mb-0 fw-bold" style="color:#991b1b;"><i class="bi bi-x-circle me-2" style="color:#dc2626;"></i>Cancelar / Postergar — {{ $cirugia->paciente }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('cirugias.cancelar', $cirugia) }}" method="POST">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <div class="mb-3">
                                <label class="form-label form-label-bio">Acción</label>
                                <select name="accion" class="form-select form-control-bio" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="cancelar">Cancelar cirugía definitivamente</option>
                                    <option value="postergar">Postergar (se puede retomar después)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label form-label-bio">Motivo</label>
                                <textarea name="motivo" rows="3" class="form-control form-control-bio" required placeholder="Ej: Paciente reprogramado / Emergencia / Equipo no disponible..."></textarea>
                            </div>
                            <div class="alert alert-warning py-2 px-3 small mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Las cajas asociadas volverán a estado <strong>DISPONIBLE</strong>.
                            </div>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top">
                            <button type="button" class="btn btn-secondary btn-bio" data-bs-dismiss="modal">Volver</button>
                            <button type="submit" class="btn btn-danger btn-bio"><i class="bi bi-check-lg me-1"></i> Confirmar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        function abrirBusqueda(id) {
            document.getElementById('form-content-' + id).style.display = 'none';
            document.getElementById('search-overlay-' + id).style.display = '';
            document.getElementById('bfecha_desde-' + id).value = '';
            document.getElementById('bfecha_hasta-' + id).value = '';
            document.getElementById('bpaciente-' + id).value = '';
            document.getElementById('bmedico-' + id).value = '';
            document.getElementById('resultados-' + id).style.display = 'none';
        }

        function cerrarBusqueda(id) {
            document.getElementById('form-content-' + id).style.display = '';
            document.getElementById('search-overlay-' + id).style.display = 'none';
        }

        function buscarProcedimientos(id) {
            const fecha_desde = document.getElementById('bfecha_desde-' + id).value;
            const fecha_hasta = document.getElementById('bfecha_hasta-' + id).value;
            const paciente = document.getElementById('bpaciente-' + id).value;
            const medico = document.getElementById('bmedico-' + id).value;
            const tbody = document.getElementById('tabla-resultados-' + id);
            const contenedor = document.getElementById('resultados-' + id);

            const params = new URLSearchParams();
            params.set('limit', 20);
            if (fecha_desde) params.set('fecha_desde', fecha_desde);
            if (fecha_hasta) params.set('fecha_hasta', fecha_hasta);
            if (paciente) params.set('paciente', paciente);
            if (medico) params.set('medico', medico);

            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-2"><span class="spinner-border spinner-border-sm me-2" role="status"></span>Buscando...</td></tr>';
            contenedor.style.display = '';

            fetch('{{ route("procedimientos.buscar") }}?' + params.toString())
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (!data || data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-2">Sin resultados</td></tr>';
                        return;
                    }
                    tbody.innerHTML = '';
                    data.forEach(function(item) {
                        var tr = document.createElement('tr');
                        tr.innerHTML = '<td><code>' + item.id_cirugia + '</code></td>' +
                            '<td>' + (item.fecha_cirugia || '') + '</td>' +
                            '<td>' + (item.paciente || '') + '</td>' +
                            '<td>' + (item.nombre_medico || '') + '</td>' +
                            '<td>' + (item.nombre_hospital || '') + '</td>' +
                            '<td><button type="button" class="btn btn-success btn-bio btn-sm" onclick="seleccionarCx(\'' + id + '\',\'' + item.id_cirugia + '\',\'' + (item.paciente || '').replace(/'/g,"\\'") + '\',\'' + (item.nombre_medico || '').replace(/'/g,"\\'") + '\')"><i class="bi bi-check-lg me-1"></i> Seleccionar</button></td>';
                        tbody.appendChild(tr);
                    });
                })
                .catch(function() {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-2">Error al conectar con el servidor</td></tr>';
                });
        }

        function seleccionarCx(id, plcCod, paciente, medico) {
            document.getElementById('plc_cod-' + id).value = plcCod;
            document.getElementById('input-paciente-' + id).value = paciente;
            document.getElementById('input-medico-' + id).value = medico;

            var badge = document.getElementById('cx-seleccionada-' + id);
            document.getElementById('codigo-cx-' + id).textContent = '#' + plcCod;
            badge.classList.remove('d-none');

            cerrarBusqueda(id);
        }

        function copiarLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                const toast = document.createElement('div');
                toast.className = 'position-fixed top-50 start-50 translate-middle';
                toast.style.zIndex = '9999';
                toast.innerHTML = '<div class="bg-dark text-white px-5 py-3 rounded-4 shadow-lg fw-semibold" style="font-size:1rem;"><i class="bi bi-check-circle-fill text-success me-2"></i>Link copiado al portapapeles</div>';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            });
        }

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

        function toggleTecnicoGrupo() {
            const reg = document.getElementById('tecnico-registrado-grupo');
            const ext = document.getElementById('tecnico-externo-grupo');
            const esExterno = document.getElementById('tipo-ext-grupo').checked;
            reg.style.display = esExterno ? 'none' : '';
            ext.style.display = esExterno ? '' : 'none';
            const select = reg.querySelector('select');
            const input = ext.querySelector('input');
            if (select) select.required = !esExterno;
            if (input) input.required = esExterno;
        }

        function cargarCajasGrupo() {
            const select = document.getElementById('select-grupo');
            const grupoId = select.value;
            const contenedor = document.getElementById('lista-cajas-grupo');
            const infoDiv = document.getElementById('info-grupo');
            const btn = document.getElementById('btn-egreso-grupo');
            const form = document.getElementById('form-egreso-grupo');

            if (!grupoId) {
                infoDiv.style.display = 'none';
                btn.disabled = true;
                return;
            }

            form.action = '{{ url("grupos") }}/' + grupoId + '/egreso';

            const option = select.options[select.selectedIndex];
            let cajas = [];
            try {
                cajas = JSON.parse(option.getAttribute('data-cajas') || '[]');
            } catch(e) { cajas = []; }

            infoDiv.style.display = '';

            const cajasData = @json($cajasDisponibles->keyBy('id'));
            const allCajas = @json($cajas->keyBy('id'));

            let html = '<table class="table table-sm table-bio mb-0"><thead><tr><th>Nombre</th><th>Código</th><th>Estado</th></tr></thead><tbody>';
            let allAvailable = true;

            cajas.forEach(function(cajaId) {
                const caja = allCajas[cajaId];
                if (!caja) return;
                const disponible = caja.estado === 'CONSIGNADA';
                if (!disponible) allAvailable = false;
                const badgeClass = disponible ? 'badge-info' : 'badge-secondary';
                const estadoHtml = disponible
                    ? '<span class="badge badge-estado badge-info">CONSIGNADA</span>'
                    : '<span class="badge badge-estado badge-secondary">' + caja.estado + ' — se omitirá</span>';
                html += '<tr class="' + (disponible ? '' : 'text-muted') + '"><td><small>' + caja.nombre + '</small></td><td><code>' + caja.codigo_interno + '</code></td><td>' + estadoHtml + '</td></tr>';
            });

            html += '</tbody></table>';
            contenedor.innerHTML = html;
            btn.disabled = false;
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
