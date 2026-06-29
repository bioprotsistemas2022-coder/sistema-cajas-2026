<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cirugia extends Model
{
    protected $fillable = [
        'bioimplant_id',
        'paciente',
        'medico',
        'fecha_cx',
        'start_time',
        'end_time',
        'tecnico_id',
        'access_token',
        'status'
    ];

    protected $casts = [
        'fecha_cx' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->access_token) {
                $model->access_token = Str::random(32);
            }
        });
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function cajas()
    {
        return $this->belongsToMany(Caja::class);
    }

    public function consumos()
    {
        return $this->hasMany(Consumo::class);
    }
}
