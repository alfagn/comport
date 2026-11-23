<?php

namespace App\DTOs;

use App\Core\DTOs\BaseData;

/**
 * Typed input carrier for a job application between the HTTP layer and
 * JobApplicationService. The CV itself never enters the DTO: the service
 * receives the UploadedFile separately and stores it on the private disk.
 */
class JobApplicationData extends BaseData
{
    protected static array $fields = [
        'job_post_id', 'full_name', 'email', 'phone', 'location',
        'linkedin', 'portfolio', 'experience_years', 'notice_period',
        'achievement', 'cv_path', 'status',
    ];

    public mixed $job_post_id = null;
    public mixed $full_name = null;
    public mixed $email = null;
    public mixed $phone = null;
    public mixed $location = null;
    public mixed $linkedin = null;
    public mixed $portfolio = null;
    public mixed $experience_years = null;
    public mixed $notice_period = null;
    public mixed $achievement = null;
    public mixed $cv_path = null;
    public mixed $status = null;
}
