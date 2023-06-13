<?php

namespace App\Entity;

use App\Repository\PlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanRepository::class)
 */
class Plan
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $projectName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $month;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $year;

    /**
     * @ORM\OneToMany(targetEntity=ApprovedPlan::class, mappedBy="entry", orphanRemoval=true)
     */
    private $approvedPlan;

    public function __construct()
    {
        $this->approvedPlan = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?string
    {
        return $this->project;
    }

    public function setProject(string $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(string $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getProjectName(): ?string
    {
        return $this->projectName;
    }

    public function setProjectName(string $projectName): self
    {
        $this->projectName = $projectName;

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

    /**
     * @return Collection|ApprovedPlan[]
     */
    public function getApprovedPlan(): Collection
    {
        return $this->approvedPlan;
    }
}
