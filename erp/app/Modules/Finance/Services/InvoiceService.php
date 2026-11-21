<?php

declare(strict_types=1);

namespace App\Modules\Finance\Services;

use App\Core\Services\BaseService;
use App\Modules\Finance\Models\Estimate;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Finance\Models\InvoiceItem;
use App\Modules\Finance\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class InvoiceService
 *
 * Domain service managing commercial invoices, payment tracking, and balance settlements.
 */
class InvoiceService extends BaseService
{
    /**
     * Create a commercial client invoice with line items.
     *
     * @param  array<string, mixed>  $invoiceData
     * @param  array<int, array{description: string, quantity: float, unit_price_minor: int}>  $items
     *
     * @throws Throwable
     */
    public function createInvoice(array $invoiceData, array $items): Invoice
    {
        return $this->transaction(function () use ($invoiceData, $items) {
            $subtotalMinor = 0;
            $preparedItems = [];

            foreach ($items as $item) {
                $lineTotalMinor = (int) round($item['quantity'] * $item['unit_price_minor']);
                $subtotalMinor += $lineTotalMinor;

                $preparedItems[] = [
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price_minor' => $item['unit_price_minor'],
                    'total_minor' => $lineTotalMinor,
                ];
            }

            $taxMinor = (int) ($invoiceData['tax_minor'] ?? 0);
            $discountMinor = (int) ($invoiceData['discount_minor'] ?? 0);
            $totalMinor = max(0, $subtotalMinor + $taxMinor - $discountMinor);

            $invoiceNumber = $invoiceData['invoice_number'] ?? ('INV-'.date('Ym').'-'.strtoupper(Str::random(4)));

            /** @var Invoice $invoice */
            $invoice = Invoice::on('tenant')->create([
                'client_id' => $invoiceData['client_id'],
                'project_id' => $invoiceData['project_id'] ?? null,
                'invoice_number' => $invoiceNumber,
                'issue_date' => $invoiceData['issue_date'] ?? Carbon::now(),
                'due_date' => $invoiceData['due_date'] ?? Carbon::now()->addDays(15),
                'subtotal_minor' => $subtotalMinor,
                'tax_minor' => $taxMinor,
                'discount_minor' => $discountMinor,
                'total_minor' => $totalMinor,
                'amount_paid_minor' => 0,
                'currency' => $invoiceData['currency'] ?? 'EGP',
                'status' => 'draft',
                'notes' => $invoiceData['notes'] ?? null,
                'terms' => $invoiceData['terms'] ?? null,
            ]);

            foreach ($preparedItems as $item) {
                InvoiceItem::on('tenant')->create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price_minor' => $item['unit_price_minor'],
                    'total_minor' => $item['total_minor'],
                ]);
            }

            $this->logInfo('Commercial invoice created', [
                'invoice_id' => $invoice->id,
                'number' => $invoiceNumber,
                'total_minor' => $totalMinor,
            ]);

            return $invoice;
        }, connection: 'tenant');
    }

    /**
     * Record a payment against an invoice and update its status.
     *
     * @throws Throwable
     */
    public function recordPayment(Invoice $invoice, int $amountMinor, string $method, ?string $reference = null): Payment
    {
        return $this->transaction(function () use ($invoice, $amountMinor, $method, $reference) {
            $payment = Payment::on('tenant')->create([
                'invoice_id' => $invoice->id,
                'amount_minor' => $amountMinor,
                'currency' => $invoice->currency,
                'payment_method' => $method,
                'transaction_reference' => $reference,
                'payment_date' => Carbon::now(),
            ]);

            $newPaid = $invoice->amount_paid_minor + $amountMinor;
            $newStatus = $newPaid >= $invoice->total_minor ? 'paid' : 'partial';

            $invoice->update([
                'amount_paid_minor' => $newPaid,
                'status' => $newStatus,
            ]);

            $this->logInfo('Recorded payment on invoice', [
                'invoice_id' => $invoice->id,
                'amount_minor' => $amountMinor,
                'new_status' => $newStatus,
            ]);

            return $payment;
        }, connection: 'tenant');
    }

    /**
     * Convert an accepted estimate into an active invoice.
     *
     * @throws Throwable
     */
    public function convertEstimateToInvoice(Estimate $estimate): Invoice
    {
        return $this->transaction(function () use ($estimate) {
            $invoice = Invoice::on('tenant')->create([
                'client_id' => $estimate->client_id,
                'invoice_number' => 'INV-'.date('Ym').'-'.strtoupper(Str::random(4)),
                'issue_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays(15),
                'subtotal_minor' => $estimate->subtotal_minor,
                'tax_minor' => $estimate->tax_minor,
                'discount_minor' => 0,
                'total_minor' => $estimate->total_minor,
                'amount_paid_minor' => 0,
                'currency' => $estimate->currency,
                'status' => 'draft',
            ]);

            $estimate->update(['status' => 'accepted']);

            return $invoice;
        }, connection: 'tenant');
    }
}
