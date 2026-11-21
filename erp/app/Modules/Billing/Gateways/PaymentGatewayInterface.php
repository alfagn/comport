<?php

declare(strict_types=1);

namespace App\Modules\Billing\Gateways;

use App\Modules\Billing\Models\SubscriptionInvoice;

/**
 * Interface PaymentGatewayInterface
 *
 * Strategy contract for processing SaaS subscription payments.
 */
interface PaymentGatewayInterface
{
    /**
     * Get provider name.
     */
    public function getName(): string;

    /**
     * Process or record payment for a subscription invoice.
     *
     * @param  array<string, mixed>  $payload
     */
    public function process(SubscriptionInvoice $invoice, array $payload = []): bool;

    /**
     * Determine if this gateway requires manual proof verification (e.g. Bank Transfer, Instapay).
     */
    public function requiresManualApproval(): bool;
}
