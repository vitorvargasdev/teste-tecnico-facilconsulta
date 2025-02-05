<?php

namespace Tests\Feature;

use App\Models\Medico;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Cidade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class MedicoTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function testListAllMedicosWithoutAuthenticationSucceeds()
    {
        $response = $this->getJson('/api/medicos');

        $response->assertStatus(200);
    }

    public function testListMedicosFilteredByName()
    {
        Cidade::factory()->create();
        Medico::factory()->create(['nome' => 'Dr. João Silva']);
        Medico::factory()->create(['nome' => 'Dra. Maria Santos']);

        $response = $this->getJson('/api/medicos?nome=João');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['nome' => 'Dr. João Silva']);
    }

    public function testCreateMedicoWithoutAuthenticationFails()
    {
        $dadosMedico = [
            'nome' => 'Dr. Novo Médico',
            'especialidade' => 'Cardiologia',
            'cidade' => 'São Paulo'
        ];

        $response = $this->postJson('/api/medicos', $dadosMedico);

        $response->assertStatus(401);
    }

    public function testCreateMedicoWithAuthenticationSucceeds()
    {

        $cidade = Cidade::factory()->create();
        $dadosMedico = [
            'nome' => 'Dr. Novo Médico',
            'especialidade' => 'Cardiologia',
            'cidade_id' => $cidade->id,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/medicos', $dadosMedico);

        $response->assertStatus(201)
            ->assertJsonFragment($dadosMedico);
    }

    public function testCreateMedicoWithInvalidDataFailsValidation()
    {
        $dadosInvalidos = [
            'nome' => '',
            'especialidade' => '',
            'cidade_id' => ''
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/medicos', $dadosInvalidos);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'especialidade', 'cidade_id']);
    }

    public function testGetMedicoConsultasWithoutAuthenticationFails()
    {
        Cidade::factory()->create();
        $medico = Medico::factory()->create();

        $response = $this->getJson("/api/medicos/{$medico->id}/pacientes");

        $response->assertStatus(401);
    }

    public function testGetMedicoConsultasWithAuthenticationSucceeds()
    {
        Cidade::factory()->create();
        $medico = Medico::factory()->create();
        $paciente = Paciente::factory()->create();
        Consulta::factory()->create([
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id,
            'data' => now()->addDays(1)
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/medicos/{$medico->id}/pacientes");

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function testGetMedicoConsultasWithFiltersApplied()
    {
        Cidade::factory()->create();
        $medico = Medico::factory()->create();
        $paciente1 = Paciente::factory()->create(['nome' => 'João da Silva']);
        $paciente2 = Paciente::factory()->create(['nome' => 'Maria Oliveira']);

        Consulta::factory()->create([
            'medico_id' => $medico->id,
            'paciente_id' => $paciente1->id,
            'data' => now()->addDays(1)
        ]);
        Consulta::factory()->create([
            'medico_id' => $medico->id,
            'paciente_id' => $paciente2->id,
            'data' => now()->subDays(1)
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/medicos/{$medico->id}/pacientes?nome=João&apenas-agendadas=true");

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['nome' => 'João da Silva']);
    }

    public function testScheduleConsultaWithoutAuthenticationFails()
    {
        Cidade::factory()->create();
        $dadosConsulta = [
            'medico_id' => Medico::factory()->create()->id,
            'paciente_id' => Paciente::factory()->create()->id,
            'data' => now()->addDays(1)->format('Y-m-d H:i:s')
        ];

        $response = $this->postJson('/api/medicos/consulta', $dadosConsulta);

        $response->assertStatus(401);
    }

    public function testScheduleConsultaWithAuthenticationSucceeds()
    {
        Cidade::factory()->create();
        $dadosConsulta = [
            'medico_id' => Medico::factory()->create()->id,
            'paciente_id' => Paciente::factory()->create()->id,
            'data' => now()->addDays(1)->format('Y-m-d H:i:s')
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/medicos/consulta', $dadosConsulta);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'id' => $response->json('id'),
                'data' => $dadosConsulta['data'],
            ]);
    }

    public function testScheduleConsultaWithInvalidDataFailsValidation()
    {
        $dadosInvalidos = [
            'medico_id' => '',
            'paciente_id' => '',
            'data' => 'data-invalida'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/medicos/consulta', $dadosInvalidos);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['medico_id', 'paciente_id', 'data']);
    }
}
