<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreConsumoRequest;
use App\Http\Requests\Api\UpdateConsumoRequest;
use App\Http\Resources\ConsumoResource;
use App\Models\Consumo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ConsumoController extends Controller
{
    /**
     * Lista los consumos con filtros opcionales.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Consumo::query()
            ->with(['cirugia', 'caja']);

        if ($request->has('cirugia_id')) {
            $query->where('cirugia_id', $request->cirugia_id);
        }

        if ($request->has('caja_id')) {
            $query->where('caja_id', $request->caja_id);
        }

        return ConsumoResource::collection($query->orderByDesc('created_at')->paginate($request->integer('per_page', 15)));
    }

    /**
     * Muestra un consumo.
     */
    public function show(Consumo $consumo): ConsumoResource
    {
        $consumo->load(['cirugia', 'caja']);

        return new ConsumoResource($consumo);
    }

    /**
     * Registra un consumo de una caja en una cirugía.
     */
    public function store(StoreConsumoRequest $request): ConsumoResource
    {
        $consumo = Consumo::create($request->validated());

        return new ConsumoResource($consumo->load(['cirugia', 'caja']));
    }

    /**
     * Actualiza un consumo.
     */
    public function update(UpdateConsumoRequest $request, Consumo $consumo): ConsumoResource
    {
        $consumo->update($request->validated());

        return new ConsumoResource($consumo->fresh(['cirugia', 'caja']));
    }

    /**
     * Elimina un consumo.
     */
    public function destroy(Consumo $consumo): JsonResponse
    {
        $consumo->delete();

        return response()->json(['message' => 'Consumo eliminado correctamente.']);
    }
}