<?php

namespace App\Livewire;

use App\Models\Aplicacion;
use App\Models\Premio;
use App\Models\Resultado;
use App\Models\RuletaConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class RuletaPublica extends Component
{
    public $nick = '';

    public $nombre = '';

    public $apellido = '';

    public $aplicacion_id = ''; // ✅ NUEVO

    public $captcha = '';

    public $mostrarModal = true;

    public $mensajeError = '';

    public $premioGanador = null;

    public $girando = false;

    public $mostrarResultado = false;

    public $ruletaActiva = true;

    protected function rules(): array
    {
        $config = RuletaConfig::first();

        $rules = [
            'nick' => 'required|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'aplicacion_id' => ['required', Rule::exists('aplicacions', 'id')->where('activo', true)],
        ];

        if ($config?->captcha_activo) {
            $rules['captcha'] = 'required|captcha';
        }

        return $rules;
    }

    protected $messages = [
        'aplicacion_id.required' => 'Debes seleccionar una aplicación.',
        'aplicacion_id.exists' => 'La aplicación seleccionada no es válida o está inactiva.',
    ];

    public function mount()
    {
        $config = RuletaConfig::first();
        $this->ruletaActiva = $config?->activa ?? true;
        if (! $this->ruletaActiva) {
            $this->mostrarModal = false;
        }
    }

    public function girar(): void
    {
        $this->validate();

        $ip = request()->ip();

        $premioSeleccionado = DB::transaction(function () use ($ip): ?Premio {
            $config = RuletaConfig::query()->lockForUpdate()->first();

            if (! $config?->activa) {
                $this->ruletaActiva = false;
                $this->mostrarModal = false;

                return null;
            }

            $maxGiros = $config->max_giros_por_usuario ?? 1;
            $tipoLimite = $config->tipo_limite ?? 'ip';
            $periodo = $config->periodo_limite ?? 'dia';

            $desde = match ($periodo) {
                'dia' => now()->subDay(),
                'semana' => now()->subWeek(),
                'mes' => now()->subMonth(),
                default => null,
            };

            $queryIp = Resultado::where('ip', $ip);
            $queryNick = Resultado::where('nick', $this->nick);

            if ($desde) {
                $queryIp->where('fecha', '>=', $desde);
                $queryNick->where('fecha', '>=', $desde);
            }

            $girosPorIp = $queryIp->count();
            $girosPorNick = $queryNick->count();

            $razones = [];

            if (in_array($tipoLimite, ['ip', 'ambos']) && $girosPorIp >= $maxGiros) {
                $razones[] = 'esta conexión (IP)';
            }
            if (in_array($tipoLimite, ['nick', 'ambos']) && $girosPorNick >= $maxGiros) {
                $razones[] = 'este nick';
            }

            if ($razones !== []) {
                $periodoTexto = match ($periodo) {
                    'dia' => 'en las últimas 24 horas',
                    'semana' => 'en los últimos 7 días',
                    'mes' => 'en los últimos 30 días',
                    default => '',
                };
                $this->mensajeError = 'Has alcanzado el límite de giros permitidos ('.$maxGiros.') '
                    .$periodoTexto.' desde '.implode(' y ', $razones).'.';

                return null;
            }

            $premios = Premio::where('activo', true)->get();
            $totalPeso = $premios->sum('probabilidad');

            if ($totalPeso <= 0) {
                $this->mensajeError = 'No hay premios configurados.';

                return null;
            }

            $random = mt_rand(1, (int) ($totalPeso * 100)) / 100;
            $acumulado = 0;
            $premioSeleccionado = null;

            foreach ($premios as $premio) {
                $acumulado += $premio->probabilidad;
                if ($random <= $acumulado) {
                    $premioSeleccionado = $premio;
                    break;
                }
            }

            if (! $premioSeleccionado) {
                $this->mensajeError = 'No hay premios configurados.';

                return null;
            }

            Resultado::create([
                'nick' => $this->nick,
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'aplicacion_id' => $this->aplicacion_id,
                'premio_id' => $premioSeleccionado->id,
                'premio_nombre' => $premioSeleccionado->nombre,
                'es_premio' => (bool) $premioSeleccionado->es_premio,
                'ip' => $ip,
            ]);

            return $premioSeleccionado;
        });

        if (! $premioSeleccionado) {
            return;
        }

        $this->premioGanador = $premioSeleccionado;
        $this->mostrarModal = false;
        $this->girando = true;
        $this->mostrarResultado = false;

        $this->dispatch(
            'spin-to',
            ganadorId: $premioSeleccionado->id,
            esPremio: (bool) $premioSeleccionado->es_premio
        );
    }

    #[On('spin-finished')]
    public function finalizarGiro()
    {
        $this->mostrarResultado = true;
    }

    public function render()
    {
        $config = RuletaConfig::first();

        return view('livewire.ruleta-publica', [
            'premios' => Premio::where('activo', true)->orderBy('id')->get(),
            'aplicaciones' => Aplicacion::where('activo', true)->orderBy('nombre')->get(),
            'captchaActivo' => (bool) ($config?->captcha_activo ?? true),
        ])->layout('layouts.guest');
    }
}
