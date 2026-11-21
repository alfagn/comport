<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Modules\CMS\Models\Testimonial;

/**
 * Class CmsTestimonialsIndex
 *
 * Livewire component managing client reviews & testimonials on the website.
 */
class CmsTestimonialsIndex extends BaseComponent
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $client_name = '';

    public string $client_position = '';

    public string $client_company = '';

    public string $content = '';

    public string $avatar = '';

    public int $rating = 5;

    public bool $is_published = true;

    public function create(): void
    {
        $this->reset(['editingId', 'client_name', 'client_position', 'client_company', 'content', 'avatar', 'rating', 'is_published']);
        $this->rating = 5;
        $this->is_published = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $item = Testimonial::on('landlord')->findOrFail($id);
        $this->editingId = $item->id;
        $this->client_name = $item->client_name;
        $this->client_position = (string) $item->client_position;
        $this->client_company = (string) $item->client_company;
        $this->content = $item->content;
        $this->avatar = (string) $item->avatar;
        $this->rating = (int) $item->rating;
        $this->is_published = (bool) $item->is_published;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'client_name' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $data = [
            'client_name' => $this->client_name,
            'client_position' => $this->client_position,
            'client_company' => $this->client_company,
            'content' => $this->content,
            'avatar' => $this->avatar ?: 'assets/img/team/farida-zaki.jpg',
            'rating' => $this->rating,
            'is_published' => $this->is_published,
        ];

        if ($this->editingId) {
            Testimonial::on('landlord')->where('id', $this->editingId)->update($data);
            $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم تحديث التقييم' : 'Review updated');
        } else {
            Testimonial::on('landlord')->create($data);
            $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم إضافة التقييم بنجاح' : 'Review added');
        }

        $this->showModal = false;
    }

    public function togglePublished(int $id): void
    {
        $t = Testimonial::on('landlord')->findOrFail($id);
        $t->update(['is_published' => ! $t->is_published]);
        $this->toastSuccess('Status updated');
    }

    public function delete(int $id): void
    {
        Testimonial::on('landlord')->where('id', $id)->delete();
        $this->toastSuccess('Testimonial removed');
    }

    public function render()
    {
        $testimonials = Testimonial::on('landlord')->latest()->get();

        return view('admin::livewire.cms-testimonials-index', [
            'testimonials' => $testimonials,
            'totalCount' => Testimonial::on('landlord')->count(),
            'publishedCount' => Testimonial::on('landlord')->where('is_published', true)->count(),
        ])->layout('layouts.admin', [
            'title' => 'Client Testimonials & Reviews — Empetix CMS',
            'header' => app()->getLocale() === 'ar' ? 'آراء وتقييمات العملاء' : 'Client Testimonials & Reviews Management',
        ]);
    }
}
