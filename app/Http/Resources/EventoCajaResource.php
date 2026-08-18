<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoCajaResource extends JsonResource
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
            'caja_id' => $this->caja_id,
            'user_id' => $this->user_id,
            'responsable_nombre' => $this->responsable_nombre,
            'estado_anterior' => $this->estado_anterior,
            'estado_nuevo' => $this->estado_nuevo,
            'observaciones' => $this->observaciones,
            'fotos' => $this->fotos,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'caja' => new CajaResource($this->whenLoaded('caja')),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
        ];
    }
}