<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'category_id' => Category::factory(),
            'title'       => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'status'      => TicketStatus::OPEN->value,
            'priority'    => TicketPriority::MEDIUM->value,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => TicketStatus::IN_PROGRESS->value]);
    }

    public function resolved(): static
    {
        return $this->state(fn () => [
            'status'          => TicketStatus::RESOLVED->value,
            'resolved_at'     => now(),
            'resolution_time' => fake()->numberBetween(30, 1440),
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn () => ['priority' => TicketPriority::URGENT->value]);
    }
}
