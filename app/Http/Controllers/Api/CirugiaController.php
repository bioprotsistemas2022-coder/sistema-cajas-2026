<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCirugiaRequest;
use App\Http\Requests\Api\UpdateCirugiaRequest;
use App\Http\Requests\Api\VincularCxRequest;
use App\Http\Resources\CirugiaResource;
use App\Models\Caja;
use App\Models\Cirugia;
use App\Models\Consumo;
use App\Services\BoxStateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CirugiaController extends Controller
{
    /**
     * Lista las cirugías con filtros opcionales.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Cirugia::query()
            ->with(['cajas', 'tecnico']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('fecha')) {
            $query->whereDate('fecha_cx', $request->fecha);
        }

        if ($request->has('desde') && $request->has('hasta')) {
            $query->whereBetween('fecha_cx', [$request->desde, $request->hasta]);
        }

        if ($request->has('paciente')) {
            $query->where('paciente', 'like', "%{$request->paciente}%");
        }

        if ($request->has('medico')) {
            $query->where('medico', 'like', "%{$request->medico}%");
        }

        if ($request->has('caja_id')) {
            $query->whereHas('cajas', fn ($q) => $q->where('cajas.id', $request->caja_id));
        }

        return CirugiaResource::collection($query->orderByDesc('fecha_cx')->paginate($request->integer('per_page', 15)));
    }

    /**
     * Muestra una cirugía con sus relaciones.
     */
    public function show(Cirugia $cirugia): CirugiaResource
    {
        $cirugia->load(['cajas', 'consumos', 'tecnico']);

        return new CirugiaResource($cirugia);
    }

    /**
     * Crea una nueva cirugía y opcionalmente asocia cajas.
     */
    public function store(StoreCirugiaRequest $request): CirugiaResource
    {
        $cirugia = Cirugia::create($request->safe()->except('cajas'));

        if ($request->has('cajas')) {
            $this->validarCajasConsignadas($request->cajas);
            $cirugia->cajas()->sync($request->cajas);
        }

        return new CirugiaResource($cirugia->load(['cajas', 'tecnico']));
    }

    /**
     * Actualiza una cirugía.
     */
    public function update(UpdateCirugiaRequest $request, Cirugia $cirugia): CirugiaResource
    {
        $cirugia->update($request->safe()->except('cajas'));

        if ($request->has('cajas')) {
            $this->validarCajasConsignadas($request->cajas);
            $cirugia->cajas()->sync($request->cajas);
        }

        return new CirugiaResource($cirugia->fresh(['cajas', 'tecnico']));
    }

    /**
     * Elimina una cirugía.
     */
    public function destroy(Cirugia $cirugia): JsonResponse
    {
        $cirugia->delete();

        return response()->json(['message' => 'Cirugía eliminada correctamente.']);
    }

    /**
     * Asocia cajas a una cirugía (agrega o reemplaza según el parámetro sync).
     */
    public function attachCajas(Request $request, Cirugia $cirugia): CirugiaResource
    {
        $request->validate([
            'cajas' => ['required', 'array'],
            'cajas.*' => ['integer', 'exists:cajas,id'],
            'sync' => ['nullable', 'boolean'],
        ]);

        $this->validarCajasConsignadas($request->cajas);

        if ($request->boolean('sync')) {
            $cirugia->cajas()->sync($request->cajas);
        } else {
            $cirugia->cajas()->attach($request->cajas);
        }

        return new CirugiaResource($cirugia->fresh(['cajas', 'tecnico']));
    }

    /**
     * Desasocia una caja de una cirugía.
     */
    public function detachCaja(Request $request, Cirugia $cirugia, int $cajaId): CirugiaResource
    {
        $cirugia->cajas()->detach($cajaId);

        return new CirugiaResource($cirugia->fresh(['cajas', 'tecnico']));
    }

    /**
     * Vincula una CX de consignaciones: crea cirugía con todos los datos,
     * asocia cajas por codigo_interno y las pasa a PENDIENTE_DESPACHO.
     * Idempotente por external_nco_cod si se provee.
     */
    public function vincularCx(VincularCxRequest $request): JsonResponse
    {
        $data = $request->validated();

        $plcCod = (string) $data['plc_cod'];
        $externalNco = $data['external_nco_cod'] ?? null;

        if ($externalNco) {
            $existente = Cirugia::where('external_nco_cod', $externalNco)
                ->where('plc_cod', $plcCod)
                ->first();
            if ($existente) {
                $existente->load(['cajas', 'consumos', 'tecnico']);
                return response()->json([
                    'message' => 'Cirugía ya vinculada (idempotente).',
                    'data' => new CirugiaResource($existente),
                    'idempotente' => true,
                ]);
            }
        }

        $codigos = $data['cajas'];
        $cajas = Caja::whereIn('codigo_interno', $codigos)->get();
        $mapCodigo = $cajas->keyBy('codigo_interno');
        $faltantes = array_filter($codigos, fn ($c) => !isset($mapCodigo[$c]));
        if (count($faltantes) > 0) {
            throw ValidationException::withMessages([
                'cajas' => ['Cajas no encontradas por codigo_interno: ' . implode(', ', $faltantes)],
            ]);
        }

        $cajaIds = $cajas->pluck('id')->all();
        $this->validarCajasConsignadas($cajaIds);

        $observaciones = $data['observaciones'] ?? '';
        if (!empty($data['hospital'])) {
            $observaciones = trim($observaciones . "\nInstitución: " . $data['hospital']);
        }

        return DB::transaction(function () use ($data, $plcCod, $externalNco, $cajas, $cajaIds, $observaciones) {
            $cirugia = Cirugia::create([
                'plc_cod' => $plcCod,
                'external_nco_cod' => $externalNco,
                'paciente' => $data['paciente'],
                'medico' => $data['medico'],
                'fecha_cx' => $data['fecha_cx'],
                'observaciones' => $observaciones ?: null,
                'external_implantes' => $data['implantes'] ?? null,
                'status' => 'PENDIENTE',
            ]);

            $cirugia->cajas()->attach($cajaIds);

            if (!empty($data['implantes']) || !empty($data['detalles'])) {
                foreach ($cajas as $caja) {
                    Consumo::create([
                        'cirugia_id' => $cirugia->id,
                        'caja_id' => $caja->id,
                        'items' => $data['implantes'] ?? [],
                        'observaciones' => !empty($data['detalles']) ? json_encode($data['detalles']) : null,
                    ]);
                }
            }

            $userId = $data['user_id'] ?? auth()->id();
            foreach ($cajas as $caja) {
                BoxStateService::transition($caja->fresh(), 'PENDIENTE_DESPACHO', $userId, [
                    'responsable_nombre' => 'Consignaciones PlcCod ' . $plcCod,
                    'observaciones' => 'Vinculada a CX ' . $plcCod . ($externalNco ? " (NcoCod {$externalNco})" : ''),
                ]);
            }

            $cirugia->load(['cajas', 'consumos', 'tecnico']);

            return response()->json([
                'message' => 'CX vinculada correctamente. Cajas en Pend. Despacho.',
                'data' => new CirugiaResource($cirugia),
                'cajas_afectadas' => count($cajaIds),
            ], 201);
        });
    }

    /**
     * Regla de negocio: solo las cajas CONSIGNADAS pueden asignarse a una CX.
     *
     * @param  array<int>  $cajaIds
     */
    private function validarCajasConsignadas(array $cajaIds): void
    {
        $noConsignadas = Caja::whereIn('id', $cajaIds)
            ->where('estado', '!=', 'CONSIGNADA')
            ->get();

        if ($noConsignadas->isNotEmpty()) {
            throw ValidationException::withMessages([
                'cajas' => [
                    'Solo las cajas CONSIGNADAS pueden asignarse a una cirugía. No consignadas: ' .
                    $noConsignadas->map(fn ($c) => $c->codigo_interno . ' (' . $c->estadoLabel() . ')')->implode(', '),
                ],
            ]);
        }
    }
}