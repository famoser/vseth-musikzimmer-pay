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
use App\Entity\PaymentRemainder;
use App\Entity\User;
use App\Enum\PaymentRemainderStatusType;
use App\Form\PaymentRemainder\PaymentRemainderType;
use App\Model\Breadcrumb;
use App\Service\Interfaces\PaymentServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    public function newAction(Request $request, PaymentServiceInterface $paymentService, TranslatorInterface $translator)
    {
        $paymentRemainder = $this->createPaymentRemainder();

        //create persist callable
        $myOnSuccessCallable = function ($form) use ($paymentRemainder, $paymentService, $translator) {
            $this->closeOpenPayments($paymentService); // if new payment remainder is created

            return $form;
        };

        //handle the form
        $buttonLabel = $translator->trans('form.submit_buttons.update', [], 'framework');
        $myForm = $this->handleForm(
            $this->createForm(PaymentRemainderType::class, $paymentRemainder)
                ->add('submit', SubmitType::class, ['label' => $buttonLabel, 'translation_domain' => false])
                ->add('send_test_email', SubmitType::class, ['label' => $buttonLabel, 'translation_domain' => false]),
            $request,
            $myOnSuccessCallable
        );

        if ($myForm instanceof Response) {
            return $myForm;
        }

        return $this->render('administration/payment_remainder/new.html.twig', ['form' => $myForm->createView()]);
    }

    private function createPaymentRemainder()
    {
        $paymentRemainder = new PaymentRemainder();
        $paymentRemainder->setName('Invoice / Rechnung');
        $paymentRemainder->setSubject('[VSETH] music rooms invoice 2019 / Musikzimmer Rechnung 2019');

        $supportEmail = $this->getParameter('REPLY_EMAIL');
        $german = "Hallo (name)\n\nDer VSETH dankt für die Nutzung der Musikzimmer!\nUm den offenen Bertrag von 2019 zu zahlen bitte folge dem Link: (url)\n\nLiebe Grüsse\nVSETH\n\nPS: Bitte antworte nicht auf diese Mail. Wenn du Fragen hast, wende dich an " . $supportEmail;
        $english = "Hi (name)\n\nVSETH thanks you for using the music rooms!\nTo pay your open fees from 2019, please follow the link: (url)\n\nBest regards\nVSETH\n\nPS: Please do not answer this mail. If you have questions write us at " . $supportEmail;
        $paymentRemainder->setBody("(english E-Mail below)\n\n" . $german . "\n\n\n" . $english);

        $paymentRemainder->setFee(0);
        $paymentRemainder->setDueAt((new \DateTime())->add(new \DateInterval('P1M')));

        return $paymentRemainder;
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
                $this->generateUrl('administration'),
                $this->getTranslator()->trans('index.title', [], 'administration')
            ),
        ]);
    }
}
