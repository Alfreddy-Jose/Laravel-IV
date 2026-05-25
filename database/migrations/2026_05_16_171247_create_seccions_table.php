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
        Schema::create('seccions', function (Blueprint $table) {
            $table->id();
            // Foreign keys de PNF
            $table->foreignId('pnf_id')
                ->constrained('pnfs')
                ->onDelete('restrict');
            // Foreign keys de Tipo de Matricula
            $table->foreignId('matricula_id')
                ->constrained()
                ->onDelete('restrict');
            // Foreign keys de Trayecto
            $table->foreignId('trayecto_id')
                ->constrained('trayectos')
                ->onDelete('restrict');
            // Foreign keys de Sede
            $table->foreignId('sede_id')
                ->constrained('sedes')
                ->onDelete('restrict');
            // Foreign keys de Lapso
            $table->foreignId('lapso_id')
                ->constrained('lapso_academicos')
                ->onDelete('restrict');
            // Campo de numero de seccion
            $table->bigInteger('numero_seccion');
            // Campo de nombre
            $table->string('nombre', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seccions');
    }
};
