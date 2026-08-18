<x-app-layout>
    <x-slot name="header">Mis Cirugías Asignadas</x-slot>

    <div class="card card-elegante">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
            <div>
                <h5 class="mb-0"><i class="bi bi-heart-pulse me-2" style="color:#3b82f6;"></i>Historial y Pendientes</h5>
                <small style="color:#94a3b8;">Listado de cirugías vinculadas a tu usuario</small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span id="contador-cirugias" class="badge" style="background:linear-gradient(135deg,#f1f5f9,#e2e8f0);color:#64748b;font-weight:700;padding:0.5em 1em;border-radius:50rem;">
                    {{ $cirugias->count() }} Cirugías
                </span>
                <div class="input-group" style="width:260px;">
                    <span class="input-group-text border-0 bg-light">
                        <i class="bi bi-search" style="color:#94a3b8;"></i>
                    </span>
                    <input type="text" id="buscador-cirugias" class="form-control border-0 bg-light" placeholder="Buscar paciente o médico...">
                </div>
            </div>
        </div>

        <div class="card-header py-3 bg-white" style="border-top:1px solid #e2e8f0;">
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <button type="button" class="btn btn-sm btn-primary btn-bio filtro-cirugia-btn active px-3" data-filtro="todos" onclick="filtrarCirugias('todos')">Todos</button>
                <button type="button" class="btn btn-sm btn-outline-warning btn-bio filtro-cirugia-btn px-3" data-filtro="PENDIENTE" onclick="filtrarCirugias('PENDIENTE')">Pendiente</button>
                <button type="button" class="btn btn-sm btn-outline-success btn-bio filtro-cirugia-btn px-3" data-filtro="EN_CURSO" onclick="filtrarCirugias('EN_CURSO')">En curso</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-bio filtro-cirugia-btn px-3" data-filtro="COMPLETADA" onclick="filtrarCirugias('COMPLETADA')">Completada</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-bio filtro-cirugia-btn px-3" data-filtro="CANCELADA" onclick="filtrarCirugias('CANCELADA')">Cancelada</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-bio filtro-cirugia-btn px-3" data-filtro="POSTPUESTA" onclick="filtrarCirugias('POSTPUESTA')">Postpuesta</button>
                <div class="vr mx-1 d-none d-md-block"></div>
                <div class="d-flex gap-2 align-items-center">
                    <label class="small text-muted mb-0" style="white-space:nowrap;">Desde</label>
                    <input type="date" id="fecha-desde" class="form-control form-control-sm" style="width:150px;">
                    <label class="small text-muted mb-0" style="white-space:nowrap;">Hasta</label>
                    <input type="date" id="fecha-hasta" class="form-control form-control-sm" style="width:150px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-bio" onclick="limpiarFiltrosCirugias()">
                        <i class="bi bi-x-lg me-1"></i> Limpiar
                    </button>
                </div>
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
                <table class="table table-bio table-hover mb-0" id="tabla-cirugias">
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
                            <tr data-estado="{{ $cirugia->status }}" data-fecha="{{ $cirugia->fecha_cx->format('Y-m-d') }}">
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

    <script>
        let filtroCirugiaActual = 'todos';

        function aplicarFiltrosCirugias() {
            const texto = (document.getElementById('buscador-cirugias').value || '').toLowerCase();
            const desde = document.getElementById('fecha-desde').value;
            const hasta = document.getElementById('fecha-hasta').value;
            let count = 0;

            document.querySelectorAll('#tabla-cirugias tbody tr').forEach(function (f) {
                const estado = f.getAttribute('data-estado');
                const fecha = f.getAttribute('data-fecha');

                let ok = true;
                if (filtroCirugiaActual !== 'todos' && estado !== filtroCirugiaActual) ok = false;
                if (ok && texto !== '') {
                    const contenido = f.textContent.toLowerCase();
                    if (!contenido.includes(texto)) ok = false;
                }
                if (ok && desde !== '' && fecha < desde) ok = false;
                if (ok && hasta !== '' && fecha > hasta) ok = false;

                f.style.display = ok ? '' : 'none';
                if (ok) count++;
            });

            document.getElementById('contador-cirugias').textContent = count + ' Cirugías';
        }

        function filtrarCirugias(filtro) {
            filtroCirugiaActual = filtro;
            document.querySelectorAll('.filtro-cirugia-btn').forEach(function (b) {
                b.classList.remove('active', 'btn-primary', 'btn-outline-warning', 'btn-outline-success', 'btn-outline-secondary');
            });
            const btn = document.querySelector(`.filtro-cirugia-btn[data-filtro="${filtro}"]`);
            if (btn) {
                btn.classList.add('active', 'btn-primary');
                btn.classList.remove('btn-outline-warning', 'btn-outline-success', 'btn-outline-secondary');
            }
            aplicarFiltrosCirugias();
        }

        function limpiarFiltrosCirugias() {
            document.getElementById('buscador-cirugias').value = '';
            document.getElementById('fecha-desde').value = '';
            document.getElementById('fecha-hasta').value = '';
            filtrarCirugias('todos');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const buscador = document.getElementById('buscador-cirugias');
            if (buscador) {
                buscador.addEventListener('input', aplicarFiltrosCirugias);
            }
            const desde = document.getElementById('fecha-desde');
            const hasta = document.getElementById('fecha-hasta');
            if (desde) desde.addEventListener('change', aplicarFiltrosCirugias);
            if (hasta) hasta.addEventListener('change', aplicarFiltrosCirugias);
        });
    </script>
</x-app-layout>
