<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Premio;
use App\Models\Resultado;
use App\Models\RuletaConfig;

class RuletaController extends Controller
{
    public function index()
    {
        $premios = Premio::where('activo', true)->orderBy('id')->get();
        return view('ruleta.index', compact('premios'));
    }

        public function girar(Request $request)
    {
        $request->validate([
            'nick' => 'required|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'captcha' => 'required|captcha',
        ], [
            'captcha.captcha' => 'El código de verificación es incorrecto.',
        ]);

        $config = RuletaConfig::first();
        $maxGiros = $config?->max_giros_por_usuario ?? 1;

        $girosRealizados = Resultado::where('nick', $request->nick)->count();

        if ($girosRealizados >= $maxGiros) {
            return response()->json([
                'success' => false,
                'error' => 'Has alcanzado el límite de giros permitidos (' . $maxGiros . ').'
            ], 429);
        }

        $premioSeleccionado = $this->seleccionarPremio();

        if (!$premioSeleccionado) {
            return response()->json([
                'success' => false,
                'error' => 'No hay premios configurados.'
            ], 500);
        }

        Resultado::create([
            'nick' => $request->nick,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'premio_id' => $premioSeleccionado->id,
            'premio_nombre' => $premioSeleccionado->nombre,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'premio' => $premioSeleccionado,
            'giros_restantes' => max(0, $maxGiros - $girosRealizados - 1),
        ]);
    }

    private function seleccionarPremio(): ?Premio
    {
        $premios = Premio::where('activo', true)->get();
        if ($premios->isEmpty()) return null;

        $totalPeso = $premios->sum('probabilidad');
        if ($totalPeso <= 0) return $premios->random();

        $random = mt_rand(1, (int)($totalPeso * 100)) / 100;
        $acumulado = 0;

        foreach ($premios as $premio) {
            $acumulado += $premio->probabilidad;
            if ($random <= $acumulado) return $premio;
        }

        return $premios->last();
    }
}
