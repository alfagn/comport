<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\Member;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Eloquent-backed repository for Member records (founders + team).
 */
class MemberRepository extends EloquentRepository implements MemberRepositoryInterface
{
    protected string $model = Member::class;

    /** Active founders in display order. */
    public function founders(): Collection
    {
        return $this->query()->founders()->get();
    }

    /** Active regular team members in display order. */
    public function team(): Collection
    {
        return $this->query()->team()->get();
    }

    /** An active founder by slug for the public profile page. */
    public function findFounderBySlug(string $slug): Member
    {
        return $this->query()->founders()->where('slug', $slug)->firstOrFail();
    }
}
