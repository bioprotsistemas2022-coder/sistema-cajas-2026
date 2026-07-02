<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cirugias', function (Blueprint $table) {
            $table->text('observaciones')->nullable()->after('plc_cod');
        });
    }

    public function down()
    {
        Schema::table('cirugias', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });
    }
};
