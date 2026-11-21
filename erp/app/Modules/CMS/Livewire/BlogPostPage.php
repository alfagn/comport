<?php

declare(strict_types=1);

namespace App\Modules\CMS\Livewire;

use App\Modules\CMS\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.site')]
class BlogPostPage extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render(): View
    {
        /** @var Post $post */
        $post = Post::query()
            ->where('slug', $this->slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Increment view count
        $post->increment('views_count');

        $relatedPosts = Post::query()
            ->where('id', '!=', $post->id)
            ->where('is_published', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('modules.cms.livewire.blog-post-page', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
