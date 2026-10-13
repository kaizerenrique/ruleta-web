<!-- resources/views/livewire/aplicaciones-manager.blade.php -->
<div>

    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Encabezado: título + botón --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
        <h3 class="text-lg font-semibold text-gray-800">
            Listado de Aplicaciones
        </h3>
        <button wire:click="abrirModal"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition
                       w-full sm:w-auto">
            ➕ Nueva Aplicación
        </button>
    </div>

    {{-- Tabla --}}
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($aplicaciones as $app)
                        <tr wire:key="app-{{ $app->id }}">
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $app->id }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $app->nombre }}</td>
                            <td class="px-6 py-4 text-gray-600 text-sm hidden md:table-cell">
                                {{ $app->descripcion ?: '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($app->activo)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-medium">
                                        ● Activa
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-medium">
                                        ● Inactiva
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <button wire:click="editar({{ $app->id }})"
                                        class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    Editar
                                </button>
                                <button wire:click="confirmarEliminar({{ $app->id }})"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No hay aplicaciones registradas.
                                <button wire:click="abrirModal" class="text-indigo-600 underline">
                                    Crea la primera
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Crear / Editar --}}
    @if ($mostrarModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
             wire:key="modal-crear-editar">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6
                        max-h-[95vh] overflow-y-auto">

                <h3 class="text-xl font-bold text-slate-800 mb-4">
                    {{ $editandoId ? '✏️ Editar Aplicación' : '➕ Nueva Aplicación' }}
                </h3>

                <form wire:submit="guardar" class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre de la aplicación <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.blur="nombre" required maxlength="100"
                               placeholder="Ej: Tienda Online, App Móvil, Portal de Clientes"
                               class="w-full border-gray-300 rounded-lg shadow-sm
                                      focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('nombre')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Descripción <span class="text-gray-400 font-normal">(opcional)</span>
                        </label>
                        <textarea wire:model.blur="descripcion" rows="3" maxlength="255"
                                  placeholder="Breve descripción de la aplicación o su propósito"
                                  class="w-full border-gray-300 rounded-lg shadow-sm
                                         focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        @error('descripcion')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="activo"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">Aplicación activa</span>
                    </label>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="button" wire:click="cerrarModal"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold
                                       py-2.5 rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold
                                       py-2.5 rounded-lg transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="guardar">
                                {{ $editandoId ? 'Actualizar' : 'Guardar' }}
                            </span>
                            <span wire:loading wire:target="guardar">Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal de confirmación para eliminar --}}
    @if ($confirmandoEliminarId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
             wire:key="modal-eliminar">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">

                <div class="text-5xl mb-4">⚠️</div>

                <h3 class="text-lg font-bold text-slate-800 mb-2">
                    ¿Eliminar esta aplicación?
                </h3>

                <p class="text-slate-600 text-sm mb-6">
                    Esta acción no se puede deshacer.
                </p>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button wire:click="cancelarEliminar"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold
                                   py-2.5 rounded-lg transition">
                        Cancelar
                    </button>
                    <button wire:click="eliminar"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold
                                   py-2.5 rounded-lg transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="eliminar">Eliminar</span>
                        <span wire:loading wire:target="eliminar">Eliminando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
