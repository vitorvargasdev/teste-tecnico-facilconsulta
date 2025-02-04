<?php

namespace Tests\Unit\Services;

use App\Models\Paciente;
use App\Services\PacienteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PacienteServiceTest extends TestCase
{
    use RefreshDatabase;

    private PacienteService $pacienteService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pacienteService = new PacienteService();
    }

    public function testShouldCreatePatient()
    {
        $data = [
            'nome' => 'João Silva',
            'cpf' => '12345678901',
            'celular' => '11999999999',
        ];

        $patient = $this->pacienteService->criarPaciente($data);

        $this->assertInstanceOf(Paciente::class, $patient);
        $this->assertEquals($data['nome'], $patient->nome);
        $this->assertEquals($data['cpf'], $patient->cpf);
        $this->assertEquals($data['celular'], $patient->celular);
    }

    public function testShouldUpdatePatient()
    {
        $patient = Paciente::factory()->create();

        $data = [
            'nome' => 'João Silva Atualizado',
            'celular' => '11988888888',
        ];

        $updatedPatient = $this->pacienteService->atualizarPaciente($patient->id, $data);

        $this->assertInstanceOf(Paciente::class, $updatedPatient);
        $this->assertEquals($data['nome'], $updatedPatient->nome);
        $this->assertEquals($data['celular'], $updatedPatient->celular);
        $this->assertEquals($patient->cpf, $updatedPatient->cpf);
    }

    public function testShouldThrowExceptionWhenUpdatingNonExistentPatient()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $data = [
            'nome' => 'João Silva',
            'celular' => '11999999999',
        ];

        $this->pacienteService->atualizarPaciente(999, $data);
    }
}
