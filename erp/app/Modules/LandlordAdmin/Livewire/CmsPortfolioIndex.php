<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Modules\CMS\Models\Category;
use App\Modules\CMS\Models\PortfolioProject;
use Illuminate\Support\Str;

/**
 * Class CmsPortfolioIndex
 *
 * Livewire component managing portfolio projects and case studies on the website.
 */
class CmsPortfolioIndex extends BaseComponent
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $slug = '';

    public string $client_name = '';

    public ?int $category_id = null;

    public string $excerpt = '';

    public string $description = '';

    public string $featured_image = '';

    public string $project_url = '';

    public string $completion_date = '';

    public bool $is_featured = false;

    public bool $is_published = true;

    public string $selectedCategory = 'all';

    public string $newCategoryName = '';

    public function createCategory(): void
    {
        $name = trim($this->newCategoryName);
        if ($name === '') {
            return;
        }

        Category::on('landlord')->updateOrCreate(
            ['slug' => Str::slug($name), 'type' => 'portfolio'],
            ['name' => $name]
        );

        $this->newCategoryName = '';
        $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم إضافة التصنيف بنجاح!' : 'Category added!');
    }

    public function deleteCategory(int $id): void
    {
        Category::on('landlord')->where('id', $id)->delete();
        $this->selectedCategory = 'all';
        $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم حذف التصنيف' : 'Category deleted');
    }

    public function create(): void
    {
        $this->reset(['editingId', 'title', 'slug', 'client_name', 'category_id', 'excerpt', 'description', 'featured_image', 'project_url', 'completion_date', 'is_featured', 'is_published']);
        $this->completion_date = now()->format('Y-m-d');
        $this->is_published = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $project = PortfolioProject::on('landlord')->findOrFail($id);
        $this->editingId = $project->id;
        $this->title = $project->title;
        $this->slug = $project->slug;
        $this->client_name = (string) $project->client_name;
        $this->category_id = $project->category_id;
        $this->excerpt = (string) $project->excerpt;
        $this->description = (string) $project->description;
        $this->featured_image = (string) $project->featured_image;
        $this->project_url = (string) $project->project_url;
        $this->completion_date = $project->completion_date ? $project->completion_date->format('Y-m-d') : '';
        $this->is_featured = (bool) $project->is_featured;
        $this->is_published = (bool) $project->is_published;
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
        ]);

        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->slug ?: $this->title),
            'client_name' => $this->client_name,
            'category_id' => $this->category_id,
            'excerpt' => $this->excerpt,
            'description' => $this->description,
            'featured_image' => $this->featured_image ?: 'assets/img/work/nilepay.jpg',
            'project_url' => $this->project_url,
            'completion_date' => $this->completion_date ?: null,
            'is_featured' => $this->is_featured,
            'is_published' => $this->is_published,
        ];

        if ($this->editingId) {
            PortfolioProject::on('landlord')->where('id', $this->editingId)->update($data);
            $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم تحديث المشروع بنجاح!' : 'Project updated successfully!');
        } else {
            PortfolioProject::on('landlord')->create($data);
            $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم إضافة المشروع لسجل الأعمال!' : 'Project added to portfolio!');
        }

        $this->showModal = false;
    }

    public function togglePublished(int $id): void
    {
        $project = PortfolioProject::on('landlord')->findOrFail($id);
        $project->update(['is_published' => ! $project->is_published]);
        $this->toastSuccess('Status updated');
    }

    public function delete(int $id): void
    {
        PortfolioProject::on('landlord')->where('id', $id)->delete();
        $this->toastSuccess('Project removed');
    }

    public function render()
    {
        $categories = Category::on('landlord')->where('type', 'portfolio')->get();

        $query = PortfolioProject::on('landlord')->with('category')->latest();

        if ($this->selectedCategory !== 'all') {
            $query->where('category_id', $this->selectedCategory);
        }

        $projects = $query->paginate(12);

        return view('admin::livewire.cms-portfolio-index', [
            'projects' => $projects,
            'categories' => $categories,
            'totalProjects' => PortfolioProject::on('landlord')->count(),
            'featuredCount' => PortfolioProject::on('landlord')->where('is_featured', true)->count(),
        ])->layout('layouts.admin', [
            'title' => 'Portfolio & Case Studies — Empetix CMS',
            'header' => app()->getLocale() === 'ar' ? 'سابقة الأعمال ودراسات الحالة' : 'Portfolio & Case Studies Management',
        ]);
    }
}
