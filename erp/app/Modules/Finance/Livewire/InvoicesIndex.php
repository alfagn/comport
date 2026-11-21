<?php

declare(strict_types=1);

namespace App\Modules\Finance\Livewire;

use App\Core\Livewire\BaseTable;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Finance\Services\InvoiceService;

/**
 * Class InvoicesIndex
 *
 * Livewire component managing commercial client invoices, settlements, and payment recording.
 */
class InvoicesIndex extends BaseTable
{
    public string $statusFilter = '';

    public function recordPayment(int $invoiceId, InvoiceService $invoiceService): void
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::on('tenant')->findOrFail($invoiceId);
        $dueMinor = $invoice->balance_due->amountInMinorUnits;

        if ($dueMinor <= 0) {
            $this->toast('Invoice is already fully paid.', 'warning');

            return;
        }

        $invoiceService->recordPayment($invoice, $dueMinor, 'bank_transfer', 'MANUAL-PAY');

        $this->toastSuccess('Payment recorded and invoice marked as fully paid!');
    }

    public function render()
    {
        $invoices = Invoice::on('tenant')
            ->with(['client', 'items'])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('invoice_number', 'like', "%{$this->search}%")
                        ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('finance::livewire.invoices-index', [
            'invoices' => $invoices,
        ])->layout('layouts.tenant', [
            'title' => 'Commercial Invoices & Billing',
            'header' => app()->getLocale() === 'ar' ? 'فواتير العملاء والتحصيل المالي' : 'Commercial Invoices & Client Billing',
        ]);
    }
}
