<?php

use App\Livewire\Asistencia\Asistencia;
use App\Livewire\Cargos\Index as CargosIndex;
use App\Livewire\Gestiones\Index as GestionesIndex;
use App\Livewire\Lotes\Index as LotesIndex;
use App\Livewire\Familias\Index as FamiliasIndex;
use App\Livewire\CiudadanoCargo\Index as CiudadanoCargoIndex;
use App\Livewire\Ciudadanos\Index as CiudadanosIndex;
use App\Livewire\Comuneros\Index as ComunerosIndex;
use App\Livewire\Faena\Faena;
use App\Livewire\Multas\Index as MultasIndex;
use App\Livewire\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Route;

// Página de inicio - Login
Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Rutas para Admin, Registrador y Tesorero
    Route::middleware('role:admin,registrador')->group(function () {

        // Rutas de Ciudadanos (Livewire)
        Route::get('ciudadanos', CiudadanosIndex::class)->name('ciudadanos.index');

        // Rutas de Lotes (Livewire)
        Route::get('lotes', LotesIndex::class)->name('lotes.index');

        // Rutas de Familias (Livewire)
        Route::get('familias', FamiliasIndex::class)->name('familias.index');

        // Rutas de Comuneros (Livewire)
        Route::get('comuneros', ComunerosIndex::class)->name('comuneros.index');

        // Rutas de Faenas (Livewire)
        Route::get('faena', Faena::class)->name('faena.index');

        // Asistencia (Livewire)
        Route::get('asistencia', Asistencia::class)->name('faena.asistencia');
    });

    // Rutas para Admin y Tesorero (Multas y Asistencia)
    Route::middleware('role:admin,tesorero')->group(function () {
        // Rutas de Multas (Livewire)
        Route::get('multas', MultasIndex::class)->name('multas.index');

    });

    // Rutas solo para Admin (Usuarios)
    Route::middleware('role:admin')->group(function () {

        // Rutas de Cargos (Livewire)
        Route::get('cargos', CargosIndex::class)->name('cargos.index');

      // Rutas de Gestiones (Livewire)
        Route::get('gestiones', GestionesIndex::class)->name('gestiones.index');

        // Rutas de Usuarios (Livewire)
        Route::get('usuarios', UsersIndex::class)->name('usuarios.index');

        // Rutas de Ciudadano Cargo (Livewire)
        Route::get('ciudadano-cargo', CiudadanoCargoIndex::class)->name('ciudadano_cargo.index');
    });
});

require __DIR__.'/settings.php';
