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
        Schema::table('servicio_detalles', function (Blueprint $table) {
            $table->enum('estado', ['activo', 'anulado'])->default('activo')->after('total');
            $table->foreignId('anulado_por')->nullable()->after('estado')->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_anulacion')->nullable()->after('anulado_por');
            $table->text('motivo_anulacion')->nullable()->after('fecha_anulacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicio_detalles', function (Blueprint $table) {
            $table->dropForeign(['anulado_por']);
            $table->dropColumn(['estado', 'anulado_por', 'fecha_anulacion', 'motivo_anulacion']);
        });
    }
};
