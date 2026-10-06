<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Premio: {{ $premio->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">

                @if($errors->any())
                    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.premios.update', $premio) }}" method="POST" class="space-y-5">
                    @csrf @method('PUT')

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $premio->nombre) }}" required maxlength="100"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Color (hex)</label>
                        <input type="color" name="color" value="{{ old('color', $premio->color) }}" required
                               class="h-10 w-20 border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Probabilidad (peso relativo)</label>
                        <input type="number" name="probabilidad" value="{{ old('probabilidad', $premio->probabilidad) }}"
                               step="0.01" min="0" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex gap-3">
                        <label class="inline-flex items-center">
                            <input type="hidden" name="activo" value="0">
                            <input type="checkbox" name="activo" value="1" {{ $premio->activo ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-gray-700">Activo</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="hidden" name="es_premio" value="0">
                            <input type="checkbox" name="es_premio" value="1" {{ $premio->es_premio ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-gray-700">Es premio</span>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition">
                            Actualizar
                        </button>
                        <a href="{{ route('admin.premios.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>