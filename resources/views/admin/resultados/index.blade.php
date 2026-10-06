<!-- resources/views/admin/resultados/index.blade.php (extracto) -->
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
            <form method="GET" class="bg-white shadow rounded-lg p-4 mb-6 flex flex-wrap gap-4">
                <input type="text" name="nick" value="{{ request('nick') }}" placeholder="Buscar nick..."
                       class="border-gray-300 rounded-md shadow-sm">
                <select name="premio_id" class="border-gray-300 rounded-md shadow-sm">
                    <option value="">Todos los premios</option>
                    @foreach($premios as $p)
                        <option value="{{ $p->id }}" {{ request('premio_id') == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                    @endforeach
                </select>
                <input type="date" name="desde" value="{{ request('desde') }}" class="border-gray-300 rounded-md shadow-sm">
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="border-gray-300 rounded-md shadow-sm">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Filtrar</button>
            </form>

            {{-- Tabla --}}
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nick</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Premio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($resultados as $r)
                        <tr>
                            <td class="px-6 py-4">{{ $r->nick }}</td>
                            <td class="px-6 py-4">{{ $r->nombre }} {{ $r->apellido }}</td>
                            <td class="px-6 py-4">{{ $r->premio_nombre }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $r->fecha->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.resultados.destroy', $r) }}" method="POST" class="inline"
                                      onsubmit="return confirm('¿Eliminar este resultado?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $resultados->links() }}</div>
        </div>
    </div>
</x-app-layout>