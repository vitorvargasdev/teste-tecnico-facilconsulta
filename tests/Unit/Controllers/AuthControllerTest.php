<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\AuthController;
use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\Middleware;
use Tests\TestCase;
use Mockery;

class AuthControllerTest extends TestCase
{
    protected $authService;

    protected $authController;

    protected function setUp(): void {
        parent::setUp();
        $this->authService = Mockery::mock(AuthService::class);
        $this->authController = new AuthController($this->authService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testMiddleware()
    {
        $middleware = AuthController::middleware();
        $this->assertIsArray($middleware);
        $this->assertCount(1, $middleware);
        $this->assertInstanceOf(Middleware::class, $middleware[0]);
        $this->assertEquals('auth:api', $middleware[0]->middleware);
        $this->assertEquals(['login'], $middleware[0]->except);
    }

    public function testLoginSuccess()
    {
        $request = Mockery::mock(AuthRequest::class);
        $request->shouldReceive('validated')->andReturn(['email' => 'test@example.com', 'password' => 'password']);

        $this->authService->shouldReceive('attemptLogin')->andReturn('fake_token');
        $this->authService->shouldReceive('getTokenTTL')->andReturn(3600);

        $response = $this->authController->login($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'access_token' => 'fake_token',
            'token_type' => 'bearer',
            'expires_in' => 3600
        ], $response->getData(true));
    }

    public function testLoginFailure()
    {
        $request = Mockery::mock(AuthRequest::class);
        $request->shouldReceive('validated')->andReturn(['email' => 'test@example.com', 'password' => 'wrong_password']);

        $this->authService->shouldReceive('attemptLogin')->andReturn(false);

        $response = $this->authController->login($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['error' => 'Unauthorized'], $response->getData(true));
    }

    public function testMe()
    {
        $user = ['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com'];
        $this->authService->shouldReceive('getAuthenticatedUser')->andReturn($user);

        $response = $this->authController->me();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($user, $response->getData(true));
    }

    public function testLogout()
    {
        $this->authService->shouldReceive('logout')->once();

        $response = $this->authController->logout();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Successfully logged out'], $response->getData(true));
    }

    public function testRefresh()
    {
        $this->authService->shouldReceive('refreshToken')->andReturn('new_fake_token');
        $this->authService->shouldReceive('getTokenTTL')->andReturn(3600);

        $response = $this->authController->refresh();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'access_token' => 'new_fake_token',
            'token_type' => 'bearer',
            'expires_in' => 3600
        ], $response->getData(true));
    }
}
