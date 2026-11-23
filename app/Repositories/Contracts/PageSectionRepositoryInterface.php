<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Persistence contract for editable page sections.
 */
interface PageSectionRepositoryInterface extends RepositoryInterface
{
    /** Every section of one page in display order. */
    public function forPage(string $page): Collection;

    /** All sections grouped by page for the dashboard index. */
    public function groupedByPage(): Collection;
}
