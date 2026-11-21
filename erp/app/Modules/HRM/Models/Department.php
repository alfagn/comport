<?php

declare(strict_types=1);

namespace App\Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Department
 *
 * Organizational department within a tenant company.
 */
class Department extends Model
{
    protected $connection = 'tenant';

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function designations(): HasMany
    {
        return $this->hasMany(Designation::class, 'department_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department_id');
    }
}
