<?php

declare(strict_types=1);

namespace App\Modules\Finance\Models;

use App\Core\ValueObjects\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Payment
 *
 * Payment transaction received against a client invoice.
 *
 * @property int $id
 * @property int $invoice_id
 * @property int $amount_minor
 * @property string $currency
 * @property string $payment_method bank_transfer|card|cash|cheque|online
 * @property string|null $transaction_reference
 * @property Carbon $payment_date
 * @property string|null $notes
 */
class Payment extends Model
{
    protected $connection = 'tenant';

    protected $table = 'payments';

    protected $fillable = [
        'invoice_id',
        'amount_minor',
        'currency',
        'payment_method',
        'transaction_reference',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'amount_minor' => 'integer',
        'payment_date' => 'date',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function getAmountAttribute(): Money
    {
        return new Money($this->amount_minor, $this->currency ?: 'EGP');
    }
}
