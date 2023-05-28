<?php

namespace App\Entity;

use App\Repository\ReservationChangeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReservationChangeRepository::class)
 */
class ReservationChange
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Reservation::class, inversedBy="reservationChanges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $entry;

    /**
     * @ORM\Column(type="datetime")
     */
    private $changeTime;

    /**
     * @ORM\Column(type="float")
     */
    private $oldValue;

    /**
     * @ORM\Column(type="float")
     */
    private $newValue;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntry(): ?Reservation
    {
        return $this->entry;
    }

    public function setEntry(?Reservation $entry): self
    {
        $this->entry = $entry;

        return $this;
    }

    public function getChangeTime(): ?\DateTimeInterface
    {
        return $this->changeTime;
    }

    public function setChangeTime(\DateTimeInterface $changeTime): self
    {
        $this->changeTime = $changeTime;

        return $this;
    }

    public function getOldValue(): ?float
    {
        return $this->oldValue;
    }

    public function setOldValue(float $oldValue): self
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    public function getNewValue(): ?float
    {
        return $this->newValue;
    }

    public function setNewValue(float $newValue): self
    {
        $this->newValue = $newValue;

        return $this;
    }
}
