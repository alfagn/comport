<?php

namespace App\Services;

use App\Core\Contracts\RepositoryInterface;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use App\Repositories\Contracts\JobPostRepositoryInterface;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Repositories\Contracts\PartnerRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;

/**
 * Aggregates everything the dashboard home page shows.
 *
 * Every number on a stat card is a real database figure: the headline count,
 * the 14-day activity sparkline (records created per day) and the
 * week-over-week delta (this week vs. the previous one). Nothing here is
 * decorative placeholder data — a card with no history simply shows an
 * em-dash and a flat line.
 */
class DashboardService
{
    /** Days of history behind each sparkline. */
    private const SPARKLINE_DAYS = 14;

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
     * Legacy flat count map kept for the topbar badge and tests.
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

    /**
     * Rich stat cards for the dashboard home: one card per managed entity,
     * each with its total, a real 14-day creation sparkline and an honest
     * week-over-week delta (null when there is no baseline to compare).
     *
     * @return array<int, array{key: string, label: string, count: int, delta: ?float, sparkline: array<int, int>, route: string}>
     */
    public function statCards(): array
    {
        return [
            $this->card('messages', 'Unread briefs', $this->messages->unreadCount(), $this->messages, 'admin.inbox.index'),
            $this->card('projects', 'Projects', $this->projects->count(), $this->projects, 'admin.projects.index'),
            $this->card('services', 'Services', $this->services->count(), $this->services, 'admin.services.index'),
            $this->card('members', 'Team & founders', $this->members->count(), $this->members, 'admin.members.index'),
            $this->card('jobs', 'Open roles', $this->jobs->count(), $this->jobs, 'admin.jobs.index'),
            $this->card('posts', 'Blog posts', $this->posts->count(), $this->posts, 'admin.posts.index'),
            $this->card('partners', 'Partners', $this->partners->count(), $this->partners, 'admin.partners.index'),
        ];
    }

    /**
     * Assemble one card from a repository. `count` is passed in so the
     * unread-briefs card can show its filtered headline while the trend
     * figures still describe the whole inbox.
     */
    private function card(string $key, string $label, int $count, RepositoryInterface $repository, string $route): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'count' => $count,
            'delta' => $this->weekOverWeekDelta($repository),
            'sparkline' => $this->sparkline($repository),
            'route' => $route,
        ];
    }

    /**
     * Week-over-week percentage change in created records: the last 7 days
     * against the 7 days before them. Null when the previous week had no
     * records — a percentage against zero would be meaningless.
     */
    private function weekOverWeekDelta(RepositoryInterface $repository): ?float
    {
        $thisWeekStart = now()->subDays(6)->startOfDay();
        $lastWeekStart = now()->subDays(13)->startOfDay();

        $thisWeek = $repository->countCreatedBetween($thisWeekStart, now());
        $lastWeek = $repository->countCreatedBetween($lastWeekStart, $thisWeekStart);

        if ($lastWeek === 0) {
            return null;
        }

        return round((($thisWeek - $lastWeek) / $lastWeek) * 100, 1);
    }

    /**
     * The last SPARKLINE_DAYS days of created-record counts, zero-filled so
     * the sparkline always has the same number of points.
     *
     * @return array<int, int>
     */
    private function sparkline(RepositoryInterface $repository): array
    {
        $counts = $repository->dailyCreatedCounts(self::SPARKLINE_DAYS);

        $points = [];
        for ($i = self::SPARKLINE_DAYS - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $points[] = $counts[$day] ?? 0;
        }

        return $points;
    }

    /** Latest contact briefs for the dashboard home widget. */
    public function latestMessages()
    {
        return $this->messages->latest(5);
    }
}
