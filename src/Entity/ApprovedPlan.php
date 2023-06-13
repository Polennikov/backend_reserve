<?php

namespace App\Entity;

use App\Repository\ApprovedPlanRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApprovedPlanRepository::class)
 */
class ApprovedPlan
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $competence;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $checkValue;

    /**
     * @ORM\ManyToOne(targetEntity=Plan::class, inversedBy="plan")
     */
    private $plan;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheckValue(): ?string
    {
        return $this->checkValue;
    }

    public function setCheckValue(string $checkValue): self
    {
        $this->checkValue = $checkValue;

        return $this;
    }

    public function getCompetence(): ?string
    {
        return $this->competence;
    }

    public function setCompetence(string $competence): self
    {
        $this->competence = $competence;

        return $this;
    }

    public function getPlan(): ?plan
    {
        return $this->plan;
    }

    public function setPlan(?plan $plan): self
    {
        $this->plan = $plan;

        return $this;
    }
}
