<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_uni')->unique(); // Código asignado por la universidad
            $table->string('nombre'); // Ej: Silla ejecutiva, Computadora de oficina
            $table->text('descripcion')->nullable(); // Características técnicas / especificaciones
            $table->string('imagen')->nullable(); // Ruta de la foto del estado del bien

            // Relación con PNF (en caso de que la coordinación pertenezca a un PNF específico)
            $table->foreignId('pnf_id')
                  ->nullable()
                  ->constrained('pnfs')
                  ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};
