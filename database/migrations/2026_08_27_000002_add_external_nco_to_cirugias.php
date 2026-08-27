<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cirugias', function (Blueprint $table) {
            $table->bigInteger('external_nco_cod')->nullable()->after('plc_cod');
            $table->json('external_implantes')->nullable()->after('observaciones');
            $table->index('external_nco_cod');
            $table->index('plc_cod');
        });
    }

    public function down(): void
    {
        Schema::table('cirugias', function (Blueprint $table) {
            $table->dropIndex(['external_nco_cod']);
            $table->dropIndex(['plc_cod']);
            $table->dropColumn(['external_nco_cod', 'external_implantes']);
        });
    }
};
