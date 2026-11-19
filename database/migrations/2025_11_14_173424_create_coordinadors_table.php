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
        Schema::create('coordinadors', function (Blueprint $table) {
            $table->id();
            // Relacion con la tabla de Docentes
            $table->foreignId('docente_id')
                ->unique()
                ->nullable()
                ->constrained('docentes')
                ->onDelete('cascade');
            // Relacion con la tabla de PNF
            $table->foreignId('pnf_id') 
                ->unique()
                ->constrained('pnfs')
                ->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordinadors');
    }
};
