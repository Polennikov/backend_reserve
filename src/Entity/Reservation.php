<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $projectId;

    /**
     * @ORM\Column(type="integer")
     */
    private $employerId;

    /**
     * @ORM\Column(type="integer")
     */
    private $month;

    /**
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @ORM\Column(type="float")
     */
    private $percent;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateReservation;

    /**
     * @ORM\OneToMany(targetEntity=ReservationChange::class, mappedBy="entry", orphanRemoval=true)
     */
    private $reservationChanges;

    /**
     * @ORM\ManyToOne(targetEntity=Manager::class, inversedBy="reservations")
     */
    private $manager;

    public function __construct()
    {
        $this->reservationChanges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectId(): ?int
    {
        return $this->projectId;
    }

    public function setProjectId(int $projectId): self
    {
        $this->projectId = $projectId;

        return $this;
    }

    public function getEmployerId(): ?int
    {
        return $this->employerId;
    }

    public function setEmployerId(int $employerId): self
    {
        $this->employerId = $employerId;

        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(int $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(float $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    public function setDateReservation(DateTime $date): self
    {
        $this->dateReservation = $date;

        return $this;
    }

    public function getDateReservation()
    {
        return $this->dateReservation;
    }

    /**
     * @return Collection|ReservationChange[]
     */
    public function getReservationChanges(): Collection
    {
        return $this->reservationChanges;
    }

    public function addReservationChange(ReservationChange $reservationChange): self
    {
        if (!$this->reservationChanges->contains($reservationChange)) {
            $this->reservationChanges[] = $reservationChange;
            $reservationChange->setEntry($this);
        }

        return $this;
    }

    public function removeReservationChange(ReservationChange $reservationChange): self
    {
        if ($this->reservationChanges->removeElement($reservationChange)) {
            // set the owning side to null (unless already changed)
            if ($reservationChange->getEntry() === $this) {
                $reservationChange->setEntry(null);
            }
        }

        return $this;
    }

    public function getManager(): ?Manager
    {
        return $this->manager;
    }

    public function setManager(?Manager $manager): self
    {
        $this->manager = $manager;

        return $this;
    }
}
