<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Persistence contract for navigation links.
 */
interface NavItemRepositoryInterface extends RepositoryInterface
{
    /** All active links of one menu, in display order. */
    public function activeForMenu(string $menu): Collection;
}
