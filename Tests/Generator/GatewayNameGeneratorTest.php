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

namespace Tests\Sylius\Bundle\PaymentBundle\Generator;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\Generator\GatewayNameGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class GatewayNameGeneratorTest extends TestCase
{
    private GatewayNameGenerator $gatewayNameGenerator;
    protected function setUp(): void
    {
        $this->gatewayNameGenerator = new GatewayNameGenerator();
    }
    public function testGeneratesGatewayConfigNameBasedOnPaymentMethodCode(): void
    {
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        $paymentMethodMock->expects($this->once())->method('getCode')->willReturn('PayPal Express Checkout');
        $this->assertSame('paypal_express_checkout', $this->gatewayNameGenerator->generate($paymentMethodMock));
    }

    public function testReturnsNullIfPaymentMethodCodeIsNull(): void
    {
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        $paymentMethodMock->expects($this->once())->method('getCode')->willReturn(null);
        $this->assertNull($this->gatewayNameGenerator->generate($paymentMethodMock));
    }
}
