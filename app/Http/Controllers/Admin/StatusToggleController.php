<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\ContentListItem;
use App\Models\HeroSlide;
use App\Models\Industry;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Member;
use App\Models\NavItem;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServicePage;
use App\Services\ContentListItemService;
use App\Services\NavItemService;
use App\Services\SeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Unified one-click status toggle endpoint for all CMS listing tables and cards.
 * Enforces entity-specific Spatie permissions and flushes public caches immediately.
 */
class StatusToggleController extends Controller
{
    /**
     * Map of supported entity keys => [ModelClass, column, permission, onLabel, offLabel, flushTarget].
     */
    private const ENTITIES = [
        'hero-slides' => [HeroSlide::class, 'is_active', 'manage hero slides', 'Live', 'Hidden', null],
        'services' => [Service::class, 'is_active', 'manage services', 'Live', 'Hidden', 'seo'],
        'service-pages' => [ServicePage::class, 'is_published', 'manage services', 'Published', 'Draft', 'seo'],
        'industries' => [Industry::class, 'is_active', 'manage industries', 'Live', 'Hidden', null],
        'projects' => [Project::class, 'is_active', 'manage projects', 'Live', 'Hidden', 'seo'],
        'projects-featured' => [Project::class, 'is_featured', 'manage projects', 'Featured', 'Standard', null],
        'members' => [Member::class, 'is_active', 'manage members', 'Live', 'Hidden', 'seo'],
        'jobs' => [JobPost::class, 'is_active', 'manage jobs', 'Live', 'Hidden', 'seo'],
        'posts' => [Post::class, 'is_published', 'manage posts', 'Published', 'Draft', 'seo'],
        'partners' => [Partner::class, 'is_active', 'manage partners', 'Live', 'Hidden', null],
        'navigation' => [NavItem::class, 'is_active', 'manage navigation', 'Live', 'Hidden', 'nav'],
        'content-lists' => [ContentListItem::class, 'is_active', 'manage content lists', 'Live', 'Hidden', 'lists'],
        'inbox' => [ContactMessage::class, 'read_at', 'manage inbox', 'Read', 'Unread', null],
        'applications' => [JobApplication::class, 'status', 'manage applications', 'Reviewed', 'New', null],
    ];

    public function __invoke(Request $request, string $entity, int $id): JsonResponse
    {
        abort_unless(isset(self::ENTITIES[$entity]), 404);

        [$modelClass, $column, $permission, $labelOn, $labelOff, $flush] = self::ENTITIES[$entity];

        abort_unless($request->user()?->can($permission), 403);

        $record = $modelClass::query()->findOrFail($id);

        if ($record instanceof ServicePage) {
            abort_unless($record->locale === 'en', 404);
        }

        if ($entity === 'inbox') {
            $nextActive = $record->read_at === null;
            $record->forceFill(['read_at' => $nextActive ? now() : null])->save();
        } elseif ($entity === 'applications') {
            $nextActive = $record->status === 'new';
            $record->forceFill(['status' => $nextActive ? 'reviewed' : 'new'])->save();
        } else {
            $nextActive = ! (bool) $record->{$column};
            $updates = [$column => $nextActive];

            if ($record instanceof Post && $nextActive && $record->published_at === null) {
                $updates['published_at'] = now();
            }

            $record->forceFill($updates)->save();
        }

        $this->flushCaches($flush, $record);

        $stateLabel = $nextActive ? $labelOn : $labelOff;

        return response()->json([
            'ok' => true,
            'active' => $nextActive,
            'label' => $stateLabel,
            'message' => "Status updated to {$stateLabel}.",
        ]);
    }

    private function flushCaches(?string $flush, object $record): void
    {
        if ($flush === 'seo') {
            app(SeoService::class)->flushCaches();
        } elseif ($flush === 'nav') {
            foreach (['utility', 'drawer', 'mega-capabilities', 'mega-journal'] as $menu) {
                Cache::forget(NavItemService::CACHE_PREFIX.$menu);
            }
        } elseif ($flush === 'lists') {
            if ($record instanceof ContentListItem && $record->group) {
                Cache::forget(ContentListItemService::CACHE_PREFIX.$record->group);
            }
            foreach (array_keys(ContentListItemService::GROUPS) as $group) {
                Cache::forget(ContentListItemService::CACHE_PREFIX.$group);
            }
        }
    }
}
