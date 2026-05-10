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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cedula_persona')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->text('direccion');
            $table->string('telefono');
            $table->string('email')->unique();
            $table->string('tipo_persona');
            $table->string('grado_inst');
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
        Schema::dropIfExists('personas');
    }
};
