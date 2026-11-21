<?php

declare(strict_types=1);

namespace App\Modules\CMS\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Post
 *
 * Blog posts, photo galleries, and video articles for the public site.
 * Stored on landlord connection.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $type article|photo|video
 * @property int|null $category_id
 * @property string|null $excerpt
 * @property string $body
 * @property string|null $featured_image
 * @property string|null $video_url
 * @property array<int, string>|null $gallery_images
 * @property bool $is_published
 * @property int $views_count
 * @property Carbon|null $published_at
 */
class Post extends Model
{
    protected $connection = 'landlord';

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'type',
        'category_id',
        'excerpt',
        'body',
        'featured_image',
        'video_url',
        'gallery_images',
        'is_published',
        'views_count',
        'published_at',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'is_published' => 'boolean',
        'views_count' => 'integer',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
