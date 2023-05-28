<?php

namespace App\Entity;

use App\Repository\SettingEmployerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SettingEmployerRepository::class)
 */
class SettingEmployer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hoursWork;

    /**
     * @ORM\Column(type="integer")
     */
    private $employerId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHoursWork(): ?int
    {
        return $this->hoursWork;
    }

    public function setHoursWork(?int $hoursWork): self
    {
        $this->hoursWork = $hoursWork;

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
}
