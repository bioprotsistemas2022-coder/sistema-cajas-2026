<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consumo extends Model
{
    protected $fillable = [
        'cirugia_id',
        'caja_id',
        'items',
        'observaciones'
    ];

    protected $casts = [
        'items' => 'json'
    ];

    public function cirugia()
    {
        return $this->belongsTo(Cirugia::class);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
}
