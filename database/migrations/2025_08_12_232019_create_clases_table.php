<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clases', function (Blueprint $table) {
            // Identificador único
            $table->id();

            // Relaciones con otras tablas
            $table->foreignId('sede_id')->constrained('sedes')->onDelete('cascade');
            $table->foreignId('pnf_id')->constrained('pnfs')->onDelete('cascade');
            $table->foreignId('trayecto_id')->constrained('trayectos')->onDelete('cascade');
            $table->foreignId('trimestre_id')->constrained('trimestres')->onDelete('cascade');
            $table->foreignId('unidad_curricular_id')->constrained('unidad_curriculars')->onDelete('cascade');
            $table->foreignId('docente_id')->nullable()->constrained('docentes')->onDelete('cascade');
            $table->foreignId('espacio_id')->nullable()->constrained('espacios')->onDelete('cascade');
            $table->foreignId('bloque_id')->constrained('bloques_turnos')->onDelete('cascade');
            $table->foreignId('horario_id')
                ->after('id')
                ->constrained('horarios')
                ->onDelete('cascade');

            // Datos del evento
            $table->string('dia', 10); // Ej: 'LUNES'
            $table->integer('duracion'); // Duración en bloques (1 bloque = 1 hora)
            $table->timestamps();

            // Restricción única menos restrictiva (solo para evitar duplicados exactos)
            $table->unique([
                'docente_id',
                'espacio_id',
                'dia',
                'bloque_id',
                'horario_id'
            ], 'horario_unique_espacio_dia_bloque');

            // Índices útiles para búsquedas
            $table->index(['horario_id', 'dia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clases');
        Schema::table('clases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('horario_id');
            $table->dropIndex(['horario_id', 'dia']);
        });
    }
};
