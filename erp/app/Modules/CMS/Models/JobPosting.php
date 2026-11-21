<?php

declare(strict_types=1);

namespace App\Modules\CMS\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class JobPosting
 *
 * Career vacancies posted on the company website.
 * Stored on landlord connection.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $department
 * @property string $location
 * @property string $employment_type full_time|part_time|contract|remote
 * @property string $description
 * @property string|null $requirements
 * @property string|null $salary_range
 * @property bool $is_active
 * @property Carbon|null $expires_at
 */
class JobPosting extends Model
{
    protected $connection = 'landlord';

    protected $table = 'job_postings';

    protected $fillable = [
        'title',
        'slug',
        'department',
        'location',
        'employment_type',
        'description',
        'requirements',
        'salary_range',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'date',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_posting_id');
    }
}
