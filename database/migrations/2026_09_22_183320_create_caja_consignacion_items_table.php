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
        Schema::create('caja_consignacion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_consignacion_id')->constrained('caja_consignaciones')->onDelete('cascade');
            $table->string('seccion')->nullable();
            $table->integer('cantidad');
            $table->string('descripcion');
            $table->boolean('incluido')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caja_consignacion_items');
    }
};
