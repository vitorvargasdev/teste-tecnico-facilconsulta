<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdatePacienteRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdatePacienteRequestTest extends TestCase
{
    protected $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new UpdatePacienteRequest();
    }

    public function testValidationRules()
    {
        $rules = $this->request->rules();

        $this->assertEquals([
            'nome' => 'required|string',
            'celular' => 'required|string|min:13|max:16',
        ], $rules);
    }

    public function testValidInput()
    {
        $validator = Validator::make([
            'nome' => 'João Silva',
            'celular' => '(11) 98765-4321',
        ], $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    public function testInvalidInput()
    {
        $validator = Validator::make([
            'nome' => '',
            'celular' => '123',
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
        $this->assertArrayHasKey('celular', $validator->errors()->toArray());
    }

    public function testMissingFields()
    {
        $validator = Validator::make([], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
        $this->assertArrayHasKey('celular', $validator->errors()->toArray());
    }

    public function testCelularTooShort()
    {
        $validator = Validator::make([
            'nome' => 'João Silva',
            'celular' => '+55 123456',
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('celular', $validator->errors()->toArray());
    }

    public function testCelularTooLong()
    {
        $validator = Validator::make([
            'nome' => 'João Silva',
            'celular' => '+55 11 98765-43210',
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('celular', $validator->errors()->toArray());
    }
}