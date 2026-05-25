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
        Schema::create('docente_unidad_curricular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained()->onDelete('restrict');
            $table->foreignId('unidad_curricular_id')->constrained()->onDelete('restrict');
            $table->unique(['docente_id', 'unidad_curricular_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docente_unidad_curricular');
    }
};
