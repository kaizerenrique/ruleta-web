<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📊 Panel de Estadísticas
            </h2>
            <div class="text-sm text-gray-500 hidden sm:block">
                Actualizado: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ── Estado de la ruleta ────────────────────────────── --}}
            <div class="rounded-2xl border-2 p-4
                        {{ ($config?->activa) ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="font-bold text-lg {{ ($config?->activa) ? 'text-emerald-800' : 'text-red-800' }}">
                            {{ ($config?->activa) ? '🟢 Ruleta activa' : '🔴 Ruleta apagada' }}
                        </h3>
                        <p class="text-sm mt-1 {{ ($config?->activa) ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ $premiosActivos }} premio(s) activo(s) ·
                            {{ $appsActivas }} aplicación(es) activa(s) ·
                            Límite: {{ $config?->max_giros_por_usuario ?? '—' }} giro(s)
                            por {{ $config?->tipo_limite ?? '—' }}
                            ({{ $config?->periodo_limite ?? '—' }})
                        </p>
                    </div>
                    <a href="{{ route('admin.configuracion.edit') }}"
                       class="text-sm font-medium underline
                              {{ ($config?->activa) ? 'text-emerald-700 hover:text-emerald-900' : 'text-red-700 hover:text-red-900' }}">
                        Ir a configuración →
                    </a>
                </div>
            </div>

            {{-- ── KPIs principales ───────────────────────────────── --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow p-5 border-l-4 border-indigo-500">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total giros</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalGiros) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Histórico completo</p>
                </div>

                <div class="bg-white rounded-xl shadow p-5 border-l-4 border-amber-500">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Premios entregados</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ number_format($totalPremios) }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        Tasa de éxito: <span class="font-semibold text-amber-700">{{ $tasaConversion }}%</span>
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow p-5 border-l-4 border-emerald-500">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Participantes únicos</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1">{{ number_format($ipsUnicas) }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        IPs · {{ number_format($nicksUnicos) }} nicks distintos
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow p-5 border-l-4 border-pink-500">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Giros hoy</p>
                    <p class="text-3xl font-bold text-pink-600 mt-1">{{ number_format($girosHoy) }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ number_format($girosSemana) }} últimos 7 días ·
                        {{ number_format($girosMes) }} últimos 30 días
                    </p>
                </div>
            </div>

            {{-- ── Gráficos ───────────────────────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow p-5 lg:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">
                        Giros en los últimos 14 días
                    </h3>
                    <div class="h-64">
                        <canvas id="chartGirosDia"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">
                        Distribución de resultados
                    </h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="chartDistribucion"></canvas>
                    </div>
                </div>
            </div>

            {{-- ── Top premios y aplicaciones ─────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">
                        🏆 Premios más entregados
                    </h3>
                    @if ($topPremios->isEmpty())
                        <p class="text-gray-400 text-sm">Aún no hay premios entregados.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($topPremios as $i => $p)
                                <li class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2 text-gray-700">
                                        <span class="w-5 text-gray-400 text-xs">{{ $i + 1 }}.</span>
                                        {{ $p->premio_nombre }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">
                                        {{ $p->total }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">
                        📱 Aplicaciones con más participación
                    </h3>
                    @if ($topAplicaciones->isEmpty())
                        <p class="text-gray-400 text-sm">Aún no hay participación registrada.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($topAplicaciones as $i => $app)
                                <li class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2 text-gray-700">
                                        <span class="w-5 text-gray-400 text-xs">{{ $i + 1 }}.</span>
                                        {{ $app->nombre }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-xs font-semibold">
                                        {{ $app->total }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- ── Últimos resultados ─────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        🕒 Últimos 10 giros
                    </h3>
                    <a href="{{ route('admin.resultados.index') }}"
                       class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                        Ver todos →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nick</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aplicación</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Resultado</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($ultimosResultados as $r)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 font-medium text-gray-800">{{ $r->nick }}</td>
                                    <td class="px-5 py-3 text-gray-600">
                                        {{ $r->aplicacion?->nombre ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        @if ($r->premio?->es_premio)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">
                                                🎁 {{ $r->premio_nombre }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">
                                                ❌ {{ $r->premio_nombre ?? 'Sin premio' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-500">
                                        {{ $r->fecha->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-gray-400">
                                        Aún no hay giros registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js desde CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const girosDia     = @json($girosPorDia);
            const distribucion = @json($distribucion);

            const ctxLine = document.getElementById('chartGirosDia');
            if (ctxLine) {
                new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: girosDia.labels,
                        datasets: [{
                            label: 'Giros',
                            data: girosDia.data,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99,102,241,0.15)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3,
                            pointBackgroundColor: '#6366f1',
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 },
                                grid: { color: 'rgba(0,0,0,0.05)' },
                            },
                            x: { grid: { display: false } },
                        },
                    },
                });
            }

            const ctxDona = document.getElementById('chartDistribucion');
            if (ctxDona) {
                new Chart(ctxDona, {
                    type: 'doughnut',
                    data: {
                        labels: distribucion.labels,
                        datasets: [{
                            data: distribucion.data,
                            backgroundColor: ['#f59e0b', '#6b7280'],
                            borderColor: '#fff',
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const v = ctx.parsed;
                                        const pct = total > 0 ? ((v / total) * 100).toFixed(1) : 0;
                                        return ` ${ctx.label}: ${v} (${pct}%)`;
                                    },
                                },
                            },
                        },
                    },
                });
            }
        })();
    </script>
</x-app-layout>
