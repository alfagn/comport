<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageSectionRequest;
use App\Models\ContentListItem;
use App\Models\PageSection;
use App\Services\ContentListItemService;
use App\Services\SectionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Dashboard editor for page content sections, private-storage videos,
 * and inline page-scoped card collections.
 */
class PageSectionController extends Controller
{
    /** Inline editable card groups surfaced directly inside a page's editor. */
    private const PAGE_CARD_GROUPS = [
        'about' => [
            'about.principles' => [
                'title' => 'Operating Principles Cards ("How we work when the stakes are high.")',
                'subtitle' => 'Displayed as architectural cards on the About page. Edit, add, or remove cards below.',
                'has_meta' => false,
                'add_label' => 'Add Principle Card',
            ],
            'about.timeline' => [
                'title' => 'Studio Journey & Milestones (Started 2025 — Engineering & Marketing)',
                'subtitle' => 'Timeline milestones highlighting Empetix\'s software engineering and growth marketing journey.',
                'has_meta' => true,
                'meta_label' => 'Year / Stage',
                'meta_placeholder' => '2025',
                'add_label' => 'Add Milestone Card',
            ],
            'about.pillars' => [
                'title' => '"Who We Are" Core Studio Pillars',
                'subtitle' => 'Three core capability pillars shown directly beneath the overview video.',
                'has_meta' => false,
                'add_label' => 'Add Pillar Card',
            ],
        ],
    ];

    public function __construct(private SectionService $sections) {}

    /** List pages with their editable sections. */
    public function index(): View
    {
        return view('admin.pages.index', ['pages' => $this->sections->groupedByPage()]);
    }

    /** Edit one page's sections and any attached card groups. */
    public function edit(string $page): View
    {
        $cardGroups = [];
        foreach (self::PAGE_CARD_GROUPS[$page] ?? [] as $groupKey => $meta) {
            $cardGroups[$groupKey] = $meta + [
                'items' => ContentListItem::query()
                    ->where('group', $groupKey)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get(),
            ];
        }

        return view('admin.pages.edit', [
            'page' => $page,
            'sections' => $this->sections->forPage($page),
            'cardGroups' => $cardGroups,
        ]);
    }

    /**
     * Persist one page's sections, public images, private-storage videos,
     * and any inline card lists submitted from the page editor.
     */
    public function update(PageSectionRequest $request, string $page): RedirectResponse
    {
        $values = $request->validated('sections');

        foreach ($request->file('images', []) as $key => $file) {
            if ($file) {
                $values[$key] = $file->store('uploads/pages', 'public');
            }
        }

        foreach ($request->file('videos', []) as $key => $file) {
            if ($file) {
                $previous = PageSection::query()
                    ->where('page', $page)
                    ->where('key', $key)
                    ->value('value');

                $storedPath = $file->store('videos/pages', 'local');
                $values[$key] = 'private:'.$storedPath;

                if (is_string($previous) && str_starts_with($previous, 'private:videos/')) {
                    $oldDiskPath = substr($previous, strlen('private:'));
                    if ($oldDiskPath !== $storedPath) {
                        Storage::disk('local')->delete($oldDiskPath);
                    }
                }
            }
        }

        // If user switched from a private video to a direct HTTPS/asset link, clean up the old private file.
        foreach ($values as $key => $newVal) {
            if (! $request->hasFile("videos.{$key}")) {
                $previous = PageSection::query()
                    ->where('page', $page)
                    ->where('key', $key)
                    ->value('value');

                if (
                    is_string($previous)
                    && str_starts_with($previous, 'private:videos/')
                    && $newVal !== $previous
                ) {
                    Storage::disk('local')->delete(substr($previous, strlen('private:')));
                }
            }
        }

        $this->sections->savePage($page, $values);

        if ($request->has('lists') && isset(self::PAGE_CARD_GROUPS[$page])) {
            $allowedGroups = array_keys(self::PAGE_CARD_GROUPS[$page]);
            $submittedLists = (array) $request->validated('lists', []);

            foreach ($allowedGroups as $groupKey) {
                if (! array_key_exists($groupKey, $submittedLists)) {
                    continue;
                }

                $rows = collect((array) $submittedLists[$groupKey])
                    ->map(fn ($row) => [
                        'title' => trim((string) ($row['title'] ?? '')),
                        'body' => trim((string) ($row['body'] ?? '')),
                        'meta' => isset($row['meta']) && trim((string) $row['meta']) !== '' ? trim((string) $row['meta']) : null,
                    ])
                    ->filter(fn ($row) => $row['title'] !== '' || $row['body'] !== '')
                    ->values();

                ContentListItem::query()->where('group', $groupKey)->delete();

                foreach ($rows as $index => $row) {
                    ContentListItem::query()->create([
                        'group' => $groupKey,
                        'title' => $row['title'] !== '' ? $row['title'] : 'Untitled Card',
                        'body' => $row['body'],
                        'meta' => $row['meta'],
                        'sort_order' => $index,
                        'is_active' => true,
                    ]);
                }

                Cache::forget(ContentListItemService::CACHE_PREFIX.$groupKey);
            }
        }

        return back()->with('success', 'Page content saved.');
    }
}
