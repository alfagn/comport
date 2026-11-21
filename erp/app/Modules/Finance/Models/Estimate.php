<?php

declare(strict_types=1);

namespace App\Modules\Finance\Models;

use App\Core\ValueObjects\Money;
use App\Modules\CRM\Models\CrmClient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Estimate
 *
 * Formal quotation/estimate sent to client before invoice issuance.
 *
 * @property int $id
 * @property int $client_id
 * @property string $estimate_number
 * @property Carbon $issue_date
 * @property Carbon $expiry_date
 * @property int $subtotal_minor
 * @property int $tax_minor
 * @property int $total_minor
 * @property string $currency
 * @property string $status draft|sent|accepted|declined
 */
class Estimate extends Model
{
    protected $connection = 'tenant';

    protected $table = 'estimates';

    protected $fillable = [
        'client_id',
        'estimate_number',
        'issue_date',
        'expiry_date',
        'subtotal_minor',
        'tax_minor',
        'total_minor',
        'currency',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'subtotal_minor' => 'integer',
        'tax_minor' => 'integer',
        'total_minor' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'client_id');
    }

    public function getTotalAttribute(): Money
    {
        return new Money($this->total_minor, $this->currency ?: 'EGP');
    }
}
