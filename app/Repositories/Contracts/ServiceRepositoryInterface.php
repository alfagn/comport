<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;

/**
 * Persistence contract for the Service entity.
 *
 * Extends the base CRUD contract; add Service-specific finders here so
 * services never build queries themselves.
 */
interface ServiceRepositoryInterface extends RepositoryInterface
{
    //
}
