<?php

namespace Tests\Unit\Enums;

use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_all_cases_have_correct_values(): void
    {
        $this->assertSame('admin', UserRole::ADMIN->value);
        $this->assertSame('technician', UserRole::TECHNICIAN->value);
        $this->assertSame('customer', UserRole::CUSTOMER->value);
    }

    public function test_label_returns_portuguese_name(): void
    {
        $this->assertSame('Administrador', UserRole::ADMIN->label());
        $this->assertSame('Técnico', UserRole::TECHNICIAN->label());
        $this->assertSame('Cliente', UserRole::CUSTOMER->label());
    }

    public function test_can_be_created_from_string(): void
    {
        $this->assertSame(UserRole::ADMIN, UserRole::from('admin'));
        $this->assertSame(UserRole::TECHNICIAN, UserRole::from('technician'));
        $this->assertSame(UserRole::CUSTOMER, UserRole::from('customer'));
    }

    public function test_try_from_returns_null_for_invalid_value(): void
    {
        $this->assertNull(UserRole::tryFrom('superadmin'));
        $this->assertNull(UserRole::tryFrom(''));
    }

    public function test_has_three_cases(): void
    {
        $this->assertCount(3, UserRole::cases());
    }
}
