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
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('tipo_habitacion_id')->constrained('tipo_habitaciones');
            $table->decimal('precio_por_noche', 8, 2);
            $table->string('estado')->default('disponible'); // 'disponible', 'ocupada', 'limpieza', 'mantenimiento'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitaciones');
    }
};
