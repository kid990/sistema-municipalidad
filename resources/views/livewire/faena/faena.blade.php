<section x-data="clientTableFilter()" class="flex h-full w-full flex-1 flex-col gap-4">
    <!-- Header -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <h1 class="text-2xl font-bold">{{ __('Gestión de Faenas') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-2">{{ __('Administra faenas, fechas y asistencias') }}</p>
    </div>

    <!-- Alertas -->
    @if (session()->has('success'))
    <div class="rounded-xl border border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-900/20 p-4">
        <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="rounded-xl border border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-900/20 p-4">
        <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Lista y Botón -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">{{ __('Lista de Faenas') }}</h2>
            <button wire:click="openModal('create')"
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
                   placeholder="{{ __('Buscar faenas...') }}"
                   class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <th class="text-left py-3 px-4 font-semibold">{{ __('ID') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Nombre') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Fechas') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Multa por falta') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faenas as $faena)
                    <tr x-show="matches($el.dataset.filter)"
                        x-cloak
                        data-filter="{{ \Illuminate\Support\Str::lower($faena->id.' '.$faena->nombre.' '.$faena->cantidad_dias.' '.$faena->costo_multa_inasistencia) }}"
                        class="border-b border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                        <td class="py-3 px-4">{{ $faena->id }}</td>
                        <td class="py-3 px-4">{{ $faena->nombre }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $faena->cantidad_dias }}
                            </span>
                        </td>
                        <td class="py-3 px-4">S/ {{ number_format($faena->costo_multa_inasistencia, 2) }}</td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <button wire:click="openModal('dates', {{ $faena->id }})" 
                                        type="button"
                                        title="{{ __('Agregar fechas') }}"
                                        class="p-2 bg-amber-500 text-white rounded hover:bg-amber-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                <button wire:click="openModal('sync', {{ $faena->id }})" 
                                        type="button"
                                        title="{{ __('Completar registros') }}"
                                        class="p-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                                <button wire:click="openModal('view', {{ $faena->id }})" 
                                        type="button"
                                        title="{{ __('Ver estadísticas') }}"
                                        class="p-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <button onclick="
                                    Swal.fire({
                                        title: '¿Eliminar faena?',
                                        text: 'Se eliminarán también los registros de asistencia.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Sí, eliminar',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            @this.eliminarFaena({{ $faena->id }});
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
        @if($faenas->hasPages())
        <div x-show="!search" class="mt-6">
            {{ $faenas->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Crear Faena -->
    @if($activeModal === 'create')
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="bg-white dark:bg-neutral-800 rounded-xl p-6 w-full max-w-md shadow-xl">
            <h3 id="modal-title" class="text-lg font-bold mb-4">{{ __('Faena') }}</h3>
            
            <form wire:submit.prevent="crearFaena" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">{{ __('Nombre') }}</label>
                    <input type="text" 
                           wire:model="nombre"
                           placeholder="{{ __('Nombre de la faena') }}"
                           class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg dark:bg-neutral-700 dark:text-white">
                    @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">{{ __('Costo de multa por inasistencia') }}</label>
                    <input type="number" 
                           wire:model="costo_multa_inasistencia"
                           min="0"
                           step="0.01"
                           placeholder="{{ __('50.00') }}"
                           class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg dark:bg-neutral-700 dark:text-white">
                    @error('costo_multa_inasistencia') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2 pt-4">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        {{ __('Crear') }}
                    </button>
                    <button type="button" wire:click="closeModal" class="flex-1 px-4 py-2 bg-neutral-300 text-neutral-900 rounded-lg hover:bg-neutral-400">
                        {{ __('Cancelar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Agregar Fechas -->
    @if($activeModal === 'dates' && $selectedFaenaId)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="bg-white dark:bg-neutral-800 rounded-xl p-6 w-full max-w-lg shadow-xl my-auto">
            <h3 id="modal-title" class="text-lg font-bold mb-2">{{ __('Agregar Fechas') }} - <span class="text-blue-600">{{ $selectedFaena?->nombre }}</span></h3>
            <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-4">
                {{ __('Fechas registradas: ') }}<strong>{{ $selectedFaena?->cantidad_dias }}</strong> |
                {{ __('Fechas pendientes: ') }}<strong class="text-amber-600">{{ count($fechasTemp) }}</strong>
            </p>

            <!-- Input para agregar fechas -->
            <div class="flex items-end gap-2 mb-4">
                <div class="flex-1">
                    <label class="block text-xs font-medium mb-1">{{ __('Seleccionar fecha') }}</label>
                    <input type="date"
                           wire:model.live="nuevaFecha"
                           wire:keydown.enter="agregarUnaFecha"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg dark:bg-neutral-700 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                    @error('nuevaFecha')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <button type="button"
                        wire:click="agregarUnaFecha"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium whitespace-nowrap">
                    {{ __('+ Agregar') }}
                </button>
            </div>

            <!-- Lista de fechas pendientes -->
            @if(count($fechasTemp) > 0)
            <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg max-h-64 overflow-y-auto">
                <div class="bg-neutral-100 dark:bg-neutral-700 px-3 py-2 text-xs font-semibold text-neutral-600 dark:text-neutral-300">
                    {{ __('Fechas seleccionadas (pendientes de registro)') }}
                </div>
                <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach($fechasTemp as $index => $fecha)
                    <div class="flex items-center justify-between px-3 py-2 hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
                        </div>
                        <button type="button"
                                wire:click="quitarFechaTemporal({{ $index }})"
                                class="text-red-500 hover:text-red-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="border border-dashed border-neutral-300 dark:border-neutral-600 rounded-lg p-6 text-center text-neutral-500 dark:text-neutral-400 text-sm">
                {{ __('Agrega fechas para registrar asistencias') }}
            </div>
            @endif

            <!-- Mensaje de error general -->
            @error('newFechas')
            <div class="mt-4 text-red-500 text-sm p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">{{ $message }}</div>
            @enderror

            <div class="flex gap-2 pt-4 mt-4 border-t border-neutral-200 dark:border-neutral-700">
                <button type="button" wire:click="closeModal" class="flex-1 px-4 py-2 bg-neutral-300 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded-lg hover:bg-neutral-400 dark:hover:bg-neutral-500 transition-colors">
                    {{ __('Cancelar') }}
                </button>
                <button type="button"
                        onclick="
                            Swal.fire({
                                title: '¿Registrar fechas?',
                                text: 'Se registrarán {{ count($fechasTemp) }} fecha(s) y se crearán registros para todos los comuneros activos.',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#d97706',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Sí, registrar',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    @this.agregarFechas();
                                }
                            })"
                        class="flex-1 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors font-medium {{ count($fechasTemp) === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ count($fechasTemp) === 0 ? 'disabled' : '' }}>
                    {{ __('Registrar') }}
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Completar Registros -->
    @if($activeModal === 'sync' && $selectedFaenaId)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="bg-white dark:bg-neutral-800 rounded-xl p-6 w-full max-w-md shadow-xl">
            <h3 id="modal-title" class="text-lg font-bold mb-4">{{ __('Completar registros') }}</h3>
            <p class="text-neutral-600 dark:text-neutral-400 mb-6">{{ __('Se crearán solo los registros faltantes para las fechas de esta faena y los comuneros activos.') }}</p>
            
            <div class="flex gap-2">
                <button wire:click="completarRegistros({{ $selectedFaenaId }})" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    {{ __('Completar registros') }}
                </button>
                <button wire:click="closeModal" class="flex-1 px-4 py-2 bg-neutral-300 text-neutral-900 rounded-lg hover:bg-neutral-400">
                    {{ __('Cancelar') }}
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Ver Detalles -->
    @if($activeModal === 'view' && $selectedFaenaId)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="bg-white dark:bg-neutral-800 rounded-xl p-6 w-full max-w-4xl shadow-xl my-auto">
            <h3 id="modal-title" class="text-lg font-bold mb-2">{{ __('Estadísticas') }} - <span class="text-blue-600">{{ $selectedFaena?->nombre }}</span></h3>
            
            <!-- Resumen General -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                    <p class="text-xs text-green-600 dark:text-green-400 font-medium">{{ __('Asistió') }}</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $estadisticasGenerales['asistio'] }}</p>
                </div>
                <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                    <p class="text-xs text-red-600 dark:text-red-400 font-medium">{{ __('Faltó') }}</p>
                    <p class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $estadisticasGenerales['falto'] }}</p>
                </div>
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                    <p class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">{{ __('Justificado') }}</p>
                    <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $estadisticasGenerales['justificado'] }}</p>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">{{ __('Total') }}</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $estadisticasGenerales['total'] }}</p>
                </div>
            </div>

            <!-- Tabla de Estadísticas por Fecha -->
            <h4 class="text-sm font-semibold mb-3">{{ __('Desglose por Fecha') }}</h4>
            <div class="overflow-x-auto max-h-96 overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                <table class="w-full text-sm border-collapse">
                    <thead class="sticky top-0">
                        <tr class="bg-neutral-100 dark:bg-neutral-700">
                            <th class="text-left py-3 px-4 border border-neutral-200 dark:border-neutral-600">{{ __('Fecha') }}</th>
                            <th class="text-center py-3 px-4 border border-neutral-200 dark:border-neutral-600 bg-green-50 dark:bg-green-900/20">{{ __('Asistió') }}</th>
                            <th class="text-center py-3 px-4 border border-neutral-200 dark:border-neutral-600 bg-red-50 dark:bg-red-900/20">{{ __('Faltó') }}</th>
                            <th class="text-center py-3 px-4 border border-neutral-200 dark:border-neutral-600 bg-yellow-50 dark:bg-yellow-900/20">{{ __('Justificado') }}</th>
                            <th class="text-center py-3 px-4 border border-neutral-200 dark:border-neutral-600 bg-blue-50 dark:bg-blue-900/20">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estadisticasPorFecha as $stat)
                        <tr class="border-b border-neutral-200 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-700">
                            <td class="py-3 px-4 border border-neutral-200 dark:border-neutral-600 font-medium">
                                {{ \Carbon\Carbon::parse($stat['fecha'])->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 border border-neutral-200 dark:border-neutral-600 text-center font-semibold text-green-600 dark:text-green-400">
                                {{ $stat['asistio'] }}
                            </td>
                            <td class="py-3 px-4 border border-neutral-200 dark:border-neutral-600 text-center font-semibold text-red-600 dark:text-red-400">
                                {{ $stat['falto'] }}
                            </td>
                            <td class="py-3 px-4 border border-neutral-200 dark:border-neutral-600 text-center font-semibold text-yellow-600 dark:text-yellow-400">
                                {{ $stat['justificado'] }}
                            </td>
                            <td class="py-3 px-4 border border-neutral-200 dark:border-neutral-600 text-center font-semibold text-blue-600 dark:text-blue-400">
                                {{ $stat['total'] }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 px-4 text-center text-neutral-500">
                                {{ __('No hay registros de asistencia') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <button wire:click="closeModal" class="w-full mt-6 px-4 py-2 bg-neutral-300 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded-lg hover:bg-neutral-400 dark:hover:bg-neutral-500 transition-colors font-medium">
                {{ __('Cerrar') }}
            </button>
        </div>
    </div>
    @endif

</section>
