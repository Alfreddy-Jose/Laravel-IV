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
            $table->integer('ano');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('status')->default('cronograma');
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
