<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use App\Entity\Traits\IdTrait;
use App\Form\Type\SemesterType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * an event determines how the questionnaire looks like.
 *
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class Organisation extends BaseEntity
{
    use IdTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $relationSinceSemester;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $authenticationCode;

    /**
     * @var Event[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Event", mappedBy="organisation")
     * @ORM\OrderBy({"semester" = "DESC", "startDate" = "DESC", "endDate" = "DESC"})
     */
    private $events;

    /**
     * @var SemesterReport[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SemesterReport", mappedBy="organisation")
     * @ORM\OrderBy({"semester" = "DESC"})
     */
    private $semesterReports;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->semesterReports = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getRelationSinceSemester(): int
    {
        return $this->relationSinceSemester;
    }

    public function setRelationSinceSemester(int $relationSinceSemester): void
    {
        $this->relationSinceSemester = $relationSinceSemester;
    }

    public function getAuthenticationCode(): string
    {
        return $this->authenticationCode;
    }

    public function getCurrentSemesterReport(): ?SemesterReport
    {
        $currentSemester = SemesterType::getCurrentSemester();
        foreach ($this->getSemesterReports() as $semesterReport) {
            if ($semesterReport->getSemester() === $currentSemester) {
                return $semesterReport;
            }
        }

        return null;
    }

    /**
     * @var Event[]
     */
    private $futureEvents = null;

    private function ensureFutureEventsPopulated()
    {
        if ($this->futureEvents !== null) {
            return;
        }

        $this->futureEvents = [];

        $currentSemester = SemesterType::getCurrentSemester();
        foreach ($this->getEvents() as $event) {
            if ($event->getSemester() >= $currentSemester) {
                $this->futureEvents[] = $event;
            }
        }
    }

    public function getFutureEventCount(): int
    {
        $this->ensureFutureEventsPopulated();

        return \count($this->futureEvents);
    }

    public function getFutureRevenueSum(): int
    {
        $this->ensureFutureEventsPopulated();

        $revenueSum = 0;
        foreach ($this->futureEvents as $futureEvent) {
            $revenueSum += $futureEvent->getRevenue();
        }

        return $revenueSum;
    }

    public function getFutureExpenditureSum(): int
    {
        $this->ensureFutureEventsPopulated();

        $expenditureSum = 0;
        foreach ($this->futureEvents as $futureEvent) {
            $expenditureSum += $futureEvent->getExpenditure();
        }

        return $expenditureSum;
    }

    public function futureFinancialSupport(): bool
    {
        $this->ensureFutureEventsPopulated();

        foreach ($this->futureEvents as $futureEvent) {
            if ($futureEvent->isNeedFinancialSupport()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws \Exception
     */
    public function setAuthenticationCode(string $authenticationCode)
    {
        $this->authenticationCode = $authenticationCode;
    }

    /**
     * @throws \Exception
     */
    public function generateAuthenticationCode()
    {
        $this->authenticationCode = Uuid::uuid4();
    }

    /**
     * @return Event[]|ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return SemesterReport[]|ArrayCollection
     */
    public function getSemesterReports()
    {
        return $this->semesterReports;
    }
}
