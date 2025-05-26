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

namespace Tests\Sylius\Bundle\PaymentBundle\CommandHandler\Offline;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\PaymentBundle\CommandHandler\Offline\CapturePaymentRequestHandler;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Command\Offline\CapturePaymentRequest;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;

final class CapturePaymentRequestHandlerTest extends TestCase
{
    /**
     * @var PaymentRequestProviderInterface|MockObject
     */
    private MockObject $paymentRequestProviderMock;
    /**
     * @var StateMachineInterface|MockObject
     */
    private MockObject $stateMachineMock;
    private CapturePaymentRequestHandler $capturePaymentRequestHandler;
    protected function setUp(): void
    {
        $this->paymentRequestProviderMock = $this->createMock(PaymentRequestProviderInterface::class);
        $this->stateMachineMock = $this->createMock(StateMachineInterface::class);
        $this->capturePaymentRequestHandler = new CapturePaymentRequestHandler($this->paymentRequestProviderMock, $this->stateMachineMock);
    }

    public function testProcessesOfflineCapture(): void
    {
        /** @var PaymentRequestInterface|MockObject $paymentRequestMock */
        $paymentRequestMock = $this->createMock(PaymentRequestInterface::class);
        $capturePaymentRequest = new CapturePaymentRequest('hash');
        $this->paymentRequestProviderMock->expects($this->once())->method('provide')->with($capturePaymentRequest)->willReturn($paymentRequestMock);
        $this->stateMachineMock->expects($this->once())->method('apply')->with($paymentRequestMock, PaymentRequestTransitions::GRAPH, PaymentRequestTransitions::TRANSITION_COMPLETE);
        $this->capturePaymentRequestHandler->__invoke($capturePaymentRequest);
    }
}
