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
use Doctrine\ORM\Mapping as ORM;

/**
 * an event determines how the questionnaire looks like.
 *
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class SemesterReport extends BaseEntity
{
    use IdTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $semester;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $submittedDateTime;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $politicalEventsDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comments;

    /**
     * @var Organisation
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Organisation", inversedBy="semesterReports")
     */
    private $organisation;

    public function getSemester(): int
    {
        return $this->semester;
    }

    public function setSemester(int $semester): void
    {
        $this->semester = $semester;
    }

    public function getSubmittedDateTime(): \DateTime
    {
        return $this->submittedDateTime;
    }

    public function setSubmittedDateTime(\DateTime $submittedDateTime): void
    {
        $this->submittedDateTime = $submittedDateTime;
    }

    public function getPoliticalEventsDescription(): ?string
    {
        return $this->politicalEventsDescription;
    }

    public function setPoliticalEventsDescription(?string $politicalEventsDescription): void
    {
        $this->politicalEventsDescription = $politicalEventsDescription;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): void
    {
        $this->comments = $comments;
    }

    public function getOrganisation(): Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(Organisation $organisation): void
    {
        $this->organisation = $organisation;
    }
}
