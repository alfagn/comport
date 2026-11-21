<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Modules\CMS\Models\Partner;

/**
 * Class CmsPartnersIndex
 *
 * Livewire component managing client & partner logos on the website.
 */
class CmsPartnersIndex extends BaseComponent
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $logo = '';

    public string $website_url = '';

    public int $sort_order = 0;

    public bool $is_active = true;

    public function create(): void
    {
        $this->reset(['editingId', 'name', 'logo', 'website_url', 'sort_order', 'is_active']);
        $this->sort_order = Partner::on('landlord')->count() + 1;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $partner = Partner::on('landlord')->findOrFail($id);
        $this->editingId = $partner->id;
        $this->name = $partner->name;
        $this->logo = (string) $partner->logo;
        $this->website_url = (string) $partner->website_url;
        $this->sort_order = (int) $partner->sort_order;
        $this->is_active = (bool) $partner->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = [
            'name' => $this->name,
            'logo' => $this->logo ?: 'assets/img/partners/partner-logo.svg',
            'website_url' => $this->website_url,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            Partner::on('landlord')->where('id', $this->editingId)->update($data);
            $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم تحديث بيانات الشريك' : 'Partner updated');
        } else {
            Partner::on('landlord')->create($data);
            $this->toastSuccess(app()->getLocale() === 'ar' ? 'تم إضافة الشريك بنجاح' : 'Partner added');
        }

        $this->showModal = false;
    }

    public function toggleActive(int $id): void
    {
        $partner = Partner::on('landlord')->findOrFail($id);
        $partner->update(['is_active' => ! $partner->is_active]);
        $this->toastSuccess('Status updated');
    }

    public function delete(int $id): void
    {
        Partner::on('landlord')->where('id', $id)->delete();
        $this->toastSuccess('Partner removed');
    }

    public function render()
    {
        $partners = Partner::on('landlord')->orderBy('sort_order')->latest()->get();

        return view('admin::livewire.cms-partners-index', [
            'partners' => $partners,
            'totalPartners' => Partner::on('landlord')->count(),
            'activeCount' => Partner::on('landlord')->where('is_active', true)->count(),
        ])->layout('layouts.admin', [
            'title' => 'Partners & Client Brands — Empetix CMS',
            'header' => app()->getLocale() === 'ar' ? 'شركاء النجاح والعملاء' : 'Partners & Client Brands Management',
        ]);
    }
}
