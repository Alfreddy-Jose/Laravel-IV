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
        Schema::create('ano_academicos', function (Blueprint $table) {
            $table->id();
            $table->integer('ano')->unique();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            // 'cronograma' = automático por fechas. 'activo'/'cerrado' = forzado manual.
            $table->string('status', 20)->default('cronograma');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ano_academicos');
    }
};
