<?php

declare(strict_types=1);

namespace App\Modules\Finance\Models;

use App\Core\ValueObjects\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InvoiceItem
 *
 * Line item on an invoice.
 *
 * @property int $id
 * @property int $invoice_id
 * @property string $description
 * @property float $quantity
 * @property int $unit_price_minor
 * @property int $total_minor
 */
class InvoiceItem extends Model
{
    protected $connection = 'tenant';

    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price_minor',
        'total_minor',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price_minor' => 'integer',
        'total_minor' => 'integer',
    ];

    /**
     * @return BelongsTo<Invoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function getTotalAttribute(): Money
    {
        /** @var Invoice|null $invoice */
        $invoice = $this->invoice;

        return new Money($this->total_minor, $invoice?->currency ?: 'EGP');
    }
}
