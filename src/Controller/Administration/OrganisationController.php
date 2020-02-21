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
use App\Entity\Organisation;
use App\Form\Type\SemesterType;
use App\Model\Breadcrumb;
use App\Service\Interfaces\CsvServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/organisation")
 */
class OrganisationController extends BaseController
{
    /**
     * @Route("", name="administration_organisations")
     *
     * @return Response
     */
    public function indexAction()
    {
        //get all existing semesters
        /** @var Organisation[] $organisations */
        $organisations = $this->getDoctrine()->getRepository(Organisation::class)->findBy([], ['name' => 'DESC']);

        return $this->render('administration/organisations.twig', ['organisations' => $organisations]);
    }

    /**
     * @Route("/export", name="administration_organisations_export")
     *
     * @return Response
     */
    public function exportAction(CsvServiceInterface $csvService)
    {
        //get all existing semesters
        /** @var Organisation[] $organisations */
        $organisations = $this->getDoctrine()->getRepository(Organisation::class)->findAll();

        $organisationArray = [];
        foreach ($organisations as $organisation) {
            $entry = [];
            $entry[] = $organisation->getName();
            $entry[] = $organisation->getEmail();
            $entry[] = $this->generateUrl('login_code', ['code' => $organisation->getAuthenticationCode()], UrlGeneratorInterface::ABSOLUTE_URL);

            $organisationArray[] = $entry;
        }

        return $csvService->streamCsv('authentication_codes.csv', $organisationArray);
    }

    /**
     * @Route("/{organisation}/events", name="administration_organisations_events")
     *
     * @return Response
     */
    public function eventsAction(Organisation $organisation)
    {
        return $this->render('administration/organisation/events.twig', ['organisation' => $organisation]);
    }

    /**
     * @Route("/new", name="administration_organisation_new")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        //create the event
        $organisation = new Organisation();
        $organisation->setName('');
        $organisation->setEmail('');
        $organisation->setRelationSinceSemester(SemesterType::getCurrentSemester());

        //process form
        $myForm = $this->handleCreateForm(
            $request,
            $organisation,
            function () use ($organisation) {
                $organisation->generateAuthenticationCode();
            }
        );
        if ($myForm instanceof Response) {
            return $myForm;
        }

        return $this->render('administration/organisation/new.html.twig', ['form' => $myForm->createView()]);
    }

    /**
     * @Route("/{organisation}/edit", name="administration_organisation_edit")
     *
     * @return Response
     */
    public function editAction(Request $request, Organisation $organisation)
    {
        //process form
        $myForm = $this->handleUpdateForm($request, $organisation);
        if ($myForm instanceof Response) {
            return $myForm;
        }

        return $this->render('administration/organisation/edit.html.twig', ['form' => $myForm->createView(), 'organisation' => $organisation]);
    }

    /**     *
     * @Route("/{organisation}/remove", name="administration_organisation_remove")
     *
     * @return Response
     */
    public function removeAction(Request $request, Organisation $organisation)
    {
        //process form
        $form = $this->handleDeleteForm($request, $organisation);
        if ($form === null) {
            return $this->redirectToRoute('administration_organisations');
        }

        return $this->render('administration/organisation/remove.html.twig', ['form' => $form->createView(), 'organisation' => $organisation]);
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
                $this->generateUrl('administration_organisations'),
                $this->getTranslator()->trans('index.title', [], 'administration_organisation')
            ),
        ]);
    }
}
