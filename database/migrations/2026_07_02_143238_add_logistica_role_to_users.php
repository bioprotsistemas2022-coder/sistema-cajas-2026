<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','deposito','tecnico','consumo','acondicionador','logistica') NOT NULL DEFAULT 'deposito'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('users')->where('role', 'logistica')->update(['role' => 'deposito']);
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','deposito','tecnico','consumo','acondicionador') NOT NULL DEFAULT 'deposito'");
        }
    }
};
