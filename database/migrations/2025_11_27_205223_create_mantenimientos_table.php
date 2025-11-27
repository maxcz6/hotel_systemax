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
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habitacion_id')->constrained('habitaciones')->onDelete('cascade');
            $table->string('tipo')->comment('preventivo, correctivo, urgente');
            $table->text('descripcion');
            $table->string('estado')->default('pendiente')->comment('pendiente, en_progreso, completado');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->foreignId('asignado_a')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notas_tecnicas')->nullable();
            $table->decimal('costo', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
