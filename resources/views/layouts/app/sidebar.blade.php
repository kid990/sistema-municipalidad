<!DOCTYPE html>
<html lang="es-PE" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Plataforma')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Panel') }}
                    </flux:sidebar.item>

                    @if(auth()->user()->isAdmin() || auth()->user()->isRegistrador())
                    <flux:sidebar.item icon="briefcase" :href="route('cargos.index')" :current="request()->routeIs('cargos.*')" wire:navigate>
                        {{ __('Cargos') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="users" :href="route('ciudadanos.index')" :current="request()->routeIs('ciudadanos.*')" wire:navigate>
                        {{ __('Ciudadanos') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="calendar" :href="route('gestiones.index')" :current="request()->routeIs('gestiones.*')" wire:navigate>
                        {{ __('Gestiones') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="map" :href="route('lotes.index')" :current="request()->routeIs('lotes.*')" wire:navigate>
                        {{ __('Lotes') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="home" :href="route('familias.index')" :current="request()->routeIs('familias.*')" wire:navigate>
                        {{ __('Familias') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="link" :href="route('ciudadano_cargo.index')" :current="request()->routeIs('ciudadano_cargo.*')" wire:navigate>
                        {{ __('Ciudadano Cargo') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="users" :href="route('comuneros.index')" :current="request()->routeIs('comuneros.*')" wire:navigate>
                        {{ __('Comuneros') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="briefcase" :href="route('faena.index')" :current="request()->routeIs('faena.index')" wire:navigate>
                        {{ __('Faenas') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="check-circle" :href="route('faena.asistencia')" :current="request()->routeIs('faena.asistencia')" wire:navigate>
                        {{ __('Asistencia') }}
                    </flux:sidebar.item>
                    @endif

                    @if(auth()->user()->isAdmin() || auth()->user()->isTesorero())
                    <flux:sidebar.item icon="currency-dollar" :href="route('multas.index')" :current="request()->routeIs('multas.*')" wire:navigate>
                        {{ __('Multas') }}
                    </flux:sidebar.item>


                    @endif

                    @if(auth()->user()->isAdmin())
                    <flux:sidebar.item icon="user-group" :href="route('usuarios.index')" :current="request()->routeIs('usuarios.*')" wire:navigate>
                        {{ __('Usuarios') }}
                    </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                    <flux:badge size="sm" color="zinc" inset="top bottom" class="mt-1">
                                        {{ auth()->user()->rol->label() }}
                                    </flux:badge>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Configuración') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Cerrar sesión') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('livewire:init', () => {
                // Toast de éxito (esquina superior derecha)
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    customClass: {
                        title: 'text-sm font-semibold'
                    },
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });

                // Escuchar todos los eventos de éxito
                const successEvents = [
                    'cargo-created', 'cargo-updated', 'cargo-deleted',
                    'gestion-created', 'gestion-updated', 'gestion-deleted',
                    'lote-created', 'lote-updated', 'lote-deleted',
                    'familia-created', 'familia-updated', 'familia-deleted',
                    'ciudadano-created', 'ciudadano-updated', 'ciudadano-deleted',
                    'asignacion-created', 'asignacion-updated', 'asignacion-deleted',
                    'comunero-created', 'comunero-deleted',
                    'multa-created', 'multa-updated', 'multa-deleted',
                    'user-created', 'user-updated', 'user-deleted',
                    'faena-created', 'faena-updated', 'faena-deleted',
                ];

                successEvents.forEach(event => {
                    Livewire.on(event, (data) => {
                        // data puede ser un objeto con 'message' o el primer elemento del array
                        const message = data?.message || (Array.isArray(data) ? data[0]?.message : null) || 'Operación exitosa';
                        Toast.fire({
                            icon: 'success',
                            title: message
                        });
                    });
                });

                // Toast de error genérico
                Livewire.on('error', (data) => {
                    const message = data?.message || (Array.isArray(data) ? data[0]?.message : null) || 'Ha ocurrido un error';
                    Toast.fire({
                        icon: 'error',
                        title: message
                    });
                });
            });
        </script>

        @fluxScripts
    </body>
</html>
