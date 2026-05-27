<?php

namespace Tests\Feature\Models;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_is_cast_to_enum(): void
    {
        $user = User::factory()->create(['role' => UserRole::TECHNICIAN]);

        $this->assertInstanceOf(UserRole::class, $user->fresh()->role);
        $this->assertSame(UserRole::TECHNICIAN, $user->fresh()->role);
    }

    public function test_is_active_is_cast_to_boolean(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->assertTrue($user->fresh()->is_active);
    }

    public function test_preferences_is_cast_to_array(): void
    {
        $prefs = ['notifications' => true, 'theme' => 'dark'];
        $user = User::factory()->create(['preferences' => $prefs]);

        $this->assertIsArray($user->fresh()->preferences);
        $this->assertEquals($prefs, $user->fresh()->preferences);
    }

    public function test_password_is_hidden(): void
    {
        $user = User::factory()->create();

        $this->assertArrayNotHasKey('password', $user->toArray());
        $this->assertArrayNotHasKey('remember_token', $user->toArray());
    }

    public function test_scope_active_filters_correctly(): void
    {
        User::factory()->count(3)->create(['is_active' => true]);
        User::factory()->count(2)->create(['is_active' => false]);

        $this->assertCount(3, User::active()->get());
    }

    public function test_scope_technicians_filters_by_role(): void
    {
        User::factory()->technician()->count(2)->create();
        User::factory()->count(3)->create(['role' => UserRole::CUSTOMER]);

        $this->assertCount(2, User::technicians()->get());
    }

    public function test_scope_customers_filters_by_role(): void
    {
        User::factory()->count(3)->create(['role' => UserRole::CUSTOMER]);
        User::factory()->technician()->count(2)->create();

        $this->assertCount(3, User::customers()->get());
    }

    public function test_soft_delete_works(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->assertSoftDeleted($user);
        $this->assertNull(User::find($user->id));
        $this->assertNotNull(User::withTrashed()->find($user->id));
    }
}
