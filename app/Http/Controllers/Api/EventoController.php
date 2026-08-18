<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventoCajaResource;
use App\Models\EventoCaja;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventoController extends Controller
{
    /**
     * Lista los eventos del historial de cajas.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = EventoCaja::query()
            ->with(['caja', 'user'])
            ->orderByDesc('created_at');

        if ($request->has('caja_id')) {
            $query->where('caja_id', $request->caja_id);
        }

        if ($request->has('estado_nuevo')) {
            $query->where('estado_nuevo', $request->estado_nuevo);
        }

        if ($request->has('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->has('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        return EventoCajaResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    /**
     * Muestra un evento del historial.
     */
    public function show(EventoCaja $evento): EventoCajaResource
    {
        return new EventoCajaResource($evento->load(['caja', 'user']));
    }
}