<section x-data="clientTableFilter()" class="flex h-full w-full flex-1 flex-col gap-4">
    <!-- Header -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <h1 class="text-2xl font-bold">{{ __('Gestiones') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-2">{{ __('Gestiona las gestiones del sistema') }}</p>
    </div>

    <!-- Lista y Boton -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">{{ __('Lista de Gestiones') }}</h2>
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
                   placeholder="{{ __('Buscar gestiones...') }}"
                   class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Nombre') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Año Inicio') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Año Fin') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Estado') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gestiones as $gestion)
                    <tr x-show="matches($el.dataset.filter)"
                        x-cloak
                        data-filter="{{ \Illuminate\Support\Str::lower($gestion->nombre_gestion.' '.$gestion->fecha_inicio->format('Y').' '.($gestion->fecha_fin ? $gestion->fecha_fin->format('Y') : '')) }}"
                        class="border-b border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                        <td class="py-3 px-4">{{ $gestion->nombre_gestion }}</td>
                        <td class="py-3 px-4">{{ $gestion->fecha_inicio->format('Y') }}</td>
                        <td class="py-3 px-4">{{ $gestion->fecha_fin ? $gestion->fecha_fin->format('Y') : '-' }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs rounded {{ $gestion->estado_gestion ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $gestion->estado_gestion ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <button wire:click="openEdit({{ $gestion->id }})"
                                        type="button"
                                        title="{{ __('Editar') }}"
                                        class="p-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick="
                                    Swal.fire({
                                        title: '¿Eliminar gestión?',
                                        text: '¿Estás seguro de eliminar esta gestión?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Sí, eliminar',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            @this.delete({{ $gestion->id }});
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

        <!-- Paginacion -->
        @if($gestiones->hasPages())
        <div x-show="!search" class="mt-6">
            {{ $gestiones->links() }}
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
                    {{ $editingId ? __('Editar Gestion') : __('Nueva Gestion') }}
                </h3>
                <button wire:click="closeModal" class="text-neutral-500 hover:text-neutral-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label for="nombre_gestion" class="block text-sm font-medium mb-2">{{ __('Nombre') }}</label>
                    <input type="text"
                           id="nombre_gestion"
                           wire:model="form.nombre_gestion"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                           required>
                    @error('form.nombre_gestion')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="anio_inicio" class="block text-sm font-medium mb-2">{{ __('Año Inicio') }}</label>
                        <select id="anio_inicio"
                                wire:model="form.anio_inicio"
                                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                                required>
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        @error('form.anio_inicio')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="anio_fin" class="block text-sm font-medium mb-2">{{ __('Año Fin') }}</label>
                        <select id="anio_fin"
                                wire:model="form.anio_fin"
                                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
                            <option value="">{{ __('Sin fecha fin') }}</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        @error('form.anio_fin')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox"
                           id="estado_gestion"
                           wire:model="form.estado_gestion"
                           class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                    <label for="estado_gestion" class="ml-2 text-sm font-medium">{{ __('Activa') }}</label>
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
