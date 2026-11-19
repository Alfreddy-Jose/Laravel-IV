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
      Schema::create('estadistica_inscripciones', function (Blueprint $table) {
            $table->id();
            
            // Llaves foráneas a las entidades existentes
            $table->foreignId('lapso_id')->constrained('lapso_academicos')->onDelete('cascade');
            $table->foreignId('unidad_curricular_id')->constrained('unidad_curriculars')->onDelete('cascade');
            $table->string('seccion', 50);
            $table->integer('cantidad_estudiantes'); 
            $table->timestamps();
            // Blindaje definitivo contra duplicados en cargas de archivos
            $table->unique(['lapso_id', 'unidad_curricular_id', 'seccion'], 'uid_control_carga_estadistica');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estadistica_inscripciones');
    }
};
