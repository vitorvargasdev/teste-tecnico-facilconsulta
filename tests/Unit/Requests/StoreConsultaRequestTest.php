<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreConsultaRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Cidade;

class StoreConsultaRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreConsultaRequest();
    }

    public function testValidationRules()
    {
        $rules = $this->request->rules();

        $this->assertEquals([
            'medico_id' => 'required|integer|exists:medicos,id',
            'paciente_id' => 'required|integer|exists:pacientes,id',
            'data' => 'required|date|date_format:Y-m-d H:i:s|after:today',
        ], $rules);
    }

    public function testValidInput()
    {
        $cidade = Cidade::factory()->create();
        $medico = Medico::factory()->create(['cidade_id' => $cidade->id]);
        $paciente = Paciente::factory()->create();

        $validator = Validator::make([
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id,
            'data' => now()->addDay()->format('Y-m-d H:i:s'),
        ], $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    public function testInvalidMedicoId()
    {
        $paciente = Paciente::factory()->create();

        $validator = Validator::make([
            'medico_id' => 9999, // Non-existent ID
            'paciente_id' => $paciente->id,
            'data' => now()->addDay()->format('Y-m-d H:i:s'),
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('medico_id', $validator->errors()->toArray());
    }

    public function testInvalidPacienteId()
    {
        $cidade = Cidade::factory()->create();
        $medico = Medico::factory()->create(['cidade_id' => $cidade->id]);

        $validator = Validator::make([
            'medico_id' => $medico->id,
            'paciente_id' => 9999, // Non-existent ID
            'data' => now()->addDay()->format('Y-m-d H:i:s'),
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('paciente_id', $validator->errors()->toArray());
    }

    public function testInvalidDate()
    {
        $cidade = Cidade::factory()->create();
        $medico = Medico::factory()->create(['cidade_id' => $cidade->id]);
        $paciente = Paciente::factory()->create();

        $validator = Validator::make([
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id,
            'data' => now()->subDay()->format('Y-m-d H:i:s'), // Past date
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('data', $validator->errors()->toArray());
    }

    public function testInvalidDateFormat()
    {
        $cidade = Cidade::factory()->create();
        $medico = Medico::factory()->create(['cidade_id' => $cidade->id]);
        $paciente = Paciente::factory()->create();

        $validator = Validator::make([
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id,
            'data' => now()->addDay()->format('d/m/Y H:i:s'), // Wrong format
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('data', $validator->errors()->toArray());
    }

    public function testMissingFields()
    {
        $validator = Validator::make([], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('medico_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('paciente_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('data', $validator->errors()->toArray());
    }
}