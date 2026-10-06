<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resultado;
use App\Models\Premio;

class ResultadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Resultado::with('premio')->orderBy('fecha', 'desc');

        if ($request->filled('nick')) $query->where('nick', 'like', '%'.$request->nick.'%');
        if ($request->filled('premio_id')) $query->where('premio_id', $request->premio_id);
        if ($request->filled('desde')) $query->whereDate('fecha', '>=', $request->desde);
        if ($request->filled('hasta')) $query->whereDate('fecha', '<=', $request->hasta);

        $resultados = $query->paginate(20)->withQueryString();
        $premios = Premio::all();

        return view('admin.resultados.index', compact('resultados', 'premios'));
    }

    public function export(Request $request)
    {
        $query = Resultado::with('premio')->orderBy('fecha', 'desc');
        if ($request->filled('nick')) $query->where('nick', 'like', '%'.$request->nick.'%');
        if ($request->filled('premio_id')) $query->where('premio_id', $request->premio_id);

        $resultados = $query->get();
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="resultados_'.now()->format('Y-m-d_His').'.csv"',
        ];

        $callback = function () use ($resultados) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para Excel
            fputcsv($file, ['ID', 'Nick', 'Nombre', 'Apellido', 'Premio', 'IP', 'Fecha']);
            foreach ($resultados as $r) {
                fputcsv($file, [$r->id, $r->nick, $r->nombre, $r->apellido, $r->premio_nombre, $r->ip, $r->fecha->format('Y-m-d H:i:s')]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(Resultado $resultado)
    {
        $resultado->delete();
        return redirect()->route('admin.resultados.index')->with('success', 'Resultado eliminado.');
    }
}
