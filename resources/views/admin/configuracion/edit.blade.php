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
                    <div class="bg-green-100 text-green-800 p-3 rounded-lg mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.configuracion.update') }}" class="space-y-6">
                    @csrf @method('PUT')

                    {{-- ✅ INTERRUPTOR MAESTRO: encender/apagar la ruleta --}}
                    <div class="rounded-xl border-2 p-4 transition
                                {{ $config->activa ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                        <div class="flex items-center justify-between">
                            <div class="pr-4">
                                <h3 class="font-bold text-lg
                                           {{ $config->activa ? 'text-emerald-800' : 'text-red-800' }}">
                                    {{ $config->activa ? '🟢 Ruleta activa' : '🔴 Ruleta apagada' }}
                                </h3>
                                <p class="text-sm mt-1
                                          {{ $config->activa ? 'text-emerald-700' : 'text-red-700' }}">
                                    {{ $config->activa
                                        ? 'Los usuarios pueden participar normalmente.'
                                        : 'Los usuarios verán un mensaje de que la ruleta está inactiva. Nadie podrá girar.' }}
                                </p>
                            </div>

                            {{-- Toggle switch --}}
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="hidden" name="activa" value="0">
                                <input type="checkbox" name="activa" value="1"
                                       {{ $config->activa ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-14 h-8 bg-gray-300 peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-6
                                            peer-checked:after:border-white
                                            after:content-[''] after:absolute after:top-1 after:left-1
                                            after:bg-white after:border-gray-300 after:border
                                            after:rounded-full after:h-6 after:w-6
                                            after:transition-all
                                            peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Resto de la configuración --}}
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">
                            Máximo de giros permitidos
                        </label>
                        <input type="number" name="max_giros_por_usuario"
                               value="{{ old('max_giros_por_usuario', $config->max_giros_por_usuario) }}"
                               min="1" max="100" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('max_giros_por_usuario') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">
                            Criterio de validación del límite
                        </label>
                        <select name="tipo_limite" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="ip" {{ old('tipo_limite', $config->tipo_limite) === 'ip' ? 'selected' : '' }}>
                                Por IP (recomendado para demos públicas)
                            </option>
                            <option value="nick" {{ old('tipo_limite', $config->tipo_limite) === 'nick' ? 'selected' : '' }}>
                                Por Nick
                            </option>
                            <option value="ambos" {{ old('tipo_limite', $config->tipo_limite) === 'ambos' ? 'selected' : '' }}>
                                Por IP y Nick (más estricto)
                            </option>
                        </select>
                        @error('tipo_limite') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
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