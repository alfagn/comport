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
use App\Models\NewsletterSubscriber;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Project;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Services\ContentListItemService;
use App\Services\NavItemService;
use App\Services\SeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Unified bulk deletion endpoint for all CMS DataTables.
 * Supports checkbox multi-selection, enforces permissions, and flushes relevant caches.
 */
class BulkDeleteController extends Controller
{
    /**
     * Map of supported entity keys => [ModelClass, permission, flushTarget].
     */
    private const ENTITIES = [
        'hero-slides' => [HeroSlide::class, 'manage hero slides', null],
        'services' => [Service::class, 'manage services', 'seo'],
        'industries' => [Industry::class, 'manage industries', null],
        'projects' => [Project::class, 'manage projects', 'seo'],
        'members' => [Member::class, 'manage members', 'seo'],
        'jobs' => [JobPost::class, 'manage jobs', 'seo'],
        'posts' => [Post::class, 'manage posts', 'seo'],
        'partners' => [Partner::class, 'manage partners', null],
        'navigation' => [NavItem::class, 'manage navigation', 'nav'],
        'content-lists' => [ContentListItem::class, 'manage content lists', 'lists'],
        'inbox' => [ContactMessage::class, 'manage inbox', null],
        'applications' => [JobApplication::class, 'manage applications', null],
        'subscribers' => [NewsletterSubscriber::class, 'manage inbox', null],
        'admins' => [User::class, 'manage admin users', null],
        'roles' => [Role::class, 'manage roles', null],
    ];

    public function __invoke(Request $request, string $entity): JsonResponse
    {
        abort_unless(isset(self::ENTITIES[$entity]), 404);

        [$modelClass, $permission, $flush] = self::ENTITIES[$entity];

        abort_unless($request->user()?->can($permission), 403);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer'],
        ]);

        $ids = array_map('intval', $validated['ids']);

        // Safety rules for sensitive tables:
        if ($entity === 'members') {
            $currentUserId = (int) $request->user()->id;
            $currentUserEmail = $request->user()->email;
            $ids = array_values(array_filter($ids, function ($id) use ($currentUserId, $currentUserEmail) {
                $m = Member::find($id);
                return $m && $m->user_id !== $currentUserId && $m->email !== $currentUserEmail;
            }));
            abort_if(empty($ids), 422, 'You cannot delete your own team profile.');
        }

        if ($entity === 'admins') {
            // Cannot delete yourself or peer owners
            $currentUserId = (int) $request->user()->id;
            $isOwner = $request->user()->hasRole(\App\Support\Permissions::OWNER_ROLE);
            $ids = array_values(array_filter($ids, function ($id) use ($currentUserId, $isOwner) {
                if ($id === $currentUserId) return false;
                if ($isOwner) {
                    $target = User::find($id);
                    if ($target && $target->hasRole(\App\Support\Permissions::OWNER_ROLE)) return false;
                }
                return true;
            }));
            abort_if(empty($ids), 422, 'You cannot delete your own account or peer Owner accounts.');
        }

        if ($entity === 'roles') {
            // Protect primary Super Admin role (id: 1)
            $ids = array_values(array_filter($ids, fn ($id) => $id !== 1));
            abort_if(empty($ids), 422, 'The Super Admin role cannot be deleted.');
        }

        $records = $modelClass::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($records as $record) {
            $record->delete();
            $count++;
        }

        $this->flushCaches($flush);

        $label = $count === 1 ? 'record' : 'records';

        return response()->json([
            'ok' => true,
            'count' => $count,
            'message' => "Successfully deleted {$count} {$label}.",
        ]);
    }

    private function flushCaches(?string $target): void
    {
        if ($target === 'seo') {
            app(SeoService::class)->forgetPublicCaches();
        } elseif ($target === 'nav') {
            app(NavItemService::class)->forgetNavigationCaches();
        } elseif ($target === 'lists') {
            app(ContentListItemService::class)->forgetListItemCaches();
        }

        Cache::forget('footer_links');
    }
}
