<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function cajas()
    {
        return $this->belongsToMany(Caja::class, 'grupo_caja');
    }
}
