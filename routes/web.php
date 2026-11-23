<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Public\ContactMessageController;
use App\Http\Controllers\Public\JobApplicationController;
use App\Http\Controllers\Public\NewsletterController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\SeoDiscoveryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
| The converted Empetix marketing site. All content comes from the CMS
| (entities + editable page sections) via repositories.
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/work', [PageController::class, 'work'])->name('work');
Route::get('/work/{slug}', [PageController::class, 'project'])->name('work.show');
Route::get('/team', [PageController::class, 'team'])->name('team');
Route::get('/founders/{slug}', [PageController::class, 'founder'])->name('founders.show');
Route::get('/careers', [PageController::class, 'careers'])->name('careers');
Route::get('/careers/{slug}', [PageController::class, 'job'])->name('jobs.show');
Route::post('/careers/{slug}/apply', [JobApplicationController::class, 'store'])->name('jobs.apply');
Route::get('/partners', [PageController::class, 'partners'])->name('partners');
Route::get('/blog', [PageController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [PageController::class, 'post'])->name('blog.show');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');
Route::post('/subscribe', [NewsletterController::class, 'store'])->name('subscribe');

/*
|--------------------------------------------------------------------------
| SEO, Sitemap & AI Discovery Standards
|--------------------------------------------------------------------------
| Dynamic sitemap.xml, search-engine & AI-crawler robots.txt, and
| llms.txt / llms-full.txt following modern AI ingestion standards.
*/
Route::get('/sitemap.xml', [SeoDiscoveryController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoDiscoveryController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [SeoDiscoveryController::class, 'llms'])->name('llms');
Route::get('/llms-full.txt', [SeoDiscoveryController::class, 'llmsFull'])->name('llms.full');

/*
|--------------------------------------------------------------------------
| Dashboard (CMS)
|--------------------------------------------------------------------------
| Session-authenticated admin area under /admin. There is no public
| registration; the admin user is seeded.
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest-only screens.
    Route::middleware('guest')->group(function () {
        Route::get('/login', [Admin\AuthController::class, 'create'])->name('login');
        Route::post('/login', [Admin\AuthController::class, 'store'])->name('login.store');
    });

    // Authenticated CMS screens.
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'destroy'])->name('logout');

        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        // Page content: every editable text/image block of the site.
        Route::middleware('permission:manage pages')->group(function () {
            Route::get('/pages', [Admin\PageSectionController::class, 'index'])->name('pages.index');
            Route::get('/pages/{page}', [Admin\PageSectionController::class, 'edit'])->name('pages.edit');
            Route::put('/pages/{page}', [Admin\PageSectionController::class, 'update'])->name('pages.update');
        });

        // Content entities. Each section is enforced server-side by its
        // permission; the sidebar only hides what the server already blocks.
        Route::resource('hero-slides', Admin\HeroSlideController::class)->except('show')->middleware('permission:manage hero slides');
        Route::resource('services', Admin\ServiceController::class)->except('show')->middleware('permission:manage services');
        Route::resource('industries', Admin\IndustryController::class)->except('show')->middleware('permission:manage industries');
        Route::resource('projects', Admin\ProjectController::class)->except('show')->middleware('permission:manage projects');
        Route::resource('members', Admin\MemberController::class)->except('show')->middleware('permission:manage members');
        Route::resource('jobs', Admin\JobPostController::class)->except('show')->middleware('permission:manage jobs');
        Route::resource('posts', Admin\PostController::class)->except('show')->middleware('permission:manage posts');
        Route::resource('partners', Admin\PartnerController::class)->except('show')->middleware('permission:manage partners');
        Route::resource('navigation', Admin\NavItemController::class)->except('show')->parameters(['navigation' => 'id'])->middleware('permission:manage navigation');
        Route::resource('content-lists', Admin\ContentListController::class)->except('show')->parameters(['content-lists' => 'id'])->middleware('permission:manage content lists');

        // Admin accounts + roles/permissions (the "owner" role bypasses).
        Route::resource('admins', Admin\AdminUserController::class)->except('show')->middleware(['permission:manage admins', 'role:owner']);
        Route::resource('roles', Admin\RoleController::class)->except('show')->middleware(['permission:manage admins', 'role:owner']);

        // Inbox: contact briefs + newsletter subscribers.
        Route::middleware('permission:manage inbox')->group(function () {
            Route::get('/inbox', [Admin\InboxController::class, 'index'])->name('inbox.index');
            Route::get('/inbox/subscribers', [Admin\InboxController::class, 'subscribers'])->name('inbox.subscribers');
            Route::get('/inbox/{id}', [Admin\InboxController::class, 'show'])->name('inbox.show');
            Route::delete('/inbox/{id}', [Admin\InboxController::class, 'destroy'])->name('inbox.destroy');
        });

        // Job applications (CVs download only through this authenticated group).
        Route::middleware('permission:manage applications')->group(function () {
            Route::get('/applications', [Admin\ApplicationController::class, 'index'])->name('applications.index');
            Route::get('/applications/{id}', [Admin\ApplicationController::class, 'show'])->name('applications.show');
            Route::get('/applications/{application}/cv', [Admin\ApplicationController::class, 'cv'])->name('applications.cv');
            Route::delete('/applications/{id}', [Admin\ApplicationController::class, 'destroy'])->name('applications.destroy');
        });

        // Site settings, SEO & Sitemap.
        Route::middleware('permission:manage settings')->group(function () {
            Route::get('/settings', [Admin\SettingsController::class, 'edit'])->name('settings.edit');
            Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');

            // SEO & AI Search Discovery
            Route::get('/seo', [Admin\SeoController::class, 'index'])->name('seo.index');
            Route::put('/seo', [Admin\SeoController::class, 'update'])->name('seo.update');

            // Dynamic XML Sitemap
            Route::get('/sitemap', [Admin\SitemapController::class, 'index'])->name('sitemap.index');
            Route::post('/sitemap/regenerate', [Admin\SitemapController::class, 'regenerate'])->name('sitemap.regenerate');
            Route::post('/sitemap/ping', [Admin\SitemapController::class, 'ping'])->name('sitemap.ping');
        });
    });
});
