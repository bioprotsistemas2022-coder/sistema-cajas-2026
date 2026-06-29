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
        Schema::create('cirugias', function (Blueprint $table) {
            $table->id();
            $table->string('bioimplant_id')->nullable();
            $table->string('paciente');
            $table->string('medico');
            $table->date('fecha_cx');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->foreignId('tecnico_id')->nullable()->constrained('users');
            $table->string('access_token')->unique();
            $table->enum('status', ['PENDIENTE', 'EN_CURSO', 'COMPLETADA', 'CANCELADA'])->default('PENDIENTE');
            $table->timestamps();
        });

        // Many-to-many relationship between boxes and surgeries
        Schema::create('caja_cirugia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->onDelete('cascade');
            $table->foreignId('cirugia_id')->constrained('cirugias')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caja_cirugia');
        Schema::dropIfExists('cirugias');
    }
};
