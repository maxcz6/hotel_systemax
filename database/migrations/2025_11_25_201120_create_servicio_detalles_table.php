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
        Schema::create('servicio_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas');
            $table->foreignId('servicio_id')->constrained('servicios');
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 8, 2);
            $table->decimal('total', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicio_detalles');
    }
};
