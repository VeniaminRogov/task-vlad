<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Employee
 * @package App\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="employees")
 */
class Employee
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
     * @var string|null
     *
     * @ORM\Column(type="string")
     */
    private $fullName;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date")
     */
    private $hiredAt;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $dismissedAt;

    /**
     * @var Collection|Vacation[]
     *
     * @ORM\OneToMany(targetEntity="Vacation", mappedBy="employee", cascade={"persist", "remove"})
     */
    private $vacations;

    /**
     * Employee constructor.
     */
    public function __construct()
    {
        $this->vacations = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string|null $fullName
     * @return Employee
     */
    public function setFullName(?string $fullName): Employee
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getHiredAt(): ?DateTime
    {
        return $this->hiredAt;
    }

    /**
     * @param DateTime|null $hiredAt
     * @return Employee
     */
    public function setHiredAt(?DateTime $hiredAt): Employee
    {
        $this->hiredAt = $hiredAt;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDismissedAt(): ?DateTime
    {
        return $this->dismissedAt;
    }

    /**
     * @param DateTime|null $dismissedAt
     * @return Employee
     */
    public function setDismissedAt(?DateTime $dismissedAt): Employee
    {
        $this->dismissedAt = $dismissedAt;
        return $this;
    }

    /**
     * @return Vacation[]|Collection
     */
    public function getVacations()
    {
        return $this->vacations;
    }

    /**
     * @param Collection $vacations
     * @return Employee
     */
    public function setVacations(Collection $vacations): Employee
    {
        $this->vacations = $vacations;
        return $this;
    }

    /**
     * @param Vacation $vacation
     * @return Employee
     */
    public function addVacation(Vacation $vacation): Employee
    {
        $this->vacations->add($vacation);
        return $this;
    }

    /**
     * @param Vacation $vacation
     * @return Employee
     */
    public function removeVacation(Vacation $vacation): Employee
    {
        $this->vacations->removeElement($vacation);
        return $this;
    }
}