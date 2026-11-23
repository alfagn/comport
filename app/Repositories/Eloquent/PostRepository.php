<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Eloquent-backed repository for blog Post records.
 */
class PostRepository extends EloquentRepository implements PostRepositoryInterface
{
    protected string $model = Post::class;

    /** Published posts, newest first, for the blog index. */
    public function published(): Collection
    {
        return $this->query()->published()->get();
    }

    /** A published post by slug for the public article page. */
    public function findPublishedBySlug(string $slug): Post
    {
        return $this->query()->published()->where('slug', $slug)->firstOrFail();
    }

    /** Latest published posts for the home-page journal strip. */
    public function latestPublished(int $limit = 3): Collection
    {
        return $this->query()->published()->limit($limit)->get();
    }
}
