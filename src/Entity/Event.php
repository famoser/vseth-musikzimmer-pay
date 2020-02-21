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
use Doctrine\ORM\Mapping as ORM;

/**
 * an event determines how the questionnaire looks like.
 *
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class Event extends BaseEntity
{
    use IdTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $semester;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $nameDe;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $nameEn;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionDe;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionEn;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $location;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private $showInCalender = true;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $revenue = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $expenditure = 0;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $needFinancialSupport;

    /**
     * @var Organisation
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Organisation", inversedBy="events")
     */
    private $organisation;

    public function getSemester(): int
    {
        return $this->semester;
    }

    /**
     * @return int
     */
    public function getSemesterName(): string
    {
        return SemesterType::semesterToString($this->getSemester());
    }

    public function setSemester(int $semester): void
    {
        $this->semester = $semester;
    }

    /**
     * @return string|null
     */
    public function getName($preference = 'de')
    {
        if ($preference === 'de') {
            return $this->getNameDe() !== null ? $this->getNameDe() : $this->getNameEn();
        }

        return $this->getNameEn() !== null ? $this->getNameEn() : $this->getNameDe();
    }

    public function getNameDe(): ?string
    {
        return $this->nameDe;
    }

    public function setNameDe(?string $nameDe): void
    {
        $this->nameDe = $nameDe;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(?string $nameEn): void
    {
        $this->nameEn = $nameEn;
    }

    /**
     * @return string|null
     */
    public function getDescription($preference = 'de')
    {
        if ($preference === 'de') {
            return $this->getDescriptionDe() !== null ? $this->getDescriptionDe() : $this->getDescriptionEn();
        }

        return $this->getDescriptionEn() !== null ? $this->getDescriptionEn() : $this->getDescriptionDe();
    }

    public function getDescriptionDe(): ?string
    {
        return $this->descriptionDe;
    }

    public function setDescriptionDe(?string $descriptionDe): void
    {
        $this->descriptionDe = $descriptionDe;
    }

    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn(?string $descriptionEn): void
    {
        $this->descriptionEn = $descriptionEn;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getRevenue(): int
    {
        return $this->revenue;
    }

    public function setRevenue(int $revenue): void
    {
        $this->revenue = $revenue;
    }

    public function getExpenditure(): int
    {
        return $this->expenditure;
    }

    public function setExpenditure(int $expenditure): void
    {
        $this->expenditure = $expenditure;
    }

    public function isNeedFinancialSupport(): bool
    {
        return $this->needFinancialSupport;
    }

    public function setNeedFinancialSupport(bool $needFinancialSupport): void
    {
        $this->needFinancialSupport = $needFinancialSupport;
    }

    public function getOrganisation(): Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(Organisation $organisation): void
    {
        $this->organisation = $organisation;
    }

    public function getShowInCalender(): bool
    {
        return $this->showInCalender;
    }

    public function setShowInCalender(bool $showInCalender): void
    {
        $this->showInCalender = $showInCalender;
    }
}
