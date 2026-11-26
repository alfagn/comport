<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Public\ContactMessageController;
use App\Http\Controllers\Public\JobApplicationController;
use App\Http\Controllers\Public\MediaStreamController;
use App\Http\Controllers\Public\NewsletterController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\SeoDiscoveryController;
use App\Http\Controllers\Public\ServicePageController;
use App\Http\Controllers\Public\SupportChatController as PublicSupportChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
| The converted Empetix marketing site. All content comes from the CMS
| (entities + editable page sections) via repositories.
*/

Route::get('/', [PageController::class, 'home'])->name('home')->middleware('page.active:home');
Route::get('/about', [PageController::class, 'about'])->name('about')->middleware('page.active:about');

Route::middleware('page.active:work')->group(function () {
    Route::get('/work', [PageController::class, 'work'])->name('work');
    Route::get('/work/{slug}', [PageController::class, 'project'])->name('work.show');
});

Route::middleware('page.active:team')->group(function () {
    Route::get('/team', [PageController::class, 'team'])->name('team');
    Route::get('/founders/{slug}', [PageController::class, 'founder'])->name('founders.show');
});

Route::middleware('page.active:careers')->group(function () {
    Route::get('/careers', [PageController::class, 'careers'])->name('careers');
    Route::get('/careers/{slug}', [PageController::class, 'job'])->name('jobs.show');
    Route::post('/careers/{slug}/apply', [JobApplicationController::class, 'store'])->name('jobs.apply');
});

Route::get('/partners', [PageController::class, 'partners'])->name('partners')->middleware('page.active:partners');

Route::middleware('page.active:blog')->group(function () {
    Route::get('/blog', [PageController::class, 'blog'])->name('blog');
    Route::get('/blog/{slug}', [PageController::class, 'post'])->name('blog.show');
});

Route::middleware('page.active:contact')->group(function () {
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');
});

// English service pages. Archived language drafts have no public routes.
Route::middleware('page.active:services')->group(function () {
    Route::get('/services', [ServicePageController::class, 'index'])->name('services.index');
    Route::get('/services/{slug}', [ServicePageController::class, 'show'])->name('services.show');
});

Route::get('/media/video/{path}', [MediaStreamController::class, 'video'])
    ->where('path', 'videos/[A-Za-z0-9_\-\./]+')
    ->name('media.video');

Route::post('/subscribe', [NewsletterController::class, 'store'])->name('subscribe');

