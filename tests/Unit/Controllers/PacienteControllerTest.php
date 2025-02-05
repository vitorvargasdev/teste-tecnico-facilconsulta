<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\PacienteController;
use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;
use App\Services\PacienteService;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Tests\TestCase;
use Mockery;

class PacienteControllerTest extends TestCase
{
    protected $pacienteService;
    protected $pacienteController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pacienteService = Mockery::mock(PacienteService::class);
        $this->pacienteController = new PacienteController($this->pacienteService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testMiddleware()
    {
        $middleware = PacienteController::middleware();
        $this->assertCount(1, $middleware);
        $this->assertEquals('auth:api', $middleware[0]->middleware);
    }

    public function testStore()
    {
        $requestData = [
            'nome' => 'João Silva',
            'cpf' => '12345678901',
            'celular' => '11999999999'
        ];

        $request = Mockery::mock(StorePacienteRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($requestData);

        $paciente = new Paciente($requestData);

        $this->pacienteService->shouldReceive('criarPaciente')
            ->once()
            ->with($requestData)
            ->andReturn($paciente);

        $result = $this->pacienteController->store($request);

        $this->assertInstanceOf(Paciente::class, $result);
        $this->assertEquals($requestData['nome'], $result->nome);
        $this->assertEquals($requestData['cpf'], $result->cpf);
        $this->assertEquals($requestData['celular'], $result->celular);
    }

    public function testUpdate()
    {
        $id = 1;
        $requestData = [
            'nome' => 'João Silva Atualizado',
            'celular' => '11988888888'
        ];

        $request = Mockery::mock(UpdatePacienteRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($requestData);

        $pacienteAtualizado = new Paciente(array_merge(['id' => $id], $requestData));

        $this->pacienteService->shouldReceive('atualizarPaciente')
            ->once()
            ->with($id, $requestData)
            ->andReturn($pacienteAtualizado);

        $result = $this->pacienteController->update($request, $id);

        $this->assertInstanceOf(Paciente::class, $result);
        $this->assertEquals($id, $result->id);
        $this->assertEquals($requestData['nome'], $result->nome);
        $this->assertEquals($requestData['celular'], $result->celular);
    }
}