<?php

namespace Tests\Unit\Services;

use App\Models\Cidade;
use App\Models\Medico;
use App\Services\CidadeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CidadeServiceTest extends TestCase
{
    use RefreshDatabase;

    private CidadeService $cidadeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cidadeService = new CidadeService();
    }

    public function testGetCitiesWithoutFilter()
    {
        Cidade::factory()->count(3)->create();

        $cities = $this->cidadeService->obterCidades(null);

        $this->assertCount(3, $cities->toArray());
        $this->assertInstanceOf(Cidade::class, $cities->first());
    }

    public function testGetCitiesWithFilter()
    {
        Cidade::factory()->create(['nome' => 'São Paulo']);
        Cidade::factory()->create(['nome' => 'Rio de Janeiro']);
        Cidade::factory()->create(['nome' => 'Salvador']);

        $cities = $this->cidadeService->obterCidades('São');

        $this->assertCount(1, $cities->toArray());
        $this->assertEquals('São Paulo', $cities->first()->nome);
    }

    public function testGetDoctorsByCityWithoutFilter()
    {
        $cidade = Cidade::factory()->create();
        Medico::factory()->count(3)->create(['cidade_id' => $cidade->id]);

        $doctors = $this->cidadeService->obterMedicosPorCidade($cidade->id, null);

        $this->assertCount(3, $doctors->toArray());
        $this->assertInstanceOf(Medico::class, $doctors->first());
    }

    public function testGetDoctorsByCityWithFilter()
    {
        $cidade = Cidade::factory()->create();
        Medico::factory()->create(['nome' => 'Dr. Silva', 'cidade_id' => $cidade->id]);
        Medico::factory()->create(['nome' => 'Dr. Santos', 'cidade_id' => $cidade->id]);
        Medico::factory()->create(['nome' => 'Dr. Oliveira', 'cidade_id' => $cidade->id]);

        $doctors = $this->cidadeService->obterMedicosPorCidade($cidade->id, 'Silva');

        $this->assertCount(1, $doctors->toArray());
        $this->assertEquals('Dr. Silva', $doctors->first()->nome);
    }
}
