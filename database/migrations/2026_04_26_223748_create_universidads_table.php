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
        Schema::create('universidads', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_univ');
            $table->string('abreviado_univ');
            $table->string('rif_univ')->unique();
            $table->string('direccion');
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('universidads');
    }
};
