<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Editar Usuario: {{ $usuario->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                @if ($errors->any())
                    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST" class="space-y-5">
                    @csrf @method('PUT')

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" name="name" value="{{ old('name', $usuario->name) }}" required maxlength="255"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required maxlength="255"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Nueva contraseña</label>
                        <input type="password" name="password" minlength="8"
                               placeholder="Dejar vacío para no cambiar"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmation" minlength="8"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Rol</label>
                        <select name="role" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach ($rolesDisponibles as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('role', $rolActual) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition">
                            Actualizar
                        </button>
                        <a href="{{ route('admin.usuarios.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>