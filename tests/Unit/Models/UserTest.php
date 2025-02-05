<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCreation()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(password_verify('password123', $user->password));
    }

    public function testUserFillableAttributes()
    {
        $fillable = ['name', 'email', 'password'];
        $user = new User;

        $this->assertEquals($fillable, $user->getFillable());
    }

    public function testUserHiddenAttributes()
    {
        $hidden = ['password', 'remember_token'];
        $user = new User;

        $this->assertEquals($hidden, $user->getHidden());
    }

    public function testUserCasts()
    {
        $user = new User;
        $casts = $user->getCasts();

        $this->assertIsArray($casts);
        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertArrayHasKey('password', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
        $this->assertEquals('hashed', $casts['password']);
    }

    public function testUserImplementsJWTSubject()
    {
        $user = new User;

        $this->assertInstanceOf(JWTSubject::class, $user);
    }

    public function testGetJWTIdentifier()
    {
        $user = User::factory()->create();

        $this->assertEquals($user->id, $user->getJWTIdentifier());
    }

    public function testGetJWTCustomClaims()
    {
        $user = new User;

        $this->assertIsArray($user->getJWTCustomClaims());
        $this->assertEmpty($user->getJWTCustomClaims());
    }
}