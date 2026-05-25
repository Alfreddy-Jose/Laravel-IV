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
        Schema::create('historial_actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('control_entrega_id')
                ->constrained('control_entregas')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users');
            $table->string('accion', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_actividades');
    }
};
