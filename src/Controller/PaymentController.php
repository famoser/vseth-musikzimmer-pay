<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Controller\Administration\Base\BaseController;
use App\Entity\User;
use App\Enum\PaymentRemainderStatusType;
use App\Security\Voter\Base\BaseVoter;
use App\Service\Interfaces\BillServiceInterface;
use App\Service\Interfaces\PaymentServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/payment")
 */
class PaymentController extends BaseController
{
    /**
     * @Route("/{user}", name="payment_index")
     *
     * @return Response
     */
    public function indexAction(User $user, BillServiceInterface $billService)
    {
        $this->ensureAccessGranted($user);

        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_SUCCESSFUL) {
            return $this->redirectToRoute('payment_successful', ['user' => $user->getId()]);
        } elseif ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_STARTED) {
            return $this->redirect($user->getInvoiceLink());
        }

        $user->setPaymentRemainderStatus(PaymentRemainderStatusType::SEEN);
        $this->fastSave($user);

        $bill = $billService->createBill($user);

        return $this->render('payment/view.html.twig', ['user' => $user, 'bill' => $bill]);
    }

    /**
     * @Route("/{user}/confirm", name="payment_confirm")
     *
     * @return Response
     */
    public function confirmAction(User $user, BillServiceInterface $billService, PaymentServiceInterface $paymentService)
    {
        $this->ensureAccessGranted($user);

        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_STARTED) {
            return $this->redirect($user->getInvoiceLink());
        }

        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_SUCCESSFUL) {
            return $this->redirectToRoute('payment_successful', ['user' => $user->getId()]);
        }

        $bill = $billService->createBill($user);
        $paymentInfo = $paymentService->startPayment($bill);

        $user->writePaymentInfo($paymentInfo);
        $user->setPaymentRemainderStatus(PaymentRemainderStatusType::PAYMENT_STARTED);
        $this->fastSave($user);

        return $this->redirect($user->getInvoiceLink());
    }

    /**
     * @Route("/{user}/successful", name="payment_successful")
     *
     * @return Response
     */
    public function successfulAction(User $user, PaymentServiceInterface $paymentService)
    {
        $this->ensureAccessGranted($user);

        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_STARTED) {
            $successful = $paymentService->paymentSuccessful($user->getPaymentInfo());
            if (!$successful) {
                return $this->redirect($user->getInvoiceLink());
            }

            $user->setPaymentRemainderStatus(PaymentRemainderStatusType::PAYMENT_SUCCESSFUL);
            $this->fastSave($user);
        }

        if ($user->getPaymentRemainderStatus() !== PaymentRemainderStatusType::PAYMENT_SUCCESSFUL) {
            return $this->redirectToRoute('payment_index', ['user' => $user->getId()]);
        }

        return $this->render('payment/successful.html.twig');
    }

    private function ensureAccessGranted(User $user)
    {
        $this->denyAccessUnlessGranted(BaseVoter::VIEW, $user);
    }
}
