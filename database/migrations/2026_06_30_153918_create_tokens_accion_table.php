<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tokens_accion', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('accion');
            $table->foreignId('caja_id')->constrained('cajas');
            $table->string('responsable_nombre')->nullable();
            $table->json('params')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokens_accion');
    }
};
