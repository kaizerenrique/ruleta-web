<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Premios de la Ruleta
            </h2>
            <a href="{{ route('admin.premios.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition">
                ➕ Nuevo Premio
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Probabilidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($premios as $premio)
                        <tr>
                            <td class="px-6 py-4 text-gray-500">{{ $premio->id }}</td>

                            {{-- Color en círculo grande con borde --}}
                            <td class="px-6 py-4">
                                <span class="inline-block w-8 h-8 rounded-full border-2 border-gray-300 shadow-sm"
                                      style="background-color: {{ $premio->color }}"
                                      title="{{ $premio->color }}"></span>
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $premio->nombre }}
                            </td>

                            {{-- ✅ Nueva columna: tipo con badge --}}
                            <td class="px-6 py-4">
                                @if($premio->es_premio)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        🎁 Premio
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                                        ❌ Sin premio
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-gray-700">{{ $premio->probabilidad }}%</td>

                            <td class="px-6 py-4">
                                @if($premio->activo)
                                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Activo</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Inactivo</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.premios.edit', $premio) }}"
                                   class="text-indigo-600 hover:text-indigo-800 text-sm">Editar</a>
                                <form action="{{ route('admin.premios.destroy', $premio) }}" method="POST" class="inline"
                                      onsubmit="return confirm('¿Eliminar este premio?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No hay premios configurados. <a href="{{ route('admin.premios.create') }}" class="text-indigo-600 underline">Crea el primero</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $premios->links() }}</div>
        </div>
    </div>
</x-app-layout>