<?php

declare(strict_types=1);

namespace App\Modules\CMS\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PortfolioProject
 *
 * Case studies and portfolio showcase items for the public company site.
 * Stored on landlord connection.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $client_name
 * @property int|null $category_id
 * @property string|null $excerpt
 * @property string $description
 * @property string|null $featured_image
 * @property array<int, string>|null $gallery_images
 * @property string|null $project_url
 * @property Carbon|null $completion_date
 * @property bool $is_featured
 * @property bool $is_published
 */
class PortfolioProject extends Model
{
    protected $connection = 'landlord';

    protected $table = 'portfolio_projects';

    protected $fillable = [
        'title',
        'slug',
        'client_name',
        'category_id',
        'excerpt',
        'description',
        'featured_image',
        'gallery_images',
        'project_url',
        'completion_date',
        'is_featured',
        'is_published',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'completion_date' => 'date',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
