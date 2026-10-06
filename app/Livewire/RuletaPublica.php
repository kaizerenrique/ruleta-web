<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Premio;
use App\Models\Resultado;
use App\Models\RuletaConfig;

class RuletaPublica extends Component
{
    public $nick = '';
    public $nombre = '';
    public $apellido = '';
    public $captcha = '';
    public $mostrarModal = true;
    public $mensajeError = '';
    public $premioGanador = null;
    public $girando = false;
    public $mostrarResultado = false; // ✅ NUEVO: solo true al terminar la animación

    protected $rules = [
        'nick' => 'required|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
        'nombre' => 'required|string|max:100',
        'apellido' => 'required|string|max:100',
        'captcha' => 'required|captcha',
    ];

    public function girar()
    {
        $this->validate();

        $config = RuletaConfig::first();
        $maxGiros = $config?->max_giros_por_usuario ?? 1;
        $tipoLimite = $config?->tipo_limite ?? 'ip';
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
            'premio_id' => $premioSeleccionado->id,
            'premio_nombre' => $premioSeleccionado->nombre,
            'ip' => $ip,
        ]);

        $this->premioGanador = $premioSeleccionado;
        $this->mostrarModal = false;
        $this->girando = true;
        $this->mostrarResultado = false; // ✅ Asegurar que el mensaje NO se muestre todavía

        // ✅ Avisar al JS para que empiece a girar
        $this->dispatch('spin-to', ganadorId: $premioSeleccionado->id);
    }

    /**
     * ✅ Llamado desde el JS al terminar la animación.
     */
    #[On('spin-finished')]
    public function finalizarGiro()
    {
        $this->mostrarResultado = true;
    }

    public function render()
    {
        return view('livewire.ruleta-publica', [
            'premios' => Premio::where('activo', true)->orderBy('id')->get(),
        ])->layout('layouts.guest');
    }
}