<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('servicio_detalles', function (Blueprint $table) {
            // 1. Agregar nueva columna id_estancia
            $table->foreignId('id_estancia')->nullable()->after('id')->constrained('estancias')->onDelete('cascade');
            
            // 2. Renombrar servicio_id a id_servicio
            $table->renameColumn('servicio_id', 'id_servicio');
            
            // 3. Renombrar total a subtotal
            $table->renameColumn('total', 'subtotal');
        });

        // 4. Migrar datos: Asignar id_estancia basado en reserva_id
        // Esto asume que ya existen estancias para las reservas (lo cual arreglamos antes)
        $servicios = \DB::table('servicio_detalles')->get();
        foreach ($servicios as $servicio) {
            $estancia = \DB::table('estancias')->where('reserva_id', $servicio->reserva_id)->first();
            if ($estancia) {
                \DB::table('servicio_detalles')
                    ->where('id', $servicio->id)
                    ->update(['id_estancia' => $estancia->id]);
            }
        }

        Schema::table('servicio_detalles', function (Blueprint $table) {
            // 5. Eliminar la columna antigua reserva_id
            $table->dropForeign(['reserva_id']);
            $table->dropColumn('reserva_id');
            
            // 6. Hacer id_estancia no nullable (opcional, si estamos seguros que todos tienen estancia)
            // $table->unsignedBigInteger('id_estancia')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicio_detalles', function (Blueprint $table) {
            $table->foreignId('reserva_id')->nullable()->constrained('reservas');
            $table->renameColumn('id_servicio', 'servicio_id');
            $table->renameColumn('subtotal', 'total');
            $table->dropForeign(['id_estancia']);
            $table->dropColumn('id_estancia');
        });
    }
};
