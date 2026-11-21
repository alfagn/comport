<?php

declare(strict_types=1);

namespace App\Modules\Tenancy\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Modules\Billing\Models\Plan;
use App\Modules\Billing\Models\Subscription;
use App\Modules\Billing\Services\SubscriptionService;
use App\Modules\Tenancy\Models\Tenant;

/**
 * Class TenantSubscription
 *
 * Livewire component displaying tenant subscription status, active plan, quotas, and renewal.
 */
class TenantSubscription extends BaseComponent
{
    public function upgradeToPlan(int $planId, SubscriptionService $subscriptionService): void
    {
        $tenant = Tenant::current();
        if ($tenant === null) {
            return;
        }

        $plan = Plan::on('landlord')->findOrFail($planId);
        $subscriptionService->subscribe($tenant, $plan, 'monthly');

        $this->toastSuccess("Subscribed to {$plan->name}. Pending manual payment invoice created.");
    }

    public function render()
    {
        $tenant = Tenant::current();
        /** @var Subscription|null $subscription */
        $subscription = $tenant?->currentSubscription;
        $plan = $subscription?->plan;
        $allPlans = Plan::on('landlord')->where('is_active', true)->orderBy('sort_order')->get();

        return view('tenancy::livewire.tenant-subscription', [
            'tenant' => $tenant,
            'subscription' => $subscription,
            'plan' => $plan,
            'allPlans' => $allPlans,
        ])->layout('layouts.tenant', [
            'title' => 'Subscription & Plan',
            'header' => app()->getLocale() === 'ar' ? 'خطة الاشتراك والترقية' : 'Subscription & Quota Limits',
        ]);
    }
}
