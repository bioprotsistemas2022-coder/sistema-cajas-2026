<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo_interno')->unique();
            $table->enum('estado', [
                'DISPONIBLE',
                'EN ESTERILIZADORA',
                'EN CX',
                'EN TRANSITO',
                'PENDIENTE',
                'ACONDICIONAMIENTO',
                'EN REPARACION',
                'BAJA'
            ])->default('DISPONIBLE');
            $table->string('pdf_path')->nullable();
            $table->string('imagen_salida_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
