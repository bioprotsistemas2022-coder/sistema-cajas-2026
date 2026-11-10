<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaConsignacionItem extends Model
{
    protected $fillable = [
        'caja_consignacion_id',
        'seccion',
        'cantidad',
        'descripcion',
        'incluido',
    ];

    protected $casts = [
        'incluido' => 'boolean',
    ];

    public function consignacion()
    {
        return $this->belongsTo(CajaConsignacion::class, 'caja_consignacion_id');
    }
}
