<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evento_cajas', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('responsable_nombre')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('evento_cajas', function (Blueprint $table) {
            $table->dropColumn('responsable_nombre');
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
