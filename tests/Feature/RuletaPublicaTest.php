<?php

namespace Tests\Feature;

use App\Livewire\RuletaPublica;
use App\Models\Aplicacion;
use App\Models\Premio;
use App\Models\RuletaConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RuletaPublicaTest extends TestCase
{
    use RefreshDatabase;

    public function test_spin_limit_is_checked_before_another_result_is_created(): void
    {
        RuletaConfig::query()->firstOrFail()->update([
            'activa' => true,
            'captcha_activo' => false,
            'max_giros_por_usuario' => 1,
            'tipo_limite' => 'ip',
            'periodo_limite' => 'dia',
        ]);

        $aplicacion = Aplicacion::create(['nombre' => 'Web']);
        Premio::create([
            'nombre' => 'Premio',
            'color' => '#000000',
            'probabilidad' => 100,
            'es_premio' => true,
            'activo' => true,
        ]);

        Livewire::test(RuletaPublica::class)
            ->set('nick', 'primer_usuario')
            ->set('nombre', 'Nombre')
            ->set('apellido', 'Apellido')
            ->set('aplicacion_id', $aplicacion->id)
            ->call('girar')
            ->assertSet('girando', true)
            ->assertDispatched('spin-to');

        Livewire::test(RuletaPublica::class)
            ->set('nick', 'segundo_usuario')
            ->set('nombre', 'Nombre')
            ->set('apellido', 'Apellido')
            ->set('aplicacion_id', $aplicacion->id)
            ->call('girar')
            ->assertSet('girando', false)
            ->assertSet(
                'mensajeError',
                'Gracias por participar. Completaste el Giro de hoy. ¡Mantente atento a nuestras próximas oportunidades! 🍀'
            );

        $this->assertDatabaseCount('resultados', 1);
        $this->assertDatabaseHas('resultados', ['nick' => 'primer_usuario']);
    }

    public function test_nick_limit_is_shared_across_different_ip_addresses(): void
    {
        RuletaConfig::query()->firstOrFail()->update([
            'activa' => true,
            'captcha_activo' => false,
            'max_giros_por_usuario' => 1,
            'tipo_limite' => 'nick',
            'periodo_limite' => 'dia',
        ]);

        $aplicacion = Aplicacion::create(['nombre' => 'Web']);
        Premio::create([
            'nombre' => 'Premio',
            'color' => '#000000',
            'probabilidad' => 100,
            'es_premio' => true,
            'activo' => true,
        ]);

        Livewire::test(RuletaPublica::class)
            ->set('nick', 'usuario_unico')
            ->set('nombre', 'Nombre')
            ->set('apellido', 'Apellido')
            ->set('aplicacion_id', $aplicacion->id)
            ->call('girar')
            ->assertSet('girando', true);

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10']);

        Livewire::test(RuletaPublica::class)
            ->set('nick', 'usuario_unico')
            ->set('nombre', 'Nombre')
            ->set('apellido', 'Apellido')
            ->set('aplicacion_id', $aplicacion->id)
            ->call('girar')
            ->assertSet('girando', false)
            ->assertSet(
                'mensajeError',
                'Gracias por participar. Completaste el Giro de hoy. ¡Mantente atento a nuestras próximas oportunidades! 🍀'
            );

        $this->assertDatabaseCount('resultados', 1);
    }

    public function test_spin_shows_an_error_when_active_prizes_have_zero_total_weight(): void
    {
        RuletaConfig::query()->firstOrFail()->update([
            'activa' => true,
            'captcha_activo' => false,
        ]);

        $aplicacion = Aplicacion::create(['nombre' => 'Web']);
        Premio::create([
            'nombre' => 'Premio sin probabilidad',
            'color' => '#000000',
            'probabilidad' => 0,
            'es_premio' => true,
            'activo' => true,
        ]);

        Livewire::test(RuletaPublica::class)
            ->set('nick', 'usuario_sin_premio')
            ->set('nombre', 'Nombre')
            ->set('apellido', 'Apellido')
            ->set('aplicacion_id', $aplicacion->id)
            ->call('girar')
            ->assertSet('girando', false)
            ->assertSet('mensajeError', 'No hay premios configurados.');

        $this->assertDatabaseCount('resultados', 0);
    }

    public function test_spin_rejects_an_inactive_application(): void
    {
        RuletaConfig::query()->firstOrFail()->update([
            'activa' => true,
            'captcha_activo' => false,
        ]);

        $aplicacion = Aplicacion::create([
            'nombre' => 'Aplicación inactiva',
            'activo' => false,
        ]);
        Premio::create([
            'nombre' => 'Premio',
            'color' => '#000000',
            'probabilidad' => 100,
            'es_premio' => true,
            'activo' => true,
        ]);

        Livewire::test(RuletaPublica::class)
            ->set('nick', 'usuario_inactivo')
            ->set('nombre', 'Nombre')
            ->set('apellido', 'Apellido')
            ->set('aplicacion_id', $aplicacion->id)
            ->call('girar')
            ->assertHasErrors(['aplicacion_id'])
            ->assertSet('girando', false)
            ->assertSet('mensajeError', '');

        $this->assertDatabaseCount('resultados', 0);
    }

    public function test_public_form_displays_the_updated_copy(): void
    {
        Livewire::test(RuletaPublica::class)
            ->assertSee('LA RULETA ESTÁ GIRANDO...')
            ->assertSee('Probando tu suerte - Exclusivo en VEVPOKER.COM')
            ->assertSee('¿En qué app prefieres el premio?');
    }

    public function test_prize_and_no_prize_states_display_the_updated_copy(): void
    {
        $premioGanado = Premio::create([
            'nombre' => 'Premio ganado',
            'color' => '#000000',
            'probabilidad' => 100,
            'es_premio' => true,
            'activo' => true,
        ]);
        $premioSinPremio = Premio::create([
            'nombre' => 'Sin premio',
            'color' => '#000000',
            'probabilidad' => 0,
            'es_premio' => false,
            'activo' => true,
        ]);

        Livewire::test(RuletaPublica::class)
            ->set('premioGanador', $premioGanado)
            ->set('mostrarResultado', true)
            ->assertSee('📸 Presenta esta pantalla al organizador para recibir tu premio en VEVPOKER')
            ->assertSee('*Aplican términos y condiciones. Consulta con tu agente.');

        Livewire::test(RuletaPublica::class)
            ->set('premioGanador', $premioSinPremio)
            ->set('mostrarResultado', true)
            ->assertSee('¡Gracias por participar y ser parte de VEVPOKER!');
    }

    public function test_inactive_screen_displays_the_updated_copy(): void
    {
        RuletaConfig::query()->firstOrFail()->update(['activa' => false]);

        Livewire::test(RuletaPublica::class)
            ->assertSee('🎁 Próximamente habrá nuevas oportunidades')
            ->assertSee('Gracias por ser parte de VEVPoker. No te pierdas nuestras promociones y días de recompensas. Consulta con tu agente.');
    }
}
