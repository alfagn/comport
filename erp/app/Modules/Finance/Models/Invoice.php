<?php

declare(strict_types=1);

namespace App\Modules\Finance\Models;

use App\Core\ValueObjects\Money;
use App\Modules\CRM\Models\CrmClient;
use App\Modules\Projects\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Invoice
 *
 * Client commercial invoice in the tenant database.
 *
 * @property int $id
 * @property int $client_id
 * @property int|null $project_id
 * @property string $invoice_number
 * @property Carbon $issue_date
 * @property Carbon $due_date
 * @property int $subtotal_minor
 * @property int $tax_minor
 * @property int $discount_minor
 * @property int $total_minor
 * @property int $amount_paid_minor
 * @property string $currency
 * @property string $status draft|sent|partial|paid|overdue|void
 * @property string|null $notes
 * @property string|null $terms
 */
class Invoice extends Model
{
    protected $connection = 'tenant';

    protected $table = 'invoices';

    protected $fillable = [
        'client_id',
        'project_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'subtotal_minor',
        'tax_minor',
        'discount_minor',
        'total_minor',
        'amount_paid_minor',
        'currency',
        'status',
        'notes',
        'terms',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal_minor' => 'integer',
        'tax_minor' => 'integer',
        'discount_minor' => 'integer',
        'total_minor' => 'integer',
        'amount_paid_minor' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'client_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function getTotalAttribute(): Money
    {
        return new Money($this->total_minor, $this->currency ?: 'EGP');
    }

    public function getSubtotalAttribute(): Money
    {
        return new Money($this->subtotal_minor, $this->currency ?: 'EGP');
    }

    public function getBalanceDueAttribute(): Money
    {
        $due = max(0, $this->total_minor - $this->amount_paid_minor);

        return new Money($due, $this->currency ?: 'EGP');
    }

    public function isFullyPaid(): bool
    {
        return $this->amount_paid_minor >= $this->total_minor;
    }
}
