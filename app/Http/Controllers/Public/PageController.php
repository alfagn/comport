<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;
use App\Repositories\Contracts\IndustryRepositoryInterface;
use App\Repositories\Contracts\JobPostRepositoryInterface;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Repositories\Contracts\PartnerRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Contracts\View\View;

/**
 * Serves every public marketing page.
 *
 * Controllers stay thin: they pull ready-to-render collections from
 * repositories (via contracts) and hand them to Blade. No queries, no
 * business rules here.
 */
class PageController extends Controller
{
    public function __construct(
        private HeroSlideRepositoryInterface $heroSlides,
        private ServiceRepositoryInterface $services,
        private IndustryRepositoryInterface $industries,
        private ProjectRepositoryInterface $projects,
        private MemberRepositoryInterface $members,
        private JobPostRepositoryInterface $jobs,
        private PostRepositoryInterface $posts,
        private PartnerRepositoryInterface $partners,
    ) {}

    /** Home: hero slider, solutions, featured work, founders, partners, journal. */
    public function home(): View
    {
        return view('pages.index', [
            'slides' => $this->heroSlides->all()->where('is_active', true)->sortBy('sort_order'),
            'services' => $this->services->all()->where('is_active', true)->sortBy('sort_order')->values(),
            'industries' => $this->industries->all()->where('is_active', true)->sortBy('sort_order')->values(),
            'projects' => $this->projects->featured(),
            'founders' => $this->members->founders(),
            'partners' => $this->partners->all()->where('is_active', true)->sortBy('sort_order')->values(),
            'posts' => $this->posts->latestPublished(3),
        ]);
    }

    /** About page with the founders strip. */
    public function about(): View
    {
        return view('pages.about', ['founders' => $this->members->founders()]);
    }

    /** Work index: every active case study. */
    public function work(): View
    {
        return view('pages.work', ['projects' => $this->projects->activeWithTags()]);
    }

    /** Case-study detail page. */
    public function project(string $slug): View
    {
        $project = $this->projects->findActiveBySlug($slug);
        // The pager links to the next live project in sort order, wrapping around.
        $next = $this->projects->nextAfter($project);

        return view('pages.project', ['project' => $project, 'nextProject' => $next]);
    }

    /** Team page: founders plus the wider team grid. */
    public function team(): View
    {
        return view('pages.team', [
            'founders' => $this->members->founders(),
            'members' => $this->members->team(),
        ]);
    }

    /** Founder profile page. */
    public function founder(string $slug): View
    {
        $member = $this->members->findFounderBySlug($slug);

        return view('pages.founder', [
            'member' => $member,
            // The remaining founders, for the "other founders" strip.
            'otherFounders' => $this->members->founders()->where('id', '!=', $member->id),
            // Featured projects shared across founder profiles.
            'projects' => $this->projects->featured()->take(2),
        ]);
    }

    /** Careers index: all open positions. */
    public function careers(): View
    {
        return view('pages.careers', ['jobs' => $this->jobs->all()->where('is_active', true)->sortBy('sort_order')->values()]);
    }

    /** Job detail page. */
    public function job(string $slug): View
    {
        $job = $this->jobs->all()->where('is_active', true)->where('slug', $slug)->first();
        abort_unless($job, 404);

        return view('pages.job', [
            'job' => $job,
            // Remaining openings, for the "other positions" grid.
            'otherJobs' => $this->jobs->all()->where('is_active', true)->where('id', '!=', $job->id)->take(3),
            // Real count of open positions, for the "all N open jobs" links.
            'openCount' => $this->jobs->all()->where('is_active', true)->count(),
        ]);
    }

    /** Partners page. */
    public function partners(): View
    {
        return view('pages.partners', ['partners' => $this->partners->all()->where('is_active', true)->sortBy('sort_order')->values()]);
    }

    /** Blog index: published posts. */
    public function blog(): View
    {
        // The header search panel submits here with ?q=.
        $q = trim((string) request('q', ''));
        $posts = $this->posts->published();
        if ($q !== '') {
            $posts = $posts->filter(fn ($post) => str_contains(strtolower($post->title.' '.$post->excerpt.' '.$post->category), strtolower($q)))->values();
        }

        return view('pages.blog', ['posts' => $posts, 'q' => $q]);
    }

    /** Blog article page (article, video episode or photo essay). */
    public function post(string $slug): View
    {
        $post = $this->posts->findPublishedBySlug($slug);

        return view('pages.post', [
            'post' => $post,
            // Two latest other published posts for "Continue reading".
            'relatedPosts' => $this->posts->latestPublished()->where('id', '!=', $post->id)->take(2),
        ]);
    }

    /** Contact page with the services list for the brief form. */
    public function contact(): View
    {
        return view('pages.contact', ['services' => $this->services->all()->where('is_active', true)->sortBy('sort_order')->values()]);
    }
}
