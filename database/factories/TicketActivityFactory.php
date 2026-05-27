<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketActivity>
 */
class TicketActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id'  => Ticket::factory(),
            'user_id'    => User::factory(),
            'action'     => fake()->randomElement(['created', 'assigned', 'status_changed', 'replied', 'closed']),
            'old_value'  => null,
            'new_value'  => null,
            'ip_address' => fake()->ipv4(),
        ];
    }
}
