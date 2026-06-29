<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\EventoCaja;
use Illuminate\Support\Facades\DB;

class BoxStateService
{
    public static function transition(Caja $caja, string $newEstado, int $userId, array $data = [])
    {
        return DB::transaction(function () use ($caja, $newEstado, $userId, $data) {
            $estadoAnterior = $caja->estado;
            
            // Update box state
            $caja->update(['estado' => $newEstado]);

            // Log event
            EventoCaja::create([
                'caja_id' => $caja->id,
                'user_id' => $userId,
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
            'DISPONIBLE' => ['EN ESTERILIZADORA', 'EN REPARACION', 'BAJA'],
            'EN ESTERILIZADORA' => ['EN CX', 'DISPONIBLE', 'EN REPARACION'],
            'EN CX' => ['EN TRANSITO', 'EN REPARACION'],
            'EN TRANSITO' => ['PENDIENTE'],
            'PENDIENTE' => ['ACONDICIONAMIENTO', 'EN REPARACION'],
            'ACONDICIONAMIENTO' => ['DISPONIBLE'],
            'EN REPARACION' => ['DISPONIBLE', 'BAJA'],
            'BAJA' => []
        ];

        return in_array($targetEstado, $allowed[$current] ?? []);
    }
}
