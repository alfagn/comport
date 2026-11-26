<?php

namespace Database\Factories;

use App\Models\SupportChatSession;
use App\Models\WhatsappMessage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<WhatsappMessage>
 */
class WhatsappMessageFactory extends Factory
{
    protected $model = WhatsappMessage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'support_chat_session_id' => SupportChatSession::factory()->state([
                'channel' => SupportChatSession::CHANNEL_WHATSAPP,
                'external_thread_id' => '2010'.fake()->numerify('########'),
                'visitor_phone' => '+2010'.fake()->numerify('########'),
            ]),
            'wa_message_id' => 'wamid.'.Str::upper(Str::random(20)),
            'direction' => WhatsappMessage::DIRECTION_INBOUND,
            'sender_type' => WhatsappMessage::SENDER_CUSTOMER,
            'type' => 'text',
            'body' => fake()->sentence(),
            'status' => WhatsappMessage::STATUS_RECEIVED,
        ];
    }
}
