<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Livewire;

use App\Core\Livewire\BaseTable;
use App\Modules\Billing\Models\SubscriptionInvoice;
use App\Modules\Billing\Services\SubscriptionService;

/**
 * Class SubscriptionsIndex
 *
 * Super Admin Livewire component for reviewing invoices and approving manual payment receipts.
 */
class SubscriptionsIndex extends BaseTable
{
    public string $statusFilter = 'pending';

    public function approvePayment(int $invoiceId, SubscriptionService $subscriptionService): void
    {
        $subscriptionService->approveInvoicePayment($invoiceId);

        $this->toastSuccess('Payment receipt approved and subscription activated!');
    }

    public function rejectPayment(int $invoiceId, SubscriptionService $subscriptionService): void
    {
        $subscriptionService->rejectInvoicePayment($invoiceId, 'Receipt could not be verified.');

        $this->toast('Payment receipt rejected.', 'warning');
    }

    public function render()
    {
        $invoices = SubscriptionInvoice::on('landlord')
            ->with(['tenant', 'subscription.plan'])
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('admin::livewire.subscriptions-index', [
            'invoices' => $invoices,
        ])->layout('layouts.admin', [
            'title' => 'Billing & Payment Approvals — Empetix',
            'header' => app()->getLocale() === 'ar' ? 'الاشتراكات واعتماد المدفوعات' : 'Subscriptions & Invoices',
        ]);
    }
}
