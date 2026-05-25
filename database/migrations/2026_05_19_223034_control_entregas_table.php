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
        Schema::create('control_entregas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('entregado')->default(false);
            $table->timestamp('fecha_entrega')->nullable();
            $table->text('observaciones')->nullable();

            // Foreign keys
            // Relación con la tabla docentes
            $table->foreignId('docente_id')
                ->constrained('docentes')
                ->onDelete('cascade');

            // Relación con la tabla lapso_academicos
            $table->foreignId('lapso_academico_id')
                ->constrained('lapso_academicos')
                ->onDelete('cascade');

            // Relación con la tabla tipo_documentos
            $table->foreignId('tipo_documento_id')
                ->constrained('tipo_documentos')
                ->onDelete('cascade');

            // Relación con la tabla secciones
            $table->foreignId('seccion_id')
            ->nullable()
            ->constrained('seccions')
            ->onDelete('cascade');

            // Garantiza que no existan duplicados de la misma entrega en un mismo lapso académico
            $table->unique(
                ['docente_id', 'tipo_documento_id', 'seccion_id', 'lapso_academico_id'],
                'unique_entrega'
            );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_entregas');
    }
};
