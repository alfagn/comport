<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;

/**
 * Persistence contract for the Setting entity.
 *
 * Extends the base CRUD contract; add Setting-specific finders here so
 * services never build queries themselves.
 */
interface SettingRepositoryInterface extends RepositoryInterface
{
    //
}
