<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cajas MODIFY COLUMN estado ENUM('DISPONIBLE','EN ESTERILIZADORA','EN CX','CX FINALIZADA','EN TRANSITO VUELTA','PENDIENTE','ACONDICIONAMIENTO','EN REPARACION','BAJA') NOT NULL DEFAULT 'DISPONIBLE'");
            DB::table('cajas')->where('estado', 'EN TRANSITO')->update(['estado' => 'EN TRANSITO VUELTA']);
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('cajas')->where('estado', 'EN TRANSITO VUELTA')->update(['estado' => 'EN TRANSITO']);
            DB::statement("ALTER TABLE cajas MODIFY COLUMN estado ENUM('DISPONIBLE','EN ESTERILIZADORA','EN CX','EN TRANSITO','PENDIENTE','ACONDICIONAMIENTO','EN REPARACION','BAJA') NOT NULL DEFAULT 'DISPONIBLE'");
        }
    }
};
