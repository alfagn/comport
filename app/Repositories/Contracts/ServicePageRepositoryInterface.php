<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;
use App\Models\ServicePage;
use Illuminate\Database\Eloquent\Collection;

interface ServicePageRepositoryInterface extends RepositoryInterface
{
    public function published(string $locale): Collection;

    public function publishedBySlug(string $slug, string $locale): ServicePage;
}
