<?php

declare(strict_types=1);

namespace App\Modules\CMS\Livewire;

use App\Modules\CMS\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.site')]
class BlogPage extends Component
{
    #[Url]
    public string $type = 'all'; // all, article, photo, video

    #[Url]
    public string $q = '';

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function render(): View
    {
        $query = Post::query()->where('is_published', true);

        if ($this->type !== 'all') {
            $query->where('type', $this->type);
        }

        if (trim($this->q) !== '') {
            $term = '%'.trim($this->q).'%';
            $query->where(function ($sub) use ($term) {
                $sub->where('title', 'like', $term)
                    ->orWhere('excerpt', 'like', $term)
                    ->orWhere('body', 'like', $term);
            });
        }

        $posts = $query->latest('published_at')->get();

        return view('modules.cms.livewire.blog-page', [
            'posts' => $posts,
        ]);
    }
}
