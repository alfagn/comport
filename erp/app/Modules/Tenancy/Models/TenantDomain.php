<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TenantDomain
 *
 * Entity mapping subdomains or custom verified domains to a specific tenant.
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $domain
 * @property bool $is_primary
 * @property bool $is_custom
 * @property bool $is_verified
 * @property string|null $verification_token
 * @property Carbon|null $verified_at
 * @property-read Tenant $tenant
 */
class TenantDomain extends Model
{
    /**
     * Stored in landlord database.
     *
     * @var string
     */
    protected $connection = 'landlord';

    /**
     * Associated table.
     *
     * @var string
     */
    protected $table = 'tenant_domains';

    protected $fillable = [
        'tenant_id',
        'domain',
        'is_primary',
        'is_custom',
        'is_verified',
        'verification_token',
        'verified_at',
    ];

    /**
     * Casts.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'is_custom' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
