<section x-data="clientTableFilter()" class="flex h-full w-full flex-1 flex-col gap-4">
    <!-- Header -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <h1 class="text-2xl font-bold">{{ __('Comuneros') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-2">{{ __('Gestiona los comuneros del sistema') }}</p>
    </div>

    <!-- Lista y Botón -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">{{ __('Lista de Comuneros') }}</h2>
            <div class="flex gap-2">
                <button wire:click="descargarExcel"
                        type="button"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    {{ __('Excel') }}
                </button>
                <button wire:click="openNew"
                        type="button"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Nuevo') }}
                </button>
            </div>
        </div>

        <!-- Búsqueda -->
        <div class="mb-6">
            <input type="text" 
                   x-model.debounce.150ms="search"
                   @keydown.escape="resetSearch()"
                   placeholder="{{ __('Buscar por nombre, apellido o DNI...') }}"
                   class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <th class="text-left py-3 px-4 font-semibold">{{ __('ID') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Ciudadano') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('DNI') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Estado') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comuneros as $comunero)
                    <tr x-show="matches($el.dataset.filter)"
                        x-cloak
                        data-filter="{{ \Illuminate\Support\Str::lower($comunero->id.' '.$comunero->ciudadano->ape_paterno.' '.$comunero->ciudadano->ape_materno.' '.$comunero->ciudadano->nombres.' '.$comunero->ciudadano->dni.' '.$comunero->estado_comunero) }}"
                        class="border-b border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                        <td class="py-3 px-4 font-medium">{{ $comunero->id }}</td>
                        <td class="py-3 px-4">{{ $comunero->ciudadano->ape_paterno }} {{ $comunero->ciudadano->ape_materno }}, {{ $comunero->ciudadano->nombres }}</td>
                        <td class="py-3 px-4">{{ $comunero->ciudadano->dni }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs rounded {{ $comunero->estado_comunero === 'Activo' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $comunero->estado_comunero }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <button onclick="
                                    Swal.fire({
                                        title: '¿Eliminar comunero?',
                                        text: '¿Estás seguro de eliminar este comunero?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Sí, eliminar',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            @this.delete({{ $comunero->id }});
                                        }
                                    })"
                                        type="button"
                                        title="{{ __('Eliminar') }}"
                                        class="p-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <td colspan="5" class="py-8 px-4 text-center text-neutral-500">
                            {{ __('No hay registros') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($comuneros->hasPages())
        <div x-show="!search" class="mt-6">
            {{ $comuneros->links() }}
        </div>
        @endif
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div wire:click="closeModal" class="absolute inset-0 bg-black/10"></div>
        <div wire:click.stop
             class="bg-white dark:bg-neutral-800 rounded-xl shadow-2xl w-full max-w-2xl p-6 relative z-10 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modal-title" class="text-xl font-bold">{{ __('Agregar Comunero(s)') }}</h3>
                <button wire:click="closeModal" class="text-neutral-500 hover:text-neutral-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <!-- Búsqueda dentro del modal -->
                <div>
                    <label class="block text-sm font-medium mb-2">{{ __('Buscar Ciudadanos') }}</label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="searchCiudadano"
                           placeholder="{{ __('Buscar por nombre o DNI...') }}"
                           autocomplete="off"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
                </div>

                <!-- Lista de ciudadanos seleccionables -->
                <div>
                    <label class="block text-sm font-medium mb-3">{{ __('Seleccionar Ciudadanos') }} ({{ __('Se pueden elegir múltiples') }})</label>
                    
                    @if(count($this->filtredCiudadanos) > 0)
                    <div class="grid grid-cols-1 max-h-64 overflow-y-auto border border-neutral-300 dark:border-neutral-600 rounded-lg">
                        @foreach($this->filtredCiudadanos as $id => $nombre)
                        <label class="flex items-center gap-3 p-3 border-b border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700 cursor-pointer">
                            <input type="checkbox" 
                                   wire:model="selectedCiudadanos"
                                   value="{{ $id }}"
                                   class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm">{{ $nombre }}</span>
                        </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-neutral-600 dark:text-neutral-400">
                        {{ count($this->selectedCiudadanos) }} {{ __('ciudadano(s) seleccionado(s)') }}
                    </p>
                    @else
                    <div class="p-4 text-center text-neutral-500 border border-neutral-300 dark:border-neutral-600 rounded-lg">
                        {{ __('No hay ciudadanos disponibles') }}
                    </div>
                    @endif

                    @error('selectedCiudadanos')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="estado_comunero" class="block text-sm font-medium mb-2">{{ __('Estado') }}</label>
                    <select id="estado_comunero"
                            wire:model="estado_comunero"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
                        <option value="Activo">Activo</option>
                        <option value="Suspendido">Suspendido</option>
                        <option value="Retirado">Retirado</option>
                    </select>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" 
                            wire:click="closeModal"
                            class="px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit"
                            :disabled="count($this->selectedCiudadanos) === 0"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="{ 'opacity-50 cursor-not-allowed': count($this->selectedCiudadanos) === 0 }">
                        {{ __('Agregar Comunero(s)') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</section>
