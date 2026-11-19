<?php

namespace Tests\Feature;

use App\Models\Premio;
use App\Models\Resultado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_saved_outcomes_after_a_prize_is_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

        $premioEntregado = Premio::create([
            'nombre' => 'Premio entregado',
            'color' => '#000000',
            'probabilidad' => 10,
            'es_premio' => true,
            'activo' => true,
        ]);
        $premioNoEntregado = Premio::create([
            'nombre' => 'Sin premio',
            'color' => '#000000',
            'probabilidad' => 90,
            'es_premio' => false,
            'activo' => true,
        ]);

        Resultado::create([
            'nick' => 'ganador',
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'premio_id' => $premioEntregado->id,
            'premio_nombre' => $premioEntregado->nombre,
            'es_premio' => true,
        ]);
        Resultado::create([
            'nick' => 'participante',
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'premio_id' => $premioNoEntregado->id,
            'premio_nombre' => $premioNoEntregado->nombre,
            'es_premio' => false,
        ]);
        Resultado::create([
            'nick' => 'historico',
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'premio_nombre' => 'Premio eliminado antes del registro',
        ]);

        $premioEntregado->delete();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('totalPremios', 1)
            ->assertViewHas('totalSinPremio', 1)
            ->assertViewHas('totalSinClasificar', 1)
            ->assertViewHas('distribucion', fn (array $distribucion): bool => $distribucion['data'] === [1, 1, 1])
            ->assertViewHas(
                'topPremios',
                fn ($premios): bool => $premios->pluck('premio_nombre')->all() === ['Premio entregado']
            );
    }
}
