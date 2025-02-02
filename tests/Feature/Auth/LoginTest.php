<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use \App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_login(): void
    {
        $response = $this->post('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $this->assertNotEmpty($response->json('access_token'));
        $this->assertEquals($response->json('token_type'), 'bearer');
        $this->assertEquals($response->json('expires_in'), 3600);
    }

    public function test_me(): void
    {
        $token = $this->post('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ])->json('access_token');

        $response = $this->get('/api/user', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);

        $this->assertEquals($response->json('name'), $this->user->name);
        $this->assertEquals($response->json('email'), $this->user->email);
        $this->assertEquals($response->json('email_verified_at'), $this->user->email_verified_at);
        $this->assertNotEmpty($response->json('created_at'));
        $this->assertNotEmpty($response->json('updated_at'));
    }
}

