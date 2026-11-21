<?php

declare(strict_types=1);

namespace App\Modules\CRM\Models;

use App\Core\ValueObjects\Money;
use App\Modules\TenantCore\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CrmDeal
 *
 * Sales opportunity tracked through pipeline stages.
 *
 * @property int $id
 * @property string $title
 * @property int $crm_client_id
 * @property string $stage discovery|proposal|negotiation|closed_won|closed_lost
 * @property int $amount_minor
 * @property string $currency
 * @property Carbon|null $expected_close_date
 * @property int|null $assigned_to
 */
class CrmDeal extends Model
{
    protected $connection = 'tenant';

    protected $table = 'crm_deals';

    protected $fillable = [
        'title',
        'crm_client_id',
        'stage',
        'amount_minor',
        'currency',
        'expected_close_date',
        'assigned_to',
    ];

    protected $casts = [
        'amount_minor' => 'integer',
        'expected_close_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'crm_client_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getAmountAttribute(): Money
    {
        return new Money($this->amount_minor, $this->currency ?: 'EGP');
    }
}
