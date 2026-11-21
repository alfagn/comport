<?php

declare(strict_types=1);

namespace App\Modules\TenantCore\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Modules\TenantCore\Models\CompanySetting;

/**
 * Class TenantSettings
 *
 * Tenant ERP Company profile and localization settings.
 */
class TenantSettings extends BaseComponent
{
    public string $companyName = '';

    public string $taxNumber = '';

    public string $currency = 'EGP';

    public function mount(): void
    {
        $this->companyName = (string) CompanySetting::get('company_name', 'My Company');
        $this->taxNumber = (string) CompanySetting::get('tax_number', '');
        $this->currency = (string) CompanySetting::get('currency', 'EGP');
    }

    public function save(): void
    {
        CompanySetting::set('company_name', $this->companyName);
        CompanySetting::set('tax_number', $this->taxNumber);
        CompanySetting::set('currency', $this->currency);

        $this->toastSuccess('Company settings updated successfully!');
    }

    public function render()
    {
        return view('tenant::livewire.tenant-settings')->layout('layouts.tenant', [
            'title' => 'Company Settings',
            'header' => app()->getLocale() === 'ar' ? 'إعدادات الشركة والملف التجاري' : 'Company Profile & Invoice Settings',
        ]);
    }
}
