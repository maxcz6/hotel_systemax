<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserva;
use App\Models\Estancia;
use Carbon\Carbon;

class FixReservasSinEstancia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservas:fix-estancias {--dry-run : Solo mostrar qué se haría sin ejecutar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Arregla reservas con check-in que no tienen estancia creada';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Buscando reservas con check-in sin estancia...');
        $this->newLine();

        // Buscar reservas con estado 'checkin' que no tienen estancia
        $reservasSinEstancia = Reserva::where('estado', 'checkin')
            ->whereDoesntHave('estancia')
            ->with(['cliente', 'habitacion'])
            ->get();

        if ($reservasSinEstancia->isEmpty()) {
            $this->info('✅ ¡Perfecto! Todas las reservas con check-in tienen su estancia.');
            return Command::SUCCESS;
        }

        $this->warn("⚠️  Se encontraron {$reservasSinEstancia->count()} reserva(s) con problemas:");
        $this->newLine();

        // Mostrar tabla con las reservas problemáticas
        $tableData = [];
        foreach ($reservasSinEstancia as $reserva) {
            $tableData[] = [
                'ID' => $reserva->id,
                'Cliente' => $reserva->cliente->nombre . ' ' . $reserva->cliente->apellido,
                'Habitación' => '#' . $reserva->habitacion->numero,
                'Fecha Entrada' => $reserva->fecha_entrada->format('d/m/Y'),
                'Estado' => $reserva->estado,
            ];
        }

        $this->table(
            ['ID', 'Cliente', 'Habitación', 'Fecha Entrada', 'Estado'],
            $tableData
        );

        if ($this->option('dry-run')) {
            $this->info('🔍 Modo DRY-RUN: No se realizarán cambios.');
            $this->info('📝 Ejecuta sin --dry-run para aplicar los cambios.');
            return Command::SUCCESS;
        }

        // Confirmar antes de proceder
        if (!$this->confirm('¿Deseas crear las estancias faltantes para estas reservas?', true)) {
            $this->warn('❌ Operación cancelada.');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->info('✨ Creando estancias...');
        $this->newLine();

        $creadas = 0;
        $errores = 0;

        foreach ($reservasSinEstancia as $reserva) {
            try {
                Estancia::create([
                    'reserva_id' => $reserva->id,
                    'check_in_real' => $reserva->fecha_entrada,
                    'estado' => 'activa',
                ]);

                $this->line("✅ Estancia creada para Reserva #{$reserva->id} - {$reserva->cliente->nombre}");
                $creadas++;
            } catch (\Exception $e) {
                $this->error("❌ Error en Reserva #{$reserva->id}: " . $e->getMessage());
                $errores++;
            }
        }

        $this->newLine();
        $this->info("📊 Resumen:");
        $this->info("   ✅ Estancias creadas: {$creadas}");
        if ($errores > 0) {
            $this->warn("   ❌ Errores: {$errores}");
        }

        $this->newLine();
        $this->info('🎉 ¡Proceso completado!');

        return Command::SUCCESS;
    }
}
