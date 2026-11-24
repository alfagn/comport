<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

/**
 * Business logic for site settings: grouped key/value pairs edited
 * from the dashboard Site Settings screen.
 */
class SettingService extends BaseService
{
    /** Curated groups rendered as standard text-input cards on /admin/settings. */
    private const VISIBLE_GROUPS = ['general', 'contact', 'social'];

    public function __construct(SettingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Curated site settings grouped for the edit form.
     * Excludes seo_* (managed in /admin/seo), brand_* (uploaded via Brand Assets card),
     * sitemap_* (internal timestamps), and cookie_* (rendered in dedicated Cookie Consent card).
     */
    public function grouped(): Collection
    {
        $order = array_flip(self::VISIBLE_GROUPS);

        return $this->repository->all()
            ->filter(fn (Setting $s) => in_array($s->group, self::VISIBLE_GROUPS, true)
                && ! str_starts_with($s->key, 'seo_')
                && ! str_starts_with($s->key, 'brand_')
                && ! str_starts_with($s->key, 'cookie_')
                && ! str_starts_with($s->key, 'sitemap_'))
            ->groupBy('group')
            ->sortBy(fn ($items, $group) => $order[$group] ?? 99);
    }

    /**
     * Persist a settings form submission: every submitted key is upserted;
     * keys absent from the submission are left untouched.
     */
    public function saveGroup(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::setValue($key, $value);
        }
    }

    /**
     * Persist uploaded brand assets (favicon, logos) to the public disk and
     * point the matching settings at them. Absent files keep the current asset.
     */
    public function saveBrandAssets(array $files): void
    {
        foreach (['brand_favicon', 'brand_logo', 'brand_logo_dark'] as $key) {
            $file = $files[$key] ?? null;
            if ($file instanceof UploadedFile) {
                $path = $file->store('uploads/brand', 'public');
                Setting::setValue($key, $path, 'brand');
            }
        }
    }
}
