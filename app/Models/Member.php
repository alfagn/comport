<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * A person: founder (rich profile + detail page) or team member (grid card).
 */
class Member extends Model
{
    protected $fillable = [
        'member_code', 'user_id', 'name', 'slug', 'role', 'kind', 'department', 'focus', 'lead', 'bio', 'expertise', 'photo',
        'email', 'linkedin', 'twitter', 'sort_order', 'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Member $member) {
            if (blank($member->member_code)) {
                $member->member_code = static::generateNextCode();
            } else {
                $member->member_code = strtoupper(trim((string) $member->member_code));
            }

            $member->slug = static::generateUniqueSlug($member->slug ?: $member->name);

            if (is_string($member->email)) {
                $clean = strtolower(trim($member->email));
                $member->email = $clean !== '' ? $clean : null;
            }
        });

        static::updating(function (Member $member) {
            if (blank($member->member_code)) {
                $member->member_code = static::generateNextCode($member->id);
            } else {
                $member->member_code = strtoupper(trim((string) $member->member_code));
            }

            if (blank($member->slug) || $member->isDirty('slug')) {
                $member->slug = static::generateUniqueSlug($member->slug ?: $member->name, $member->id);
            }

            if (is_string($member->email)) {
                $clean = strtolower(trim($member->email));
                $member->email = $clean !== '' ? $clean : null;
            }
        });
    }

    /** Generate a guaranteed-unique URL slug from a name or candidate slug. */
    public static function generateUniqueSlug(?string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug((string) $source) ?: 'member';
        $candidate = $base;
        $suffix = 2;

        while (
            static::query()
                ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }

    /** Generate the next guaranteed-unique sequential Team Member ID (e.g. EMP-001). */
    public static function generateNextCode(?int $ignoreId = null): string
    {
        $codes = static::query()
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->whereNotNull('member_code')
            ->pluck('member_code');

        $max = 0;
        foreach ($codes as $code) {
            if (preg_match('/^EMP-(\d+)$/i', (string) $code, $m)) {
                $num = (int) $m[1];
                if ($num > $max) {
                    $max = $num;
                }
            }
        }

        $next = $max + 1;
        do {
            $candidate = 'EMP-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            $exists = static::query()
                ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                ->where('member_code', $candidate)
                ->exists();
            $next++;
        } while ($exists);

        return $candidate;
    }

    protected $casts = [
        'bio' => 'array',
        'expertise' => 'array',        // array of paragraphs
        'is_active' => 'boolean',
    ];

    /** Optional relation to admin dashboard user account. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Scope: founders only, in display order. */
    public function scopeFounders($query)
    {
        return $query->where('kind', 'founder')->where('is_active', true)->orderBy('sort_order');
    }

    /** Scope: regular team members only, in display order. */
    public function scopeTeam($query)
    {
        return $query->where('kind', 'member')->where('is_active', true)->orderBy('sort_order');
    }
}
