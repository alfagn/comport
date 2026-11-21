<?php

declare(strict_types=1);

use App\Modules\CMS\Livewire\AboutPage;
use App\Modules\CMS\Livewire\BlogPage;
use App\Modules\CMS\Livewire\BlogPostPage;
use App\Modules\CMS\Livewire\CareersPage;
use App\Modules\CMS\Livewire\ContactPage;
use App\Modules\CMS\Livewire\FounderPage;
use App\Modules\CMS\Livewire\HomePage;
use App\Modules\CMS\Livewire\JobDetailPage;
use App\Modules\CMS\Livewire\PartnersPage;
use App\Modules\CMS\Livewire\ProjectDetailPage;
use App\Modules\CMS\Livewire\TeamPage;
use App\Modules\CMS\Livewire\WorkPage;
use App\Modules\CRM\Livewire\LeadsIndex;
use App\Modules\Finance\Livewire\InvoicesIndex;
use App\Modules\HRM\Livewire\EmployeesIndex;
use App\Modules\HRM\Livewire\PayrollIndex;
use App\Modules\LandlordAdmin\Livewire\CmsApplicationsIndex;
use App\Modules\LandlordAdmin\Livewire\CmsCareersIndex;
use App\Modules\LandlordAdmin\Livewire\CmsInquiriesIndex;
use App\Modules\LandlordAdmin\Livewire\CmsPartnersIndex;
use App\Modules\LandlordAdmin\Livewire\CmsPortfolioIndex;
use App\Modules\LandlordAdmin\Livewire\CmsPostsIndex;
use App\Modules\LandlordAdmin\Livewire\CmsSettings;
use App\Modules\LandlordAdmin\Livewire\CmsTeamIndex;
use App\Modules\LandlordAdmin\Livewire\CmsTestimonialsIndex;
use App\Modules\LandlordAdmin\Livewire\Dashboard as AdminDashboard;
use App\Modules\LandlordAdmin\Livewire\SubscriptionsIndex;
use App\Modules\LandlordAdmin\Livewire\TenantsIndex;
use App\Modules\Marketing\Livewire\CampaignsIndex;
use App\Modules\Projects\Livewire\ProjectsIndex;
use App\Modules\Tenancy\Livewire\TenantSubscription;
use App\Modules\Tenancy\Models\Tenant;
use App\Modules\TenantCore\Livewire\Dashboard as TenantDashboard;
use App\Modules\TenantCore\Livewire\TenantSettings;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Language / Locale Switcher
|--------------------------------------------------------------------------
*/
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'en'], true)) {
        Session::put('locale', $locale);
        App::setLocale($locale);
    }

    return back();
})->name('locale.switch');

/*
|--------------------------------------------------------------------------
| Tenant Workspace Routes Group (Subdomain Context)
|--------------------------------------------------------------------------
*/
Route::middleware(['tenant.needed', 'tenant.active', 'tenant.subscribed'])->group(function () {
    Route::get('/workspace', TenantDashboard::class)->name('tenant.dashboard');
    Route::get('/workspace/hrm/employees', EmployeesIndex::class)->name('tenant.hrm.employees');
    Route::get('/workspace/hrm/payroll', PayrollIndex::class)->name('tenant.hrm.payroll');
    Route::get('/workspace/crm/leads', LeadsIndex::class)->name('tenant.crm.leads');
    Route::get('/workspace/marketing/campaigns', CampaignsIndex::class)->name('tenant.marketing.campaigns');
    Route::get('/workspace/projects', ProjectsIndex::class)->name('tenant.projects.index');
    Route::get('/workspace/finance/invoices', InvoicesIndex::class)->name('tenant.finance.invoices');
    Route::get('/workspace/subscription', TenantSubscription::class)->name('tenant.subscription');
    Route::get('/workspace/settings', TenantSettings::class)->name('tenant.settings');
});

