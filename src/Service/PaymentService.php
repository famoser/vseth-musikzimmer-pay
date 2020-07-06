<?php

/*
 * This file is part of the vseth-musikzimmer-pay project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Entity\PaymentRemainder;
use App\Entity\User;
use App\Enum\PaymentRemainderStatusType;
use App\Model\Bill;
use App\Model\TransactionInfo;
use App\Service\Interfaces\BillServiceInterface;
use App\Service\Interfaces\EmailServiceInterface;
use App\Service\Interfaces\PaymentServiceInterface;
use App\Service\Payment\Interfaces\PaymentProviderServiceInterface;
use App\Service\Payment\PayrexxService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\RouterInterface;

class PaymentService implements PaymentServiceInterface
{
    /**
     * @var PaymentProviderServiceInterface
     */
    private $paymentProviderService;

    /**
     * @var BillServiceInterface
     */
    private $billService;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var EmailServiceInterface
     */
    private $emailService;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * UserPaymentService constructor.
     *
     * @param EmailServiceInterface $emailService
     */
    public function __construct(PayrexxService $paymentService, ManagerRegistry $doctrine, Interfaces\EmailServiceInterface $emailService, RouterInterface $router, BillServiceInterface $billService)
    {
        $this->paymentProviderService = $paymentService;
        $this->doctrine = $doctrine;
        $this->emailService = $emailService;
        $this->router = $router;
        $this->billService = $billService;
    }

    public function sendPaymentRemainder(User $user)
    {
        $paymentRemainder = $this->doctrine->getRepository(PaymentRemainder::class)->findActive();

        $body = $paymentRemainder->getBody();
        $url = $this->router->generate('login_code', ['code' => $user->getAuthenticationCode()], RouterInterface::ABSOLUTE_URL);
        $body = str_replace('(url)', $url, $body);
        $name = $user->getGivenName() . ' ' . $user->getFamilyName();
        $body = str_replace('(name)', $name, $body);

        $this->emailService->sendEmail($user->getEmail(), $paymentRemainder->getSubject(), $body);

        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::NONE) {
            $user->setPaymentRemainderStatus(PaymentRemainderStatusType::SENT);
        }
        $user->setPaymentRemainder($paymentRemainder);
        $this->save($user);
    }

    /**
     * @throws \Payrexx\PayrexxException
     */
    public function startPayment(User $user, Bill $bill, string $url)
    {
        $paymentInfo = $this->paymentProviderService->startPayment($bill, $url);

        $user->writePaymentInfo($paymentInfo);
        $user->setPaymentRemainderStatus(PaymentRemainderStatusType::PAYMENT_STARTED);
        $this->save($user);
    }

    /**
     * @throws \Exception
     */
    public function refreshPaymentStatus(User $user)
    {
        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_STARTED) {
            /** @var TransactionInfo $transactionInfo */
            $successful = $this->paymentProviderService->paymentSuccessful($user->getPaymentInfo(), $transactionInfo);
            if ($successful) {
                $user->setAmountPayed($transactionInfo->getAmount());
                $user->setTransactionId($transactionInfo->getId());
                $user->setPaymentRemainderStatus(PaymentRemainderStatusType::PAYMENT_SUCCESSFUL);
                $this->save($user);
            }
        }
    }

    /**
     * @throws \Payrexx\PayrexxException
     * @throws \Exception
     */
    public function closeInvoice(User $user)
    {
        $this->paymentProviderService->closePayment($user->getPaymentInfo());
        $user->setPaymentRemainderStatus(PaymentRemainderStatusType::PAYMENT_ABORTED);
        $user->clearPaymentInfo();

        $this->save($user);
    }

    private function save(User $user)
    {
        $manager = $this->doctrine->getManager();
        $manager->persist($user);
        $manager->flush();
    }
}
