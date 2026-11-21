<?php

declare(strict_types=1);

namespace App\Modules\Billing\Gateways;

use App\Modules\Billing\Models\SubscriptionInvoice;

/**
 * Class ManualPaymentGateway
 *
 * Handles manual payments (Bank Transfer, Instapay, Vodafone Cash) where
 * the customer uploads a transaction receipt and Super Admin verifies it.
 */
class ManualPaymentGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'manual';
    }

    public function process(SubscriptionInvoice $invoice, array $payload = []): bool
    {
        $invoice->update([
            'status' => 'pending',
            'receipt_path' => $payload['receipt_path'] ?? null,
            'notes' => $payload['notes'] ?? null,
        ]);

        return true;
    }

    public function requiresManualApproval(): bool
    {
        return true;
    }
}
