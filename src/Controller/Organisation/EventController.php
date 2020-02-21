<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Organisation;

use App\Controller\Administration\Base\BaseController;
use App\Entity\Organisation;
use App\Entity\Reservation;
use App\Form\Type\SemesterType;
use App\Model\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/event")
 */
class EventController extends BaseController
{
    /**
     * @var Organisation
     */
    private $organisation;

    /**
     * @Route("/new", name="organisation_event_new")
     *
     * @return Response
     */
    public function newAction(Organisation $organisation, Request $request, TranslatorInterface $translator)
    {
        //create the event
        $event = new Reservation();
        $event->setSemester(SemesterType::getCurrentSemester());
        $event->setOrganisation($organisation);
        $event->setLocation('');
        $event->setRevenue(0);
        $event->setNeedFinancialSupport(false);
        $event->setStartDate(new \DateTime());

        return $this->displayNewForm($request, $translator, $organisation, $event);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface|Response
     */
    private function displayNewForm(Request $request, TranslatorInterface $translator, Organisation $organisation, Reservation $event)
    {
        //process form
        $myForm = $this->handleCreateForm(
            $request,
            $event,
            function () use ($event, $translator) {
                return $this->validateEvent($event, $translator);
            }
        );
        if ($myForm instanceof Response) {
            return $myForm;
        }

        $this->organisation = $organisation;

        return $this->render('organisation/event/new.html.twig', ['form' => $myForm->createView()]);
    }

    /**
     * @Route("/{event}/clone", name="organisation_event_copy")
     *
     * @return Response
     */
    public function copyAction(Request $request, Organisation $organisation, Reservation $event, TranslatorInterface $translator)
    {
        $clonedEvent = clone $event;

        return $this->displayNewForm($request, $translator, $organisation, $clonedEvent);
    }

    /**
     * @Route("/{event}/edit", name="organisation_event_edit")
     *
     * @return Response
     */
    public function editAction(Organisation $organisation, Request $request, Reservation $event, TranslatorInterface $translator)
    {
        //process form
        $myForm = $this->handleUpdateForm(
            $request,
            $event,
            function () use ($event, $translator) {
                return $this->validateEvent($event, $translator);
            }
        );

        if ($myForm instanceof Response) {
            return $myForm;
        }

        $this->organisation = $organisation;

        return $this->render('organisation/event/edit.html.twig', ['form' => $myForm->createView(), 'event' => $event]);
    }

    /**     *
     * @Route("/{event}/remove", name="organisation_event_remove")
     *
     * @return Response
     */
    public function removeAction(Organisation $organisation, Request $request, Reservation $event)
    {
        //process form
        $form = $this->handleDeleteForm($request, $event);
        if ($form === null) {
            return $this->redirectToRoute('organisation_view', ['organisation' => $organisation->getId()]);
        }

        $this->organisation = $organisation;

        return $this->render('organisation/event/remove.html.twig', ['form' => $form->createView(), 'event' => $event]);
    }

    private function validateEvent(Reservation $event, TranslatorInterface $translator): bool
    {
        if (mb_strlen($event->getNameDe()) === 0 && mb_strlen($event->getNameEn()) === 0) {
            $this->displayError($translator->trans('new.error.no_name', [], 'organisation_event'));

            return false;
        }

        return true;
    }

    /**
     * get the breadcrumbs leading to this controller.
     *
     * @return Breadcrumb[]
     */
    protected function getIndexBreadcrumbs()
    {
        // test in frontend
        return array_merge(parent::getIndexBreadcrumbs(), [
            new Breadcrumb(
                $this->generateUrl('organisation_view', ['organisation' => $this->organisation->getId()]),
                $this->getTranslator()->trans('view.title', [], 'organisation')
            ),
        ]);
    }
}
