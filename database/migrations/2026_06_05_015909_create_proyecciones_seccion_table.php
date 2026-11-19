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
        Schema::create('proyeccion_secciones', function (Blueprint $table) {
            $table->id();

            // Relación obligatoria con la cabecera de la proyección
            $table->foreignId('proyeccion_id')
                  ->constrained('proyecciones')
                  ->onDelete('cascade');

            // Llaves foráneas directas para asegurar rendimiento e integridad histórica
            $table->foreignId('sede_id')->constrained('sedes')->onDelete('cascade');
            $table->foreignId('trayecto_id')->constrained('trayectos')->onDelete('cascade');

            // Códigos de las secciones como strings
            $table->string('seccion_origen');
            $table->string('seccion_destino');

            // La materia prima ingresada por el usuario (Paso 1 del Wizard)
            $table->integer('grupos_aprobados')->default(0);
            $table->integer('grupos_reprobados')->default(0);

            // Control del Coordinador
            $table->boolean('crear_seccion')->default(true);
            $table->boolean('tiene_reincorporados')->default(false);

            $table->timestamps();

            // Restricción para evita repetir la misma sección de origen en el mismo contexto
            $table->unique(
                ['proyeccion_id', 'sede_id', 'trayecto_id', 'seccion_origen'],
                'proyeccion_seccion_origen_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyeccion_secciones');
    }
};
