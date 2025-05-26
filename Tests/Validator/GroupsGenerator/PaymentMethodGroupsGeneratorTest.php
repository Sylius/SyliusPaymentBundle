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

namespace Tests\Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\PaymentMethodGroupsGenerator;
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\GatewayConfigGroupsGeneratorInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class PaymentMethodGroupsGeneratorTest extends TestCase
{
    /**
     * @var GatewayConfigGroupsGeneratorInterface|MockObject
     */
    private MockObject $gatewayConfigGroupsGeneratorMock;
    private PaymentMethodGroupsGenerator $paymentMethodGroupsGenerator;
    protected function setUp(): void
    {
        $this->gatewayConfigGroupsGeneratorMock = $this->createMock(GatewayConfigGroupsGeneratorInterface::class);
        $this->paymentMethodGroupsGenerator = new PaymentMethodGroupsGenerator(['sylius'], $this->gatewayConfigGroupsGeneratorMock);
    }

    public function testReturnsPaymentMethodValidationGroups(): void
    {
        /** @var GatewayConfigInterface|MockObject $gatewayConfigMock */
        $gatewayConfigMock = $this->createMock(GatewayConfigInterface::class);
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        $paymentMethodMock->expects($this->once())->method('getGatewayConfig')->willReturn($gatewayConfigMock);
        $this->gatewayConfigGroupsGeneratorMock->expects($this->once())->method('__invoke')->with($gatewayConfigMock)->willReturn(['paypal_express_checkout', 'sylius']);
        $this->assertSame(['sylius', 'paypal_express_checkout'], $this($paymentMethodMock));
    }

    public function testReturnsDefaultValidationGroupsIfGatewayConfigIsNull(): void
    {
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        $paymentMethodMock->expects($this->once())->method('getGatewayConfig')->willReturn(null);
        $this->assertSame(['sylius'], $this($paymentMethodMock));
    }
}
