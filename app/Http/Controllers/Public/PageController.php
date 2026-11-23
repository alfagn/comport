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
        return view('pages.project', ['project' => $this->projects->findActiveBySlug($slug)]);
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
        return view('pages.founder', ['member' => $this->members->findFounderBySlug($slug)]);
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

        return view('pages.job', ['job' => $job]);
    }

    /** Partners page. */
    public function partners(): View
    {
        return view('pages.partners', ['partners' => $this->partners->all()->where('is_active', true)->sortBy('sort_order')->values()]);
    }

    /** Blog index: published posts. */
    public function blog(): View
    {
        return view('pages.blog', ['posts' => $this->posts->published()]);
    }

    /** Blog article page (article, video episode or photo essay). */
    public function post(string $slug): View
    {
        return view('pages.post', ['post' => $this->posts->findPublishedBySlug($slug)]);
    }

    /** Contact page with the services list for the brief form. */
    public function contact(): View
    {
        return view('pages.contact', ['services' => $this->services->all()->where('is_active', true)->sortBy('sort_order')->values()]);
    }
}
