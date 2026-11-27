<?php

namespace App\Models;

use App\Casts\EncryptedJsonOrPlain;
use App\Casts\EncryptedTextOrPlain;
use Database\Factories\WhatsappMessageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Rich message record for the dedicated WhatsApp Business Inbox.
 *
 * Tracks text, photos, videos, voice notes, audio, documents, stickers,
 * locations, reactions, quoted replies, delivery/read checkmarks, and
 * unread email notification state.
 */
class WhatsappMessage extends Model
{
    /** @use HasFactory<WhatsappMessageFactory> */
    use HasFactory;

    public const DIRECTION_INBOUND = 'inbound';

    public const DIRECTION_OUTBOUND = 'outbound';

    public const SENDER_CUSTOMER = 'customer';

    public const SENDER_ADMIN = 'admin';

    public const SENDER_AI = 'ai';

    public const SENDER_SYSTEM = 'system';

    public const STATUS_RECEIVED = 'received';

    public const STATUS_SENDING = 'sending';

    public const STATUS_SENT = 'sent';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_READ = 'read';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'support_chat_session_id',
        'wa_message_id',
        'idempotency_key',
        'reply_to_wa_message_id',
        'direction',
        'sender_type',
        'sender_user_id',
        'type',
        'body',
        'media_id',
        'media_disk',
        'media_path',
        'media_mime',
        'media_filename',
        'media_size',
        'media_duration',
        'reaction_emoji',
        'metadata',
        'status',
        'delivered_at',
        'read_at',
        'notified_email_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'body' => EncryptedTextOrPlain::class,
            'metadata' => EncryptedJsonOrPlain::class,
            'media_size' => 'integer',
            'media_duration' => 'integer',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
            'notified_email_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(SupportChatSession::class, 'support_chat_session_id');
    }

    public function senderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function scopeInbound(Builder $query): Builder
    {
        return $query->where('direction', self::DIRECTION_INBOUND);
    }

    public function scopeOutbound(Builder $query): Builder
    {
        return $query->where('direction', self::DIRECTION_OUTBOUND);
    }

    public function scopeUnreadByAdmin(Builder $query): Builder
    {
        return $query->where('direction', self::DIRECTION_INBOUND)->whereNull('read_at');
    }

    public function isInbound(): bool
    {
        return $this->direction === self::DIRECTION_INBOUND;
    }

    public function hasMedia(): bool
    {
        return filled($this->media_path) || filled($this->media_id);
    }

    /**
     * Compact preview line for the left contact list and quoted reply banners.
     */
    public function previewLabel(): string
    {
        $prefix = match ($this->type) {
            'image' => '📷 ',
            'video' => '🎥 ',
            'voice', 'audio' => '🎤 ',
            'document' => '📄 ',
            'sticker' => '🏷️ ',
            'location' => '📍 ',
            'contacts' => '👤 ',
            'template' => '📋 ',
            default => '',
        };

        $text = trim((string) ($this->body ?? ''));

        if ($text !== '' && ! str_starts_with($text, '[Customer sent ')) {
            return Str::limit($prefix.$text, 90);
        }

        return match ($this->type) {
            'image' => '📷 Photo',
            'video' => '🎥 Video',
            'voice' => '🎤 Voice message',
            'audio' => '🎵 Audio',
            'document' => '📄 '.($this->media_filename ?: 'Document'),
            'sticker' => '🏷️ Sticker',
            'location' => '📍 '.($this->metadata['name'] ?? 'Shared location'),
            'contacts' => '👤 Shared contact',
            'template' => '📋 Template: '.($this->metadata['template_name'] ?? 'message'),
            default => Str::limit($text ?: 'Message', 90),
        };
    }
}
