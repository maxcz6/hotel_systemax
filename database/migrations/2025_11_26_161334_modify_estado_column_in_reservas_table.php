<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar la columna estado a VARCHAR(50) para aceptar todos los valores
        DB::statement('ALTER TABLE `reservas` MODIFY COLUMN `estado` VARCHAR(50) NOT NULL DEFAULT "pendiente"');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir al tipo anterior si es necesario
        DB::statement('ALTER TABLE `reservas` MODIFY COLUMN `estado` VARCHAR(255) NOT NULL DEFAULT "pendiente"');
    }
};
