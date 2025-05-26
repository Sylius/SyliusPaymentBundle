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
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\GatewayConfigGroupsGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Payment\Model\GatewayConfigInterface;

final class GatewayConfigGroupsGeneratorTest extends TestCase
{
    private GatewayConfigGroupsGenerator $gatewayConfigGroupsGenerator;
    protected function setUp(): void
    {
        $this->gatewayConfigGroupsGenerator = new GatewayConfigGroupsGenerator(['sylius'], [
            'paypal_express_checkout' => ['paypal_express_checkout', 'sylius'],
            'stripe_checkout' => ['stripe_checkout', 'sylius'],
        ]);
    }

    public function testReturnsGatewayConfigValidationGroups(): void
    {
        /** @var GatewayConfigInterface|MockObject $gatewayConfigMock */
        $gatewayConfigMock = $this->createMock(GatewayConfigInterface::class);
        $gatewayConfigMock->expects($this->once())->method('getFactoryName')->willReturn('paypal_express_checkout');
        $this->assertSame(['paypal_express_checkout', 'sylius'], $this($gatewayConfigMock));
    }

    public function testReturnsDefaultValidationGroupsIfFactoryNameIsNull(): void
    {
        /** @var GatewayConfigInterface|MockObject $gatewayConfigMock */
        $gatewayConfigMock = $this->createMock(GatewayConfigInterface::class);
        $gatewayConfigMock->expects($this->once())->method('getFactoryName')->willReturn(null);
        $this->assertSame(['sylius'], $this($gatewayConfigMock));
    }
}
