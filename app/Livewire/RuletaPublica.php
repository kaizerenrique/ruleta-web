<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Premio;
use App\Models\Resultado;
use App\Models\RuletaConfig;
use App\Models\Aplicacion;

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

    protected $rules = [
        'nick' => 'required|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
        'nombre' => 'required|string|max:100',
        'apellido' => 'required|string|max:100',
        'aplicacion_id' => 'required|exists:aplicacions,id',
        'captcha' => 'required|captcha',
    ];

    protected $messages = [
        'aplicacion_id.required' => 'Debes seleccionar una aplicación.',
        'aplicacion_id.exists' => 'La aplicación seleccionada no es válida.',
    ];

    public function mount()
    {
        $config = RuletaConfig::first();
        $this->ruletaActiva = $config?->activa ?? true;
        if (!$this->ruletaActiva) {
            $this->mostrarModal = false;
        }
    }

    public function girar()
    {
        $config = RuletaConfig::first();
        if (!$config?->activa) {
            $this->ruletaActiva = false;
            $this->mostrarModal = false;
            return;
        }

        $this->validate();

        $maxGiros = $config->max_giros_por_usuario ?? 1;
        $tipoLimite = $config->tipo_limite ?? 'ip';
        $ip = request()->ip();

        $girosPorIp = Resultado::where('ip', $ip)->count();
        $girosPorNick = Resultado::where('nick', $this->nick)->count();

        $bloqueado = false;
        $razones = [];

        if (in_array($tipoLimite, ['ip', 'ambos']) && $girosPorIp >= $maxGiros) {
            $bloqueado = true;
            $razones[] = 'esta conexión (IP)';
        }
        if (in_array($tipoLimite, ['nick', 'ambos']) && $girosPorNick >= $maxGiros) {
            $bloqueado = true;
            $razones[] = 'este nick';
        }

        if ($bloqueado) {
            $this->mensajeError = 'Has alcanzado el límite de giros permitidos (' . $maxGiros . ') desde ' . implode(' y ', $razones) . '.';
            return;
        }

        $premios = Premio::where('activo', true)->get();
        $totalPeso = $premios->sum('probabilidad');
        $random = mt_rand(1, (int)($totalPeso * 100)) / 100;
        $acumulado = 0;
        $premioSeleccionado = null;

        foreach ($premios as $premio) {
            $acumulado += $premio->probabilidad;
            if ($random <= $acumulado) {
                $premioSeleccionado = $premio;
                break;
            }
        }

        if (!$premioSeleccionado) {
            $this->mensajeError = 'No hay premios configurados.';
            return;
        }

        Resultado::create([
            'nick' => $this->nick,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'aplicacion_id' => $this->aplicacion_id, // ✅ NUEVO
            'premio_id' => $premioSeleccionado->id,
            'premio_nombre' => $premioSeleccionado->nombre,
            'ip' => $ip,
        ]);

        $this->premioGanador = $premioSeleccionado;
        $this->mostrarModal = false;
        $this->girando = true;
        $this->mostrarResultado = false;

        $this->dispatch('spin-to',
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
        return view('livewire.ruleta-publica', [
            'premios' => Premio::where('activo', true)->orderBy('id')->get(),
            'aplicaciones' => Aplicacion::where('activo', true)->orderBy('nombre')->get(), // ✅ NUEVO
        ])->layout('layouts.guest');
    }
}