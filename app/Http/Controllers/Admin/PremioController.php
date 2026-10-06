<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Premio;


class PremioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $premios = Premio::orderBy('id')->paginate(10);
        return view('admin.premios.index', compact('premios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.premios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:100',
            'color'        => 'required|string|max:20',
            'probabilidad' => 'required|numeric|min:0',
            'es_premio'    => 'nullable|boolean',
            'activo'       => 'nullable|boolean',
        ]);

        $data['es_premio'] = $request->boolean('es_premio');
        $data['activo']    = $request->boolean('activo');

        Premio::create($data);

        return redirect()->route('admin.premios.index')->with('success', 'Premio creado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Premio $premio)
    {
        return view('admin.premios.edit', compact('premio')); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Premio $premio)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:100',
            'color'        => 'required|string|max:20',
            'probabilidad' => 'required|numeric|min:0',
            'es_premio'    => 'nullable|boolean',
            'activo'       => 'nullable|boolean',
        ]);

        $data['es_premio'] = $request->boolean('es_premio');
        $data['activo']    = $request->boolean('activo');

        $premio->update($data);

        return redirect()->route('admin.premios.index')->with('success', 'Premio actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Premio $premio)
    {
        $premio->delete();
        return redirect()->route('admin.premios.index')->with('success', 'Premio eliminado.');
    }
}
