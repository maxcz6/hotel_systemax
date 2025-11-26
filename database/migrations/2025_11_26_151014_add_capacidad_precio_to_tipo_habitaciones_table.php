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
        Schema::table('tipo_habitaciones', function (Blueprint $table) {
            $table->integer('capacidad')->default(2)->after('descripcion');
            $table->decimal('precio_por_noche', 10, 2)->default(0)->after('capacidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipo_habitaciones', function (Blueprint $table) {
            $table->dropColumn(['capacidad', 'precio_por_noche']);
        });
    }
};
