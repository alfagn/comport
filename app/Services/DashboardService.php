<?php

namespace App\Services;

use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use App\Repositories\Contracts\JobPostRepositoryInterface;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Repositories\Contracts\PartnerRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;

/**
 * Aggregates the numbers and lists shown on the dashboard home page.
 * Keeps the controller free of query orchestration.
 */
class DashboardService
{
    public function __construct(
        private ProjectRepositoryInterface $projects,
        private ServiceRepositoryInterface $services,
        private MemberRepositoryInterface $members,
        private JobPostRepositoryInterface $jobs,
        private PostRepositoryInterface $posts,
        private PartnerRepositoryInterface $partners,
        private ContactMessageRepositoryInterface $messages,
    ) {}

    /**
     * Stat cards: entity counts plus the unread inbox badge.
     */
    public function stats(): array
    {
        return [
            'projects' => $this->projects->count(),
            'services' => $this->services->count(),
            'members' => $this->members->count(),
            'jobs' => $this->jobs->count(),
            'posts' => $this->posts->count(),
            'partners' => $this->partners->count(),
            'unread_messages' => $this->messages->unreadCount(),
        ];
    }

    /** Latest contact briefs for the dashboard home widget. */
    public function latestMessages()
    {
        return $this->messages->latest(5);
    }
}
