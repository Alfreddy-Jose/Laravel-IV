<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('unidad_curriculars', function (Blueprint $table) {
            $table->foreignId('pnf_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('pnfs')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('unidad_curriculars', function (Blueprint $table) {
            $table->dropForeign(['pnf_id']);
            $table->dropColumn('pnf_id');
        });
    }
};
