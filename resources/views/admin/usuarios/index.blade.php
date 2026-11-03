<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Usuarios</h2>
            <a href="{{ route('admin.usuarios.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition">
                ➕ Nuevo Usuario
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded-lg mb-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 text-red-800 p-3 rounded-lg mb-4">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($usuarios as $u)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $u->name }}
                                    @if ($u->id === auth()->id())
                                        <span class="ml-2 text-xs text-indigo-600">(tú)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $u->email }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @forelse ($u->getRoleNames() as $r)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $r === 'admin' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                                            {{ $r }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400">—</span>
                                    @endforelse
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    {{ $u->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.usuarios.edit', $u) }}"
                                       class="text-indigo-600 hover:text-indigo-800 text-sm">Editar</a>

                                    @if ($u->id !== auth()->id())
                                        <form action="{{ route('admin.usuarios.destroy', $u) }}" method="POST" class="inline"
                                              onsubmit="return confirm('¿Eliminar al usuario {{ $u->name }}?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800 text-sm">Eliminar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $usuarios->links() }}</div>
        </div>
    </div>
</x-app-layout>