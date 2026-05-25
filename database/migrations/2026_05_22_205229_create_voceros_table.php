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
        Schema::create('voceros', function (Blueprint $table) {
            $table->id();
            // Foreign keys de Persona
            $table->foreignId('persona_id')
                ->constrained()
                ->onDelete('restrict');
            // Foreign keys de Seccion
            $table->foreignId('seccion_id')
                ->constrained()
                ->onDelete('restrict');
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
        Schema::dropIfExists('voceros');
    }
};
