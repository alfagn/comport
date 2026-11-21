<?php

declare(strict_types=1);

namespace App\Modules\TenantCore\Models;

use App\Modules\HRM\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * Tenant Workspace User entity residing on the 'tenant' connection.
 * Supports RBAC via spatie/laravel-permission.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $phone
 * @property string|null $avatar
 * @property string $locale
 * @property bool $is_active
 * @property Carbon|null $email_verified_at
 */
class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $connection = 'tenant';

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'locale',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }
}