/*
|--------------------------------------------------------------------------
| Tenant Workspace Path-Fallback Group (/t/{slug}/...)
| Allows testing multi-tenancy locally without DNS wildcard/hosts editing.
|--------------------------------------------------------------------------
*/
Route::prefix('t/{tenant_slug}')->group(function () {
    Route::middleware([
        'tenant.path',
        'tenant.active',
        'tenant.subscribed',
    ])->group(function () {
        Route::get('/', TenantDashboard::class)->name('tenant.path.dashboard');
        Route::get('/hrm/employees', EmployeesIndex::class)->name('tenant.path.hrm.employees');
        Route::get('/hrm/payroll', PayrollIndex::class)->name('tenant.path.hrm.payroll');
        Route::get('/crm/leads', LeadsIndex::class)->name('tenant.path.crm.leads');
        Route::get('/marketing/campaigns', CampaignsIndex::class)->name('tenant.path.marketing.campaigns');
        Route::get('/projects', ProjectsIndex::class)->name('tenant.path.projects.index');
        Route::get('/finance/invoices', InvoicesIndex::class)->name('tenant.path.finance.invoices');
        Route::get('/subscription', TenantSubscription::class)->name('tenant.path.subscription');
        Route::get('/settings', TenantSettings::class)->name('tenant.path.settings');
    });
});

/*
|--------------------------------------------------------------------------
| Landlord Super Admin Panel (/admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::get('/', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/tenants', TenantsIndex::class)->name('admin.tenants');
    Route::get('/subscriptions', SubscriptionsIndex::class)->name('admin.subscriptions');
    Route::get('/invoices', SubscriptionsIndex::class)->name('admin.invoices');

    // Complete Website CMS Management Suite
    Route::get('/cms/settings', CmsSettings::class)->name('admin.cms.settings');
    Route::get('/cms/team', CmsTeamIndex::class)->name('admin.cms.team');
    Route::get('/cms/portfolio', CmsPortfolioIndex::class)->name('admin.cms.portfolio');
    Route::get('/cms/posts', CmsPostsIndex::class)->name('admin.cms.posts');
    Route::get('/cms/careers', CmsCareersIndex::class)->name('admin.cms.careers');
    Route::get('/cms/applications', CmsApplicationsIndex::class)->name('admin.cms.applications');
    Route::get('/cms/partners', CmsPartnersIndex::class)->name('admin.cms.partners');
    Route::get('/cms/inquiries', CmsInquiriesIndex::class)->name('admin.cms.inquiries');
    Route::get('/cms/testimonials', CmsTestimonialsIndex::class)->name('admin.cms.testimonials');
});

/*
|--------------------------------------------------------------------------
| Central Public Website & CMS Routes (Pure Livewire + Blade)
| Fully managed by Landlord Admin CMS
|--------------------------------------------------------------------------
*/
Route::get('/', HomePage::class)->name('home');
Route::get('/about', AboutPage::class)->name('site.about');
Route::get('/work', WorkPage::class)->name('site.work');
Route::get('/work/{slug}', ProjectDetailPage::class)->name('site.work.detail');
Route::get('/blog', BlogPage::class)->name('site.blog');
Route::get('/blog/{slug}', BlogPostPage::class)->name('site.blog.detail');
Route::get('/team', TeamPage::class)->name('site.team');
Route::get('/founder/{slug}', FounderPage::class)->name('site.founder');
Route::get('/careers', CareersPage::class)->name('site.careers');
Route::get('/careers/{slug}', JobDetailPage::class)->name('site.job.detail');
Route::get('/partners', PartnersPage::class)->name('site.partners');
Route::get('/contact', ContactPage::class)->name('site.contact');
Route::view('/pricing', 'site.pricing')->name('site.pricing');

// Legacy .html routes redirection for seamless navigation from source site assets
Route::redirect('/founder-ayman.html', '/founder/ayman');
Route::redirect('/founder-bishoy.html', '/founder/bishoy');
Route::redirect('/founder-mohanad.html', '/founder/mohanad');
Route::redirect('/founder.html', '/founder/mohanad');
Route::redirect('/project-nilepay.html', '/work/nilepay');
Route::redirect('/blog-post.html', '/blog/arabic-first');
Route::redirect('/blog-photos.html', '/blog/behind-the-lens');
Route::redirect('/blog-video.html', '/blog/design-sprint');
Route::redirect('/careers.html', '/careers');
Route::redirect('/work.html', '/work');
Route::redirect('/about.html', '/about');
Route::redirect('/team.html', '/team');
Route::redirect('/contact.html', '/contact');
Route::redirect('/partners.html', '/partners');
Route::redirect('/blog.html', '/blog');
Route::redirect('/index.html', '/');
