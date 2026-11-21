<?php

declare(strict_types=1);

namespace App\Modules\Projects\Models;

use App\Core\ValueObjects\Money;
use App\Modules\CRM\Models\CrmClient;
use App\Modules\TenantCore\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Project
 *
 * Software development / client project entity.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int|null $client_id
 * @property string|null $description
 * @property int $budget_minor
 * @property string $currency
 * @property string $status planning|in_progress|on_hold|completed|canceled
 * @property Carbon|null $start_date
 * @property Carbon|null $deadline
 * @property int|null $leader_id
 */
class Project extends Model
{
    protected $connection = 'tenant';

    protected $table = 'projects';

    protected $fillable = [
        'name',
        'slug',
        'client_id',
        'description',
        'budget_minor',
        'currency',
        'status',
        'start_date',
        'deadline',
        'leader_id',
    ];

    protected $casts = [
        'budget_minor' => 'integer',
        'start_date' => 'date',
        'deadline' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'client_id');
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class, 'project_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function getBudgetAttribute(): Money
    {
        return new Money($this->budget_minor, $this->currency ?: 'EGP');
    }
}
