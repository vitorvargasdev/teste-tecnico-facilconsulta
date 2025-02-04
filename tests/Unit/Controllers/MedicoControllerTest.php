<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\MedicoController;
use App\Http\Requests\StoreMedicoRequest;
use App\Http\Requests\StoreConsultaRequest;
use App\Services\MedicoService;
use App\Models\Medico;
use App\Models\Consulta;
use Illuminate\Http\Request;
use Tests\TestCase;
use Mockery;

class MedicoControllerTest extends TestCase
{
    protected $medicoService;
    protected $medicoController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->medicoService = Mockery::mock(MedicoService::class);
        $this->medicoController = new MedicoController($this->medicoService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testMiddleware()
    {
        $middleware = MedicoController::middleware();
        $this->assertCount(1, $middleware);
        $this->assertEquals('auth:api', $middleware[0]->middleware);
        $this->assertEquals(['index'], $middleware[0]->except);
    }

    public function testIndex()
    {
        $request = new Request(['nome' => 'Dr. Silva']);
        $expectedResult = [new Medico(['nome' => 'Dr. Silva'])];

        $this->medicoService->shouldReceive('listarMedicos')
            ->once()
            ->with('Dr. Silva')
            ->andReturn($expectedResult);

        $result = $this->medicoController->index($request);

        $this->assertEquals($expectedResult, $result);
    }

    public function testStore()
    {
        $requestData = [
            'nome' => 'Dr. João Silva',
            'especialidade' => 'Cardiologia',
            'cidade_id' => 1
        ];

        $request = Mockery::mock(StoreMedicoRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($requestData);

        $medico = new Medico($requestData);

        $this->medicoService->shouldReceive('criarMedico')
            ->once()
            ->with($requestData)
            ->andReturn($medico);

        $result = $this->medicoController->store($request);

        $this->assertInstanceOf(Medico::class, $result);
        $this->assertEquals($requestData['nome'], $result->nome);
        $this->assertEquals($requestData['especialidade'], $result->especialidade);
        $this->assertEquals($requestData['cidade_id'], $result->cidade_id);
    }

    public function testObterConsultas()
    {
        $medicoId = 1;
        $request = new Request([
            'nome' => 'Paciente Silva',
            'apenas-agendadas' => 'true'
        ]);
        $expectedResult = [new Consulta()];

        $this->medicoService->shouldReceive('obterConsultas')
            ->once()
            ->with($medicoId, 'Paciente Silva', true)
            ->andReturn($expectedResult);

        $result = $this->medicoController->obterConsultas($medicoId, $request);

        $this->assertEquals($expectedResult, $result);
    }

    public function testAgendarConsulta()
    {
        $requestData = [
            'medico_id' => 1,
            'paciente_id' => 2,
            'data' => '2023-06-01 10:00:00'
        ];

        $request = Mockery::mock(StoreConsultaRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($requestData);

        $consulta = new Consulta($requestData);

        $this->medicoService->shouldReceive('agendarConsulta')
            ->once()
            ->with($requestData)
            ->andReturn($consulta);

        $result = $this->medicoController->agendarConsulta($request);

        $this->assertInstanceOf(Consulta::class, $result);
        $this->assertEquals($requestData['medico_id'], $result->medico_id);
        $this->assertEquals($requestData['paciente_id'], $result->paciente_id);
        $this->assertEquals($requestData['data'], $result->data);
    }
}
