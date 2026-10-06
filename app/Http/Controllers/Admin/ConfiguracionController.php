<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RuletaConfig;

class ConfiguracionController extends Controller
{
    public function edit()
    {
        $config = RuletaConfig::firstOrCreate([], [
            'max_giros_por_usuario' => 1,
            'captcha_activo' => true,
        ]);
        return view('admin.configuracion.edit', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'max_giros_por_usuario' => 'required|integer|min:1|max:100',
        ]);

        RuletaConfig::first()->update([
            'max_giros_por_usuario' => $request->max_giros_por_usuario,
            'captcha_activo' => $request->boolean('captcha_activo'),
        ]);

        return redirect()->route('admin.configuracion.edit')->with('success', 'Configuración actualizada.');
    }
}
