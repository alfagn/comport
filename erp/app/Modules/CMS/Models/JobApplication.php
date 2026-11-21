<?php

declare(strict_types=1);

namespace App\Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class JobApplication
 *
 * Candidate resumes and job applications submitted via careers page.
 */
class JobApplication extends Model
{
    protected $connection = 'landlord';

    protected $table = 'job_applications';

    protected $fillable = [
        'job_posting_id',
        'candidate_name',
        'candidate_email',
        'candidate_phone',
        'resume_path',
        'cover_letter',
        'portfolio_url',
        'status', // new, reviewing, shortlisted, rejected, hired
    ];

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'job_posting_id');
    }
}
