<?php

declare(strict_types=1);

namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ExpenseCategory
 *
 * Expense categorization taxonomy (e.g. Hosting & Cloud, Marketing Ads, Office Supplies, Travel).
 */
class ExpenseCategory extends Model
{
    protected $connection = 'tenant';

    protected $table = 'expense_categories';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}
