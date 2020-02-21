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
    public function indexAction(User $user)
    {
        $this->ensureAccessGranted($user);

        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_SUCCESSFUL) {
            return $this->redirectToRoute('payment_successful', ['user' => $user->getId()]);
        } elseif ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_STARTED) {
            return $this->redirect($user->getInvoiceLink());
        }

        $user->setPaymentRemainderStatus(PaymentRemainderStatusType::SEEN);
        $user->setPaymentRemainderStatusAt(new \DateTime());
        $this->fastSave($user);

        $output = ['user' => $user];

        return $this->render('payment/view.html.twig', $output);
    }

    /**
     * @Route("/{user}/confirm", name="payment_confirm")
     *
     * @return Response
     */
    public function confirmAction(User $user)
    {
        $this->ensureAccessGranted($user);

        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_STARTED) {
            return $this->redirect($user->getInvoiceLink());
        }

        if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_SUCCESSFUL) {
            return $this->redirectToRoute('payment_successful', ['user' => $user->getId()]);
        }

        // TODO: initiate payrexx payment

        return $this->redirect('payrexx url');
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
            // TODO: check payrexx url for success
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
