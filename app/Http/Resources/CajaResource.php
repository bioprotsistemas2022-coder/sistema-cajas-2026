<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CajaResource extends JsonResource
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
            'nombre' => $this->nombre,
            'codigo_interno' => $this->codigo_interno,
            'estado' => $this->estado,
            'estado_label' => $this->estadoLabel(),
            'consignatario_nombre' => $this->consignatario_nombre,
            'fecha_consignacion' => $this->fecha_consignacion,
            'pdf_path' => $this->pdf_path,
            'imagen_salida_path' => $this->imagen_salida_path,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cirugias' => CirugiaResource::collection($this->whenLoaded('cirugias')),
            'eventos' => EventoCajaResource::collection($this->whenLoaded('eventos')),
            'consumos' => ConsumoResource::collection($this->whenLoaded('consumos')),
            'imagenes' => $this->whenLoaded('imagenes', fn () => $this->imagenes->map(fn ($img) => [
                'id' => $img->id,
                'ruta' => $img->ruta,
                'descripcion' => $img->descripcion,
                'created_at' => $img->created_at,
            ])),
            'grupos' => GrupoResource::collection($this->whenLoaded('grupos')),
        ];
    }
}