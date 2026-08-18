<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCajaRequest;
use App\Http\Requests\Api\UpdateCajaRequest;
use App\Http\Resources\CajaResource;
use App\Models\Caja;
use App\Models\EventoCaja;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CajaController extends Controller
{
    /**
     * Lista las cajas con filtros opcionales.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Caja::query()
            ->with(['cirugias', 'grupos']);

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('codigo_interno')) {
            $query->where('codigo_interno', 'like', "%{$request->codigo_interno}%");
        }

        if ($request->has('nombre')) {
            $query->where('nombre', 'like', "%{$request->nombre}%");
        }

        if ($request->has('grupo_id')) {
            $query->whereHas('grupos', fn ($q) => $q->where('grupos.id', $request->grupo_id));
        }

        return CajaResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    /**
     * Muestra una caja con sus relaciones.
     */
    public function show(Caja $caja): CajaResource
    {
        $caja->load(['cirugias', 'eventos', 'consumos', 'imagenes', 'grupos']);

        return new CajaResource($caja);
    }

    /**
     * Crea una nueva caja.
     */
    public function store(StoreCajaRequest $request): CajaResource
    {
        $caja = Caja::create($request->validated());

        return new CajaResource($caja);
    }

    /**
     * Actualiza una caja.
     */
    public function update(UpdateCajaRequest $request, Caja $caja): CajaResource
    {
        $caja->update($request->validated());

        return new CajaResource($caja->fresh());
    }

    /**
     * Elimina una caja.
     */
    public function destroy(Caja $caja): JsonResponse
    {
        $caja->delete();

        return response()->json(['message' => 'Caja eliminada correctamente.']);
    }

    /**
     * Cambia el estado de una caja y registra el evento en el historial.
     */
    public function changeState(Request $request, Caja $caja): CajaResource
    {
        $request->validate([
            'estado' => ['required', Rule::in([
                'DISPONIBLE', 'EN ESTERILIZADORA', 'EN CX', 'EN TRANSITO',
                'PENDIENTE', 'ACONDICIONAMIENTO', 'EN REPARACION', 'BAJA', 'CONSIGNADA',
            ])],
            'observaciones' => ['nullable', 'string'],
        ]);

        $estadoAnterior = $caja->estado;
        $estadoNuevo = $request->estado;

        if ($estadoAnterior === $estadoNuevo) {
            throw ValidationException::withMessages([
                'estado' => ['La caja ya se encuentra en el estado ' . $estadoNuevo . '.'],
            ]);
        }

        $caja->update(['estado' => $estadoNuevo]);

        EventoCaja::create([
            'caja_id' => $caja->id,
            'user_id' => $request->user()->id,
            'responsable_nombre' => $request->user()->name,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'observaciones' => $request->observaciones,
        ]);

        return new CajaResource($caja->fresh(['eventos']));
    }
}