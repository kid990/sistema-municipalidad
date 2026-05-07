<x-layouts::app :title="__('Panel')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Header -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-100 dark:bg-neutral-800 p-8 text-neutral-900 dark:text-neutral-100">
            <h1 class="text-3xl font-bold mb-2">{{ __('Panel de Control') }}</h1>
            <p class="text-neutral-600 dark:text-neutral-300">{{ __('Bienvenido') }}, {{ auth()->user()->name }}</p>
        </div>

        <!-- Estadísticas -->
        <div class="grid gap-4 md:grid-cols-3">
            <!-- Total Ciudadanos -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-1">{{ __('Total Ciudadanos') }}</p>
                        <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ \App\Models\Ciudadano::count() }}</p>
                    </div>
                    <div class="p-3 bg-neutral-100 dark:bg-neutral-700 rounded-full">
                        <svg class="w-8 h-8 text-neutral-700 dark:text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-xs text-neutral-600 dark:text-neutral-300">
                        {{ \App\Models\Comunero::where('estado_comunero', 'Activo')->count() }} {{ __('comuneros activos') }}
                    </span>
                </div>
            </div>

            <!-- Total Cargos -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-1">{{ __('Total Cargos') }}</p>
                        <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ \App\Models\Cargo::count() }}</p>
                    </div>
                    <div class="p-3 bg-neutral-100 dark:bg-neutral-700 rounded-full">
                        <svg class="w-8 h-8 text-neutral-700 dark:text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-xs text-neutral-600 dark:text-neutral-300">
                        {{ \App\Models\Cargo::count() }} {{ __('registrados') }}
                    </span>
                </div>
            </div>

            <!-- Asignaciones Activas -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-1">{{ __('Asignaciones Activas') }}</p>
                        <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ \App\Models\CiudadanoCargo::where('estado_asignacion', 'Vigente')->count() }}</p>
                    </div>
                    <div class="p-3 bg-neutral-100 dark:bg-neutral-700 rounded-full">
                        <svg class="w-8 h-8 text-neutral-700 dark:text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-xs text-neutral-600 dark:text-neutral-400">
                        {{ \App\Models\CiudadanoCargo::count() }} {{ __('total') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos y Actividad Reciente -->
        <div class="grid gap-4 md:grid-cols-2">
            <!-- Accesos Rápidos -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <h2 class="text-lg font-bold mb-4">{{ __('Accesos Rápidos') }}</h2>
                <div class="space-y-3">
                    <a href="{{ route('ciudadanos.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                        <div class="p-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg">
                            <svg class="w-5 h-5 text-neutral-700 dark:text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">{{ __('Gestión de Ciudadanos') }}</p>
                            <p class="text-xs text-neutral-600 dark:text-neutral-400">{{ __('Ver y gestionar ciudadanos') }}</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('cargos.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                        <div class="p-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg">
                            <svg class="w-5 h-5 text-neutral-700 dark:text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">{{ __('Gestión de Cargos') }}</p>
                            <p class="text-xs text-neutral-600 dark:text-neutral-400">{{ __('Ver y gestionar cargos') }}</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('ciudadano_cargo.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                        <div class="p-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg">
                            <svg class="w-5 h-5 text-neutral-700 dark:text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">{{ __('Asignación de Cargos') }}</p>
                            <p class="text-xs text-neutral-600 dark:text-neutral-400">{{ __('Asignar cargos a ciudadanos') }}</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <h2 class="text-lg font-bold mb-4">{{ __('Últimas Asignaciones') }}</h2>
                <div class="space-y-3">
                    @forelse(\App\Models\CiudadanoCargo::with(['ciudadano', 'cargo'])->orderByDesc('id')->limit(5)->get() as $asignacion)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-700/50">
                        <div class="flex-1">
                            <p class="font-medium text-sm">{{ $asignacion->ciudadano->nombres }} {{ $asignacion->ciudadano->ape_paterno }}</p>
                            <p class="text-xs text-neutral-600 dark:text-neutral-400">{{ $asignacion->cargo->nombre }}</p>
                        </div>
                        <div class="text-xs text-neutral-500">
                            {{ $asignacion->fecha_inicio?->format('d/m/Y') }}
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-neutral-500 py-8">{{ __('No hay asignaciones recientes') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
