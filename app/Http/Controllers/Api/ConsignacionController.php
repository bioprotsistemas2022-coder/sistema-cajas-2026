<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreConsignacionRequest;
use App\Http\Resources\CajaResource;
use App\Models\Caja;
use App\Models\EventoCaja;
use App\Services\BoxStateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class ConsignacionController extends Controller
{
    /**
     * Lista las cajas en estado CONSIGNADA.
     * Útil para que el Módulo de Consignaciones vea qué cajas están
     * consignadas y puedan elegirse para una cirugía (CX).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Caja::query()
            ->where('estado', 'CONSIGNADA')
            ->with(['cirugias', 'grupos']);

        if ($request->has('consignatario_nombre')) {
            $query->where('consignatario_nombre', 'like', "%{$request->consignatario_nombre}%");
        }

        if ($request->has('codigo_interno')) {
            $query->where('codigo_interno', 'like', "%{$request->codigo_interno}%");
        }

        if ($request->has('disponible_para_cx')) {
            // Solo cajas que aún no están asignadas a una cirugía activa
            if ($request->boolean('disponible_para_cx')) {
                $query->whereDoesntHave('cirugias', fn ($q) => $q->whereIn('status', ['PENDIENTE', 'EN_CURSO']));
            }
        }

        return CajaResource::collection($query->orderBy('fecha_consignacion', 'desc')->paginate($request->integer('per_page', 15)));
    }

    /**
     * Muestra una caja consignada.
     */
    public function show(Caja $caja): CajaResource
    {
        if ($caja->estado !== 'CONSIGNADA') {
            throw ValidationException::withMessages([
                'caja' => ['La caja no se encuentra en estado CONSIGNADA.'],
            ]);
        }

        $caja->load(['cirugias', 'eventos', 'consumos', 'imagenes', 'grupos']);

        return new CajaResource($caja);
    }

    /**
     * Marca una caja como CONSIGNADA para el Módulo de Consignaciones.
     * Acepta caja_id o codigo_interno.
     */
    public function store(StoreConsignacionRequest $request): CajaResource
    {
        $caja = $request->filled('caja_id')
            ? Caja::findOrFail($request->caja_id)
            : Caja::where('codigo_interno', $request->codigo_interno)->firstOrFail();

        if ($caja->estado === 'CONSIGNADA') {
            throw ValidationException::withMessages([
                'caja' => ['La caja ya se encuentra en estado CONSIGNADA.'],
            ]);
        }

        if (! BoxStateService::canTransition($caja, 'CONSIGNADA')) {
            throw ValidationException::withMessages([
                'caja' => ['No se puede consignar una caja en estado ' . $caja->estado . '. Solo se consignan cajas DISPONIBLES.'],
            ]);
        }

        $fecha = $request->fecha_consignacion ?? now();

        $caja = BoxStateService::transition($caja, 'CONSIGNADA', $request->user()->id, [
            'responsable_nombre' => $request->user()->name,
            'observaciones' => $request->observaciones,
        ]);

        $caja->update([
            'consignatario_nombre' => $request->consignatario_nombre ?: 'Módulo Consignaciones',
            'fecha_consignacion' => $fecha,
        ]);

        return new CajaResource($caja->fresh(['cirugias', 'eventos', 'grupos']));
    }

    /**
     * Devuelve una caja consignada al estado DISPONIBLE.
     */
    public function devolver(Request $request, Caja $caja): CajaResource
    {
        if ($caja->estado !== 'CONSIGNADA') {
            throw ValidationException::withMessages([
                'caja' => ['La caja no se encuentra en estado CONSIGNADA.'],
            ]);
        }

        $request->validate([
            'observaciones' => ['nullable', 'string'],
        ]);

        $caja = BoxStateService::transition($caja, 'DISPONIBLE', $request->user()->id, [
            'responsable_nombre' => $request->user()->name,
            'observaciones' => $request->observaciones,
        ]);

        $caja->update([
            'consignatario_nombre' => null,
            'fecha_consignacion' => null,
        ]);

        return new CajaResource($caja->fresh(['cirugias', 'eventos', 'grupos']));
    }

    /**
     * Estadísticas rápidas de consignaciones.
     */
    public function stats(Request $request): JsonResponse
    {
        return response()->json([
            'total_consignadas' => Caja::where('estado', 'CONSIGNADA')->count(),
            'por_consignatario' => Caja::where('estado', 'CONSIGNADA')
                ->selectRaw('consignatario_nombre, count(*) as total')
                ->groupBy('consignatario_nombre')
                ->get(),
        ]);
    }
}