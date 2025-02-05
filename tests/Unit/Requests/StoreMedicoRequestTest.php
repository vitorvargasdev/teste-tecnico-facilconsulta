<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreMedicoRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Cidade;

class StoreMedicoRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreMedicoRequest();
    }

    public function testValidationRules()
    {
        $rules = $this->request->rules();

        $this->assertEquals([
            'nome' => 'required|string',
            'especialidade' => 'required|string',
            'cidade_id' => 'required|integer|exists:cidades,id',
        ], $rules);
    }

    public function testValidInput()
    {
        $cidade = Cidade::factory()->create();

        $validator = Validator::make([
            'nome' => 'Dr. João Silva',
            'especialidade' => 'Cardiologia',
            'cidade_id' => $cidade->id,
        ], $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    public function testInvalidInput()
    {
        $validator = Validator::make([
            'nome' => '',
            'especialidade' => '',
            'cidade_id' => 'not_an_integer',
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
        $this->assertArrayHasKey('especialidade', $validator->errors()->toArray());
        $this->assertArrayHasKey('cidade_id', $validator->errors()->toArray());
    }

    public function testMissingFields()
    {
        $validator = Validator::make([], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
        $this->assertArrayHasKey('especialidade', $validator->errors()->toArray());
        $this->assertArrayHasKey('cidade_id', $validator->errors()->toArray());
    }

    public function testNonExistentCidadeId()
    {
        $validator = Validator::make([
            'nome' => 'Dr. João Silva',
            'especialidade' => 'Cardiologia',
            'cidade_id' => 9999, // Assuming this ID doesn't exist
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cidade_id', $validator->errors()->toArray());
    }
}