<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;

/**
 * Persistence contract for the JobPost entity.
 *
 * Extends the base CRUD contract; add JobPost-specific finders here so
 * services never build queries themselves.
 */
interface JobPostRepositoryInterface extends RepositoryInterface
{
    //
}
