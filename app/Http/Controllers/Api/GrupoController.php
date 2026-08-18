<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreGrupoRequest;
use App\Http\Requests\Api\UpdateGrupoRequest;
use App\Http\Resources\GrupoResource;
use App\Models\Grupo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GrupoController extends Controller
{
    /**
     * Lista los grupos con sus cajas.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Grupo::query()->with('cajas');

        if ($request->has('nombre')) {
            $query->where('nombre', 'like', "%{$request->nombre}%");
        }

        return GrupoResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    /**
     * Muestra un grupo.
     */
    public function show(Grupo $grupo): GrupoResource
    {
        return new GrupoResource($grupo->load('cajas'));
    }

    /**
     * Crea un grupo y opcionalmente asocia cajas.
     */
    public function store(StoreGrupoRequest $request): GrupoResource
    {
        $grupo = Grupo::create($request->safe()->except('cajas'));

        if ($request->has('cajas')) {
            $grupo->cajas()->sync($request->cajas);
        }

        return new GrupoResource($grupo->load('cajas'));
    }

    /**
     * Actualiza un grupo.
     */
    public function update(UpdateGrupoRequest $request, Grupo $grupo): GrupoResource
    {
        $grupo->update($request->safe()->except('cajas'));

        if ($request->has('cajas')) {
            $grupo->cajas()->sync($request->cajas);
        }

        return new GrupoResource($grupo->fresh('cajas'));
    }

    /**
     * Elimina un grupo.
     */
    public function destroy(Grupo $grupo): JsonResponse
    {
        $grupo->delete();

        return response()->json(['message' => 'Grupo eliminado correctamente.']);
    }

    /**
     * Asocia cajas a un grupo.
     */
    public function attachCajas(Request $request, Grupo $grupo): GrupoResource
    {
        $request->validate([
            'cajas' => ['required', 'array'],
            'cajas.*' => ['integer', 'exists:cajas,id'],
            'sync' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('sync')) {
            $grupo->cajas()->sync($request->cajas);
        } else {
            $grupo->cajas()->attach($request->cajas);
        }

        return new GrupoResource($grupo->fresh('cajas'));
    }

    /**
     * Desasocia una caja de un grupo.
     */
    public function detachCaja(Request $request, Grupo $grupo, int $cajaId): GrupoResource
    {
        $grupo->cajas()->detach($cajaId);

        return new GrupoResource($grupo->fresh('cajas'));
    }
}