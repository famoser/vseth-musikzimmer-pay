<?php

/*
 * This file is part of the vseth-musikzimmer-pay project.
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
use App\Service\Interfaces\SettingsServiceInterface;
use App\Service\Interfaces\UserPaymentServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

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
    public function indexAction(User $user, BillServiceInterface $billService, SettingsServiceInterface $settingsService)
    {
        $this->ensureAccessGranted($user);

        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_SUCCESSFUL || $user->getMarkedAsPayed()) {
            return $this->redirectToRoute('payment_successful', ['user' => $user->getId()]);
        } elseif ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_STARTED) {
            return $this->redirectToRoute('payment_confirm', ['user' => $user->getId()]);
        }

        if (\in_array('ROLE_USER', $this->getUser()->getRoles(), true)) {
            $user->setPaymentRemainderStatus(PaymentRemainderStatusType::SEEN);
            $this->fastSave($user);
        }

        $bill = $billService->createBill($user);
        $setting = $settingsService->get();

        return $this->render('payment/view.html.twig', ['user' => $user, 'bill' => $bill, 'setting' => $setting]);
    }

    /**
     * @Route("/{user}/confirm", name="payment_confirm")
     *
     * @throws \Exception
     * @throws \Payrexx\PayrexxException
     *
     * @return Response
     */
    public function confirmAction(User $user, BillServiceInterface $billService, UserPaymentServiceInterface $userPaymentService)
    {
        $this->ensureAccessGranted($user);

        if ($user->getMarkedAsPayed()) {
            return $this->redirectToRoute('payment_successful', ['user' => $user->getId()]);
        }

        $userPaymentService->refreshPaymentStatus($user);
        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_STARTED) {
            return $this->redirect($user->getInvoiceLink());
        }

        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_SUCCESSFUL) {
            return $this->redirectToRoute('payment_successful', ['user' => $user->getId()]);
        }

        // start payment
        $successUrl = $this->generateUrl('payment_successful', ['user' => $user->getId()], RouterInterface::ABSOLUTE_URL);
        $bill = $billService->createBill($user);
        $userPaymentService->startPayment($user, $bill, $successUrl);

        return $this->redirect($user->getInvoiceLink());
    }

    /**
     * @Route("/{user}/successful", name="payment_successful")
     *
     * @return Response
     */
    public function successfulAction(User $user)
    {
        $this->ensureAccessGranted($user);

        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_STARTED) {
            return $this->redirectToRoute('payment_confirm', ['user' => $user->getId()]);
        }

        if ($user->getPaymentRemainderStatus() !== PaymentRemainderStatusType::PAYMENT_SUCCESSFUL && !$user->getMarkedAsPayed()) {
            return $this->redirectToRoute('payment_index', ['user' => $user->getId()]);
        }

        return $this->render('payment/successful.html.twig');
    }

    private function ensureAccessGranted(User $user)
    {
        $this->denyAccessUnlessGranted(BaseVoter::VIEW, $user);
    }
}
