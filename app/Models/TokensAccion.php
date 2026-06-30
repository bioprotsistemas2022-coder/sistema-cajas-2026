<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokensAccion extends Model
{
    protected $table = 'tokens_accion';

    protected $fillable = [
        'token',
        'accion',
        'caja_id',
        'responsable_nombre',
        'params',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'params' => 'json',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
}
