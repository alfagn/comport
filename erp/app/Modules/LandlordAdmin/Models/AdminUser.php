<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class AdminUser
 *
 * Super Administrator account stored in the landlord database.
 * Controls tenant provisioning, subscription approvals, and CMS content.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role super_admin|support|content_editor
 * @property bool $is_active
 */
class AdminUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'landlord';

    protected $table = 'admin_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
