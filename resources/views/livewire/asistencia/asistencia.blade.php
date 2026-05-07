<section x-data="clientTableFilter()" class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <h1 class="text-2xl font-bold">{{ __('Registros de Asistencia') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-2">{{ __('Gestiona la asistencia de comuneros en las faenas') }}</p>
    </div>

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

    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">{{ __('Faena') }}</label>
                <select wire:model.live="selectedFaena" class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
                    <option value="">{{ __('Todas las faenas') }}</option>
                    @foreach($faenas as $faena)
                    <option value="{{ $faena->id }}">{{ $faena->nombre_actividad }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">{{ __('Fecha') }}</label>
                <select wire:model.live="selectedFecha" {{ empty($selectedFaena) ? 'disabled' : '' }} class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                    <option value="">{{ __('Todas las fechas') }}</option>
                    @foreach($fechas as $fecha)
                    <option value="{{ $fecha }}">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">{{ __('Buscar Comunero') }}</label>
                <input type="text" x-model.debounce.150ms="search" @keydown.escape="resetSearch()" placeholder="{{ __('Nombre o apellido...') }}" class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
            </div>

            <div class="flex items-end">
                <button wire:click="resetearFiltros" @click="resetSearch()" class="w-full px-4 py-2 bg-neutral-600 text-white rounded-lg hover:bg-neutral-700 transition-colors font-medium">
                    {{ __('Limpiar Filtros') }}
                </button>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Faena') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Fecha') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Comunero') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Estado') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Accion') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asistencias as $registro)
                    <tr x-show="matches($el.dataset.filter)"
                        x-cloak
                        data-filter="{{ \Illuminate\Support\Str::lower(($registro->fechaFaena?->faena?->nombre_actividad ?? 'Sin faena').' '.\Carbon\Carbon::parse($registro->fechaFaena?->fecha_realizacion)->format('d/m/Y').' '.($registro->comunero?->ciudadano?->nombres ?? 'Sin comunero').' '.($registro->comunero?->ciudadano?->ape_paterno ?? '').' '.($registro->comunero?->ciudadano?->ape_materno ?? '').' '.$registro->estado_asistencia) }}"
                        class="border-b border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                        <td class="py-3 px-4 font-medium">{{ $registro->fechaFaena?->faena?->nombre_actividad ?? 'Sin faena' }}</td>
                        <td class="py-3 px-4">{{ \Carbon\Carbon::parse($registro->fechaFaena?->fecha_realizacion)->format('d/m/Y') }}</td>
                        <td class="py-3 px-4">
                            {{ $registro->comunero?->ciudadano?->nombres ?? 'Sin comunero' }}
                            <span class="text-neutral-500 dark:text-neutral-400 text-sm block">{{ ($registro->comunero?->ciudadano?->ape_paterno ?? '') . ' ' . ($registro->comunero?->ciudadano?->ape_materno ?? '') }}</span>
                        </td>
                        <td class="py-3 px-4">
                            @php
                                $estadoClases = match($registro->estado_asistencia) {
                                    'Asistio' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'Falto' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'Justificado' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full font-medium {{ $estadoClases }}">
                                {{ match($registro->estado_asistencia) {
                                    'Asistio' => 'Asistió',
                                    'Falto' => 'Faltó',
                                    default => $registro->estado_asistencia,
                                } }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <select wire:change="actualizarAsistencia({{ $registro->id }}, $event.target.value)" class="px-3 py-1 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white text-sm font-medium">
                                <option value="Falto" {{ $registro->estado_asistencia === 'Falto' ? 'selected' : '' }}>{{ __('Faltó') }}</option>
                                <option value="Asistio" {{ $registro->estado_asistencia === 'Asistio' ? 'selected' : '' }}>{{ __('Asistió') }}</option>
                                <option value="Justificado" {{ $registro->estado_asistencia === 'Justificado' ? 'selected' : '' }}>{{ __('Justificado') }}</option>
                            </select>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 px-4 text-center text-neutral-500">{{ __('No hay registros de asistencia disponibles') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($asistencias->hasPages())
        <div x-show="!search" class="mt-6">
            {{ $asistencias->links() }}
        </div>
        @endif
    </div>
</section>
