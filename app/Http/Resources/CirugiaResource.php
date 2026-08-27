<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CirugiaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plc_cod' => $this->plc_cod,
            'bioimplant_id' => $this->bioimplant_id,
            'paciente' => $this->paciente,
            'medico' => $this->medico,
            'fecha_cx' => $this->fecha_cx?->toDateString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'tecnico_id' => $this->tecnico_id,
            'tecnico_nombre' => $this->tecnico_nombre,
            'tecnico_original_id' => $this->tecnico_original_id,
            'observaciones' => $this->observaciones,
            'external_nco_cod' => $this->external_nco_cod,
            'external_implantes' => $this->external_implantes,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cajas' => CajaResource::collection($this->whenLoaded('cajas')),
            'consumos' => ConsumoResource::collection($this->whenLoaded('consumos')),
            'tecnico' => $this->whenLoaded('tecnico', fn () => [
                'id' => $this->tecnico->id,
                'name' => $this->tecnico->name,
                'email' => $this->tecnico->email,
            ]),
        ];
    }
}