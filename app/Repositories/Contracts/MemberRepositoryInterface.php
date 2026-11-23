<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Persistence contract for the Member entity (founders + team).
 */
interface MemberRepositoryInterface extends RepositoryInterface
{
    /** Active founders in display order. */
    public function founders(): Collection;

    /** Active regular team members in display order. */
    public function team(): Collection;

    /** An active founder by slug for the public profile page. */
    public function findFounderBySlug(string $slug): \App\Models\Member;
}
