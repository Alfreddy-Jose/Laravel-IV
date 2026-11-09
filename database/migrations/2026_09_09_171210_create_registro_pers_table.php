<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('registro_pers', function (Blueprint $table) {
            $table->id();

            // Claves foráneas (Relaciones 1:N)
            $table->foreignId('estudiante_id')
                  ->constrained('estudiantes')
                  ->onDelete('cascade');

            $table->foreignId('unidad_curricular_id')
                  ->constrained('unidad_curriculars')
                  ->onDelete('cascade');

            $table->foreignId('docente_id')
                  ->constrained('docentes')
                  ->onDelete('cascade');

            $table->foreignId('lapso_academico_id')
                  ->constrained('lapso_academicos')
                  ->onDelete('cascade');

            // Campos de Control y Evaluación
            $table->decimal('calificacion', 5, 2)->nullable();
            // $table->boolean('acta_entregada')->default(false);
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registro_pers');
    }
};
