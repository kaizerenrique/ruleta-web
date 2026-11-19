<?php

namespace Tests\Feature;

use App\Models\Premio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PremioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_rejects_probability_above_the_database_limit(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.premios.store'), [
                'nombre' => 'Premio fuera de rango',
                'color' => '#000000',
                'probabilidad' => 1000,
                'es_premio' => 1,
                'activo' => 1,
            ])
            ->assertSessionHasErrors('probabilidad');

        $this->assertDatabaseMissing('premios', ['nombre' => 'Premio fuera de rango']);
    }

    public function test_update_rejects_probability_above_the_database_limit(): void
    {
        $admin = $this->admin();
        $premio = Premio::create([
            'nombre' => 'Premio existente',
            'color' => '#000000',
            'probabilidad' => 10,
            'es_premio' => true,
            'activo' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.premios.update', $premio), [
                'nombre' => 'Premio existente',
                'color' => '#000000',
                'probabilidad' => 1000,
                'es_premio' => 1,
                'activo' => 1,
            ])
            ->assertSessionHasErrors('probabilidad');

        $this->assertDatabaseHas('premios', [
            'id' => $premio->id,
            'probabilidad' => 10,
        ]);
    }

    public function test_show_route_is_not_registered(): void
    {
        $admin = $this->admin();
        $premio = Premio::create([
            'nombre' => 'Premio existente',
            'color' => '#000000',
            'probabilidad' => 10,
            'es_premio' => true,
            'activo' => true,
        ]);

        $this->actingAs($admin)->get('/admin/premios/'.$premio->id)->assertStatus(405);
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

        return $admin;
    }
}
