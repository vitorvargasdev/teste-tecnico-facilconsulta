<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    public function testShouldAttemptLogin()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $result = $this->authService->attemptLogin($credentials);

        $this->assertIsString($result);
        $this->assertAuthenticatedAs($user);
    }

    public function testShouldFailLoginWithInvalidCredentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ];

        $result = $this->authService->attemptLogin($credentials);

        $this->assertFalse($result);
        $this->assertGuest();
    }

    public function testShouldGetAuthenticatedUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $authenticatedUser = $this->authService->getAuthenticatedUser();

        $this->assertInstanceOf(User::class, $authenticatedUser);
        $this->assertEquals($user->id, $authenticatedUser->id);
    }

    public function testShouldLogoutUser()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->authService->attemptLogin([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->authService->logout();
        $this->assertGuest();
    }

    public function testShouldRefreshToken()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Auth::shouldReceive('refresh')->once()->andReturn('new_token');

        $newToken = $this->authService->refreshToken();

        $this->assertEquals('new_token', $newToken);
    }

    public function testShouldGetTokenTTL()
    {
        $expectedTTL = 3600;

        Auth::shouldReceive('factory->getTTL')->once()->andReturn(60);

        $ttl = $this->authService->getTokenTTL();

        $this->assertEquals($expectedTTL, $ttl);
    }
}

