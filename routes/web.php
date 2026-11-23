<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Public\ContactMessageController;
use App\Http\Controllers\Public\JobApplicationController;
use App\Http\Controllers\Public\NewsletterController;
use App\Http\Controllers\Public\PageController;
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
        Route::get('/pages', [Admin\PageSectionController::class, 'index'])->name('pages.index');
        Route::get('/pages/{page}', [Admin\PageSectionController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [Admin\PageSectionController::class, 'update'])->name('pages.update');

        // Content entities.
        Route::resource('hero-slides', Admin\HeroSlideController::class)->except('show');
        Route::resource('services', Admin\ServiceController::class)->except('show');
        Route::resource('industries', Admin\IndustryController::class)->except('show');
        Route::resource('projects', Admin\ProjectController::class)->except('show');
        Route::resource('members', Admin\MemberController::class)->except('show');
        Route::resource('jobs', Admin\JobPostController::class)->except('show');
        Route::resource('posts', Admin\PostController::class)->except('show');
        Route::resource('partners', Admin\PartnerController::class)->except('show');
        Route::resource('navigation', Admin\NavItemController::class)->except('show')->parameters(['navigation' => 'id']);
        Route::resource('content-lists', Admin\ContentListController::class)->except('show')->parameters(['content-lists' => 'id']);

        // Inbox: contact briefs + newsletter subscribers.
        Route::get('/inbox', [Admin\InboxController::class, 'index'])->name('inbox.index');
        Route::get('/inbox/subscribers', [Admin\InboxController::class, 'subscribers'])->name('inbox.subscribers');
        Route::get('/inbox/{id}', [Admin\InboxController::class, 'show'])->name('inbox.show');
        Route::delete('/inbox/{id}', [Admin\InboxController::class, 'destroy'])->name('inbox.destroy');

        // Job applications (CVs download only through this authenticated group).
        Route::get('/applications', [Admin\ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{id}', [Admin\ApplicationController::class, 'show'])->name('applications.show');
        Route::get('/applications/{application}/cv', [Admin\ApplicationController::class, 'cv'])->name('applications.cv');
        Route::delete('/applications/{id}', [Admin\ApplicationController::class, 'destroy'])->name('applications.destroy');

        // Site settings.
        Route::get('/settings', [Admin\SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
    });
});
