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
        Schema::create('pnf_trayecto', function (Blueprint $table) {
            $table->id();
            // Clave foránea hacia la tabla pnfs
            $table->foreignId('pnf_id')->constrained('pnfs')->onDelete('cascade');
            // Clave foránea hacia la tabla trayectos
            $table->foreignId('trayecto_id')->constrained('trayectos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pnf_trayecto');
    }
};
