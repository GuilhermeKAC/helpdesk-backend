<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ---- register ----

    public function test_register_creates_user_and_returns_token(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['token', 'token_type', 'user'])
            ->assertJsonPath('user.email', 'joao@example.com')
            ->assertJsonPath('user.role', UserRole::CUSTOMER->value);

        $this->assertDatabaseHas('users', ['email' => 'joao@example.com']);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'joao@example.com']);

        $this->postJson('/api/v1/register', [
            'name' => 'João',
            'email' => 'joao@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_fails_with_password_mismatch(): void
    {
        $this->postJson('/api/v1/register', [
            'name' => 'João',
            'email' => 'joao@example.com',
            'password' => 'password123',
            'password_confirmation' => 'wrong',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    // ---- login ----

    public function test_login_returns_token(): void
    {
        $user = User::factory()->create([
            'email' => 'joao@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'joao@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'token_type', 'user'])
            ->assertJsonPath('token_type', 'Bearer');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'joao@example.com']);

        $this->postJson('/api/v1/login', [
            'email' => 'joao@example.com',
            'password' => 'wrong',
        ])->assertStatus(422);
    }

    public function test_login_fails_for_inactive_user(): void
    {
        User::factory()->create([
            'email' => 'joao@example.com',
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);

        $this->postJson('/api/v1/login', [
            'email' => 'joao@example.com',
            'password' => 'password123',
        ])->assertStatus(422);
    }

    public function test_login_updates_last_login_at(): void
    {
        $user = User::factory()->create([
            'email' => 'joao@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->postJson('/api/v1/login', [
            'email' => 'joao@example.com',
            'password' => 'password123',
        ])->assertStatus(200);

        $this->assertNotNull($user->fresh()->last_login_at);
    }

    // ---- logout ----

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/v1/logout')->assertStatus(401);
    }

    public function test_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/logout')
            ->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    // ---- me ----

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/me')->assertStatus(401);
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create(['name' => 'João Silva']);

        $this->actingAs($user)
            ->getJson('/api/v1/me')
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'João Silva')
            ->assertJsonPath('data.email', $user->email);
    }
}
