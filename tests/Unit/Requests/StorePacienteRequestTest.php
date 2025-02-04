<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StorePacienteRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StorePacienteRequestTest extends TestCase
{
    protected $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new StorePacienteRequest();
    }

    public function testValidationRules()
    {
        $rules = $this->request->rules();

        $this->assertEquals([
            'nome' => 'required|string',
            'cpf' => 'required|string|min:11|max:14',
            'celular' => 'required|string|min:13|max:16',
        ], $rules);
    }

    public function testValidInput()
    {
        $validator = Validator::make([
            'nome' => 'João Silva',
            'cpf' => '123.456.789-01',
            'celular' => '(11) 98765-4321',
        ], $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    public function testInvalidInput()
    {
        $validator = Validator::make([
            'nome' => '',
            'cpf' => '123',
            'celular' => '123',
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
        $this->assertArrayHasKey('cpf', $validator->errors()->toArray());
        $this->assertArrayHasKey('celular', $validator->errors()->toArray());
    }

    public function testMissingFields()
    {
        $validator = Validator::make([], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
        $this->assertArrayHasKey('cpf', $validator->errors()->toArray());
        $this->assertArrayHasKey('celular', $validator->errors()->toArray());
    }
}