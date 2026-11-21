<?php

declare(strict_types=1);

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Designation
 *
 * Job position / role designation within a department.
 */
class Designation extends Model
{
    protected $connection = 'tenant';

    protected $table = 'designations';

    protected $fillable = [
        'name',
        'department_id',
        'description',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'designation_id');
    }
}
