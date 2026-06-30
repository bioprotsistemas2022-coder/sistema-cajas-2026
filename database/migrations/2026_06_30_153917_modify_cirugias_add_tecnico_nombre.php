<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cirugias', function (Blueprint $table) {
            $table->foreignId('tecnico_id')->nullable()->change();
            $table->string('tecnico_nombre')->nullable()->after('tecnico_id');
            $table->foreignId('tecnico_original_id')->nullable()->constrained('users')->after('tecnico_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('cirugias', function (Blueprint $table) {
            $table->dropColumn('tecnico_nombre');
            $table->dropColumn('tecnico_original_id');
            $table->foreignId('tecnico_id')->nullable(false)->change();
        });
    }
};
