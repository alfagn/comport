<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Livewire;

use App\Core\Livewire\BaseTable;
use App\Modules\CMS\Models\Category;
use App\Modules\CMS\Models\Post;
use Illuminate\Support\Str;

/**
 * Class CmsPostsIndex
 *
 * CMS Blog Articles & Media manager for the company public website.
 */
class CmsPostsIndex extends BaseTable
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $slug = '';

    public string $type = 'article'; // article, photo, video

    public ?int $category_id = null;

    public string $excerpt = '';

    public string $body = '';

    public string $featured_image = '';

    public string $video_url = '';

    public bool $is_published = true;

    public string $typeFilter = 'all';

    public string $newCategoryName = '';

    public function createCategory(): void
    {
        $name = trim($this->newCategoryName);
        if ($name === '') {
            return;
        }

        Category::on('landlord')->updateOrCreate(
            ['slug' => Str::slug($name), 'type' => 'blog'],
            ['name' => $name]
        );

        $this->newCategoryName = '';
        $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم إضافة تصنيف المدونة!' : 'Blog category added!');
    }

    public function create(): void
    {
        $this->reset(['editingId', 'title', 'slug', 'type', 'category_id', 'excerpt', 'body', 'featured_image', 'video_url', 'is_published']);
        $this->is_published = true;
        $this->type = 'article';
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $post = Post::on('landlord')->findOrFail($id);
        $this->editingId = $post->id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->type = $post->type;
        $this->category_id = $post->category_id;
        $this->excerpt = (string) $post->excerpt;
        $this->body = (string) $post->body;
        $this->featured_image = (string) $post->featured_image;
        $this->video_url = (string) $post->video_url;
        $this->is_published = (bool) $post->is_published;
        $this->showModal = true;
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->slug ?: $this->title),
            'type' => $this->type,
            'category_id' => $this->category_id,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'featured_image' => $this->featured_image ?: 'assets/img/blog/article-cover.jpg',
            'video_url' => $this->video_url,
            'is_published' => $this->is_published,
            'published_at' => $this->is_published ? now() : null,
        ];

        if ($this->editingId) {
            Post::on('landlord')->where('id', $this->editingId)->update($data);
            $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم تحديث المقال بنجاح!' : 'Article updated successfully!');
        } else {
            Post::on('landlord')->create($data);
            $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم نشر المقال بنجاح!' : 'Article published successfully!');
        }

        $this->showModal = false;
    }

    public function togglePublish(int $postId): void
    {
        $post = Post::on('landlord')->findOrFail($postId);
        $post->update(['is_published' => ! $post->is_published]);

        $this->toastSuccess('Post status updated.');
    }

    public function delete(int $id): void
    {
        Post::on('landlord')->where('id', $id)->delete();
        $this->toastSuccess('Article deleted.');
    }

    public function render()
    {
        $categories = Category::on('landlord')->where('type', 'blog')->get();

        $query = Post::on('landlord')
            ->with('category')
            ->when($this->search !== '', fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->typeFilter !== 'all', fn ($q) => $q->where('type', $this->typeFilter))
            ->orderBy('id', 'desc');

        $posts = $query->paginate($this->perPage);

        return view('admin::livewire.cms-posts-index', [
            'posts' => $posts,
            'categories' => $categories,
            'totalPosts' => Post::on('landlord')->count(),
            'publishedCount' => Post::on('landlord')->where('is_published', true)->count(),
        ])->layout('layouts.admin', [
            'title' => 'Journal & Articles CMS — Empetix',
            'header' => app()->getLocale() === 'ar' ? 'المدونة والمقالات والوسائط' : 'Journal, Articles & Media CMS',
        ]);
    }
}
