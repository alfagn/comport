<?php

namespace App\Repositories\Contracts;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Persistence contract for the blog Post entity.
 */
interface PostRepositoryInterface extends RepositoryInterface
{
    /** Published posts, newest first, for the blog index. */
    public function published(): Collection;

    /** A published post by slug for the public article page. */
    public function findPublishedBySlug(string $slug): \App\Models\Post;

    /** Latest published posts for the home-page journal strip. */
    public function latestPublished(int $limit = 3): Collection;
}
