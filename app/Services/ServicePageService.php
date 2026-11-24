<?php

namespace App\Services;

use App\DTOs\ServicePageData;
use App\Models\ServicePage;
use App\Repositories\Contracts\ServicePageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ServicePageService
{
    public function __construct(private ServicePageRepositoryInterface $pages) {}

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

    public function save(int $id, ServicePageData $data): Model
    {
        $this->find($id);

        return $this->pages->update($id, $data->toArray());
    }

    public function published(string $locale): Collection
    {
        return $this->pages->published($locale);
    }

    public function publicPage(string $slug, string $locale): ServicePage
    {
        return $this->pages->publishedBySlug($slug, $locale);
    }
}
