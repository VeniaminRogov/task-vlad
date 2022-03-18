<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Vacation
 * @package App\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="vacations")
 */
class Vacation
{
    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date")
     */
    private $startDate;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date")
     */
    private $endDate;

    /**
     * @var Employee|null
     *
     * @ORM\ManyToOne(targetEntity="Employee", inversedBy="vacations")
     */
    private $employee;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime|null $startDate
     * @return Vacation
     */
    public function setStartDate(?DateTime $startDate): Vacation
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @param DateTime|null $endDate
     * @return Vacation
     */
    public function setEndDate(?DateTime $endDate): Vacation
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee|null $employee
     * @return Vacation
     */
    public function setEmployee(?Employee $employee): Vacation
    {
        $this->employee = $employee;
        return $this;
    }
}