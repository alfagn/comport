<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Livewire;

use App\Core\Livewire\BaseTable;
use App\Modules\Tenancy\Models\Tenant;
use App\Modules\Tenancy\Services\TenantService;

/**
 * Class TenantsIndex
 *
 * Livewire component managing all tenant workspaces, custom domains, and activation states.
 */
class TenantsIndex extends BaseTable
{
    public string $statusFilter = '';

    public function toggleStatus(int $tenantId, TenantService $tenantService): void
    {
        $tenant = $tenantService->toggleActiveStatus($tenantId);

        $this->toastSuccess("Tenant '{$tenant->name}' status updated.");
    }

    public function verifyDomain(int $domainId, TenantService $tenantService): void
    {
        $tenantService->verifyCustomDomain($domainId);

        $this->toastSuccess('Custom domain successfully verified!');
    }

    public function render()
    {
        $query = Tenant::on('landlord')
            ->with(['domains', 'currentSubscription.plan'])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter === 'active', fn ($q) => $q->where('is_active', true))
            ->when($this->statusFilter === 'suspended', fn ($q) => $q->where('is_active', false))
            ->orderBy($this->sortField, $this->sortDirection);

        $tenants = $query->paginate($this->perPage);

        return view('admin::livewire.tenants-index', [
            'tenants' => $tenants,
        ])->layout('layouts.admin', [
            'title' => 'Tenants Management — Empetix',
            'header' => app()->getLocale() === 'ar' ? 'إدارة الشركات ومساحات العمل' : 'Company Workspaces & Tenants',
        ]);
    }
}
