<section x-data="clientTableFilter()" class="flex h-full w-full flex-1 flex-col gap-4">
    <!-- Header -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <h1 class="text-2xl font-bold">{{ __('Lotes') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-2">{{ __('Gestiona los lotes del sistema') }}</p>
    </div>

    <!-- Lista y Boton -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">{{ __('Lista de Lotes') }}</h2>
            <button wire:click="openNew"
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
                   placeholder="{{ __('Buscar por lote, manzana o referencia...') }}"
                   class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Lote') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Manzana') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Área (m²)') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Referencia') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Estado') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lotes as $lote)
                    <tr x-show="matches($el.dataset.filter)"
                        x-cloak
                        data-filter="{{ \Illuminate\Support\Str::lower($lote->numero_lote.' '.$lote->manzana.' '.$lote->referencia_ubicacion.' '.$lote->estado) }}"
                        class="border-b border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                        <td class="py-3 px-4">{{ $lote->numero_lote }}</td>
                        <td class="py-3 px-4">{{ $lote->manzana ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $lote->area_m2 ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $lote->referencia_ubicacion ?? '-' }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs rounded {{ $lote->estado === 'Habitado' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($lote->estado === 'Desocupado' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                {{ $lote->estado }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <button wire:click="openEdit({{ $lote->id }})"
                                        type="button"
                                        title="{{ __('Editar') }}"
                                        class="p-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick="
                                    Swal.fire({
                                        title: '¿Eliminar lote?',
                                        text: '¿Estás seguro de eliminar este lote?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Sí, eliminar',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            @this.delete({{ $lote->id }});
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
                        <td colspan="6" class="py-8 px-4 text-center text-neutral-500">
                            {{ __('No hay registros') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginacion -->
        @if($lotes->hasPages())
        <div x-show="!search" class="mt-6">
            {{ $lotes->links() }}
        </div>
        @endif
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div wire:click="closeModal" class="absolute inset-0 bg-black/10"></div>
        <div wire:click.stop
             class="bg-white dark:bg-neutral-800 rounded-xl shadow-2xl w-full max-w-md p-6 relative z-10">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modal-title" class="text-xl font-bold">
                    {{ $editingId ? __('Editar Lote') : __('Nuevo Lote') }}
                </h3>
                <button wire:click="closeModal" class="text-neutral-500 hover:text-neutral-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label for="numero_lote" class="block text-sm font-medium mb-2">{{ __('Número de Lote') }} *</label>
                    <input type="text"
                           id="numero_lote"
                           wire:model="form.numero_lote"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                           required>
                    @error('form.numero_lote')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="manzana" class="block text-sm font-medium mb-2">{{ __('Manzana') }}</label>
                    <input type="text"
                           id="manzana"
                           wire:model="form.manzana"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
                    @error('form.manzana')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="area_m2" class="block text-sm font-medium mb-2">{{ __('Área (m²)') }}</label>
                    <input type="number"
                           step="0.01"
                           id="area_m2"
                           wire:model="form.area_m2"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
                    @error('form.area_m2')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="referencia_ubicacion" class="block text-sm font-medium mb-2">{{ __('Referencia') }}</label>
                    <textarea id="referencia_ubicacion"
                              wire:model="form.referencia_ubicacion"
                              rows="3"
                              class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"></textarea>
                    @error('form.referencia_ubicacion')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="estado" class="block text-sm font-medium mb-2">{{ __('Estado') }} *</label>
                    <select id="estado"
                            wire:model="form.estado"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                            required>
                        <option value="Habitado">{{ __('Habitado') }}</option>
                        <option value="Desocupado">{{ __('Desocupado') }}</option>
                        <option value="En Litigio">{{ __('En Litigio') }}</option>
                    </select>
                    @error('form.estado')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button"
                            wire:click="closeModal"
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
