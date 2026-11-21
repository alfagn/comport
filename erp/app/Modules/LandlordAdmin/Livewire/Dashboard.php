<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Modules\Billing\Models\Subscription;
use App\Modules\Billing\Models\SubscriptionInvoice;
use App\Modules\CMS\Models\ContactMessage;
use App\Modules\CMS\Models\JobApplication;
use App\Modules\CMS\Models\JobPosting;
use App\Modules\CMS\Models\Partner;
use App\Modules\CMS\Models\PortfolioProject;
use App\Modules\CMS\Models\Post;
use App\Modules\CMS\Models\TeamMember;
use App\Modules\Tenancy\Models\Tenant;

/**
 * Class Dashboard
 *
 * Super Administrator main dashboard with financial, tenant growth, and CMS KPIs.
 */
class Dashboard extends BaseComponent
{
    public function markMessageAsRead(int $id): void
    {
        $msg = ContactMessage::on('landlord')->find($id);
        if ($msg) {
            $msg->update(['is_read' => true]);
            $this->toastSuccess('Message marked as read');
        }
    }

    public function render()
    {
        // 1. SaaS Metrics
        $totalTenants = Tenant::on('landlord')->count();
        $activeTenants = Tenant::on('landlord')->where('is_active', true)->count();
        $activeSubscriptions = Subscription::on('landlord')->whereIn('status', ['active', 'trialing'])->count();
        $totalRevenueMinor = (int) SubscriptionInvoice::on('landlord')->where('status', 'paid')->sum('amount_minor');

        // 2. CMS Website Metrics
        $postsCount = Post::on('landlord')->count();
        $portfolioCount = PortfolioProject::on('landlord')->count();
        $teamCount = TeamMember::on('landlord')->count();
        $openJobsCount = JobPosting::on('landlord')->where('is_active', true)->count();
        $applicationsCount = JobApplication::on('landlord')->count();
        $unreadMessagesCount = ContactMessage::on('landlord')->where('is_read', false)->count();
        $partnersCount = Partner::on('landlord')->count();

        // 3. Recent Inquiries & Job Applications
        $recentInquiries = ContactMessage::on('landlord')->latest()->take(5)->get();
        $recentApplications = JobApplication::on('landlord')->with('jobPosting')->latest()->take(5)->get();
        $recentTenants = Tenant::on('landlord')->with(['currentSubscription.plan', 'primaryDomain'])->latest()->take(5)->get();

        return view('admin::livewire.dashboard', [
            'totalTenants' => $totalTenants,
            'activeTenants' => $activeTenants,
            'activeSubscriptions' => $activeSubscriptions,
            'totalRevenueMinor' => $totalRevenueMinor,
            'postsCount' => $postsCount,
            'portfolioCount' => $portfolioCount,
            'teamCount' => $teamCount,
            'openJobsCount' => $openJobsCount,
            'applicationsCount' => $applicationsCount,
            'unreadMessagesCount' => $unreadMessagesCount,
            'partnersCount' => $partnersCount,
            'recentInquiries' => $recentInquiries,
            'recentApplications' => $recentApplications,
            'recentTenants' => $recentTenants,
        ])->layout('layouts.admin', [
            'title' => 'Mission Control — Empetix CMS & SaaS',
            'header' => app()->getLocale() === 'ar' ? 'لوحة المتابعة الشاملة وإدارة الموقع' : 'Mission Control & Website Intelligence',
        ]);
    }
}
