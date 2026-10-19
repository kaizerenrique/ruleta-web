<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Aplicacion;
use App\Models\Premio;
use App\Models\Resultado;
use App\Models\RuletaConfig;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $config  = RuletaConfig::first();
        $hoy     = now()->startOfDay();
        $semana  = now()->subDays(6)->startOfDay(); // últimos 7 días incluyendo hoy
        $mes     = now()->subDays(29)->startOfDay(); // últimos 30 días

        // ── KPIs principales ──────────────────────────────────────────
        $totalGiros    = Resultado::count();
        $totalPremios  = Resultado::whereHas('premio', fn($q) => $q->where('es_premio', true))->count();
        $totalSinPremio = $totalGiros - $totalPremios;
        $tasaConversion = $totalGiros > 0
            ? round(($totalPremios / $totalGiros) * 100, 1)
            : 0.0;

        $ipsUnicas   = Resultado::distinct('ip')->count('ip');
        $nicksUnicos = Resultado::distinct('nick')->count('nick');

        $girosHoy   = Resultado::where('fecha', '>=', $hoy)->count();
        $girosSemana = Resultado::where('fecha', '>=', $semana)->count();
        $girosMes    = Resultado::where('fecha', '>=', $mes)->count();

        $premiosActivos  = Premio::where('activo', true)->count();
        $appsActivas     = Aplicacion::where('activo', true)->count();

        // ── Giros por día (últimos 14 días) ──────────────────────────
        $dias = collect(range(13, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));

        $conteosPorDia = Resultado::where('fecha', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(fecha) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $girosPorDia = [
            'labels' => $dias->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->values()->toArray(),
            'data'   => $dias->map(fn($d) => (int) ($conteosPorDia[$d] ?? 0))->values()->toArray(),
        ];

        // ── Distribución premios vs sin premio (para gráfico dona) ────
        $distribucion = [
            'labels' => ['Premios', 'Sin premio'],
            'data'   => [$totalPremios, $totalSinPremio],
        ];

        // ── Top 5 premios más entregados ──────────────────────────────
        $topPremios = Resultado::select('premio_nombre', DB::raw('COUNT(*) as total'))
            ->whereNotNull('premio_nombre')
            ->groupBy('premio_nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ── Top 5 aplicaciones con más participación ─────────────────
        $topAplicaciones = Aplicacion::query()
            ->leftJoin('resultados', 'resultados.aplicacion_id', '=', 'aplicacions.id')
            ->select('aplicacions.id', 'aplicacions.nombre', DB::raw('COUNT(resultados.id) as total'))
            ->groupBy('aplicacions.id', 'aplicacions.nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ── Últimos 10 resultados ────────────────────────────────────
        $ultimosResultados = Resultado::with(['premio', 'aplicacion'])
            ->orderBy('fecha', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'config',
            'totalGiros',
            'totalPremios',
            'totalSinPremio',
            'tasaConversion',
            'ipsUnicas',
            'nicksUnicos',
            'girosHoy',
            'girosSemana',
            'girosMes',
            'premiosActivos',
            'appsActivas',
            'girosPorDia',
            'distribucion',
            'topPremios',
            'topAplicaciones',
            'ultimosResultados',
        ));
    }
}
