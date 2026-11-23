<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;

/**
 * Persistence contract for job applications.
 */
interface JobApplicationRepositoryInterface extends RepositoryInterface
{
    /** Applications still awaiting review, shown as the sidebar badge. */
    public function newCount(): int;
}
