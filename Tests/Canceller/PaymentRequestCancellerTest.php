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

namespace Tests\Sylius\Bundle\PaymentBundle\Canceller;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\PaymentBundle\Canceller\PaymentRequestCanceller;
use Doctrine\Persistence\ObjectManager;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

final class PaymentRequestCancellerTest extends TestCase
{
    /**
     * @var PaymentRequestRepositoryInterface|MockObject
     */
    private MockObject $paymentRequestRepositoryMock;
    /**
     * @var StateMachineInterface|MockObject
     */
    private MockObject $stateMachineMock;
    /**
     * @var ObjectManager|MockObject
     */
    private MockObject $objectManagerMock;
    private PaymentRequestCanceller $paymentRequestCanceller;
    protected function setUp(): void
    {
        $this->paymentRequestRepositoryMock = $this->createMock(PaymentRequestRepositoryInterface::class);
        $this->stateMachineMock = $this->createMock(StateMachineInterface::class);
        $this->objectManagerMock = $this->createMock(ObjectManager::class);
        $this->paymentRequestCanceller = new PaymentRequestCanceller($this->paymentRequestRepositoryMock, $this->stateMachineMock, $this->objectManagerMock, [PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING]);
    }

    public function testCancelsPaymentRequestsIfThePaymentMethodCodeIsDifferent(): void
    {
        /** @var PaymentRequestInterface|MockObject $paymentRequest1Mock */
        $paymentRequest1Mock = $this->createMock(PaymentRequestInterface::class);
        /** @var PaymentRequestInterface|MockObject $paymentRequest2Mock */
        $paymentRequest2Mock = $this->createMock(PaymentRequestInterface::class);
        /** @var PaymentMethodInterface|MockObject $paymentMethod1Mock */
        $paymentMethod1Mock = $this->createMock(PaymentMethodInterface::class);
        /** @var PaymentMethodInterface|MockObject $paymentMethod2Mock */
        $paymentMethod2Mock = $this->createMock(PaymentMethodInterface::class);
        $this->paymentRequestRepositoryMock->expects($this->once())->method('findByPaymentIdAndStates')->with(1, [PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING])
            ->willReturn([$paymentRequest1Mock, $paymentRequest2Mock])
        ;
        $paymentRequest1Mock->expects($this->once())->method('getMethod')->willReturn($paymentMethod1Mock);
        $paymentMethod1Mock->expects($this->once())->method('getCode')->willReturn('payment_method_with_different_code');
        $paymentRequest2Mock->expects($this->once())->method('getMethod')->willReturn($paymentMethod2Mock);
        $paymentMethod2Mock->expects($this->once())->method('getCode')->willReturn('payment_method_code');
        $this->stateMachineMock->expects($this->once())->method('apply')->with($paymentRequest1Mock, 'sylius_payment_request', 'cancel');
        $this->stateMachineMock->expects($this->never())->method('apply')->with($paymentRequest2Mock, 'sylius_payment_request', 'cancel');
        $this->objectManagerMock->expects($this->once())->method('persist')->with($paymentRequest1Mock);
        $this->objectManagerMock->expects($this->never())->method('persist')->with($paymentRequest2Mock);
        $this->objectManagerMock->expects($this->once())->method('flush')->shouldBeCalledOnce();
        $this->paymentRequestCanceller->cancelPaymentRequests(1, 'payment_method_code');
    }

    public function testDoesNotCancelPaymentRequestsIfNoneFound(): void
    {
        $this->paymentRequestRepositoryMock->expects($this->once())->method('findByPaymentIdAndStates')->with(1, [PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING])
            ->willReturn([]);
        $this->stateMachineMock->expects($this->never())->method('apply')->with($this->any());
        $this->paymentRequestCanceller->cancelPaymentRequests(1, 'payment_method_code');
    }
}
