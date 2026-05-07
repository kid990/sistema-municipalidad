<section x-data="clientTableFilter()" class="flex h-full w-full flex-1 flex-col gap-4">
    <!-- Header -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <h1 class="text-2xl font-bold">{{ __('Ciudadanos') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-2">{{ __('Gestiona los ciudadanos del sistema') }}</p>
    </div>

    <!-- Lista y Botón -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">{{ __('Lista de Ciudadanos') }}</h2>
            <div class="flex gap-3">
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
                   placeholder="{{ __('Buscar por DNI, nombre, apellido o email...') }}"
                   class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                         <th class="text-left py-3 px-4 font-semibold">{{ __('DNI') }}</th>
                         <th class="text-left py-3 px-4 font-semibold">{{ __('Nombres Completos') }}</th>
                         <th class="text-left py-3 px-4 font-semibold">{{ __('Email') }}</th>
                         <th class="text-left py-3 px-4 font-semibold">{{ __('Teléfono') }}</th>
                         <th class="text-left py-3 px-4 font-semibold">{{ __('Género') }}</th>
                         <th class="text-left py-3 px-4 font-semibold">{{ __('Estado') }}</th>
                         <th class="text-left py-3 px-4 font-semibold">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ciudadanos as $ciudadano)
                     <tr x-show="matches($el.dataset.filter)"
                         x-cloak
                         data-filter="{{ \Illuminate\Support\Str::lower($ciudadano->dni.' '.$ciudadano->ape_paterno.' '.$ciudadano->ape_materno.' '.$ciudadano->nombres.' '.$ciudadano->email.' '.$ciudadano->telefono.' '.$ciudadano->genero.' '.$ciudadano->direccion_referencia.' '.$ciudadano->estado) }}"
                         class="border-b border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                         <td class="py-3 px-4">{{ $ciudadano->dni }}</td>
                         <td class="py-3 px-4">{{ $ciudadano->ape_paterno }} {{ $ciudadano->ape_materno }}, {{ $ciudadano->nombres }}</td>
                         <td class="py-3 px-4">{{ $ciudadano->email ?? '-' }}</td>
                         <td class="py-3 px-4">{{ $ciudadano->telefono ?? '-' }}</td>
                         <td class="py-3 px-4">{{ $ciudadano->genero ?? '-' }}</td>
                         <td class="py-3 px-4">
                             <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $ciudadano->estado === 'Activo' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                 {{ $ciudadano->estado }}
                             </span>
                         </td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <button wire:click="openEdit({{ $ciudadano->id }})"
                                        type="button"
                                        title="{{ __('Editar') }}"
                                        class="p-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $ciudadano->id }})"
                                        wire:confirm="¿Estás seguro de eliminar este ciudadano?"
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
        @if($ciudadanos->hasPages())
        <div x-show="!search" class="mt-6">
            {{ $ciudadanos->links() }}
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
                <h3 id="modal-title" class="text-xl font-bold">
                    {{ $editingId ? __('Editar Ciudadano') : __('Nuevo Ciudadano') }}
                </h3>
                <button wire:click="closeModal" class="text-neutral-500 hover:text-neutral-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="dni" class="block text-sm font-medium mb-2">{{ __('DNI') }} *</label>
                        <div class="flex gap-2">
                            <input type="text"
                                   id="dni"
                                   wire:model="form.dni"
                                   class="flex-1 px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                                   maxlength="8"
                                   required>
                            <button type="button"
                                    wire:click="validarDniConReniec"
                                    wire:loading.attr="disabled"
                                    class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400"
                                    title="Validar DNI con RENIEC">
                                <span wire:loading.remove>🔍</span>
                                <span wire:loading>...</span>
                            </button>
                        </div>
                        @if($dniValidationMessage)
                            <div class="mt-2 text-sm {{ $dniValidationStatus === 'success' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $dniValidationMessage }}
                            </div>
                        @endif
                        @error('form.dni')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="nombres" class="block text-sm font-medium mb-2">{{ __('Nombres') }} *</label>
                        <input type="text"
                               id="nombres"
                               wire:model="form.nombres"
                               wire:key="nombres-{{ $dniValidated ? '1' : '0' }}"
                               {{ $dniValidated ? 'readonly' : '' }}
                               class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white {{ $dniValidated ? 'bg-neutral-100 dark:bg-neutral-800 cursor-not-allowed' : '' }}"
                               maxlength="100"
                               required>
                        @error('form.nombres')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="ape_paterno" class="block text-sm font-medium mb-2">{{ __('Apellido Paterno') }} *</label>
                        <input type="text"
                               id="ape_paterno"
                               wire:model="form.ape_paterno"
                               wire:key="ape_paterno-{{ $dniValidated ? '1' : '0' }}"
                               {{ $dniValidated ? 'readonly' : '' }}
                               class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white {{ $dniValidated ? 'bg-neutral-100 dark:bg-neutral-800 cursor-not-allowed' : '' }}"
                               maxlength="60"
                               required>
                        @error('form.ape_paterno')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="ape_materno" class="block text-sm font-medium mb-2">{{ __('Apellido Materno') }} *</label>
                        <input type="text"
                               id="ape_materno"
                               wire:model="form.ape_materno"
                               wire:key="ape_materno-{{ $dniValidated ? '1' : '0' }}"
                               {{ $dniValidated ? 'readonly' : '' }}
                               class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white {{ $dniValidated ? 'bg-neutral-100 dark:bg-neutral-800 cursor-not-allowed' : '' }}"
                               maxlength="60"
                               required>
                        @error('form.ape_materno')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="fecha_nacimiento" class="block text-sm font-medium mb-2">{{ __('Fecha de Nacimiento') }}</label>
                        <input type="date"
                               id="fecha_nacimiento"
                               wire:model="form.fecha_nacimiento"
                               class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
                        @error('form.fecha_nacimiento')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="genero" class="block text-sm font-medium mb-2">{{ __('GÃ©nero') }}</label>
                        <select id="genero"
                                wire:model="form.genero"
                                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
                            <option value="">{{ __('Selecciona') }}</option>
                            <option value="M">M</option>
                            <option value="F">F</option>
                            <option value="Otro">Otro</option>
                        </select>
                        @error('form.genero')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-medium mb-2">{{ __('Teléfono') }}</label>
                        <input type="text"
                               id="telefono"
                               wire:model="form.telefono"
                               class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                               maxlength="15">
                        @error('form.telefono')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium mb-2">{{ __('Email') }}</label>
                        <input type="email"
                               id="email"
                               wire:model="form.email"
                               class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                               maxlength="100">
                        @error('form.email')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                         <label for="direccion_referencia" class="block text-sm font-medium mb-2">{{ __('Dirección/Referencia') }}</label>
                         <textarea id="direccion_referencia"
                                   wire:model="form.direccion_referencia"
                                   rows="3"
                                   class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"></textarea>
                         @error('form.direccion_referencia')
                             <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                         @enderror
                     </div>

                     <div>
                         <label for="estado" class="block text-sm font-medium mb-2">{{ __('Estado') }} *</label>
                         <select id="estado"
                                 wire:model="form.estado"
                                 class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                                 required>
                             <option value="">{{ __('Selecciona') }}</option>
                             <option value="Activo">{{ __('Activo') }}</option>
                             <option value="Inactivo">{{ __('Inactivo') }}</option>
                         </select>
                         @error('form.estado')
                             <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                         @enderror
                     </div>
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
