<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Tests\Stub;

use Sylius\Bundle\PaymentBundle\Attribute\AsPaymentNotifyProvider;
use Sylius\Bundle\PaymentBundle\Provider\PaymentNotifyProviderInterface;
use Sylius\Component\Payment\Model\Payment;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Symfony\Component\HttpFoundation\Request;

#[AsPaymentNotifyProvider(priority: 15)]
final class PaymentNotifyProviderStub implements PaymentNotifyProviderInterface
{
    public function getPayment(Request $request, PaymentMethodInterface $paymentMethod): PaymentInterface {
        return new Payment();
    }

    public function supports(Request $request, PaymentMethodInterface $paymentMethod): bool {
        return true;
    }
}
