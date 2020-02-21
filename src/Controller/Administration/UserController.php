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
use App\Form\User\EditDiscountType;
use App\Model\Breadcrumb;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * @Route("/{organisation}/edit_discount", name="administration_user_edit_discount")
     *
     * @return Response
     */
    public function editDiscountAction(Request $request, User $user, TranslatorInterface $translator)
    {
        //create persist callable
        $myOnSuccessCallable = function ($form) use ($user, $translator) {
            $manager = $this->getDoctrine()->getManager();

            if ($user->getDiscount() !== 0 && $user->getDiscountDescription() === '') {
                $errorText = $translator->trans('edit_discount.error.no_discount_description', [], 'administration_user');
                $this->displayError($errorText);
            } else {
                $manager->persist($user);
                $manager->flush();

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

        return $this->render('administration/organisation/edit.html.twig', ['form' => $myForm->createView(), 'organisation' => $user]);
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
                $this->getTranslator()->trans('index.title', [], 'administration_user')
            ),
        ]);
    }
}
