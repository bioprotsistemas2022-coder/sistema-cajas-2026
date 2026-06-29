<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoCaja extends Model
{
    protected $fillable = [
        'caja_id',
        'user_id',
        'estado_anterior',
        'estado_nuevo',
        'observaciones',
        'fotos'
    ];

    protected $casts = [
        'fotos' => 'json'
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