/*
|--------------------------------------------------------------------------
| Public AI Customer Support Chatbot
|--------------------------------------------------------------------------
*/
Route::prefix('support-chat')->name('support-chat.')->group(function () {
    Route::get('/session', [PublicSupportChatController::class, 'session'])->name('session');
    Route::post('/typing', [PublicSupportChatController::class, 'typing'])->name('typing');
    Route::post('/message', [PublicSupportChatController::class, 'message'])->name('message');
});

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
        Route::patch('/status-toggle/{entity}/{id}', Admin\StatusToggleController::class)->name('status.toggle');
        Route::post('/bulk-delete/{entity}', Admin\BulkDeleteController::class)->name('bulk.delete');

        // Personal account & team profile for any dashboard user / founder
        Route::get('/profile', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [Admin\ProfileController::class, 'update'])->name('profile.update');

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
        Route::middleware('permission:manage services')->prefix('service-pages')->name('service-pages.')->group(function () {
            Route::get('/', [Admin\ServicePageController::class, 'index'])->name('index');
            Route::get('/create', [Admin\ServicePageController::class, 'create'])->name('create');
            Route::post('/', [Admin\ServicePageController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [Admin\ServicePageController::class, 'edit'])->name('edit');
            Route::put('/{id}', [Admin\ServicePageController::class, 'update'])->name('update');
            Route::delete('/{id}', [Admin\ServicePageController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/preview', [Admin\ServicePageController::class, 'preview'])->name('preview');
        });

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

        // Dashboard AI Copilot (executes permission-scoped tasks + human approvals).
        Route::middleware('permission:use ai assistant')->prefix('assistant')->name('assistant.')->group(function () {
            Route::get('/', [Admin\AssistantController::class, 'index'])->name('index');
            Route::get('/conversations', [Admin\AssistantController::class, 'conversations'])->name('conversations');
            Route::get('/conversations/{conversation}', [Admin\AssistantController::class, 'show'])->name('show');
            Route::get('/conversations/{conversation}/attachments/{filename}', [Admin\AssistantController::class, 'attachment'])->name('attachment');
            Route::post('/message', [Admin\AssistantController::class, 'message'])->name('message');
            Route::post('/conversations/{conversation}/approve', [Admin\AssistantController::class, 'approve'])->name('approve');
            Route::delete('/conversations/{conversation}', [Admin\AssistantController::class, 'destroy'])->name('destroy');
        });

        // Dedicated WhatsApp Business Inbox Studio (completely separate from Email Inbox).
        Route::middleware('permission:manage support chats')->prefix('whatsapp')->name('whatsapp.')->group(function () {
            Route::get('/', [Admin\WhatsappInboxController::class, 'index'])->name('index');
            Route::get('/poll', [Admin\WhatsappInboxController::class, 'poll'])->name('poll');
            Route::post('/conversations', [Admin\WhatsappInboxController::class, 'startConversation'])->name('start');
            Route::get('/conversations/{id}', [Admin\WhatsappInboxController::class, 'show'])->whereNumber('id')->name('show');
            Route::patch('/conversations/{id}', [Admin\WhatsappInboxController::class, 'updateContact'])->whereNumber('id')->name('contact.update');
            Route::delete('/conversations/{id}', [Admin\WhatsappInboxController::class, 'destroy'])->whereNumber('id')->name('destroy');
            Route::post('/conversations/{id}/send', [Admin\WhatsappInboxController::class, 'sendMessage'])->whereNumber('id')->name('send');
            Route::post('/conversations/{id}/messages/{messageId}/react', [Admin\WhatsappInboxController::class, 'react'])->whereNumber(['id', 'messageId'])->name('react');
            Route::put('/notification-settings', [Admin\WhatsappInboxController::class, 'updateNotificationSettings'])->name('notifications.update');
            Route::get('/media/{message}', [Admin\WhatsappInboxController::class, 'media'])->whereNumber('message')->name('media');
        });

        // Public AI Customer Support & WhatsApp Cloud API transcripts & configuration.
        Route::middleware('permission:manage support chats')->prefix('support-chats')->name('support-chats.')->group(function () {
            Route::get('/', [Admin\SupportChatController::class, 'index'])->name('index');
            Route::get('/live-pulse', [Admin\SupportChatController::class, 'livePulse'])->name('live-pulse');
            Route::get('/dispatch', [Admin\SupportChatController::class, 'dispatchForm'])->name('dispatch');
            Route::post('/whatsapp/send', [Admin\SupportChatController::class, 'sendWhatsappOutbound'])->name('whatsapp.send');
            Route::get('/whatsapp', [Admin\SupportChatController::class, 'whatsappSettings'])->name('whatsapp');
            Route::put('/whatsapp-settings', [Admin\SupportChatController::class, 'updateWhatsappSettings'])->name('whatsapp.settings.update');
            Route::post('/whatsapp-subscribe', [Admin\SupportChatController::class, 'subscribeWebhook'])->name('whatsapp.subscribe');
            Route::get('/settings', [Admin\SupportChatController::class, 'aiSettings'])->name('settings');
            Route::put('/settings', [Admin\SupportChatController::class, 'updateSettings'])->name('settings.update');
            Route::get('/{id}', [Admin\SupportChatController::class, 'show'])->whereNumber('id')->name('show');
            Route::get('/{id}/live', [Admin\SupportChatController::class, 'live'])->whereNumber('id')->name('live');
            Route::post('/{id}/typing', [Admin\SupportChatController::class, 'typing'])->whereNumber('id')->name('typing');
            Route::post('/{id}/reply', [Admin\SupportChatController::class, 'replyWhatsapp'])->whereNumber('id')->name('reply');
            Route::patch('/{id}/ai-toggle', [Admin\SupportChatController::class, 'toggleAi'])->whereNumber('id')->name('ai.toggle');
            Route::patch('/{id}/status', [Admin\SupportChatController::class, 'updateStatus'])->whereNumber('id')->name('status.update');
            Route::delete('/{id}', [Admin\SupportChatController::class, 'destroy'])->whereNumber('id')->name('destroy');
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
