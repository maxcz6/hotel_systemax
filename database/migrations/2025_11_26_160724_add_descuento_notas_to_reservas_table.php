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
        Schema::table('reservas', function (Blueprint $table) {
            // Verificar si las columnas ya existen antes de agregarlas
            if (!Schema::hasColumn('reservas', 'descuento')) {
                $table->decimal('descuento', 5, 2)->default(0)->after('total_precio');
            }
            if (!Schema::hasColumn('reservas', 'notas')) {
                $table->text('notas')->nullable()->after('estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            if (Schema::hasColumn('reservas', 'descuento')) {
                $table->dropColumn('descuento');
            }
            if (Schema::hasColumn('reservas', 'notas')) {
                $table->dropColumn('notas');
            }
        });
    }
};
