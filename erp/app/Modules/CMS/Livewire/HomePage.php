<?php

declare(strict_types=1);

namespace App\Modules\CMS\Livewire;

use App\Modules\CMS\Models\Partner;
use App\Modules\CMS\Models\PortfolioProject;
use App\Modules\CMS\Models\Post;
use App\Modules\CMS\Models\SiteSetting;
use App\Modules\CMS\Models\TeamMember;
use App\Modules\CMS\Models\Testimonial;
use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.site')]
class HomePage extends Component
{
    public function mount(): mixed
    {
        // If accessed under tenant context, redirect to workspace
        if (Tenant::current() !== null) {
            return redirect()->route('tenant.dashboard');
        }

        return null;
    }

    public function render(): View
    {
        $heroHeadline = (string) SiteSetting::get('hero_headline', 'We build scalable software & elevate market-defining brands.');
        $heroSubheadline = (string) SiteSetting::get('hero_subheadline', 'Empetix is a software engineering and growth marketing studio. We design, build, and scale enterprise web/mobile applications and high-ROAS marketing engines under one roof.');
        $tickerText = (string) SiteSetting::get('announcement_ticker', 'Now Partnering with Series A-C Startups & Enterprise Groups Across Cairo, Riyadh & London');

        $statProjects = (string) SiteSetting::get('stat_projects', '140+');
        $statRevenue = (string) SiteSetting::get('stat_revenue', '$18M+');
        $statUptime = (string) SiteSetting::get('stat_uptime', '99.4%');
        $statRating = (string) SiteSetting::get('stat_rating', '4.9/5');

        $featuredProjects = PortfolioProject::query()
            ->where('is_published', true)
            ->orderByDesc('is_featured')
            ->latest('completion_date')
            ->take(4)
            ->get();

        $founders = TeamMember::query()
            ->where('is_founder', true)
            ->orderBy('sort_order')
            ->get();

        $testimonials = Testimonial::query()
            ->where('is_published', true)
            ->latest()
            ->take(6)
            ->get();

        $partners = Partner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $latestPosts = Post::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('modules.cms.livewire.home-page', [
            'heroHeadline' => $heroHeadline,
            'heroSubheadline' => $heroSubheadline,
            'tickerText' => $tickerText,
            'statProjects' => $statProjects,
            'statRevenue' => $statRevenue,
            'statUptime' => $statUptime,
            'statRating' => $statRating,
            'featuredProjects' => $featuredProjects,
            'founders' => $founders,
            'testimonials' => $testimonials,
            'partners' => $partners,
            'latestPosts' => $latestPosts,
        ]);
    }
}
