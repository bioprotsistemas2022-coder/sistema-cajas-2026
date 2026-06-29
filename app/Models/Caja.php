<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $fillable = [
        'nombre',
        'codigo_interno',
        'estado',
        'pdf_path',
        'imagen_salida_path'
    ];

    public function cirugias()
    {
        return $this->belongsToMany(Cirugia::class);
    }

    public function eventos()
    {
        return $this->hasMany(EventoCaja::class);
    }

    public function consumos()
    {
        return $this->hasMany(Consumo::class);
    }

    public function imagenes()
    {
        return $this->hasMany(CajaImagen::class)->orderByDesc('created_at');
    }
}
