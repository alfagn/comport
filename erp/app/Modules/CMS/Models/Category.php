<?php

declare(strict_types=1);

namespace App\Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Category
 *
 * Taxonomies for blog articles, portfolio projects, and jobs.
 */
class Category extends Model
{
    protected $connection = 'landlord';

    protected $table = 'cms_categories';

    protected $fillable = [
        'name',
        'slug',
        'type', // blog, portfolio, job
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'category_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(PortfolioProject::class, 'category_id');
    }
}
