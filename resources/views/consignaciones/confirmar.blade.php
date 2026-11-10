<x-app-layout>
    <x-slot name="header">Confirmar Consignación</x-slot>

    <div class="card card-elegante mb-4">
        <div class="card-header" style="background:linear-gradient(135deg,#eef2ff,#e0e7ff);">
            <h5 class="mb-0" style="color:#4338ca;"><i class="bi bi-box-arrow-up me-2" style="color:#4338ca;"></i>{{ $caja->nombre }} <span class="text-muted small">({{ $caja->codigo_interno }})</span></h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label form-label-bio">Consignatario</label>
                    <p class="fw-semibold mb-0">{{ $consignatario_nombre }}</p>
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-bio">Fecha de consignación</label>
                    <p class="fw-semibold mb-0">{{ \Illuminate\Support\Carbon::parse($fecha_consignacion)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-bio">PDF</label>
                    <p class="fw-semibold mb-0">
                        @if($pdf_path)
                            <a href="{{ asset('storage/' . $pdf_path) }}" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i>Ver remito</a>
                        @else
                            <span class="text-muted">Sin PDF adjunto</span>
                        @endif
                    </p>
                </div>
                @if($observaciones)
                <div class="col-12">
                    <label class="form-label form-label-bio">Observaciones</label>
                    <p class="mb-0">{{ $observaciones }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card card-elegante mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-check me-2" style="color:#3b82f6;"></i>Instrumental de la caja</h5>
            @if($pdf_path)
                <span class="badge" style="background:linear-gradient(135deg,#f1f5f9,#e2e8f0);color:#64748b;font-weight:700;padding:0.5em 1em;border-radius:50rem;">
                    Extraído automáticamente del PDF — revisá y editá antes de confirmar
                </span>
            @endif
        </div>
        <form action="{{ route('cajas.consignar', $caja) }}" method="POST">
            @csrf
            <input type="hidden" name="consignatario_nombre" value="{{ $consignatario_nombre }}">
            <input type="hidden" name="fecha_consignacion" value="{{ $fecha_consignacion }}">
            <input type="hidden" name="observaciones" value="{{ $observaciones }}">
            <input type="hidden" name="pdf_path" value="{{ $pdf_path }}">

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bio mb-0" id="tabla-instrumental">
                        <thead>
                            <tr>
                                <th style="width:44px"></th>
                                <th style="width:80px">Cant.</th>
                                <th>Descripción</th>
                                <th style="width:220px">Sección</th>
                                <th style="width:70px" class="text-center">Incluir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $idx = 0; @endphp
                            @forelse($secciones as $s)
                                @foreach($s['items'] as $item)
                                <tr>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarFila(this)" title="Eliminar ítem"><i class="bi bi-x-lg"></i></button>
                                    </td>
                                    <td><input type="number" name="cant[{{ $idx }}]" value="{{ (int) $item['cantidad'] }}" min="0" class="form-control form-control-bio form-control-sm"></td>
                                    <td><input type="text" name="desc[{{ $idx }}]" value="{{ $item['descripcion'] }}" class="form-control form-control-bio form-control-sm"></td>
                                    <td><input type="text" name="seccion[{{ $idx }}]" value="{{ $s['nombre'] }}" class="form-control form-control-bio form-control-sm"></td>
                                    <td class="text-center"><input type="checkbox" name="chk[{{ $idx }}]" value="S" class="form-check-input"></td>
                                </tr>
                                @php $idx++; @endphp
                                @endforeach
                            @empty
                                <tr id="fila-vacia">
                                    <td colspan="5" class="text-center text-muted small py-4">
                                        No hay ítems para mostrar. Podés agregarlos con el botón "Agregar ítem".
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-outline-primary btn-bio btn-sm mt-3" onclick="agregarItem()">
                    <i class="bi bi-plus-lg me-1"></i> Agregar ítem
                </button>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between border-top py-3">
                <a href="{{ route('consignaciones.index') }}" class="btn btn-outline-secondary btn-bio">
                    <i class="bi bi-arrow-left me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary btn-bio">
                    <i class="bi bi-check-lg me-1"></i> Confirmar Consignación
                </button>
            </div>
        </form>
    </div>

    <script>
    var proximoIdx = {{ $idx }};
    function agregarItem() {
        var tbody = document.querySelector('#tabla-instrumental tbody');
        var filaVacia = document.getElementById('fila-vacia');
        if (filaVacia) filaVacia.remove();
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarFila(this)" title="Eliminar ítem"><i class="bi bi-x-lg"></i></button></td>' +
            '<td><input type="number" name="cant[' + proximoIdx + ']" value="1" min="0" class="form-control form-control-bio form-control-sm"></td>' +
            '<td><input type="text" name="desc[' + proximoIdx + ']" value="" class="form-control form-control-bio form-control-sm" placeholder="Descripción del ítem"></td>' +
            '<td><input type="text" name="seccion[' + proximoIdx + ']" value="" class="form-control form-control-bio form-control-sm" placeholder="Sección (opcional)"></td>' +
            '<td class="text-center"><input type="checkbox" name="chk[' + proximoIdx + ']" value="S" class="form-check-input"></td>';
        tbody.appendChild(tr);
        proximoIdx++;
    }
    function eliminarFila(btn) {
        if (!confirm('¿Eliminar este ítem?')) return;
        var tr = btn.closest('tr');
        if (tr) tr.remove();
    }
    </script>
</x-app-layout>
