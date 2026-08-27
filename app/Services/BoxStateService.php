<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\EventoCaja;
use Illuminate\Support\Facades\DB;

class BoxStateService
{
    public static function transition(Caja $caja, string $newEstado, ?int $userId, array $data = [])
    {
        return DB::transaction(function () use ($caja, $newEstado, $userId, $data) {
            $estadoAnterior = $caja->estado;

            $caja->update(['estado' => $newEstado]);

            EventoCaja::create([
                'caja_id' => $caja->id,
                'user_id' => $userId,
                'responsable_nombre' => $data['responsable_nombre'] ?? null,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $newEstado,
                'observaciones' => $data['observaciones'] ?? null,
                'fotos' => $data['fotos'] ?? null,
            ]);

            return $caja;
        });
    }

    public static function canTransition(Caja $caja, string $targetEstado): bool
    {
        $current = $caja->estado;

        $allowed = [
            'DISPONIBLE' => ['EN REPARACION', 'BAJA', 'CONSIGNADA'],
            'CONSIGNADA' => ['PENDIENTE_DESPACHO', 'DISPONIBLE', 'EN REPARACION', 'BAJA'],
            'PENDIENTE_DESPACHO' => ['EN ESTERILIZADORA', 'DISPONIBLE', 'EN REPARACION'],
            'EN ESTERILIZADORA' => ['EN CX', 'DISPONIBLE', 'EN REPARACION'],
            'EN CX' => ['CX FINALIZADA', 'EN REPARACION', 'DISPONIBLE'],
            'CX FINALIZADA' => ['EN TRANSITO VUELTA', 'EN REPARACION', 'DISPONIBLE'],
            'EN TRANSITO VUELTA' => ['PENDIENTE'],
            'PENDIENTE' => ['ACONDICIONAMIENTO', 'EN REPARACION'],
            'ACONDICIONAMIENTO' => ['DISPONIBLE'],
            'EN REPARACION' => ['DISPONIBLE', 'BAJA'],
            'BAJA' => []
        ];

        return in_array($targetEstado, $allowed[$current] ?? []);
    }
}
