<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\AnoAcademico;
use App\Models\LapsoAcademico;
use App\Models\TipoLapso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LapsoAcademicoConcurrentTest extends TestCase
{
    use RefreshDatabase;

    public function test_lapso_academico_status_accessor_hierarchy()
    {
        // Setup types and year
        $tipoRegular = TipoLapso::forceCreate(['nombre' => 'REGULAR']);
        $ano = AnoAcademico::create([
            'ano' => 2026,
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-12-31',
            'status' => 'activo'
        ]);

        // 3. Default logic: Lapso created should have status 'planificacion' and getEstadoAttribute() returns 'planificacion'
        $lapso = LapsoAcademico::create([
            'nombre_lapso' => '2026-I',
            'ano_academico_id' => $ano->id,
            'tipo_lapso_id' => $tipoRegular->id,
            'fecha_inicio' => '2026-01-15',
            'fecha_fin' => '2026-04-15',
        ]);

        $this->assertEquals('planificacion', $lapso->status);
        $this->assertEquals('planificacion', $lapso->estado);

        // 2. Respect manual will: When physical database field status is different from 'cronograma' and not empty, return that value
        $lapso->status = 'activo';
        $lapso->save();
        $this->assertEquals('activo', $lapso->estado);

        // 1. Clausura por el Padre: If year is closed, lapso estado returns 'cerrado' even if physical status is 'activo'
        $ano->status = 'cerrado';
        $ano->save();
        
        $lapso->refresh();
        $this->assertEquals('cerrado', $lapso->estado);
    }

    public function test_activation_of_academic_year_only_activates_first_regular_lapso()
    {
        $tipoRegular = TipoLapso::forceCreate(['nombre' => 'REGULAR']);
        $tipoIntensivo = TipoLapso::forceCreate(['nombre' => 'INTENSIVO']);

        $ano = AnoAcademico::create([
            'ano' => 2026,
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-12-31',
            'status' => 'planificacion'
        ]);

        // Create multiple lapsos, regular and non-regular
        $lapsoRegular1 = LapsoAcademico::create([
            'nombre_lapso' => '2026-REG-1',
            'ano_academico_id' => $ano->id,
            'tipo_lapso_id' => $tipoRegular->id,
            'fecha_inicio' => '2026-01-15',
            'fecha_fin' => '2026-04-15',
        ]);

        $lapsoRegular2 = LapsoAcademico::create([
            'nombre_lapso' => '2026-REG-2',
            'ano_academico_id' => $ano->id,
            'tipo_lapso_id' => $tipoRegular->id,
            'fecha_inicio' => '2026-05-15',
            'fecha_fin' => '2026-08-15',
        ]);

        $lapsoIntensivo = LapsoAcademico::create([
            'nombre_lapso' => '2026-INT-1',
            'ano_academico_id' => $ano->id,
            'tipo_lapso_id' => $tipoIntensivo->id,
            'fecha_inicio' => '2026-09-01',
            'fecha_fin' => '2026-10-15',
        ]);

        // Authenticate
        $user = User::forceCreate([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password')
        ]);
        $this->actingAs($user);

        // Activate academic year manually via controller action
        $response = $this->postJson("/api/panel/ano/{$ano->id}/forzar-inicio");

        $response->assertStatus(200);

        $lapsoRegular1->refresh();
        $lapsoRegular2->refresh();
        $lapsoIntensivo->refresh();

        // First regular lapso is active
        $this->assertEquals('activo', $lapsoRegular1->status);
        // Second regular remains in planificacion
        $this->assertEquals('planificacion', $lapsoRegular2->status);
        // Other types of lapsos remain in planificacion
        $this->assertEquals('planificacion', $lapsoIntensivo->status);
    }

    public function test_forzar_inicio_lapso_closes_only_lapsos_of_same_type_in_same_year()
    {
        $tipoRegular = TipoLapso::forceCreate(['nombre' => 'REGULAR']);
        $tipoIntensivo = TipoLapso::forceCreate(['nombre' => 'INTENSIVO']);

        $ano = AnoAcademico::create([
            'ano' => 2026,
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-12-31',
            'status' => 'activo'
        ]);

        $lapsoRegular1 = LapsoAcademico::create([
            'nombre_lapso' => '2026-REG-1',
            'ano_academico_id' => $ano->id,
            'tipo_lapso_id' => $tipoRegular->id,
            'fecha_inicio' => '2026-01-15',
            'fecha_fin' => '2026-04-15',
            'status' => 'activo'
        ]);

        $lapsoRegular2 = LapsoAcademico::create([
            'nombre_lapso' => '2026-REG-2',
            'ano_academico_id' => $ano->id,
            'tipo_lapso_id' => $tipoRegular->id,
            'fecha_inicio' => '2026-05-15',
            'fecha_fin' => '2026-08-15',
            'status' => 'planificacion'
        ]);

        $lapsoIntensivo = LapsoAcademico::create([
            'nombre_lapso' => '2026-INT-1',
            'ano_academico_id' => $ano->id,
            'tipo_lapso_id' => $tipoIntensivo->id,
            'fecha_inicio' => '2026-09-01',
            'fecha_fin' => '2026-10-15',
            'status' => 'activo'
        ]);

        // Authenticate
        $user = User::forceCreate([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password')
        ]);
        $this->actingAs($user);

        // Force start lapsoRegular2
        $response = $this->postJson("/api/lapso/{$lapsoRegular2->id}/forzar-inicio");
        $response->assertStatus(200);

        $lapsoRegular1->refresh();
        $lapsoRegular2->refresh();
        $lapsoIntensivo->refresh();

        // lapsoRegular2 is now active
        $this->assertEquals('activo', $lapsoRegular2->status);
        // lapsoRegular1 (same type regular) is now closed
        $this->assertEquals('cerrado', $lapsoRegular1->status);
        // lapsoIntensivo (different type) remains active
        $this->assertEquals('activo', $lapsoIntensivo->status);
    }

    public function test_store_ano_academico_fails_if_year_from_fecha_inicio_already_exists()
    {
        $existingAno = AnoAcademico::create([
            'ano' => 2026,
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-12-31',
            'status' => 'activo'
        ]);

        $user = User::forceCreate([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password')
        ]);
        $this->actingAs($user);

        // Attempting to create an academic year with a fecha_inicio in 2026
        $response = $this->postJson("/api/panel/ano", [
            'ano' => 2027, // Send a different year parameter, but date year is 2026
            'fecha_inicio' => '2026-02-15',
            'fecha_fin' => '2026-11-15'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['fecha_inicio']);
        $response->assertJson([
            'errors' => [
                'fecha_inicio' => [
                    'Ya existe un año académico registrado para el año 2026.'
                ]
            ]
        ]);
    }
}
