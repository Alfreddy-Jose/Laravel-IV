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
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id();

            // Usuario que hizo la acción
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Tipo de acción
            $table->string('accion'); // Insertar, Modificar, Eliminar

            // En qué tabla ocurrió el cambio
            $table->string('tabla');

            // ID del registro afectado
            $table->unsignedBigInteger('registro_id')->nullable();

            // Datos antes y después
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('ip_address')->nullable(); // <--- acepta valores nulos

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
