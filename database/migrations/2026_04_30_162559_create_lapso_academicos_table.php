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
        Schema::create('lapso_academicos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_lapso', 50);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('status')->default('planificacion');
            $table->foreignId('ano_academico_id')
                ->constrained('ano_academicos')
                ->onDelete('cascade');
            $table->foreignId('tipo_lapso_id')
                ->constrained()
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lapso_academicos');
    }
};
