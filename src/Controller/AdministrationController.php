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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/administration")
 */
class AdministrationController extends BaseController
{
    /**
     * @Route("", name="administration")
     *
     * @return Response
     */
    public function indexAction()
    {
        //get all existing semesters
        /** @var User[] $users */
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([], ['email' => 'ASC']);

        return $this->render('administration.twig', ['users' => $users]);
    }
}
