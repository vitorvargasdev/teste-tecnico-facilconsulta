<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\CidadeController;
use App\Services\CidadeService;
use Illuminate\Http\Request;
use Tests\TestCase;
use Mockery;

class CidadeControllerTest extends TestCase
{
    protected $cidadeService;
    protected $cidadeController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cidadeService = Mockery::mock(CidadeService::class);
        $this->cidadeController = new CidadeController($this->cidadeService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testIndex()
    {
        $request = new Request(['nome' => 'São Paulo']);
        $expectedResult = ['cidade1', 'cidade2'];

        $this->cidadeService->shouldReceive('obterCidades')
            ->once()
            ->with('São Paulo')
            ->andReturn($expectedResult);

        $result = $this->cidadeController->index($request);

        $this->assertEquals($expectedResult, $result);
    }

    public function testObterMedicosPorCidade()
    {
        $cidadeId = 1;
        $request = new Request(['nome' => 'Dr. Silva']);
        $expectedResult = ['medico1', 'medico2'];

        $this->cidadeService->shouldReceive('obterMedicosPorCidade')
            ->once()
            ->with($cidadeId, 'Dr. Silva')
            ->andReturn($expectedResult);

        $result = $this->cidadeController->obterMedicosPorCidade($cidadeId, $request);

        $this->assertEquals($expectedResult, $result);
    }
}