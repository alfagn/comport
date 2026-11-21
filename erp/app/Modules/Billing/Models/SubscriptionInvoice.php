<?php

declare(strict_types=1);

namespace App\Modules\Billing\Models;

use App\Core\ValueObjects\Money;
use App\Modules\Tenancy\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SubscriptionInvoice
 *
 * Landlord invoice record for SaaS tenant subscription billing.
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $subscription_id
 * @property string $invoice_number
 * @property int $amount_minor
 * @property string $currency
 * @property string $status pending|paid|rejected|refunded
 * @property string $payment_method manual_bank_transfer|instapay|vodafone_cash|card
 * @property string|null $receipt_path
 * @property string|null $notes
 * @property Carbon|null $paid_at
 * @property-read Tenant $tenant
 * @property-read Subscription $subscription
 */
class SubscriptionInvoice extends Model
{
    protected $connection = 'landlord';

    protected $table = 'subscription_invoices';

    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'invoice_number',
        'amount_minor',
        'currency',
        'status',
        'payment_method',
        'receipt_path',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount_minor' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function getAmountAttribute(): Money
    {
        return new Money($this->amount_minor, $this->currency);
    }
}
