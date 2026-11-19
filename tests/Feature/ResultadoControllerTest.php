<?php

namespace Tests\Feature;

use App\Models\Resultado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ResultadoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_export_neutralizes_spreadsheet_formulas_in_user_data(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

        Resultado::create([
            'nick' => 'usuario_demo',
            'nombre' => '=1+1',
            'apellido' => '@SUM(1,1)',
            'ip' => '127.0.0.1',
            'fecha' => '2026-01-01 00:00:00',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.resultados.export'));

        $response->assertOk()->assertStreamed();

        $this->assertStringContainsString(
            "usuario_demo,'=1+1,\"'@SUM(1,1)\"",
            $response->streamedContent()
        );
    }

    public function test_csv_export_applies_date_filters_and_streams_only_matching_results(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

        Resultado::create([
            'nick' => 'registro_fuera_de_rango',
            'nombre' => 'Fuera',
            'apellido' => 'Rango',
            'fecha' => '2026-03-09 23:59:59',
        ]);
        Resultado::create([
            'nick' => 'registro_en_rango',
            'nombre' => 'Dentro',
            'apellido' => 'Rango',
            'fecha' => '2026-03-10 12:00:00',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.resultados.export', [
            'desde' => '2026-03-10',
            'hasta' => '2026-03-10',
        ]));

        $response->assertOk()->assertStreamed();

        $content = $response->streamedContent();
        $this->assertStringContainsString('registro_en_rango', $content);
        $this->assertStringNotContainsString('registro_fuera_de_rango', $content);
    }

    public function test_spin_limit_queries_have_composite_indexes(): void
    {
        $this->assertTrue(Schema::hasIndex('resultados', ['nick', 'fecha']));
        $this->assertTrue(Schema::hasIndex('resultados', ['ip', 'fecha']));
    }
}
