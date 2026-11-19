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
        Schema::create('voceros', function (Blueprint $table) {
            $table->id();

            $table->foreignId('estudiante_id')
                  ->constrained('estudiantes')
                  ->onDelete('cascade');

            $table->foreignId('seccion_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('tipo_vocero'); // ej: 'Principal', 'Suplente'

            $table->timestamps();

            // Una sección (que ya es única por lapso) solo puede tener un tipo de vocero único
            $table->unique(['seccion_id', 'tipo_vocero']);
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
