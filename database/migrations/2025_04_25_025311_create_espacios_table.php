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
        Schema::create('espacios', function (Blueprint $table) {
            $table->id();
            $table->string('etapa');
            $table->integer('nro_aula')->nullable();
            $table->string('nombre_aula');
            $table->string('abreviado_lab')->nullable();
            $table->integer('equipos')->nullable();
            $table->string('tipo_espacio');
            $table->foreignId('sede_id')
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
        Schema::dropIfExists('espacios');
    }
};
