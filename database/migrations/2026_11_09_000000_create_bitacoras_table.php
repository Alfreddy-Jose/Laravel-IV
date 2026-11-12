<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de auditoría. La tabla ya existía en la base de datos de
     * producción sin una migración registrada, por lo que se usan guardas
     * para no destruir el histórico ni fallar en instalaciones existentes.
     */
    public function up(): void
    {
        if (! Schema::hasTable('bitacoras')) {
            Schema::create('bitacoras', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('accion');
                $table->string('tabla');
                $table->unsignedBigInteger('registro_id')->nullable();
                $table->json('datos_anteriores')->nullable();
                $table->json('datos_nuevos')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });

            return;
        }

        // Instalaciones antiguas: garantiza la columna sin recrear la tabla.
        if (! Schema::hasColumn('bitacoras', 'ip_address')) {
            Schema::table('bitacoras', function (Blueprint $table) {
                $table->string('ip_address', 45)->nullable()->after('datos_nuevos');
            });
        }
    }

    /**
     * No se elimina la tabla para no perder el histórico de auditoría.
     */
    public function down(): void
    {
        // Intencionalmente vacío.
    }
};
