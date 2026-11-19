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

        
        Schema::create('trimestres', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('numero_relativo');
            $table->foreignId('trayecto_id')
                ->constrained()
                ->onDelete('cascade');
            $table->timestamps();


             // Asegurar que no haya duplicados en el mismo trayecto
            $table->unique(['trayecto_id', 'numero_relativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trimestres');
    }
};
