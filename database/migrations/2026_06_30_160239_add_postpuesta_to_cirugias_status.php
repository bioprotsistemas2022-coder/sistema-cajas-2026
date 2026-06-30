<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE cirugias MODIFY COLUMN status ENUM('PENDIENTE','EN_CURSO','COMPLETADA','CANCELADA','POSTPUESTA') NOT NULL DEFAULT 'PENDIENTE'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE cirugias MODIFY COLUMN status ENUM('PENDIENTE','EN_CURSO','COMPLETADA','CANCELADA') NOT NULL DEFAULT 'PENDIENTE'");
    }
};
