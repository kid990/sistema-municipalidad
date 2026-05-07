<section x-data="clientTableFilter()" class="flex h-full w-full flex-1 flex-col gap-4">
    <!-- Header -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <h1 class="text-2xl font-bold">{{ __('Usuarios') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-2">{{ __('Gestiona los usuarios del sistema') }}</p>
    </div>

    <!-- Lista y Botón -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">{{ __('Lista de Usuarios') }}</h2>
            <button wire:click="openNew"
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
                   placeholder="{{ __('Buscar usuarios por nombre o email...') }}"
                   class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Nombre') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Email') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Rol') }}</th>
                        <th class="text-left py-3 px-4 font-semibold">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr x-show="matches($el.dataset.filter)"
                        x-cloak
                        data-filter="{{ \Illuminate\Support\Str::lower($user->name.' '.$user->email.' '.$user->rol->label()) }}"
                        class="border-b border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                        <td class="py-3 px-4">{{ $user->name }}</td>
                        <td class="py-3 px-4">{{ $user->email }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $user->rol->label() }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <button wire:click="openEdit({{ $user->id }})" 
                                        type="button"
                                        title="{{ __('Editar') }}"
                                        class="p-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @if($user->id !== auth()->id())
                                <button onclick="
                                    Swal.fire({
                                        title: '¿Eliminar usuario?',
                                        text: '¿Estás seguro de eliminar este usuario?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Sí, eliminar',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            @this.delete({{ $user->id }});
                                        }
                                    })"
                                        type="button"
                                        title="{{ __('Eliminar') }}"
                                        class="p-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <td colspan="4" class="py-8 px-4 text-center text-neutral-500">
                            {{ __('No hay registros') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($users->hasPages())
        <div x-show="!search" class="mt-6">
            {{ $users->links() }}
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
                    {{ $editingId ? __('Editar Usuario') : __('Nuevo Usuario') }}
                </h3>
                <button wire:click="closeModal" class="text-neutral-500 hover:text-neutral-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium mb-2">{{ __('Nombre') }}</label>
                    <input type="text" 
                           id="name" 
                           wire:model="form.name"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                           required>
                    @error('form.name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-2">{{ __('Email') }}</label>
                    <input type="email" 
                           id="email" 
                           wire:model="form.email"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                           required>
                    @error('form.email')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="rol" class="block text-sm font-medium mb-2">{{ __('Rol') }}</label>
                    <select id="rol" 
                            wire:model="form.rol"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                            required>
                        <option value="">{{ __('Selecciona un rol') }}</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                        @endforeach
                    </select>
                    @error('form.rol')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                @if(!$editingId)
                <div>
                    <label for="password" class="block text-sm font-medium mb-2">{{ __('Contraseña') }}</label>
                    <div x-data="{ showPassword: false }" class="relative">
                        <input :type="showPassword ? 'text' : 'password'"
                               id="password"
                               wire:model="form.password"
                               class="w-full px-3 py-2 pr-10 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                               required>
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-2.5 text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300">
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 1.657-.672 3.157-1.757 4.243A6 6 0 0121 12a6 6 0 00-8.757-5.657"></path>
                                </svg>
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                        </button>
                    </div>
                    @error('form.password')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-2">{{ __('Confirmar Contraseña') }}</label>
                    <div x-data="{ showPasswordConfirmation: false }" class="relative">
                        <input :type="showPasswordConfirmation ? 'text' : 'password'"
                               id="password_confirmation"
                               wire:model="form.password_confirmation"
                               class="w-full px-3 py-2 pr-10 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                               required>
                        <button type="button"
                                @click="showPasswordConfirmation = !showPasswordConfirmation"
                                class="absolute right-3 top-2.5 text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300">
                                <svg x-show="showPasswordConfirmation" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 1.657-.672 3.157-1.757 4.243A6 6 0 0121 12a6 6 0 00-8.757-5.657"></path>
                                </svg>
                                <svg x-show="!showPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                        </button>
                    </div>
                    @error('form.password_confirmation')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
                @endif

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
