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
        Schema::create('carga_docentes', function (Blueprint $table) {
           $table->id();
            
            $table->foreignId('docente_unidad_curricular_id')->constrained('docente_unidad_curricular')->onDelete('cascade');
            // Apunta a tu tabla de secciones (sean base o contingencia)
            $table->foreignId('seccion_id')->constrained('seccions')->onDelete('cascade');
            $table->integer('horas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carga_docentes');
    }
};
