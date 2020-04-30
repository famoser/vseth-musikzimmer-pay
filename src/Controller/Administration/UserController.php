<?php

/*
 * This file is part of the vseth-musikzimmer-pay project.
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
use App\Form\User\EditDiscountType;
use App\Model\Breadcrumb;
use App\Service\Interfaces\BillServiceInterface;
use App\Service\Interfaces\PaymentServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/user")
 */
class UserController extends BaseController
{
    /**
     * @Route("/{user}/edit_discount", name="administration_user_edit_discount")
     *
     * @return Response
     */
    public function editDiscountAction(Request $request, User $user, TranslatorInterface $translator, PaymentServiceInterface $userPaymentService)
    {
        $discountBefore = $user->getDiscount();

        //create persist callable
        $myOnSuccessCallable = function (FormInterface $form) use ($user, $translator, $discountBefore, $userPaymentService) {
            if (($user->getDiscount() !== 0 || $user->getMarkedAsPayed()) && $user->getDiscountDescription() === null) {
                $errorText = $translator->trans('edit_discount.error.no_discount_description', [], 'administration_user');
                $this->displayError($errorText);
            } else {
                $this->fastSave($user);

                // close invoice if discount has changed
                if ($user->getDiscount() !== $discountBefore || $user->getMarkedAsPayed()) {
                    $userPaymentService->refreshPaymentStatus($user);
                    if ($user->getPaymentRemainderStatus() === PaymentRemainderStatusType::PAYMENT_STARTED) {
                        $userPaymentService->closeInvoice($user);
                    }
                }

                $successfulText = $translator->trans('form.successful.updated', [], 'framework');
                $this->displaySuccess($successfulText);
            }

            return $form;
        };

        //handle the form
        $buttonLabel = $translator->trans('form.submit_buttons.update', [], 'framework');
        $myForm = $this->handleForm(
            $this->createForm(EditDiscountType::class, $user)
                ->add('submit', SubmitType::class, ['label' => $buttonLabel, 'translation_domain' => false]),
            $request,
            $myOnSuccessCallable
        );

        if ($myForm instanceof Response) {
            return $myForm;
        }

        return $this->render('administration/user/edit.html.twig', ['form' => $myForm->createView(), 'user' => $user]);
    }

    /**
     * @Route("/{user}/close_invoice", name="administration_user_close_invoice")
     *
     * @return Response
     */
    public function closeInvoiceAction(User $user, TranslatorInterface $translator, PaymentServiceInterface $userPaymentService)
    {
        $userPaymentService->closeInvoice($user);

        $invoiceClosed = $translator->trans('close_invoice.successful', ['email' => $user->getEmail()], 'administration_user');
        $this->displaySuccess($invoiceClosed);

        return $this->redirectToRoute('administration');
    }

    /**
     * @Route("/{user}/send_payment_remainder", name="administration_user_send_payment_remainder")
     *
     * @return Response
     */
    public function sendPaymentRemainderAction(User $user, TranslatorInterface $translator, PaymentServiceInterface $userPaymentService)
    {
        $userPaymentService->sendPaymentRemainder($user);

        $invoiceClosed = $translator->trans('send_payment_remainder.successful', ['email' => $user->getEmail()], 'administration_user');
        $this->displaySuccess($invoiceClosed);

        return $this->redirectToRoute('administration');
    }

    /**
     * @Route("/recalculate", name="administration_user_recalculate")
     *
     * @return Response
     */
    public function recalculateAction(BillServiceInterface $billService, TranslatorInterface $translator)
    {
        /** @var User[] $users */
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $billService->setAmountOwed($user);
        }
        $this->fastSave(...$users);

        $recalculationSuccessful = $translator->trans('recalculate.successful', [], 'administration_user');
        $this->displaySuccess($recalculationSuccessful);

        return $this->redirectToRoute('administration');
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
