<section x-data="clientTableFilter()" class="flex h-full w-full flex-1 flex-col gap-4">
    <!-- Header -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <h1 class="text-2xl font-bold">{{ __('Asignación de Cargos') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-2">{{ __('Gestiona la asignación de cargos a ciudadanos') }}</p>
    </div>

    <!-- Lista y Botón -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">{{ __('Lista de Asignaciones') }}</h2>
            <button wire:click="openNew; resetForm"
                    type="button"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('Nuevo') }}
            </button>
        </div>

        <!-- Búsqueda -->
        <div class="mb-6">
            <input type="text" 
                   x-model.debounce.150ms="search"
                   @keydown.escape="resetSearch()"
                   placeholder="{{ __('Buscar por ciudadano o cargo...') }}"
                   class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Ciudadano') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Gestión') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Cargo') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Fecha Inicio') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Fecha Fin') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Estado') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $asignacion)
                    <tr x-show="matches($el.dataset.filter)"
                        x-cloak
                        data-filter="{{ \Illuminate\Support\Str::lower($asignacion->ciudadano->ape_paterno.' '.$asignacion->ciudadano->ape_materno.' '.$asignacion->ciudadano->nombres.' '.($asignacion->gestion?->nombre_gestion ?? '').' '.$asignacion->cargo->nombre.' '.$asignacion->fecha_inicio->format('d/m/Y').' '.($asignacion->fecha_fin ? $asignacion->fecha_fin->format('d/m/Y') : '').' '.($asignacion->activo ? 'Activo' : 'Inactivo')) }}"
                        class="border-b border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                        <td class="py-3 px-4">{{ $asignacion->ciudadano->ape_paterno }} {{ $asignacion->ciudadano->ape_materno }}, {{ $asignacion->ciudadano->nombres }}</td>
                        <td class="py-3 px-4">{{ $asignacion->gestion?->nombre_gestion ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $asignacion->cargo->nombre }}</td>
                        <td class="py-3 px-4">{{ $asignacion->fecha_inicio->format('d/m/Y') }}</td>
                        <td class="py-3 px-4">{{ $asignacion->fecha_fin ? $asignacion->fecha_fin->format('d/m/Y') : '-' }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs rounded {{ $asignacion->activo ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $asignacion->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <button wire:click="openEdit({{ $asignacion->id }})" 
                                        type="button"
                                        title="{{ __('Editar') }}"
                                        class="p-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $asignacion->id }})" 
                                        wire:confirm="¿Estás seguro de eliminar esta asignación?"
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

        <!-- Paginación -->
        @if($asignaciones->hasPages())
        <div x-show="!search" class="mt-6">
            {{ $asignaciones->links() }}
        </div>
        @endif
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div wire:click="closeModal" class="absolute inset-0 bg-black/10"></div>
        <div wire:click.stop
             class="bg-white dark:bg-neutral-800 rounded-xl shadow-2xl w-full max-w-md p-6 relative z-10 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modal-title" class="text-xl font-bold">
                    {{ $editingId ? __('Editar Asignación') : __('Nueva Asignación') }}
                </h3>
                <button wire:click="closeModal" class="text-neutral-500 hover:text-neutral-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label for="ciudadano_search" class="block text-sm font-medium mb-2">{{ __('Buscar Ciudadano por DNI') }} *</label>
                    
                    <!-- Input de búsqueda con lupa -->
                    <div class="relative flex gap-2">
                        <input type="text"
                               id="ciudadano_search"
                               wire:model="searchCiudadano"
                               placeholder="{{ __('Ingresa el DNI...') }}"
                               autocomplete="off"
                               @keyup.enter="$wire.buscarCiudadano()"
                               {{ $isSearching ? 'disabled' : '' }}
                               class="flex-1 px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white {{ $isSearching ? 'opacity-50 cursor-not-allowed' : '' }}">
                        
                        <!-- Botón lupa para buscar -->
                        <button type="button"
                                wire:click="buscarCiudadano"
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

                    <!-- Mensaje si no encuentra -->
                    @if($showSearchResults && empty($selectedCiudadanoNombre))
                    <div class="mt-2 px-3 py-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        {{ __('No se encontró ciudadano con ese DNI o no está activo') }}
                    </div>
                    @endif

                    @error('form.ciudadano_id')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Input de nombre completo readonly - SIEMPRE VISIBLE -->
                <div>
                    <label for="ciudadano_nombre" class="block text-sm font-medium mb-2">{{ __('Nombre Completo') }}</label>
                    <div class="relative">
                        <input type="text"
                               id="ciudadano_nombre"
                               value="{{ $selectedCiudadanoNombre }}"
                               readonly
                               placeholder="{{ __('Se llenará automáticamente') }}"
                               class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-neutral-100 dark:bg-neutral-700 dark:text-white text-neutral-600 dark:text-neutral-300 cursor-not-allowed {{ $selectedCiudadanoId ? 'pr-10' : '' }}">
                        @if($selectedCiudadanoId)
                        <button type="button"
                                wire:click="limpiarCiudadano"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="cargo_id" class="block text-sm font-medium mb-2">{{ __('Cargo') }} *</label>
                    <select id="cargo_id" 
                            wire:model="form.cargo_id"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                            required>
                        <option value="">{{ __('Selecciona un cargo') }}</option>
                        @foreach($cargos as $id => $nombre)
                            <option value="{{ $id }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                    @error('form.cargo_id')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="gestion_id" class="block text-sm font-medium mb-2">{{ __('Gestión') }} *</label>
                    <select id="gestion_id"
                            wire:model="form.gestion_id"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                            required>
                        <option value="">{{ __('Selecciona una gestión') }}</option>
                        @foreach($gestiones as $id => $nombre)
                            <option value="{{ $id }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                    @error('form.gestion_id')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium mb-2">{{ __('Fecha Inicio') }} *</label>
                    <input type="date" 
                           id="fecha_inicio" 
                           wire:model="form.fecha_inicio"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                           required>
                    @error('form.fecha_inicio')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="fecha_fin" class="block text-sm font-medium mb-2">{{ __('Fecha Fin') }}</label>
                    <input type="date" 
                           id="fecha_fin" 
                           wire:model="form.fecha_fin"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
                    @error('form.fecha_fin')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="activo" 
                               wire:model="form.activo"
                               class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        <label for="activo" class="ml-2 text-sm font-medium">{{ __('Activo') }}</label>
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
