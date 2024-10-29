<?php

namespace Sylius\Bundle\PaymentBundle\Checker;


use Sylius\Component\Payment\Model\GatewayConfigInterface;

/** @experimental */
interface GatewayConfigEncryptionCheckerInterface
{
    public function isEncryptionEnabled(GatewayConfigInterface $gatewayConfig): bool;
}
