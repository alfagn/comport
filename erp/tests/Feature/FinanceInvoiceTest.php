<?php

declare(strict_types=1);

use App\Modules\CRM\Models\CrmClient;
use App\Modules\Finance\Services\InvoiceService;
use App\Modules\Tenancy\Models\Tenant;

it('creates commercial invoices, computes tax and balance, and records settlements', function () {
    $tenant = Tenant::on('landlord')->where('slug', 'acme')->first();
    if ($tenant === null) {
        $this->markTestSkipped('Acme tenant not seeded.');
    }

    $tenant->makeCurrent();

    $client = CrmClient::on('tenant')->firstOrCreate([
        'name' => 'Test Tech Client',
        'email' => 'client@tech.test',
    ]);

    $service = app(InvoiceService::class);

    $invoice = $service->createInvoice(
        invoiceData: [
            'client_id' => $client->id,
            'tax_minor' => 140000, // 1,400 EGP tax
            'discount_minor' => 40000, // 400 EGP discount
            'currency' => 'EGP',
        ],
        items: [
            ['description' => 'Software Cloud Setup', 'quantity' => 2, 'unit_price_minor' => 500000], // 10,000 EGP
        ]
    );

    // Subtotal: 10,000 + 1,400 (tax) - 400 (discount) = 11,000 EGP (1100000 minor)
    expect($invoice->subtotal_minor)->toBe(1000000);
    expect($invoice->total_minor)->toBe(1100000);
    expect($invoice->status)->toBe('draft');
    expect($invoice->isFullyPaid())->toBeFalse();

    // Record partial payment of 6,000 EGP (600000 minor)
    $service->recordPayment($invoice, 600000, 'bank_transfer');
    $invoice->refresh();

    expect($invoice->amount_paid_minor)->toBe(600000);
    expect($invoice->status)->toBe('partial');
    expect($invoice->balance_due->toDecimal())->toBe(5000.0);

    // Settle remaining 5,000 EGP
    $service->recordPayment($invoice, 500000, 'bank_transfer');
    $invoice->refresh();

    expect($invoice->status)->toBe('paid');
    expect($invoice->isFullyPaid())->toBeTrue();
    expect($invoice->balance_due->amountInMinorUnits)->toBe(0);

    $tenant->forgetCurrent();
});
