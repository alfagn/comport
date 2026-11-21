<?php

declare(strict_types=1);

namespace App\Modules\Billing\Services;

use App\Core\Exceptions\BusinessLogicException;
use App\Core\Services\BaseService;
use App\Modules\Billing\Models\Plan;
use App\Modules\Billing\Models\Subscription;
use App\Modules\Billing\Models\SubscriptionInvoice;
use App\Modules\Tenancy\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class SubscriptionService
 *
 * Use cases: subscribe, change plan, record payment, approve manual payment, check expiry.
 */
class SubscriptionService extends BaseService
{
    /**
     * Subscribe or change tenant plan.
     *
     * @throws Throwable
     */
    public function subscribe(Tenant $tenant, Plan $plan, string $interval = 'monthly'): Subscription
    {
        return $this->transaction(function () use ($tenant, $plan, $interval) {
            $durationMonths = $interval === 'yearly' ? 12 : 1;
            $amountMinor = $interval === 'yearly' ? $plan->price_yearly_minor : $plan->price_monthly_minor;

            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => 'pending',
                'billing_interval' => $interval,
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addMonths($durationMonths),
            ]);

            // Create initial pending invoice
            SubscriptionInvoice::create([
                'tenant_id' => $tenant->id,
                'subscription_id' => $subscription->id,
                'invoice_number' => 'INV-SUB-'.strtoupper(Str::random(8)),
                'amount_minor' => $amountMinor,
                'currency' => $plan->currency,
                'status' => 'pending',
                'payment_method' => 'manual_bank_transfer',
            ]);

            $this->logInfo('Created subscription and pending invoice', [
                'tenant_id' => $tenant->id,
                'plan' => $plan->name,
                'amount_minor' => $amountMinor,
            ]);

            return $subscription;
        }, connection: 'landlord');
    }

    /**
     * Super Admin approves manual payment receipt.
     *
     * @throws Throwable
     */
    public function approveInvoicePayment(int $invoiceId): SubscriptionInvoice
    {
        return $this->transaction(function () use ($invoiceId) {
            /** @var SubscriptionInvoice $invoice */
            $invoice = SubscriptionInvoice::on('landlord')->with(['subscription', 'tenant'])->findOrFail($invoiceId);

            if ($invoice->status === 'paid') {
                throw new BusinessLogicException('Invoice is already marked as paid.');
            }

            $invoice->update([
                'status' => 'paid',
                'paid_at' => Carbon::now(),
            ]);

            // Activate subscription
            if ($invoice->subscription !== null) {
                $durationMonths = $invoice->subscription->billing_interval === 'yearly' ? 12 : 1;

                $invoice->subscription->update([
                    'status' => 'active',
                    'starts_at' => Carbon::now(),
                    'ends_at' => Carbon::now()->addMonths($durationMonths),
                ]);
            }

            // Ensure tenant is active
            if ($invoice->tenant !== null && ! $invoice->tenant->is_active) {
                $invoice->tenant->update(['is_active' => true]);
            }

            $this->logInfo('Invoice approved and subscription activated', [
                'invoice_id' => $invoice->id,
                'tenant_id' => $invoice->tenant_id,
            ]);

            return $invoice;
        }, connection: 'landlord');
    }

    /**
     * Super Admin rejects manual payment receipt.
     */
    public function rejectInvoicePayment(int $invoiceId, ?string $reason = null): SubscriptionInvoice
    {
        /** @var SubscriptionInvoice $invoice */
        $invoice = SubscriptionInvoice::on('landlord')->findOrFail($invoiceId);

        $invoice->update([
            'status' => 'rejected',
            'notes' => $reason ?? 'Payment receipt rejected.',
        ]);

        $this->logInfo('Invoice rejected', ['invoice_id' => $invoiceId]);

        return $invoice;
    }
}
