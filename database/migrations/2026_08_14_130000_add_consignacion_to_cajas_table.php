<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega el estado CONSIGNADA al enum de cajas y columnas
     * para registrar la consignación (cliente y fecha).
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cajas MODIFY COLUMN estado ENUM('DISPONIBLE','EN ESTERILIZADORA','EN CX','CX FINALIZADA','EN TRANSITO VUELTA','PENDIENTE','ACONDICIONAMIENTO','EN REPARACION','BAJA','CONSIGNADA') NOT NULL DEFAULT 'DISPONIBLE'");
        }

        Schema::table('cajas', function (Blueprint $table) {
            $table->string('consignatario_nombre')->nullable()->after('estado');
            $table->timestamp('fecha_consignacion')->nullable()->after('consignatario_nombre');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn(['consignatario_nombre', 'fecha_consignacion']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cajas MODIFY COLUMN estado ENUM('DISPONIBLE','EN ESTERILIZADORA','EN CX','CX FINALIZADA','EN TRANSITO VUELTA','PENDIENTE','ACONDICIONAMIENTO','EN REPARACION','BAJA') NOT NULL DEFAULT 'DISPONIBLE'");
        }
    }
};