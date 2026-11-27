<?php

namespace App\Models;

use App\Casts\EncryptedTextOrPlain;
use Database\Factories\SupportChatSessionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Laravel\Ai\Concerns\HasConversations;
use Laravel\Ai\Models\Conversation;

/**
 * A customer conversation across Website live-chat or Meta WhatsApp Cloud API.
 *
 * Acts as the Laravel AI SDK conversation participant, and for WhatsApp threads
 * also owns rich `WhatsappMessage` records (media, voice notes, locations, ticks).
 */
class SupportChatSession extends Model
{
    /** @use HasFactory<SupportChatSessionFactory> */
    use HasConversations, HasFactory;

    public const CHANNEL_WEB = 'web';

    public const CHANNEL_WHATSAPP = 'whatsapp';

    public const STATUS_OPEN = 'open';

    public const STATUS_HANDED_OFF = 'handed_off';

    public const STATUS_CLOSED = 'closed';

    /** @var array<string, string> */
    public const STATUS_LABELS = [
        self::STATUS_OPEN => 'Open',
        self::STATUS_HANDED_OFF => 'Handed off',
        self::STATUS_CLOSED => 'Closed',
    ];

    protected $fillable = [
        'token', 'conversation_id', 'channel', 'visitor_name', 'visitor_username',
        'visitor_avatar', 'visitor_email', 'visitor_phone', 'external_thread_id',
        'status', 'ai_paused', 'is_pinned', 'is_archived', 'handoff_summary', 'contact_notes',
        'contact_message_id', 'message_count', 'unread_count', 'ip_address',
        'user_agent', 'landing_page', 'last_external_message_id', 'last_message_preview',
        'last_message_at', 'last_admin_read_at', 'last_customer_message_at',
        'last_unread_notified_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ai_paused' => 'boolean',
            'is_pinned' => 'boolean',
            'is_archived' => 'boolean',
            'message_count' => 'integer',
            'unread_count' => 'integer',
            'last_message_preview' => EncryptedTextOrPlain::class,
            'contact_notes' => EncryptedTextOrPlain::class,
            'handoff_summary' => EncryptedTextOrPlain::class,
            'last_message_at' => 'datetime',
            'last_admin_read_at' => 'datetime',
            'last_customer_message_at' => 'datetime',
            'last_unread_notified_at' => 'datetime',
        ];
    }

    /** The SDK conversation holding this chat's AI transcript. */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    /** Rich WhatsApp messages (text, media, voice notes, statuses, reactions). */
    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'support_chat_session_id');
    }

    /** The Inbox brief created when the chat was handed off to the team. */
    public function contactMessage(): BelongsTo
    {
        return $this->belongsTo(ContactMessage::class);
    }

    /** Scope: chats the AI handed to a human. */
    public function scopeHandedOff(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_HANDED_OFF);
    }

    /** Scope: WhatsApp Cloud API conversations. */
    public function scopeWhatsapp(Builder $query): Builder
    {
        return $query->where('channel', self::CHANNEL_WHATSAPP);
    }

    /** Scope: Website widget conversations only. */
    public function scopeWeb(Builder $query): Builder
    {
        return $query->where('channel', self::CHANNEL_WEB);
    }

    /** Whether this session runs over WhatsApp. */
    public function isWhatsapp(): bool
    {
        return $this->channel === self::CHANNEL_WHATSAPP;
    }

    /** Whether Meta's free 24-hour customer service reply window is currently open. */
    public function withinCustomerServiceWindow(): bool
    {
        if ($this->last_customer_message_at !== null) {
            return $this->last_customer_message_at->gt(now()->subHours(24));
        }

        $latestInbound = $this->whatsappMessages()
            ->where('direction', WhatsappMessage::DIRECTION_INBOUND)
            ->latest('created_at')
            ->value('created_at');

        return $latestInbound !== null && Carbon::parse($latestInbound)->gt(now()->subHours(24));
    }

    /** Whether the visitor can still send messages. */
    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /** Human label for the current status. */
    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    /** Best available visitor label for dashboard listings. */
    public function visitorLabel(): string
    {
        return $this->visitor_name
            ?: ($this->visitor_phone ?: ($this->visitor_email ?: 'Anonymous visitor #'.$this->id));
    }
}
