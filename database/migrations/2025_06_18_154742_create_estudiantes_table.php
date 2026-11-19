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
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            // Relación un-a-uno con personas (un estudiante es una persona)
            $table->foreignId('persona_id')
                  ->constrained('personas')
                  ->onDelete('cascade');

            // Aquí se pueden agregar campos netamente académicos en el futuro si se requiere
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
