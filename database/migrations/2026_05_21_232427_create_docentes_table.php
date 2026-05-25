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
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->string('categoria', 50);
            // Relacion con persona
            $table->foreignId('persona_id')
                ->constrained()
                ->onDelete('cascade');
            // Relacion con PNF
            $table->foreignId('pnf_id')
                ->constrained('pnfs')
                ->onDelete('cascade'); 
            $table->integer('horas_dedicacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};
