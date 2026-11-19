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
        Schema::create('proyecciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ano_academico_id')->constrained('ano_academicos')->onDelete('cascade');
            $table->foreignId('pnf_id')->constrained('pnfs')->onDelete('cascade');
            $table->string('estado')->default('borrador');
            $table->timestamps();

            $table->unique(['ano_academico_id', 'pnf_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecciones');
    }
};
