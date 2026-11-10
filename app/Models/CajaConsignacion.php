<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaConsignacion extends Model
{
    // Eloquent pluraliza "consignacion" mal (asume inglés) -> forzar el nombre real.
    protected $table = 'caja_consignaciones';

    protected $fillable = [
        'caja_id',
        'user_id',
        'consignatario_nombre',
        'fecha_consignacion',
        'observaciones',
        'pdf_path',
    ];

    protected $casts = [
        'fecha_consignacion' => 'date',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CajaConsignacionItem::class);
    }
}
