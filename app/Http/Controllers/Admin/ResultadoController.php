<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aplicacion;
use App\Models\Premio;
use App\Models\Resultado;
use Illuminate\Http\Request;

class ResultadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Resultado::with(['premio', 'aplicacion'])
            ->orderBy('fecha', 'desc'); // ✅ with aplicacion

        if ($request->filled('nick')) {
            $query->where('nick', 'like', '%'.$request->nick.'%');
        }
        if ($request->filled('premio_id')) {
            $query->where('premio_id', $request->premio_id);
        }
        if ($request->filled('aplicacion_id')) {
            $query->where('aplicacion_id', $request->aplicacion_id);
        } // ✅
        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }

        $resultados = $query->paginate(20)->withQueryString();
        $premios = Premio::all();
        $aplicaciones = Aplicacion::orderBy('nombre')->get(); // ✅

        return view('admin.resultados.index', compact('resultados', 'premios', 'aplicaciones'));
    }

    public function export(Request $request)
    {
        $query = Resultado::query()
            ->leftJoin('aplicacions', 'resultados.aplicacion_id', '=', 'aplicacions.id')
            ->select('resultados.*', 'aplicacions.nombre as aplicacion_nombre')
            ->orderByDesc('resultados.fecha')
            ->orderByDesc('resultados.id');

        if ($request->filled('nick')) {
            $query->where('resultados.nick', 'like', '%'.$request->nick.'%');
        }
        if ($request->filled('premio_id')) {
            $query->where('resultados.premio_id', $request->premio_id);
        }
        if ($request->filled('aplicacion_id')) {
            $query->where('resultados.aplicacion_id', $request->aplicacion_id);
        }
        if ($request->filled('desde')) {
            $query->whereDate('resultados.fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('resultados.fecha', '<=', $request->hasta);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="resultados_'.now()->format('Y-m-d_His').'.csv"',
        ];

        $callback = function () use ($query): void {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // ✅ Cabecera con Aplicación
            fputcsv($file, ['ID', 'Nick', 'Nombre', 'Apellido', 'Aplicación', 'Premio', 'IP', 'Fecha']);

            foreach ($query->lazy(1000) as $r) {
                $row = [
                    $r->id,
                    $r->nick,
                    $r->nombre,
                    $r->apellido,
                    $r->aplicacion_nombre ?? '—',
                    $r->premio_nombre,
                    $r->ip,
                    $r->fecha->format('Y-m-d H:i:s'),
                ];

                foreach ($row as $index => $value) {
                    if (is_string($value) && preg_match('/^[\t\r\n ]*[=+\-@]/', $value) === 1) {
                        $row[$index] = "'".$value;
                    }
                }

                fputcsv($file, $row);
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
