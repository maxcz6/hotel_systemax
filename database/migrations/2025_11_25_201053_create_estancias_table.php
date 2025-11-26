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
        Schema::create('estancias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->unique()->constrained('reservas');
            $table->dateTime('check_in_real')->nullable();
            $table->dateTime('check_out_real')->nullable();
            $table->string('estado')->default('activa'); // 'activa', 'finalizada'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estancias');
    }
};
