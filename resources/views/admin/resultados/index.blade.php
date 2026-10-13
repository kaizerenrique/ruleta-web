<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Resultados</h2>
            <a href="{{ route('admin.resultados.export', request()->query()) }}"
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                📥 Exportar CSV
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filtros --}}
            <form method="GET" class="bg-white shadow rounded-lg p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <input type="text" name="nick" value="{{ request('nick') }}" placeholder="Buscar nick..."
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm">

                <select name="premio_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Todos los premios</option>
                    @foreach($premios as $p)
                        <option value="{{ $p->id }}" {{ request('premio_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nombre }}
                        </option>
                    @endforeach
                </select>

                {{-- ✅ Filtro por aplicación --}}
                <select name="aplicacion_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Todas las aplicaciones</option>
                    @foreach($aplicaciones as $app)
                        <option value="{{ $app->id }}" {{ request('aplicacion_id') == $app->id ? 'selected' : '' }}>
                            {{ $app->nombre }}
                        </option>
                    @endforeach
                </select>

                <input type="date" name="desde" value="{{ request('desde') }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm">

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm lg:col-span-5 sm:col-span-2">
                    Filtrar
                </button>
            </form>

            {{-- Tabla --}}
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nick</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aplicación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Premio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($resultados as $r)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $r->nick }}</td>
                                <td class="px-6 py-4 text-sm">{{ $r->nombre }} {{ $r->apellido }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($r->aplicacion)
                                        <span class="inline-block px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-700 font-medium">
                                            {{ $r->aplicacion->nombre }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $r->premio_nombre }}</td>
                                <td class="px-6 py-4 text-xs text-gray-500">{{ $r->fecha->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.resultados.destroy', $r) }}" method="POST" class="inline"
                                          onsubmit="return confirm('¿Eliminar este resultado?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 text-sm">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No hay resultados que coincidan con los filtros.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $resultados->links() }}</div>
        </div>
    </div>
</x-app-layout>