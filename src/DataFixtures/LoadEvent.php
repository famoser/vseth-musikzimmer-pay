<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\DataFixtures\Base\BaseFixture;
use App\Entity\Event;
use App\Entity\Organisation;
use App\Form\Type\SemesterType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Serializer\SerializerInterface;

class LoadEvent extends BaseFixture
{
    const ORDER = LoadOrganisations::ORDER + 1;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * LoadEvent constructor.
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Load data fixtures with the passed EntityManager.
     */
    public function load(ObjectManager $manager)
    {
        //fill semester with events
        /** @var Organisation[] $organisations */
        $organisations = $manager->getRepository(Organisation::class)->findAll();

        $this->fillWithPremadeEvents($manager, $organisations[0]);

        $faker = $this->getFaker();
        $organisationCount = \count($organisations);
        for ($i = 1; $i < $organisationCount; ++$i) {
            /** @var Event[] $randomEvents */
            $randomEvents = $this->loadSomeRandoms($manager, $faker->randomFloat(0, 0, 10));
            foreach ($randomEvents as $randomEvent) {
                $randomEvent->setOrganisation($organisations[$i]);
                $manager->persist($randomEvent);
            }
        }

        $manager->flush();
    }

    protected function getRandomInstance()
    {
        $faker = $this->getFaker();

        $lang = 'de';
        if ($faker->randomDigit < 5) {
            $lang = 'en';
        } elseif ($faker->randomDigit < 5) {
            $lang = 'both';
        }

        $event = new Event();
        $event->setSemester(SemesterType::getCurrentSemester());

        if ($lang === 'de' || $lang === 'both') {
            $event->setNameDe($faker->text(40));
            $event->setDescriptionDe($faker->text(100));
        }

        if ($lang === 'en' || $lang === 'both') {
            $event->setNameEn($faker->text(40));
            $event->setDescriptionEn($faker->text(100));
        }

        $event->setStartDate($faker->dateTime);
        $event->setEndDate($faker->dateTimeInInterval($event->getStartDate()->format('c'), '+4 hours'));
        $event->setLocation($faker->text(20));
        $event->setShowInCalender($faker->boolean(80));

        if ($faker->randomDigit < 5) {
            $event->setRevenue(0);
        } else {
            $event->setRevenue($faker->randomNumber(3));
        }
        $event->setNeedFinancialSupport($event->getRevenue() > 0 && $faker->randomDigit < 3);

        return $event;
    }

    private function fillWithPremadeEvents(ObjectManager $manager, Organisation $organisation)
    {
        //prepare resources
        $json = file_get_contents(__DIR__ . '/Resources/events.json');
        /** @var Event[] $events */
        $events = $this->serializer->deserialize($json, Event::class . '[]', 'json');

        $startDate = new \DateTime('today 18:00');
        $endDate = new \DateTime('today 20:00');
        foreach ($events as $event) {
            $event->setOrganisation($organisation);
            $event->setSemester(SemesterType::getCurrentSemester());
            $event->setStartDate($startDate);
            $event->setEndDate($endDate);
            $manager->persist($event);

            $startDate = $startDate->add(new \DateInterval('P10D'));
            $endDate = $endDate->add(new \DateInterval('P10D'));
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture.
     *
     * @return int
     */
    public function getOrder()
    {
        return static::ORDER;
    }
}
