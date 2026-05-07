<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comuneros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciudadano_id')
                ->unique()
                ->constrained('ciudadanos')
                ->restrictOnDelete();
            $table->date('fecha_empadronamiento');
            $table->enum('estado_comunero', ['Activo', 'Suspendido', 'Retirado'])->default('Activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comuneros');
    }
};
