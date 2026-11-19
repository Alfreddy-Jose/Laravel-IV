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
        Schema::create('censos', function (Blueprint $table) {
            $table->id();

            // Relación (1 a Muchos Histórico): Estudiante Tiene Censo (De tu Diagrama)
            $table->foreignId('estudiante_id')
                  ->constrained('estudiantes')
                  ->onDelete('cascade');

            // Relación (Muchos a 1): Censos se realizan en un Lapso Académico (De tu Diagrama)
            $table->foreignId('lapso_id')
                  ->constrained('lapso_academicos')
                  ->onDelete('cascade');

            // Atributos de la Entidad Censo Estudiantil en tu diagrama
            $table->string('nombre');       // Ej: "Censo de Políticas Estudiantiles 2026"
            $table->string('tipo_censo');  // Ej: "POLITICAS ESTUDIANTILES", "ACADEMICO", "INTENSIVO"

            /**
             * LA OPTIMIZACIÓN: Sustituto de la tabla Detalle_censo
             * Almacena datos dinámicos directo: {"categoria": "discapacidad", "condicion": "Asperger", "grado": 1}
             */
            $table->jsonb('detalles')->nullable();

            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Regla de Negocio: Un estudiante no puede duplicar el mismo tipo de censo en el mismo año/lapso
            $table->unique(['estudiante_id', 'lapso_id', 'tipo_censo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('censos');
    }
};
