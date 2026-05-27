<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_active_is_cast_to_boolean(): void
    {
        $category = Category::factory()->create(['is_active' => true]);

        $this->assertTrue($category->fresh()->is_active);
    }

    public function test_sla_hours_is_cast_to_integer(): void
    {
        $category = Category::factory()->create(['sla_hours' => 24]);

        $this->assertIsInt($category->fresh()->sla_hours);
        $this->assertSame(24, $category->fresh()->sla_hours);
    }

    public function test_scope_active_returns_only_active(): void
    {
        Category::factory()->count(2)->create(['is_active' => true]);
        Category::factory()->count(1)->create(['is_active' => false]);

        $this->assertCount(2, Category::active()->get());
    }

    public function test_auto_assign_technician_relationship(): void
    {
        $technician = User::factory()->technician()->create();
        $category = Category::factory()->create([
            'auto_assign_technician_id' => $technician->id,
        ]);

        $this->assertInstanceOf(User::class, $category->autoAssignTechnician);
        $this->assertSame($technician->id, $category->autoAssignTechnician->id);
    }

    public function test_auto_assign_technician_can_be_null(): void
    {
        $category = Category::factory()->create(['auto_assign_technician_id' => null]);

        $this->assertNull($category->autoAssignTechnician);
    }
}
