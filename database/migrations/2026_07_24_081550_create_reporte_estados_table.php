<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('reporte_estados', function (Blueprint $table) {
            $table->id();

            // Relación con el Bien
            $table->foreignId('bien_id')
                  ->constrained('biens')
                  ->onDelete('cascade');

            // Relación con el Lapso Académico
            $table->foreignId('lapso_academico_id')
                  ->constrained('lapso_academicos')
                  ->onDelete('cascade');

            // Estado operativo del bien
            $table->enum('estado_operativo', ['operativo', 'dañado', 'desincorporado'])
                  ->default('operativo');

            $table->date('fecha');
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Clave única: Garantiza solo un reporte por bien en cada lapso académico
            $table->unique(['bien_id', 'lapso_academico_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporte_estados');
    }
};
