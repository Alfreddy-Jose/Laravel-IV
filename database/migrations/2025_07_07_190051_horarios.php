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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->string('lapso_academico', 50);
            $table->foreignId('seccion_id')->constrained('seccions');
            $table->foreignId('trimestre_id')->constrained('trimestres');
            $table->string('nombre', 150)->nullable();
            $table->enum('estado', ['borrador', 'publicado'])->default('borrador');
            $table->timestamps();

            // Regla: una sección NO puede tener 2 horarios para el mismo trimestre en el mismo lapso academico
            $table->unique(['seccion_id', 'trimestre_id', 'lapso_academico']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
