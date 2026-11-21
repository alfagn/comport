<?php

declare(strict_types=1);

use App\Modules\Billing\Models\Plan;
use App\Modules\Billing\Models\Subscription;
use App\Modules\Billing\Models\SubscriptionInvoice;
use App\Modules\Billing\Services\FeatureGate;
use App\Modules\Billing\Services\SubscriptionService;
use App\Modules\Tenancy\Models\Tenant;

it('enforces feature gating based on tenant subscription plan', function () {
    $planWithHrOnly = Plan::on('landlord')->create([
        'name' => 'HR Only Plan',
        'slug' => 'hr-only-'.time(),
        'price_monthly_minor' => 100000,
        'currency' => 'EGP',
        'features' => ['hr'],
        'limits' => ['max_employees' => 10],
    ]);

    $tenant = Tenant::on('landlord')->create([
        'name' => 'Feature Test Corp',
        'slug' => 'feature-corp-'.time(),
        'email' => 'feature@corp.test',
        'database_name' => 'tenant_feature_'.time(),
        'is_active' => true,
        'is_internal' => false,
    ]);

    Subscription::on('landlord')->create([
        'tenant_id' => $tenant->id,
        'plan_id' => $planWithHrOnly->id,
        'status' => 'active',
        'starts_at' => now(),
        'ends_at' => now()->addMonth(),
    ]);

    $tenant->makeCurrent();

    // Allowed feature
    expect(FeatureGate::allows('hr'))->toBeTrue();

    // Disallowed feature
    expect(FeatureGate::allows('marketing'))->toBeFalse();
    expect(FeatureGate::allows('projects'))->toBeFalse();

    // Resource quota limits
    expect(FeatureGate::canCreate('max_employees', 5))->toBeTrue();
    expect(FeatureGate::canCreate('max_employees', 10))->toBeFalse(); // 10 reached limit

    $tenant->forgetCurrent();
});

it('approves manual invoice payment and activates tenant subscription', function () {
    $service = app(SubscriptionService::class);

    $plan = Plan::on('landlord')->first();
    $tenant = Tenant::on('landlord')->first();

    if ($plan !== null && $tenant !== null) {
        $subscription = $service->subscribe($tenant, $plan, 'monthly');
        $invoice = SubscriptionInvoice::on('landlord')
            ->where('subscription_id', $subscription->id)
            ->firstOrFail();

        expect($invoice->status)->toBe('pending');

        $approvedInvoice = $service->approveInvoicePayment($invoice->id);

        expect($approvedInvoice->status)->toBe('paid');
        expect($approvedInvoice->subscription->status)->toBe('active');
    }
});
