<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AuthRequestTest extends TestCase
{
    protected $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new AuthRequest();
    }

    public function testValidationRules()
    {
        $rules = $this->request->rules();

        $this->assertEquals([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ], $rules);
    }

    public function testValidInput()
    {
        $validator = Validator::make([
            'email' => 'user@example.com',
            'password' => 'password123',
        ], $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    public function testInvalidEmail()
    {
        $validator = Validator::make([
            'email' => 'not-an-email',
            'password' => 'password123',
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function testMissingEmail()
    {
        $validator = Validator::make([
            'password' => 'password123',
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function testMissingPassword()
    {
        $validator = Validator::make([
            'email' => 'user@example.com',
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function testEmptyFields()
    {
        $validator = Validator::make([
            'email' => '',
            'password' => '',
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }
}