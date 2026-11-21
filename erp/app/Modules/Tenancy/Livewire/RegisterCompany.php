<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Modules\Billing\Models\Plan;
use App\Modules\Tenancy\DTOs\ProvisionTenantData;
use App\Modules\Tenancy\Services\TenantService;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class RegisterCompany
 *
 * Livewire component allowing new companies to register and instantly provision their isolated SaaS workspace.
 */
class RegisterCompany extends BaseComponent
{
    public string $companyName = '';

    public string $slug = '';

    public string $ownerName = '';

    public string $ownerEmail = '';

    public string $ownerPassword = '';

    public ?string $phone = null;

    public ?int $selectedPlanId = null;

    protected $rules = [
        'companyName' => 'required|string|min:2|max:100',
        'slug' => 'required|string|min:2|max:50|regex:/^[a-z0-9\-]+$/|unique:landlord.tenants,slug',
        'ownerName' => 'required|string|min:2|max:100',
        'ownerEmail' => 'required|email|max:150',
        'ownerPassword' => 'required|string|min:8',
        'phone' => 'nullable|string|max:30',
        'selectedPlanId' => 'required|exists:landlord.plans,id',
    ];

    public function updatedCompanyName(): void
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->companyName);
        }
    }

    public function selectPlan(int $planId): void
    {
        $this->selectedPlanId = $planId;
    }

    public function register(TenantService $tenantService): void
    {
        $this->validate();

        try {
            $data = new ProvisionTenantData(
                companyName: $this->companyName,
                slug: $this->slug,
                ownerName: $this->ownerName,
                ownerEmail: $this->ownerEmail,
                ownerPassword: $this->ownerPassword,
                phone: $this->phone,
                planId: $this->selectedPlanId,
                trialDays: 14
            );

            $tenant = $tenantService->provision($data);

            $this->toastSuccess('Company workspace provisioned successfully!');

            $centralDomain = (string) config('app.central_domain', 'empetix.test');
            $workspaceUrl = "http://{$tenant->slug}.{$centralDomain}:8000";

            $this->redirect($workspaceUrl);
        } catch (Throwable $e) {
            $this->toastError('Failed to provision company workspace: '.$e->getMessage());
        }
    }

    public function render()
    {
        $plans = Plan::on('landlord')->where('is_active', true)->orderBy('sort_order')->get();

        if ($this->selectedPlanId === null && $plans->isNotEmpty()) {
            $this->selectedPlanId = $plans->first()->id;
        }

        return view('tenancy::livewire.register-company', [
            'plans' => $plans,
        ]);
    }
}
