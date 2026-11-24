<?php

namespace App\Services;

use App\DTOs\ServicePageData;
use App\Models\ServicePage;
use App\Repositories\Contracts\ServicePageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServicePageService
{
    public function __construct(
        private ServicePageRepositoryInterface $pages,
        private SeoService $seo,
    ) {}

    public function all(): Collection
    {
        return $this->pages->all()->where('locale', 'en')->values();
    }

    public function find(int $id): Model
    {
        $page = $this->pages->find($id);
        abort_unless($page->locale === 'en', 404);

        return $page;
    }

    public function create(ServicePageData $data): Model
    {
        $attrs = $data->toArray();
        $attrs['locale'] = 'en';
        $attrs['slug'] = ! empty($attrs['slug'])
            ? Str::slug((string) $attrs['slug'])
            : $this->uniqueSlug((string) $attrs['title']);

        $created = $this->pages->create($attrs);
        $this->seo->flushCaches();

        return $created;
    }

    public function save(int $id, ServicePageData $data): Model
    {
        $this->find($id);
        $attrs = $data->toArray();

        if (empty($attrs['slug'])) {
            unset($attrs['slug']);
        } else {
            $attrs['slug'] = Str::slug((string) $attrs['slug']);
        }

        $updated = $this->pages->update($id, $attrs);
        $this->seo->flushCaches();

        return $updated;
    }

    public function delete(int $id): void
    {
        $this->find($id);
        $this->pages->delete($id);
        $this->seo->flushCaches();
    }

    public function published(string $locale): Collection
    {
        return $this->pages->published($locale);
    }

    public function publicPage(string $slug, string $locale): ServicePage
    {
        return $this->pages->publishedBySlug($slug, $locale);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'service';
        $slug = $base;
        $i = 2;

        while (ServicePage::query()->where('locale', 'en')->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
