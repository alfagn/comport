<?php

declare(strict_types=1);

namespace App\Modules\Billing\Gateways;

use InvalidArgumentException;

/**
 * Class PaymentGatewayFactory
 *
 * Factory pattern resolving appropriate PaymentGateway strategy.
 */
class PaymentGatewayFactory
{
    public static function make(string $method = 'manual'): PaymentGatewayInterface
    {
        return match (strtolower($method)) {
            'manual', 'bank_transfer', 'instapay', 'vodafone_cash' => new ManualPaymentGateway,
            default => throw new InvalidArgumentException("Unsupported payment gateway method: {$method}"),
        };
    }
}
