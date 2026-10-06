<!-- resources/views/admin/configuracion/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Configuración de la Ruleta
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-3 rounded-lg mb-4">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.configuracion.update') }}" class="space-y-6">
                    @csrf @method('PUT')

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">
                            Máximo de giros por usuario (nick)
                        </label>
                        <input type="number" name="max_giros_por_usuario"
                               value="{{ old('max_giros_por_usuario', $config->max_giros_por_usuario) }}"
                               min="1" max="100" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('max_giros_por_usuario') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <label class="inline-flex items-center">
                        <input type="checkbox" name="captcha_activo" value="1"
                               {{ $config->captcha_activo ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">Captcha activo</span>
                    </label>

                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition">
                        Guardar Configuración
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>