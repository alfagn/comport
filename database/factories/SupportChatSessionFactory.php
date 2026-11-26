<?php

namespace Database\Factories;

use App\Models\SupportChatSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SupportChatSession>
 */
class SupportChatSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'token' => (string) Str::uuid(),
            'status' => SupportChatSession::STATUS_OPEN,
            'message_count' => 0,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'landing_page' => '/',
        ];
    }

    /** A chat where the visitor shared contact details and was handed to the team. */
    public function handedOff(): static
    {
        return $this->state(fn (): array => [
            'visitor_name' => fake()->name(),
            'visitor_email' => fake()->safeEmail(),
            'status' => SupportChatSession::STATUS_HANDED_OFF,
            'handoff_summary' => fake()->sentence(),
            'message_count' => 4,
            'last_message_at' => now(),
        ]);
    }
}
