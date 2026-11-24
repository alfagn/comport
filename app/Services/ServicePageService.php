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
        return $this->pages->all();
    }

    public function find(int $id): Model
    {
        return $this->pages->find($id);
    }

    public function save(int $id, ServicePageData $data): Model
    {
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
