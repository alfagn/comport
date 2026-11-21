<?php

declare(strict_types=1);

namespace App\Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TeamMember
 *
 * Founders, executive leadership, and core team member profiles.
 * Stored on landlord connection.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $role_title
 * @property string|null $bio
 * @property string|null $avatar
 * @property bool $is_founder
 * @property array<string, string>|null $social_links
 * @property int $sort_order
 */
class TeamMember extends Model
{
    protected $connection = 'landlord';

    protected $table = 'team_members';

    protected $fillable = [
        'name',
        'slug',
        'role_title',
        'bio',
        'avatar',
        'is_founder',
        'social_links',
        'sort_order',
    ];

    protected $casts = [
        'is_founder' => 'boolean',
        'social_links' => 'array',
        'sort_order' => 'integer',
    ];
}
