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
        Schema::create('unidad_curriculars', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->text('descripcion', 255)->nullable();
            $table->boolean('es_proyecto')->default(false);
            $table->integer('unidad_credito');
            $table->integer('hora_teorica')->nullable();
            $table->integer('hora_practica')->nullable();
            $table->integer('hora_total_est');
            $table->string('periodo', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad_curriculars');
    }
};
