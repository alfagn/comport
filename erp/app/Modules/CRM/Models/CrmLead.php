<?php

declare(strict_types=1);

namespace App\Modules\CRM\Models;

use App\Core\ValueObjects\Money;
use App\Modules\TenantCore\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CrmLead
 *
 * Incoming business lead across digital marketing sources with Kanban pipeline statuses.
 *
 * @property int $id
 * @property string $title
 * @property string $source facebook|google|website|referral|linkedin
 * @property string $status new|contacted|qualified|proposal_sent|won|lost
 * @property int $estimated_value_minor
 * @property string $currency
 * @property string $contact_name
 * @property string|null $email
 * @property string|null $phone
 * @property int|null $assigned_to
 * @property string|null $notes
 */
class CrmLead extends Model
{
    protected $connection = 'tenant';

    protected $table = 'crm_leads';

    protected $fillable = [
        'title',
        'source',
        'status',
        'estimated_value_minor',
        'currency',
        'contact_name',
        'email',
        'phone',
        'assigned_to',
        'notes',
    ];

    protected $casts = [
        'estimated_value_minor' => 'integer',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getEstimatedValueAttribute(): Money
    {
        return new Money($this->estimated_value_minor, $this->currency ?: 'EGP');
    }
}
