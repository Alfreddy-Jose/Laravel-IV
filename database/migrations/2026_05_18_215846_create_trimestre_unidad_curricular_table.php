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
        Schema::create('trimestre_unidad_curricular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_curricular_id')->constrained('unidad_curriculars')->onDelete('cascade');
            $table->foreignId('trimestre_id')->constrained('trimestres')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trimestre_unidad_curricular');
    }
};
