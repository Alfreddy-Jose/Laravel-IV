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
        Schema::create('pnf_sede', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pnf_id')->constrained()->onDelete('cascade');
            $table->foreignId('sede_id')->constrained()->onDelete('cascade');
            $table->unique(['sede_id', 'pnf_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pnf_sede');
    }
};
