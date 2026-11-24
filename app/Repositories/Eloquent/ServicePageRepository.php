<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\ServicePage;
use App\Repositories\Contracts\ServicePageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ServicePageRepository extends EloquentRepository implements ServicePageRepositoryInterface
{
    protected string $model = ServicePage::class;

    public function published(string $locale): Collection
    {
        return $this->query()->where('locale', $locale)->where('is_published', true)->orderBy('title')->get();
    }

    public function publishedBySlug(string $slug, string $locale): ServicePage
    {
        return $this->query()->where('slug', $slug)->where('locale', $locale)->where('is_published', true)->firstOrFail();
    }
}
