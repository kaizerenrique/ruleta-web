<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Premio;

class PremiosDemoSeeder extends Seeder
{
    /**
     * Siembra 8 premios de demostración:
     *   - 2 ganadores (3% y 7% → 10% total de victoria)
     *   - 6 perdedores (15% cada uno → 90% total)
     *
     * Los nombres de los perdedores están redactados para encajar con
     * el mensaje del componente: "😔 ¡CASI, {nick}! {nombre}".
     */
    public function run(): void
    {
        if ($this->command && app()->environment('production')) {
            if (! $this->command->confirm(
                '⚠️  Estás en PRODUCCIÓN. ¿Borrar los premios actuales y reemplazarlos por los de demo?',
                false
            )) {
                $this->command->warn('Seeder cancelado.');
                return;
            }
        }

        // Borra los premios existentes. Gracias al FK `onDelete('set null')`,
        // los resultados históricos conservan `premio_nombre` y solo pierden
        // la relación con `premio_id`.
        Premio::query()->delete();

        $premios = [
            // 0 — ✅ GANADOR principal (opuesto al segundo ganador en la rueda)
            [
                'nombre'       => 'PREMIO MAYOR',
                'color'        => '#FFD700',
                'probabilidad' => 3,
                'es_premio'    => true,
                'activo'       => true,
            ],
            // 1 — ❌ Derrota
            [
                'nombre'       => 'Sigue Intentando',
                'color'        => '#DC2626',
                'probabilidad' => 15,
                'es_premio'    => false,
                'activo'       => true,
            ],
            // 2 — ❌ Derrota
            [
                'nombre'       => 'Casi lo Logras',
                'color'        => '#059669',
                'probabilidad' => 15,
                'es_premio'    => false,
                'activo'       => true,
            ],
            // 3 — ✅ GANADOR secundario
            [
                'nombre'       => 'BONO ESPECIAL',
                'color'        => '#A855F7',
                'probabilidad' => 7,
                'es_premio'    => true,
                'activo'       => true,
            ],
            // 4 — ❌ Derrota
            [
                'nombre'       => 'Suerte la Próxima',
                'color'        => '#EA580C',
                'probabilidad' => 15,
                'es_premio'    => false,
                'activo'       => true,
            ],
            // 5 — ❌ Derrota
            [
                'nombre'       => 'No Fue Esta Vez',
                'color'        => '#2563EB',
                'probabilidad' => 15,
                'es_premio'    => false,
                'activo'       => true,
            ],
            // 6 — ❌ Derrota
            [
                'nombre'       => 'Intenta de Nuevo',
                'color'        => '#DB2777',
                'probabilidad' => 15,
                'es_premio'    => false,
                'activo'       => true,
            ],
            // 7 — ❌ Derrota
            [
                'nombre'       => 'Sin Premio',
                'color'        => '#6B7280',
                'probabilidad' => 15,
                'es_premio'    => false,
                'activo'       => true,
            ],
        ];

        foreach ($premios as $premio) {
            Premio::create($premio);
        }

        $totalPeso         = collect($premios)->sum('probabilidad');
        $totalGanadores    = collect($premios)->where('es_premio', true)->count();
        $totalPerdedores   = count($premios) - $totalGanadores;
        $porcentajeVictoria = collect($premios)->where('es_premio', true)->sum('probabilidad');

        $this->command?->info('✅ Se crearon ' . count($premios) . ' premios de demo:');
        $this->command?->info("   🎁 Ganadores:  {$totalGanadores} ({$porcentajeVictoria}% de probabilidad)");
        $this->command?->info("   ❌ Perdedores: {$totalPerdedores} (" . (100 - $porcentajeVictoria) . '%)');
        $this->command?->info("   ⚖️  Peso total: {$totalPeso}");
    }
}
