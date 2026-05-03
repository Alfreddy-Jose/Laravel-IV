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
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('nro_sede');
            $table->string('nombre_sede', 100);
            $table->string('nombre_abreviado', 30);
            $table->text('direccion');
            // Relación con la tabla universidads
            $table->foreignId('universidad_id')
                ->constrained('universidads')
                ->onDelete('cascade'); 
            // Relacion con la tabla de municipios especificando llave primaria
            $table->foreignId('municipio_id')
                ->constrained('municipios', 'id_municipio')
                ->onDelete('cascade');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};
