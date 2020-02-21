<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Administration;

use App\Controller\Administration\Base\BaseController;
use App\Entity\User;
use App\Enum\PaymentRemainderStatusType;
use App\Model\Breadcrumb;
use App\Service\Interfaces\PaymentServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/payment_remainder")
 */
class PaymentRemainderController extends BaseController
{
    /**
     * @Route("/new", name="administration_payment_remainder_new")
     *
     * @return Response
     */
    public function newAction(PaymentServiceInterface $paymentService)
    {
        $this->closeOpenPayments($paymentService); // if new payment remainder is created
    }

    private function closeOpenPayments(PaymentServiceInterface $paymentService)
    {
        /** @var User[] $usersWithOpenPayment */
        $usersWithOpenPayment = $this->getDoctrine()->getRepository(User::class)->findBy(['paymentRemainderStatus' => PaymentRemainderStatusType::PAYMENT_STARTED]);
        foreach ($usersWithOpenPayment as $userWithOpenPayment) {
            $paymentService->closePayment($userWithOpenPayment->getPaymentInfo());
            $userWithOpenPayment->setPaymentRemainderStatus(PaymentRemainderStatusType::PAYMENT_ABORTED);
            $userWithOpenPayment->clearPaymentInfo();
            $this->fastSave($userWithOpenPayment);
        }
    }

    /**
     * get the breadcrumbs leading to this controller.
     *
     * @return Breadcrumb[]
     */
    protected function getIndexBreadcrumbs()
    {
        return array_merge(parent::getIndexBreadcrumbs(), [
            new Breadcrumb(
                $this->generateUrl('administration_users'),
                $this->getTranslator()->trans('index.title', [], 'administration_users')
            ),
        ]);
    }
}
