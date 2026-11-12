<?php

namespace Tests\Feature;

use App\Models\Documento;
use App\Models\Pnf;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DocumentoTest extends TestCase
{
    use RefreshDatabase;

    private function crearPnf(): Pnf
    {
        return Pnf::create([
            'codigo' => 'PNF-001',
            'nombre' => 'Informática',
            'abreviado' => 'INF',
            'abreviado_coord' => 'INF',
        ]);
    }

    public function test_administrador_puede_subir_listar_descargar_y_eliminar_un_documento(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $pnf = $this->crearPnf();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/documentos', [
            'pnf_id' => $pnf->id,
            'archivo' => UploadedFile::fake()->create('planificacion.pdf', 120, 'application/pdf'),
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.pnf_id', $pnf->id);

        $documento = Documento::findOrFail($response->json('data.id'));

        Storage::disk('public')->assertExists($documento->archivo);

        $this->getJson('/api/documentos?pnf_id='.$pnf->id)
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->get('/api/documentos/'.$documento->id.'/descargar')
            ->assertOk();

        $this->deleteJson('/api/documentos/'.$documento->id)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('documentos', ['id' => $documento->id]);
        Storage::disk('public')->assertMissing($documento->archivo);
    }

    public function test_rechaza_archivos_con_extension_no_permitida(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $pnf = $this->crearPnf();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/documentos', [
            'pnf_id' => $pnf->id,
            'archivo' => UploadedFile::fake()->create('script.exe', 10, 'application/octet-stream'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('archivo');
    }

    public function test_el_administrador_debe_indicar_el_pnf(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/documentos', [
            'archivo' => UploadedFile::fake()->create('planificacion.pdf', 120, 'application/pdf'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('pnf_id');
    }
}
