<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLapsoContext;
use App\Models\AnoAcademico;
use App\Models\LapsoAcademico;
use App\Models\TipoLapso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Regresión: el contexto de lapso no debe bloquear la API cuando el cliente
 * todavía no lo conoce (bootstrap del SPA, localStorage vacío o id obsoleto).
 */
class LapsoContextTest extends TestCase
{
    use RefreshDatabase;

    private function crearLapso(string $nombre, string $estado, int $desplazamientoDias = 0): LapsoAcademico
    {
        $tipo = TipoLapso::first() ?? TipoLapso::forceCreate(['nombre' => 'REGULAR']);

        $ano = AnoAcademico::firstOrCreate(
            ['ano' => now()->year],
            [
                'fecha_inicio' => now()->startOfYear(),
                'fecha_fin' => now()->endOfYear(),
                'status' => 'activo',
            ]
        );

        return LapsoAcademico::create([
            'nombre_lapso' => $nombre,
            'ano_academico_id' => $ano->id,
            'tipo_lapso_id' => $tipo->id,
            'fecha_inicio' => now()->subMonth()->addDays($desplazamientoDias),
            'fecha_fin' => now()->addMonth(),
            'status' => $estado,
        ]);
    }

    private function usuario(): User
    {
        return User::forceCreate([
            'name' => 'Contexto Test',
            'email' => 'contexto@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_lapsos_activos_es_accesible_sin_contexto_previo(): void
    {
        $lapso = $this->crearLapso('2026-REG', 'activo');

        $response = $this->actingAs($this->usuario())->getJson('/api/lapsos/activos');

        $response->assertOk();
        $response->assertJsonFragment(['id' => $lapso->id]);
    }

    public function test_middleware_resuelve_el_lapso_actual_sin_header(): void
    {
        $primero = $this->crearLapso('2026-REG', 'activo');
        $this->crearLapso('2026-REG-2', 'activo', 30);

        $request = Request::create('/api/lapsos', 'GET');

        $response = (new SetLapsoContext)->handle($request, function (Request $req) {
            return response()->json([
                'header' => $req->header('X-Lapso-Id'),
                'contexto' => app('lapso_actual'),
            ]);
        });

        // Sin header se publica el primer lapso vigente (orden cronológico).
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($primero->id, app('lapso_actual'));
        $this->assertSame((string) $primero->id, $request->header('X-Lapso-Id'));
    }

    public function test_middleware_respeta_el_header_del_cliente(): void
    {
        $this->crearLapso('2026-REG', 'activo');
        $seleccionado = $this->crearLapso('2026-INT', 'activo', 30);

        $request = Request::create('/api/lapsos', 'GET');
        $request->headers->set('X-Lapso-Id', (string) $seleccionado->id);

        $response = (new SetLapsoContext)->handle($request, fn () => response()->json(['ok' => true]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($seleccionado->id, app('lapso_actual'));
        $this->assertSame((string) $seleccionado->id, $request->header('X-Lapso-Id'));
    }

    public function test_middleware_ignora_un_lapso_inexistente_y_usa_el_actual(): void
    {
        $vigente = $this->crearLapso('2026-REG', 'activo');

        $request = Request::create('/api/lapsos', 'GET');
        $request->headers->set('X-Lapso-Id', '999999');

        $response = (new SetLapsoContext)->handle($request, fn () => response()->json(['ok' => true]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($vigente->id, app('lapso_actual'));
    }

    public function test_middleware_no_bloquea_el_sistema_sin_lapsos_registrados(): void
    {
        $request = Request::create('/api/lapsos', 'GET');

        $response = (new SetLapsoContext)->handle($request, fn () => response()->json(['ok' => true]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertFalse(app()->bound('lapso_actual'));
    }
}
