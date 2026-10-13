<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RuletaConfig;

class ConfiguracionController extends Controller
{
    // app/Http/Controllers/Admin/ConfiguracionController.php
    public function edit()
    {
        $config = RuletaConfig::firstOrCreate([], [
            'max_giros_por_usuario' => 1,
            'captcha_activo' => true,
            'tipo_limite' => 'ip',
            'activa' => true,
        ]);
        return view('admin.configuracion.edit', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'max_giros_por_usuario' => 'required|integer|min:1|max:100',
            'tipo_limite' => 'required|in:ip,nick,ambos',
        ]);

        RuletaConfig::first()->update([
            'max_giros_por_usuario' => $request->max_giros_por_usuario,
            'captcha_activo' => $request->boolean('captcha_activo'),
            'tipo_limite' => $request->tipo_limite,
            'activa' => $request->boolean('activa'),
        ]);

        return redirect()->route('admin.configuracion.edit')
            ->with('success', 'Configuración actualizada.');
    }
}
