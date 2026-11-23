<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Persistence contract for repeating content list cards.
 */
interface ContentListItemRepositoryInterface extends RepositoryInterface
{
    /** All active cards of one list, in display order. */
    public function activeForGroup(string $group): Collection;
}
