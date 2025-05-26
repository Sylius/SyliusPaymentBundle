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

namespace Tests\Sylius\Bundle\PaymentBundle\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\PaymentBundle\Validator\Constraints\GatewayFactoryExistsValidator;
use Sylius\Bundle\PaymentBundle\Validator\Constraints\GatewayFactoryExists;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class GatewayFactoryExistsValidatorTest extends TestCase
{
    /**
     * @var ExecutionContextInterface|MockObject
     */
    private MockObject $executionContextMock;
    private GatewayFactoryExistsValidator $gatewayFactoryExistsValidator;
    protected function setUp(): void
    {
        $this->executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->gatewayFactoryExistsValidator = new GatewayFactoryExistsValidator(['paypal' => 'sylius.payum_gateway_factory.paypal', 'stripe_checkout' => 'sylius.payum_gateway_factory.stripe_checkout']);
        $this->initialize($this->executionContextMock);
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfGatewayFactoryExists(): void
    {
        /** @var Constraint|MockObject $constraintMock */
        $constraintMock = $this->createMock(Constraint::class);
        /** @var GatewayConfigInterface|MockObject $gatewayConfigMock */
        $gatewayConfigMock = $this->createMock(GatewayConfigInterface::class);
        $this->expectException(UnexpectedTypeException::class);
        $this->gatewayFactoryExistsValidator->validate($gatewayConfigMock, $constraintMock);
    }

    public function testAddsViolationToGatewayConfigurationWithWrongName(): void
    {
        /** @var ConstraintViolationBuilderInterface|MockObject $constraintViolationBuilderMock */
        $constraintViolationBuilderMock = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->executionContextMock->expects($this->once())->method('buildViolation')->with((new GatewayFactoryExists())->invalidGatewayFactory)->willReturn($constraintViolationBuilderMock);
        $constraintViolationBuilderMock->expects($this->once())->method('setParameter')->with($this->any())->willReturn($constraintViolationBuilderMock);
        $constraintViolationBuilderMock->expects($this->once())->method('addViolation');
        $this->gatewayFactoryExistsValidator->validate('wrong_factory', new GatewayFactoryExists());
    }

    public function testDoesNotAddViolationToGatewayConfigurationWithCorrectName(): void
    {
        $this->executionContextMock->expects($this->never())->method('buildViolation')->with($this->any());
        $this->gatewayFactoryExistsValidator->validate('paypal', new GatewayFactoryExists());
    }
}
