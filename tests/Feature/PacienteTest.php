<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PacienteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testStorePatientSuccess()
    {
        $data = [
            'nome' => $this->faker->name,
            'cpf' => $this->faker->numerify('###########'),
            'celular' => $this->faker->numerify('#############'),
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/pacientes', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'nome', 'cpf', 'celular']);

        $this->assertDatabaseHas('pacientes', $data);
    }

    public function testStorePatientValidationFailure()
    {
        $data = [
            'nome' => '',
            'cpf' => 'invalid',
            'celular' => 'invalid',
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/pacientes', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'cpf', 'celular']);
    }

    public function testUpdatePatientSuccess()
    {
        $paciente = Paciente::factory()->create();

        $data = [
            'nome' => $this->faker->name,
            'celular' => $this->faker->numerify('#############'),
        ];

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/pacientes/{$paciente->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'nome', 'cpf', 'celular'])
            ->assertJson($data);

        $this->assertDatabaseHas('pacientes', $data);
    }

    public function testUpdatePatientValidationFailure()
    {
        $paciente = Paciente::factory()->create();

        $data = [
            'nome' => '',
            'celular' => 'invalid',
        ];

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/pacientes/{$paciente->id}", $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'celular']);
    }

    public function testUnauthorizedAccess()
    {
        $response = $this->postJson('/api/pacientes', []);

        $response->assertStatus(401);
    }

    public function testUpdateNonExistentPatient()
    {
        $nonExistentId = 9999;

        $data = [
            'nome' => $this->faker->name,
            'celular' => $this->faker->numerify('#############'),
        ];

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/pacientes/{$nonExistentId}", $data);

        $response->assertStatus(404);
    }
}