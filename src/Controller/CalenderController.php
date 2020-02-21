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

use App\Controller\Base\BaseDoctrineController;
use App\Entity\Event;
use Doctrine\Persistence\ManagerRegistry;
use Eluceo\iCal\Component\Calendar;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/calender")
 */
class CalenderController extends BaseDoctrineController
{
    /**
     * @Route("", name="calender")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, ManagerRegistry $managerRegistry)
    {
        $langPreference = $this->getLanguagePreference($request);

        /** @var Event[] $events */
        $events = $managerRegistry->getRepository(Event::class)->findBy(['showInCalender' => true], ['semester' => 'ASC', 'startDate' => 'ASC']);
        $vCalendar = new Calendar('vseth.ethz.ch/anerkannte-organisation');
        foreach ($events as $event) {
            if ($event->getStartDate() === null || $event->getEndDate() === null) {
                continue;
            }

            $id = base64_encode('vseth-anorg-event-' . $event->getId());
            $calenderEvent = new \Eluceo\iCal\Component\Event($id);
            $calenderEvent->setSummary($event->getName($langPreference));
            $calenderEvent->setDescription($event->getDescription($langPreference));
            $calenderEvent->setLocation($event->getLocation());
            $calenderEvent->setUseTimezone(true);
            $calenderEvent->setDtStart($event->getStartDate());
            $calenderEvent->setDtEnd($event->getEndDate());

            $vCalendar->addComponent($calenderEvent);
        }

        return Response::create($vCalendar->render(), 200, ['Content-Type' => 'text/calendar; charset=utf-8', 'Content-Disposition' => 'attachment; filename="cal.ics"']);
    }

    private function getLanguagePreference(Request $request)
    {
        if ($request->query->has('lang')) {
            $value = $request->query->get('lang');
            if (\in_array($value, ['de', 'en'], true)) {
                return $value;
            }
        }

        return 'de';
    }

    /**
     * no breadcrumbs on the index.
     *
     * @return \App\Model\Breadcrumb[]|array
     */
    protected function getIndexBreadcrumbs()
    {
        return [];
    }
}
