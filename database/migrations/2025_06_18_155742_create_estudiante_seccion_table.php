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
        Schema::create('estudiante_seccion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('estudiante_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('seccion_id')
                  ->constrained()
                  ->onDelete('cascade');

            // El estatus nos sirve para saber si aprobó, reprobó o se retiró de ese grupo
            $table->string('estatus')->default('regular');

            $table->timestamps();

            // Evita que el mismo alumno se inscriba dos veces en el mismo grupo académico
            $table->unique(['estudiante_id', 'seccion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiante_seccion');
    }
};
