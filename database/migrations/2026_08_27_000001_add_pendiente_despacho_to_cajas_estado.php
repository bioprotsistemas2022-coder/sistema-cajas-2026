<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cajas MODIFY COLUMN estado ENUM('DISPONIBLE','CONSIGNADA','PENDIENTE_DESPACHO','EN ESTERILIZADORA','EN CX','CX FINALIZADA','EN TRANSITO VUELTA','PENDIENTE','ACONDICIONAMIENTO','EN REPARACION','BAJA') NOT NULL DEFAULT 'DISPONIBLE'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('cajas')->where('estado', 'PENDIENTE_DESPACHO')->update(['estado' => 'CONSIGNADA']);
            DB::statement("ALTER TABLE cajas MODIFY COLUMN estado ENUM('DISPONIBLE','CONSIGNADA','EN ESTERILIZADORA','EN CX','CX FINALIZADA','EN TRANSITO VUELTA','PENDIENTE','ACONDICIONAMIENTO','EN REPARACION','BAJA') NOT NULL DEFAULT 'DISPONIBLE'");
        }
    }
};
