<section x-data="clientTableFilter()" class="flex h-full w-full flex-1 flex-col gap-4">
    <!-- Header -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <h1 class="text-2xl font-bold">{{ __('Multas') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-2">{{ __('Gestiona las multas del sistema') }}</p>
    </div>

    <!-- Lista y Boton -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">{{ __('Lista de Multas') }}</h2>
            <button wire:click="openNew; resetForm"
                    type="button"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('Nuevo') }}
            </button>
        </div>

        <!-- Busqueda -->
        <div class="mb-6">
            <input type="text"
                   x-model.debounce.150ms="search"
                   @keydown.escape="resetSearch()"
                   placeholder="{{ __('Buscar por ciudadano, DNI o motivo...') }}"
                   class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Ciudadano') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('DNI') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Monto') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Motivo') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Fecha Emision') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Estado') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($multas as $multa)
                    <tr x-show="matches($el.dataset.filter)"
                        x-cloak
                        data-filter="{{ \Illuminate\Support\Str::lower($multa->comunero->ciudadano->ape_paterno.' '.$multa->comunero->ciudadano->ape_materno.' '.$multa->comunero->ciudadano->nombres.' '.$multa->comunero->ciudadano->dni.' '.$multa->motivo) }}"
                        class="border-b border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                        <td class="py-3 px-4">{{ $multa->comunero->ciudadano->ape_paterno }} {{ $multa->comunero->ciudadano->ape_materno }}, {{ $multa->comunero->ciudadano->nombres }}</td>
                        <td class="py-3 px-4">{{ $multa->comunero->ciudadano->dni }}</td>
                        <td class="py-3 px-4">S/ {{ number_format($multa->monto, 2) }}</td>
                        <td class="py-3 px-4">{{ $multa->motivo ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $multa->fecha_emision->format('d/m/Y') }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs rounded {{ $multa->estado_pago ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                {{ $multa->estado_pago ? 'Pagada' : 'Pendiente' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <button wire:click="openEdit({{ $multa->id }})"
                                        type="button"
                                        title="{{ __('Editar') }}"
                                        class="p-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick="
                                    Swal.fire({
                                        title: '¿Eliminar multa?',
                                        text: '¿Estás seguro de eliminar esta multa?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Sí, eliminar',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            @this.delete({{ $multa->id }});
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
                        <td colspan="7" class="py-8 px-4 text-center text-neutral-500">
                            {{ __('No hay registros') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginacion -->
        @if($multas->hasPages())
        <div x-show="!search" class="mt-6">
            {{ $multas->links() }}
        </div>
        @endif
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div wire:click="closeModal; resetForm" class="absolute inset-0 bg-black/10"></div>
        <div wire:click.stop
             class="bg-white dark:bg-neutral-800 rounded-xl shadow-2xl w-full max-w-md p-6 relative z-10">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modal-title" class="text-xl font-bold">
                    {{ $editingId ? __('Editar Multa') : __('Nueva Multa') }}
                </h3>
                <button wire:click="closeModal; resetForm" class="text-neutral-500 hover:text-neutral-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label for="comunero_search" class="block text-sm font-medium mb-2">{{ __('Buscar Comunero por DNI') }} *</label>
                    <div class="relative flex gap-2">
                        <input type="text"
                               id="comunero_search"
                               wire:model="searchCiudadano"
                               placeholder="{{ __('Ingresa el DNI...') }}"
                               autocomplete="off"
                               @keyup.enter="$wire.buscarComunero()"
                               {{ $isSearching ? 'disabled' : '' }}
                               class="flex-1 px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white {{ $isSearching ? 'opacity-50 cursor-not-allowed' : '' }}">

                        <button type="button"
                                wire:click="buscarComunero"
                                {{ $isSearching ? 'disabled' : '' }}
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center {{ $isSearching ? 'opacity-50 cursor-not-allowed' : '' }}">
                            @if($isSearching)
                                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            @endif
                        </button>
                    </div>

                    @if($showSearchResults && empty($selectedComuneroNombre))
                    <div class="mt-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        {{ __('No se encontró comunero con ese DNI') }}
                    </div>
                    @endif

                    @error('form.comunero_id')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="comunero_nombre" class="block text-sm font-medium mb-2">{{ __('Nombre Completo') }}</label>
                    <div class="relative">
                        <input type="text"
                               id="comunero_nombre"
                               value="{{ $selectedComuneroNombre }}"
                               readonly
                               placeholder="{{ __('Se llenará automáticamente') }}"
                               class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-neutral-100 dark:bg-neutral-700 dark:text-white text-neutral-600 dark:text-neutral-300 cursor-not-allowed {{ $form['comunero_id'] ? 'pr-10' : '' }}">
                        @if($form['comunero_id'])
                        <button type="button"
                                wire:click="limpiarComunero"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="monto" class="block text-sm font-medium mb-2">{{ __('Monto') }} *</label>
                    <input type="number"
                           step="0.01"
                           id="monto"
                           wire:model="form.monto"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                           required>
                    @error('form.monto')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="motivo" class="block text-sm font-medium mb-2">{{ __('Motivo') }}</label>
                    <input type="text"
                           id="motivo"
                           wire:model="form.motivo"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
                    @error('form.motivo')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="fecha_emision" class="block text-sm font-medium mb-2">{{ __('Fecha Emision') }} *</label>
                    <input type="date"
                           id="fecha_emision"
                           wire:model="form.fecha_emision"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                           required>
                    @error('form.fecha_emision')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center">
                        <input type="checkbox"
                               id="estado_pago"
                               wire:model="form.estado_pago"
                               class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        <label for="estado_pago" class="ml-2 text-sm font-medium">{{ __('Pagada') }}</label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button"
                            wire:click="closeModal; resetForm"
                            class="px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        {{ __('Guardar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</section>
