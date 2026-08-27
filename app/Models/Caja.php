<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $fillable = [
        'nombre',
        'codigo_interno',
        'estado',
        'consignatario_nombre',
        'fecha_consignacion',
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

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_caja');
    }

    /**
     * Etiqueta legible del estado para UI y API.
     * DISPONIBLE se muestra como "Sin Consignar".
     */
    public function estadoLabel(): string
    {
        return self::estadoLabels()[$this->estado] ?? $this->estado;
    }

    /**
     * Mapa de estados -> etiquetas legibles.
     */
    public static function estadoLabels(): array
    {
        return [
            'DISPONIBLE' => 'Sin Consignar',
            'CONSIGNADA' => 'Consignada',
            'PENDIENTE_DESPACHO' => 'Pend. Despacho',
            'EN ESTERILIZADORA' => 'En Esterilizadora',
            'EN CX' => 'En Cirugía',
            'CX FINALIZADA' => 'CX Finalizada',
            'EN TRANSITO VUELTA' => 'En Tránsito Vuelta',
            'PENDIENTE' => 'Pendiente',
            'ACONDICIONAMIENTO' => 'Acondicionamiento',
            'EN REPARACION' => 'En Reparación',
            'BAJA' => 'Baja',
        ];
    }

    /**
     * Clase CSS del badge según el estado.
     */
    public function estadoBadgeClass(): string
    {
        return [
            'DISPONIBLE' => 'badge-success',
            'CONSIGNADA' => 'badge-info',
            'PENDIENTE_DESPACHO' => 'badge-warning',
            'EN ESTERILIZADORA' => 'badge-primary',
            'EN CX' => 'badge-dark',
            'CX FINALIZADA' => 'badge-dark',
            'EN TRANSITO VUELTA' => 'badge-warning',
            'PENDIENTE' => 'badge-warning',
            'ACONDICIONAMIENTO' => 'badge-info',
            'EN REPARACION' => 'badge-danger',
            'BAJA' => 'badge-secondary',
        ][$this->estado] ?? 'badge-secondary';
    }

    /**
     * Regla de negocio: solo las cajas CONSIGNADAS pueden asignarse a una CX.
     */
    public function puedeIrACX(): bool
    {
        return $this->estado === 'CONSIGNADA';
    }

    public function puedeIrADespacho(): bool
    {
        return $this->estado === 'CONSIGNADA';
    }

    public function puedeIrAEsterilizadora(): bool
    {
        return $this->estado === 'PENDIENTE_DESPACHO';
    }
}
