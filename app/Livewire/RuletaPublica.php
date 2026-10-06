<?php

namespace App\Livewire;

use Livewire\Component;
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

        $girosRealizados = Resultado::where('nick', $this->nick)->count();

        if ($girosRealizados >= $maxGiros) {
            $this->mensajeError = 'Has alcanzado el límite de giros permitidos (' . $maxGiros . ').';
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
            'ip' => request()->ip(),
        ]);

        $this->premioGanador = $premioSeleccionado;
        $this->mostrarModal = false;
        $this->girando = true;
    }

    public function render()
    {
        return view('livewire.ruleta-publica', [
            'premios' => Premio::where('activo', true)->orderBy('id')->get(),
        ])->layout('layouts.guest');
    }
}
