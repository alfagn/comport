<?php

declare(strict_types=1);

namespace App\Modules\Finance\Models;

use App\Core\ValueObjects\Money;
use App\Modules\Projects\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Expense
 *
 * Operational or project-specific company expense record.
 *
 * @property int $id
 * @property int|null $category_id
 * @property int|null $project_id
 * @property string $title
 * @property int $amount_minor
 * @property string $currency
 * @property Carbon $date
 * @property string|null $receipt_path
 * @property string|null $vendor_name
 * @property string|null $notes
 */
class Expense extends Model
{
    protected $connection = 'tenant';

    protected $table = 'expenses';

    protected $fillable = [
        'category_id',
        'project_id',
        'title',
        'amount_minor',
        'currency',
        'date',
        'receipt_path',
        'vendor_name',
        'notes',
    ];

    protected $casts = [
        'amount_minor' => 'integer',
        'date' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function getAmountAttribute(): Money
    {
        return new Money($this->amount_minor, $this->currency ?: 'EGP');
    }
}
