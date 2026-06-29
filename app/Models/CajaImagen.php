<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaImagen extends Model
{
    protected $table = 'caja_imagenes';

    protected $fillable = ['caja_id', 'ruta', 'descripcion'];

    protected $casts = ['created_at' => 'datetime'];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
}
