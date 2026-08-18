<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsumoResource extends JsonResource
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
            'cirugia_id' => $this->cirugia_id,
            'caja_id' => $this->caja_id,
            'items' => $this->items,
            'observaciones' => $this->observaciones,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cirugia' => new CirugiaResource($this->whenLoaded('cirugia')),
            'caja' => new CajaResource($this->whenLoaded('caja')),
        ];
    }
}