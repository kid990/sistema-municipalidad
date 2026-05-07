<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('multas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunero_id')->constrained('comuneros');
            $table->decimal('monto', 10, 2);
            $table->string('motivo', 255)->nullable();
            $table->date('fecha_emision')->default(DB::raw('(CURRENT_DATE)'));
            $table->boolean('estado_pago')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('multas');
    }
};
